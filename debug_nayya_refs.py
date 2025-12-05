import sys
sys.path.insert(0, '.')
from python.analyze_pdf_openrouter import extract_section_locations
from PyPDF2 import PdfReader
import re

pdf_path = 'Referensi/5025211183-Nayya Kamila Putri Yulianto_Buku TA.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

locations = extract_section_locations(pages_text)
dafpus_page = locations.get('daftar_pustaka', {}).get('page', 'NOT FOUND')

print(f'Daftar Pustaka page: {dafpus_page}')

# Get references from daftar pustaka page
start_page = dafpus_page - 1
end_page = min(start_page + 30, len(pages_text))
ref_pages = pages_text[start_page:end_page]

combined_text = '\n'.join(ref_pages)

# Extract references manually
year_pattern = r'(?:19|20)\d{2}'
lines = combined_text.split('\n')

refs_with_year = []
for line in lines:
    line = line.strip()
    if len(line) < 25:
        continue
    if line.upper() in ['DAFTAR PUSTAKA', 'REFERENCES', 'BIBLIOGRAPHY', 'KEPUSTAKAAN']:
        continue
    if re.search(year_pattern, line):
        refs_with_year.append(line)

print(f'Lines with year pattern: {len(refs_with_year)}')
print()

# Check for author pattern (required by function)
author_pattern = r'[A-Z][a-z\s\'-]+,\s+[A-Z]\.?'
refs_with_author = [r for r in refs_with_year if re.search(author_pattern, r)]
print(f'References with author pattern: {len(refs_with_author)}')
print()

print('Sample references:')
for i, ref in enumerate(refs_with_author[:10], 1):
    display = ref[:110] if len(ref) > 110 else ref
    print(f'{i}. {display}')
