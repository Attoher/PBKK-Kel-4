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
print(f'Daftar Pustaka found at page: {dafpus_page}')
print()

# Get daftar pustaka pages
start_page = dafpus_page - 1
end_page = min(start_page + 30, len(pages_text))
ref_pages = pages_text[start_page:end_page]

combined_text = '\n'.join(ref_pages)

# Count by different methods
year_pattern = r'\((?:19|20)\d{2}\)'
year_matches = re.findall(year_pattern, combined_text)

print(f'Total year patterns found (raw): {len(year_matches)}')
print(f'Unique years: {len(set(year_matches))}')
print()

# Print first 50 references to see what's being counted
lines = combined_text.split('\n')
ref_lines = []
for line in lines:
    line = line.strip()
    if len(line) > 20 and re.search(year_pattern, line):
        ref_lines.append(line)

print(f'Lines with year pattern: {len(ref_lines)}')
print()
print('First 40 reference lines:')
for i, line in enumerate(ref_lines[:40], 1):
    # Truncate long lines
    display_line = line[:100] + '...' if len(line) > 100 else line
    print(f'{i:2d}. {display_line}')
