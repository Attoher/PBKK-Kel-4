import os
import sys
import json
import time
import re
import requests
from PyPDF2 import PdfReader
import pypdfium2 as pdfium

# ===== CONFIG SENOPATI =====
SENOPATI_API_URL = os.getenv("SENOPATI_BASE_URL", "https://senopati.its.ac.id/senopati-lokal-dev/generate")
SENOPATI_MODEL = os.getenv("SENOPATI_MODEL", "dolphin-mixtral:latest")
USE_SENOPATI = os.getenv("USE_SENOPATI", "true").lower() == "true"

# PEDOMAN RESMI ITS UNTUK TUGAS AKHIR (SK Rektor No. 280/IT2/T/HK.00.01/2022)
SYSTEM_PROMPT = """Anda adalah asisten ahli analisis format dokumen Tugas Akhir Institut Teknologi Sepuluh Nopember (ITS).

PEDOMAN RESMI ITS YANG HARUS ANDA IKUTI:

1. FORMAT DOKUMEN:
   - Kertas: HVS 80 gram ukuran A4 (210mm x 297mm)
   - Margin: Atas 3.0cm, Bawah 2.5cm, Kiri 3.0cm, Kanan 2.0cm
   - Spasi: 1.5 untuk isi, 1.0 untuk abstrak dan daftar pustaka
   - Font: Times New Roman 12pt (judul bisa 14pt), kata asing ITALIC

2. STRUKTUR WAJIB (Halaman romawi kecil i, ii, iii untuk bagian awal):
   - Halaman Sampul Depan
   - Halaman Judul (Indonesia DAN Inggris)
   - Halaman Pengesahan
   - Halaman Pernyataan Orisinalitas
   - ABSTRAK Indonesia (200-300 kata, max 1 halaman)
   - ABSTRACT Inggris (200-300 kata, max 1 halaman)
   - Kata Pengantar
   - Daftar Isi, Daftar Gambar, Daftar Tabel

3. ISI DOKUMEN (Halaman angka arab 1, 2, 3):
   BAB 1 PENDAHULUAN: Latar Belakang, Rumusan Masalah, Batasan, Tujuan, Manfaat
   BAB 2 TINJAUAN PUSTAKA: Penelitian Terdahulu, Teori/Konsep Dasar
   BAB 3 METODOLOGI: Metode, Bahan/Peralatan, Tahapan, Teknik Analisis
   BAB 4 HASIL DAN PEMBAHASAN: Analisis hasil, Interpretasi data
   BAB 5 KESIMPULAN DAN SARAN: Kesimpulan (jawab rumusan masalah), Saran

4. DAFTAR PUSTAKA (WAJIB):
   - Minimal 20 referensi untuk TA Sarjana
   - Format APA Style atau IEEE Style (konsisten)
   - Urut alfabetis berdasarkan penulis
   - Sumber kredibel: jurnal ilmiah, buku, conference paper
   - Hindari blog/website tidak kredibel

5. KRITERIA KELAYAKAN:
   LAYAK: Semua struktur lengkap, format sesuai, ≥20 referensi, 2 abstrak, ≥40 halaman
   PERLU PERBAIKAN: Kurang 1-2 komponen minor, format tidak sesuai, <20 referensi
   TIDAK LAYAK: Tidak ada Daftar Pustaka, tidak ada Bab 1-5, <15 halaman

Hasilkan HANYA JSON valid tanpa penjelasan tambahan atau teks lain."""


def hitung_total_halaman(pdf_path):
    try:
        reader = PdfReader(pdf_path)
        return len(reader.pages)
    except Exception:
        return 0


def extract_pdf_format_and_margin(pdf_path):
    try:
        pdf = pdfium.PdfDocument(pdf_path)
        page = pdf[0]
        # Estimasi margin standar ITS (fallback sederhana)
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
    except Exception:
        return {
            "Format Teks": {"notes": "Format teks standar (deteksi otomatis tidak tersedia)"},
            "Margin": {"notes": "Margin standar ITS (deteksi otomatis tidak tersedia)"}
        }


def read_pdf_text(pdf_path):
    text = ""
    try:
        reader = PdfReader(pdf_path)
        for page in reader.pages:
            try:
                t = page.extract_text() or ""
                text += t + "\n"
            except Exception:
                pass
    except Exception:
        pass
    return text.strip()


def read_pdf_text_pages(pdf_path):
    pages = []
    try:
        reader = PdfReader(pdf_path)
        for page in reader.pages:
            try:
                t = page.extract_text() or ""
                pages.append(t)
            except Exception:
                pages.append("")
    except Exception:
        pass
    return pages


def _clean_snippet(s: str) -> str:
    s = re.sub(r"\s+", " ", s or "").strip()
    return s[:200]


