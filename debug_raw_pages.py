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

# Show raw pages to see actual structure
print(f'=== PAGES {start_page+1} to {end_page} ===')
print()
for i, page_text in enumerate(ref_pages[:5], start=start_page+1):
    print(f'\n--- PAGE {i} ---')
    print(page_text[:600])
    print('...')
