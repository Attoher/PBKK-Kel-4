import sys
sys.path.insert(0, '.')
from PyPDF2 import PdfReader
import re

pdf_path = 'Referensi/5025211183-Nayya Kamila Putri Yulianto_Buku TA.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

# Check page 119 (index 118)
page_119 = pages_text[118]

print('Page 119 raw content (first 500 chars):')
print(repr(page_119[:500]))
print()

# Test patterns
patterns = [
    r"(?:^|\n)\s*(DAFTAR\s+PUSTAKA|Daftar\s+Pustaka)\s*(?:\n|$)",
    r"(?:^|\n)\s*(REFERENCES|References)\s*(?:\n|$)",
]

for i, pattern in enumerate(patterns):
    matches = re.findall(pattern, page_119, re.MULTILINE)
    print(f'Pattern {i}: {len(matches)} matches')
    if matches:
        print(f'  Found: {matches}')

# Try simpler pattern
simple = re.search(r'DAFTAR\s+PUSTAKA', page_119, re.IGNORECASE)
print(f'\nSimple pattern match (DAFTAR PUSTAKA): {simple is not None}')
if simple:
    print(f'  Position: {simple.start()}')
    print(f'  Context: {page_119[max(0, simple.start()-20):simple.end()+50]}')