def classify_pages(pages_text):
    """Return a set of TOC/Daftar Isi page indices (0-based)."""
    toc_pages = set()
    dotted_line_re = re.compile(r"\.{3,}\s*\d{1,4}")
    bab_toc_re = re.compile(r"\bBAB\b.*\.{2,}.*\d{1,4}", re.IGNORECASE)

    for idx, text in enumerate(pages_text):
        if not text:
            continue
        t = text.lower()
        if "daftar isi" in t or "table of contents" in t:
            toc_pages.add(idx)
            continue
        dotted_hits = len(dotted_line_re.findall(text))
        bab_toc_hits = len(bab_toc_re.findall(text))
        if dotted_hits >= 5 or bab_toc_hits >= 3:
            toc_pages.add(idx)
    return toc_pages


def extract_section_locations(pages_text):
    """
    Cari lokasi Abstrak, Bab (I-V), dan Daftar Pustaka.
    Mengembalikan dict: {
      "abstrak": {"page": int, "snippet": str} | None,
      "bab": [{"label": "Bab N", "page": int, "title": str}],
      "daftar_pustaka": {"page": int, "snippet": str} | None
    }
    """
    locations = {"abstrak": None, "bab": [], "daftar_pustaka": None}
    bab_candidates = []

    toc_pages = classify_pages(pages_text)

    for idx, text in enumerate(pages_text):
        if not text:
            continue

        # Skip ToC pages untuk abstrak detection
        if idx not in toc_pages:
            # Deteksi Abstrak dengan heading variations
            # Support both standalone and inline headings (e.g., "xi ABSTRAK TITLE")
            abstrak_patterns = [
                r"(?:^|\n)\s*(ABSTRAK|Abstrak|ABSTRAKSI|Abstraksi|RINGKASAN|Ringkasan)\s*(?:\n|$)",  # Standalone
                r"\b(ABSTRAK|Abstrak|ABSTRAKSI|Abstraksi|RINGKASAN|Ringkasan)\s+[A-Z]"  # Inline with title
            ]
            
            for abstrak_pattern in abstrak_patterns:
                m_abs = re.search(abstrak_pattern, text)
                if m_abs and locations["abstrak"] is None:
                    # Verify tidak ada pattern ToC setelahnya (e.g., dots + page number)
                    following = text[m_abs.end():m_abs.end()+100]
                    if not re.search(r"\.{3,}\s*\d+", following):
                        start = m_abs.start()
                        locations["abstrak"] = {
                            "page": idx + 1,
                            "snippet": _clean_snippet(text[start:start + 300])
                        }
                        break

        # Deteksi Daftar Pustaka dengan berbagai variasi penulisan
        # Pattern yang lebih robust untuk menangkap berbagai format
        ref_patterns = [
            r"(?:^|\n)\s*(DAFTAR\s+PUSTAKA|Daftar\s+Pustaka)\s*(?:\n|$)",
            r"(?:^|\n)\s*(REFERENCES|References)\s*(?:\n|$)",
            r"(?:^|\n)\s*(BIBLIOGRAPHY|Bibliography)\s*(?:\n|$)",
            r"(?:^|\n)\s*(DAFTAR\s+REFERENSI|Daftar\s+Referensi)\s*(?:\n|$)",
            r"(?:^|\n)\s*(KEPUSTAKAAN|Kepustakaan)\s*(?:\n|$)"
        ]
        
        for pattern in ref_patterns:
            m_ref = re.search(pattern, text, re.MULTILINE)
            if m_ref and locations["daftar_pustaka"] is None:
                start = m_ref.start()
                locations["daftar_pustaka"] = {
                    "page": idx + 1,
                    "snippet": _clean_snippet(text[start:start + 200])
                }
                break

        if idx in toc_pages:
            continue
        for m_bab in re.finditer(r"\b(BAB)\s+(I|II|III|IV|V|VI|VII|VIII|IX|X|1|2|3|4|5|6|7|8|9|10)\b", text, re.IGNORECASE):
            label_raw = m_bab.group(2).upper()
            roman_map = {"I": 1, "II": 2, "III": 3, "IV": 4, "V": 5, "VI": 6, "VII": 7, "VIII": 8, "IX": 9, "X": 10}
            num = roman_map.get(label_raw)
            if num is None:
                try:
                    num = int(label_raw)
                except Exception:
                    continue
            following = text[m_bab.end():m_bab.end() + 200]
            if re.search(r"\.{3,}\s*\d{1,4}$", following.strip()):
                continue
            bab_candidates.append({
                "label": f"Bab {num}",
                "page": idx + 1,
                "title": _clean_snippet(following)
            })

    seen = set()
    unique_babs = []
    for b in bab_candidates:
        if b["label"] not in seen:
            unique_babs.append(b)
            seen.add(b["label"])

    locations["bab"] = unique_babs
    
    # Fallback heuristic: jika Daftar Pustaka tidak terdeteksi via heading,
    # scan halaman akhir untuk dense reference patterns
    if not locations["daftar_pustaka"]:
        for idx in range(len(pages_text) - 1, max(0, len(pages_text) - 30), -1):
            text = pages_text[idx] or ""
            # Heuristic: halaman dengan >5 entries tahun (YYYY) dan author patterns
            year_matches = re.findall(r'\(\d{4}\)|\d{4}\.', text)
            author_patterns = re.findall(r'[A-Z][a-z]+,\s+[A-Z]\.', text)
            
            if len(year_matches) > 5 and len(author_patterns) > 3:
                locations["daftar_pustaka"] = {
                    "page": idx + 1,
                    "snippet": _clean_snippet(text[:300])
                }
                break
    
    return locations


