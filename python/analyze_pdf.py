#!/usr/bin/env python3
# analyze_pdf.py - Script untuk menganalisis dokumen PDF

import sys
import json
import re
import PyPDF2
import os
from pathlib import Path

def analyze_pdf(file_path):
    """Menganalisis file PDF dan mengembalikan hasil analisis"""
    
    results = {
        "metadata": {},
        "abstract": {
            "found": False,
            "word_count": 0,
            "status": "error",
            "message": "Abstrak tidak ditemukan"
        },
        "table_of_contents": {
            "found": False,
            "status": "error",
            "message": "Daftar isi tidak ditemukan"
        },
        "formatting": {
            "margin_issues": [],
            "font_consistency": False,
            "status": "error",
            "message": "Tidak dapat menganalisis format"
        },
        "chapters": {
            "introduction": False,
            "methodology": False,
            "results": False,
            "conclusion": False,
            "status": "error",
            "message": "Tidak dapat menganalisis bab"
        },
        "references": {
            "count": 0,
            "min_references": 20,
            "status": "error",
            "message": "Daftar pustaka tidak ditemukan"
        },
        "overall_score": 0,
        "recommendations": []
    }
    
    try:
        # Baca file PDF
        with open(file_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            
            # Extract metadata
            if pdf_reader.metadata:
                results['metadata'] = {
                    'title': pdf_reader.metadata.get('/Title', 'Tidak diketahui'),
                    'author': pdf_reader.metadata.get('/Author', 'Tidak diketahui'),
                    'creator': pdf_reader.metadata.get('/Creator', 'Tidak diketahui'),
                    'producer': pdf_reader.metadata.get('/Producer', 'Tidak diketahui'),
                    'pages': len(pdf_reader.pages)
                }
            
            # Extract teks dari semua halaman
            full_text = ""
            for page_num, page in enumerate(pdf_reader.pages):
                try:
                    page_text = page.extract_text()
                    if page_text:
                        full_text += page_text + "\n"
                        # Berhenti setelah 20 halaman untuk efisiensi
                        if page_num >= 20:
                            break
                except Exception as e:
                    continue
            
            # Analisis abstrak - cari di 2 halaman pertama
            abstract_text = ""
            abstract_patterns = [
                r'(?i)(abstrak|abstract)(.*?)(?=bab|chapter|pendahuluan|introduction|abstract|$)',
                r'(?i)(abstrak|abstract)(.*?)(?=\n\n|\n\s*\n|$)'
            ]
            
            for pattern in abstract_patterns:
                abstract_match = re.search(pattern, full_text, re.DOTALL)
                if abstract_match:
                    abstract_text = abstract_match.group(2).strip()
                    # Bersihkan teks abstrak
                    abstract_text = re.sub(r'\s+', ' ', abstract_text)
                    abstract_text = re.sub(r'^\s*[-:]\s*', '', abstract_text)
                    
                    if abstract_text and len(abstract_text) > 50:
                        word_count = len(abstract_text.split())
                        results['abstract'] = {
                            "found": True,
                            "text": abstract_text[:500] + "..." if len(abstract_text) > 500 else abstract_text,
                            "word_count": word_count,
                            "status": "success" if 150 <= word_count <= 350 else "warning",
                            "message": f"{word_count} kata ditemukan" + 
                                      (" (sesuai)" if 150 <= word_count <= 350 else " (di luar range ideal 150-350 kata)")
                        }
                        break
            
            # Analisis daftar isi
            toc_patterns = [
                r'(?i)daftar isi',
                r'(?i)table of contents',
                r'(?i)contents'
            ]
            
            toc_found = any(re.search(pattern, full_text) for pattern in toc_patterns)
            results['table_of_contents'] = {
                "found": toc_found,
                "status": "success" if toc_found else "warning",
                "message": "Daftar isi ditemukan" if toc_found else "Daftar isi tidak terdeteksi"
            }
            
            # Analisis bab
            chapter_patterns = {
                'introduction': [
                    r'(?i)bab\s*1[^a-zA-Z0-9]*pendahuluan',
                    r'(?i)chapter\s*1[^a-zA-Z0-9]*introduction',
                    r'(?i)pendahuluan',
                    r'(?i)introduction'
                ],
                'methodology': [
                    r'(?i)bab\s*2[^a-zA-Z0-9]*metod',
                    r'(?i)chapter\s*2[^a-zA-Z0-9]*method',
                    r'(?i)metodologi',
                    r'(?i)methodology'
                ],
                'results': [
                    r'(?i)bab\s*3[^a-zA-Z0-9]*hasil',
                    r'(?i)chapter\s*3[^a-zA-Z0-9]*result',
                    r'(?i)hasil\s*dan\s*pembahasan',
                    r'(?i)results?\s*and?\s*discussion'
                ],
                'conclusion': [
                    r'(?i)bab\s*[45][^a-zA-Z0-9]*kesimpulan',
                    r'(?i)chapter\s*[45][^a-zA-Z0-9]*conclusion',
                    r'(?i)kesimpulan',
                    r'(?i)conclusion'
                ]
            }
            
            chapter_results = {}
            for chapter, patterns in chapter_patterns.items():
                found = any(re.search(pattern, full_text) for pattern in patterns)
                chapter_results[chapter] = found
            
            all_chapters_found = all(chapter_results.values())
            results['chapters'] = {
                **chapter_results,
                "status": "success" if all_chapters_found else "warning",
                "message": "Semua bab penting ditemukan" if all_chapters_found else "Beberapa bab tidak terdeteksi"
            }
            
            # Analisis referensi
            ref_patterns = [
                r'(?i)daftar pustaka',
                r'(?i)references',
                r'(?i)bibliography'
            ]
            
            ref_section_found = any(re.search(pattern, full_text) for pattern in ref_patterns)
            
            if ref_section_found:
                # Hitung perkiraan jumlah referensi
                ref_count_estimate = 0
                
                # Method 1: Hitung baris yang mirip referensi
                lines = full_text.split('\n')
                ref_lines = [line for line in lines if re.search(r'\[\d+\]|\(\d{4}\)|\d{4}[a-z]?\)', line)]
                ref_count_estimate = min(len(ref_lines), 50)  # Batasi maksimal 50
                
                # Method 2: Hitung pola nomor referensi
                ref_pattern_count = len(re.findall(r'\[\d+\]', full_text))
                ref_count_estimate = max(ref_count_estimate, min(ref_pattern_count, 50))
                
                if ref_count_estimate > 0:
                    results['references'] = {
                        "count": ref_count_estimate,
                        "min_references": 20,
                        "status": "success" if ref_count_estimate >= 20 else "warning",
                        "message": f"{ref_count_estimate} referensi diperkirakan" + 
                                  (" (mencukupi)" if ref_count_estimate >= 20 else " (kurang dari 20)")
                    }
            
            # Hitung overall score berdasarkan hasil analisis
            score_components = []
            
            # Abstract score (25%)
            if results['abstract']['status'] == 'success':
                score_components.append(25)
            elif results['abstract']['status'] == 'warning':
                score_components.append(15)
            else:
                score_components.append(5)
            
            # Table of contents score (15%)
            if results['table_of_contents']['status'] == 'success':
                score_components.append(15)
            else:
                score_components.append(5)
            
            # Chapters score (30%)
            if results['chapters']['status'] == 'success':
                score_components.append(30)
            elif results['chapters']['status'] == 'warning':
                score_components.append(20)
            else:
                score_components.append(10)
            
            # References score (20%)
            if results['references']['status'] == 'success':
                score_components.append(20)
            elif results['references']['status'] == 'warning':
                score_components.append(10)
            else:
                score_components.append(5)
            
            # Formatting score (10%)
            score_components.append(10)  # Default, asumsikan OK
            
            total_score = sum(score_components)
            results['overall_score'] = round(total_score / 100 * 10, 1)  # Convert to 0-10 scale
            
            # Generate recommendations
            recommendations = []
            
            if not results['abstract']['found']:
                recommendations.append("Tambahkan bagian abstrak yang jelas")
            elif results['abstract']['status'] == 'warning':
                if results['abstract']['word_count'] < 150:
                    recommendations.append(f"Abstrak terlalu pendek ({results['abstract']['word_count']} kata). Minimal 150 kata")
                else:
                    recommendations.append(f"Abstrak terlalu panjang ({results['abstract']['word_count']} kata). Maksimal 350 kata")
            
            if not results['table_of_contents']['found']:
                recommendations.append("Tambahkan daftar isi")
            
            missing_chapters = [chap for chap, found in chapter_results.items() if not found]
            if missing_chapters:
                recommendations.append(f"Tambahkan bab yang kurang: {', '.join(missing_chapters)}")
            
            if results['references']['status'] != 'success':
                if results['references']['count'] < 20:
                    recommendations.append(f"Tambahkan referensi (minimal 20, saat ini {results['references']['count']})")
                else:
                    recommendations.append("Tambahkan bagian daftar pustaka yang jelas")
            
            if not recommendations:
                recommendations.append("Dokumen sudah memenuhi persyaratan dasar")
            
            results['recommendations'] = recommendations
            
            return results
            
    except Exception as e:
        results['error'] = f"Terjadi kesalahan dalam menganalisis dokumen: {str(e)}"
        return results

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print(json.dumps({"error": "Usage: python analyze_pdf.py <path_to_pdf>"}))
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    
    if not os.path.exists(pdf_path):
        print(json.dumps({"error": f"File {pdf_path} tidak ditemukan."}))
        sys.exit(1)
    
    analysis_results = analyze_pdf(pdf_path)
    print(json.dumps(analysis_results, ensure_ascii=False))
