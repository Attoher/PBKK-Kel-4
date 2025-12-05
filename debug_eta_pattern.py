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

# Try different patterns
print('Pattern 1: Author, Initial. (YYYY)')
p1 = re.findall(r'[A-Z][a-z\s\'-]*,\s+[A-Z]\..*?\(\d{4}\)', combined_text)
print(f'  Found: {len(p1)} matches')
print()

print('Pattern 2: Author, Initial. (YYYY) - without extra chars')
p2 = re.findall(r'[A-Z][a-z\s\'-]+,\s+[A-Z]\.\s*\([0-9]{4}\)', combined_text)
print(f'  Found: {len(p2)} matches')
for i, m in enumerate(p2[:5], 1):
    print(f'    {i}. {m[:80]}')
print()

print('Pattern 3: Just split by "Author, "')
authors = re.findall(r'[A-Z][a-z\s\'-]+,\s+[A-Z]\.', combined_text)
print(f'  Found: {len(authors)} author patterns')
print(f'  Unique: {len(set(authors))}')