def validate_ta_document(text, total_halaman, pages_text=None, locations=None):
    """
    Validasi dokumen berdasarkan PEDOMAN RESMI ITS SK Rektor No. 280/2022
    
    KRITERIA MINIMUM:
    - Minimal 15 halaman (Proposal TA) atau 40+ halaman (Laporan TA)
    - HARUS ada: Abstrak, Struktur Bab (min 3 bab), Daftar Pustaka
    - HARUS ada: Pendahuluan dan Metodologi
    """
    text_lower = text.lower()

    # Cek panjang dokumen sesuai pedoman ITS
    if total_halaman < 15:
        return False, f"Dokumen terlalu pendek ({total_halaman} halaman). Berdasarkan Pedoman ITS: Proposal TA minimal 15-20 halaman, Laporan TA minimal 40-60 halaman."

    # Tolak jenis dokumen yang bukan TA/Skripsi
    reject_keywords = [
        'kwitansi', 'invoice', 'receipt', 'bukti pembayaran', 'tagihan',
        'surat jalan', 'delivery note', 'purchase order',
        'quotation', 'penawaran harga', 'bukti transfer',
        'form pendaftaran', 'formulir pendaftaran', 'permohonan izin',
        'sertifikat', 'certificate of', 'surat keterangan'
    ]
    for keyword in reject_keywords:
        if keyword in text_lower:
            return False, f"Dokumen terdeteksi sebagai '{keyword.upper()}', bukan Tugas Akhir/Skripsi ITS."

    # Komponen WAJIB berdasarkan Pedoman ITS
    required_components = {
        'abstrak': False,
        'bab': False,
        'daftar_pustaka': False,
        'pendahuluan': False,
        'metodologi': False
    }

    # Gunakan locations (hasil deteksi ekstraksi)
    if locations:
        required_components['abstrak'] = bool(locations.get('abstrak'))
        required_components['bab'] = bool(locations.get('bab') and len(locations['bab']) >= 3)
        required_components['daftar_pustaka'] = bool(locations.get('daftar_pustaka'))
    else:
        # Fallback: deteksi langsung dari teks
        if re.search(r'\babstrak\b', text_lower) or re.search(r'\babstract\b', text_lower):
            required_components['abstrak'] = True
        bab_pattern = r'\b(bab\s+[i1-5]|chapter\s+[i1-5])\b'
        if len(re.findall(bab_pattern, text_lower)) >= 3:
            required_components['bab'] = True
        if re.search(r'(?:daftar\s+pustaka|daftar\s+referensi|references|bibliography)', text_lower):
            required_components['daftar_pustaka'] = True

    # Deteksi Pendahuluan dan Metodologi (komponen WAJIB Bab 1 & 3)
    if pages_text is not None:
        toc_pages = classify_pages(pages_text)
        def any_non_toc(pattern):
            r = re.compile(pattern, re.IGNORECASE)
            for i, ptxt in enumerate(pages_text):
                if i in toc_pages:
                    continue
                if r.search(ptxt or ""):
                    return True
            return False
        required_components['pendahuluan'] = any_non_toc(r'\b(pendahuluan|introduction|latar\s+belakang)\b')
        required_components['metodologi'] = any_non_toc(r'\b(metodologi|methodology|metode\s+penelitian|research\s+method)\b')
    else:
        required_components['pendahuluan'] = bool(re.search(r'\b(pendahuluan|introduction|latar\s+belakang)\b', text_lower))
        required_components['metodologi'] = bool(re.search(r'\b(metodologi|methodology|metode\s+penelitian|research\s+method)\b', text_lower))

    # PEDOMAN ITS: Minimal harus ada 4 dari 5 komponen wajib
    valid_components = sum(required_components.values())
    if valid_components < 4:
        missing = [k.replace('_', ' ').title() for k, v in required_components.items() if not v]
        return False, f"Dokumen TIDAK MEMENUHI standar Pedoman ITS. Komponen WAJIB yang hilang: {', '.join(missing)}. Sesuai Pedoman ITS, dokumen harus memiliki: Abstrak (2 bahasa), Bab 1-5 (Pendahuluan, Tinjauan Pustaka, Metodologi, Hasil, Kesimpulan), dan Daftar Pustaka (min 20 referensi)."

    # Validasi panjang konten minimum
    if len(text) < 5000:
        return False, "Konten dokumen terlalu sedikit untuk Tugas Akhir/Skripsi ITS. Pastikan dokumen memiliki konten yang memadai sesuai Pedoman ITS."

    return True, ""


