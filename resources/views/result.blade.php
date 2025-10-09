<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Analisis - FormatCheck ITS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#4f46e5',
            secondary: '#7c73d9',
            success: '#10b981',
            warning: '#f59e0b',
            danger: '#ef4444',
            itsblue: '#0067ac'
          }
        }
      }
    }
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }
    
    .score-circle {
      transition: all 0.5s ease;
    }
    
    .result-card {
      transition: transform 0.3s ease;
    }
    
    .result-card:hover {
      transform: translateY(-5px);
    }
    
    .progress-bar {
      transition: width 1s ease-in-out;
    }
    
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
      animation: fadeIn 0.8s ease-out forwards;
    }
    
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    .delay-400 { animation-delay: 0.4s; }
    
    /* Custom styles for circular score */
    .circle-bg {
      fill: none;
      stroke: #eee;
      stroke-width: 3.8;
    }
    
    .circle {
      fill: none;
      stroke-width: 3.8;
      stroke-linecap: round;
      transform: rotate(-90deg);
      transform-origin: 50% 50%;
      transition: stroke-dasharray 1s ease-in-out;
    }

    .print-hidden {
      display: block;
    }

    /* Navbar styles */
    .navbar {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
    }

    @media print {
      .print-hidden {
        display: none !important;
      }
      
      body {
        background: white !important;
      }
      
      .bg-gradient-to-r {
        background: #0067ac !important;
      }
    }
  </style>
