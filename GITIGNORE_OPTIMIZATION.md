# Optimasi .gitignore untuk Repository TAkCekIn ITS

## Perubahan yang Dilakukan

### Folder/File yang Di-ignore (Tidak Di-upload ke GitHub)

#### 1. **Python Virtual Environment** (~341MB)
- `/venv/` - Virtual environment Python dengan semua dependencies
- Alasan: Bisa di-recreate dengan `python -m venv venv` dan `pip install -r requirements.txt`

#### 2. **Storage Uploads & Results** (~313MB)
- `/storage/app/private/uploads/` - PDF yang di-upload user
- `/storage/app/private/results/` - JSON hasil analisa
- `/storage/logs/*.log` - Log files
- `/storage/framework/cache/` - Cache Laravel
- `/storage/framework/sessions/` - Session files
- `/storage/framework/views/` - Compiled Blade views
- Alasan: Data sementara yang berubah terus, tidak perlu di-track

#### 3. **Public PDFs** (~117MB)
- `/public/pdfs/*.pdf` - PDF yang di-serve ke user
- `/public/temp/*.pdf` - Temporary PDF files
- Alasan: Copy dari storage uploads, tidak perlu duplikasi

#### 4. **Reference & Documentation** (~107MB)
- `/Referensi/` - Dokumen referensi contoh TA
- `/pedoman/` - Pedoman ITS
- Alasan: File besar yang tidak berubah, bisa disimpan terpisah

#### 5. **Dependencies**
- `/vendor/` - Composer packages (~76MB)
- `/node_modules/` - NPM packages
- Alasan: Bisa di-recreate dengan `composer install` dan `npm install`

#### 6. **Backup & Temporary Files**
- `*.save`, `*.bak`, `*.backup`, `*.swp`, `*.tmp`
- Alasan: File backup editor/IDE yang tidak perlu

#### 7. **Python Cache**
- `__pycache__/`, `*.pyc`, `*.pyo`
- `.pytest_cache/`
- Alasan: Generated files yang bisa di-recreate

#### 8. **Database**
- `*.sqlite`, `*.sqlite-journal`
- Alasan: Database lokal development

## Struktur Folder yang Tetap Di-track

Folder-folder berikut tetap di-track dengan file `.gitkeep`:
- `storage/app/private/uploads/.gitkeep`
- `storage/app/private/results/.gitkeep`
- `public/pdfs/.gitkeep`
- `public/temp/.gitkeep`

Ini memastikan struktur folder tetap ada saat clone repository.

## Cara Setup Repository Setelah Clone

1. **Install PHP Dependencies**
   ```bash
   composer install
   ```

2. **Setup Environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Setup Python Virtual Environment**
   ```bash
   python -m venv venv
   source venv/bin/activate  # Linux/Mac
   # atau
   .\venv\Scripts\activate  # Windows
   pip install -r python/requirements.txt
   ```

4. **Setup Database**
   ```bash
   touch database/database.sqlite
   php artisan migrate
   ```

5. **Create Storage Symlink**
   ```bash
   php artisan storage:link
   ```

6. **Run Development Server**
   ```bash
   php artisan serve
   ```

## Estimasi Pengurangan Ukuran Repository

| Item | Sebelum | Sesudah | Pengurangan |
|------|---------|---------|-------------|
| venv/ | 341MB | 0MB | 341MB |
| storage/ | 313MB | ~1MB | 312MB |
| public/pdfs/ | 117MB | 0MB | 117MB |
| Referensi/ | 107MB | 0MB | 107MB |
| vendor/ | 76MB | 0MB | 76MB |
| **Total** | **~954MB** | **~1MB** | **~953MB** |

## Catatan

- File `.env` tetap di-ignore untuk keamanan (credentials)
- File `composer-setup.php` di-remove karena tidak perlu di-track
- File backup seperti `*.save` di-ignore
- Logs dan cache di-ignore untuk menghindari file yang terus berubah

## Best Practices

1. **Jangan commit file besar** - Gunakan `.gitignore`
2. **Commit dependencies configuration, bukan dependencies itu sendiri** - `composer.json`, `package.json`, `requirements.txt`
3. **Gunakan `.gitkeep` untuk folder kosong yang perlu struktur**
4. **Dokumentasikan setup** - README dengan langkah instalasi
