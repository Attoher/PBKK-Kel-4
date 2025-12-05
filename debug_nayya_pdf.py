import sys
sys.path.insert(0, '.')
from python.analyze_pdf_openrouter import extract_section_locations, read_pdf_text_pages
from PyPDF2 import PdfReader
import re

pdf_path = 'Referensi/5025211183-Nayya Kamila Putri Yulianto_Buku TA.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

print(f'Total pages: {len(pages_text)}')
print()

# Show last 5 pages
for i in range(max(0, len(pages_text)-5), len(pages_text)):
    print(f'\n{"="*80}')
    print(f'PAGE {i+1}')
    print(f'{"="*80}')
    text = pages_text[i]
    print(text[:800])
    print('...')
