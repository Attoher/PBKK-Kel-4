#!/usr/bin/env python3
# analyze_pdf.py - Script untuk menganalisis dokumen PDF format ITS

import sys
import json
import re
import PyPDF2
import os
from pathlib import Path
from datetime import datetime

def analyze_pdf(file_path):
    """Menganalisis file PDF dan mengembalikan hasil analisis format ITS"""
    
    # Dapatkan info file terlebih dahulu
    file_size = os.path.getsize(file_path)
    file_size_kb = f"{file_size / 1024:.1f} KB"
    
    results = {
        "metadata": {
            "title": "Tidak Diketahui",
            "author": "Tidak diketahui", 
            "page_count": 0,
            "file_size": file_size_kb,
            "file_format": "PDF",
            "analysis_date": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        },
        "abstract": {
            "found": False,
            "id_word_count": 0,
            "en_word_count": 0,
            "status": "error",
            "message": "Abstrak tidak ditemukan"
        },
        "format": {
            "font_family": "Times New Roman",
            "line_spacing": "1",
            "status": "warning", 
            "message": "Format teks diasumsikan sesuai (perlu verifikasi manual)"
        },
        "margin": {
            "top": "3.0",
            "bottom": "2.5", 
            "left": "3.0",
            "right": "2.0",
            "status": "warning",
            "message": "Margin diasumsikan sesuai standar ITS (perlu verifikasi manual)"
        },
        "chapters": {
            "bab1": False,
            "bab2": False,
            "bab3": False,
            "bab4": False,
            "bab5": False,
            "status": "error",
            "message": "Struktur bab tidak terdeteksi"
        },
        "references": {
            "count": 0,
            "min_references": 20,
            "apa_compliant": False,
            "status": "error",
            "message": "Daftar pustaka tidak ditemukan"
        },
        "cover": {
            "found": False,
            "status": "error", 
            "message": "Halaman cover tidak terdeteksi"
        },
        "overall_score": 0,
        "document_type": "Tidak Diketahui",
        "recommendations": []
    }
    
    try:
        # Baca file PDF
        with open(file_path, 'rb') as file:
            pdf_reader = PyPDF2.PdfReader(file)
            
            # **PERBAIKAN: Validasi PDF**
            if len(pdf_reader.pages) == 0:
                results['error'] = "PDF tidak memiliki halaman"
                return results
            
            # Update metadata dengan informasi yang lebih akurat
            results['metadata']['page_count'] = len(pdf_reader.pages)
            
            if pdf_reader.metadata:
                metadata = pdf_reader.metadata
                if metadata.get('/Title'):
                    title = str(metadata.get('/Title'))
                    # Bersihkan title
                    title = re.sub(r'^Microsoft Word\s*-\s*', '', title)
                    title = re.sub(r'[\x00-\x1f\x7f-\x9f]', '', title)  # Remove control characters
                    results['metadata']['title'] = title.strip()
                if metadata.get('/Author'):
                    author = str(metadata.get('/Author'))
                    author = re.sub(r'[\x00-\x1f\x7f-\x9f]', '', author)
                    results['metadata']['author'] = author.strip()
            
            # Jika title masih "Tidak Diketahui", coba ekstrak dari halaman pertama
            if results['metadata']['title'] == "Tidak Diketahui":
                try:
                    first_page = pdf_reader.pages[0]
                    first_page_text = first_page.extract_text() or ""
                    # **PERBAIKAN: Pattern yang lebih baik untuk judul**
                    lines = [line.strip() for line in first_page_text.split('\n') if line.strip()]
                    for line in lines:
                        # Filter line yang kemungkinan judul
                        if (len(line) > 10 and len(line) < 200 and 
                            not re.search(r'(?i)(abstract|abstrak|chapter|bab|page|\d+|oleh|nama|nrp|program|stud|department|universitas|institut)', line) and
                            not line.isupper() and  # Bukan semua uppercase
                            not line.islower()):   # Bukan semua lowercase
                            results['metadata']['title'] = line
                            break
                except Exception as e:
                    print(f"Error extracting title: {str(e)}", file=sys.stderr)
            
            # Extract teks dari semua halaman
            full_text = ""
            for page_num, page in enumerate(pdf_reader.pages):
                try:
                    page_text = page.extract_text()
                    if page_text:
                        full_text += page_text + "\n"
                    # Batasi untuk efisiensi (maksimal 50 halaman)
                    if page_num >= 50:
                        break
                except Exception as e:
                    print(f"Error extracting page {page_num}: {str(e)}", file=sys.stderr)
                    continue
            
            # **PERBAIKAN: Normalisasi teks**
            full_text = re.sub(r'\s+', ' ', full_text)  # Normalize whitespace
            
            # Deteksi jenis dokumen (Proposal vs Laporan)
            if re.search(r'(?i)bab\s*4|hasil\s+dan\s+pembahasan|results?\s+and?\s+discussion', full_text):
                results['document_type'] = "Laporan Akhir"
                required_chapters = ['bab1', 'bab2', 'bab3', 'bab4', 'bab5']
            else:
                results['document_type'] = "Proposal"
                required_chapters = ['bab1', 'bab2', 'bab3']
            
            # **PERBAIKAN: Analisis abstrak dengan pattern yang lebih robust**
            abstract_id_patterns = [
                r'(?i)abstrak\s*[:\-]?\s*(.*?)(?=abstract|keywords|kata kunci|pendahuluan|bab\s*1|\n\n\s*[A-Z])',
                r'(?i)abstrak\s*(.*?)(?=\s*abstract)',
                r'(?i)abstrak\s*(.*?)(?=\s*keywords|\s*kata kunci)',
            ]
            
            abstract_id_text = ""
            for pattern in abstract_id_patterns:
                try:
                    match = re.search(pattern, full_text, re.DOTALL | re.IGNORECASE)
                    if match:
                        abstract_id_text = match.group(1).strip()
                        # Bersihkan teks
                        abstract_id_text = re.sub(r'^\W+', '', abstract_id_text)  # Remove leading punctuation
                        abstract_id_text = re.sub(r'\s+', ' ', abstract_id_text)
                        
                        if len(abstract_id_text) > 50:  # **PERBAIKAN: Minimal 50 karakter**
                            word_count = len(abstract_id_text.split())
                            results['abstract']['id_word_count'] = word_count
                            results['abstract']['found'] = True
                            break
                except re.error as e:
                    print(f"Regex error in pattern '{pattern}': {str(e)}", file=sys.stderr)
                    continue
            
            # **PERBAIKAN: Analisis abstract Bahasa Inggris**  
            abstract_en_patterns = [
                r'(?i)abstract\s*[:\-]?\s*(.*?)(?=abstrak|keywords|kata kunci|introduction|chapter\s*1|\n\n\s*[A-Z])',
                r'(?i)abstract\s*(.*?)(?=\s*abstrak)',
                r'(?i)abstract\s*(.*?)(?=\s*keywords|\s*kata kunci)',
            ]
            
            abstract_en_text = ""
            for pattern in abstract_en_patterns:
                try:
                    match = re.search(pattern, full_text, re.DOTALL | re.IGNORECASE)
                    if match:
                        abstract_en_text = match.group(1).strip()
                        abstract_en_text = re.sub(r'^\W+', '', abstract_en_text)
                        abstract_en_text = re.sub(r'\s+', ' ', abstract_en_text)
                        
                        if len(abstract_en_text) > 50:  # **PERBAIKAN: Minimal 50 karakter**
                            word_count = len(abstract_en_text.split())
                            results['abstract']['en_word_count'] = word_count
                            results['abstract']['found'] = True
                            break
                except re.error as e:
                    print(f"Regex error in pattern '{pattern}': {str(e)}", file=sys.stderr)
                    continue
            
            # **PERBAIKAN: Update status abstrak dengan range yang lebih realistis**
            if results['abstract']['found']:
                id_ok = 150 <= results['abstract']['id_word_count'] <= 400  # **PERBAIKAN: Range lebih longgar**
                en_ok = 150 <= results['abstract']['en_word_count'] <= 400
                
                if id_ok and en_ok:
                    results['abstract']['status'] = 'success'
                    results['abstract']['message'] = f"Abstrak ID: {results['abstract']['id_word_count']} kata, EN: {results['abstract']['en_word_count']} kata (sesuai)"
                elif results['abstract']['id_word_count'] > 0 or results['abstract']['en_word_count'] > 0:
                    results['abstract']['status'] = 'warning'
                    message_parts = []
                    if results['abstract']['id_word_count'] > 0:
                        status_id = "✓" if id_ok else "⚠"
                        message_parts.append(f"ID: {results['abstract']['id_word_count']} kata {status_id}")
                    if results['abstract']['en_word_count'] > 0:
                        status_en = "✓" if en_ok else "⚠"  
                        message_parts.append(f"EN: {results['abstract']['en_word_count']} kata {status_en}")
                    results['abstract']['message'] = f"Abstrak {', '.join(message_parts)}"
                else:
                    results['abstract']['status'] = 'error'
                    results['abstract']['message'] = "Abstrak ditemukan tetapi tidak dapat dianalisis"
            
            # **PERBAIKAN: Analisis struktur bab dengan pattern yang lebih akurat**
            chapter_patterns = {
                'bab1': [
                    r'(?i)^\s*bab\s*1\W*\s*pendahuluan',
                    r'(?i)^\s*1\W*\s*pendahuluan',
                    r'(?i)chapter\s*1\W*\s*introduction',
                    r'(?i)^\s*pendahuluan\s*$',
                ],
                'bab2': [
                    r'(?i)^\s*bab\s*2\W*\s*tinjauan',
                    r'(?i)^\s*2\W*\s*tinjauan',
                    r'(?i)chapter\s*2\W*\s*(literature|review)',
                    r'(?i)^\s*tinjauan\s+pustaka\s*$',
                ],
                'bab3': [
                    r'(?i)^\s*bab\s*3\W*\s*metod',
                    r'(?i)^\s*3\W*\s*metod',
                    r'(?i)chapter\s*3\W*\s*method',
                    r'(?i)^\s*metodologi\s+(penelitian|kerja)\s*$',
                ],
                'bab4': [
                    r'(?i)^\s*bab\s*4\W*\s*hasil',
                    r'(?i)^\s*4\W*\s*hasil',
                    r'(?i)chapter\s*4\W*\s*result',
                    r'(?i)^\s*hasil\s+dan\s+pembahasan\s*$',
                ],
                'bab5': [
                    r'(?i)^\s*bab\s*5\W*\s*kesimpulan',
                    r'(?i)^\s*5\W*\s*kesimpulan',
                    r'(?i)chapter\s*5\W*\s*conclusion',
                    r'(?i)^\s*kesimpulan\s+dan\s+saran\s*$',
                ]
            }
            
            # **PERBAIKAN: Cari pattern per line untuk akurasi lebih tinggi**
            lines = full_text.split('\n')
            for chapter, patterns in chapter_patterns.items():
                found = False
                for line in lines:
                    line_clean = line.strip()
                    for pattern in patterns:
                        try:
                            if re.search(pattern, line_clean, re.IGNORECASE):
                                found = True
                                break
                        except re.error:
                            continue
                    if found:
                        break
                results['chapters'][chapter] = found
            
            # Tentukan status struktur bab
            found_chapters = [chap for chap in required_chapters if results['chapters'][chap]]
            if len(found_chapters) == len(required_chapters):
                results['chapters']['status'] = 'success'
                results['chapters']['message'] = f"Struktur {results['document_type']} lengkap"
            elif len(found_chapters) >= len(required_chapters) - 1:
                results['chapters']['status'] = 'warning'
                results['chapters']['message'] = f"Struktur {results['document_type']} hampir lengkap ({len(found_chapters)} dari {len(required_chapters)} bab)"
            else:
                results['chapters']['status'] = 'error'
                results['chapters']['message'] = f"Struktur {results['document_type']} tidak lengkap ({len(found_chapters)} dari {len(required_chapters)} bab)"
            
            # **PERBAIKAN: Analisis daftar pustaka yang lebih akurat**
            ref_patterns = [
                r'(?i)^\s*daftar\s+pustaka\s*$',
                r'(?i)^\s*references\s*$',
                r'(?i)^\s*bibliography\s*$',
                r'(?i)daftar\s+pustaka',
            ]
            
            ref_section_found = False
            ref_section_start = -1
            
            # Cari section referensi
            for i, line in enumerate(lines):
                line_clean = line.strip()
                for pattern in ref_patterns:
                    try:
                        if re.search(pattern, line_clean, re.IGNORECASE):
                            ref_section_found = True
                            ref_section_start = i
                            break
                    except re.error:
                        continue
                if ref_section_found:
                    break
            
            if ref_section_found:
                # **PERBAIKAN: Hitung referensi dari section yang terdeteksi**
                ref_count = 0
                in_ref_section = False
                
                for i, line in enumerate(lines):
                    line_clean = line.strip()
                    
                    # Tandai awal section referensi
                    if i >= ref_section_start and not in_ref_section:
                        in_ref_section = True
                        continue
                    
                    if in_ref_section:
                        # Deteksi akhir section (bab baru atau appendix)
                        if (re.search(r'(?i)^\s*(bab|chapter|append|lampiran)\s*\d+', line_clean) or
                            re.search(r'(?i)^\s*appendices', line_clean) or
                            i > ref_section_start + 100):  # Batas maksimal
                            break
                        
                        # Hitung baris yang mirip referensi
                        if (len(line_clean) > 20 and  # Minimal panjang
                            re.search(r'[A-Za-z]', line_clean) and  # Mengandung huruf
                            (re.search(r'\b(19|20)\d{2}\b', line_clean) or  # Tahun
                             re.search(r'[A-Z][a-z]+,\s*[A-Z]\.', line_clean) or  # Penulis
                             re.search(r'\.\s*(Retrieved|Available|from|http)', line_clean) or  # Link
                             re.search(r'[A-Za-z]+\.\s*[A-Za-z]', line_clean) or  # Journal
                             re.search(r'\(\s*\d{4}\s*\)', line_clean))):  # Tahun dalam kurung
                            ref_count += 1
                
                # **PERBAIKAN: Validasi jumlah referensi**
                ref_count = min(ref_count, 200)  # Batasi maksimal
                ref_count = max(ref_count, 1)    # Minimal 1
                
                results['references']['count'] = ref_count
                results['references']['apa_compliant'] = True  # Asumsi sementara
                
                if ref_count >= 20:
                    results['references']['status'] = 'success'
                    results['references']['message'] = f"{ref_count} referensi ditemukan (mencukupi)"
                elif ref_count >= 10:
                    results['references']['status'] = 'warning' 
                    results['references']['message'] = f"{ref_count} referensi ditemukan (minimal 20)"
                else:
                    results['references']['status'] = 'error'
                    results['references']['message'] = f"Hanya {ref_count} referensi ditemukan (minimal 20)"
            else:
                results['references']['message'] = "Bagian daftar pustaka tidak terdeteksi"
            
            # **PERBAIKAN: Analisis cover yang lebih komprehensif**
            first_page_text = ""
            if len(pdf_reader.pages) > 0:
                try:
                    first_page = pdf_reader.pages[0]
                    first_page_text = first_page.extract_text() or ""
                except Exception as e:
                    print(f"Error reading first page: {str(e)}", file=sys.stderr)
            
            # Deteksi elemen cover
            cover_indicators = [
                r'(?i)proposal\s+(tugas\s+akhir|ta)',
                r'(?i)tugas\s+akhir',
                r'(?i)final\s+(project|assignment)',
                r'(?i)institut\s+teknologi\s+sepuluh\s+nopember',
                r'(?i)\bits\b',
                r'(?i)nrp?\s*:?\s*\d+',
                r'(?i)nim\s*:?\s*\d+',
                r'(?i)program\s+studi',
                r'(?i)departemen',
                r'(?i)fakultas',
            ]
            
            cover_matches = 0
            first_page_lines = [line.strip() for line in first_page_text.split('\n') if line.strip()]
            
            for line in first_page_lines:
                for pattern in cover_indicators:
                    try:
                        if re.search(pattern, line, re.IGNORECASE):
                            cover_matches += 1
                            break  # Hitung sekali per line
                    except re.error:
                        continue
            
            # **PERBAIKAN: Threshold yang lebih realistis**
            if cover_matches >= 3:  # Minimal 3 indikator cover
                results['cover']['found'] = True
                results['cover']['status'] = 'success'
                results['cover']['message'] = f'Halaman cover terdeteksi ({cover_matches} indikator)'
            elif cover_matches >= 2:
                results['cover']['found'] = True
                results['cover']['status'] = 'warning'
                results['cover']['message'] = f'Halaman cover terdeteksi tetapi tidak lengkap ({cover_matches} indikator)'
            elif cover_matches == 1:
                results['cover']['found'] = True
                results['cover']['status'] = 'warning'
                results['cover']['message'] = 'Halaman cover terdeteksi tetapi informasi minimal'
            
            # **PERBAIKAN: Hitung overall score yang lebih seimbang**
            score_components = []
            
            # Abstract score (20%)
            if results['abstract']['status'] == 'success':
                score_components.append(20)
            elif results['abstract']['status'] == 'warning':
                score_components.append(15)
            else:
                score_components.append(5)
            
            # Chapters score (25%)
            if results['chapters']['status'] == 'success':
                score_components.append(25)
            elif results['chapters']['status'] == 'warning':
                score_components.append(18)
            else:
                score_components.append(8)
            
            # References score (20%)
            if results['references']['status'] == 'success':
                score_components.append(20)
            elif results['references']['status'] == 'warning':
                score_components.append(14)
            else:
                score_components.append(6)
            
            # Format score (15%) - diasumsikan warning
            score_components.append(12)
            
            # Cover score (20%)
            if results['cover']['status'] == 'success':
                score_components.append(20)
            elif results['cover']['status'] == 'warning':
                score_components.append(15)
            else:
                score_components.append(5)
            
            total_score = sum(score_components)
            results['overall_score'] = round(total_score / 100 * 10, 1)  # Convert to 0-10 scale
            
            # **PERBAIKAN: Generate recommendations yang lebih spesifik**
            recommendations = []
            
            # Rekomendasi abstrak
            if not results['abstract']['found']:
                recommendations.append("Tambahkan abstrak dalam Bahasa Indonesia dan Inggris")
            elif results['abstract']['status'] == 'warning':
                if results['abstract']['id_word_count'] < 150:
                    recommendations.append(f"Perpanjang abstrak Bahasa Indonesia ({results['abstract']['id_word_count']}/150 kata minimal)")
                elif results['abstract']['id_word_count'] > 400:
                    recommendations.append(f"Persingkat abstrak Bahasa Indonesia ({results['abstract']['id_word_count']}/400 kata maksimal)")
                
                if results['abstract']['en_word_count'] < 150:
                    recommendations.append(f"Perpanjang abstrak Bahasa Inggris ({results['abstract']['en_word_count']}/150 kata minimal)")
                elif results['abstract']['en_word_count'] > 400:
                    recommendations.append(f"Persingkat abstrak Bahasa Inggris ({results['abstract']['en_word_count']}/400 kata maksimal)")
            
            # Rekomendasi bab
            missing_chapters = [chap for chap in required_chapters if not results['chapters'][chap]]
            if missing_chapters:
                chapter_names = {
                    'bab1': 'Bab 1 Pendahuluan',
                    'bab2': 'Bab 2 Tinjauan Pustaka', 
                    'bab3': 'Bab 3 Metodologi',
                    'bab4': 'Bab 4 Hasil dan Pembahasan',
                    'bab5': 'Bab 5 Kesimpulan dan Saran'
                }
                missing_names = [chapter_names.get(chap, chap) for chap in missing_chapters]
                recommendations.append(f"Tambahkan bab yang kurang: {', '.join(missing_names)}")
            
            # Rekomendasi referensi
            if results['references']['status'] != 'success':
                if results['references']['count'] < 20:
                    recommendations.append(f"Tambahkan referensi hingga minimal 20 (saat ini {results['references']['count']})")
                elif not ref_section_found:
                    recommendations.append("Pastikan bagian 'Daftar Pustaka' jelas teridentifikasi")
            
            # Rekomendasi cover
            if not results['cover']['found']:
                recommendations.append("Tambahkan halaman cover sesuai format ITS")
            elif results['cover']['status'] == 'warning':
                recommendations.append("Lengkapi informasi pada halaman cover (judul, nama, NRP, program studi)")
            
            # Rekomendasi standar format ITS
            recommendations.extend([
                "Gunakan font Times New Roman 12pt untuk isi dokumen",
                "Atur margin: Atas 3cm, Bawah 2.5cm, Kiri 3cm, Kanan 2cm",
                "Gunakan spasi 1.15 atau 1.5 untuk meningkatkan keterbacaan",
                "Nomor halaman di pojok kanan bawah",
                "Daftar pustaka mengikuti format APA Edisi ke-7"
            ])
            
            results['recommendations'] = recommendations
            
            return results
            
    except PyPDF2.PdfReadError as e:
        print(f"PDF read error: {str(e)}", file=sys.stderr)
        results['error'] = f"Tidak dapat membaca file PDF: {str(e)}"
        return results
    except Exception as e:
        print(f"Error in analyze_pdf: {str(e)}", file=sys.stderr)
        results['error'] = f"Terjadi kesalahan dalam menganalisis dokumen: {str(e)}"
        return results

