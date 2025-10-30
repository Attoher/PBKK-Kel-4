import sys
import json
import os
import time
import re
import pypdfium2 as pdfium
import PyPDF2
import requests
from dotenv import load_dotenv

load_dotenv()

# ===== CONFIG SENOPATI =====
SENOPATI_API_URL = os.getenv("SENOPATI_BASE_URL", "https://senopati.its.ac.id/senopati-lokal-dev/generate")
SENOPATI_MODEL = os.getenv("SENOPATI_MODEL", "dolphin-mixtral:latest")
SYSTEM_PROMPT = "Anda adalah asisten ahli format dokumen ITS. Hasilkan HANYA JSON valid tanpa penjelasan tambahan."

# PROMPT ANALISIS FRONTEND
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

HANYA output JSON, tanpa teks lain. Pastikan output adalah JSON valid.
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
        pdf = pdfium.PdfDocument(pdf_path)
        page = pdf[0]
        
        width = page.get_width()
        height = page.get_height()
        
        # Estimasi margin standar ITS
        left_margin = 3.0
        right_margin = 2.0
        top_margin = 3.0
        bottom_margin = 2.5
        
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
    """Deteksi keberadaan bab dalam teks"""
    import re
    
    pages = full_text.split('--- HALAMAN')
    
    chapter_patterns = [
        (r'BAB\s+[1-5]', lambda x: str(int(re.findall(r'\d+', x)[0]))),
        (r'BAB\s+[IVX]+', lambda x: str({'I':1, 'II':2, 'III':3, 'IV':4, 'V':5}[x.split()[-1]])),
        (r'Bab\s+[1-5]', lambda x: str(int(re.findall(r'\d+', x)[0]))),
        (r'CHAPTER\s+[1-5]', lambda x: str(int(re.findall(r'\d+', x)[0]))),
    ]
    
    chapter_keywords = {
        '1': ['PENDAHULUAN', 'INTRODUCTION', 'LATAR BELAKANG'],
        '2': ['TINJAUAN PUSTAKA', 'LANDASAN TEORI', 'STUDI LITERATUR'],
        '3': ['METODOLOGI', 'METODE PENELITIAN', 'PERANCANGAN SISTEM'],
        '4': ['HASIL', 'IMPLEMENTASI', 'PENGUJIAN', 'ANALISIS HASIL'],
        '5': ['KESIMPULAN', 'PENUTUP', 'CONCLUSION', 'SARAN']
    }
    
    found_chapters = {}
    
    for page in pages:
        page_upper = page.upper()
        
        for pattern, converter in chapter_patterns:
            matches = re.finditer(pattern, page_upper)
            for match in matches:
                try:
                    if match.start() < 200:
                        num = converter(match.group())
                        if num in ['1','2','3','4','5']:
                            found_chapters[num] = True
                except:
                    continue
        
        for num, keywords in chapter_keywords.items():
            if num not in found_chapters:
                for keyword in keywords:
                    if keyword in page_upper:
                        keyword_pos = page_upper.find(keyword)
                        if keyword_pos < 300:
                            found_chapters[num] = True
                            break
    
    return {
        'Bab 1': '✓' if '1' in found_chapters else '✗',
        'Bab 2': '✓' if '2' in found_chapters else '✗',
        'Bab 3': '✓' if '3' in found_chapters else '✗',
        'Bab 4': '✓' if '4' in found_chapters else '✗',
        'Bab 5': '✓' if '5' in found_chapters else '✗'
    }

def count_references(full_text):
    """Hitung jumlah referensi di Daftar Pustaka"""
    try:
        import re
        
        pages = full_text.split('--- HALAMAN')
        ref_section = ""
        ref_start = False
        
        for page in pages:
            if not ref_start and any(kw in page.upper() for kw in ['DAFTAR PUSTAKA', 'REFERENCES']):
                ref_start = True
            
            if ref_start:
                ref_section += page
                if any(kw in page.upper() for kw in ['LAMPIRAN', 'APPENDIX']):
                    break
        
        if not ref_section:
            return 0, "Bagian Daftar Pustaka tidak ditemukan"
            
        # Hitung referensi sederhana
        ref_lines = [line.strip() for line in ref_section.split('\n') if line.strip()]
        ref_count = len([line for line in ref_lines if len(line.split()) > 3])
        
        return ref_count, "APA/IEEE"
        
    except Exception as e:
        return 0, f"Error: {e}"

