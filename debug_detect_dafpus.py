import sys
sys.path.insert(0, '.')
from python.analyze_pdf_openrouter import extract_section_locations, read_pdf_text_pages
from PyPDF2 import PdfReader
import re

pdf_path = 'Referensi/5025211183-Nayya Kamila Putri Yulianto_Buku TA.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

locations = extract_section_locations(pages_text)
print('Detected locations:')
print(f'  Abstrak: {locations.get("abstrak")}')
print(f'  Bab: {locations.get("bab")}')
print(f'  Daftar Pustaka: {locations.get("daftar_pustaka")}')
print()

# Manually check pages 115-123 for DAFTAR PUSTAKA header
for i in range(115, len(pages_text)):
    text = pages_text[i]
    if 'DAFTAR PUSTAKA' in text or 'daftar pustaka' in text.lower():
        # Find which line
        lines = text.split('\n')
        for j, line in enumerate(lines):
            if 'daftar pustaka' in line.lower():
                print(f'Page {i+1}: Found "DAFTAR PUSTAKA" at line {j}')
                print(f'  Context: {line.strip()[:100]}')
                break
