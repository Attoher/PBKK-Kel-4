"""Test script untuk menghitung daftar pustaka"""
import sys
import PyPDF2
import re

def read_pdf_content(file_path):
    """Baca konten PDF dan ekstrak teks"""
    try:
        with open(file_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            full_text = ''
            for i, page in enumerate(pdf_reader.pages):
                page_text = page.extract_text() or ""
                full_text += f"\n--- HALAMAN {i + 1} ---\n{page_text}"
            return full_text
    except Exception as e:
        return f"Error: {e}"

def count_references(full_text):
    """Hitung jumlah referensi di Daftar Pustaka"""
    try:
        text_lower = full_text.lower()
        
        # Cari bagian Daftar Pustaka - harus di HALAMAN sendiri (bukan di daftar isi)
        # Pattern: "--- HALAMAN XX ---" diikuti "DAFTAR PUSTAKA"
        import re
        pages = full_text.split('--- HALAMAN')
        
        ref_section = ""
        ref_page_num = -1
        
        for i, page in enumerate(pages):
            page_lower = page.lower()
            # Cari halaman yang punya "daftar pustaka" di awal (bukan di tengah daftar isi)
            lines = page_lower.split('\n')
            for idx, line in enumerate(lines[:20]):  # Cek 20 baris pertama halaman
                if any(kw in line for kw in ['daftar pustaka', 'references', 'bibliography']):
                    # Pastikan bukan daftar isi (cek apakah ada titik-titik ".....")
                    if '.....' not in page[:500]:  # Jika tidak ada titik2 (bukan daftar isi)
                        ref_section = page
                        ref_page_num = i
                        break
            if ref_section:
                break
        
        if not ref_section:
            return 0, "Bagian Daftar Pustaka tidak ditemukan"
        
        print(f"\n✓ Daftar Pustaka ditemukan di halaman index {ref_page_num}")
        
        # Hitung referensi dengan pattern:
        # Pattern 1: [1], [2], [3]
        bracket_refs = len(re.findall(r'\[\d+\]', ref_section))
        print(f"  - Bracket style [1], [2]: {bracket_refs} referensi")
        
        # Pattern 2: 1., 2., 3. di awal baris
        numbered_refs = len(re.findall(r'^\d+\.', ref_section, re.MULTILINE))
        print(f"  - Numbered style 1., 2.: {numbered_refs} referensi")
        
        # Pattern 3: Tahun (2015), (2020), dll
        year_refs = len(re.findall(r'\(\d{4}\)', ref_section))
        print(f"  - Year pattern (2020): {year_refs} kemunculan")
        
        # Pattern 4: Author, A. (year) - style APA
        author_year = len(re.findall(r'[A-Z][a-z]+,\s+[A-Z]\.\s*\(\d{4}\)', ref_section))
        print(f"  - Author, A. (year): {author_year} kemunculan")
        
        # Pattern 5: Hitung baris yang mengandung URL atau DOI 
        doi_count = len(re.findall(r'https?://|doi\.org', ref_section))
        print(f"  - URL/DOI: {doi_count} kemunculan")
        
        # Pattern 6: Nama dengan koma (Author, X) di awal baris
        author_comma = len(re.findall(r'^[A-Z][a-z]+,\s+[A-Z]', ref_section, re.MULTILINE))
        print(f"  - Author nama di awal baris: {author_comma} kemunculan")
        
        # Gunakan yang terbanyak dan paling masuk akal
        ref_count = max(bracket_refs, numbered_refs, year_refs // 2, author_year, doi_count, author_comma)
        
        # Preview section untuk debugging
        print("\n📄 Preview Daftar Pustaka (800 karakter pertama):")
        print(ref_section[:800])
        print("\n---END PREVIEW---\n")
        
        if ref_count == 0:
            return 0, "Referensi tidak terdeteksi (format mungkin tidak standar)"
        
        # Deteksi format (APA vs IEEE)
        if bracket_refs > numbered_refs:
            format_style = "IEEE"
        else:
            format_style = "APA"
        
        print(f"\n✅ Total referensi: {ref_count} (Format: {format_style})")
        return ref_count, format_style
        
    except Exception as e:
        return 0, f"Error: {e}"

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python test_count_refs.py <path_to_pdf>")
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    print(f"📖 Membaca PDF: {pdf_path}")
    
    full_text = read_pdf_content(pdf_path)
    if full_text.startswith("Error:"):
        print(f"❌ {full_text}")
        sys.exit(1)
    
    print(f"✓ PDF berhasil dibaca, total karakter: {len(full_text):,}")
    
    ref_count, ref_format = count_references(full_text)
    
    if ref_count == 0:
        print(f"\n❌ {ref_format}")
    else:
        print(f"\n🎯 Hasil: {ref_count} referensi (Format {ref_format})")