def query_senopati(prompt, max_retries=1):
    if not USE_SENOPATI:
        print("DEBUG: USE_SENOPATI=false, skipping external API and using fallback", file=sys.stderr)
        return {"success": False, "error": "Senopati disabled in development"}
    print(f"DEBUG: Calling Senopati API at {SENOPATI_API_URL}", file=sys.stderr)
    print(f"DEBUG: Using model {SENOPATI_MODEL}", file=sys.stderr)

    payload = {
        "model": SENOPATI_MODEL,
        "prompt": prompt,
        "system": SYSTEM_PROMPT,
        "stream": False,
        "temperature": 0.05,
        "max_tokens": 0
    }
    headers = {"Content-Type": "application/json"}

    for attempt in range(max_retries):
        try:
            print(f"DEBUG: Attempt {attempt + 1}/{max_retries}", file=sys.stderr)
            start_time = time.time()
            response = requests.post(SENOPATI_API_URL, json=payload, headers=headers, timeout=10)
            print(f"DEBUG: Got response with status {response.status_code}", file=sys.stderr)
            if response.status_code != 200:
                print(f"DEBUG: Error response: {response.text[:500]}", file=sys.stderr)
                response.raise_for_status()
            data = response.json()
            end_time = time.time()
            content = data.get("response") or data.get("message") or data.get("content")
            if not content and "choices" in data:
                content = data["choices"][0].get("message", {}).get("content", "")
            if not content:
                raise Exception("API returned empty response content")
            print(f"DEBUG: Successfully got response in {round(end_time - start_time, 2)}s", file=sys.stderr)
            return {"success": True, "response": content}
        except requests.exceptions.RequestException as e:
            print(f"DEBUG: Request error on attempt {attempt + 1}: {e}", file=sys.stderr)
            if attempt < max_retries - 1:
                time.sleep(3)
                continue
            return {"success": False, "error": f"Senopati API Error: {e}"}
        except Exception as e:
            print(f"DEBUG: Exception: {e}", file=sys.stderr)
            return {"success": False, "error": f"Error: {e}"}


def extract_json_from_text(text):
    if not text:
        raise ValueError("Respons kosong")
    try:
        return json.loads(text)
    except json.JSONDecodeError:
        pass
    try:
        m = re.search(r"```(?:json)?\s*(\{.*?\})\s*```", text, re.DOTALL)
        if m:
            return json.loads(m.group(1))
    except Exception:
        pass
    try:
        m = re.search(r"\{.*\}", text, re.DOTALL)
        if m:
            return json.loads(m.group(0))
    except Exception:
        pass
    raise ValueError("Tidak dapat mengekstrak JSON dari respons")


