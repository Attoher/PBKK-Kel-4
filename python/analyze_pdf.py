# analyze_pdf.py
import sys
import json
import os
import time
from datetime import datetime
import requests
from requests.exceptions import RequestException
import PyPDF2

# CONFIG 
models = [
    "gpt-oss:20b-cloud"
]
url = "http://localhost:11434/api/generate"
timeout_seconds = 3000

# Helper function untuk deteksi bab
def detect_chapters(text):
    """Deteksi keberadaan bab dalam teks"""
    chapters = {
        'Bab 1': False, 'BAB I': False,
        'Bab 2': False, 'BAB II': False,
        'Bab 3': False, 'BAB III': False,
        'Bab 4': False, 'BAB IV': False,
        'Bab 5': False, 'BAB V': False
    }
    
    chapter_keywords = {
        '1': ['PENDAHULUAN', 'INTRODUCTION', 'LATAR BELAKANG'],
        '2': ['TINJAUAN PUSTAKA', 'LANDASAN TEORI', 'STUDI LITERATUR'],
        '3': ['METODOLOGI', 'METODE PENELITIAN', 'PERANCANGAN'],
        '4': ['HASIL', 'IMPLEMENTASI', 'PENGUJIAN'],
        '5': ['KESIMPULAN', 'PENUTUP', 'CONCLUSION']
    }
    
    found_chapters = set()
    
    # Cek format angka dan romawi
    for chapter in chapters.keys():
        if chapter in text.upper():
            chapter_num = chapter[-1]
            found_chapters.add(chapter_num)
    
    # Cek kata kunci tiap bab
    for num, keywords in chapter_keywords.items():
        for keyword in keywords:
            if keyword in text.upper():
                found_chapters.add(num)
                break
    
    return found_chapters

# PROMPT FRONT-END 
EVALUATION_FRONTEND_PROMPT = """
TUGAS: Analisis dokumen Tugas Akhir dan hasilkan JSON untuk front-end ITS.

**Instruksi:**
1. Jawab setiap pertanyaan (1-46) berdasarkan dokumen.
2. Fokus pada bagian:
   - Abstrak
   - Format Teks
   - Margin
   - Struktur Bab (Bab 1-5)
   - Daftar Pustaka
   - Cover & Halaman Formal
   - Informasi Dokumen (jenis, total halaman, ukuran file, format)
3. Untuk setiap bagian, isi:
   - Status/indikator (misal: YA, TIDAK, TIDAK TERDETEKSI / ✗ / ✓)
   - Catatan singkat bila perlu
4. Untuk struktur bab, gunakan deteksi bab yang sudah diberikan di atas
5. Hitung skor kelengkapan format ITS (0-10) dan persentase.
6. Tentukan status dokumen: "LAYAK" atau "TIDAK LAYAK"

PETUNJUK DETEKSI BAB:
- Bab dianggap ada (✓) jika ditemukan judulnya (BAB 1/I, BAB 2/II, dsb)
- Bab juga dianggap ada jika ditemukan kata kunci spesifik:
  * Bab 1: PENDAHULUAN, INTRODUCTION, LATAR BELAKANG
  * Bab 2: TINJAUAN PUSTAKA, LANDASAN TEORI, STUDI LITERATUR
  * Bab 3: METODOLOGI, METODE PENELITIAN, PERANCANGAN
  * Bab 4: HASIL, IMPLEMENTASI, PENGUJIAN
  * Bab 5: KESIMPULAN, PENUTUP, CONCLUSION

**Output JSON harus valid, contoh format front-end:**
{
  "score": 0,
  "percentage": 0.0,
  "status": "TIDAK LAYAK",
  "details": {
    "Abstrak": {"status": "Tidak ditemukan", "notes": "Abstrak tidak ditemukan"},
    "Format Teks": {"font": "Times New Roman", "spacing": 1, "notes": "Format teks diasumsikan sesuai"},
    "Margin": {"top": "3.0cm", "bottom": "2.5cm", "notes": "Margin diasumsikan sesuai standar ITS"},
    "Struktur Bab": {"Bab 1": "✗", "Bab 2": "✗", "Bab 3": "✗", "Bab 4": "✗", "Bab 5": "✗", "notes": "Struktur bab tidak terdeteksi"},
    "Daftar Pustaka": {"references_count": 0, "format": "APA ✗", "notes": "Daftar pustaka tidak ditemukan"},
    "Cover & Halaman Formal": {"status": "Tidak lengkap", "notes": "Halaman cover tidak terdeteksi"}
  },
  "document_info": {"jenis_dokumen": "Tidak Diketahui", "total_halaman": 0, "ukuran_file": "0 KB", "format_file": "PDF"}
}

⚠️ Output HARUS berupa JSON valid, tanpa penjelasan tambahan.
"""

