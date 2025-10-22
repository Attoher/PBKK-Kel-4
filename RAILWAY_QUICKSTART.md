# 🚀 Quick Deploy ke Railway.app

## Step-by-Step (5 Menit)

### 1. Push ke GitHub

```bash
git add .
git commit -m "Ready for deployment"
git push origin feature/aitry
```

### 2. Deploy di Railway

1. Buka: https://railway.app/
2. Login dengan GitHub
3. Klik **"New Project"**
4. Pilih **"Deploy from GitHub repo"**
5. Pilih repository **`PBKK-Kel-4`**
6. Pilih branch **`feature/aitry`** atau **`main`**

### 3. Set Environment Variables

Setelah deploy awal, klik tab **"Variables"** dan tambahkan:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:q36FLfYNrRgFaBXaPIgz02qRcyPISRIWjPR3ZxiStQI=

OPENROUTER_API_KEY=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
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
OPENROUTER_MODEL=meta-llama/llama-3.2-3b-instruct:free

DB_CONNECTION=sqlite
SESSION_DRIVER=database
CACHE_STORE=database
LOG_LEVEL=error
```

### 4. Redeploy

Klik **"Deploy"** → **"Redeploy"** untuk apply environment variables.

### 5. Done! ✅

Railway akan memberikan URL seperti:
```
https://pbkk-kel-4-production.up.railway.app
```

Akses URL tersebut dan aplikasi sudah live! 🎉

---

## 🔧 Troubleshooting

### Build Failed?

Check Railway logs:
- Klik **"Deployments"**
- Klik deployment yang gagal
- Lihat **"Build Logs"**

Common issues:
- **Python module error**: Pastikan `python/requirements.txt` lengkap
- **Composer error**: Pastikan `composer.json` valid
- **Permission error**: Railway auto-handle ini

### App Crashed?

Check Runtime logs:
- Klik **"Deployments"**
- Klik deployment yang running
- Lihat **"Deploy Logs"**

Common fixes:
```bash
# Di Railway Console:
php artisan config:clear
php artisan route:clear
chmod -R 775 storage bootstrap/cache
```

### Storage Issues?

Railway menggunakan ephemeral storage. File upload akan hilang saat redeploy.

**Solusi**:
- Upgrade ke Railway Pro ($5/month) untuk persistent storage
- Atau gunakan cloud storage (Cloudflare R2, AWS S3)

---

## 💰 Biaya

**Free Tier**:
- $5 credit per bulan
- ~500 jam uptime (cukup untuk demo/testing)
- 100 GB bandwidth

**Upgrade**:
- Hobby: $5/month (500 jam + persistent storage)
- Pro: $20/month (unlimited)

---

## 📊 Monitoring

Railway dashboard menampilkan:
- CPU usage
- Memory usage
- Request count
- Error rate

Access di: https://railway.app/dashboard

---

## 🔄 Auto-Deploy

Railway otomatis redeploy setiap kali Anda push ke GitHub:

```bash
git add .
git commit -m "Update feature"
git push
# Railway auto-redeploy dalam 2-3 menit
```

---

## ✅ Checklist

- [ ] Code pushed ke GitHub
- [ ] Railway project created
- [ ] Environment variables set
- [ ] First deploy success
- [ ] URL accessible
- [ ] Upload PDF test works
- [ ] AI analysis works

---

Selamat! Aplikasi Anda sudah online! 🎉