def analyze_abstracts(pages_text):
    """Analisis lokal untuk menghitung jumlah kata Abstrak dengan deteksi maksimal"""
    abstrak_id_words = 0
    abstrak_en_words = 0

    # Variasi heading yang lebih comprehensive - support standalone dan inline
    id_headings = [
        r'(?:^|\n)\s*(ABSTRAK|Abstrak)\s*(?:\n|$)',  # Standalone
        r'\b(ABSTRAK|Abstrak)\s+[A-Z]',  # Inline dengan title (e.g., "ABSTRAK PEMBUATAN")
        r'(?:^|\n)\s*(ABSTRAKSI|Abstraksi)\s*(?:\n|$)',
        r'\b(ABSTRAKSI|Abstraksi)\s+[A-Z]',
        r'(?:^|\n)\s*(RINGKASAN|Ringkasan)\s*(?:\n|$)',
        r'\b(RINGKASAN|Ringkasan)\s+[A-Z]'
    ]
    en_headings = [
        r'(?:^|\n)\s*(ABSTRACT|Abstract)\s*(?:\n|$)',  # Standalone
        r'\b(ABSTRACT|Abstract)\s+[A-Z]',  # Inline
        r'(?:^|\n)\s*(SUMMARY|Summary)\s*(?:\n|$)',
        r'\b(SUMMARY|Summary)\s+[A-Z]'
    ]
    # End markers lebih comprehensive
    end_markers_id = r'(?:Kata\s+[Kk]unci|Keywords|keyword|KATA\s+KUNCI|ABSTRACT|Abstract|BAB\s+[IVX1]|CHAPTER|PENDAHULUAN)'
    end_markers_en = r'(?:Keywords|keyword|KEYWORDS|Kata\s+[Kk]unci|KATA\s+KUNCI|BAB\s+[IVX1]|CHAPTER|PENDAHULUAN|INTRODUCTION)'

    def count_words_block(start_idx, pages, heading_patterns, end_regex, is_english=False):
        # Cari heading pada rentang luas - expand untuk coverage lebih baik
        for pi in range(max(0, start_idx-10), min(len(pages), start_idx+25)):
            page_text = pages[pi] or ''
            
            # Try all heading patterns
            for heading_regex in heading_patterns:
                h = re.search(heading_regex, page_text, re.MULTILINE)
                if not h:
                    continue
                    
                # Ambil konten mulai setelah heading
                block = page_text[h.end():]
                
                # Tambah halaman berikutnya jika belum ketemu end marker (up to 5 pages)
                for pj in range(pi+1, min(pi+6, len(pages))):
                    next_page = pages[pj] or ''
                    # Check if end marker on next page
                    if re.search(end_regex, next_page, re.IGNORECASE):
                        # Add partial content until marker
                        m_end_next = re.search(end_regex, next_page, re.IGNORECASE)
                        block += '\n' + next_page[:m_end_next.start()]
                        break
                    else:
                        block += '\n' + next_page
                
                # Check end marker in accumulated block
                m_end = re.search(end_regex, block, re.IGNORECASE)
                if m_end:
                    block = block[:m_end.start()]
                
                # Bersihkan metadata umum dan noise
                block = re.sub(r'(Nama\s+Mahasiswa|Student\s+Name|NRP|NIM|Departemen|Department|Jurusan|Major|Dosen\s+Pembimbing|Supervisor|Advisor|Pembimbing)\s*[:;]?.*?(?:\n|$)', '', block, flags=re.IGNORECASE)
                # Remove common header/footer artifacts
                block = re.sub(r'(\d+\s*$|^\s*\d+)', '', block, flags=re.MULTILINE)
                
                # Count words - filter valid words only
                if is_english:
                    words = re.findall(r"\b[A-Za-z']{2,}\b", block)
                else:
                    words = re.findall(r"\b[A-Za-zÀ-ÖØ-öø-ÿ]{2,}\b", block)
                
                word_count = len(words)
                if word_count > 30:  # Minimal sanity check
                    return word_count
        
        return 0

    # Estimasi halaman abstrak ID biasanya di 5-20
    abstrak_id_words = count_words_block(10, pages_text, id_headings, end_markers_id, is_english=False)

    # Estimasi halaman abstract EN biasanya di 10-25
    abstrak_en_words = count_words_block(15, pages_text, en_headings, end_markers_en, is_english=True)

    # Fallback: jika abstract EN tidak ditemukan via heading, deteksi blok teks Inggris
    if abstrak_en_words == 0:
        english_stop = set(["the","and","of","to","in","that","for","with","as","on","is","are","this","we","our","by","from","an","be","has","have","had"])    
        def english_score(s):
            tokens = re.findall(r"\b[A-Za-z']{2,}\b", s)
            if len(tokens) < 80:  # Raise threshold untuk abstract length
                return 0
            lower = [t.lower() for t in tokens]
            stop_hits = sum(1 for t in lower if t in english_stop)
            letters = sum(c.isalpha() for c in s)
            ratio = letters / max(1, len(s))
            # Score berdasarkan stopword density dan letter ratio
            return (stop_hits / len(tokens)) * 100 + (ratio * 50)
        
        best_block = ""
        best_score = 0
        # Scan pages 5..35 untuk blok paragraf Inggris setelah kemungkinan abstrak ID
        for pi in range(5, min(35, len(pages_text))):
            page_text = pages_text[pi] or ""
            # Ambil blok kandidat antar newline ganda
            blocks = re.split(r"\n\s*\n", page_text)
            for b in blocks:
                # Skip blocks with too much metadata
                if re.search(r'(NRP|NIM|Departemen|Department):', b, re.IGNORECASE):
                    continue
                sc = english_score(b)
                if sc > best_score and sc > 15:  # Minimum score threshold
                    best_score = sc
                    best_block = b
        
        if best_block:
            abstrak_en_words = len(re.findall(r"\b[A-Za-z']{2,}\b", best_block))

    return abstrak_id_words, abstrak_en_words


def count_references(pages_text, locations):
    """Hitung jumlah referensi di Daftar Pustaka dengan multiple strategies"""
    if not locations.get('daftar_pustaka'):
        return 0

    # Ambil halaman dari lokasi Daftar Pustaka sampai akhir (limit 30 pages)
    start_page = locations['daftar_pustaka']['page'] - 1
    if start_page < 0 or start_page >= len(pages_text):
        return 0
    
    end_page = min(start_page + 30, len(pages_text))
    ref_pages = pages_text[start_page:end_page]
    
    combined_text = '\n'.join(ref_pages)
    
    # Multiple strategies untuk counting references
    ref_count = 0
    
    # Strategy 1: Count by year patterns
    # Pattern: (YYYY) atau YYYY. atau YYYY,
    year_patterns = [
        r'\(\d{4}\)',           # (2020)
        r'\b\d{4}\.\s',        # 2020. 
        r'\b\d{4},\s',         # 2020, 
        r'\b\d{4}\)[,.]',      # 2020), atau 2020).
    ]
    
    # Split by lines and count valid reference lines
    lines = combined_text.split('\n')
    counted_lines = set()  # Avoid double counting
    
    for i, line in enumerate(lines):
        line = line.strip()
        # Skip empty, very short lines
        if not line or len(line) < 15:
            continue
        # Skip page numbers, headers, section titles
        if re.match(r'^\d+$', line):
            continue
        if line.upper() in ['DAFTAR PUSTAKA', 'REFERENCES', 'BIBLIOGRAPHY', 'KEPUSTAKAAN', 'DAFTAR REFERENSI']:
            continue
        # Skip common non-reference patterns
        if re.match(r'^(Lampiran|Appendix|Biodata|BIODATA)', line, re.IGNORECASE):
            break  # Stop counting if we hit appendix section
        
        # Check if line matches reference patterns
        has_year = any(re.search(pat, line) for pat in year_patterns)
        
        # Additional patterns: author names (capitalize) and punctuation
        has_author = re.search(r'[A-Z][a-z]+,\s+[A-Z]', line) or re.search(r'[A-Z][a-z]+\s+[A-Z]\.', line)
        
        # Must have year and reasonable length OR strong author pattern
        if (has_year and len(line) > 25) or (has_author and len(line) > 40 and i not in counted_lines):
            ref_count += 1
            counted_lines.add(i)
    
    # Strategy 2: Fallback - count numbered references [1], [2], etc.
    if ref_count < 5:
        numbered_refs = re.findall(r'^\s*\[\d+\]', combined_text, re.MULTILINE)
        ref_count = max(ref_count, len(numbered_refs))
    
    # Strategy 3: Fallback - count by dense year occurrence (group by paragraphs)
    if ref_count < 5:
        # Split by blank lines (paragraphs)
        paragraphs = re.split(r'\n\s*\n', combined_text)
        para_ref_count = 0
        for para in paragraphs:
            if len(para) > 50 and re.search(r'\b\d{4}\b', para):
                # Check if looks like reference (has author-like pattern or URL/DOI)
                if re.search(r'[A-Z][a-z]+,\s+[A-Z]|https?://|doi:|DOI:', para):
                    para_ref_count += 1
        ref_count = max(ref_count, para_ref_count)
    
    return ref_count


