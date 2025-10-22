# 🚀 Deployment Guide - TA Format Checker ITS

Panduan deploy aplikasi Laravel + Python ke platform cloud gratis.

## 📋 Prerequisites

-   Repository Git (GitHub/GitLab)
-   OpenRouter API Key (gratis di https://openrouter.ai/)
-   Akun platform cloud pilihan Anda

---

## 🎯 Opsi 1: Railway.app (RECOMMENDED)

### Kelebihan:

✅ Setup paling mudah  
✅ Support Laravel + Python otomatis  
✅ Free tier: $5 credit/bulan (~500 jam)  
✅ Auto SSL/HTTPS  
✅ GitHub integration

### Langkah Deploy:

1. **Push ke GitHub**

    ```bash
    git add .
    git commit -m "Ready for deployment"
    git push origin feature/fixsementara  # atau branch lain
    ```

2. **Buka Railway.app**

    - Login dengan GitHub: https://railway.app/
    - Klik "New Project"
    - Pilih "Deploy from GitHub repo"
    - Pilih repository `PBKK-Kel-4`
    - **PENTING**: Klik "Settings" → "Source" → Pilih branch `feature/fixsementara`

3. **Set Environment Variables (PENTING!)**

    Klik tab "Variables" di Railway Dashboard, tambahkan SATU PER SATU:

    ```
    APP_ENV=production
    APP_DEBUG=true
    APP_KEY=base64:q36FLfYNrRgFaBXaPIgz02qRcyPISRIWjPR3ZxiStQI=

    OPENROUTER_API_KEY=YOUR_OPENROUTER_API_KEY_HERE
    OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
    OPENROUTER_MODEL=meta-llama/llama-3.2-3b-instruct:free

    DB_CONNECTION=sqlite
    SESSION_DRIVER=database
    CACHE_STORE=database
    LOG_CHANNEL=stack
    LOG_LEVEL=debug
    ```

    ⚠️ **CHECKLIST WAJIB**:
    - [ ] APP_KEY ada dan benar (jangan kosong!)
    - [ ] OPENROUTER_API_KEY ada dan benar
    - [ ] DB_CONNECTION=sqlite (jangan mysql!)
    - [ ] APP_DEBUG=true (untuk debugging, ubah ke false setelah jalan)

4. **Deploy!**
    - Railway akan otomatis build & deploy setelah push ke GitHub
    - TIDAK PERLU command manual di terminal Railway
    - Build akan otomatis membuat database, migrate, dan setup storage
    - Railway akan otomatis build & deploy
    - Tunggu ~3-5 menit
    - Akses di: `https://your-app-name.up.railway.app`

---

## 🎯 Opsi 2: Render.com

### Kelebihan:

✅ Free tier unlimited (750 jam/bulan)  
✅ Auto SSL  
⚠️ Spin down setelah 15 menit idle

### Langkah Deploy:

1. **Push ke GitHub** (sama seperti Railway)

2. **Buka Render.com**

    - Login: https://render.com/
    - Klik "New +" → "Web Service"
    - Connect GitHub repository

3. **Configure Build**

    ```
    Name: pbkk-ta-checker
    Environment: PHP
    Build Command: bash build.sh
    Start Command: php artisan serve --host=0.0.0.0 --port=$PORT
    ```

4. **Set Environment Variables**
   (Sama seperti Railway di atas)

5. **Deploy!**
    - Klik "Create Web Service"
    - Tunggu build selesai (~5-10 menit)
    - Akses di: `https://pbkk-ta-checker.onrender.com`

---

## 🎯 Opsi 3: Vercel (Frontend) + Railway (Backend)

Jika ingin performa lebih baik, pisahkan frontend & backend:

### Deploy Backend (API) di Railway

-   Follow Opsi 1 di atas
-   Catat URL: `https://api.railway.app`

### Deploy Frontend di Vercel

-   Push Laravel ke Vercel (hanya serve public folder)
-   Free, unlimited bandwidth
-   CDN global

---

## 🔧 Troubleshooting

### Error: "Tidak dapat terhubung ke server" / "Failed to fetch"

**Penyebab:**
- Railway deployment belum selesai atau gagal
- Server crash atau tidak berjalan
- Network/firewall blocking

**Solusi:**

1. **Cek Status Deployment di Railway:**
   - Buka Railway Dashboard → Deployments
   - Pastikan status deployment **"SUCCESS"** (hijau)
   - Jika **"BUILDING"** (kuning) → tunggu selesai
   - Jika **"FAILED"** (merah) → klik "View Logs" untuk lihat error

2. **Cek Railway Logs:**
   ```
   Railway Dashboard → Deployments → Latest → View Logs
   ```
   Cari error seperti:
   - `Error: externally-managed-environment` → Python env issue
   - `ERROR: failed to build` → Build gagal
   - `Port already in use` → Restart deployment

3. **Test Server Manually:**
   - Buka: https://pbkk-kel-4-production.up.railway.app/
   - Jika error 500/404 → server jalan tapi ada bug
   - Jika timeout/no response → server tidak jalan

4. **Restart Deployment:**
   - Railway Dashboard → Deployments
   - Klik "..." → "Redeploy"

5. **Cek Environment Variables:**
   - Railway Dashboard → Variables
   - Pastikan APP_KEY, OPENROUTER_API_KEY sudah di-set

### Error: "Storage directory not writable"

```bash
# Di Railway/Render console:
chmod -R 775 storage bootstrap/cache
```

### Error: "Class not found"

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Python Module Error

```bash
pip install -r python/requirements.txt
```

### Database Error

```bash
touch database/database.sqlite
php artisan migrate --force
```

### Error: Upload Chunk Failed (403/500)

**Cek Railway Logs:**
```
Keyword: "Upload chunk error" atau "Merge error"
```

**Penyebab umum:**
- Folder storage/app/chunks tidak bisa dibuat → permission issue
- Memory limit exceeded → file terlalu besar
- CSRF token mismatch → refresh browser

**Solusi:**
- Pastikan build phase membuat folder chunks
- Cek permission di start.sh (chmod 777)
- Clear browser cache dan refresh

---

## 📊 Perbandingan Platform

| Platform    | Free Tier         | Uptime          | Cold Start | Python Support     |
| ----------- | ----------------- | --------------- | ---------- | ------------------ |
| **Railway** | $5 credit (~500h) | 24/7            | No         | ✅ Native          |
| **Render**  | 750h/month        | Spin down 15min | ~1 min     | ✅ Native          |
| **Fly.io**  | 3 VMs free        | 24/7            | No         | ✅ Docker          |
| **Vercel**  | Unlimited         | 24/7            | No         | ❌ Serverless only |

---

## 🎯 Rekomendasi

1. **Untuk Testing/Demo**: Railway.app (paling mudah)
2. **Untuk Production**: Render.com (unlimited free tier)
3. **Untuk Traffic Tinggi**: Upgrade ke paid tier ($7-20/month)

---

## 📝 Checklist Pre-Deploy

-   [ ] Push semua code ke GitHub
-   [ ] Update `.env.example` dengan config production
-   [ ] Test lokal: `php artisan serve`
-   [ ] Test Python script: `python python/analyze_pdf_openrouter.py`
-   [ ] Pastikan `railway.json` dan `render.yaml` ada
-   [ ] Siapkan OpenRouter API Key

---

## 🆘 Need Help?

-   Railway Docs: https://docs.railway.app/
-   Render Docs: https://render.com/docs
-   Laravel Deployment: https://laravel.com/docs/deployment

Good luck! 🚀
