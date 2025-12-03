#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Script untuk menganalisa pola Abstrak dan Daftar Pustaka dari berbagai TA
"""
import sys
import os
import re
from PyPDF2 import PdfReader
import io

# Fix encoding for Windows console
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8', errors='replace')

def extract_pages_text(pdf_path, start_page, end_page):
    """Ekstrak teks dari range halaman"""
    try:
        reader = PdfReader(pdf_path)
        total_pages = len(reader.pages)
        texts = []
        
        for i in range(start_page - 1, min(end_page, total_pages)):
            page = reader.pages[i]
            text = page.extract_text() or ""
            texts.append({
                'page_num': i + 1,
                'text': text,
                'length': len(text)
            })
        
        return texts, total_pages
    except Exception as e:
        print(f"Error reading {pdf_path}: {e}")
        return [], 0

def find_abstract_patterns(pages_text):
    """Cari pola heading abstrak"""
    patterns = []
    
    # Heading patterns to search
    heading_patterns = [
        r'\bABSTRAK\b',
        r'\bAbstrak\b',
        r'\bABSTRACT\b', 
        r'\bAbstract\b',
        r'\bRINGKASAN\b',
        r'\bRingkasan\b',
        r'\bSUMMARY\b',
        r'\bSummary\b',
        r'\bABSTRAKSI\b',
        r'\bAbstraksi\b'
    ]
    
    for page_data in pages_text:
        page_num = page_data['page_num']
        text = page_data['text']
        lines = text.split('\n')
        
        for i, line in enumerate(lines):
            for pattern in heading_patterns:
                if re.search(pattern, line, re.IGNORECASE):
                    context_before = '\n'.join(lines[max(0, i-2):i])
                    context_after = '\n'.join(lines[i:min(len(lines), i+5)])
                    
                    patterns.append({
                        'page': page_num,
                        'line_num': i,
                        'heading': line.strip(),
                        'pattern': pattern,
                        'context_before': context_before[:200],
                        'context_after': context_after[:500]
                    })
    
    return patterns

def find_bibliography_patterns(pages_text):
    """Cari pola heading daftar pustaka"""
    patterns = []
    
    # Bibliography heading patterns
    heading_patterns = [
        r'\bDAFTAR\s+PUSTAKA\b',
        r'\bDaftar\s+Pustaka\b',
        r'\bREFERENCES\b',
        r'\bReferences\b',
        r'\bBIBLIOGRAPHY\b',
        r'\bBibliography\b',
        r'\bREFERENSI\b',
        r'\bReferensi\b',
        r'\bKEPUSTAKAAN\b',
        r'\bKepustakaan\b'
    ]
    
    for page_data in pages_text:
        page_num = page_data['page_num']
        text = page_data['text']
        lines = text.split('\n')
        
        for i, line in enumerate(lines):
            for pattern in heading_patterns:
                if re.search(pattern, line, re.IGNORECASE):
                    context_after = '\n'.join(lines[i:min(len(lines), i+10)])
                    
                    # Count references in next few pages
                    ref_count = 0
                    sample_refs = []
                    
                    # Look for year patterns (common in references)
                    year_pattern = r'\(?\d{4}\)?'
                    for j in range(i+1, min(len(lines), i+20)):
                        if re.search(year_pattern, lines[j]) and len(lines[j].strip()) > 20:
                            ref_count += 1
                            if len(sample_refs) < 3:
                                sample_refs.append(lines[j].strip()[:100])
                    
                    patterns.append({
                        'page': page_num,
                        'line_num': i,
                        'heading': line.strip(),
                        'pattern': pattern,
                        'context_after': context_after[:500],
                        'estimated_refs': ref_count,
                        'sample_refs': sample_refs
                    })
    
    return patterns

def analyze_ta(pdf_path):
    """Analisa satu TA file"""
    print(f"\n{'='*80}")
    print(f"ANALYZING: {os.path.basename(pdf_path)}")
    print(f"{'='*80}")
    
    # Extract first 20 pages for abstract analysis
    print("\n--- ABSTRACT ANALYSIS (Pages 1-20) ---")
    pages_text_start, total = extract_pages_text(pdf_path, 1, 20)
    print(f"Total pages in document: {total}")
    
    abstract_patterns = find_abstract_patterns(pages_text_start)
    if abstract_patterns:
        print(f"\nFound {len(abstract_patterns)} abstract heading(s):")
        for idx, p in enumerate(abstract_patterns, 1):
            print(f"\n  [{idx}] Page {p['page']}, Line {p['line_num']}")
            print(f"      Heading: {p['heading']}")
            print(f"      Pattern: {p['pattern']}")
            print(f"      Context after:")
            print(f"      {p['context_after'][:300]}...")
    else:
        print("  ⚠️ No abstract heading found!")
    
    # Extract last 30 pages for bibliography analysis
    print("\n--- BIBLIOGRAPHY ANALYSIS (Last 30 pages) ---")
    start_page = max(1, total - 29)
    pages_text_end, _ = extract_pages_text(pdf_path, start_page, total)
    
    bib_patterns = find_bibliography_patterns(pages_text_end)
    if bib_patterns:
        print(f"\nFound {len(bib_patterns)} bibliography heading(s):")
        for idx, p in enumerate(bib_patterns, 1):
            print(f"\n  [{idx}] Page {p['page']}, Line {p['line_num']}")
            print(f"      Heading: {p['heading']}")
            print(f"      Pattern: {p['pattern']}")
            print(f"      Estimated refs on this page: {p['estimated_refs']}")
            if p['sample_refs']:
                print(f"      Sample references:")
                for ref in p['sample_refs']:
                    print(f"        - {ref}")
    else:
        print("  ⚠️ No bibliography heading found!")

def main():
    if len(sys.argv) < 2:
        print("Usage: python analyze_patterns.py <pdf_file1> [pdf_file2] ...")
        print("\nOr analyze all in Referensi folder:")
        print("  python analyze_patterns.py Referensi/*.pdf")
        sys.exit(1)
    
    pdf_files = sys.argv[1:]
    
    print(f"Analyzing {len(pdf_files)} file(s)...\n")
    
    for pdf_path in pdf_files:
        if os.path.exists(pdf_path):
            analyze_ta(pdf_path)
        else:
            print(f"File not found: {pdf_path}")
    
    print(f"\n{'='*80}")
    print(f"Analysis complete for {len(pdf_files)} file(s)")
    print(f"{'='*80}")

if __name__ == "__main__":
    main()
