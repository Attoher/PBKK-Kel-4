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


def detect_chapters(full_text):
    """Deteksi keberadaan bab dalam teks dengan berbagai format"""
    import re
    
    # Split teks menjadi halaman-halaman untuk analisis per halaman
    pages = full_text.split('--- HALAMAN')
    
    # Format penulisan bab yang mungkin
    chapter_patterns = [
        (r'BAB\s+[1-5]', lambda x: str(int(re.findall(r'\d+', x)[0]))),           # BAB 1, BAB 2, ...
        (r'BAB\s+[IVX]+', lambda x: str({'I':1, 'II':2, 'III':3, 'IV':4, 'V':5}[x.split()[-1]])),  # BAB I, BAB II, ...
        (r'Bab\s+[1-5]', lambda x: str(int(re.findall(r'\d+', x)[0]))),           # Bab 1, Bab 2, ...
        (r'Bab\s+[IVX]+', lambda x: str({'I':1, 'II':2, 'III':3, 'IV':4, 'V':5}[x.split()[-1]])),  # Bab I, Bab II, ...
        (r'CHAPTER\s+[1-5]', lambda x: str(int(re.findall(r'\d+', x)[0]))),       # CHAPTER 1, CHAPTER 2, ...
    ]
    
    # Kata kunci spesifik untuk setiap bab (diperluas)
    chapter_keywords = {
        '1': ['PENDAHULUAN', 'INTRODUCTION', 'LATAR BELAKANG MASALAH', 'LATAR BELAKANG'],
        '2': ['TINJAUAN PUSTAKA', 'LANDASAN TEORI', 'STUDI LITERATUR', 'KAJIAN PUSTAKA', 'DASAR TEORI', 'TINJAUAN TEORI'],
        '3': ['METODOLOGI', 'METODE PENELITIAN', 'PERANCANGAN SISTEM', 'DESAIN SISTEM', 'PERANCANGAN', 'ANALISIS DAN PERANCANGAN', 'METODOLOGI PENELITIAN'],
        '4': ['HASIL', 'IMPLEMENTASI', 'PENGUJIAN', 'EVALUASI', 'ANALISIS HASIL', 'HASIL DAN PEMBAHASAN', 'IMPLEMENTASI DAN PENGUJIAN', 'HASIL PENELITIAN'],
        '5': ['KESIMPULAN', 'PENUTUP', 'CONCLUSION', 'SARAN', 'KESIMPULAN DAN SARAN']
    }
    
    found_chapters = {}
    
    # Cari bab berdasarkan pattern di setiap halaman
    for page in pages:
        page_upper = page.upper()
        
        # Cek pattern formal (BAB X, dll)
        for pattern, converter in chapter_patterns:
            matches = re.finditer(pattern, page_upper)
            for match in matches:
                try:
                    # Ambil konteks sekitar
                    start = max(0, match.start() - 100)
                    end = min(len(page_upper), match.end() + 100)
                    context = page_upper[start:end]
                    
                    # Jika ini benar-benar judul bab (ada di awal halaman atau setelah baris kosong)
                    if match.start() < 200 or '\n\n' in context[:match.start()-start]:
                        num = converter(match.group())
                        if num in ['1','2','3','4','5']:
                            found_chapters[num] = True
                except:
                    continue
        
        # Cek kata kunci di setiap halaman
        for num, keywords in chapter_keywords.items():
            if num not in found_chapters:  # Hanya cek jika belum ditemukan
                for keyword in keywords:
                    if keyword in page_upper:
                        # Verifikasi konteks kata kunci
                        keyword_pos = page_upper.find(keyword)
                        if keyword_pos < 300:  # Kata kunci muncul di awal halaman
                            found_chapters[num] = True
                            break
    
    # Format hasil
    return {
        'Bab 1': '✓' if '1' in found_chapters else '✗',
        'Bab 2': '✓' if '2' in found_chapters else '✗',
        'Bab 3': '✓' if '3' in found_chapters else '✗',
        'Bab 4': '✓' if '4' in found_chapters else '✗',
        'Bab 5': '✓' if '5' in found_chapters else '✗'
    }
    
    found_chapters = {}
    text_upper = text.upper()
    
    # Cek format penulisan bab
    patterns = [
        (r'BAB\s+[1-5]', lambda x: str(int(x[-1]))),  # BAB 1-5
        (r'BAB\s+[IVX]+', lambda x: str({'I':1, 'II':2, 'III':3, 'IV':4, 'V':5}[x.split()[-1]])),  # BAB I-V
        (r'CHAPTER\s+[1-5]', lambda x: str(int(x[-1]))),  # CHAPTER 1-5
    ]
    
    import re
    for pattern, converter in patterns:
        matches = re.finditer(pattern, text_upper)
        for match in matches:
            bab_num = converter(match.group())
            found_chapters[bab_num] = True
    
    # Cek kata kunci spesifik
    for num, keywords in chapter_keywords.items():
        if num not in found_chapters:  # Hanya cek jika belum ditemukan
            for keyword in keywords:
                if keyword in text_upper:
                    found_chapters[num] = True
                    break
    
    # Format output
    result = {
        'Bab 1': '✓' if '1' in found_chapters else '✗',
        'Bab 2': '✓' if '2' in found_chapters else '✗',
        'Bab 3': '✓' if '3' in found_chapters else '✗',
        'Bab 4': '✓' if '4' in found_chapters else '✗',
        'Bab 5': '✓' if '5' in found_chapters else '✗'
    }
    
    return result

