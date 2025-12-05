import sys
sys.path.insert(0, '.')
from PyPDF2 import PdfReader
import re

pdf_path = 'Referensi/ETA_Draft Final Print.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

# Get daftar pustaka (page 73 = index 72)
start_page = 72
combined_text = '\n'.join(pages_text[start_page:76])

# Clean
combined_text = re.sub(r'^\s*\d+\s+(?=DAFTAR|Daftar|[A-Z][a-z])', '', combined_text, flags=re.MULTILINE)
combined_text = re.split(r'(BIODATA PENULIS|Lampiran|Appendix)', combined_text, flags=re.IGNORECASE)[0]
combined_text = re.sub(r'^.*?DAFTAR\s+PUSTAKA\s*', '', combined_text, flags=re.IGNORECASE | re.MULTILINE)

print('Full daftar pustaka text:')
print('='*80)
print(combined_text)
print('='*80)
print(f'\nTotal length: {len(combined_text)} chars')
