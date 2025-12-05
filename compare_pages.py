import sys
sys.path.insert(0, '.')
from PyPDF2 import PdfReader

pdf_path = 'Referensi/5025211183-Nayya Kamila Putri Yulianto_Buku TA.pdf'

reader = PdfReader(pdf_path)
pages_text = [page.extract_text() for page in reader.pages]

print('Page 24:')
print(pages_text[23][:800])
print()
print('Page 119:')
print(pages_text[118][:500])
