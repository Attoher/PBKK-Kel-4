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

# Get daftar pustaka pages
start_page = dafpus_page - 1
end_page = min(start_page + 30, len(pages_text))
ref_pages = pages_text[start_page:end_page]
combined_text = '\n'.join(ref_pages)

# Try different year patterns
patterns = [
    (r'\((?:19|20)\d{2}\)', 'Year in parentheses (YYYY)'),
    (r'\b(?:19|20)\d{2}[.,;\)]', 'Year with punctuation'),
    (r'\b(?:19|20)\d{2}\s', 'Year with space'),
]

lines = combined_text.split('\n')
unique_refs = []
seen = set()

for line in lines:
    line = line.strip()
    if len(line) < 20:
        continue
    if line.upper() in ['DAFTAR PUSTAKA', 'REFERENCES', 'BIBLIOGRAPHY', 'KEPUSTAKAAN', 'DAFTAR REFERENSI']:
        continue
    if re.match(r'^(Lampiran|Appendix|Biodata|BIODATA)', line, re.IGNORECASE):
        break
    
    # Check if ANY year pattern matches
    has_any_year = any(re.search(pat[0], line) for pat in patterns)
    
    if has_any_year:
        key = line[:80].strip()
        if key not in seen:
            unique_refs.append(line)
            seen.add(key)

print(f'Unique references with ANY year format: {len(unique_refs)}')
print()

# Show the ones that DON'T have (YYYY) format
year_paren = r'\((?:19|20)\d{2}\)'
different_format = []
for i, ref in enumerate(unique_refs, 1):
    if not re.search(year_paren, ref):
        different_format.append((i, ref))
        print(f'Ref #{i} (different year format):')
        print(f'  {ref[:120]}')
        print()

print(f'\nTotal with different format: {len(different_format)}')
