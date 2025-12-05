import sys
sys.path.insert(0, '.')
from python.analyze_pdf_openrouter import extract_section_locations
from PyPDF2 import PdfReader
import re

pdf_path = 'Referensi/1764762898_5025211103-Muhammad_Naufal_Baihaqi-BukuTA.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

locations = extract_section_locations(pages_text)
dafpus_page = locations.get('daftar_pustaka', {}).get('page', 'Not found')

print(f'Daftar Pustaka starts at page: {dafpus_page}')
print()

# Get daftar pustaka pages
start_page = dafpus_page - 1
end_page = min(start_page + 30, len(pages_text))
ref_pages = pages_text[start_page:end_page]
combined_text = '\n'.join(ref_pages)

# Extract lines dengan cara paling sederhana:
# 1. Split by newline
# 2. Filter yang punya tahun (format apapun)
# 3. Deduplicate
# 4. Remove non-reference lines

year_pattern = r'(?:19|20)\d{2}'
lines = combined_text.split('\n')

candidates = []
for line in lines:
    line = line.strip()
    
    # Basic filters
    if len(line) < 15:
        continue
    if line.upper() in ['DAFTAR PUSTAKA', 'REFERENCES', 'BIBLIOGRAPHY', 'KEPUSTAKAAN', 'DAFTAR REFERENSI']:
        continue
    if re.match(r'^(Lampiran|Appendix|Biodata|BIODATA|TABLE|GAMBAR|FIGURE)', line, re.IGNORECASE):
        break
    if re.match(r'^\d+$', line):  # Page number
        continue
    
    # Must have year to be a reference
    if re.search(year_pattern, line):
        candidates.append(line)

# Now deduplicate - jika 80 char pertama sama, dianggap reference yang sama
seen = set()
unique_refs = []
for line in candidates:
    key = line[:80]
    if key not in seen:
        unique_refs.append(line)
        seen.add(key)

print('=' * 100)
print(f'TOTAL UNIQUE REFERENCES: {len(unique_refs)}')
print('=' * 100)
print()

for i, ref in enumerate(unique_refs, 1):
    # Show full reference if short, or truncate if long
    display = ref if len(ref) <= 120 else ref[:117] + '...'
    print(f'{i:2d}. {display}')

print()
print('=' * 100)
print(f'SUMMARY: {len(unique_refs)} references found')
print('=' * 100)
