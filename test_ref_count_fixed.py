import sys
sys.path.insert(0, '.')
from python.analyze_pdf_openrouter import count_references, extract_section_locations
from PyPDF2 import PdfReader

pdf_path = 'Referensi/1764762898_5025211103-Muhammad_Naufal_Baihaqi-BukuTA.pdf'

# Read PDF
reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

# Extract locations
locations = extract_section_locations(pages_text)
dafpus_page = locations.get('daftar_pustaka', {}).get('page', 'Not found')
print(f'Daftar Pustaka found at page: {dafpus_page}')

# Count references
ref_count = count_references(pages_text, locations)
print(f'References counted (NEW LOGIC): {ref_count}')
print(f'Previous count: 102')
print(f'Expected (year patterns): 76')
