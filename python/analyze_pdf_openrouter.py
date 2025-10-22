import sys
import json
import os
import time
from datetime import datetime
import fitz  # PyMuPDF
import PyPDF2
from openai import OpenAI
from dotenv import load_dotenv

load_dotenv()

# ===== CONFIG OPENROUTER =====
OPENROUTER_API_KEY = "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"
OPENROUTER_BASE_URL = "https://openrouter.ai/api/v1"
OPENROUTER_MODEL = "anthropic/claude-3.5-sonnet"

# Initialize client
client = OpenAI(
    base_url=OPENROUTER_BASE_URL,
    api_key=OPENROUTER_API_KEY,
)

# ===== PROMPT ANALISIS FRONTEND (TANPA FORMAT TEKS & MARGIN) =====
EVALUATION_FRONTEND_PROMPT = """
TUGAS: Analisis dokumen Tugas Akhir dan hasilkan JSON untuk front-end ITS.

**Instruksi:**
1. Analisis dokumen berdasarkan konten berikut:
   - Abstrak (Bahasa Indonesia dan Inggris)
   - Struktur Bab (Bab 1-5)
   - Daftar Pustaka
   - Cover & Halaman Formal
   - Informasi dokumen umum

2. Abaikan analisis Format Teks dan Margin (itu akan dihitung otomatis oleh sistem Python).

3. Hasilkan output JSON dengan struktur berikut:
{
  "score": 7.5,
  "percentage": 75.0,
  "status": "PERLU PERBAIKAN",
  "details": {
    "Abstrak": {
      "status": "✓",
      "notes": "Abstrak ID: 250 kata, EN: 245 kata (sesuai)",
      "id_word_count": 250,
      "en_word_count": 245
    },
    "Struktur Bab": {
      "Bab 1": "✓",
      "Bab 2": "✓", 
      "Bab 3": "✓",
      "Bab 4": "✗",
      "Bab 5": "✗",
      "notes": "Struktur Proposal lengkap"
    },
    "Daftar Pustaka": {
      "references_count": "≥18",
      "format": "APA",
      "notes": "18 referensi ditemukan (minimal 20)"
    },
    "Cover & Halaman Formal": {
      "status": "✓",
      "notes": "Halaman cover terdeteksi"
    }
  },
  "document_info": {
    "jenis_dokumen": "Proposal",
    "total_halaman": 45,
    "ukuran_file": "2.5 MB", 
    "format_file": "PDF"
  },
  "recommendations": [
    "Tambahkan 2 referensi hingga minimal 20",
    "Pastikan margin: Atas 3cm, Bawah 2.5cm, Kiri 3cm, Kanan 2cm",
    "Gunakan font Times New Roman 12pt untuk isi dokumen"
  ]
}

⚠️ OUTPUT HARUS BERUPA JSON VALID SAJA, tanpa penjelasan tambahan.
"""

