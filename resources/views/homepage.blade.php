<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Formacheck TA ITS — Cek Format Tugas Akhir Berbasis AI</title>
  <meta name="description" content="Formacheck TA ITS membantu mahasiswa memeriksa kesesuaian format penulisan Tugas Akhir sesuai panduan ITS secara otomatis dengan AI." />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --brand-blue: #667eea; /* from-blue-500/600 */
      --brand-purple: #764ba2; /* to-purple-600/700 */
    }
    html, body { height: 100%; }
    body { font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif; }
    .gradient-bg { background: linear-gradient(135deg, var(--brand-blue) 0%, var(--brand-purple) 100%); }
    .btn-brand { @apply inline-flex items-center gap-2 rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-lg; }
  </style>
</head>
<body class="gradient-bg text-slate-900 antialiased">
  <!-- NAVBAR (match upload.blade.php style) -->
  <nav class="navbar shadow-lg border-b border-gray-200 bg-white/90 backdrop-blur sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
              <i class="fas fa-graduation-cap text-white text-lg"></i>
            </div>
            <span class="ml-3 text-xl font-bold text-gray-800">Formacheck ITS</span>
          </div>
          <!-- Navigation Links -->
          <div class="hidden md:ml-6 md:flex md:space-x-8">
            <a href="#fitur" class="border-transparent text-gray-700 hover:text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
              <i class="fas fa-sparkles mr-2"></i>
              Fitur
            </a>
            <a href="#cara-kerja" class="border-transparent text-gray-700 hover:text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
              <i class="fas fa-gears mr-2"></i>
              Cara Kerja
            </a>
            <a href="#panduan" class="border-transparent text-gray-700 hover:text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
              <i class="fas fa-book mr-2"></i>
              Panduan
            </a>
            <a href="#faq" class="border-transparent text-gray-700 hover:text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
              <i class="fas fa-circle-question mr-2"></i>
              FAQ
            </a>
          </div>
        </div>
        <!-- Right side -->
        <div class="flex items-center">
          <div class="hidden md:flex items-center space-x-4">
            @auth
              <span class="text-sm text-gray-700">
                <i class="fas fa-user-circle mr-1"></i>
                {{ Auth::user()->name }}
              </span>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-700 hover:text-blue-600 transition">
                  <i class="fas fa-sign-out-alt mr-1"></i>Logout
                </button>
              </form>
            @endauth
            @guest
              <a href="{{ route('login.form') }}" class="text-sm text-gray-700 hover:text-blue-600 transition">
                <i class="fas fa-right-to-bracket mr-1"></i>Login
              </a>
            @endguest
            <a
              @auth href="{{ route('upload.form') }}" @endauth
              @guest href="{{ route('login.form') }}" @endguest
              class="ml-2 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-95">
              <i class="fas fa-upload"></i>
              Cek Dokumen
            </a>
          </div>
          <!-- Mobile menu button -->
          <div class="-mr-2 flex md:hidden">
            <button id="mobileMenuButton" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-600 hover:text-blue-600 focus:outline-none">
              <span class="sr-only">Open main menu</span>
              <i class="fas fa-bars text-xl"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Mobile menu -->
    <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200 bg-white">
      <div class="pt-2 pb-3 space-y-1">
        <a href="#fitur" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600">
          <i class="fas fa-sparkles mr-2"></i>Fitur</a>
        <a href="#cara-kerja" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600">
          <i class="fas fa-gears mr-2"></i>Cara Kerja</a>
        <a href="#panduan" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600">
          <i class="fas fa-book mr-2"></i>Panduan</a>
        <a href="#faq" class="block pl-3 pr-4 py-2 text-base font-medium text-gray-700 hover:text-blue-600">
          <i class="fas fa-circle-question mr-2"></i>FAQ</a>
        <a @auth href="{{ route('upload.form') }}" @endauth @guest href="{{ route('login.form') }}" @endguest class="bg-blue-50 border-l-4 border-blue-600 text-blue-700 block pl-3 pr-4 py-2 text-base font-medium">
          <i class="fas fa-upload mr-2"></i>Upload TA</a>
      </div>
    </div>
  </nav>

  <!-- HERO -->
  <section class="py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-10 lg:grid-cols-2">
        <div class="text-white">
          <div class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs">
            <span class="inline-flex h-2 w-2 rounded-full bg-white"></span>
            Sesuai Pedoman Format ITS
          </div>
          <h1 class="mt-4 text-4xl md:text-5xl font-extrabold leading-tight">Cek Format Tugas Akhir <span class="opacity-90">otomatis</span> berbasis AI</h1>
          <p class="mt-4 max-w-xl text-white/90">Validasi struktur, tipografi, margin, abstrak, Bab 1, sitasi APA 7, serta cover — langsung dapatkan laporan & saran perbaikan.</p>
          <div class="mt-6 flex flex-wrap items-center gap-3">
            <a
              @auth href="{{ route('upload.form') }}" @endauth
              @guest href="{{ route('login.form') }}" @endguest
              class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-blue-700 shadow-lg hover:bg-blue-50">
              <i class="fas fa-upload"></i> Unggah Dokumen
            </a>
            <a href="#contoh" class="inline-flex items-center gap-2 rounded-xl border border-white/60 bg-white/10 px-5 py-3 text-sm font-semibold text-white hover:bg-white/20">
              <i class="fas fa-file-alt"></i> Lihat Contoh Laporan
            </a>
          </div>
          <p class="mt-3 text-xs text-white/80">Mendukung .docx & .pdf (maks 20MB). Privasi terjaga.</p>
        </div>
        <div>
          <div class="rounded-2xl bg-white/90 backdrop-blur shadow-xl border border-white/40 p-4">
            <!-- Mockup cards -->
            <div class="grid gap-4 md:grid-cols-2">
              <div class="rounded-xl border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500">Kelengkapan Struktur</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">98%</p>
                <ul class="mt-2 space-y-1 text-sm text-gray-600">
                  <li>✓ Cover</li>
                  <li>✓ Abstrak (ID & EN)</li>
                  <li>✓ Daftar Isi</li>
                </ul>
              </div>
              <div class="rounded-xl border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500">Format Teks</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">Perlu Revisi</p>
                <ul class="mt-2 space-y-1 text-sm text-gray-600">
                  <li>• Margin kiri 3 cm ✅</li>
                  <li>• Margin kanan 2 cm ❌</li>
                  <li>• Spasi 1.0 ✅</li>
                </ul>
              </div>
              <div class="rounded-xl border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500">Abstrak</p>
                <p class="mt-1 text-2xl font-bold text-emerald-600">Sesuai</p>
                <p class="mt-2 text-sm text-gray-600">250 kata, bahasa Indonesia & Inggris terdeteksi.</p>
              </div>
              <div class="rounded-xl border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-500">Sitasi & Daftar Pustaka</p>
                <p class="mt-1 text-2xl font-bold text-amber-600">Butuh Perbaikan</p>
                <p class="mt-2 text-sm text-gray-600">Format APA 7 belum konsisten (3 item).</p>
              </div>
            </div>
          </div>
          <p class="mt-3 text-center text-xs text-white/80">Contoh visual keluaran AI (dummy).</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FITUR -->
  <section id="fitur" class="bg-white/95 backdrop-blur border-y border-white/40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="mx-auto max-w-2xl text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">Fitur Pemeriksaan Cerdas</h2>
        <p class="mt-3 text-gray-600">Mengikuti pedoman ITS: struktur, tipografi, margin, abstrak, Bab 1–5, dan konsistensi sitasi APA 7.</p>
      </div>
      <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
              <i class="fas fa-layer-group"></i>
            </span>
            <h3 class="text-base font-semibold text-gray-900">Kelengkapan Struktur</h3>
          </div>
          <p class="mt-3 text-sm text-gray-600">Deteksi Cover, Abstrak (ID & EN), Daftar Isi, dan Bab wajib sesuai tipe naskah (Proposal/Laporan).</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
              <i class="fas fa-text-height"></i>
            </span>
            <h3 class="text-base font-semibold text-gray-900">Format Teks & Margin</h3>
          </div>
          <p class="mt-3 text-sm text-gray-600">Validasi Times New Roman 12pt, spasi 1.0, margin 3–2.5–3–2 cm, serta konsistensi heading.</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
              <i class="fas fa-file-lines"></i>
            </span>
            <h3 class="text-base font-semibold text-gray-900">Abstrak (ID & EN)</h3>
          </div>
          <p class="mt-3 text-sm text-gray-600">Hitung 200–300 kata, deteksi bahasa, dan saran kebahasaan.</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
              <i class="fas fa-book-open"></i>
            </span>
            <h3 class="text-base font-semibold text-gray-900">Bab 1 Pendahuluan</h3>
          </div>
          <p class="mt-3 text-sm text-gray-600">Verifikasi latar belakang, rumusan masalah, batasan, tujuan, dan manfaat.</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
              <i class="fas fa-quote-right"></i>
            </span>
            <h3 class="text-base font-semibold text-gray-900">Sitasi APA 7</h3>
          </div>
          <p class="mt-3 text-sm text-gray-600">Deteksi inkonsistensi kutipan & daftar pustaka, lengkap dengan saran perbaikan.</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
          <div class="flex items-center gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
              <i class="fas fa-id-card"></i>
            </span>
            <h3 class="text-base font-semibold text-gray-900">Cover & Halaman Judul</h3>
          </div>
          <p class="mt-3 text-sm text-gray-600">Pemeriksaan skema biru ITS, font Trebuchet MS pada cover, dan kontras teks putih.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CARA KERJA -->
  <section id="cara-kerja" class="bg-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid items-start gap-10 lg:grid-cols-2">
        <div class="text-white">
          <h2 class="text-3xl font-extrabold">Cara Kerja</h2>
          <ol class="mt-6 space-y-6">
            <li class="flex gap-4">
              <div class="h-9 w-9 flex-none rounded-full bg-white text-blue-700 grid place-items-center font-bold">1</div>
              <div>
                <h3 class="text-base font-semibold">Unggah Dokumen</h3>
                <p class="text-sm text-white/90">Pilih .docx/.pdf. Sistem mengekstrak teks & metadata format.</p>
              </div>
            </li>
            <li class="flex gap-4">
              <div class="h-9 w-9 flex-none rounded-full bg-white text-blue-700 grid place-items-center font-bold">2</div>
              <div>
                <h3 class="text-base font-semibold">Analisis AI</h3>
                <p class="text-sm text-white/90">Rule + NLP memeriksa struktur, tipografi, margin, abstrak, Bab 1, sitasi & cover.</p>
              </div>
            </li>
            <li class="flex gap-4">
              <div class="h-9 w-9 flex-none rounded-full bg-white text-blue-700 grid place-items-center font-bold">3</div>
              <div>
                <h3 class="text-base font-semibold">Laporan & Rekomendasi</h3>
                <p class="text-sm text-white/90">Skor kepatuhan + daftar temuan & saran perbaikan. Dapat diunduh (PDF).</p>
              </div>
            </li>
          </ol>
        </div>
        <div>
          <div id="unggah" class="rounded-2xl border-2 border-dashed border-white/50 bg-white/90 backdrop-blur p-8 text-center shadow-xl">
            <div class="mx-auto max-w-md">
              <div class="mx-auto grid h-14 w-14 place-items-center rounded-xl bg-blue-50 text-blue-700">
                <i class="fas fa-cloud-upload-alt text-xl"></i>
              </div>
              <h3 class="mt-4 text-lg font-semibold text-gray-900">Tarik & Letakkan file Anda</h3>
              <p class="mt-2 text-sm text-gray-600">atau klik untuk memilih .docx / .pdf (maks 20MB)</p>
              <div class="mt-5">
                <a
                  @auth href="{{ route('upload.form') }}" @endauth
                  @guest href="{{ route('login.form') }}" @endguest
                  class="inline-flex cursor-pointer items-center gap-3 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:opacity-95">
                  <i class="fas fa-upload"></i>
                  Pilih / Unggah File
                </a>
              </div>
              <p class="mt-3 text-xs text-gray-500">Aman — file tidak dibagikan.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- RINGKASAN PANDUAN -->
  <section id="panduan" class="bg-white/95 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="mx-auto max-w-3xl text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">Ringkasan Aturan yang Dicek</h2>
        <p class="mt-3 text-gray-600">Aspek yang divalidasi otomatis sesuai pedoman ITS.</p>
      </div>
      <div class="mt-10 grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl border border-gray-200 bg-white p-6">
          <h3 class="font-semibold text-gray-900">Struktur Dokumen</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
            <li>Cover, Abstrak (ID+EN), Daftar Isi</li>
            <li>Proposal: Bab 1–3 | Laporan: Bab 1–5</li>
          </ul>
          <h3 class="mt-5 font-semibold text-gray-900">Format Teks</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
            <li>Times New Roman 12pt</li>
            <li>Spasi 1.0; Margin: 3–2.5–3–2 cm</li>
          </ul>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-6">
          <h3 class="font-semibold text-gray-900">Abstrak</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
            <li>200–300 kata</li>
            <li>Bahasa Indonesia & Inggris</li>
          </ul>
          <h3 class="mt-5 font-semibold text-gray-900">Bab 1 Pendahuluan</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
            <li>Latar belakang, Rumusan masalah, Batasan, Tujuan, Manfaat</li>
          </ul>
          <h3 class="mt-5 font-semibold text-gray-900">Daftar Pustaka</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
            <li>Format APA 7; sitasi konsisten</li>
          </ul>
          <h3 class="mt-5 font-semibold text-gray-900">Cover & Halaman Judul</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
            <li>Latar biru ITS, font Trebuchet MS, teks putih</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTOH & SARAN -->
  <section id="contoh" class="bg-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid items-start gap-8 lg:grid-cols-2">
        <div class="rounded-2xl border border-white/30 bg-white/90 backdrop-blur p-6 shadow-xl">
          <h3 class="text-lg font-semibold text-gray-900">Ringkasan Kelayakan</h3>
          <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-emerald-50 p-4">
              <p class="text-xs font-medium text-emerald-700">Kepatuhan Umum</p>
              <p class="mt-1 text-2xl font-extrabold text-emerald-700">92%</p>
            </div>
            <div class="rounded-xl bg-amber-50 p-4">
              <p class="text-xs font-medium text-amber-700">Item Perlu Revisi</p>
              <p class="mt-1 text-2xl font-extrabold text-amber-700">5</p>
            </div>
          </div>
          <ul class="mt-6 space-y-3 text-sm text-gray-700">
            <li>• 2 entri daftar pustaka belum sesuai format APA 7.</li>
            <li>• Margin kanan 2 cm (seharusnya 2.5 cm).</li>
            <li>• Abstrak Inggris 310 kata (melewati batas 300).</li>
            <li>• Bab 1 tidak memuat "Batasan masalah" eksplisit.</li>
            <li>• Cover belum menggunakan Trebuchet MS untuk judul.</li>
          </ul>
          <a @auth href="{{ route('upload.form') }}" @endauth @guest href="{{ route('login.form') }}" @endguest class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white hover:opacity-95">
            <i class="fas fa-upload"></i> Cek Dokumen Saya
          </a>
        </div>
        <div class="rounded-2xl border border-white/30 bg-white/90 backdrop-blur p-6 shadow-xl">
          <h3 class="text-lg font-semibold text-gray-900">Saran Perbaikan</h3>
          <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm text-gray-700">
            <li>Atur margin kanan menjadi 2.5 cm (Page Setup).</li>
            <li>Sesuaikan abstrak Inggris ke 200–300 kata.</li>
            <li>Tambahkan subbab <em>Batasan Masalah</em> pada Bab 1.</li>
            <li>Perbaiki 2 entri referensi ke gaya APA 7.</li>
            <li>Gunakan Trebuchet MS untuk judul di cover, teks putih di atas biru ITS.</li>
          </ol>
          <p class="mt-4 text-xs text-gray-500">*Contoh bersifat ilustratif.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- TESTIMONI -->
  <section class="bg-white/95 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="mx-auto max-w-3xl text-center">
        <h2 class="text-3xl font-extrabold text-gray-900">Dipakai & Disukai Mahasiswa ITS</h2>
        <p class="mt-3 text-gray-600">Hemat waktu revisi, fokus ke substansi riset.</p>
      </div>
      <div class="mt-10 grid gap-6 md:grid-cols-3">
        <figure class="rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-700 shadow-sm">“Laporan kepatuhan jelas, bimbingan jadi cepat.”<figcaption class="mt-3 font-semibold text-gray-900">— Naila, SI 2021</figcaption></figure>
        <figure class="rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-700 shadow-sm">“Paling membantu pas revisi. Tinggal ikuti saran.”<figcaption class="mt-3 font-semibold text-gray-900">— Farhan, IF 2020</figcaption></figure>
        <figure class="rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-700 shadow-sm">“Cek abstrak & APA 7 otomatisnya top.”<figcaption class="mt-3 font-semibold text-gray-900">— Sinta, Stat 2022</figcaption></figure>
      </div>
    </div>
  </section>

  <!-- CTA AKHIR -->
  <section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-blue-600 to-purple-600"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid items-center gap-8 lg:grid-cols-2">
        <div>
          <h2 class="text-3xl font-extrabold text-white">Siap cek format Tugas Akhir Anda?</h2>
          <p class="mt-2 text-white/80">Unggah naskah dan terima laporan lengkap.</p>
        </div>
        <div class="text-left lg:text-right">
          <a @auth href="{{ route('upload.form') }}" @endauth @guest href="{{ route('login.form') }}" @endguest class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-50">
            Mulai Pemeriksaan <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="border-t border-white/30 bg-white/90 backdrop-blur">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid gap-8 md:grid-cols-3">
        <div>
          <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center text-white">
              <i class="fas fa-graduation-cap text-sm"></i>
            </div>
            <span class="text-sm font-semibold text-gray-900">Formacheck ITS</span>
          </div>
          <p class="mt-3 text-sm text-gray-600">Validasi format TA otomatis sesuai pedoman ITS.</p>
        </div>
        <div>
          <h4 class="text-sm font-semibold text-gray-900">Navigasi</h4>
          <ul class="mt-3 space-y-2 text-sm text-gray-700">
            <li><a href="#fitur" class="hover:text-blue-700">Fitur</a></li>
            <li><a href="#cara-kerja" class="hover:text-blue-700">Cara Kerja</a></li>
            <li><a href="#panduan" class="hover:text-blue-700">Panduan</a></li>
            <li><a href="#faq" class="hover:text-blue-700">FAQ</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-sm font-semibold text-gray-900">Dapatkan Update</h4>
          <form class="mt-3 flex gap-2">
            <input type="email" placeholder="Email ITS Anda" class="w-full rounded-xl border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-200" />
            <button type="button" class="rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white">Langganan</button>
          </form>
        </div>
      </div>
      <div class="mt-10 flex flex-col items-center justify-between gap-3 border-t border-gray-200 pt-6 text-xs text-gray-500 md:flex-row">
        <p>© <span id="year"></span> Formacheck ITS. All rights reserved.</p>
        <p>Dibuat untuk mahasiswa ITS — patuh pedoman penulisan.</p>
      </div>
    </div>
  </footer>

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
    const btn = document.getElementById('mobileMenuButton');
    const menu = document.getElementById('mobileMenu');
    if (btn && menu) btn.addEventListener('click', () => menu.classList.toggle('hidden'));
  </script>
</body>
</html>