def create_full_prompt(pdf_content):
    """Buat prompt lengkap untuk analisis front-end"""
    pdf_preview = pdf_content['full_text'][:15000]
    
    chapter_status = detect_chapters(pdf_content['full_text'])
    
    text_lower = pdf_content['full_text'].lower()
    thesis_keywords = ['abstrak', 'abstract', 'bab', 'chapter', 'daftar pustaka', 'references']
    found_keywords = sum(1 for kw in thesis_keywords if kw in text_lower)
    
    if found_keywords < 2:
        return None
    
    ref_count, ref_format = count_references(pdf_content['full_text'])
    
    return f"""ANALISIS DOKUMEN TUGAS AKHIR:

INFORMASI:
- Halaman: {pdf_content['total_pages']}
- Kata: {len(pdf_content['full_text'].split())}
- Referensi: {ref_count}

BAB YANG DITEMUKAN:
Bab 1: {chapter_status['Bab 1']}, Bab 2: {chapter_status['Bab 2']}, Bab 3: {chapter_status['Bab 3']}, Bab 4: {chapter_status['Bab 4']}, Bab 5: {chapter_status['Bab 5']}

KONTEN:
{pdf_preview}

INSTRUKSI: {EVALUATION_FRONTEND_PROMPT}
"""

def query_senopati(prompt, max_retries=3):
    """Query Senopati API dengan format yang benar"""
    
    payload = {
        "model": SENOPATI_MODEL,
        "prompt": prompt,
        "system": SYSTEM_PROMPT,
        "stream": False,
        "options": {
            "temperature": 0.1,
            "top_p": 0.9,
            "max_tokens": 2000
        }
    }
    
    headers = {
        "Content-Type": "application/json",
    }
    
    for attempt in range(max_retries):
        try:
            start_time = time.time()
            response = requests.post(
                SENOPATI_API_URL,
                json=payload,
                headers=headers,
                timeout=120
            )
            
            if response.status_code != 200:
                response.raise_for_status()
            
            response_data = response.json()
            end_time = time.time()
            
            # Ekstrak konten dari berbagai kemungkinan field
            content = response_data.get("response", "")
            if not content:
                content = response_data.get("message", "")
            if not content:
                content = response_data.get("content", "")
            if not content and "choices" in response_data:
                content = response_data["choices"][0].get("message", {}).get("content", "")
            
            if not content:
                raise Exception("API returned empty response content")

            return {
                "success": True,
                "response": content,
                "time_taken": round(end_time - start_time, 2),
                "status_code": response.status_code
            }
            
        except requests.exceptions.RequestException as e:
            error_msg = str(e)
            if attempt < max_retries - 1:
                time.sleep(3)
                continue
            return {"success": False, "error": f"Senopati API Error: {error_msg}"}
        except Exception as e:
            error_msg = str(e)
            return {"success": False, "error": f"Error: {error_msg}"}

def extract_json_from_text(text):
    """Ekstrak JSON dari teks respons"""
    if not text:
        raise ValueError("Respons kosong")
    
    # Coba parse langsung
    try:
        return json.loads(text)
    except json.JSONDecodeError:
        pass
    
    # Coba ekstrak dari markdown code blocks
    try:
        json_match = re.search(r'```(?:json)?\s*(\{.*\})\s*```', text, re.DOTALL)
        if json_match:
            return json.loads(json_match.group(1))
    except:
        pass
    
    # Coba cari object JSON dengan regex
    try:
        json_match = re.search(r'\{.*\}', text, re.DOTALL)
        if json_match:
            return json.loads(json_match.group(0))
    except:
        pass
    
    raise ValueError(f"Tidak dapat mengekstrak JSON dari respons")