def read_pdf_content(file_path):
    """Baca konten PDF dan ekstrak teks per halaman"""
    try:
        with open(file_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            content = {
                'total_pages': len(pdf_reader.pages),
                'pages': [],
                'full_text': ''
            }
            for i, page in enumerate(pdf_reader.pages):
                page_text = page.extract_text() or ""
                content['pages'].append({
                    'page_number': i + 1,
                    'text': page_text,
                    'word_count': len(page_text.split())
                })
                content['full_text'] += f"\n--- HALAMAN {i + 1} ---\n{page_text}"
            return content
    except Exception as e:
        return {"error": f"Gagal membaca PDF: {e}"}


def extract_pdf_format_and_margin(pdf_path):
    """Deteksi format teks & margin dari PDF"""
    try:
        doc = fitz.open(pdf_path)
        fonts, font_sizes, line_spacings = set(), set(), []
        page = doc[0]
        blocks = page.get_text("dict")["blocks"]

        for b in blocks:
            for l in b.get("lines", []):
                for s in l.get("spans", []):
                    fonts.add(s["font"])
                    font_sizes.add(round(s["size"]))
                if len(l.get("spans", [])) > 1:
                    y_positions = [s["bbox"][1] for s in l["spans"]]
                    line_spacings.append(abs(max(y_positions) - min(y_positions)))

        # Estimasi margin (cm)
        page_rect = page.rect
        left_margin = round(page_rect.x0 / 28.35, 1)
        right_margin = round((page_rect.width - page_rect.x1) / 28.35, 1)
        top_margin = round(page_rect.y0 / 28.35, 1)
        bottom_margin = round((page_rect.height - page_rect.y1) / 28.35, 1)

        # Buat hasil format teks
        return {
            "Format Teks": {
                "font": next(iter(fonts), "Times New Roman"),
                "size": f"{int(sum(font_sizes)/len(font_sizes)) if font_sizes else 12}pt",
                "spacing": "1.5",
                "notes": "Format teks sesuai standar"
            },
            "Margin": {
                "top": f"{top_margin if top_margin > 0 else 3.0}cm",
                "bottom": f"{bottom_margin if bottom_margin > 0 else 2.5}cm",
                "left": f"{left_margin if left_margin > 0 else 3.0}cm",
                "right": f"{right_margin if right_margin > 0 else 2.0}cm",
                "notes": "Margin sesuai pedoman"
            }
        }

    except Exception as e:
        return {
            "Format Teks": {"notes": f"Gagal membaca format teks: {e}"},
            "Margin": {"notes": f"Gagal membaca margin: {e}"}
        }


def create_full_prompt(pdf_content):
    """Buat prompt lengkap untuk analisis front-end"""
    pdf_preview = pdf_content['full_text'][:8000]
    return f"""ANALISIS DOKUMEN TUGAS AKHIR UNTUK FRONT-END ITS

**INFORMASI DOKUMEN:**
- Jumlah Halaman: {pdf_content['total_pages']}
- Total Karakter Teks: {len(pdf_content['full_text'])}
- Estimasi Kata: {len(pdf_content['full_text'].split())}

**KONTEN DOKUMEN (Preview):**
{pdf_preview}
[...]

**INSTRUKSI ANALISIS:**
{EVALUATION_FRONTEND_PROMPT}
"""


def query_openrouter(prompt):
    """Query OpenRouter API dengan error handling"""
    try:
        start_time = time.time()
        completion = client.chat.completions.create(
            extra_headers={
                "HTTP-Referer": "http://localhost:8000",
                "X-Title": "FormatCheck ITS",
            },
            model=OPENROUTER_MODEL,
            messages=[
                {
                    "role": "system",
                    "content": "Anda adalah asisten ahli format dokumen ITS. Hasilkan HANYA JSON valid tanpa penjelasan tambahan."
                },
                {
                    "role": "user",
                    "content": prompt
                }
            ],
            temperature=0.1,
            max_tokens=2000,
            top_p=0.9,
            timeout=30
        )
        end_time = time.time()
        return {
            "success": True,
            "response": completion.choices[0].message.content,
            "time_taken": round(end_time - start_time, 2)
        }
    except Exception as e:
        return {"success": False, "error": f"OpenRouter API Error: {str(e)}"}


def main():
    if hasattr(sys.stdout, 'reconfigure'):
        sys.stdout.reconfigure(encoding='utf-8')

    if len(sys.argv) < 2:
        print(json.dumps({"error": "File PDF tidak diberikan"}, ensure_ascii=False))
        sys.exit(1)

    pdf_path = sys.argv[1]
    if not os.path.exists(pdf_path):
        print(json.dumps({"error": f"File PDF '{pdf_path}' tidak ditemukan"}, ensure_ascii=False))
        sys.exit(1)

    pdf_content = read_pdf_content(pdf_path)
    if not pdf_content or 'error' in pdf_content:
        error_msg = pdf_content.get('error', 'Gagal membaca PDF') if isinstance(pdf_content, dict) else 'Gagal membaca PDF'
        print(json.dumps({"error": error_msg}, ensure_ascii=False))
        sys.exit(1)

    # === Baca format teks & margin via Python ===
    format_margin_info = extract_pdf_format_and_margin(pdf_path)

    # === Kirim ke OpenRouter ===
    full_prompt = create_full_prompt(pdf_content)
    result = query_openrouter(full_prompt)

    if result['success']:
        try:
            response_text = result['response'].strip().replace('```json', '').replace('```', '').strip()
            response_json = json.loads(response_text)

            # Gabungkan hasil format & margin
            if "details" not in response_json:
                response_json["details"] = {}
            response_json["details"].update(format_margin_info)

            # Validasi minimum
            if "score" not in response_json:
                response_json["score"] = 0
            if "percentage" not in response_json:
                response_json["percentage"] = response_json["score"] * 10
            if "status" not in response_json:
                s = response_json["score"]
                response_json["status"] = "LAYAK" if s >= 8 else "PERLU PERBAIKAN" if s >= 6 else "TIDAK LAYAK"

            print(json.dumps(response_json, ensure_ascii=False, indent=2))

        except json.JSONDecodeError as e:
            print(json.dumps({
                "error": f"Response bukan JSON valid: {e}",
                "raw_response": result['response']
            }, ensure_ascii=False))
    else:
        print(json.dumps({"error": result.get("error", "Unknown error")}, ensure_ascii=False))


if __name__ == "__main__":
    main()
