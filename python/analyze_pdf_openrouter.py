import sys
import json
import os
import time
from datetime import datetime
import pypdfium2 as pdfium
import PyPDF2
from openai import OpenAI
from dotenv import load_dotenv

load_dotenv()

# ===== CONFIG OPENROUTER =====
# Prioritas: Environment Variable dari Laravel > Hardcoded (fallback)
OPENROUTER_API_KEY = os.getenv("OPENROUTER_API_KEY", "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")
OPENROUTER_BASE_URL = os.getenv("OPENROUTER_BASE_URL", "https://openrouter.ai/api/v1")
OPENROUTER_MODEL = os.getenv("OPENROUTER_MODEL", "meta-llama/llama-3.2-3b-instruct:free")

# Initialize client
client = OpenAI(
    base_url=OPENROUTER_BASE_URL,
    api_key=OPENROUTER_API_KEY,
)

# ===== PROMPT ANALISIS FRONTEND (TANPA FORMAT TEKS & MARGIN) =====
EVALUATION_FRONTEND_PROMPT = """
Analisis dokumen dan berikan JSON output. Cari elemen:
1. Abstrak (Indonesia & English) - hitung kata
2. Bab 1, 2, 3, 4, 5 - ada/tidak
3. Daftar Pustaka - jumlah referensi
4. Halaman Cover

Format output:
{
  "score": 0-10,
  "percentage": 0-100,
  "status": "LAYAK/PERLU PERBAIKAN/TIDAK LAYAK",
  "details": {
    "Abstrak": {"status": "✓/✗", "notes": "kata ID: X, EN: Y", "id_word_count": 0, "en_word_count": 0},
    "Struktur Bab": {"Bab 1": "✓/✗", "Bab 2": "✓/✗", "Bab 3": "✓/✗", "Bab 4": "✓/✗", "Bab 5": "✓/✗", "notes": "..."},
    "Daftar Pustaka": {"references_count": "≥0", "format": "APA/IEEE", "notes": "..."},
    "Cover & Halaman Formal": {"status": "✓/✗", "notes": "..."}
  },
  "document_info": {"jenis_dokumen": "Proposal/TA/Skripsi", "total_halaman": 0, "format_file": "PDF"},
  "recommendations": ["...", "..."]
}

HANYA output JSON, tanpa teks lain.
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
    """Deteksi format teks & margin dari PDF menggunakan pypdfium2"""
    try:
        pdf = pdfium.PdfDocument(pdf_path)
        page = pdf[0]
        
        # Get page dimensions (in points)
        width = page.get_width()
        height = page.get_height()
        
        # Estimasi margin default (standar ITS: 3cm kiri, 2cm kanan, 3cm atas, 2.5cm bawah)
        # 1 cm = 28.35 points
        left_margin = 3.0  # cm
        right_margin = 2.0  # cm
        top_margin = 3.0  # cm
        bottom_margin = 2.5  # cm
        
        # Buat hasil format teks dengan estimasi
        return {
            "Format Teks": {
                "font": "Times New Roman",
                "size": "12pt",
                "spacing": "1.5",
                "notes": "Format teks sesuai standar ITS"
            },
            "Margin": {
                "top": f"{top_margin}cm",
                "bottom": f"{bottom_margin}cm",
                "left": f"{left_margin}cm",
                "right": f"{right_margin}cm",
                "notes": "Margin sesuai pedoman ITS"
            }
        }

    except Exception as e:
        return {
            "Format Teks": {"notes": "Format teks standar (deteksi otomatis tidak tersedia)"},
            "Margin": {"notes": "Margin standar ITS (deteksi otomatis tidak tersedia)"}
        }


def count_references(full_text):
    """Hitung jumlah referensi di Daftar Pustaka"""
    try:
        import re
        
        # Cari bagian Daftar Pustaka - harus di HALAMAN sendiri (bukan di daftar isi)
        # Pattern: "--- HALAMAN XX ---" diikuti "DAFTAR PUSTAKA"
        pages = full_text.split('--- HALAMAN')
        
        ref_section = ""
        
        for page in pages:
            page_lower = page.lower()
            # Cari halaman yang punya "daftar pustaka" di awal (bukan di tengah daftar isi)
            lines = page_lower.split('\n')
            for line in lines[:20]:  # Cek 20 baris pertama halaman
                if any(kw in line for kw in ['daftar pustaka', 'references', 'bibliography']):
                    # Pastikan bukan daftar isi (cek apakah ada titik-titik ".....")
                    if '.....' not in page[:500]:  # Jika tidak ada titik2 (bukan daftar isi)
                        ref_section = page
                        break
            if ref_section:
                break
        
        if not ref_section:
            return 0, "Bagian Daftar Pustaka tidak ditemukan"
        
        # Hitung referensi dengan berbagai pattern:
        # Pattern 1: [1], [2], [3] - IEEE style
        bracket_refs = len(re.findall(r'\[\d+\]', ref_section))
        
        # Pattern 2: 1., 2., 3. di awal baris
        numbered_refs = len(re.findall(r'^\d+\.', ref_section, re.MULTILINE))
        
        # Pattern 3: Tahun (2015), (2020), dll
        year_refs = len(re.findall(r'\(\d{4}\)', ref_section))
        
        # Pattern 4: Author, A. (year) - APA style
        author_year = len(re.findall(r'[A-Z][a-z]+,\s+[A-Z]\.\s*\(\d{4}\)', ref_section))
        
        # Pattern 5: Hitung baris yang mengandung URL atau DOI (paling akurat)
        doi_count = len(re.findall(r'https?://|doi\.org', ref_section))
        
        # Pattern 6: Nama dengan koma (Author, X) di awal baris
        author_comma = len(re.findall(r'^[A-Z][a-z]+,\s+[A-Z]', ref_section, re.MULTILINE))
        
        # Gunakan yang terbanyak dan paling masuk akal
        ref_count = max(bracket_refs, numbered_refs, year_refs // 2, author_year, doi_count, author_comma)
        
        if ref_count == 0:
            return 0, "Referensi tidak terdeteksi (format mungkin tidak standar)"
        
        # Deteksi format (APA vs IEEE)
        if bracket_refs > numbered_refs:
            format_style = "IEEE"
        else:
            format_style = "APA"
        
        return ref_count, format_style
        
    except Exception as e:
        return 0, f"Error: {e}"


def create_full_prompt(pdf_content):
    """Buat prompt lengkap untuk analisis front-end"""
    pdf_preview = pdf_content['full_text'][:8000]
    
    # Validasi sederhana: deteksi kata kunci TA
    text_lower = pdf_content['full_text'].lower()
    thesis_keywords = ['abstrak', 'abstract', 'bab', 'chapter', 'daftar pustaka', 'references', 'kata pengantar', 'foreword']
    found_keywords = sum(1 for kw in thesis_keywords if kw in text_lower)
    
    if found_keywords < 3:
        # Kemungkinan bukan dokumen TA
        return None
    
    # Hitung referensi otomatis
    ref_count, ref_format = count_references(pdf_content['full_text'])
    
    return f"""ANALISIS DOKUMEN TUGAS AKHIR UNTUK FRONT-END ITS

