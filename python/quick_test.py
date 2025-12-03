#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Quick test untuk verify detection improvements"""
import sys
import io
import json
from analyze_pdf_openrouter import read_pdf_text_pages, extract_section_locations, analyze_abstracts, count_references

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

def quick_test(pdf_path):
    """Quick test tanpa API call"""
    try:
        print(f"\n{'='*70}")
        print(f"Testing: {pdf_path.split('/')[-1]}")
        print(f"{'='*70}")
        
        pages_text = read_pdf_text_pages(pdf_path)
        total_pages = len(pages_text)
        locations = extract_section_locations(pages_text)
        
        print(f"Total pages: {total_pages}")
        print(f"\n✓ Abstrak detected: {'YES' if locations.get('abstrak') else 'NO'}")
        if locations.get('abstrak'):
            print(f"  Page: {locations['abstrak']['page']}")
        
        abstrak_id, abstrak_en = analyze_abstracts(pages_text)
        print(f"\n✓ Abstrak word counts:")
        print(f"  ID: {abstrak_id} words")
        print(f"  EN: {abstrak_en} words")
        
        print(f"\n✓ Daftar Pustaka detected: {'YES' if locations.get('daftar_pustaka') else 'NO'}")
        if locations.get('daftar_pustaka'):
            print(f"  Page: {locations['daftar_pustaka']['page']}")
            refs = count_references(pages_text, locations)
            print(f"  References: {refs}")
        
        babs = locations.get('bab', [])
        print(f"\n✓ Bab detected: {len(babs)}")
        if babs:
            print(f"  {', '.join([b['label'] for b in babs[:5]])}")
        
        return True
    except Exception as e:
        print(f"ERROR: {e}")
        return False

if __name__ == "__main__":
    files = [
        "Referensi/1764762898_5025211103-Muhammad_Naufal_Baihaqi-BukuTA.pdf",
        "Referensi/5004211010-Undergraduate_Thesis.pdf",
        "Referensi/5025201051-Buku TA .pdf",
        "Referensi/5025211183-Nayya Kamila Putri Yulianto_Buku TA.pdf",
        "Referensi/ETA_Draft Final Print.pdf"
    ]
    
    success = 0
    for f in files:
        if quick_test(f):
            success += 1
    
    print(f"\n{'='*70}")
    print(f"Summary: {success}/{len(files)} files processed successfully")
    print(f"{'='*70}")