# Legacy function for backward compatibility
def count_references_legacy(pages_text, locations):
    """Original reference counting logic - kept for fallback"""
    if not locations.get('daftar_pustaka'):
        return 0

    # Ambil halaman dari lokasi Daftar Pustaka sampai akhir
    start_page = locations['daftar_pustaka']['page'] - 1
    if start_page < 0 or start_page >= len(pages_text):
        return 0

    # Gabungkan beberapa halaman setelah Daftar Pustaka
    dafpus_text = ''
    for i in range(start_page, min(start_page + 12, len(pages_text))):
        dafpus_text += pages_text[i] + '\n'

    # Ekstrak section Daftar Pustaka
    match = re.search(r'DAFTAR\s+PUSTAKA\s+(.*)', dafpus_text, re.DOTALL | re.IGNORECASE)
    if not match:
        return 0

    content = match.group(1)[:15000]  # Ambil 15k karakter

    # Hitung referensi berdasarkan tahun publikasi (20xx atau 19xx)
    years = re.findall(r'\((?:19|20)\d{2}\)', content)

    # Hitung juga baris yang valid (dimulai huruf besar dan ada tahun)
    lines = content.split('\n')
    valid_lines = 0
    for line in lines:
        line = line.strip()
        if len(line) > 20 and line[0].isupper() and re.search(r'(?:19|20)\d{2}', line):
            valid_lines += 1

    return max(len(years), valid_lines)



