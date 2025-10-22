# 🌿 Deploy dari Branch Tertentu

Panduan lengkap deploy Railway/Render dari branch selain `main`.

---

## 🚂 Railway.app

### Method 1: Pilih Branch Saat Deploy (MUDAH)

Sayangnya Railway **tidak bisa** pilih branch saat initial deploy. Harus ganti setelah deploy pertama.

### Method 2: Ganti Branch Setelah Deploy (RECOMMENDED)

#### Step 1: Deploy Awal (dari main/default)
```bash
git push origin feature/fixsementara
```

#### Step 2: Buka Railway
1. https://railway.app/
2. New Project → Deploy from GitHub
3. Pilih repo `PBKK-Kel-4`
4. Deploy akan pakai branch default (main)

#### Step 3: Ganti ke Branch yang Diinginkan
1. Klik project Anda
2. Klik **Settings** (ikon gear ⚙️)
3. Scroll ke bagian **"Source"**
4. Klik **"Configure"** atau dropdown branch
5. Pilih **`feature/fixsementara`**
6. Railway otomatis **redeploy** dari branch baru ✅

#### Step 4: Verifikasi
```bash
# Railway Logs akan menunjukkan:
# "Deploying from branch: feature/fixsementara"
```

---

## 🎨 Render.com

### Method 1: Pilih Branch Saat Deploy (MUDAH) ✅

Render **bisa** pilih branch langsung saat deploy!

#### Step 1: Push Branch
```bash
git push origin feature/fixsementara
```

#### Step 2: Buat Web Service
1. https://render.com/
2. New + → Web Service
3. Connect repository `PBKK-Kel-4`

#### Step 3: Pilih Branch
Di form deployment:
- **Name**: `pbkk-ta-checker`
- **Branch**: `feature/fixsementara` ← **Pilih di sini!**
- **Build Command**: `bash build.sh`
- **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

#### Step 4: Deploy!
Klik "Create Web Service" dan Render akan deploy dari branch pilihan Anda.

### Method 2: Ganti Branch Setelah Deploy

Jika sudah deploy:
1. Dashboard → Web Service Anda
2. **Settings** → **Build & Deploy**
3. **Branch**: Ganti ke `feature/fixsementara`
4. Klik **Save Changes**
5. Manual deploy: klik **"Manual Deploy"** → **"Deploy latest commit"**

---

## 🔄 Auto-Deploy dari Branch

### Railway
Setelah set branch di Settings, setiap push ke branch tersebut akan **auto-deploy**:

```bash
# Edit code...
git add .
git commit -m "Update feature"
git push origin feature/fixsementara
# Railway auto-redeploy dalam 2-3 menit ✅
```

### Render
Sama seperti Railway, auto-deploy aktif:

```bash
git push origin feature/fixsementara
# Render auto-redeploy ✅
```

---

## 🔀 Deploy dari Multiple Branch

### Scenario: Dev, Staging, Production

#### Setup 3 Service di Railway:

1. **Development** (branch: `dev`)
   - Railway Project 1 → Settings → Branch: `dev`
   - URL: `https://pbkk-dev.railway.app`

2. **Staging** (branch: `feature/fixsementara`)
   - Railway Project 2 → Settings → Branch: `feature/fixsementara`
   - URL: `https://pbkk-staging.railway.app`

3. **Production** (branch: `main`)
   - Railway Project 3 → Settings → Branch: `main`
   - URL: `https://pbkk-prod.railway.app`

#### Workflow:
```bash
# Development
git push origin dev
# Auto deploy ke Development environment

# Staging (untuk testing)
git push origin feature/fixsementara
# Auto deploy ke Staging environment

# Production (setelah QA)
git checkout main
git merge feature/fixsementara
git push origin main
# Auto deploy ke Production environment
```

---

## 🛠️ Troubleshooting

### Railway tidak auto-deploy setelah ganti branch?

1. Pastikan sudah push ke branch baru:
   ```bash
   git push origin feature/fixsementara
   ```

2. Manual trigger deploy:
   - Railway Dashboard → Deployments
   - Klik **"Deploy"** → **"Redeploy"**

3. Check Settings:
   - Settings → Source
   - Pastikan branch sudah benar

### Render deploy dari branch lama?

1. Check Settings → Build & Deploy
2. Pastikan Branch sudah diganti
3. Manual deploy: **"Manual Deploy"** → **"Clear build cache & deploy"**

### Error: "Branch not found"

```bash
# Pastikan branch sudah di-push ke remote:
git push origin feature/fixsementara

# Cek branch remote:
git branch -r

# Refresh GitHub connection:
# Railway/Render → Settings → Reconnect GitHub
```

---

## 📋 Checklist Deploy dari Branch

- [x] Branch sudah dibuat: `feature/fixsementara`
- [x] Code sudah di-commit
- [x] Branch sudah di-push ke GitHub: `git push origin feature/fixsementara`
- [ ] Railway/Render project sudah dibuat
- [ ] Branch sudah dipilih di Settings
- [ ] Environment variables sudah diset
- [ ] Deploy berhasil
- [ ] URL accessible
- [ ] Auto-deploy sudah teruji (push lagi ke branch)

---

## 💡 Tips

1. **Branch Naming**: Gunakan nama yang jelas
   - ✅ `feature/fixsementara`
   - ✅ `feature/ai-improvements`
   - ✅ `staging`
   - ❌ `test123`

2. **Environment Variables**: Bisa beda per branch
   - Dev: `APP_DEBUG=true`
   - Staging: `APP_DEBUG=false`, test API keys
   - Production: `APP_DEBUG=false`, production API keys

3. **Custom Domain**: Bisa pakai subdomain berbeda
   - Dev: `dev.your-domain.com`
   - Staging: `staging.your-domain.com`
   - Production: `your-domain.com`

---

## ✅ Kesimpulan

- **Railway**: Ganti branch di Settings setelah deploy awal
- **Render**: Bisa pilih branch langsung saat deploy ✅
- **Auto-deploy**: Aktif untuk semua branch yang dipilih
- **Multiple Environments**: Buat service terpisah per branch

Sekarang Anda bisa deploy dari branch `feature/fixsementara`! 🚀