</head>
<body class="flex flex-col min-h-screen">
  <!-- Navbar -->
  <nav class="navbar shadow-lg border-b border-gray-200 print-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
              <i class="fas fa-graduation-cap text-white text-lg"></i>
            </div>
            <span class="ml-3 text-xl font-bold text-gray-800">FormatCheck ITS</span>
          </div>
          
          <!-- Navigation Links -->
          <div class="hidden md:ml-6 md:flex md:space-x-8">
            <a href="{{ route('upload.form') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
              <i class="fas fa-upload mr-2"></i>
              Upload TA
            </a>
            <a href="{{ route('history') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
              <i class="fas fa-history mr-2"></i>
              Riwayat
            </a>
            <a href="{{ route('results', ['filename' => $filename ?? '']) }}" class="border-b-2 border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
              <i class="fas fa-chart-bar mr-2"></i>
              Hasil Analisis
            </a>
          </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-700">
            <i class="fas fa-file-pdf mr-1"></i>
            {{ $filename ?? 'Dokumen' }}
          </span>
        </div>
        <div class="flex items-center">
          <div class="flex items-center space-x-4">
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
            @else
              <a href="{{ route('login.form') }}" class="text-sm text-gray-700 hover:text-blue-600 transition">
                <i class="fas fa-sign-in-alt mr-1"></i>Login
              </a>
            @endauth
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden">
      <div class="pt-2 pb-3 space-y-1 bg-white">
        <a href="{{ route('upload.form') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
          <i class="fas fa-upload mr-2"></i>
          Upload TA
        </a>
        <a href="{{ route('history') }}" class="border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
          <i class="fas fa-history mr-2"></i>
          Riwayat
        </a>
        <a href="{{ route('results', ['filename' => $filename ?? '']) }}" class="bg-blue-50 border-blue-500 text-blue-700 block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
          <i class="fas fa-chart-bar mr-2"></i>
          Hasil Analisis
        </a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="flex-grow flex items-center justify-center p-4 py-8">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-6xl">
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white text-center">
        <h1 class="text-3xl font-bold mb-2">Hasil Analisis Format Tugas Akhir ITS</h1>
        <p class="text-blue-100">Dokumen: <span class="font-medium" id="filename">{{ $filename ?? 'Nama File' }}</span></p>
        <p class="text-blue-100 text-sm mt-1">Dianalisis pada: {{ date('d F Y H:i') }}</p>
      </div>
      
      <div class="p-8">
        @if(isset($results) && is_array($results))
        <!-- Score Summary -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-10 p-6 bg-blue-50 rounded-xl border border-blue-200">
          <div class="flex items-center mb-4 md:mb-0">
            <div class="relative w-24 h-24 mr-4">
              <svg class="w-full h-full" viewBox="0 0 36 36">
                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#eee" stroke-width="3.8"/>
                <path class="circle" stroke="@if(($results['overall_score'] ?? 0) >= 8) #10b981 @elseif(($results['overall_score'] ?? 0) >= 6) #f59e0b @else #ef4444 @endif" 
                    stroke-dasharray="{{ ($results['overall_score'] ?? 0) * 10 }}, 100" 
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                    fill="none" stroke-width="3.8"/>
                <text x="18" y="22" text-anchor="middle" fill="@if(($results['overall_score'] ?? 0) >= 8) #10b981 @elseif(($results['overall_score'] ?? 0) >= 6) #f59e0b @else #ef4444 @endif" 
                    font-size="10" font-weight="bold">{{ $results['overall_score'] ?? 0 }}/10</text>
              </svg>
            </div>
            <div>
              <h2 class="text-2xl font-bold text-gray-800">Skor Kelengkapan Format ITS</h2>
              <p class="text-gray-600">Dokumen Anda memenuhi {{ number_format(($results['overall_score'] ?? 0) * 10, 1) }}% persyaratan format ITS</p>
              <p class="text-sm text-gray-500">{{ $results['document_type'] ?? 'Tipe Dokumen' }} - {{ $results['metadata']['title'] ?? 'Judul tidak ditemukan' }}</p>
            </div>
          </div>
          <div id="status-label" class="px-4 py-2 rounded-full font-semibold 
              @if(($results['overall_score'] ?? 0) >= 8) bg-green-100 text-green-800
              @elseif(($results['overall_score'] ?? 0) >= 6) bg-yellow-100 text-yellow-800
              @else bg-red-100 text-red-800 @endif">
            <i class="fas @if(($results['overall_score'] ?? 0) >= 8) fa-check-circle 
                @elseif(($results['overall_score'] ?? 0) >= 6) fa-exclamation-triangle 
                @else fa-times-circle @endif mr-2"></i>
            @if(($results['overall_score'] ?? 0) >= 8) LAYAK DIAJUKAN
            @elseif(($results['overall_score'] ?? 0) >= 6) PERLU PERBAIKAN
            @else TIDAK LAYAK @endif
          </div>
        </div>

        <!-- Detail Analysis -->
        <h2 class="text-xl font-bold text-gray-800 mb-6">Detail Analisis Format ITS</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10" id="results-container">
          <!-- Abstract Card -->
          <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
              @if(($results['abstract']['status'] ?? '') === 'success') border-green-200
              @elseif(($results['abstract']['status'] ?? '') === 'warning') border-yellow-200
              @else border-red-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if(($results['abstract']['status'] ?? '') === 'success') bg-green-100 text-green-500
                  @elseif(($results['abstract']['status'] ?? '') === 'warning') bg-yellow-100 text-yellow-500
                  @else bg-red-100 text-red-500 @endif">
                <i class="fas @if(($results['abstract']['status'] ?? '') === 'success') fa-check
                    @elseif(($results['abstract']['status'] ?? '') === 'warning') fa-exclamation-triangle
                    @else fa-times @endif"></i>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800">Abstrak</h3>
                <p class="text-sm text-gray-600">
                  @if($results['abstract']['found'] ?? false)
                  ID: {{ $results['abstract']['id_word_count'] ?? 0 }} kata, 
                  EN: {{ $results['abstract']['en_word_count'] ?? 0 }} kata
                  @else
                  Tidak ditemukan
                  @endif
                </p>
              </div>
            </div>
            <div class="bg-gray-100 rounded-full h-2.5 mb-2">
              <div class="h-2.5 rounded-full 
                  @if(($results['abstract']['status'] ?? '') === 'success') bg-green-500
                  @elseif(($results['abstract']['status'] ?? '') === 'warning') bg-yellow-500
                  @else bg-red-500 @endif" 
                  style="width: @if($results['abstract']['found'] ?? false) 100% @else 0% @endif">
              </div>
            </div>
            <p class="text-sm 
                @if(($results['abstract']['status'] ?? '') === 'success') text-green-600
                @elseif(($results['abstract']['status'] ?? '') === 'warning') text-yellow-600
                @else text-red-600 @endif font-medium">
              {{ $results['abstract']['message'] ?? 'Abstrak tidak ditemukan' }}
            </p>
          </div>

          <!-- Format Teks Card -->
          <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
              @if(($results['format']['status'] ?? '') === 'success') border-green-200
              @elseif(($results['format']['status'] ?? '') === 'warning') border-yellow-200
              @else border-red-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if(($results['format']['status'] ?? '') === 'success') bg-green-100 text-green-500
                  @elseif(($results['format']['status'] ?? '') === 'warning') bg-yellow-100 text-yellow-500
                  @else bg-red-100 text-red-500 @endif">
                <i class="fas @if(($results['format']['status'] ?? '') === 'success') fa-check
                    @elseif(($results['format']['status'] ?? '') === 'warning') fa-exclamation-triangle
                    @else fa-times @endif"></i>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800">Format Teks</h3>
                <p class="text-sm text-gray-600">
                  Font: {{ $results['format']['font_family'] ?? 'Times New Roman' }}, 
                  Spasi: {{ $results['format']['line_spacing'] ?? '1' }}
                </p>
              </div>
            </div>
            <p class="text-sm 
                @if(($results['format']['status'] ?? '') === 'success') text-green-600
                @elseif(($results['format']['status'] ?? '') === 'warning') text-yellow-600
                @else text-red-600 @endif font-medium">
              {{ $results['format']['message'] ?? 'Format teks tidak terdeteksi' }}
            </p>
          </div>

          <!-- Margin Card -->
          <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
              @if(($results['margin']['status'] ?? '') === 'success') border-green-200
              @elseif(($results['margin']['status'] ?? '') === 'warning') border-yellow-200
              @else border-red-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if(($results['margin']['status'] ?? '') === 'success') bg-green-100 text-green-500
                  @elseif(($results['margin']['status'] ?? '') === 'warning') bg-yellow-100 text-yellow-500
                  @else bg-red-100 text-red-500 @endif">
                <i class="fas @if(($results['margin']['status'] ?? '') === 'success') fa-check
                    @elseif(($results['margin']['status'] ?? '') === 'warning') fa-exclamation-triangle
                    @else fa-times @endif"></i>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800">Margin</h3>
                <p class="text-sm text-gray-600">
                  Atas: {{ $results['margin']['top'] ?? '3.0' }}cm, 
                  Bawah: {{ $results['margin']['bottom'] ?? '2.5' }}cm
                </p>
              </div>
            </div>
            <p class="text-sm 
                @if(($results['margin']['status'] ?? '') === 'success') text-green-600
                @elseif(($results['margin']['status'] ?? '') === 'warning') text-yellow-600
                @else text-red-600 @endif font-medium">
              {{ $results['margin']['message'] ?? 'Margin tidak terdeteksi' }}
            </p>
          </div>

          <!-- Struktur Bab Card -->
          <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
              @if(($results['chapters']['status'] ?? '') === 'success') border-green-200
              @elseif(($results['chapters']['status'] ?? '') === 'warning') border-yellow-200
              @else border-red-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if(($results['chapters']['status'] ?? '') === 'success') bg-green-100 text-green-500
                  @elseif(($results['chapters']['status'] ?? '') === 'warning') bg-yellow-100 text-yellow-500
                  @else bg-red-100 text-red-500 @endif">
                <i class="fas @if(($results['chapters']['status'] ?? '') === 'success') fa-check
                    @elseif(($results['chapters']['status'] ?? '') === 'warning') fa-exclamation-triangle
                    @else fa-times @endif"></i>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800">Struktur Bab</h3>
                <p class="text-sm text-gray-600">
                  Bab 1: @if($results['chapters']['bab1'] ?? false) ✓ @else ✗ @endif
                  Bab 2: @if($results['chapters']['bab2'] ?? false) ✓ @else ✗ @endif
                  Bab 3: @if($results['chapters']['bab3'] ?? false) ✓ @else ✗ @endif
                  @if(isset($results['chapters']['bab4']))
                  Bab 4: @if($results['chapters']['bab4']) ✓ @else ✗ @endif
                  Bab 5: @if($results['chapters']['bab5']) ✓ @else ✗ @endif
                  @endif
                </p>
              </div>
            </div>
            <p class="text-sm 
                @if(($results['chapters']['status'] ?? '') === 'success') text-green-600
                @elseif(($results['chapters']['status'] ?? '') === 'warning') text-yellow-600
                @else text-red-600 @endif font-medium">
              {{ $results['chapters']['message'] ?? 'Struktur bab tidak terdeteksi' }}
            </p>
          </div>

          <!-- Daftar Pustaka Card -->
          <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
              @if(($results['references']['status'] ?? '') === 'success') border-green-200
              @elseif(($results['references']['status'] ?? '') === 'warning') border-yellow-200
              @else border-red-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if(($results['references']['status'] ?? '') === 'success') bg-green-100 text-green-500
                  @elseif(($results['references']['status'] ?? '') === 'warning') bg-yellow-100 text-yellow-500
                  @else bg-red-100 text-red-500 @endif">
                <i class="fas @if(($results['references']['status'] ?? '') === 'success') fa-check
                    @elseif(($results['references']['status'] ?? '') === 'warning') fa-exclamation-triangle
                    @else fa-times @endif"></i>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800">Daftar Pustaka</h3>
                <p class="text-sm text-gray-600">
                  {{ $results['references']['count'] ?? 0 }} referensi
                  @if($results['references']['apa_compliant'] ?? false)
                  (Format APA ✓)
                  @else
                  (Format APA ✗)
                  @endif
                </p>
              </div>
            </div>
            <div class="bg-gray-100 rounded-full h-2.5 mb-2">
              <div class="h-2.5 rounded-full 
                  @if(($results['references']['status'] ?? '') === 'success') bg-green-500
                  @elseif(($results['references']['status'] ?? '') === 'warning') bg-yellow-500
                  @else bg-red-500 @endif" 
                  style="width: {{ min(100, (($results['references']['count'] ?? 0) / ($results['references']['min_references'] ?? 1)) * 100) }}%">
              </div>
            </div>
            <p class="text-sm 
                @if(($results['references']['status'] ?? '') === 'success') text-green-600
                @elseif(($results['references']['status'] ?? '') === 'warning') text-yellow-600
                @else text-red-600 @endif font-medium">
              {{ $results['references']['message'] ?? 'Daftar pustaka tidak ditemukan' }}
            </p>
          </div>

          <!-- Cover & Halaman Formal Card -->
          <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
              @if(($results['cover']['status'] ?? '') === 'success') border-green-200
              @elseif(($results['cover']['status'] ?? '') === 'warning') border-yellow-200
              @else border-red-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if(($results['cover']['status'] ?? '') === 'success') bg-green-100 text-green-500
                  @elseif(($results['cover']['status'] ?? '') === 'warning') bg-yellow-100 text-yellow-500
                  @else bg-red-100 text-red-500 @endif">
                <i class="fas @if(($results['cover']['status'] ?? '') === 'success') fa-check
                    @elseif(($results['cover']['status'] ?? '') === 'warning') fa-exclamation-triangle
                    @else fa-times @endif"></i>
              </div>
              <div>
                <h3 class="font-semibold text-gray-800">Cover & Halaman Formal</h3>
                <p class="text-sm text-gray-600">
                  @if($results['cover']['found'] ?? false)
                  Cover, Pengesahan, Orisinalitas
                  @else
                  Tidak lengkap
                  @endif
                </p>
              </div>
            </div>
            <p class="text-sm 
                @if(($results['cover']['status'] ?? '') === 'success') text-green-600
                @elseif(($results['cover']['status'] ?? '') === 'warning') text-yellow-600
                @else text-red-600 @endif font-medium">
              {{ $results['cover']['message'] ?? 'Halaman formal tidak terdeteksi' }}
            </p>
          </div>
        </div>

        <!-- Rekomendasi Perbaikan -->
        @if(isset($results['recommendations']) && count($results['recommendations']) > 0)
        <div class="mb-8 p-6 bg-yellow-50 rounded-xl border border-yellow-200">
          <h3 class="text-lg font-semibold text-yellow-800 mb-3 flex items-center">
            <i class="fas fa-lightbulb mr-2"></i> Rekomendasi Perbaikan Format ITS
          </h3>
          <ul class="list-disc list-inside space-y-2 text-yellow-700">
            @foreach($results['recommendations'] as $recommendation)
            <li>{{ $recommendation }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <!-- Metadata Dokumen -->
        <div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-info-circle mr-2"></i> Informasi Dokumen
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Jenis Dokumen:</span>
              <span class="font-medium">{{ $results['document_type'] ?? 'Tidak Diketahui' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Total Halaman:</span>
              <span class="font-medium">{{ $results['metadata']['page_count'] ?? 'Tidak Diketahui' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Ukuran File:</span>
              <span class="font-medium">{{ $results['metadata']['file_size'] ?? 'Tidak Diketahui' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Format File:</span>
              <span class="font-medium">{{ $results['metadata']['file_format'] ?? 'Tidak Diketahui' }}</span>
            </div>
          </div>
        </div>
        @else
        <!-- Fallback jika tidak ada results -->
        <div class="text-center py-8">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2">Data Hasil Tidak Tersedia</h3>
          <p class="text-gray-600 mb-4">Tidak dapat memuat hasil analisis. Silakan coba upload ulang dokumen.</p>
          <a href="{{ route('upload.form') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition-all">
            <i class="fas fa-upload mr-2"></i> Upload Ulang
          </a>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 print-hidden">
          <a href="{{ route('upload.form') }}" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white text-center font-semibold py-3 px-4 rounded-lg transition-all">
            <i class="fas fa-upload mr-2"></i> Analisis File Lain
          </a>
          <button onclick="saveResults()" class="flex-1 bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 font-semibold py-3 px-4 rounded-lg transition-all">
            <i class="fas fa-download mr-2"></i> Simpan Hasil
          </button>
          <button onclick="window.print()" class="flex-1 bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 px-4 rounded-lg transition-all">
            <i class="fas fa-print mr-2"></i> Cetak Laporan
          </button>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-6 mt-8 print-hidden">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-4 md:mb-0">
          <div class="flex items-center justify-center md:justify-start">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
              <i class="fas fa-graduation-cap text-white"></i>
            </div>
            <span class="text-lg font-bold">FormatCheck ITS</span>
          </div>
          <p class="text-gray-400 text-sm mt-2">Sistem Deteksi Kelengkapan Format Tugas Akhir</p>
        </div>
        
        <div class="flex space-x-6">
          <a href="{{ route('upload.form') }}" class="text-gray-300 hover:text-white transition" title="Upload Baru">
            <i class="fas fa-upload"></i>
          </a>
          <a href="{{ route('history') }}" class="text-gray-300 hover:text-white transition" title="Riwayat">
            <i class="fas fa-history"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-white transition" title="Bantuan">
            <i class="fas fa-question-circle"></i>
          </a>
        </div>
      </div>
      
      <div class="border-t border-gray-700 mt-4 pt-4">
        <p class="text-gray-400 text-sm">
          © 2025 Sistem Deteksi Kelengkapan Format Tugas Akhir - ITS. All rights reserved.
        </p>
      </div>
    </div>
  </footer>

  <script>
    // Fungsi untuk menyimpan hasil
    function saveResults() {
      const button = event.target;
      const originalText = button.innerHTML;
      
      button.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i> Menyimpan...';
      button.disabled = true;
      
      setTimeout(() => {
        // Create a blob with the results
        const resultsData = {
          filename: "{{ $filename ?? 'Nama File' }}",
          analysisDate: new Date().toLocaleString('id-ID'),
          overallScore: {{ $results['overall_score'] ?? 0 }},
          details: @json($results ?? [])
        };
        
        const blob = new Blob([JSON.stringify(resultsData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `hasil-analisis-{{ $filename ?? 'dokumen' }}-${new Date().getTime()}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        button.innerHTML = '<i class="fas fa-check mr-2"></i> Tersimpan!';
        setTimeout(() => {
          button.innerHTML = originalText;
          button.disabled = false;
        }, 2000);
      }, 1500);
    }

    // Animasi progress bars saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(() => {
        const progressBars = document.querySelectorAll('.progress-bar');
        progressBars.forEach(bar => {
          const width = bar.style.width;
          bar.style.width = '0';
          setTimeout(() => {
            bar.style.width = width;
          }, 100);
        });
      }, 500);
    });

    // Print optimization
    window.addEventListener('beforeprint', function() {
      document.body.classList.add('printing');
    });

    window.addEventListener('afterprint', function() {
      document.body.classList.remove('printing');
    });
  </script>
</body>
</html>