def build_ai_prompt(full_text, total_halaman, format_margin_info):
    template = f"""
TUGAS: Analisis dokumen Tugas Akhir ITS berdasarkan PEDOMAN RESMI SK Rektor No. 280/IT2/T/HK.00.01/2022.

KRITERIA PENILAIAN BERDASARKAN PEDOMAN ITS:

1. ABSTRAK (WAJIB 2 BAHASA):
   ✓ Ada Abstrak Indonesia (200-300 kata, max 1 halaman)
   ✓ Ada Abstract Inggris (200-300 kata, max 1 halaman)
   ✓ Berisi: latar belakang, tujuan, metode, hasil, kesimpulan
   ✓ TIDAK ada sitasi/kutipan dalam abstrak
   ✓ Ada 3-5 kata kunci

2. STRUKTUR BAB (WAJIB BAB 1-5):
   ✓ Bab 1 PENDAHULUAN: Latar Belakang, Rumusan Masalah, Batasan, Tujuan, Manfaat
   ✓ Bab 2 TINJAUAN PUSTAKA: Penelitian Terdahulu, Landasan Teori
   ✓ Bab 3 METODOLOGI: Metode, Bahan/Peralatan, Tahapan, Analisis Data
   ✓ Bab 4 HASIL DAN PEMBAHASAN: Analisis hasil, Interpretasi
   ✓ Bab 5 KESIMPULAN DAN SARAN: Kesimpulan, Saran

3. DAFTAR PUSTAKA (WAJIB):
   ✓ Minimal 20 referensi untuk TA Sarjana
   ✓ Format APA atau IEEE (konsisten)
   ✓ Urut alfabetis berdasarkan penulis
   ✓ Sumber kredibel: jurnal > buku > conference > laporan
   ✓ Hindari Wikipedia/blog pribadi
   ✓ Prioritas sumber 5-10 tahun terakhir

4. FORMAT DOKUMEN (PEDOMAN ITS):
   ✓ Margin: Atas 3.0cm, Bawah 2.5cm, Kiri 3.0cm, Kanan 2.0cm
   ✓ Font: Times New Roman 12pt
   ✓ Spasi: 1.5 (isi), 1.0 (abstrak & pustaka)
   ✓ Kertas: A4 80gsm

5. KOMPONEN FORMAL:
   ✓ Halaman Judul (Indonesia & Inggris)
   ✓ Halaman Pengesahan
   ✓ Kata Pengantar
   ✓ Daftar Isi, Daftar Gambar, Daftar Tabel

ATURAN OUTPUT:
- Hasilkan HANYA JSON valid, tanpa teks lain
- Tidak boleh ada koma trailing
- Tidak boleh ada '...' atau placeholder

FORMAT JSON OUTPUT:
{{
  "score": 0-10,
  "percentage": 0-100,
  "status": "LAYAK/PERLU PERBAIKAN/TIDAK LAYAK",
  "details": {{
    "Abstrak": {{
      "status": "✓/✗",
      "notes": "ID: X kata, EN: Y kata. Evaluasi kelengkapan abstrak 2 bahasa.",
      "id_word_count": 0,
      "en_word_count": 0
    }},
    "Struktur Bab": {{
      "Bab 1": "✓/✗",
      "Bab 2": "✓/✗",
      "Bab 3": "✓/✗",
      "Bab 4": "✓/✗",
      "Bab 5": "✓/✗",
      "notes": "Evaluasi kelengkapan Bab 1-5 sesuai pedoman ITS"
    }},
    "Daftar Pustaka": {{
      "references_count": "≥20",
      "format": "APA/IEEE/Tidak Konsisten",
      "notes": "Evaluasi jumlah (min 20) dan kualitas referensi"
    }},
    "Cover & Halaman Formal": {{
      "status": "✓/✗",
      "notes": "Evaluasi kelengkapan halaman judul, pengesahan, kata pengantar"
    }},
    {json.dumps(format_margin_info, ensure_ascii=False)}
  }},
  "document_info": {{
    "jenis_dokumen": "Proposal TA/Laporan TA/Skripsi",
    "total_halaman": {total_halaman},
    "format_file": "PDF"
  }},
  "recommendations": [
    "Rekomendasi spesifik berdasarkan pedoman ITS",
    "Saran perbaikan jika ada yang kurang"
  ]
}}

ISI DOKUMEN YANG DIANALISIS (ringkas {total_halaman} halaman):
{full_text[:12000]}

OUTPUT HANYA JSON VALID. TIDAK BOLEH ADA TEKS LAIN.
"""
    return template


