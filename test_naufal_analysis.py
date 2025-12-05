import sys
import json
sys.path.insert(0, '.')
from python.analyze_pdf_openrouter import analyze_pdf

pdf_path = 'Referensi/1764762898_5025211103-Muhammad_Naufal_Baihaqi-BukuTA.pdf'

result = analyze_pdf(pdf_path)
print(json.dumps(result, indent=2, ensure_ascii=False))
