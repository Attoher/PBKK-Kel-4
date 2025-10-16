import requests
import json
import glob
import os
import sys
import fitz
import time
from datetime import datetime
from requests.exceptions import RequestException
import re
import io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='ignore')

# ===== CONFIG =====
models = [
    "gpt-oss:20b-cloud"
]
url = "http://localhost:11434/api/generate"
timeout_seconds = 3000

# ===== EXPECTED RESULTS (GROUND TRUTH) =====
EXPECTED_RESULTS = {
    "Pendidikan": {
        "relevant_paragraphs": [1, 3, 5, 6],
        "irrelevant_paragraphs": [2, 4],
    },
    "Teknologi": {
        "relevant_paragraphs": [1, 3, 5, 6],
        "irrelevant_paragraphs": [2, 4],
    },
    "Kesehatan": {
        "relevant_paragraphs": [1, 3, 5, 6],
        "irrelevant_paragraphs": [2, 4],
    },
    "Environment": {
        "relevant_paragraphs": [1, 3, 5, 6],
        "irrelevant_paragraphs": [2, 4],
    },
    "Ekonomi": {
        "relevant_paragraphs": [1, 3, 5, 6],
        "irrelevant_paragraphs": [2, 4],
    }
}

# ===== cek dependencies =====
try:
    import docx
    HAS_DOCX = True
except ImportError:
    print("Module 'python-docx' belum terpasang. Install: pip install python-docx")
    HAS_DOCX = False

# ===== kumpulkan file dummy =====
def collect_dummy_files():
    files_txt = glob.glob("dummy_*.txt")
    files_docx = glob.glob("dummy_*.docx") if HAS_DOCX else []
    dummy_files = files_txt + files_docx
    
    if not dummy_files:
        print("Tidak ada file dummy_*.txt atau dummy_*.docx ditemukan!")
        print("Folder contents:", os.listdir("."))
        return None
    return dummy_files

# ===== baca file =====
def read_file_content(file_path):
    """Baca konten dari file .txt atau .docx"""
    try:
        if file_path.lower().endswith(".docx"):
            if not HAS_DOCX:
                return None
            doc = docx.Document(file_path)
            paragraphs = [p.text.strip() for p in doc.paragraphs if p.text.strip()]
            return paragraphs
        else:
            with open(file_path, "r", encoding="utf-8") as f:
                content = f.read().strip()
                paragraphs = [p.strip() for p in content.split('\n\n') if p.strip()]
                return paragraphs
    except Exception as e:
        print(f"Error membaca {file_path}: {e}")
        return None

# ===== generate prompt =====
def create_prompt(paragraphs, topic):
    """Buat prompt untuk analisis keselarasan paragraf"""
    numbered_paragraphs = "\n\n".join([f"PARAGRAF {i+1}: {p}" for i, p in enumerate(paragraphs)])
    
    return f"""ANALISIS KESELARASAN PARAGRAF

Teks tentang: {topic}

{numbered_paragraphs}

TUGAS:
1. Identifikasi TOPIK UTAMA dari teks di atas
2. Untuk setiap paragraf (1-{len(paragraphs)}), tentukan apakah:
   - RELEVAN dengan topik utama
   - TIDAK RELEVAN dengan topik utama (melenceng)
3. Berikan penjelasan singkat untuk setiap penilaian

FORMAT OUTPUT WAJIB:
**Topik Utama:** [sebutkan topik utama di sini]

**Analisis Paragraf:**
- Paragraf 1: [RELEVAN/TIDAK RELEVAN] - [penjelasan singkat]
- Paragraf 2: [RELEVAN/TIDAK RELEVAN] - [penjelasan singkat]
- Paragraf 3: [RELEVAN/TIDAK RELEVAN] - [penjelasan singkat]
- ... [lanjutkan untuk semua paragraf]

Jawab dalam bahasa Indonesia.
"""

# ===== kirim ke model AI =====
def query_model(model, prompt, url, timeout):
    """Query model AI dan return response"""
    payload = {
        "model": model, 
        "prompt": prompt, 
        "stream": False,
        "options": {
            "temperature": 0.1,
            "top_p": 0.9
        }
    }
    
    try:
        start_time = time.time()
        response = requests.post(url, json=payload, timeout=timeout)
        end_time = time.time()
        
        if response.status_code == 200:
            result = response.json()
            return {
                "success": True,
                "response": result.get("response", ""),
                "time_taken": round(end_time - start_time, 2)
            }
        else:
            return {
                "success": False,
                "error": f"HTTP {response.status_code}: {response.text}",
                "time_taken": 0
            }
            
    except RequestException as e:
        return {
            "success": False,
            "error": f"Connection error: {e}",
            "time_taken": 0
        }