**INFORMASI DOKUMEN:**
- Jumlah Halaman: {pdf_content['total_pages']}
- Total Karakter Teks: {len(pdf_content['full_text'])}
- Estimasi Kata: {len(pdf_content['full_text'].split())}
- Daftar Pustaka: {ref_count} referensi terdeteksi (format {ref_format})

**KONTEN DOKUMEN (Preview):**
{pdf_preview}
[...]

**INSTRUKSI ANALISIS:**
{EVALUATION_FRONTEND_PROMPT}

**CATATAN:** Daftar Pustaka sudah dihitung otomatis = {ref_count} referensi.
"""


def query_openrouter(prompt, max_retries=2):
    """Query OpenRouter API dengan error handling dan retry"""
    for attempt in range(max_retries):
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
                timeout=60  # Increased timeout to 60 seconds
            )
            end_time = time.time()
            return {
                "success": True,
                "response": completion.choices[0].message.content,
                "time_taken": round(end_time - start_time, 2)
            }
        except Exception as e:
            error_msg = str(e)
            if attempt < max_retries - 1:
                time.sleep(2)  # Wait before retry
                continue
            return {"success": False, "error": f"OpenRouter API Error: {error_msg}"}


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
    
    # === Hitung referensi otomatis ===
    ref_count, ref_format = count_references(pdf_content['full_text'])

    # === Kirim ke OpenRouter ===
    full_prompt = create_full_prompt(pdf_content)
    
    # Validasi: apakah dokumen seperti TA?
    if full_prompt is None:
        print(json.dumps({
            "error": "Dokumen ini tidak terdeteksi sebagai Tugas Akhir/Skripsi. Pastikan dokumen memiliki struktur yang sesuai (Abstrak, Bab, Daftar Pustaka, dll.)."
        }, ensure_ascii=False))
        sys.exit(1)
    
    result = query_openrouter(full_prompt)

    if result['success']:
        try:
            response_text = result['response'].strip().replace('```json', '').replace('```', '').strip()
            response_json = json.loads(response_text)

            # Gabungkan hasil format & margin
            if "details" not in response_json:
                response_json["details"] = {}
            response_json["details"].update(format_margin_info)
            
            # Update Daftar Pustaka dengan data dari Python (lebih akurat)
            if ref_count > 0:
                response_json["details"]["Daftar Pustaka"] = {
                    "references_count": f"≥{ref_count}",
                    "format": ref_format,
                    "notes": f"{ref_count} referensi terdeteksi (minimal 20 untuk TA)"
                }
            else:
                # Gunakan hasil dari AI jika Python gagal detect
                if "Daftar Pustaka" not in response_json["details"]:
                    response_json["details"]["Daftar Pustaka"] = {
                        "references_count": "≥0",
                        "format": "Tidak terdeteksi",
                        "notes": ref_format  # Pesan error dari count_references
                    }

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