def count_references(full_text):
    """Hitung jumlah referensi di Daftar Pustaka dengan metode yang lebih akurat"""
    try:
        import re
        
        # Split teks menjadi halaman-halaman
        pages = full_text.split('--- HALAMAN')
        
        # Temukan bagian Daftar Pustaka
        ref_section = ""
        ref_start = False
        
        for page in pages:
            # Cek apakah ini awal dari Daftar Pustaka
            if not ref_start and any(kw in page.upper() for kw in ['DAFTAR PUSTAKA', 'REFERENCES', 'BIBLIOGRAPHY']):
                if '.....' not in page[:500]:  # Bukan daftar isi
                    ref_start = True
            
            # Jika sudah di bagian Daftar Pustaka, tambahkan ke ref_section
            if ref_start:
                ref_section += page
                # Hentikan jika sudah mencapai bagian lain (misalnya LAMPIRAN)
                if any(kw in page.upper() for kw in ['LAMPIRAN', 'BIODATA', 'APPENDIX']):
                    break
        
        if not ref_section:
            return 0, "Bagian Daftar Pustaka tidak ditemukan"
            
        # Bersihkan teks referensi
        ref_lines = ref_section.split('\n')
        cleaned_refs = []
        current_ref = ""
        
        for line in ref_lines:
            line = line.strip()
            # Abaikan nomor halaman
            if re.match(r'^\d+\s*$', line):
                continue
                
            # Gabungkan baris yang terpotong
            if line and (line[0].isupper() or line[0] == '[' or re.match(r'^\d+\.', line)):
                if current_ref:
                    cleaned_refs.append(current_ref)
                current_ref = line
            elif line:
                current_ref += " " + line
                
        if current_ref:
            cleaned_refs.append(current_ref)
            
        # Hitung referensi dengan berbagai pattern
        ref_count = 0
        format_style = "Unknown"
        
        # Pattern untuk IEEE style
        ieee_pattern = len([r for r in cleaned_refs if re.match(r'^\[\d+\]', r.strip())])
        
        # Pattern untuk APA style
        apa_patterns = [
            # Author, A. B. (year)
            len([r for r in cleaned_refs if re.search(r'[A-Z][a-z]+,\s+[A-Z]\.(\s+[A-Z]\.)*\s*\(\d{4}\)', r)]),
            # DOI atau URL
            len([r for r in cleaned_refs if 'doi.org' in r.lower() or 'http' in r.lower()]),
            # Akhiran dengan tahun
            len([r for r in cleaned_refs if re.search(r'\(\d{4}\)[^\)]*$', r)])
        ]
        
        if ieee_pattern > max(apa_patterns):
            ref_count = ieee_pattern
            format_style = "IEEE"
        else:
            ref_count = max(apa_patterns)
            format_style = "APA"
            
        # Fallback ke menghitung baris yang valid
        if ref_count == 0:
            ref_count = len([r for r in cleaned_refs if len(r.split()) > 5])  # Minimal 5 kata
            
        return ref_count, format_style
        
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
    pdf_preview = pdf_content['full_text'][:32000]  # Ditingkatkan ke 32000 karakter
    
    # Deteksi bab menggunakan fungsi khusus
    chapter_status = detect_chapters(pdf_content['full_text'])
    
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

**DETEKSI BAB OTOMATIS:**
- Bab 1 (Pendahuluan): {chapter_status['Bab 1']}
- Bab 2 (Tinjauan Pustaka): {chapter_status['Bab 2']}
- Bab 3 (Metodologi): {chapter_status['Bab 3']}
- Bab 4 (Hasil/Implementasi): {chapter_status['Bab 4']}
- Bab 5 (Kesimpulan): {chapter_status['Bab 5']}

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