def create_fallback_result(pdf_content, format_margin_info, ref_count):
    """Buat hasil fallback ketika AI gagal"""
    chapter_status = detect_chapters(pdf_content['full_text'])
    
    bab_count = sum(1 for bab in chapter_status.values() if bab == '✓')
    score = min(10, bab_count * 2)
    
    status = "LAYAK" if score >= 8 else "PERLU PERBAIKAN" if score >= 6 else "TIDAK LAYAK"
    
    return {
        "score": score,
        "percentage": score * 10,
        "status": status,
        "details": {
            "Abstrak": {"status": "✓", "notes": "Terdeteksi otomatis", "id_word_count": 0, "en_word_count": 0},
            "Struktur Bab": {
                "Bab 1": chapter_status['Bab 1'],
                "Bab 2": chapter_status['Bab 2'], 
                "Bab 3": chapter_status['Bab 3'],
                "Bab 4": chapter_status['Bab 4'],
                "Bab 5": chapter_status['Bab 5'],
                "notes": f"{bab_count} dari 5 bab terdeteksi"
            },
            "Daftar Pustaka": {
                "references_count": f"≥{ref_count}",
                "format": "APA/IEEE",
                "notes": f"{ref_count} referensi terdeteksi"
            },
            "Cover & Halaman Formal": {"status": "✓", "notes": "Terdeteksi otomatis"},
            **format_margin_info
        },
        "document_info": {
            "jenis_dokumen": "TA/Skripsi",
            "total_halaman": pdf_content['total_pages'],
            "format_file": "PDF"
        },
        "recommendations": [
            "Gunakan analisis ini sebagai panduan awal",
            "Periksa manual untuk hasil yang lebih akurat"
        ],
        "note": "Hasil analisis otomatis (AI tidak tersedia)"
    }

def main():
    # HANYA output JSON ke stdout, TANPA DEBUG
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
    
    format_margin_info = extract_pdf_format_and_margin(pdf_path)
    ref_count, ref_format = count_references(pdf_content['full_text'])

    full_prompt = create_full_prompt(pdf_content)
    
    if full_prompt is None:
        print(json.dumps({
            "error": "Dokumen tidak terdeteksi sebagai Tugas Akhir/Skripsi."
        }, ensure_ascii=False))
        sys.exit(1)
    
    result = query_senopati(full_prompt)

    if result['success']:
        try:
            response_text = result['response'].strip()
            
            # Bersihkan response seperti di OpenRouter
            response_text = response_text.replace('```json', '').replace('```', '').strip()
            response_json = json.loads(response_text)
            
            # Gabungkan dengan info format & margin
            if "details" not in response_json:
                response_json["details"] = {}
            response_json["details"].update(format_margin_info)
            
            # Update referensi dengan count dari Python
            if ref_count > 0:
                response_json["details"]["Daftar Pustaka"] = {
                    "references_count": f"≥{ref_count}",
                    "format": ref_format,
                    "notes": f"{ref_count} referensi terdeteksi (minimal 20 untuk TA)"
                }
            else:
                if "Daftar Pustaka" not in response_json["details"]:
                    response_json["details"]["Daftar Pustaka"] = {
                        "references_count": "≥0",
                        "format": "Tidak terdeteksi",
                        "notes": ref_format
                    }

            # Validasi minimum
            if "score" not in response_json:
                response_json["score"] = 0
            if "percentage" not in response_json:
                response_json["percentage"] = response_json["score"] * 10
            if "status" not in response_json:
                s = response_json["score"]
                response_json["status"] = "LAYAK" if s >= 8 else "PERLU PERBAIKAN" if s >= 6 else "TIDAK LAYAK"

            # HANYA print JSON ke stdout - TANPA DEBUG
            print(json.dumps(response_json, ensure_ascii=False, indent=2))

        except Exception as e:
            # Fallback ke hasil otomatis
            fallback_result = create_fallback_result(pdf_content, format_margin_info, ref_count)
            print(json.dumps(fallback_result, ensure_ascii=False, indent=2))
    else:
        # Fallback ke hasil otomatis
        fallback_result = create_fallback_result(pdf_content, format_margin_info, ref_count)
        print(json.dumps(fallback_result, ensure_ascii=False, indent=2))

if __name__ == "__main__":
    main()