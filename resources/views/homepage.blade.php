<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FormatCheck TA ITS — Cek Format Tugas Akhir Berbasis AI</title>
  <meta name="description" content="FormatCheck TA ITS membantu mahasiswa memeriksa kesesuaian format penulisan Tugas Akhir sesuai panduan ITS secara otomatis dengan AI." />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }

    /* Navbar styles */
    .navbar {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.98);
    }

    /* Optimized animations */
    .nav-link {
      transition: all 0.2s ease-in-out;
    }

    .nav-link:hover {
      color: #2563eb;
      transform: translateY(-1px);
    }

    .card-hover {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card-hover:hover {
      transform: translateY(-8px);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
    }

    .btn-hover {
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-hover:hover {
      transform: translateY(-3px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
    }

    /* Floating animation */
    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-15px) rotate(2deg); }
    }
    
    .floating {
      animation: float 6s ease-in-out infinite;
    }

    /* Smooth scroll behavior */
    html {
      scroll-behavior: smooth;
      scroll-padding-top: 80px;
    }

    /* Loading animation for cards */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-in-up {
      animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Gradient text */
    .gradient-text {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }

    /* Mobile optimizations */
    @media (max-width: 768px) {
      .mobile-padding {
        padding-left: 1rem;
        padding-right: 1rem;
      }
      
      .mobile-text-center {
        text-align: center;
      }
      
      .mobile-stack {
        flex-direction: column;
      }
    }

    /* Performance optimizations */
    .will-change-transform {
      will-change: transform;
    }
  </style>
</head>
<body class="flex flex-col min-h-screen">
  <!-- NAVBAR - Optimized -->
  <nav class="navbar shadow-lg border-b border-gray-200/50 sticky top-0 z-50 will-change-transform">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 md:h-20">
        <div class="flex items-center">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
              <i class="fas fa-graduation-cap text-white text-lg md:text-xl"></i>
            </div>
            <a href="{{ url('/') }}" class="flex items-center ml-3">
              <span class="text-xl md:text-2xl font-bold text-gray-800">FormatCheck ITS</span>
            </a>
          </div>
          
          <!-- Navigation Links -->
          <div class="hidden md:ml-8 md:flex md:space-x-6">
            <a href="#fitur" class="nav-link border-transparent text-gray-600 hover:text-gray-900 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200">
              <i class="fas fa-rocket mr-2 text-blue-500"></i>
              Fitur
            </a>
            <a href="#cara-kerja" class="nav-link border-transparent text-gray-600 hover:text-gray-900 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200">
              <i class="fas fa-gears mr-2 text-purple-500"></i>
              Cara Kerja
            </a>
            <a href="#panduan" class="nav-link border-transparent text-gray-600 hover:text-gray-900 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200">
              <i class="fas fa-book mr-2 text-green-500"></i>
              Panduan
            </a>
            <a href="#contoh" class="nav-link border-transparent text-gray-600 hover:text-gray-900 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200">
              <i class="fas fa-file-alt mr-2 text-orange-500"></i>
              Contoh
            </a>
          </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-3 md:space-x-6">
          <div class="hidden md:flex items-center space-x-6">
            @auth
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-blue-600 transition-all duration-200 font-medium">
                  <i class="fas fa-sign-out-alt mr-1"></i>Logout
                </button>
              </form>
            @endauth
            @guest
              <a href="{{ route('login.form') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-all duration-200 font-medium">
                <i class="fas fa-right-to-bracket mr-1"></i>Login
              </a>
            @endguest
            <a
              @auth href="{{ route('upload.form') }}" @endauth
              @guest href="{{ route('login.form') }}" @endguest
              class="btn-hover inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl ml-4">
              <i class="fas fa-upload"></i>
              <span class="hidden lg:inline">Cek Dokumen</span>
              <span class="lg:hidden">Upload</span>
            </a>
          </div>
          <!-- Mobile menu button -->
          <div class="md:hidden">
            <button id="mobileMenuButton" type="button" class="inline-flex items-center justify-center p-3 rounded-xl text-gray-600 hover:text-blue-600 hover:bg-gray-100 transition-all duration-200">
              <span class="sr-only">Open main menu</span>
              <i class="fas fa-bars text-xl"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Mobile menu - FIXED: Remove Upload TA highlight and show Login instead -->
    <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200 bg-white/95 backdrop-blur-lg">
      <div class="pt-2 pb-4 space-y-1">
        <a href="#fitur" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
          <i class="fas fa-rocket mr-2 text-blue-500"></i>Fitur</a>
        <a href="#cara-kerja" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
          <i class="fas fa-gears mr-3 text-purple-500"></i>Cara Kerja</a>
        <a href="#panduan" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
          <i class="fas fa-book mr-3 text-green-500"></i>Panduan</a>
        <a href="#contoh" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
          <i class="fas fa-file-alt mr-3 text-orange-500"></i>Contoh</a>
        
        <!-- FIXED: Show Login button instead of Upload TA for mobile -->
        <div class="border-t border-gray-200 pt-2 mt-2">
          @auth
            <div class="px-4 py-2 text-sm text-gray-600">
              <i class="fas fa-user-circle mr-2"></i>
              {{ Auth::user()->name }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
                <i class="fas fa-sign-out-alt mr-3"></i>Logout
              </button>
            </form>
            <a href="{{ route('upload.form') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
              <i class="fas fa-upload mr-3"></i>Upload TA
            </a>
          @endauth
          @guest
            <a href="{{ route('login.form') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
              <i class="fas fa-right-to-bracket mr-3"></i>Login
            </a>
          @endguest
        </div>
      </div>
    </div>
  </nav>

  <!-- HERO - Optimized -->
  <section class="py-16 md:py-24 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-12 lg:gap-16 xl:gap-20 lg:grid-cols-2">
        <div class="text-white mobile-text-center">
          <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-sm border border-white/20 mb-6 animate-fade-in-up">
            <span class="inline-flex h-2 w-2 rounded-full bg-white animate-pulse"></span>
            Sesuai Pedoman Format ITS 2025
          </div>
          <h1 class="text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-extrabold leading-tight md:leading-tight lg:leading-tight animate-fade-in-up" style="animation-delay: 0.1s">
            Cek Format <span>Tugas Akhir</span> Otomatis
          </h1>
          <p class="mt-6 text-lg md:text-xl lg:text-2xl text-white/90 max-w-2xl animate-fade-in-up" style="animation-delay: 0.2s">
            Validasi struktur, tipografi, margin, abstrak, Bab 1, sitasi APA 7 — langsung dapatkan laporan & saran perbaikan berbasis AI.
          </p>
          <div class="mt-8 flex flex-wrap items-center gap-4 animate-fade-in-up mobile-stack" style="animation-delay: 0.3s">
            <a
              @auth href="{{ route('upload.form') }}" @endauth
              @guest href="{{ route('login.form') }}" @endguest
              class="btn-hover inline-flex items-center gap-3 rounded-2xl bg-white px-8 py-4 text-base font-bold text-blue-700 shadow-2xl hover:bg-blue-50 hover:scale-105">
              <i class="fas fa-upload text-lg"></i> 
              Unggah Dokumen Sekarang
            </a>
            <a href="#contoh" class="btn-hover inline-flex items-center gap-3 rounded-2xl border-2 border-white/60 bg-white/10 backdrop-blur-sm px-6 py-4 text-base font-semibold text-white hover:bg-white/20 hover:scale-105">
              <i class="fas fa-chart-bar text-lg"></i>
              Lihat Contoh Analisis
            </a>
          </div>
          <div class="mt-6 flex items-center gap-6 text-sm text-white/80 animate-fade-in-up" style="animation-delay: 0.4s">
            <div class="flex items-center gap-2">
              <i class="fas fa-shield-check text-green-400"></i>
              <span>Privasi Terjaga</span>
            </div>
            <div class="flex items-center gap-2">
              <i class="fas fa-bolt text-yellow-400"></i>
              <span>Analisis Cepat</span>
            </div>
            <div class="flex items-center gap-2">
              <i class="fas fa-file-alt text-blue-400"></i>
              <span>PDF & DOCX</span>
            </div>
          </div>
        </div>
        <div class="animate-fade-in-up" style="animation-delay: 0.5s">
          <div class="card-hover rounded-3xl bg-white/95 backdrop-blur-lg shadow-2xl border border-white/40 p-6 lg:p-8">
            <div class="grid gap-5 md:gap-6 lg:gap-7 md:grid-cols-2">
              <!-- Card 1 -->
              <div class="rounded-2xl border border-gray-200/80 bg-gradient-to-br from-white to-gray-50 p-5 lg:p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-700">Kelengkapan Struktur</p>
                </div>
                <p class="text-2xl lg:text-3xl font-bold text-emerald-600 mb-2">98%</p>
                <ul class="space-y-2 text-xs lg:text-sm text-gray-600">
                  <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500 text-xs"></i> Cover & Halaman Judul</li>
                  <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500 text-xs"></i> Abstrak (ID & EN)</li>
                  <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500 text-xs"></i> Daftar Isi</li>
                </ul>
              </div>
              
              <!-- Card 2 -->
              <div class="rounded-2xl border border-gray-200/80 bg-gradient-to-br from-white to-gray-50 p-5 lg:p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-lg"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-700">Format Teks</p>
                </div>
                <p class="text-xl lg:text-2xl font-bold text-amber-600 mb-2">Perlu Revisi</p>
                <ul class="space-y-2 text-xs lg:text-sm text-gray-600">
                  <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500 text-xs"></i> Margin kiri 3 cm</li>
                  <li class="flex items-center gap-2"><i class="fas fa-times text-red-500 text-xs"></i> Margin kanan 2 cm</li>
                  <li class="flex items-center gap-2"><i class="fas fa-check text-emerald-500 text-xs"></i> Spasi 1.0</li>
                </ul>
              </div>
              
              <!-- Card 3 -->
              <div class="rounded-2xl border border-gray-200/80 bg-gradient-to-br from-white to-gray-50 p-5 lg:p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-file-lines text-emerald-600 text-lg"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-700">Abstrak</p>
                </div>
                <p class="text-2xl lg:text-3xl font-bold text-emerald-600 mb-2">Sesuai</p>
                <p class="text-xs lg:text-sm text-gray-600">250 kata, bahasa Indonesia & Inggris terdeteksi dengan sempurna.</p>
              </div>
              
              <!-- Card 4 -->
              <div class="rounded-2xl border border-gray-200/80 bg-gradient-to-br from-white to-gray-50 p-5 lg:p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-quote-right text-amber-600 text-lg"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-700">Sitasi & Pustaka</p>
                </div>
                <p class="text-xl lg:text-2xl font-bold text-amber-600 mb-2">Butuh Perbaikan</p>
                <p class="text-xs lg:text-sm text-gray-600">Format APA 7 belum konsisten pada 3 item referensi.</p>
              </div>
            </div>
          </div>
          <p class="mt-4 text-center text-sm text-white/80 animate-fade-in-up">Contoh visual keluaran AI — Hasil analisis real-time</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FITUR - Optimized -->
  <section id="fitur" class="bg-white/95 backdrop-blur-lg border-y border-white/40 py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mx-auto max-w-4xl text-center mb-16 lg:mb-20">
        <div class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-500 px-4 py-2 text-sm text-white font-semibold mb-6">
          <i class="fas fa-rocket"></i>
          Fitur Unggulan
        </div>
        <h2 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-6">Pemeriksaan <span>Menyeluruh</span></h2>
        <p class="text-xl md:text-2xl text-gray-600 max-w-3xl mx-auto">Setiap aspek format Tugas Akhir ITS diperiksa secara detail dan akurat</p>
      </div>
      <div class="grid gap-8 sm:gap-10 lg:gap-12 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Feature 1 -->
        <div class="card-hover rounded-3xl border border-gray-200 bg-white p-8 shadow-lg hover:border-blue-200/50 animate-fade-in-up">
          <div class="flex items-center gap-4 mb-6">
            <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 shadow-sm">
              <i class="fas fa-layer-group text-2xl"></i>
            </span>
            <h3 class="text-xl font-bold text-gray-900">Struktur Dokumen</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Deteksi otomatis Cover, Abstrak (ID & EN), Daftar Isi, dan Bab wajib sesuai tipe naskah Proposal atau Laporan.</p>
          <ul class="mt-4 space-y-2 text-sm text-gray-500">
            <li class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i> Cover & Halaman Judul</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i> Abstrak Bahasa Indonesia</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-blue-500 text-xs"></i> Abstrak Bahasa Inggris</li>
          </ul>
        </div>

        <!-- Feature 2 -->
        <div class="card-hover rounded-3xl border border-gray-200 bg-white p-8 shadow-lg hover:border-purple-200/50 animate-fade-in-up" style="animation-delay: 0.1s">
          <div class="flex items-center gap-4 mb-6">
            <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-purple-50 text-purple-600 shadow-sm">
              <i class="fas fa-text-height text-2xl"></i>
            </span>
            <h3 class="text-xl font-bold text-gray-900">Format Teks</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Validasi Times New Roman 12pt, spasi 1.0, margin 3–2.5–3–2 cm, serta konsistensi heading dan paragraf.</p>
          <ul class="mt-4 space-y-2 text-sm text-gray-500">
            <li class="flex items-center gap-2"><i class="fas fa-check text-purple-500 text-xs"></i> Font & Ukuran</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-purple-500 text-xs"></i> Spasi & Margin</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-purple-500 text-xs"></i> Konsistensi Heading</li>
          </ul>
        </div>

        <!-- Feature 3 -->
        <div class="card-hover rounded-3xl border border-gray-200 bg-white p-8 shadow-lg hover:border-green-200/50 animate-fade-in-up" style="animation-delay: 0.2s">
          <div class="flex items-center gap-4 mb-6">
            <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-green-50 text-green-600 shadow-sm">
              <i class="fas fa-file-lines text-2xl"></i>
            </span>
            <h3 class="text-xl font-bold text-gray-900">Abstrak</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Hitung 200–300 kata, deteksi bahasa otomatis, dan saran kebahasaan untuk abstrak Indonesia dan Inggris.</p>
          <ul class="mt-4 space-y-2 text-sm text-gray-500">
            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Jumlah Kata</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Deteksi Bahasa</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-green-500 text-xs"></i> Saran Kebahasaan</li>
          </ul>
        </div>

        <!-- Feature 4 -->
        <div class="card-hover rounded-3xl border border-gray-200 bg-white p-8 shadow-lg hover:border-orange-200/50 animate-fade-in-up" style="animation-delay: 0.3s">
          <div class="flex items-center gap-4 mb-6">
            <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-orange-50 text-orange-600 shadow-sm">
              <i class="fas fa-book-open text-2xl"></i>
            </span>
            <h3 class="text-xl font-bold text-gray-900">Bab 1 Pendahuluan</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Verifikasi lengkap latar belakang, rumusan masalah, batasan, tujuan, dan manfaat penelitian.</p>
          <ul class="mt-4 space-y-2 text-sm text-gray-500">
            <li class="flex items-center gap-2"><i class="fas fa-check text-orange-500 text-xs"></i> Latar Belakang</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-orange-500 text-xs"></i> Rumusan Masalah</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-orange-500 text-xs"></i> Batasan & Tujuan</li>
          </ul>
        </div>

        <!-- Feature 5 -->
        <div class="card-hover rounded-3xl border border-gray-200 bg-white p-8 shadow-lg hover:border-red-200/50 animate-fade-in-up" style="animation-delay: 0.4s">
          <div class="flex items-center gap-4 mb-6">
            <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-red-50 text-red-600 shadow-sm">
              <i class="fas fa-quote-right text-2xl"></i>
            </span>
            <h3 class="text-xl font-bold text-gray-900">Sitasi APA 7</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Deteksi inkonsistensi kutipan & daftar pustaka, lengkap dengan saran perbaikan format APA Edisi 7.</p>
          <ul class="mt-4 space-y-2 text-sm text-gray-500">
            <li class="flex items-center gap-2"><i class="fas fa-check text-red-500 text-xs"></i> Format Kutipan</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-red-500 text-xs"></i> Daftar Pustaka</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-red-500 text-xs"></i> Konsistensi Referensi</li>
          </ul>
        </div>

        <!-- Feature 6 -->
        <div class="card-hover rounded-3xl border border-gray-200 bg-white p-8 shadow-lg hover:border-indigo-200/50 animate-fade-in-up" style="animation-delay: 0.5s">
          <div class="flex items-center gap-4 mb-6">
            <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600 shadow-sm">
              <i class="fas fa-id-card text-2xl"></i>
            </span>
            <h3 class="text-xl font-bold text-gray-900">Cover & Halaman Formal</h3>
          </div>
          <p class="text-gray-600 leading-relaxed">Pemeriksaan skema biru ITS, font Trebuchet MS pada cover, dan kontras teks putih sesuai standar.</p>
          <ul class="mt-4 space-y-2 text-sm text-gray-500">
            <li class="flex items-center gap-2"><i class="fas fa-check text-indigo-500 text-xs"></i> Warna & Layout</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-indigo-500 text-xs"></i> Font Standar</li>
            <li class="flex items-center gap-2"><i class="fas fa-check text-indigo-500 text-xs"></i> Kontras Teks</li>
          </ul>
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
          <div id="unggah" class="card-hover rounded-2xl border-2 border-dashed border-white/50 bg-white/90 backdrop-blur p-8 text-center shadow-xl">
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
                  class="btn-hover inline-flex cursor-pointer items-center gap-3 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm">
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
        <div class="card-hover rounded-2xl border border-gray-200 bg-white p-6">
          <h3 class="font-semibold text-gray-900">Struktur Dokumen</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
            <li>Cover, Abstrak (ID+EN), Daftar Isi</li>
            <li>Proposal: Bab 1-3 | Laporan: Bab 1-5</li>
          </ul>
          <h3 class="mt-5 font-semibold text-gray-900">Format Teks</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700">
            <li>Times New Roman 12pt</li>
            <li>Spasi 1.0; Margin: 3-2.5-3-2 cm</li>
          </ul>
        </div>
        <div class="card-hover rounded-2xl border border-gray-200 bg-white p-6">
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
        <div class="card-hover rounded-2xl border border-white/30 bg-white/90 backdrop-blur p-6 shadow-xl">
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
          <a @auth href="{{ route('upload.form') }}" @endauth @guest href="{{ route('login.form') }}" @endguest class="btn-hover mt-6 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white">
            <i class="fas fa-upload"></i> Cek Dokumen Saya
          </a>
        </div>
        <div class="card-hover rounded-2xl border border-white/30 bg-white/90 backdrop-blur p-6 shadow-xl">
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
        <figure class="card-hover rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-700 shadow-sm">"Laporan kepatuhan jelas, bimbingan jadi cepat."<figcaption class="mt-3 font-semibold text-gray-900">— Naila, SI 2021</figcaption></figure>
        <figure class="card-hover rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-700 shadow-sm">"Paling membantu pas revisi. Tinggal ikuti saran."<figcaption class="mt-3 font-semibold text-gray-900">— Farhan, IF 2020</figcaption></figure>
        <figure class="card-hover rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-700 shadow-sm">"Cek abstrak & APA 7 otomatisnya top."<figcaption class="mt-3 font-semibold text-gray-900">— Sinta, Stat 2022</figcaption></figure>
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
          <a @auth href="{{ route('upload.form') }}" @endauth @guest href="{{ route('login.form') }}" @endguest class="btn-hover inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-50">
            Mulai Pemeriksaan <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER - Diperbarui untuk konsistensi -->
  <footer class="bg-gray-800 text-white py-8">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-6 md:mb-0 text-center md:text-left">
          <div class="flex items-center justify-center md:justify-start">
            <a href="{{ url('/') }}" class="flex items-center">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
              </div>
              <span class="text-xl font-bold">FormatCheck ITS</span>
            </a>
          </div>
          <p class="text-gray-400 text-sm mt-2">Sistem Deteksi Kelengkapan Format Tugas Akhir</p>
        </div>
        
        <div class="flex space-x-6">
          <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="GitHub">
            <i class="fab fa-github text-xl"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="Email">
            <i class="fas fa-envelope text-xl"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="Tentang">
            <i class="fas fa-info-circle text-xl"></i>
          </a>
        </div>
      </div>
      
      <div class="border-t border-gray-700 mt-6 pt-6 text-center">
        <p class="text-gray-400 text-sm">
          © 2025 Sistem Deteksi Kelengkapan Format Tugas Akhir - ITS. All rights reserved.
        </p>
      </div>
    </div>
  </footer>

  <script>
    // Mobile menu toggle
    const btn = document.getElementById('mobileMenuButton');
    const menu = document.getElementById('mobileMenu');
    if (btn && menu) {
        btn.addEventListener('click', () => menu.classList.toggle('hidden'));
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', (e) => {
      if (!e.target.closest('#mobileMenu') && !e.target.closest('#mobileMenuButton') && menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
      }
    });

    // Active section indicator
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');

    function updateActiveSection() {
        let current = '';
        const scrollY = window.pageYOffset;

        sections.forEach(section => {
            const sectionHeight = section.offsetHeight;
            const sectionTop = section.offsetTop - 100;
            const sectionId = section.getAttribute('id');

            if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                current = sectionId;
            }
        });

        navLinks.forEach(link => {
            if (link.classList) {
                link.classList.remove('border-blue-500', 'text-gray-900');
                link.classList.add('border-transparent', 'text-gray-500');
                
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.remove('border-transparent', 'text-gray-500');
                    link.classList.add('border-blue-500', 'text-gray-900');
                }
            }
        });
    }

    if (sections.length > 0 && navLinks.length > 0) {
        window.addEventListener('scroll', updateActiveSection);
        window.addEventListener('load', updateActiveSection);
    }
  </script>
</body>
</html>