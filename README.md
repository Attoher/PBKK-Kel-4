# Deteksi Kelengkapan Format Buku TA Berbasis AI

<img width="1919" height="990" alt="image" src="https://github.com/user-attachments/assets/b83a226c-f77e-4307-aedf-1dd9171abb81" />

## Kelompok C04
- 5025231026 — Kasyiful Kurob  
- 5025231033 — Raditya Yusuf Annaafi’  
- 5025231044 — Rahman Azkarafi Prasetya  
- 5025231056 — Razky Ageng Syahputra  
- 5025231181 — Ath Thahir Muhammad Isa Rahmatullah  
- 5025231182 — Abimanyu Danendra A  
- 5025231184 — Alden Zhorif Muhammad  

## Deskripsi
Aplikasi untuk mendeteksi kelengkapan format buku Tugas Akhir (TA) berbasis AI.  
Mahasiswa dapat mengunggah file PDF buku TA, lalu sistem akan memeriksa apakah format sudah sesuai dengan ketentuan kampus.  

## Target Pengguna
- Mahasiswa  
- Dosen Pembimbing  
- Administrasi Kampus  

## Fitur Utama
- Upload file buku TA (PDF)  
- Analisis kelengkapan format menggunakan AI  
- Hasil deteksi ditampilkan dalam bentuk laporan sederhana  

## Teknologi
- Framework: Laravel 11 (PHP)  
- Frontend: Blade Template  
- Database: MySQL / SQLite (opsional)  
- Version Control: Git + GitHub  

## Cara Instalasi dan Menjalankan
1. Clone repo ini:
   ```bash
   git clone https://github.com/username/deteksi-ta.git
   cd deteksi-ta

2. Install dependency Laravel:

   ```bash
   composer install
   ```

3. Copy file environment:

   ```bash
   cp .env.example .env
   ```

4. Generate key aplikasi:

   ```bash
   php artisan key:generate
   ```

5. Jalankan server Laravel:

   ```bash
   php artisan serve
   ```

6. Buka di browser:

   ```
   http://127.0.0.1:8000/upload
   ```

## Struktur Folder Penting

* `app/Http/Controllers/UploadController.php` → logic upload file
* `resources/views/` → Blade templates (form upload dan hasil analisis)
* `routes/web.php` → routing aplikasi

## Cara Kontribusi

1. Buat branch baru untuk fitur/bugfix:

   ```bash
   git checkout -b fitur-nama
   ```
2. Commit perubahan:

   ```bash
   git add .
   git commit -m "Tambah fitur upload PDF"
   ```
3. Push ke GitHub:

   ```bash
   git push origin fitur-nama
   ```
4. Buat Pull Request di GitHub.

## Lisensi

Proyek ini dibuat untuk keperluan akademik di Institut Teknologi Sepuluh Nopember (ITS) tahun ajaran 2025/2026.