# ===== baca file PDF =====
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
        print(json.dumps({"error": f"Gagal membaca PDF: {e}"}))
        return None

# ===== generate prompt lengkap =====
def create_full_prompt(pdf_content):
    """Buat prompt lengkap untuk analisis front-end"""
    pdf_preview = pdf_content['full_text'][:32000]  # Ditingkatkan menjadi 32000 karakter
    
    # Deteksi bab yang ada
    found_chapters = detect_chapters(pdf_content['full_text'])
    chapter_status = {
        'Bab 1': '✓' if '1' in found_chapters else '✗',
        'Bab 2': '✓' if '2' in found_chapters else '✗',
        'Bab 3': '✓' if '3' in found_chapters else '✗',
        'Bab 4': '✓' if '4' in found_chapters else '✗',
        'Bab 5': '✓' if '5' in found_chapters else '✗'
    }
    
    chapter_info = "\n".join([f"- {bab}: {status}" for bab, status in chapter_status.items()])
    
    return f"""ANALISIS DOKUMEN TUGAS AKHIR UNTUK FRONT-END

DETEKSI BAB:
{chapter_info}

**DATA DOKUMEN:**
- Jumlah halaman: {pdf_content['total_pages']}
- Total teks: {len(pdf_content['full_text'])} karakter

**KONTEN DOKUMEN (preview 8000 karakter pertama):**
{pdf_preview}
[...]

**TUGAS ANALISIS & EVALUASI UNTUK FRONT-END:**
{EVALUATION_FRONTEND_PROMPT}
"""

# ===== kirim ke model AI =====
def query_model(model, prompt, url, timeout):
    """Query model AI dan return response"""
    payload = {
        "model": model,
        "prompt": prompt,
        "stream": False,
        "options": {
            "temperature": 0.1,
            "top_p": 0.9
        }
    }
    try:
        start_time = time.time()
        response = requests.post(url, json=payload, timeout=timeout)
        end_time = time.time()
        if response.status_code == 200:
            result = response.json()
            return {
                "success": True,
                "response": result.get("response", ""),
                "time_taken": round(end_time - start_time, 2)
            }
        else:
            return {"success": False, "error": f"HTTP {response.status_code}: {response.text}", "time_taken": 0}
    except RequestException as e:
        return {"success": False, "error": f"Connection error: {e}", "time_taken": 0}

# ===== MAIN =====
if __name__ == "__main__":
    # Pastikan output Python menggunakan UTF-8
    if sys.stdout.encoding.lower() != 'utf-8':
        sys.stdout.reconfigure(encoding='utf-8')

    if len(sys.argv) < 2:
        print(json.dumps({"error": "File PDF tidak diberikan"}, ensure_ascii=False))
        sys.exit(1)

    pdf_path = sys.argv[1]
    if not os.path.exists(pdf_path):
        print(json.dumps({"error": f"File PDF '{pdf_path}' tidak ditemukan"}, ensure_ascii=False))
        sys.exit(1)

    pdf_content = read_pdf_content(pdf_path)
    if not pdf_content:
        sys.exit(1)

    full_prompt = create_full_prompt(pdf_content)

    model = models[0]
    result = query_model(model, full_prompt, url, timeout_seconds)

    # Output JSON siap untuk front-end
    if result['success']:
        try:
            response_json = json.loads(result['response'])
            print(json.dumps(response_json, ensure_ascii=False, indent=2))
        except Exception as e:
            print(json.dumps({
                "error": f"AI response bukan JSON valid: {e}",
                "raw_response": result['response']
            }, ensure_ascii=False))
    else:
        print(json.dumps({"error": result.get("error", "Unknown error")}, ensure_ascii=False))
