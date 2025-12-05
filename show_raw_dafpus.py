import sys
sys.path.insert(0, '.')
from PyPDF2 import PdfReader

pdf_path = 'Referensi/5025211183-Nayya Kamila Putri Yulianto_Buku TA.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

# Pages 119-121 (daftar pustaka)
for page_idx in range(118, 121):
    if page_idx < len(pages_text):
        print(f'\n{"="*80}')
        print(f'PAGE {page_idx + 1}')
        print(f'{"="*80}')
        print(pages_text[page_idx][:1200])
