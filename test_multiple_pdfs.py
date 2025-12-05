import sys
import json
sys.path.insert(0, '.')
from python.analyze_pdf_openrouter import extract_section_locations, count_references, read_pdf_text_pages
from PyPDF2 import PdfReader

test_files = [
    'Referensi/03111540000109-Undergraduate_Thesis.pdf',
    'Referensi/05111940000060-MUHAMMAD ARIF FAIZIN-Buku TA_Signed.pdf',
    'Referensi/5025211183-Nayya Kamila Putri Yulianto_Buku TA.pdf',
]

for pdf_path in test_files:
    try:
        print(f'\n{"="*80}')
        print(f'FILE: {pdf_path.split("/")[-1]}')
        print(f'{"="*80}')
        
        reader = PdfReader(pdf_path)
        pages_text = [page.extract_text() for page in reader.pages]
        
        locations = extract_section_locations(pages_text)
        dafpus_page = locations.get('daftar_pustaka', {}).get('page', 'NOT FOUND')
        
        print(f'Total pages: {len(pages_text)}')
        print(f'Daftar Pustaka page: {dafpus_page}')
        
        if dafpus_page != 'NOT FOUND':
            ref_count = count_references(pages_text, locations)
            print(f'References counted: {ref_count}')
        else:
            print('❌ Daftar Pustaka NOT detected')
            
    except Exception as e:
        print(f'ERROR: {str(e)[:100]}')
