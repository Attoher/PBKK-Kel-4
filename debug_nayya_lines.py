import sys
sys.path.insert(0, '.')
from PyPDF2 import PdfReader
import re

pdf_path = 'Referensi/5025211183-Nayya Kamila Putri Yulianto_Buku TA.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

# Get daftar pustaka
start_page = 118  # page 119
combined_text = '\n'.join(pages_text[start_page:121])

# Clean
combined_text = re.sub(r'^\s*\d+\s+(?=DAFTAR|Daftar|[A-Z][a-z])', '', combined_text, flags=re.MULTILINE)
combined_text = re.split(r'(BIODATA PENULIS|Lampiran|Appendix)', combined_text, flags=re.IGNORECASE)[0]
combined_text = re.sub(r'^.*?DAFTAR\s+PUSTAKA\s*', '', combined_text, flags=re.IGNORECASE | re.MULTILINE)

print('Cleaned text (first 1000 chars):')
print(repr(combined_text[:1000]))
print()

# Check line-by-line
lines = combined_text.split('\n')
print(f'Total lines: {len(lines)}')
print()

for i, line in enumerate(lines):
    line = line.strip()
    if len(line) > 25:
        has_author = bool(re.search(r'[A-Z][a-z\s\'-]+,\s+[A-Z]\.', line))
        has_year = bool(re.search(r'(?:19|20)\d{2}', line))
        if has_author or has_year:
            print(f'Line {i}: author={has_author}, year={has_year}')
            print(f'  {line[:100]}')
