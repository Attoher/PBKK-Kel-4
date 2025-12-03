from PyPDF2 import PdfReader
import re

# Baca PDF
reader = PdfReader('public/pdfs/1764768634_1764762898_5025211103-Muhammad_Naufal_Baihaqi-BukuTA.pdf')
print(f'Total halaman: {len(reader.pages)}')

# Ekstrak semua teks
text = ''.join([p.extract_text() for p in reader.pages])
print(f'Total karakter: {len(text)}')

# Cek Abstrak Indonesia
abstrak_id = re.search(r'ABSTRAK[\s\S]{0,100}(.{0,2000}?)(?:ABSTRACT|Kata Kunci)', text, re.IGNORECASE)
if abstrak_id:
    content = abstrak_id.group(1).strip()
    words = len([w for w in content.split() if len(w) > 2])
    print(f'\n✓ Abstrak ID ditemukan: ~{words} kata')
    print(f'Preview: {content[:200]}...')
else:
    print('\n✗ Abstrak ID TIDAK ditemukan')

# Cek Abstract English
abstrak_en = re.search(r'ABSTRACT[\s\S]{0,100}(.{0,2000}?)(?:Keywords|BAB|CHAPTER)', text, re.IGNORECASE)
if abstrak_en:
    content = abstrak_en.group(1).strip()
    words = len([w for w in content.split() if len(w) > 2])
    print(f'\n✓ Abstract EN ditemukan: ~{words} kata')
    print(f'Preview: {content[:200]}...')
else:
    print('\n✗ Abstract EN TIDAK ditemukan')

# Cek Daftar Pustaka
dafpus_section = re.search(r'DAFTAR\s+PUSTAKA[\s\S]{100,}', text, re.IGNORECASE)
if dafpus_section:
    # Hitung referensi dengan berbagai format
    refs1 = re.findall(r'\n[A-Z][a-z]+,\s+[A-Z]\.', dafpus_section.group(0)[:5000])  # Format: Nama, I.
    refs2 = re.findall(r'\n\[\d+\]', dafpus_section.group(0)[:5000])  # Format: [1]
    refs3 = re.findall(r'\(\d{4}\)', dafpus_section.group(0)[:5000])  # Format: (2020)
    
    total_refs = max(len(refs1), len(refs2), len(refs3))
    print(f'\n✓ Daftar Pustaka ditemukan: ~{total_refs} referensi')
    print(f'Format deteksi: Nama-Inisial={len(refs1)}, Numbered={len(refs2)}, Tahun={len(refs3)}')
else:
    print('\n✗ Daftar Pustaka TIDAK ditemukan')

# Cek Bab
babs = re.findall(r'BAB\s+([IVX]+|[1-5])\s*[:\n]', text)
print(f'\n✓ Bab terdeteksi: {len(set(babs))} bab unik')
print(f'Daftar: {sorted(set(babs))}')

# Cek struktur Bab 1
bab1_content = re.search(r'BAB\s+[I1][\s\S]{0,50}(PENDAHULUAN|Pendahuluan)', text)
if bab1_content:
    print('\n✓ Bab 1 PENDAHULUAN ditemukan')
    # Cek sub-bab
    latar = re.search(r'1\.1.*?(Latar Belakang|LATAR BELAKANG)', text, re.IGNORECASE)
    rumusan = re.search(r'1\.2.*?(Rumusan Masalah|RUMUSAN MASALAH)', text, re.IGNORECASE)
    tujuan = re.search(r'1\.[34].*?(Tujuan|TUJUAN)', text, re.IGNORECASE)
    print(f'  - Latar Belakang: {"✓" if latar else "✗"}')
    print(f'  - Rumusan Masalah: {"✓" if rumusan else "✗"}')
    print(f'  - Tujuan: {"✓" if tujuan else "✗"}')

# Cek halaman per komponen
print('\n--- LOKASI HALAMAN ---')
for i, page in enumerate(reader.pages[:30], 1):
    page_text = page.extract_text() or ''
    if 'ABSTRAK' in page_text and 'ABSTRACT' not in page_text:
        print(f'Halaman {i}: ABSTRAK (ID)')
    if 'ABSTRACT' in page_text and len(page_text) > 100:
        print(f'Halaman {i}: ABSTRACT (EN)')
    if re.search(r'BAB\s+[I1][\s\S]{0,50}PENDAHULUAN', page_text, re.IGNORECASE):
        print(f'Halaman {i}: BAB 1 PENDAHULUAN')

# Cek lokasi Daftar Pustaka
for i, page in enumerate(reader.pages[100:], 101):
    page_text = page.extract_text() or ''
    if re.search(r'DAFTAR\s+PUSTAKA', page_text, re.IGNORECASE):
        print(f'Halaman {i}: DAFTAR PUSTAKA')
        break
