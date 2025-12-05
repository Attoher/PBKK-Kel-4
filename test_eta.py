import sys
sys.path.insert(0, '.')
from python.analyze_pdf_openrouter import extract_section_locations
from PyPDF2 import PdfReader
import re

pdf_path = 'Referensi/ETA_Draft Final Print.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

locations = extract_section_locations(pages_text)
dafpus_page = locations.get('daftar_pustaka', {}).get('page', 'NOT FOUND')

print(f'Daftar Pustaka page: {dafpus_page}')
print(f'Total pages: {len(pages_text)}')
print()

# Get daftar pustaka pages
start_page = dafpus_page - 1
end_page = min(start_page + 30, len(pages_text))
ref_pages = pages_text[start_page:end_page]

combined_text = '\n'.join(ref_pages)

# Count year patterns
year_pattern = r'\((?:19|20)\d{2}\)'
year_matches = re.findall(year_pattern, combined_text)
print(f'Year patterns found: {len(year_matches)}')
print(f'Unique years: {len(set(year_matches))}')
print()

# Show structure
print('First 1500 chars of daftar pustaka:')
print(combined_text[:1500])
