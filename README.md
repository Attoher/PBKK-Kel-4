# Deteksi Kelengkapan Format Buku TA Berbasis AI

![Laravel](https://img.shields.io/badge/Laravel-11-red?style=flat-square)
![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue?style=flat-square)
![Python](https://img.shields.io/badge/Python-3.10%2B-yellow?style=flat-square)
![License](https://img.shields.io/badge/License-Academic-lightgrey?style=flat-square)
![Status](https://img.shields.io/badge/Status-Active-success?style=flat-square)

<img width="1919" height="955" alt="image" src="https://github.com/user-attachments/assets/f8809e76-8a88-4aaa-ab45-4ab05b3e167e" />

## Kelompok C04

* 5025231181 — Ath Thahir Muhammad Isa Rahmatullah — UI/UX & Frontend & Backend Developer
* 5025231182 — Abimanyu Danendra A — Frontend & Backend & Database Developer
* 5025231184 — Alden Zhorif Muhammad — Backend & Database Developer
* 5025231056 — Razky Ageng Syahputra — UI/UX & DevOps
* 5025231044 — Rahman Azkarafi Prasetya — Prompt Engineering (AI) & AI Engineer & DevOps
* 5025231033 — Raditya Yusuf Annaafi’ — Prompt Engineering (AI) & DevOps
* 5025231026 — Kasyiful Kurob — Prompt Engineering (AI) & Documentation

## Deskripsi

Aplikasi web untuk mendeteksi kelengkapan format Tugas Akhir (TA) secara otomatis menggunakan AI.
Pengguna mengunggah dokumen PDF/DOC/DOCX, kemudian sistem menganalisis struktur dokumen, tipografi, margin, penomoran, abstrak, dan sitasi berdasarkan standar format ITS.

## Target Pengguna

* Mahasiswa
* Dosen pembimbing
* Staf administrasi kampus

## Fitur Utama

* Upload dokumen PDF/DOC/DOCX
* Chunk upload untuk file besar
* Analisis format berbasis AI
* Laporan hasil analisis dan rekomendasi
* Preview PDF menggunakan PDF.js
* Dark mode
* Riwayat analisis (filter, ekspor CSV, hapus)
* Pendaftaran multi-step

## Teknologi yang Digunakan

* Laravel 11
* Blade Templates + Tailwind CSS
* JavaScript + PDF.js
* MySQL / SQLite
* Python (analisis konten dokumen)
* Git + GitHub

---

# Instalasi dan Menjalankan

## 1. Clone Repository

```bash
git clone https://github.com/username/deteksi-ta.git
cd deteksi-ta
```

## 2. Install Dependencies Laravel

```bash
composer install
npm install
```

## 3. Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

Contoh `.env` (SQLite default):

```env
APP_NAME="TA Format Checker ITS"
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite

SESSION_DRIVER=database
QUEUE_CONNECTION=database

PYTHON_EXECUTABLE=
```

## 4. Migrasi Database

```bash
php artisan migrate
```

## 5. Jalankan Server

```bash
php artisan serve
```

Akses:

```
http://127.0.0.1:8000
```

---

# Konfigurasi Python Analyzer

Aplikasi menjalankan script Python di folder `python/` untuk analisis konten dokumen.

## 1. Install dependencies Python

```bash
pip install -r python/requirements.txt
```

## 2. Atur Python Executable di `.env` (opsional)

Jika Python tidak terbaca otomatis:

```
PYTHON_EXECUTABLE=C:\\Path\\To\\Python\\python.exe
```

## 3. Tes script secara manual

```bash
python python/analyze_pdf.py sample.pdf
```

Middleware `check.python` akan menolak request jika Python tidak terdeteksi.

---

# Struktur Folder Penting

## Folder View (Blade)

* `homepage.blade.php` – Beranda
* `upload.blade.php` – Upload dokumen
* `history.blade.php` – Riwayat analisis
* `result.blade.php` – Hasil analisis
* `login.blade.php` – Login
* `register.blade.php` – Registrasi multi-step

## Stylesheet

* `public/css/loading-its.css`
* `public/css/history-its.css`
* `public/css/pdf-its.css`
* `public/css/formatcheck-its.css`
* `public/css/dark-its.css`

## Backend

* `app/Http/Controllers/UploadController.php`
* `app/Http/Controllers/AnalysisController.php`
* `routes/web.php`
* `python/analyze_pdf.py` (AI analyzer)

---

# Cara Kontribusi

1. Buat branch baru:

```bash
git checkout -b feature-nama-fitur
```

2. Commit perubahan:

```bash
git add .
git commit -m "Deskripsi perubahan"
```

3. Push branch:

```bash
git push origin feature-nama-fitur
```

4. Ajukan Pull Request di GitHub.

---

# Lisensi

Proyek ini dibuat untuk keperluan akademik di Institut Teknologi Sepuluh Nopember (ITS), tahun ajaran 2025/2026.

---
