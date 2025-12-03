import PyPDF2
import re
import os

# --- KONFIGURASI ---
INPUT_PDF = "TA.pdf"
OUTPUT_FOLDER = "Hasil_Split_Revisi"

if not os.path.exists(OUTPUT_FOLDER):
    os.makedirs(OUTPUT_FOLDER)

def clean_text(text):
    if not text:
        return ""
    return " ".join(text.replace("\n", " ").split()).upper()

def line_contains_toc_signature(line):
    if not line:
        return False
    s = line.rstrip()
    if re.search(r"\.{3,}", s):
        return True
    if re.search(r"\b\d{1,3}\s*$", s):
        return True
    return False

def find_line_with_pattern(text, pattern):
    if not text:
        return None
    T = text.upper()
    for line in T.splitlines():
        if re.search(pattern, line):
            return line
    return None

def split_pdf_robust(input_path):
    #print(f"--- Memulai Proses Split: {input_path} ---")
    try:
        reader = PyPDF2.PdfReader(input_path)
    except Exception as e:
        #print(f"Error membaca PDF: {e}")
        return

    total_pages = len(reader.pages)

    # --- MARKER SECTION ---
    roman_bab = r"(?:BAB|ВАВ|8AB|BАB|B\.B)\s*"

    section_markers = [
        ("01_Cover", r"TUGAS AKHIR|FINAL PROJECT"),
        ("02_Lembar_Pengesahan", r"LEMBAR PENGESAHAN|APPROVAL SHEET"),
        ("03_Lembar_Orisinalitas", r"PERNYATAAN ORISINALITAS|STATEMENT OF ORIGINALITY"),
        ("04_Abstrak", r"ABSTRAK|ABSTRACT"),
        ("05_Kata_Pengantar", r"KATA PENGANTAR"),
        ("06_Daftar_Isi_Gbr_Tbl", r"DAFTAR ISI"),

        ("07_Bab_1_Pendahuluan", roman_bab + r"I\b"),
        ("08_Bab_2_Tinjauan_Pustaka", roman_bab + r"II\b"),
        ("09_Bab_3_Metodologi", roman_bab + r"III\b"),
        ("10_Bab_4_Hasil_Pembahasan", roman_bab + r"IV\b"),
        ("11_Bab_5_Kesimpulan", roman_bab + r"V\b"),

        ("12_Daftar_Pustaka", r"DAFTAR PUSTAKA|REFERENCES"),
        ("13_Biodata_Penulis", r"BIODATA PENULIS|BIOGRAPHY")
    ]

    section_starts = {"01_Cover": 0}

    #print("--- Mencari Semua Section ---")

    # detect TOC / BAB1 to skip TOC blocks
    toc_regex = None
    bab1_regex = None
    for name, reg in section_markers:
        if name == "06_Daftar_Isi_Gbr_Tbl":
            toc_regex = reg
        if name == "07_Bab_1_Pendahuluan":
            bab1_regex = reg

    toc_start = None
    bab1_start = None

    for i in range(total_pages):
        page = reader.pages[i]
        raw_text = page.extract_text() or ""

        header_clean = clean_text(raw_text)[:600]
        raw_upper = raw_text.upper()

        if toc_start is None and toc_regex and re.search(toc_regex, header_clean):
            toc_start = i
        if bab1_start is None and bab1_regex and re.search(bab1_regex, header_clean):
            bab1_start = i

        if toc_start is not None and bab1_start is not None:
            if toc_start <= i < bab1_start:
                continue

        for target_name, target_regex in section_markers:
            if target_name in section_starts:
                continue

            if re.search(target_regex, header_clean):
                matched_line = find_line_with_pattern(raw_upper, target_regex)

                if matched_line is not None:
                    if line_contains_toc_signature(matched_line):
                        continue
                    else:
                        #print(f"[KETEMU] {target_name} di halaman {i+1}")
                        section_starts[target_name] = i
                        break
                else:
                    idx_match = re.search(target_regex, header_clean)
                    if idx_match and idx_match.start() <= 120:
                        #print(f"[KETEMU-FALLBACK] {target_name} di halaman {i+1}")
                        section_starts[target_name] = i
                        break

    #print(f"\n--- HASIL PENCARIAN: {len(section_starts)}/{len(section_markers)} ditemukan ---")
    # for section_name, page_num in sorted(section_starts.items(), key=lambda x: x[1]):
    #     print(f"  {section_name:25} → Halaman {page_num + 1}")

    missing = [name for name, _ in section_markers if name not in section_starts]
    # if missing:
    #     #print(f"\n⚠️ Section tidak ditemukan: {missing}")

    # -------------------------------------------------------
    # Tambahan: Section yang tidak ditemukan tetap dibuat PDF kosong
    # -------------------------------------------------------
    for name, _ in section_markers:
        if name not in section_starts:
            section_starts[name] = total_pages  # posisi "paling akhir"
            #print(f"📄 {name} ditandai sebagai halaman akhir (akan dibuat PDF kosong).")

    # Proses split
    #print("\n--- Menyimpan File PDF ---")
    sorted_sections = sorted(section_starts.items(), key=lambda x: x[1])

    for idx, (section_name, start_page) in enumerate(sorted_sections):
        if idx < len(sorted_sections) - 1:
            end_page = sorted_sections[idx + 1][1]
        else:
            end_page = total_pages

        output_filename = os.path.join(OUTPUT_FOLDER, f"{section_name}.pdf")

        writer = PyPDF2.PdfWriter()

        if start_page < end_page:
            for p in range(start_page, end_page):
                writer.add_page(reader.pages[p])
        # else:
            ##print(f"⚠ {section_name} tidak memiliki halaman → membuat PDF kosong.")

        with open(output_filename, "wb") as f:
            writer.write(f)

        #print(f"✅ {section_name:25} → {output_filename} (Hal {start_page+1} - {end_page})")

    #print(f"\n🎉 PROSES SELESAI! File disimpan di folder '{OUTPUT_FOLDER}'")

if __name__ == "__main__":
    split_pdf_robust(INPUT_PDF)
