# 🔧 Railway Deployment Debug Guide

## Status Check Checklist

### 1. Cek Railway Dashboard
- [ ] Login ke https://railway.app/dashboard
- [ ] Buka project: **PBKK-Kel-4**
- [ ] Lihat status deployment terakhir

**Status yang mungkin:**
- ✅ **SUCCESS** (hijau) → Deployment berhasil
- ⚠️ **BUILDING** (kuning) → Masih proses build (~2-5 menit)
- ❌ **FAILED** (merah) → Build gagal
- ⏸️ **CRASHED** → Server crash setelah start

### 2. Cek Logs

**Cara akses:**
1. Railway Dashboard → Deployments
2. Klik deployment terakhir
3. Klik **"View Logs"** atau tab **"Deploy Logs"**

**Yang harus terlihat (jika sukses):**
```
✅ Build completed successfully
🚀 Starting Laravel application...
📦 Activating Python virtual environment...
📝 Creating .env file from Railway variables...
✅ .env file created successfully
🐍 Testing Python environment...
Python 3.11.x
✓ PyMuPDF installed
✓ PyPDF2 installed
✓ openai installed
📁 Checking storage permissions...
🔗 Creating storage link...
🗃️ Running database migrations...
✅ Starting PHP server on port 8080...
Server running on [http://0.0.0.0:8080]
```

**Error yang mungkin muncul:**

#### Error: `externally-managed-environment`
```
❌ This environment is externally managed
```
**Fix:** Sudah diperbaiki di commit terakhir (menggunakan venv)

#### Error: `APP_KEY is not set`
```
❌ ERROR: APP_KEY is not set!
```
**Fix:** 
- start.sh otomatis generate APP_KEY
- Atau set manual di Railway Variables

#### Error: `Python not found` / `Module not found`
```
⚠️ Python not found in PATH
⚠️ PyMuPDF not found
```
**Fix:**
- Cek nixpacks.toml install phase
- Redeploy untuk reinstall packages

### 3. Test Endpoints

**Homepage:**
```
https://pbkk-kel-4-production.up.railway.app/
```
- **200 OK** → Server jalan
- **500 Error** → Laravel error (cek logs)
- **Timeout** → Server tidak jalan

**Upload Page:**
```
https://pbkk-kel-4-production.up.railway.app/upload
```
- **200 OK** → Route berfungsi
- **404** → Route tidak ditemukan
- **CSRF token** harus ada di page source

**Chunk Upload Endpoint (test via curl):**
```bash
curl -X POST https://pbkk-kel-4-production.up.railway.app/upload/chunk \
  -H "Content-Type: multipart/form-data" \
  -F "file=@test.txt" \
  -F "uploadId=test123" \
  -F "chunkIndex=0" \
  -F "totalChunks=1" \
  -F "fileName=test.pdf"
```

**Expected:**
- ✅ `{"success":false,"message":"..."}` → Route exists, CSRF error normal
- ❌ `404 Not Found` → Route missing
- ❌ `Connection refused` → Server down

### 4. Browser Console Debug

**Cara:**
1. Buka https://pbkk-kel-4-production.up.railway.app/upload
2. Tekan **F12** → Tab **Console**
3. Upload file PDF kecil
4. Perhatikan logs

**Success logs:**
```javascript
Uploading chunk 0 to: https://pbkk-kel-4-production.up.railway.app/upload/chunk
Chunk upload response: {"success":true,"message":"Chunk 0 uploaded successfully"}
Merging chunks... {uploadId: "...", fileName: "test.pdf"}
Merge response: {"success":true,"redirect":"/results/test.pdf"}
Redirecting to: /results/test.pdf
```

**Error logs:**
```javascript
// CSRF Error
Chunk upload failed: 419 - CSRF token mismatch

// Permission Error  
Chunk upload failed: 500 - Gagal membuat folder chunks

// Network Error
Upload error: Failed to fetch

// Server Down
net::ERR_CONNECTION_REFUSED
```

### 5. Railway Variables Check

**Required Variables:**
```
APP_KEY=base64:q36FLfYNrRgFaBXaPIgz02qRcyPISRIWjPR3ZxiStQI=
OPENROUTER_API_KEY=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
OPENROUTER_BASE_URL=https://openrouter.ai/api/v1
OPENROUTER_MODEL=meta-llama/llama-3.2-3b-instruct:free
DB_CONNECTION=sqlite
APP_ENV=production
APP_DEBUG=true
LOG_LEVEL=debug
```

**Cara cek:**
1. Railway Dashboard → Variables tab
2. Pastikan semua ada
3. Jika edit → Railway auto-redeploy

### 6. Common Fixes

#### Redeploy
```
Railway Dashboard → Deployments → "..." → Redeploy
```

#### Force Rebuild
```
Railway Dashboard → Settings → "Trigger Deploy"
```

#### Check Disk Space
```
Railway Dashboard → Metrics → Storage
```
- Jika > 90% → hapus cache/logs

#### Restart Service
```
Railway Dashboard → Settings → "Restart"
```

### 7. Emergency: Switch to main Branch

Jika `feature/fixsementara` bermasalah:

```bash
# Local
git checkout main
git merge feature/fixsementara
git push origin main

# Railway
Settings → Source → Change branch to "main"
```

### 8. Last Resort: Recreate Service

1. Backup environment variables
2. Railway Dashboard → Settings → Delete Service
3. Create new service from GitHub
4. Pilih branch `feature/fixsementara`
5. Add environment variables
6. Wait for deployment

---

## Quick Diagnostic Commands

**Test if server is up:**
```bash
curl -I https://pbkk-kel-4-production.up.railway.app/
```

**Test upload endpoint:**
```bash
curl https://pbkk-kel-4-production.up.railway.app/upload/chunk
```

**Check Railway status:**
```bash
# Install Railway CLI first
railway status
railway logs
```

---

## Contact & Support

Jika masih bermasalah setelah semua troubleshooting:

1. **Screenshot:**
   - Railway deployment status
   - Railway logs (full output)
   - Browser console (saat upload)

2. **Info:**
   - Branch yang digunakan
   - Commit terakhir
   - Kapan terakhir berhasil

3. **Test:**
   - Coba dari device/network lain
   - Coba file yang berbeda (< 1MB)
   - Coba browser lain (Chrome/Firefox)
