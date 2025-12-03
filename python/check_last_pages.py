#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Check last few pages of PDFs to see bibliography format"""
import sys
import io
from PyPDF2 import PdfReader

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

def check_pdf(path):
    reader = PdfReader(path)
    total = len(reader.pages)
    print(f"\n{'='*80}")
    print(f"File: {path}")
    print(f"Total pages: {total}")
    print(f"{'='*80}\n")
    
    # Check last 5 pages
    for i in range(max(0, total-5), total):
        print(f"\n--- PAGE {i+1} ---")
        page = reader.pages[i]
        text = page.extract_text() or ""
        # Show first 500 chars
        print(text[:800])
        print("...")

if __name__ == "__main__":
    for f in sys.argv[1:]:
        check_pdf(f)
