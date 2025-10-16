import sys
import fitz  # PyMuPDF
import json
import requests
import re
from datetime import datetime
import time

AI_URL = "http://localhost:11434/api/generate"
AI_MODEL = "gpt-oss:20b-cloud"
MAX_TOKENS = 2000
POLL_INTERVAL = 1  # detik, waktu tunggu sebelum poll ulang
MAX_POLL = 300     # max 300 detik = 5 menit

def read_pdf(file_path):
    doc = fitz.open(file_path)
    paragraphs = [page.get_text("text") for page in doc]
    layout_info = {
        "page_count": len(doc),
        "file_size": f"{doc.metadata.get('filesize', 0)/1024/1024:.1f} MB"
    }
    doc.close()
    return "\n".join(paragraphs), layout_info

def parse_ai_json(response_text):
    match = re.search(r'\{.*?\}', response_text, re.DOTALL)
    if not match:
        raise ValueError("Tidak ditemukan JSON di output AI")
    json_str = match.group()
    try:
        return json.loads(json_str)
    except json.JSONDecodeError:
        json_str_clean = re.sub(r'[\x00-\x1f]+', '', json_str)
        try:
            return json.loads(json_str_clean)
        except json.JSONDecodeError:
            raise ValueError("AI mengembalikan JSON tidak valid")

def call_ai(text_sample):
        prompt = f"""
    Kamu adalah AI untuk menganalisis dokumen PDF sesuai format ITS.
    ⚠️ Kembalikan **HANYA JSON**, tanpa penjelasan tambahan.
    Tugasmu:
    1. Deteksi BAB 1-5 (status "ada"/"tidak ada").
    2. Deteksi abstrak (ID & EN, "ada"/"tidak ada").
    3. Tidak perlu membaca isi bab, cukup cek judul.
    Output harus JSON:
    {{
    "abstrak": "ada/tidak ada",
    "bab": {{
        "BAB1": "ada/tidak ada",
        "BAB2": "ada/tidak ada",
        "BAB3": "ada/tidak ada",
        "BAB4": "ada/tidak ada",
        "BAB5": "ada/tidak ada"
    }}
    }}
    Berikut teks PDF:
    {text_sample}
    """
        payload = {
            "model": AI_MODEL,
            "prompt": prompt,
            "max_tokens": MAX_TOKENS
        }

        try:
            # langsung ambil response tanpa polling
            response = requests.post(AI_URL, json=payload, timeout=120)
            response.raise_for_status()
            return parse_ai_json(response.text)
        except Exception:
            # fallback aman
            return {
                "abstrak": "tidak ada",
                "bab": {f"BAB{i}": "tidak ada" for i in range(1, 6)}
            }


def main(file_path):
    full_text, layout = read_pdf(file_path)
    sample_text = "\n".join(full_text.split("\n")[:10000])

    try:
        ai_results = call_ai(sample_text)
    except Exception as e:
        ai_results = {
            "abstrak": "tidak ada",
            "bab": {f"BAB{i}": "tidak ada" for i in range(1, 6)}
        }

    results = {
        "metadata": layout,
        "results": ai_results,
        "file_name": file_path,
        "generated_at": datetime.now().isoformat()
    }

    results_file = file_path.replace(".pdf", "_results.json")
    with open(results_file, "w", encoding="utf-8") as f:
        json.dump(results, f, indent=2, ensure_ascii=False)

    print(json.dumps(results, ensure_ascii=False, indent=2))

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Usage: python analyze_pdf.py <file.pdf>"}))
        sys.exit(1)
    main(sys.argv[1])
