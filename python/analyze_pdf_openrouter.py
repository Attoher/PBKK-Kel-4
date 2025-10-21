import sys
import json
import os
import time
from datetime import datetime
import requests
from requests.exceptions import RequestException, ConnectionError, Timeout
import PyPDF2
from openai import OpenAI

# ===== CONFIG OPENROUTER =====
OPENROUTER_API_KEY = "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"
OPENROUTER_BASE_URL = "https://openrouter.ai/api/v1"
OPENROUTER_MODEL = "tngtech/deepseek-r1t2-chimera:free"

# Initialize client
client = OpenAI(
    base_url=OPENROUTER_BASE_URL,
    api_key=OPENROUTER_API_KEY,
)

# ===== PROMPT ANALISIS FRONTEND =====
EVALUATION_FRONTEND_PROMPT = """
TUGAS: Analisis dokumen Tugas Akhir dan hasilkan JSON untuk front-end ITS.

**Instruksi:**
1. Analisis dokumen Tugas Akhir berdasarkan konten yang diberikan
2. Fokus pada aspek-aspek berikut:
   - Abstrak (Bahasa Indonesia dan Inggris)
   - Format teks (font, spacing, margin)
   - Struktur Bab (Bab 1-5)
   - Daftar Pustaka
   - Cover & Halaman Formal
   - Informasi dokumen umum

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
    "Format Teks": {
      "font": "Times New Roman", 
      "size": "12pt",
      "spacing": "1.5",
      "notes": "Format font dan spasi sesuai"
    },
    "Margin": {
      "top": "3.0cm",
      "bottom": "2.5cm", 
      "left": "3.0cm",
      "right": "2.0cm",
      "notes": "Margin sesuai standar ITS"
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
                    "content": "Anda adalah asisten ahli untuk analisis format dokumen akademik ITS. Hasilkan HANYA JSON valid tanpa penjelasan tambahan."
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
    # Ensure UTF-8 output
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

    full_prompt = create_full_prompt(pdf_content)
    
    result = query_openrouter(full_prompt)

    if result['success']:
        try:
            # Clean response
            response_text = result['response'].strip()
            response_text = response_text.replace('```json', '').replace('```', '').strip()
            
            # Parse JSON
            response_json = json.loads(response_text)
            
            # Validate structure
            if 'score' not in response_json:
                response_json['score'] = 0
            if 'percentage' not in response_json:
                response_json['percentage'] = response_json['score'] * 10
            if 'status' not in response_json:
                score = response_json['score']
                response_json['status'] = 'LAYAK' if score >= 8 else 'PERLU PERBAIKAN' if score >= 6 else 'TIDAK LAYAK'
                
            print(json.dumps(response_json, ensure_ascii=False, indent=2))
            
        except json.JSONDecodeError as e:
            print(json.dumps({
                "error": f"Response bukan JSON valid: {e}",
                "raw_response": result['response']
            }, ensure_ascii=False))
    else:
        print(json.dumps({
            "error": result.get("error", "Unknown error from OpenRouter")
        }, ensure_ascii=False))

if __name__ == "__main__":
    main()