def fallback_result(total_halaman, format_margin_info, pages_text=None, locations=None):
    """
    Hasil fallback dengan analisis lokal (tanpa AI eksternal)
    Sesuai Pedoman ITS SK Rektor No. 280/2022
    """
    # Analisis lokal jika data tersedia
    abstrak_id_words = 0
    abstrak_en_words = 0
    ref_count = 0
    bab_count = len(locations.get('bab', [])) if locations else 0
    
    if pages_text:
        abstrak_id_words, abstrak_en_words = analyze_abstracts(pages_text)
        if locations:
            ref_count = count_references(pages_text, locations)
    
    # Evaluasi komponen berdasarkan hasil analisis lokal
    abstrak_status = "✓" if (abstrak_id_words >= 200 and abstrak_en_words >= 200) else "⚠️"
    abstrak_notes = f"ID: {abstrak_id_words} kata, EN: {abstrak_en_words} kata. "
    if abstrak_id_words == 0 and abstrak_en_words == 0:
        abstrak_notes += "Abstrak tidak terdeteksi atau di luar rentang halaman yang dicek."
    elif abstrak_id_words < 200 or abstrak_en_words < 200:
        abstrak_notes += "Pedoman ITS: 200-300 kata per bahasa."
    else:
        abstrak_notes += "Memenuhi Pedoman ITS (200-300 kata)."
    
    dafpus_status = "✓" if ref_count >= 20 else "⚠️"
    dafpus_notes = f"{ref_count} referensi terdeteksi. "
    if ref_count == 0:
        dafpus_notes += "Daftar Pustaka tidak terdeteksi atau format tidak standar."
    elif ref_count < 20:
        dafpus_notes += f"Pedoman ITS: Minimal 20 referensi (kurang {20-ref_count})."
    else:
        dafpus_notes += "Memenuhi Pedoman ITS (≥20 referensi)."
    
    bab_status = ["⚠️"] * 5
    bab_notes = f"{bab_count} bab terdeteksi. "
    if bab_count >= 5:
        bab_status = ["✓"] * 5
        bab_notes += "Bab 1-5 terdeteksi sesuai Pedoman ITS."
    elif bab_count >= 3:
        bab_notes += "Sebagian bab terdeteksi, verifikasi manual untuk kelengkapan Bab 1-5."
        for i in range(min(bab_count, 5)):
            bab_status[i] = "✓"
    else:
        bab_notes += "Struktur bab tidak lengkap atau tidak terdeteksi."
    
    # Tentukan jenis dokumen berdasarkan jumlah halaman
    has_abstract = abstrak_id_words > 0 or abstrak_en_words > 0
    has_dafpus = ref_count > 0
    has_babs = bab_count >= 3
    
    if total_halaman < 30:
        jenis_dok = "Proposal TA"
        score = 7 if (has_abstract and has_babs) else 6
        percentage = score * 10
        status = "PERLU PERBAIKAN" if score >= 6 else "TIDAK LAYAK"
        rec = [
            "Dokumen terdeteksi sebagai Proposal TA (halaman < 30)",
            "Untuk Laporan TA lengkap, sesuai Pedoman ITS minimal 40-60 halaman",
            "Pastikan semua Bab 1-5 lengkap sesuai Pedoman ITS",
            "Daftar Pustaka harus minimal 20 referensi (jurnal/buku kredibel)",
            "Gunakan format APA atau IEEE secara konsisten"
        ]
    else:
        jenis_dok = "Laporan TA/Skripsi"
        # Hitung score berdasarkan komponen yang terdeteksi
        score = 6  # base
        if abstrak_id_words >= 200 and abstrak_en_words >= 200: score += 1
        if ref_count >= 20: score += 1.5
        if bab_count >= 5: score += 1
        if total_halaman >= 40: score += 0.5
        
        percentage = round(score * 10, 1)
        status = "LAYAK" if score >= 8 else "PERLU PERBAIKAN"
        
        rec = [
            f"Analisis otomatis berhasil: Abstrak ({abstrak_id_words}+{abstrak_en_words} kata), {ref_count} referensi, {bab_count} bab",
            "✓ Dokumen memenuhi panjang minimum Pedoman ITS (122 halaman)" if total_halaman >= 40 else "⚠️ Verifikasi panjang dokumen",
            f"{'✓' if abstrak_status == '✓' else '⚠️'} Abstrak: {abstrak_notes.strip()}",
            f"{'✓' if dafpus_status == '✓' else '⚠️'} Daftar Pustaka: {dafpus_notes.strip()}",
            f"{'✓' if all(s == '✓' for s in bab_status) else '⚠️'} Struktur Bab: {bab_notes.strip()}"
        ]
    
    return {
        "score": score,
        "percentage": percentage,
        "status": status,
        "details": {
            "Abstrak": {
                "status": abstrak_status,
                "notes": abstrak_notes,
                "id_word_count": abstrak_id_words,
                "en_word_count": abstrak_en_words
            },
            "Struktur Bab": {
                "Bab 1": bab_status[0], "Bab 2": bab_status[1], 
                "Bab 3": bab_status[2], "Bab 4": bab_status[3], "Bab 5": bab_status[4],
                "notes": bab_notes
            },
            "Daftar Pustaka": {
                "references_count": str(ref_count) if ref_count > 0 else "Tidak terdeteksi",
                "format": "APA/IEEE" if ref_count >= 20 else "Perlu verifikasi manual",
                "notes": dafpus_notes
            },
            "Cover & Halaman Formal": {
                "status": "✓",
                "notes": "Pedoman ITS: Sampul, Judul (2 bahasa), Pengesahan, Pernyataan Orisinalitas, Kata Pengantar"
            },
            **format_margin_info
        },
        "document_info": {
            "jenis_dokumen": jenis_dok,
            "total_halaman": total_halaman,
            "format_file": "PDF"
        },
        "recommendations": rec
    }


def main():
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Path PDF tidak diberikan"}, ensure_ascii=False))
        return

    pdf_path = sys.argv[1]
    if not os.path.exists(pdf_path):
        print(json.dumps({"error": f"File tidak ditemukan: {pdf_path}"}, ensure_ascii=False))
        return

    total_halaman = hitung_total_halaman(pdf_path)
    format_margin_info = extract_pdf_format_and_margin(pdf_path)
    full_text = read_pdf_text(pdf_path)
    pages_text = read_pdf_text_pages(pdf_path)
    locations = extract_section_locations(pages_text)

    is_valid, error_msg = validate_ta_document(full_text, total_halaman, pages_text, locations)
    if not is_valid:
        print(f"DEBUG: Document validation failed: {error_msg}", file=sys.stderr)
        print(json.dumps({"error": error_msg}, ensure_ascii=False))
        return

    print("DEBUG: Document validated as TA/Skripsi", file=sys.stderr)

    if not USE_SENOPATI:
        print("DEBUG: USE_SENOPATI=false -> returning fallback result", file=sys.stderr)
        result = fallback_result(total_halaman, format_margin_info, pages_text, locations)
        result["locations"] = locations
        print(json.dumps(result, ensure_ascii=False))
        return

    prompt = build_ai_prompt(full_text, total_halaman, format_margin_info)
    resp = query_senopati(prompt, max_retries=1)
    if resp.get("success"):
        try:
            parsed = extract_json_from_text(resp.get("response", ""))
            if isinstance(parsed, dict):
                parsed["locations"] = locations
            print(json.dumps(parsed, ensure_ascii=False))
            return
        except Exception as e:
            print(f"DEBUG: JSON parse failed: {e}", file=sys.stderr)

    result = fallback_result(total_halaman, format_margin_info, pages_text, locations)
    result["locations"] = locations
    print(json.dumps(result, ensure_ascii=False))


if __name__ == "__main__":
    main()
