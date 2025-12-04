<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="api-base" content="{{ url('/') }}">
  <title>TAkCekIn TA ITS — Cek Format Tugas Akhir Berbasis AI</title>
  <link rel="icon" type="image/png" href="{{ asset('icon/favicon.png') }}">
  <link rel="shortcut icon" href="{{ asset('icon/favicon.png') }}">
  <meta name="description" content="TAkCekIn TA ITS membantu mahasiswa memeriksa kesesuaian format penulisan Tugas Akhir sesuai panduan ITS secara otomatis dengan AI." />
  <script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="{{ asset('css/loading-its.css') }}">
<link rel="stylesheet" href="{{ asset('css/formatcheck-its.css') }}">
<link rel="stylesheet" href="{{ asset('css/dark-its.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  
  <!-- Add animate.css for additional animations -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  
  <style>
    /* Custom Animation Classes */
    /* Typewriter animation for sequential typing */
    .typewriter-container {
      position: relative;
      display: inline-block;
    }
    
    .typewriter-text {
      overflow: hidden;
      white-space: nowrap;
      display: inline-block;
    }
    
    @keyframes typing {
      from { width: 0 }
      to { width: 100% }
    }
    
    /* Pulse glow animation */
    @keyframes pulse-glow {
      0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4); }
      70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
      100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
    }
    
    .pulse-glow {
      animation: pulse-glow 2s infinite;
    }
    
    /* Text gradient animation */
    @keyframes gradient-shift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    
    .gradient-animate {
      background: linear-gradient(90deg, #3b82f6, #8b5cf6, #3b82f6);
      background-size: 200% auto;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      animation: gradient-shift 3s ease infinite;
    }
    
    /* Staggered fade in */
    .stagger-fade-in {
      opacity: 0;
      transform: translateY(20px);
      animation: staggerFadeIn 0.6s ease forwards;
    }
    
    @keyframes staggerFadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    /* Wave animation */
    .wave {
      animation: wave 2s infinite;
      transform-origin: 70% 70%;
      display: inline-block;
    }
    
    @keyframes wave {
      0% { transform: rotate(0deg); }
      10% { transform: rotate(14deg); }
      20% { transform: rotate(-8deg); }
      30% { transform: rotate(14deg); }
      40% { transform: rotate(-4deg); }
      50% { transform: rotate(10deg); }
      60% { transform: rotate(0deg); }
      100% { transform: rotate(0deg); }
    }
    
    /* Scroll-triggered animations */
    .animate-on-scroll {
      opacity: 0;
      transform: translateY(30px);
      transition: all 0.6s ease;
    }
    
    .animate-on-scroll.visible {
      opacity: 1;
      transform: translateY(0);
    }
    
    /* Shine effect */
    @keyframes shine {
      0% { background-position: -100px; }
      100% { background-position: 200px; }
    }
    
    .shine-effect {
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      background-size: 200px 100%;
      animation: shine 2s infinite;
    }
  </style>
</head>
<body class="flex flex-col min-h-screen no-horizontal-scroll">
  <!-- NAVBAR - Optimized -->
  <nav class="navbar shadow-lg border-b border-gray-200/50 sticky top-0 z-50 will-change-transform animate-on-scroll">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 safe-area">
      <div class="flex justify-between h-16 xl:h-20">
        <div class="flex items-center">
          <!-- Logo with animation -->
          <div class="flex-shrink-0 flex items-center">
            <div class="w-10 h-10 xl:w-12 xl:h-12 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg pulse-glow">
              <i class="fas fa-graduation-cap text-white text-lg xl:text-xl floating-element"></i>
            </div>
            <a href="{{ url('/') }}" class="flex items-center ml-3">
              <span class="text-xl xl:text-2xl font-bold text-gray-800 break-words gradient-animate">TAkCekIn ITS</span>
            </a>
          </div>
          
          <!-- Navigation Links -->
          <div  class="hidden xl:ml-8 xl:flex xl:space-x-6">
            <a href="#fitur" class="nav-link border-transparent text-gray-600 hover:text-gray-900 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200 break-words hover:scale-105">
              <i class="fas fa-rocket mr-2 text-blue-500 wave"></i>
              Fitur
            </a>
            <a href="#cara-kerja" class="nav-link border-transparent text-gray-600 hover:text-gray-900 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200 break-words hover:scale-105">
              <i class="fas fa-gears mr-2 text-purple-500 wave"></i>
              Cara Kerja
            </a>
            <a href="#panduan" class="nav-link border-transparent text-gray-600 hover:text-gray-900 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200 break-words hover:scale-105">
              <i class="fas fa-book mr-2 text-green-500 wave"></i>
              Panduan
            </a>
            <a href="#contoh" class="nav-link border-transparent text-gray-600 hover:text-gray-900 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200 break-words hover:scale-105">
              <i class="fas fa-file-alt mr-2 text-orange-500 wave"></i>
              Contoh
            </a>
          </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-3 xl:space-x-6">
          <div class="hidden xl:flex items-center space-x-6">
            @auth
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-blue-600 transition-all duration-200 font-medium break-words hover:scale-105">
                  <i class="fas fa-sign-out-alt mr-1"></i>Logout
                </button>
              </form>
            @endauth
            @guest
              <a href="{{ route('login.form') }}" class="text-sm text-gray-600 hover:text-blue-600 transition-all duration-200 font-medium break-words hover:scale-105">
                <i class="fas fa-right-to-bracket mr-1"></i>Login
              </a>
            @endguest
            <a
              @auth href="{{ route('upload.form') }}" @endauth
              @guest href="{{ route('login.form') }}" @endguest
              class="btn-hover pulse-glow inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl ml-4 break-words bounce-hover">
              <i class="fas fa-upload floating-element"></i>
              <span class="hidden lg:inline">Cek Dokumen</span>
              <span class="lg:hidden">Upload</span>
            </a>
          </div>
          <!-- Mobile menu button -->
          <div class="xl:hidden">
            <button id="mobileMenuButton" type="button" class="inline-flex items-center justify-center p-3 rounded-xl text-gray-600 hover:text-blue-600 hover:bg-gray-100 transition-all duration-200 bounce-hover">
              <span class="sr-only">Open main menu</span>
              <i class="fas fa-bars text-xl floating-element"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- Mobile menu - FIXED: Remove Upload TA highlight and show Login instead -->
    <div id="mobileMenu" class="xxl:hidden hidden border-t border-gray-200 bg-white/95 backdrop-blur-lg">
      <div class="pt-2 pb-4 space-y-1">
        <a href="#fitur" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words stagger-fade-in" style="animation-delay: 0.1s">
          <i class="fas fa-rocket mr-2 text-blue-500"></i>Fitur</a>
        <a href="#cara-kerja" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words stagger-fade-in" style="animation-delay: 0.2s">
          <i class="fas fa-gears mr-3 text-purple-500"></i>Cara Kerja</a>
        <a href="#panduan" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words stagger-fade-in" style="animation-delay: 0.3s">
          <i class="fas fa-book mr-3 text-green-500"></i>Panduan</a>
        <a href="#contoh" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words stagger-fade-in" style="animation-delay: 0.4s">
          <i class="fas fa-file-alt mr-3 text-orange-500"></i>Contoh</a>
        
        <!-- FIXED: Show Login button instead of Upload TA for mobile -->
        <div class="border-t border-gray-200 pt-2 mt-2">
          @auth
            <div class="px-4 py-2 text-sm text-gray-600 break-words stagger-fade-in" style="animation-delay: 0.5s">
              <i class="fas fa-user-circle mr-2"></i>
              {{ Auth::user()->name }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words stagger-fade-in" style="animation-delay: 0.6s">
                <i class="fas fa-sign-out-alt mr-3"></i>Logout
              </button>
            </form>
            <a href="{{ route('upload.form') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words stagger-fade-in" style="animation-delay: 0.7s">
              <i class="fas fa-upload mr-3"></i>Upload TA
            </a>
          @endauth
          @guest
            <a href="{{ route('login.form') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words stagger-fade-in" style="animation-delay: 0.5s">
              <i class="fas fa-right-to-bracket mr-3"></i>Login
            </a>
          @endguest
        </div>
      </div>
    </div>
  </nav>

  <!-- HERO - Optimized -->
  <section class="py-16 xl:py-24 lg:py-28 safe-area">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid items-center gap-12 lg:gap-16 xl:gap-20 lg:grid-cols-2">
        <div class="text-white mobile-text-center">
          <div class="inline-flex items-center gap-2 rounded-full bg-white/10 backdrop-blur-sm px-4 py-2 text-sm border border-white/20 mb-6 animate-fade-in-up break-words shine-effect">
            <span class="inline-flex h-2 w-2 rounded-full bg-white animate-pulse floating-element"></span>
            Sesuai Pedoman Format ITS 2025
          </div> <br>
          
          <!-- Sequential Typing Animation Title -->
          <div class="mb-6">
            <h1 class="text-3xl xl:text-4xl lg:text-5xl xl:text-6xl font-extrabold leading-tight xl:leading-tight lg:leading-tight text-balance">
              <span id="typewriter-line1" class="typing-animation inline-block" style="animation: typing 2s steps(20, end) 0.5s forwards, blink-caret .75s step-end 3; border-right: 3px solid #3b82f6; width: 0; overflow: hidden; white-space: nowrap;">Cek Format</span>
              <br class="xl:hidden">
              <span id="typewriter-line2" class="typing-animation inline-block" style="animation: typing 2s steps(20, end) 2.5s forwards, blink-caret .75s step-end 3; border-right: 3px solid #3b82f6; width: 0; overflow: hidden; white-space: nowrap;"> <span class="break-words text-green-300">Tugas Akhir</span></span>
              <br class="xl:hidden">
              <span id="typewriter-line3" class="typing-animation inline-block" style="animation: typing 2s steps(20, end) 4.5s forwards, blink-caret .75s step-end infinite; border-right: 3px solid #3b82f6; width: 0; overflow: hidden; white-space: nowrap;"> Otomatis</span>
            </h1>
          </div>
          
          <p class="mt-6 text-lg xl:text-xl lg:text-2xl text-white/90 max-w-2xl animate-fade-in-up break-words stagger-fade-in" style="animation-delay: 0.2s">
            Validasi struktur, tipografi, margin, abstrak, Bab 1, sitasi APA 7 — langsung dapatkan laporan & saran perbaikan berbasis AI.
          </p>
          <div class="mt-8 flex flex-wrap items-center gap-4 animate-fade-in-up mobile-stack stagger-fade-in" style="animation-delay: 0.3s">
            <a
              @auth href="{{ route('upload.form') }}" @endauth
              @guest href="{{ route('login.form') }}" @endguest
              class="btn-hover bounce-hover inline-flex items-center gap-3 rounded-2xl bg-white px-6 xl:px-8 py-4 text-base font-bold text-blue-700 shadow-2xl hover:bg-blue-50 hover:scale-105 break-words pulse-glow">
              <i class="fas fa-upload text-lg floating-element"></i> 
              Unggah Dokumen Sekarang
            </a>
            <a href="#contoh" class="btn-hover bounce-hover inline-flex items-center gap-3 rounded-2xl border-2 border-white/60 bg-white/10 backdrop-blur-sm px-6 py-4 text-base font-semibold text-white hover:bg-white/20 hover:scale-105 break-words">
              <i class="fas fa-chart-bar text-lg floating-element"></i>
              Lihat Contoh Analisis
            </a>
          </div>
          <div class="mt-6 flex items-center gap-6 text-sm text-white/80 animate-fade-in-up flex-wrap stagger-fade-in" style="animation-delay: 0.4s">
            <div class="flex items-center gap-2 break-words">
              <i class="fas fa-shield-check text-green-400 floating-element"></i>
              <span>Privasi Terjaga</span>
            </div>
            <div class="flex items-center gap-2 break-words">
              <i class="fas fa-bolt text-yellow-400 floating-element"></i>
              <span>Analisis Cepat</span>
            </div>
            <div class="flex items-center gap-2 break-words">
              <i class="fas fa-file-alt text-blue-400 floating-element"></i>
              <span>PDF & DOCX</span>
            </div>
          </div>
        </div>
        <div class="animate-fade-in-up" style="animation-delay: 0.5s">
          <div class="card-hover bounce-hover rounded-3xl bg-white/95 backdrop-blur-lg shadow-2xl border border-white/40 p-6 lg:p-8 floating-element">
            <div class="grid gap-5 xl:gap-6 lg:gap-7 xl:grid-cols-2">
              <!-- Card 1 -->
              <div class="rounded-2xl border border-gray-200/80 bg-gradient-to-br from-white to-gray-50 p-5 lg:p-6 shadow-sm hover:shadow-xl transition-all duration-300 bounce-hover stagger-fade-in" style="animation-delay: 0.1s">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center pulse-glow">
                    <i class="fas fa-check-circle text-emerald-600 text-lg floating-element"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-700 break-words">Kelengkapan Struktur</p>
                </div>
                <p class="text-2xl lg:text-3xl font-bold text-emerald-600 mb-2 break-words gradient-animate">98%</p>
                <ul class="space-y-2 text-xs lg:text-sm text-gray-600">
                  <li class="flex items-center gap-2 break-words stagger-fade-in" style="animation-delay: 0.2s"><i class="fas fa-check text-emerald-500 text-xs floating-element"></i> Cover & Halaman Judul</li>
                  <li class="flex items-center gap-2 break-words stagger-fade-in" style="animation-delay: 0.3s"><i class="fas fa-check text-emerald-500 text-xs floating-element"></i> Abstrak (ID & EN)</li>
                  <li class="flex items-center gap-2 break-words stagger-fade-in" style="animation-delay: 0.4s"><i class="fas fa-check text-emerald-500 text-xs floating-element"></i> Daftar Isi</li>
                </ul>
              </div>
              
              <!-- Card 2 -->
              <div class="rounded-2xl border border-gray-200/80 bg-gradient-to-br from-white to-gray-50 p-5 lg:p-6 shadow-sm hover:shadow-xl transition-all duration-300 bounce-hover stagger-fade-in" style="animation-delay: 0.2s">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center pulse-glow">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-lg floating-element"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-700 break-words">Format Teks</p>
                </div>
                <p class="text-xl lg:text-2xl font-bold text-amber-600 mb-2 break-words gradient-animate">Perlu Revisi</p>
                <ul class="space-y-2 text-xs lg:text-sm text-gray-600">
                  <li class="flex items-center gap-2 break-words stagger-fade-in" style="animation-delay: 0.3s"><i class="fas fa-check text-emerald-500 text-xs floating-element"></i> Margin kiri 3 cm</li>
                  <li class="flex items-center gap-2 break-words stagger-fade-in" style="animation-delay: 0.4s"><i class="fas fa-times text-red-500 text-xs floating-element"></i> Margin kanan 2 cm</li>
                  <li class="flex items-center gap-2 break-words stagger-fade-in" style="animation-delay: 0.5s"><i class="fas fa-check text-emerald-500 text-xs floating-element"></i> Spasi 1.0</li>
                </ul>
              </div>
              
              <!-- Card 3 -->
              <div class="rounded-2xl border border-gray-200/80 bg-gradient-to-br from-white to-gray-50 p-5 lg:p-6 shadow-sm hover:shadow-xl transition-all duration-300 bounce-hover stagger-fade-in" style="animation-delay: 0.3s">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center pulse-glow">
                    <i class="fas fa-file-lines text-emerald-600 text-lg floating-element"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-700 break-words">Abstrak</p>
                </div>
                <p class="text-2xl lg:text-3xl font-bold text-emerald-600 mb-2 break-words gradient-animate">Sesuai</p>
                <p class="text-xs lg:text-sm text-gray-600 line-clamp-2 break-words stagger-fade-in" style="animation-delay: 0.4s">250 kata, bahasa Indonesia & Inggris terdeteksi dengan sempurna.</p>
              </div>
              
              <!-- Card 4 -->
              <div class="rounded-2xl border border-gray-200/80 bg-gradient-to-br from-white to-gray-50 p-5 lg:p-6 shadow-sm hover:shadow-xl transition-all duration-300 bounce-hover stagger-fade-in" style="animation-delay: 0.4s">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center pulse-glow">
                    <i class="fas fa-quote-right text-amber-600 text-lg floating-element"></i>
                  </div>
                  <p class="text-sm font-semibold text-gray-700 break-words">Sitasi & Pustaka</p>
                </div>
                <p class="text-xl lg:text-2xl font-bold text-amber-600 mb-2 break-words gradient-animate">Butuh Perbaikan</p>
                <p class="text-xs lg:text-sm text-gray-600 line-clamp-2 break-words stagger-fade-in" style="animation-delay: 0.5s">Format APA 7 belum konsisten pada 3 item referensi.</p>
              </div>
            </div>
          </div>
          <p class="mt-4 text-center text-sm text-white/80 animate-fade-in-up break-words stagger-fade-in" style="animation-delay: 0.6s">Contoh visual keluaran AI — Hasil analisis real-time</p>
        </div>
      </div>
    </div>
  </section>

  <!-- VIDEO DEMO + FITUR - Section terpadu -->
  <section id="fitur" class="bg-gradient-to-br from-blue-50 to-purple-50 py-16 xl:py-20 safe-area">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <!-- Header Section -->
          <div class="text-center mb-12 lg:mb-16">
              <div class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-red-500 to-pink-500 px-4 py-2 text-sm text-white font-semibold mb-6 break-words animate-fade-in-up pulse-glow">
                  <i class="fas fa-play-circle floating-element"></i> Demo & Tutorial
              </div>
              <h2 class="text-3xl xl:text-4xl lg:text-5xl font-extrabold text-gray-900 mb-6 text-balance stagger-fade-in">
                  Lihat <span class="break-words gradient-animate">Cara Kerjanya</span> & <span class="break-words gradient-animate">Fitur Utama</span>
              </h2>
              <p class="text-xl xl:text-2xl text-gray-600 max-w-3xl mx-auto break-words stagger-fade-in" style="animation-delay: 0.1s">
                  Tonton demo dan pelajari fitur lengkap pemeriksaan format Tugas Akhir ITS
              </p>
          </div>

          <!-- Video dan Fitur Grid -->
          <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
              <!-- Kolom Kiri: Video dengan desain clean dan fungsional -->
              <div class="animate-on-scroll" style="animation-delay: 0.1s">
                  <div class="card-hover bounce-hover rounded-3xl border border-gray-200 bg-white overflow-hidden shadow-2xl h-full floating-element">
                      <!-- YouTube Embed - langsung terlihat dan bisa diputar -->
                      <div class="relative">
                          <div class="aspect-video">
                              <iframe 
                                  src="https://www.youtube.com/embed/YcqEDvqLne8?si=nbRDyek6LSyP5JWU" 
                                  title="Demo TAkCekIn TA ITS" 
                                  frameborder="0" 
                                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                  referrerpolicy="strict-origin-when-cross-origin"
                                  allowfullscreen
                                  class="w-full h-full"
                              ></iframe>
                          </div>
                      </div>
                      
                      <!-- Video Info -->
                      <div class="p-6 lg:p-8">
                          <div class="flex items-start justify-between mb-4">
                              <div class="flex-1">
                                  <div class="flex items-center gap-3 mb-2 stagger-fade-in">
                                      <div class="inline-flex items-center gap-1 px-3 py-1 bg-red-50 text-red-700 rounded-full text-xs font-medium pulse-glow">
                                          <i class="fab fa-youtube text-red-600 floating-element"></i>
                                          YouTube
                                      </div>
                                      <span class="text-xs text-gray-500 flex items-center gap-1 stagger-fade-in" style="animation-delay: 0.1s">
                                          <i class="far fa-clock floating-element"></i> 3:45
                                      </span>
                                  </div>
                                  <h3 class="text-lg lg:text-xl font-bold text-gray-900 mb-3 break-words stagger-fade-in" style="animation-delay: 0.2s">Demo Lengkap: TAkCekIn TA ITS</h3>
                                  <p class="text-gray-600 text-sm mb-4 break-words stagger-fade-in" style="animation-delay: 0.3s">
                                      Pelajari cara menggunakan TAkCekIn TA ITS untuk memeriksa format Tugas Akhir Anda secara lengkap dan akurat.
                                  </p>
                              </div>
                          </div>
                          
                          <!-- Fitur yang dipelajari -->
                          <div class="space-y-3 mb-6">
                              <div class="flex items-start gap-3 stagger-fade-in" style="animation-delay: 0.4s">
                                  <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5 pulse-glow">
                                      <i class="fas fa-upload text-blue-600 text-xs floating-element"></i>
                                  </div>
                                  <p class="text-sm text-gray-600 break-words">Cara mengunggah dokumen TA dengan benar</p>
                              </div>
                              <div class="flex items-start gap-3 stagger-fade-in" style="animation-delay: 0.5s">
                                  <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5 pulse-glow">
                                      <i class="fas fa-chart-bar text-green-600 text-xs floating-element"></i>
                                  </div>
                                  <p class="text-sm text-gray-600 break-words">Memahami laporan analisis yang dihasilkan</p>
                              </div>
                              <div class="flex items-start gap-3 stagger-fade-in" style="animation-delay: 0.6s">
                                  <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0 mt-0.5 pulse-glow">
                                      <i class="fas fa-file-alt text-purple-600 text-xs floating-element"></i>
                                  </div>
                                  <p class="text-sm text-gray-600 break-words">Interpretasi saran perbaikan untuk revisi</p>
                              </div>
                          </div>
                          
                          <!-- Tombol aksi -->
                          <div class="flex flex-col sm:flex-row gap-3">
                              <a href="https://youtu.be/YcqEDvqLne8?si=nbRDyek6LSyP5JWU" 
                                target="_blank"
                                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition-all duration-200 btn-hover bounce-hover break-words pulse-glow">
                                  <i class="fab fa-youtube floating-element"></i>
                                  Tonton di YouTube
                              </a>
                              <button onclick="document.querySelector('iframe').contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');" 
                                      class="inline-flex items-center justify-center gap-2 px-5 py-3 border-2 border-gray-300 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-all duration-200 bounce-hover break-words">
                                  <i class="fas fa-play floating-element"></i>
                                  Putar Video
                              </button>
                          </div>
                      </div>
                  </div>
                  
                  <!-- Catatan kecil -->
                  <div class="mt-4 text-center text-sm text-gray-500 break-words stagger-fade-in" style="animation-delay: 0.7s">
                      Video dapat diputar langsung di halaman ini
                  </div>
              </div>
              
              <!-- Kolom Kanan: Fitur dalam grid 2 kolom -->
              <div><br>
                  <!-- Grid Fitur 2 kolom -->
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                      <!-- Feature 1 -->
                      <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 shadow-lg hover:border-blue-200/50 animate-on-scroll">
                          <div class="flex items-center gap-4 mb-4">
                              <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 shadow-sm pulse-glow">
                                  <i class="fas fa-layer-group text-lg floating-element"></i>
                              </span>
                              <h4 class="text-base font-bold text-gray-900 break-words">Struktur Dokumen</h4>
                          </div>
                          <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 break-words mb-3 stagger-fade-in" style="animation-delay: 0.1s">Deteksi otomatis Cover, Abstrak (ID & EN), Daftar Isi, dan Bab wajib.</p>
                          <div class="flex flex-wrap gap-2">
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs pulse-glow">
                                  <i class="fas fa-check text-xs floating-element"></i> Cover
                              </span>
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs pulse-glow">
                                  <i class="fas fa-check text-xs floating-element"></i> Abstrak
                              </span>
                          </div>
                      </div>

                      <!-- Feature 2 -->
                      <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 shadow-lg hover:border-purple-200/50 animate-on-scroll" style="animation-delay: 0.1s">
                          <div class="flex items-center gap-4 mb-4">
                              <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-purple-50 text-purple-600 shadow-sm pulse-glow">
                                  <i class="fas fa-text-height text-lg floating-element"></i>
                              </span>
                              <h4 class="text-base font-bold text-gray-900 break-words">Format Teks</h4>
                          </div>
                          <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 break-words mb-3 stagger-fade-in" style="animation-delay: 0.2s">Validasi font, ukuran, spasi, dan margin sesuai pedoman.</p>
                          <div class="flex flex-wrap gap-2">
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 rounded-full text-xs pulse-glow">
                                  TNR 12pt
                              </span>
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-50 text-purple-700 rounded-full text-xs pulse-glow">
                                  Spasi 1.0
                              </span>
                          </div>
                      </div>

                      <!-- Feature 3 -->
                      <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 shadow-lg hover:border-green-200/50 animate-on-scroll" style="animation-delay: 0.2s">
                          <div class="flex items-center gap-4 mb-4">
                              <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600 shadow-sm pulse-glow">
                                  <i class="fas fa-file-lines text-lg floating-element"></i>
                              </span>
                              <h4 class="text-base font-bold text-gray-900 break-words">Abstrak</h4>
                          </div>
                          <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 break-words mb-3 stagger-fade-in" style="animation-delay: 0.3s">Analisis jumlah kata dan kualitas abstrak Indonesia & Inggris.</p>
                          <div class="flex flex-wrap gap-2">
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 rounded-full text-xs pulse-glow">
                                  200-300 kata
                              </span>
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-50 text-green-700 rounded-full text-xs pulse-glow">
                                  2 Bahasa
                              </span>
                          </div>
                      </div>

                      <!-- Feature 4 -->
                      <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 shadow-lg hover:border-orange-200/50 animate-on-scroll" style="animation-delay: 0.3s">
                          <div class="flex items-center gap-4 mb-4">
                              <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-orange-600 shadow-sm pulse-glow">
                                  <i class="fas fa-book-open text-lg floating-element"></i>
                              </span>
                              <h4 class="text-base font-bold text-gray-900 break-words">Bab 1</h4>
                          </div>
                          <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 break-words mb-3 stagger-fade-in" style="animation-delay: 0.4s">Verifikasi kelengkapan pendahuluan dan struktur bab pertama.</p>
                          <div class="flex flex-wrap gap-2">
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-50 text-orange-700 rounded-full text-xs pulse-glow">
                                  Latar Belakang
                              </span>
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-orange-50 text-orange-700 rounded-full text-xs pulse-glow">
                                  Rumusan
                              </span>
                          </div>
                      </div>

                      <!-- Feature 5 -->
                      <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 shadow-lg hover:border-red-200/50 animate-on-scroll" style="animation-delay: 0.4s">
                          <div class="flex items-center gap-4 mb-4">
                              <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-red-50 text-red-600 shadow-sm pulse-glow">
                                  <i class="fas fa-quote-right text-lg floating-element"></i>
                              </span>
                              <h4 class="text-base font-bold text-gray-900 break-words">Sitasi APA 7</h4>
                          </div>
                          <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 break-words mb-3 stagger-fade-in" style="animation-delay: 0.5s">Pemeriksaan konsistensi kutipan dan daftar pustaka.</p>
                          <div class="flex flex-wrap gap-2">
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 text-red-700 rounded-full text-xs pulse-glow">
                                  APA 7
                              </span>
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-50 text-red-700 rounded-full text-xs pulse-glow">
                                  Konsistensi
                              </span>
                          </div>
                      </div>

                      <!-- Feature 6 -->
                      <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 shadow-lg hover:border-indigo-200/50 animate-on-scroll" style="animation-delay: 0.5s">
                          <div class="flex items-center gap-4 mb-4">
                              <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600 shadow-sm pulse-glow">
                                  <i class="fas fa-id-card text-lg floating-element"></i>
                              </span>
                              <h4 class="text-base font-bold text-gray-900 break-words">Cover Formal</h4>
                          </div>
                          <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 break-words mb-3 stagger-fade-in" style="animation-delay: 0.6s">Pemeriksaan standar cover ITS, font, dan tata letak.</p>
                          <div class="flex flex-wrap gap-2">
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs pulse-glow">
                                  Biru ITS
                              </span>
                              <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs pulse-glow">
                                  Trebuchet MS
                              </span>
                          </div>
                      </div>
                  </div>

                  <!-- Tombol Aksi -->
                  <div class="mt-8 text-center animate-on-scroll" style="animation-delay: 0.6s">
                      <a @auth href="{{ route('upload.form') }}" @endauth @guest href="{{ route('login.form') }}" @endguest 
                        class="btn-hover bounce-hover inline-flex items-center gap-3 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl break-words pulse-glow">
                          <i class="fas fa-rocket floating-element"></i>
                          Jelajahi Semua Fitur
                      </a>
                      <p class="mt-3 text-xs text-gray-500 break-words stagger-fade-in" style="animation-delay: 0.7s">
                          6 fitur utama untuk pemeriksaan format yang komprehensif
                      </p>
                  </div>
              </div>
          </div>
      </div>
  </section>

  <!-- CARA KERJA -->
  <section id="cara-kerja" class="bg-white/10 safe-area">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid items-start gap-10 lg:grid-cols-2">
        <div class="text-white animate-on-scroll">
          <h2 class="text-2xl xl:text-3xl font-extrabold break-words">Cara Kerja</h2>
          <ol class="mt-6 space-y-6">
            <li class="flex gap-4 stagger-fade-in">
              <div class="h-9 w-9 flex-none rounded-full bg-white text-blue-700 grid place-items-center font-bold pulse-glow floating-element">1</div>
              <div>
                <h3 class="text-base font-semibold break-words">Unggah Dokumen</h3>
                <p class="text-sm text-white/90 break-words">Pilih .docx/.pdf. Sistem mengekstrak teks & metadata format.</p>
              </div>
            </li>
            <li class="flex gap-4 stagger-fade-in" style="animation-delay: 0.1s">
              <div class="h-9 w-9 flex-none rounded-full bg-white text-blue-700 grid place-items-center font-bold pulse-glow floating-element">2</div>
              <div>
                <h3 class="text-base font-semibold break-words">Analisis AI</h3>
                <p class="text-sm text-white/90 break-words">Rule + NLP memeriksa struktur, tipografi, margin, abstrak, Bab 1, sitasi & cover.</p>
              </div>
            </li>
            <li class="flex gap-4 stagger-fade-in" style="animation-delay: 0.2s">
              <div class="h-9 w-9 flex-none rounded-full bg-white text-blue-700 grid place-items-center font-bold pulse-glow floating-element">3</div>
              <div>
                <h3 class="text-base font-semibold break-words">Laporan & Rekomendasi</h3>
                <p class="text-sm text-white/90 break-words">Skor kepatuhan + daftar temuan & saran perbaikan. Dapat diunduh (PDF).</p>
              </div>
            </li>
          </ol>
        </div>
        <div class="animate-on-scroll" style="animation-delay: 0.3s">
          <div id="unggah" class="card-hover bounce-hover rounded-2xl border-2 border-dashed border-white/50 bg-white/90 backdrop-blur p-6 xl:p-8 text-center shadow-xl floating-element">
            <div class="mx-auto max-w-xl">
              <div class="mx-auto grid h-14 w-14 place-items-center rounded-xl bg-blue-50 text-blue-700 pulse-glow">
                <i class="fas fa-cloud-upload-alt text-xl floating-element"></i>
              </div>
              <h3 class="mt-4 text-lg font-semibold text-gray-900 break-words stagger-fade-in">Tarik & Letakkan file Anda</h3>
              <p class="mt-2 text-sm text-gray-600 break-words stagger-fade-in" style="animation-delay: 0.1s">atau klik untuk memilih .docx / .pdf (maks 20MB)</p>
              <div class="mt-5">
                <a
                  @auth href="{{ route('upload.form') }}" @endauth
                  @guest href="{{ route('login.form') }}" @endguest
                  class="btn-hover bounce-hover inline-flex cursor-pointer items-center gap-3 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm break-words pulse-glow">
                  <i class="fas fa-upload floating-element"></i>
                  Pilih / Unggah File
                </a>
              </div>
              <p class="mt-3 text-xs text-gray-500 break-words stagger-fade-in" style="animation-delay: 0.2s">Aman — file tidak dibagikan.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- RINGKASAN PANDUAN -->
  <section id="panduan" class="bg-white/95 backdrop-blur safe-area">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="mx-auto max-w-3xl text-center animate-on-scroll">
        <h2 class="text-2xl xl:text-3xl font-extrabold text-gray-900 break-words gradient-animate">Ringkasan Aturan yang Dicek</h2>
        <p class="mt-3 text-gray-600 break-words stagger-fade-in">Aspek yang divalidasi otomatis sesuai pedoman ITS.</p>
      </div>
      <div class="mt-10 grid gap-6 xl:grid-cols-2">
        <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 animate-on-scroll">
          <h3 class="font-semibold text-gray-900 break-words gradient-animate">Struktur Dokumen</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700 space-y-1">
            <li class="break-words stagger-fade-in">Cover, Abstrak (ID+EN), Daftar Isi</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.1s">Proposal: Bab 1-3 | Laporan: Bab 1-5</li>
          </ul>
          <h3 class="mt-5 font-semibold text-gray-900 break-words gradient-animate">Format Teks</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700 space-y-1">
            <li class="break-words stagger-fade-in" style="animation-delay: 0.2s">Times New Roman 12pt</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.3s">Spasi 1.0; Margin: 3-2.5-3-2 cm</li>
          </ul>
        </div>
        <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 animate-on-scroll" style="animation-delay: 0.2s">
          <h3 class="font-semibold text-gray-900 break-words gradient-animate">Abstrak</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700 space-y-1">
            <li class="break-words stagger-fade-in">200–300 kata</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.1s">Bahasa Indonesia & Inggris</li>
          </ul>
          <h3 class="mt-5 font-semibold text-gray-900 break-words gradient-animate">Bab 1 Pendahuluan</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700 space-y-1">
            <li class="break-words stagger-fade-in" style="animation-delay: 0.2s">Latar belakang, Rumusan masalah, Batasan, Tujuan, Manfaat</li>
          </ul>
          <h3 class="mt-5 font-semibold text-gray-900 break-words gradient-animate">Daftar Pustaka</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700 space-y-1">
            <li class="break-words stagger-fade-in" style="animation-delay: 0.3s">Format APA 7; sitasi konsisten</li>
          </ul>
          <h3 class="mt-5 font-semibold text-gray-900 break-words gradient-animate">Cover & Halaman Judul</h3>
          <ul class="mt-3 list-disc pl-5 text-sm text-gray-700 space-y-1">
            <li class="break-words stagger-fade-in" style="animation-delay: 0.4s">Latar biru ITS, font Trebuchet MS, teks putih</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- CONTOH & SARAN -->
  <section id="contoh" class="bg-white/10 safe-area">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid items-start gap-8 lg:grid-cols-2">
        <div class="card-hover bounce-hover rounded-2xl border border-white/30 bg-white/90 backdrop-blur p-6 shadow-xl animate-on-scroll floating-element">
          <h3 class="text-lg font-semibold text-gray-900 break-words gradient-animate">Ringkasan Kelayakan</h3>
          <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div class="rounded-xl bg-emerald-50 p-4 pulse-glow">
              <p class="text-xs font-medium text-emerald-700 break-words stagger-fade-in">Kepatuhan Umum</p>
              <p class="mt-1 text-2xl font-extrabold text-emerald-700 break-words stagger-fade-in" style="animation-delay: 0.1s">92%</p>
            </div>
            <div class="rounded-xl bg-amber-50 p-4 pulse-glow">
              <p class="text-xs font-medium text-amber-700 break-words stagger-fade-in" style="animation-delay: 0.2s">Item Perlu Revisi</p>
              <p class="mt-1 text-2xl font-extrabold text-amber-700 break-words stagger-fade-in" style="animation-delay: 0.3s">5</p>
            </div>
          </div>
          <ul class="mt-6 space-y-3 text-sm text-gray-700">
            <li class="break-words stagger-fade-in" style="animation-delay: 0.4s">• 2 entri daftar pustaka belum sesuai format APA 7.</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.5s">• Margin kanan 2 cm (seharusnya 2.5 cm).</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.6s">• Abstrak Inggris 310 kata (melewati batas 300).</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.7s">• Bab 1 tidak memuat "Batasan masalah" eksplisit.</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.8s">• Cover belum menggunakan Trebuchet MS untuk judul.</li>
          </ul>
          <a @auth href="{{ route('upload.form') }}" @endauth @guest href="{{ route('login.form') }}" @endguest class="btn-hover bounce-hover mt-6 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white break-words pulse-glow">
            <i class="fas fa-upload floating-element"></i> Cek Dokumen Saya
          </a>
        </div>
        <div class="card-hover bounce-hover rounded-2xl border border-white/30 bg-white/90 backdrop-blur p-6 shadow-xl animate-on-scroll floating-element" style="animation-delay: 0.2s">
          <h3 class="text-lg font-semibold text-gray-900 break-words gradient-animate">Saran Perbaikan</h3>
          <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm text-gray-700">
            <li class="break-words stagger-fade-in">Atur margin kanan menjadi 2.5 cm (Page Setup).</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.1s">Sesuaikan abstrak Inggris ke 200–300 kata.</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.2s">Tambahkan subbab <em>Batasan Masalah</em> pada Bab 1.</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.3s">Perbaiki 2 entri referensi ke gaya APA 7.</li>
            <li class="break-words stagger-fade-in" style="animation-delay: 0.4s">Gunakan Trebuchet MS untuk judul di cover, teks putih di atas biru ITS.</li>
          </ol>
          <p class="mt-4 text-xs text-gray-500 break-words stagger-fade-in" style="animation-delay: 0.5s">*Contoh bersifat ilustratif.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- KOMUNITAS & KOLABORASI -->
  <section class="bg-white/95 backdrop-blur safe-area">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="mx-auto max-w-3xl text-center animate-on-scroll">
        <div class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-green-500 to-emerald-500 px-4 py-2 text-sm text-white font-semibold mb-6 break-words pulse-glow">
          <i class="fas fa-users floating-element"></i>
          Komunitas Aktif
        </div>
        <h2 class="text-2xl xl:text-3xl font-extrabold text-gray-900 break-words gradient-animate">Bergabung dengan 1,000+ Mahasiswa ITS</h2>
        <p class="mt-3 text-gray-600 break-words stagger-fade-in">Raih kemudahan revisi format bersama komunitas yang saling mendukung.</p>
      </div>
      
      <div class="mt-10 grid gap-6 xl:grid-cols-3">
        <!-- Stat 1 -->
        <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-sm animate-on-scroll floating-element">
          <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 pulse-glow">
            <i class="fas fa-file-upload text-blue-600 text-2xl floating-element"></i>
          </div>
          <div class="text-3xl font-bold text-gray-900 break-words gradient-animate">500+</div>
          <div class="text-sm text-gray-600 mt-2 break-words stagger-fade-in">Dokumen Dianalisis</div>
          <p class="text-xs text-gray-500 mt-3 break-words stagger-fade-in" style="animation-delay: 0.1s">Setiap minggu</p>
        </div>

        <!-- Stat 2 -->
        <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-sm animate-on-scroll floating-element" style="animation-delay: 0.2s">
          <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4 pulse-glow">
            <i class="fas fa-graduation-cap text-green-600 text-2xl floating-element"></i>
          </div>
          <div class="text-3xl font-bold text-gray-900 break-words gradient-animate">92%</div>
          <div class="text-sm text-gray-600 mt-2 break-words stagger-fade-in">Skor Rata-rata</div>
          <p class="text-xs text-gray-500 mt-3 break-words stagger-fade-in" style="animation-delay: 0.1s">Setelah menggunakan tool</p>
        </div>

        <!-- Stat 3 -->
        <div class="card-hover bounce-hover rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-sm animate-on-scroll floating-element" style="animation-delay: 0.4s">
          <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4 pulse-glow">
            <i class="fas fa-clock text-purple-600 text-2xl floating-element"></i>
          </div>
          <div class="text-3xl font-bold text-gray-900 break-words gradient-animate">3x</div>
          <div class="text-sm text-gray-600 mt-2 break-words stagger-fade-in">Lebih Cepat</div>
          <p class="text-xs text-gray-500 mt-3 break-words stagger-fade-in" style="animation-delay: 0.1s">Proses revisi format</p>
        </div>
      </div>

      <!-- Program Studi -->
      <div class="mt-12 animate-on-scroll" style="animation-delay: 0.6s">
        <h3 class="text-lg font-semibold text-gray-900 text-center mb-6 break-words gradient-animate">Digunakan oleh Berbagai Program Studi</h3>
        <div class="flex flex-wrap justify-center gap-4">
          <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-full text-sm font-medium break-words pulse-glow stagger-fade-in">Teknik Informatika</span>
          <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-medium break-words pulse-glow stagger-fade-in" style="animation-delay: 0.1s">Sistem Informasi</span>
          <span class="px-4 py-2 bg-purple-100 text-purple-800 rounded-full text-sm font-medium break-words pulse-glow stagger-fade-in" style="animation-delay: 0.2s">Teknik Elektro</span>
          <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium break-words pulse-glow stagger-fade-in" style="animation-delay: 0.3s">Teknik Mesin</span>
          <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full text-sm font-medium break-words pulse-glow stagger-fade-in" style="animation-delay: 0.4s">Teknik Sipil</span>
          <span class="px-4 py-2 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium break-words pulse-glow stagger-fade-in" style="animation-delay: 0.5s">Arsitektur</span>
        </div>
      </div>
    </div>
  </section>
  
  <!-- CTA AKHIR -->
  <section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-blue-600 to-purple-600"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 safe-area">
      <div class="grid items-center gap-8 lg:grid-cols-2">
        <div class="animate-on-scroll">
          <h2 class="text-2xl xl:text-3xl font-extrabold text-white break-words">Siap cek format Tugas Akhir Anda?</h2>
          <p class="mt-2 text-white/80 break-words stagger-fade-in">Unggah naskah dan terima laporan lengkap.</p>
        </div>
        <div class="text-left lg:text-right animate-on-scroll" style="animation-delay: 0.2s">
          <a @auth href="{{ route('upload.form') }}" @endauth @guest href="{{ route('login.form') }}" @endguest class="btn-hover bounce-hover inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-50 break-words pulse-glow">
            Mulai Pemeriksaan <i class="fas fa-arrow-right ml-1 floating-element"></i>
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- FOOTER - Diperbarui untuk konsistensi -->
  <footer class="bg-gray-800 text-white py-8 safe-area">
    <div class="max-w-7xl mx-auto px-4">
      <div class="flex flex-col xl:flex-row justify-between items-center">
        <div class="mb-6 xl:mb-0 text-center xl:text-left">
          <div class="flex items-center justify-center xl:justify-start">
            <a href="{{ url('/') }}" class="flex items-center">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
              </div>
              <span class="text-xl font-bold break-words">TAkCekIn ITS</span>
            </a>
          </div>
          <p class="text-gray-400 text-sm mt-2 break-words">Sistem Deteksi Kelengkapan Format Tugas Akhir</p>
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
        <p class="text-gray-400 text-sm break-words">
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

    // Dark Mode Toggle System
    document.addEventListener('DOMContentLoaded', function() {
      // Cek preferensi dark mode di localStorage
      const isDarkMode = localStorage.getItem('darkMode') === 'true';
      const darkModeToggle = document.getElementById('darkModeToggle');
      const darkModeIcon = document.getElementById('darkModeIcon');
      
      // Fungsi untuk mengaktifkan dark mode
      function enableDarkMode() {
        document.documentElement.classList.add('dark-mode');
        darkModeIcon.classList.remove('fa-moon');
        darkModeIcon.classList.add('fa-sun');
        localStorage.setItem('darkMode', 'true');
        console.log('Dark mode enabled');
      }
      
      // Fungsi untuk menonaktifkan dark mode
      function disableDarkMode() {
        document.documentElement.classList.remove('dark-mode');
        darkModeIcon.classList.remove('fa-sun');
        darkModeIcon.classList.add('fa-moon');
        localStorage.setItem('darkMode', 'false');
        console.log('Dark mode disabled');
      }
      
      // Toggle dark mode
      function toggleDarkMode() {
        if (document.documentElement.classList.contains('dark-mode')) {
          disableDarkMode();
        } else {
          enableDarkMode();
        }
      }
      
      // Inisialisasi dark mode berdasarkan preferensi yang disimpan
      if (isDarkMode) {
        enableDarkMode();
      }
      
      // Event listener untuk tombol toggle
      if (darkModeToggle) {
        darkModeToggle.addEventListener('click', toggleDarkMode);
      }
      
      // Optional: Tambahkan hotkey (Ctrl+Shift+D) untuk toggle dark mode
      document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.shiftKey && e.key === 'D') {
          e.preventDefault();
          toggleDarkMode();
          
          // Tampilkan notifikasi
          showDarkModeNotification();
        }
      });
      
      // Fungsi untuk menampilkan notifikasi dark mode
      function showDarkModeNotification() {
        const isDark = document.documentElement.classList.contains('dark-mode');
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg flex items-center animate-fade-in-up';
        notification.style.animationDuration = '0.3s';
        
        if (isDark) {
          notification.innerHTML = `
            <div class="bg-gray-800 text-white px-4 py-3 rounded-lg shadow-lg flex items-center">
              <i class="fas fa-sun text-yellow-300 mr-2"></i>
              <span>Dark Mode Aktif</span>
            </div>
          `;
        } else {
          notification.innerHTML = `
            <div class="bg-white text-gray-800 px-4 py-3 rounded-lg shadow-lg flex items-center">
              <i class="fas fa-moon text-blue-500 mr-2"></i>
              <span>Light Mode Aktif</span>
            </div>
          `;
        }
        
        document.body.appendChild(notification);
        
        // Hapus notifikasi setelah 3 detik
        setTimeout(() => {
          notification.style.opacity = '0';
          notification.style.transform = 'translateY(-10px)';
          notification.style.transition = 'all 0.3s ease';
          
          setTimeout(() => {
            document.body.removeChild(notification);
          }, 300);
        }, 3000);
      }
      
      // Fungsi untuk mendeteksi preferensi sistem
      function detectSystemPreference() {
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
          // Jika user belum pernah menyetel preferensi, gunakan preferensi sistem
          if (localStorage.getItem('darkMode') === null) {
            enableDarkMode();
          }
        }
      }
      
      // Deteksi preferensi sistem saat pertama kali load
      detectSystemPreference();
      
      // Listen untuk perubahan preferensi sistem
      window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', detectSystemPreference);
    });

    // Scroll animation observer
    document.addEventListener('DOMContentLoaded', function() {
      const animateElements = document.querySelectorAll('.animate-on-scroll');
      
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            
            // Add staggered animation to child elements
            const staggerElements = entry.target.querySelectorAll('.stagger-fade-in');
            staggerElements.forEach((el, index) => {
              el.style.animationDelay = `${index * 0.1}s`;
              el.classList.add('stagger-fade-in');
            });
          }
        });
      }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      });
      
      animateElements.forEach(el => observer.observe(el));
      
      // Apply staggered animation to initial elements
      document.querySelectorAll('.stagger-fade-in').forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
      });
    });

    // Add hover sound effects (optional)
    document.addEventListener('DOMContentLoaded', function() {
      const buttons = document.querySelectorAll('.bounce-hover, .btn-hover');
      
      buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
          this.style.transform = 'scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
          this.style.transform = 'scale(1)';
        });
      });
    });

    // Single Typewriter Animation
    function startTypewriter() {
      const typewriterElement = document.getElementById('typewriter');
      const fullText = typewriterElement.innerHTML;
      
      // Reset
      typewriterElement.innerHTML = '';
      typewriterElement.style.position = 'relative';
      
      // Buat span untuk teks yang akan diketik
      const textSpan = document.createElement('span');
      textSpan.id = 'typewriter-text';
      textSpan.style.display = 'inline-block';
      textSpan.style.width = '0';
      textSpan.style.overflow = 'hidden';
      textSpan.style.whiteSpace = 'nowrap';
      textSpan.style.verticalAlign = 'bottom';
      
      // Mulai animasi
      let currentChar = 0;
      const speed = 60; // ms per karakter
      
      function typeCharacter() {
        if (currentChar < fullText.length) {
          // Ambil karakter berikutnya
          const nextChar = fullText.charAt(currentChar);
          
          // Handle span khusus (gradient-animate)
          if (nextChar === '<') {
            // Cari tag penutup
            const closingTagIndex = fullText.indexOf('>', currentChar);
            if (closingTagIndex !== -1) {
              const htmlSegment = fullText.substring(currentChar, closingTagIndex + 1);
              textSpan.innerHTML += htmlSegment;
              currentChar = closingTagIndex + 1;
            }
          } else {
            textSpan.innerHTML += nextChar;
            currentChar++;
          }
          
          // Update lebar untuk animasi typing
          textSpan.style.width = 'auto';
          
          setTimeout(typeCharacter, speed);
        } else {
          // Animasi selesai, hilangkan kursor setelah beberapa detik
          setTimeout(() => {
            cursorSpan.style.opacity = '0';
            cursorSpan.style.transition = 'opacity 0.5s ease';
          }, 2000);
        }
      }
      
      // Mulai mengetik setelah delay kecil
      setTimeout(typeCharacter, 500);
    }
  </script>

  <!-- Dark Mode Toggle Button -->
  <button id="darkModeToggle" class="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-white shadow-lg hover:shadow-xl border border-gray-200 flex items-center justify-center transition-all duration-300 hover:scale-110 print-hidden">
    <i id="darkModeIcon" class="fas fa-moon text-gray-700 text-lg"></i>
  </button>
</body>
</html>