def main():
    if len(sys.argv) != 2:
        error_result = {
            "error": "Usage: python analyze_pdf.py <path_to_pdf>",
            "metadata": {
                "title": "Error",
                "author": "System", 
                "page_count": 0,
                "file_size": "0 KB",
                "file_format": "PDF"
            },
            "overall_score": 0,
            "document_type": "Error"
        }
        print(json.dumps(error_result, ensure_ascii=False))
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    
    if not os.path.exists(pdf_path):
        error_result = {
            "error": f"File {pdf_path} tidak ditemukan.",
            "metadata": {
                "title": "File Not Found",
                "author": "System", 
                "page_count": 0,
                "file_size": "0 KB",
                "file_format": "PDF"
            },
            "overall_score": 0,
            "document_type": "Error"
        }
        print(json.dumps(error_result, ensure_ascii=False))
        sys.exit(1)
    
    # **PERBAIKAN: Validasi ekstensi file**
    if not pdf_path.lower().endswith('.pdf'):
        error_result = {
            "error": f"File {pdf_path} bukan file PDF",
            "metadata": {
                "title": "Invalid File Type",
                "author": "System", 
                "page_count": 0,
                "file_size": "0 KB", 
                "file_format": "Unknown"
            },
            "overall_score": 0,
            "document_type": "Error"
        }
        print(json.dumps(error_result, ensure_ascii=False))
        sys.exit(1)
    
    try:
        print("Starting PDF analysis...", file=sys.stderr)
        analysis_results = analyze_pdf(pdf_path)
        print("Analysis completed.", file=sys.stderr)
        
        # **PERBAIKAN: Pastikan output selalu valid JSON**
        if 'error' in analysis_results:
            # Tetap kembalikan struktur lengkap dengan error
            analysis_results.setdefault('metadata', {})
            analysis_results.setdefault('overall_score', 0)
            analysis_results.setdefault('document_type', 'Error')
        
        # Print results as JSON
        print(json.dumps(analysis_results, ensure_ascii=False, indent=2))
        
    except Exception as e:
        error_result = {
            "error": f"Unexpected error: {str(e)}",
            "metadata": {
                "title": "Analysis Error", 
                "author": "System",
                "page_count": 0,
                "file_size": "0 KB",
                "file_format": "PDF"
            },
            "overall_score": 0,
            "document_type": "Error"
        }
        print(json.dumps(error_result, ensure_ascii=False))
        sys.exit(1)

if __name__ == "__main__":
    main()