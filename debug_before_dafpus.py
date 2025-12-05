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

print(f'Daftar Pustaka detected at page: {dafpus_page}')
print()

# Check pages right before daftar pustaka for any references
start_page = max(0, dafpus_page - 5)  # Check 5 pages before
for page_num in range(start_page, dafpus_page):
    text = pages_text[page_num]
    year_pattern = r'\((?:19|20)\d{2}\)'
    matches = re.findall(year_pattern, text)
    if matches:
        print(f'Page {page_num + 1}: Found {len(matches)} year patterns')

print()
print('Page 109 (1 before dafpus) content preview:')
print(pages_text[108][:800])