# ===== HITUNG AKURASI SEDERHANA =====
def calculate_simple_accuracy(response_text, expected, paragraphs_count):
    """Hitung akurasi sederhana berdasarkan prediksi relevansi"""
    
    predictions = {}
    
    lines = response_text.split('\n')
    for line in lines:
        line_lower = line.lower().strip()
        
        for i in range(1, paragraphs_count + 1):
            if f"paragraf {i}:" in line_lower or f"paragraph {i}:" in line_lower:
                if "tidak relevan" in line_lower or "melenceng" in line_lower:
                    predictions[i] = False
                elif "relevan" in line_lower:
                    predictions[i] = True
    
    correct_predictions = 0
    total_paragraphs = len(expected["relevant_paragraphs"]) + len(expected["irrelevant_paragraphs"])
    
    for i in range(1, paragraphs_count + 1):
        expected_relevant = (i in expected["relevant_paragraphs"])
        
        if i in predictions:
            if predictions[i] == expected_relevant:
                correct_predictions += 1
    
    accuracy = (correct_predictions / total_paragraphs) * 100 if total_paragraphs > 0 else 0
    
    return {
        "accuracy": round(accuracy, 2),
        "correct_predictions": correct_predictions,
        "total_paragraphs": total_paragraphs,
        "predictions": predictions
    }

# ===== MAIN EXECUTION =====
def main():
    print("Starting AI Model Testing (Simple Version)...")
    print("=" * 50)
    
    try:
        test_resp = requests.get("http://localhost:11434/api/tags", timeout=5)
        if test_resp.status_code != 200:
            print("Ollama tidak berjalan di http://localhost:11434")
            return
        print("Terhubung ke Ollama")
    except:
        print("Tidak bisa terkoneksi ke Ollama")
        return
    
    dummy_files = collect_dummy_files()
    if not dummy_files:
        return
    
    print(f"Found {len(dummy_files)} dummy files")
    
    output_dir = "results_simple"
    os.makedirs(output_dir, exist_ok=True)
    
    all_results = []
    
    for file_path in dummy_files:
        topic_name = os.path.basename(file_path).replace("dummy_", "").rsplit(".", 1)[0]
        topic_name = topic_name.capitalize()
        
        if topic_name not in EXPECTED_RESULTS:
            print(f"Skip {topic_name} - tidak ada expected results")
            continue
        
        print(f"\n{'='*50}")
        print(f"PROCESSING: {topic_name}")
        print(f"{'='*50}")
        
        paragraphs = read_file_content(file_path)
        if not paragraphs:
            print(f"Skip {file_path} - konten kosong")
            continue
        
        print(f"Jumlah paragraf: {len(paragraphs)}")
        print(f"Expected Relevan: {EXPECTED_RESULTS[topic_name]['relevant_paragraphs']}")
        print(f"Expected Tidak Relevan: {EXPECTED_RESULTS[topic_name]['irrelevant_paragraphs']}")
        
        prompt = create_prompt(paragraphs, topic_name)
        
        for model in models:
            print(f"\nTesting model: {model}")
            
            result = query_model(model, prompt, url, timeout_seconds)
            
            if result['success']:
                response_text = result['response']
                
                accuracy_result = calculate_simple_accuracy(
                    response_text, 
                    EXPECTED_RESULTS[topic_name],
                    len(paragraphs)
                )
                
                safe_topic = topic_name.replace(" ", "_")
                output_file = os.path.join(output_dir, f"hasil_{safe_topic}_{model.replace(':','_')}.txt")
                
                with open(output_file, "w", encoding="utf-8") as f:
                    f.write(f"TOPIK: {topic_name}\n")
                    f.write(f"MODEL: {model}\n")
                    f.write(f"AKURASI: {accuracy_result['accuracy']}%\n")
                    f.write(f"WAKTU: {result['time_taken']} detik\n")
                    f.write(f"PREDIKSI BENAR: {accuracy_result['correct_predictions']}/{accuracy_result['total_paragraphs']}\n")
                    f.write("\n" + "="*50 + "\n")
                    f.write("PROMPT:\n")
                    f.write(prompt)
                    f.write("\n" + "="*50 + "\n")
                    f.write("RESPONSE MODEL:\n")
                    f.write(response_text)
                    f.write("\n" + "="*50 + "\n")
                    f.write("DETAIL AKURASI:\n")
                    f.write(json.dumps(accuracy_result, indent=2, ensure_ascii=False))
                
                all_results.append({
                    'model': model,
                    'topic': topic_name,
                    'accuracy': accuracy_result['accuracy'],
                    'time_taken': result['time_taken'],
                    'correct': accuracy_result['correct_predictions'],
                    'total': accuracy_result['total_paragraphs']
                })
                
                print(f"Akurasi: {accuracy_result['accuracy']}% ({accuracy_result['correct_predictions']}/{accuracy_result['total_paragraphs']})")
                print(f"Waktu: {result['time_taken']}s")
                print(f"Saved to: {output_file}")
                
            else:
                print(f"Failed: {result['error']}")
    
    if all_results:
        print(f"\n{'='*60}")
        print("HASIL AKHIR TESTING")
        print(f"{'='*60}")
        
        model_stats = {}
        for result in all_results:
            model = result['model']
            if model not in model_stats:
                model_stats[model] = []
            model_stats[model].append(result['accuracy'])
        
        print("\nRANKING MODEL:")
        print("-" * 40)
        
        ranked_models = sorted(
            model_stats.items(),
            key=lambda x: sum(x[1]) / len(x[1]),
            reverse=True
        )
        
        for rank, (model, accuracies) in enumerate(ranked_models, 1):
            avg_acc = sum(accuracies) / len(accuracies)
            print(f"{rank}. {model}: {avg_acc:.2f}% (dari {len(accuracies)} test)")
        
        print(f"\nDETAIL PER MODEL:")
        print("-" * 50)
        
        for model in model_stats:
            accuracies = model_stats[model]
            avg_acc = sum(accuracies) / len(accuracies)
            best_acc = max(accuracies)
            worst_acc = min(accuracies)
            
            print(f"\n{model}:")
            print(f"   Rata-rata: {avg_acc:.2f}%")
            print(f"   Tertinggi: {best_acc:.2f}%")
            print(f"   Terendah: {worst_acc:.2f}%")
            print(f"   Test cases: {len(accuracies)}")
    
    print(f"\nTesting selesai! File detail ada di folder '{output_dir}'")

    print("Starting AI Model Testing (Simple Version)...")
    print("=" * 50)

