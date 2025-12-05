import sys
sys.path.insert(0, '.')
from python.analyze_pdf_openrouter import extract_section_locations
from PyPDF2 import PdfReader
import re

pdf_path = 'Referensi/1764762898_5025211103-Muhammad_Naufal_Baihaqi-BukuTA.pdf'

# Read PDF
reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

# Extract locations
locations = extract_section_locations(pages_text)
dafpus_page = locations.get('daftar_pustaka', {}).get('page', 'Not found')
print(f'Daftar Pustaka detected at page: {dafpus_page}')
print(f'Total pages in PDF: {len(pages_text)}')
print()

# Get daftar pustaka pages
start_page = dafpus_page - 1
end_page = min(start_page + 30, len(pages_text))
ref_pages = pages_text[start_page:end_page]

combined_text = '\n'.join(ref_pages)

# Find unique references (deduplicate)
year_pattern = r'\((?:19|20)\d{2}\)'
lines = combined_text.split('\n')
unique_refs = []
seen = set()

for line in lines:
    line = line.strip()
    if len(line) > 20 and re.search(year_pattern, line):
        # Use first 80 chars as unique identifier to catch duplicates
        key = line[:80]
        if key not in seen:
            unique_refs.append(line)
            seen.add(key)

print(f'Total year patterns (with duplicates): {len(re.findall(year_pattern, combined_text))}')
print(f'Unique references (deduplicated): {len(unique_refs)}')
print()
print('All unique references:')
for i, line in enumerate(unique_refs, 1):
    display_line = line[:100] + '...' if len(line) > 100 else line
    print(f'{i:2d}. {display_line}')
