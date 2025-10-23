import PyPDF2
import sys
import json

def analyze_pdf_structure(pdf_path):
    try:
        with open(pdf_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            content = {
                'total_pages': len(pdf_reader.pages),
                'structure': []
            }
            
            # Analisis setiap halaman
            full_text = ''
            for i, page in enumerate(pdf_reader.pages):
                text = page.extract_text() or ""
                full_text += text
                
                # Cek judul-judul penting
                important_sections = ['BAB', 'DAFTAR PUSTAKA', 'ABSTRAK', 'ABSTRACT']
                for section in important_sections:
                    if section in text.upper():
                        content['structure'].append({
                            'page': i + 1,
                            'type': section,
                            'preview': text[:200]  # Preview 200 karakter pertama
                        })
            
            # Analisis referensi
            ref_section = ''
            in_ref = False
            lines = full_text.split('\n')
            for line in lines:
                if 'DAFTAR PUSTAKA' in line.upper():
                    in_ref = True
                    continue
                if in_ref:
                    ref_section += line + '\n'
            
            content['references'] = ref_section
            return content
            
    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "File PDF tidak diberikan"}))
        sys.exit(1)
        
    pdf_path = sys.argv[1]
    result = analyze_pdf_structure(pdf_path)
    print(json.dumps(result, ensure_ascii=False, indent=2))