if __name__ == "__main__":
    if len(sys.argv) > 1:
        file_path = sys.argv[1]

        if not os.path.exists(file_path):
            print(json.dumps({"error": f"File tidak ditemukan: {file_path}"}))
            sys.exit(1)

        topic_name = "Dokumen Akademik"

        try:
            doc = fitz.open(file_path)
            page_count = len(doc)
            all_text = ""
            for page in doc:
                all_text += page.get_text("text") + "\n\n"
            doc.close()

            paragraphs = [p.strip() for p in all_text.split("\n\n") if p.strip()]
        except Exception as e:
            print(json.dumps({"error": f"Gagal membaca PDF: {str(e)}"}))
            sys.exit(1)

        # Minta AI bantu analisis format ITS
        prompt = """
        Kamu adalah sistem penilai dokumen akademik ITS.
        Analisis teks berikut dan nilai formatnya berdasarkan ketentuan standar ITS:

        Output **WAJIB dalam format JSON VALID**:

        {
        "format_score": 0–100,
        "status": "SESUAI STANDAR ITS" atau "PERLU PERBAIKAN",
        "summary": "Proposal - [Judul Dokumen]",
        "message": "Dokumen Anda memenuhi [persentase]% persyaratan format ITS",
        "details": {
            "abstrak": { "status": "...", "catatan": "..." },
            "format_teks": { "status": "...", "catatan": "..." },
            "margin": { "status": "...", "catatan": "..." },
            "struktur_bab": { "status": "...", "catatan": "..." },
            "daftar_pustaka": { "status": "...", "catatan": "..." },
            "cover_formal": { "status": "...", "catatan": "..." }
        }
        }
        """

        
        text_sample = "\n".join(paragraphs[:50])  # kirim sebagian agar cepat
        full_prompt = prompt + "\n\nIsi dokumen:\n" + text_sample

        model = models[0]
        result = query_model(model, full_prompt, url, timeout_seconds)

        if not result["success"]:
            print(json.dumps({"error": result["error"]}))
            sys.exit(1)

        try:
            response_text = result["response"]
            # Ambil hanya JSON-nya (jika AI kirim dalam code block atau ada teks tambahan)
            json_match = re.search(r"\{.*\}", response_text, re.DOTALL)
            if json_match:
                response_parsed = json.loads(json_match.group(0))
            else:
                response_parsed = {"error": "Output AI bukan JSON valid", "raw_output": response_text}
        except Exception as e:
            response_parsed = {"error": f"Gagal parse output AI: {str(e)}"}

        # ===== BUAT JSON FINAL UNTUK FRONTEND =====
        output = {
            "metadata": {
                "title": "Analisis Dokumen PDF",
                "page_count": page_count,
                "file_size": f"{os.path.getsize(file_path) / 1024 / 1024:.1f} MB"
            },
            "response_text": result["response"],
            "parsed_result": response_parsed,
            "overall_score": response_parsed.get("format_score", 0) if isinstance(response_parsed, dict) else 0,
            "document_type": response_parsed.get("summary", "Proposal") if isinstance(response_parsed, dict) else "Proposal",
            "generated_at": datetime.now().isoformat()
        }

        print(json.dumps(output, ensure_ascii=False, indent=2))


        sys.exit(0)
    else:
        main()

