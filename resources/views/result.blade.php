<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Analisis - FormatCheck ITS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"></script>
  <link rel="stylesheet" href="{{ asset('css/formatcheck-its.css') }}">
  <link rel="stylesheet" href="css/formatcheck-its.css">
  <link rel="stylesheet" href="{{ asset('css/dark-its.css') }}">
  <link rel="stylesheet" href="css/dark-its.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex flex-col min-h-screen no-horizontal-scroll">
  <!-- Navbar (desktop + mobile toggle) -->
  <nav class="navbar shadow-lg border-b border-gray-200/50 sticky top-0 z-50 print-hidden will-change-transform">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 safe-area">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
              <i class="fas fa-graduation-cap text-white text-lg"></i>
            </div>
            <a href="{{ url('/') }}" class="flex items-center ml-3">
              <span class="text-xl font-bold text-gray-800 break-words">FormatCheck ITS</span>
            </a>
          </div>

          <!-- Navigation Links (desktop) -->
          <div class="hidden md:ml-8 md:flex md:space-x-6">
            <a href="{{ route('upload.form') }}" class="nav-link border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium break-words">
              <i class="fas fa-upload mr-2 text-blue-500"></i>
              Upload TA
            </a>
            <a href="{{ route('history') }}" class="nav-link border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium break-words">
              <i class="fas fa-history mr-2 text-purple-500"></i>
              Riwayat
            </a>
            <a href="{{ route('results', ['filename' => $filename ?? '']) }}" class="nav-link border-b-2 border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium break-words">
              <i class="fas fa-chart-bar mr-2 text-orange-500"></i>
              Hasil Analisis
            </a>
          </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-3">
          <div class="hidden md:flex items-center space-x-4">
            <div class="flex items-center space-x-4">
              @auth
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="text-sm text-gray-700 hover:text-blue-600 transition break-words">
                    <i class="fas fa-sign-out-alt mr-1"></i>Logout
                  </button>
                </form>
              @else
                <a href="{{ route('login.form') }}" class="text-sm text-gray-700 hover:text-blue-600 transition break-words">
                  <i class="fas fa-sign-in-alt mr-1"></i>Login
                </a>
              @endauth
            </div>
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

    <!-- Mobile menu (hidden by default, toggled) -->
    <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200 bg-white/95 backdrop-blur-lg">
      <div class="pt-2 pb-4 space-y-1">
        <a href="{{ route('upload.form') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words">
          <i class="fas fa-upload mr-3 text-blue-500"></i>Upload TA
        </a>
        <a href="{{ route('history') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words">
          <i class="fas fa-history mr-3 text-purple-500"></i>Riwayat
        </a>

        <div class="border-t border-gray-200 pt-2 mt-2">
          @auth
            <div class="px-4 py-2 text-sm text-gray-600 break-words">
              <i class="fas fa-user-circle mr-2"></i>
              {{ Auth::user()->name }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit" class="w-full text-left nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words">
                <i class="fas fa-sign-out-alt mr-3"></i>Logout
              </button>
            </form>
          @endauth
          @guest
            <a href="{{ route('login.form') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words">
              <i class="fas fa-right-to-bracket mr-3"></i>Login
            </a>
          @endguest
        </div>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="flex-grow flex items-center justify-center p-4 py-8 safe-area">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-6xl">
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white text-center">
        <h1 class="text-2xl md:text-3xl font-bold mb-2 break-words text-balance">Hasil Analisis Format Tugas Akhir ITS</h1>
        <p class="text-blue-100 break-words">Dokumen: <span class="font-medium filename-truncate">{{ $filename ?? 'Nama File' }}</span></p>
        <p class="text-blue-100 text-sm mt-1 break-words">Dianalisis pada: {{ date('d F Y H:i') }}</p>
      </div>
      
      <div class="p-6 md:p-8">
        @if(isset($results) && is_array($results))
        <!-- Score Summary -->
        <div class="card-hover flex flex-col md:flex-row items-center justify-between mb-8 md:mb-10 p-6 bg-blue-50 rounded-xl border border-blue-200">
          <div class="flex items-center mb-4 md:mb-0">
            <div class="relative w-20 h-20 md:w-24 md:h-24 mr-4">
              <svg class="w-full h-full" viewBox="0 0 36 36">
                <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#eee" stroke-width="3.8"/>
                <path class="circle" 
                    stroke="@if(($results['score'] ?? 0) >= 8) #10b981 @elseif(($results['score'] ?? 0) >= 6) #f59e0b @else #ef4444 @endif" 
                    stroke-dasharray="{{ $results['percentage'] ?? 0 }}, 100" 
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                    fill="none" stroke-width="3.8"/>
                <text x="18" y="22" text-anchor="middle" 
                    fill="@if(($results['score'] ?? 0) >= 8) #10b981 @elseif(($results['score'] ?? 0) >= 6) #f59e0b @else #ef4444 @endif" 
                    font-size="10" font-weight="bold">{{ $results['score'] ?? 0 }}/10</text>
              </svg>
            </div>
            <div class="min-w-0">
              <h2 class="text-xl md:text-2xl font-bold text-gray-800 break-words text-balance">Skor Kelengkapan Format ITS</h2>
              <p class="text-gray-600 break-words">Dokumen Anda memenuhi {{ $results['percentage'] ?? 0 }}% persyaratan format ITS</p>
              <p class="text-sm text-gray-500 break-words">{{ $results['document_info']['jenis_dokumen'] ?? 'Tipe Dokumen' }}</p>
            </div>
          </div>
          <div class="px-4 py-2 rounded-full font-semibold break-words 
              @if(($results['status'] ?? '') === 'LAYAK') bg-green-100 text-green-800
              @elseif(($results['status'] ?? '') === 'PERLU PERBAIKAN') bg-yellow-100 text-yellow-800
              @else bg-red-100 text-red-800 @endif">
            <i class="fas @if(($results['status'] ?? '') === 'LAYAK') fa-check-circle 
                @elseif(($results['status'] ?? '') === 'PERLU PERBAIKAN') fa-exclamation-triangle 
                @else fa-times-circle @endif mr-2"></i>
            {{ $results['status'] ?? 'TIDAK DIKETAHUI' }}
          </div>
        </div>

        <!-- Lokasi Konten dalam PDF -->
        @php $locations = $results['locations'] ?? []; @endphp
        @if(!empty($locations))
        <div class="mb-8 md:mb-10">
          <h2 class="text-xl font-bold text-gray-800 mb-4 break-words">Lokasi Konten pada PDF</h2>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <!-- Lokasi Konten Terdeteksi -->
            <div class="bg-white rounded-xl border border-blue-200 p-4 md:p-5 shadow-sm">
              <h3 class="font-semibold text-gray-800 mb-3">Bagian Terdeteksi</h3>
              <div class="space-y-3">
                @if(isset($locations['abstrak']) && $locations['abstrak'])
                  <button 
                    onclick="navigateToPDFPage({{ $locations['abstrak']['page'] }})" 
                    class="w-full text-left p-3 bg-blue-50 rounded-lg border border-blue-100 hover:bg-blue-100 hover:border-blue-200 transition cursor-pointer group requirement-item">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center flex-1 min-w-0">
                        <i class="fas fa-file-alt text-blue-500 mr-2 flex-shrink-0"></i>
                        <span class="font-medium text-gray-800">Abstrak</span>
                        <span class="ml-2 text-sm text-gray-500">Hal. {{ $locations['abstrak']['page'] }}</span>
                      </div>
                      <i class="fas fa-chevron-right text-blue-400 opacity-0 group-hover:opacity-100 transition ml-2 flex-shrink-0"></i>
                    </div>
                    @if(!empty($locations['abstrak']['snippet']))
                      <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $locations['abstrak']['snippet'] }}</p>
                    @endif
                  </button>
                @endif

                @if(isset($locations['bab']) && is_array($locations['bab']) && count($locations['bab']) > 0)
                  <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                    <div class="font-medium text-gray-800 mb-2 flex items-center">
                      <i class="fas fa-book text-purple-500 mr-2"></i>
                      Struktur Bab
                    </div>
                    <div class="space-y-2">
                      @foreach($locations['bab'] as $b)
                        <button 
                          onclick="navigateToPDFPage({{ $b['page'] }})"
                          class="w-full text-left p-2 bg-white rounded hover:bg-purple-100 transition cursor-pointer group border border-transparent hover:border-purple-200 requirement-item">
                          <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1 min-w-0">
                              <span class="font-medium text-gray-700">{{ $b['label'] }}</span>
                              <span class="ml-2 text-sm text-gray-500">Hal. {{ $b['page'] }}</span>
                            </div>
                            <i class="fas fa-chevron-right text-purple-400 opacity-0 group-hover:opacity-100 transition text-sm ml-2 flex-shrink-0"></i>
                          </div>
                          @if(!empty($b['title']))
                            <p class="text-sm text-gray-600 line-clamp-2 mt-1">{{ $b['title'] }}</p>
                          @endif
                        </button>
                      @endforeach
                    </div>
                  </div>
                @endif

                @if(isset($locations['daftar_pustaka']) && $locations['daftar_pustaka'])
                  <button 
                    onclick="navigateToPDFPage({{ $locations['daftar_pustaka']['page'] }})"
                    class="w-full text-left p-3 bg-green-50 rounded-lg border border-green-100 hover:bg-green-100 hover:border-green-200 transition cursor-pointer group requirement-item">
                    <div class="flex items-center justify-between">
                      <div class="flex items-center flex-1 min-w-0">
                        <i class="fas fa-list text-green-500 mr-2 flex-shrink-0"></i>
                        <span class="font-medium text-gray-800">Daftar Pustaka</span>
                        <span class="ml-2 text-sm text-gray-500">Hal. {{ $locations['daftar_pustaka']['page'] }}</span>
                      </div>
                      <i class="fas fa-chevron-right text-green-400 opacity-0 group-hover:opacity-100 transition ml-2 flex-shrink-0"></i>
                    </div>
                    @if(!empty($locations['daftar_pustaka']['snippet']))
                      <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $locations['daftar_pustaka']['snippet'] }}</p>
                    @endif
                  </button>
                @endif
              </div>
            </div>
          </div>
        </div>

        <!-- PDF Preview Section -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 mb-8">
          <div class="flex flex-col md:flex-row md:items-center justify-between mb-4 gap-3">
            <h3 class="text-lg font-bold text-gray-800">
              <i class="fas fa-file-pdf text-red-500 mr-2"></i>
              Preview Dokumen PDF
            </h3>
            
            <!-- PDF Controls -->
            <div class="flex flex-wrap items-center gap-3">
              <!-- Page Navigation -->
              <div class="flex items-center space-x-2">
                <button id="prev-page" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 disabled:opacity-50" disabled>
                  <i class="fas fa-chevron-left"></i>
                </button>
                <span class="text-sm font-medium">
                  Halaman: <span id="current-page">1</span> / <span id="total-pages">1</span>
                </span>
                <button id="next-page" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700 disabled:opacity-50" disabled>
                  <i class="fas fa-chevron-right"></i>
                </button>
              </div>
              
              <!-- Zoom Controls -->
              <div class="flex items-center space-x-2">
                <button id="zoom-out" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">
                  <i class="fas fa-search-minus"></i>
                </button>
                <span id="zoom-level" class="text-sm font-medium">100%</span>
                <button id="zoom-in" class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded text-gray-700">
                  <i class="fas fa-search-plus"></i>
                </button>
              </div>
              
              <!-- Full PDF Link -->
              <a href="{{ route('serve.pdf', ['filename' => $filename]) }}" target="_blank" 
                class="text-sm text-blue-600 hover:text-blue-700 font-medium px-3 py-1 bg-blue-50 rounded">
                <i class="fas fa-external-link-alt mr-1"></i> Buka PDF
              </a>
            </div>
          </div>
          
          <!-- PDF.js Viewer Container -->
          <div id="pdf-container" class="border border-gray-300 rounded-lg overflow-hidden bg-gray-100 relative" style="height: 600px;">
            
            <!-- Loading Indicator -->
            <div id="pdf-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-gray-50 z-20">
              <div class="text-center">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-500 mb-4"></i>
                <p class="text-gray-600 font-medium">Memuat dokumen PDF...</p>
                <p class="text-sm text-gray-500 mt-2">Harap tunggu sebentar</p>
              </div>
            </div>
            
            <!-- PDF Viewer -->
            <div id="pdf-viewer-container" class="w-full h-full overflow-auto">
              <canvas id="pdf-canvas" class="mx-auto block shadow-lg"></canvas>
            </div>
          </div>
          
          <!-- Quick Navigation Buttons -->
          <div class="mt-4 flex flex-wrap gap-2">
            @if(isset($locations['abstrak']) && $locations['abstrak'])
              <button onclick="goToPage({{ $locations['abstrak']['page'] }})" class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-medium transition btn-hover">
                <i class="fas fa-file-alt mr-1"></i> Abstrak (Hal. {{ $locations['abstrak']['page'] }})
              </button>
            @endif
            
            @if(isset($locations['bab']) && is_array($locations['bab']))
              @foreach($locations['bab'] as $bab)
                <button onclick="goToPage({{ $bab['page'] }})" class="px-4 py-2 bg-purple-100 hover:bg-purple-200 text-purple-700 rounded-lg text-sm font-medium transition btn-hover">
                  <i class="fas fa-book mr-1"></i> {{ $bab['label'] }} (Hal. {{ $bab['page'] }})
                </button>
              @endforeach
            @endif
            
            @if(isset($locations['daftar_pustaka']) && $locations['daftar_pustaka'])
              <button onclick="goToPage({{ $locations['daftar_pustaka']['page'] }})" class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm font-medium transition btn-hover">
                <i class="fas fa-list mr-1"></i> Daftar Pustaka (Hal. {{ $locations['daftar_pustaka']['page'] }})
              </button>
            @endif
          </div>
          
          <!-- PDF Navigation Helper -->
          <div class="mt-3 text-sm text-gray-600 bg-blue-50 p-3 rounded-lg">
            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
            <span class="font-medium">Tip:</span> Klik tombol di atas untuk langsung menuju halaman tersebut di PDF.
          </div>
        </div>
        @endif

        <!-- Detail Analysis -->
        <h2 class="text-xl font-bold text-gray-800 mb-6 break-words">Detail Analisis Format ITS</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 mb-8 md:mb-10">
          <!-- Abstrak Card -->
          @php
            $abstrak = $results['details']['Abstrak'] ?? [];
            $abstrakStatus = $abstrak['status'] ?? '';
            $isAbstrakValid = $abstrakStatus === 'YA' || $abstrakStatus === '✓';
            $abstrakColor = $isAbstrakValid ? 'success' : 'warning';
          @endphp
          <div class="result-card bg-white rounded-xl border p-4 md:p-5 shadow-sm 
              @if($abstrakColor === 'success') border-green-200
              @else border-yellow-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if($abstrakColor === 'success') bg-green-100 text-green-500
                  @else bg-yellow-100 text-yellow-500 @endif">
                <i class="fas @if($abstrakColor === 'success') fa-check
                    @else fa-exclamation-triangle @endif"></i>
              </div>
              <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-gray-800 break-words">Abstrak</h3>
                <p class="text-sm text-gray-600 break-words">Status: {{ $abstrakStatus }}</p>
              </div>
            </div>
            <p class="text-sm line-clamp-2
                @if($abstrakColor === 'success') text-green-600
                @else text-yellow-600 @endif font-medium break-words">
              {{ $abstrak['notes'] ?? 'Abstrak tidak ditemukan' }}
            </p>
          </div>

          <!-- Format Teks Card -->
          @php
            $format = $results['details']['Format Teks'] ?? [];
            $font = $format['font'] ?? '';
            $isFormatValid = $font !== 'Tidak terdeteksi';
            $formatColor = $isFormatValid ? 'success' : 'warning';
          @endphp
          <div class="result-card bg-white rounded-xl border p-4 md:p-5 shadow-sm 
              @if($formatColor === 'success') border-green-200
              @else border-yellow-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if($formatColor === 'success') bg-green-100 text-green-500
                  @else bg-yellow-100 text-yellow-500 @endif">
                <i class="fas @if($formatColor === 'success') fa-check
                    @else fa-exclamation-triangle @endif"></i>
              </div>
              <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-gray-800 break-words">Format Teks</h3>
                <p class="text-sm text-gray-600 line-clamp-2 break-words">
                  Font: {{ $format['font'] ?? 'Tidak terdeteksi' }}, 
                  Spacing: {{ $format['spacing'] ?? 'Tidak terdeteksi' }}
                </p>
              </div>
            </div>
            <p class="text-sm line-clamp-2
                @if($formatColor === 'success') text-green-600
                @else text-yellow-600 @endif font-medium break-words">
              {{ $format['notes'] ?? 'Format teks tidak terdeteksi' }}
            </p>
          </div>

          <!-- Margin Card -->
          @php
            $margin = $results['details']['Margin'] ?? [];
            $topMargin = $margin['top'] ?? '';
            $isMarginValid = $topMargin !== 'Tidak terdeteksi';
            $marginColor = $isMarginValid ? 'success' : 'warning';
          @endphp
          <div class="result-card bg-white rounded-xl border p-4 md:p-5 shadow-sm 
              @if($marginColor === 'success') border-green-200
              @else border-yellow-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if($marginColor === 'success') bg-green-100 text-green-500
                  @else bg-yellow-100 text-yellow-500 @endif">
                <i class="fas @if($marginColor === 'success') fa-check
                    @else fa-exclamation-triangle @endif"></i>
              </div>
              <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-gray-800 break-words">Margin</h3>
                <p class="text-sm text-gray-600 line-clamp-2 break-words">
                  Atas: {{ $margin['top'] ?? 'Tidak terdeteksi' }}, 
                  Bawah: {{ $margin['bottom'] ?? 'Tidak terdeteksi' }}
                </p>
              </div>
            </div>
            <p class="text-sm line-clamp-2
                @if($marginColor === 'success') text-green-600
                @else text-yellow-600 @endif font-medium break-words">
              {{ $margin['notes'] ?? 'Margin tidak terdeteksi' }}
            </p>
          </div>

          <!-- Struktur Bab Card -->
          @php
            $chapters = $results['details']['Struktur Bab'] ?? [];
            $completedChapters = 0;
            $totalChapters = 0;
            
            for ($i = 1; $i <= 5; $i++) {
                $babKey = "Bab $i";
                if (isset($chapters[$babKey])) {
                    $totalChapters++;
                    if ($chapters[$babKey] === '✓') {
                        $completedChapters++;
                    }
                }
            }
            
            $completionRate = $totalChapters > 0 ? ($completedChapters / $totalChapters) * 100 : 0;
            $chaptersColor = $completionRate >= 80 ? 'success' : ($completionRate >= 60 ? 'warning' : 'danger');
          @endphp
          <div class="result-card bg-white rounded-xl border p-4 md:p-5 shadow-sm 
              @if($chaptersColor === 'success') border-green-200
              @elseif($chaptersColor === 'warning') border-yellow-200
              @else border-red-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if($chaptersColor === 'success') bg-green-100 text-green-500
                  @elseif($chaptersColor === 'warning') bg-yellow-100 text-yellow-500
                  @else bg-red-100 text-red-500 @endif">
                <i class="fas @if($chaptersColor === 'success') fa-check
                    @elseif($chaptersColor === 'warning') fa-exclamation-triangle
                    @else fa-times @endif"></i>
              </div>
              <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-gray-800 break-words">Struktur Bab</h3>
                <p class="text-sm text-gray-600 line-clamp-2 break-words">
                  @for($i = 1; $i <= 5; $i++)
                    @if(isset($chapters["Bab $i"]))
                    Bab {{ $i }}: {{ $chapters["Bab $i"] }} 
                    @endif
                  @endfor
                </p>
              </div>
            </div>
            <div class="bg-gray-100 rounded-full h-2.5 mb-2">
              <div class="progress-bar h-2.5 rounded-full 
                  @if($chaptersColor === 'success') bg-green-500
                  @elseif($chaptersColor === 'warning') bg-yellow-500
                  @else bg-red-500 @endif" 
                  style="width: {{ $completionRate }}%">
              </div>
            </div>
            <p class="text-sm line-clamp-2
                @if($chaptersColor === 'success') text-green-600
                @elseif($chaptersColor === 'warning') text-yellow-600
                @else text-red-600 @endif font-medium break-words">
              {{ $chapters['notes'] ?? 'Struktur bab tidak terdeteksi' }}
            </p>
          </div>

          <!-- Daftar Pustaka Card -->
          @php
            $references = $results['details']['Daftar Pustaka'] ?? [];
            $refCount = $references['references_count'] ?? '';
            $refCountStr = is_array($refCount) ? '' : trim((string) $refCount);
            $refNotes = $references['notes'] ?? '';
            
            // Extract angka dari string seperti "≥21"
            preg_match('/(\d+)/', $refCountStr, $matches);
            $refNumber = isset($matches[1]) ? intval($matches[1]) : 0;

            // Cek apakah daftar pustaka terdeteksi dari locations atau notes
            $hasDaftarPustaka = false;
            if (isset($results['locations']['daftar_pustaka']) && $results['locations']['daftar_pustaka']) {
                $hasDaftarPustaka = true;
            }
            
            // Jika notes mengandung "Tidak dievaluasi" atau "Terdeteksi" berarti ada daftar pustaka
            if (stripos($refNotes, 'Tidak dievaluasi') !== false || 
                stripos($refNotes, 'Terdeteksi') !== false ||
                stripos($refNotes, 'ditemukan') !== false) {
                $hasDaftarPustaka = true;
            }

            // Status berdasarkan deteksi dan jumlah referensi
            if ($hasDaftarPustaka && $refNumber >= 20) {
                $refStatus = 'success';
                $refIcon = 'fa-check-circle';
            } elseif ($hasDaftarPustaka) {
                // Daftar pustaka terdeteksi tapi jumlah tidak dihitung atau < 20
                $refStatus = 'success';
                $refIcon = 'fa-check-circle';
            } elseif ($refNumber > 0) {
                $refStatus = 'warning';
                $refIcon = 'fa-exclamation-triangle';
            } else {
                $refStatus = 'danger';
                $refIcon = 'fa-times-circle';
            }
          @endphp
          <div class="result-card bg-white rounded-xl border p-4 md:p-5 shadow-sm 
              @if($refStatus === 'success') border-green-200
              @elseif($refStatus === 'warning') border-yellow-200
              @else border-red-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if($refStatus === 'success') bg-green-100 text-green-500
                  @elseif($refStatus === 'warning') bg-yellow-100 text-yellow-500
                  @else bg-red-100 text-red-500 @endif">
                <i class="fas {{ $refIcon }}"></i>
              </div>
              <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-gray-800 break-words">Daftar Pustaka</h3>
                <p class="text-sm text-gray-600 line-clamp-2 break-words">
                  {{ $references['references_count'] ?? 'Tidak terdeteksi' }} referensi,
                  Format: {{ $references['format'] ?? 'Tidak terdeteksi' }}
                </p>
              </div>
            </div>
            <p class="text-sm line-clamp-2
                @if($refStatus === 'success') text-green-600
                @elseif($refStatus === 'warning') text-yellow-600
                @else text-red-600 @endif font-medium break-words">
              {{ $references['notes'] ?? 'Daftar pustaka tidak ditemukan' }}
            </p>
          </div>

          <!-- Cover & Halaman Formal Card -->
          @php
            $cover = $results['details']['Cover & Halaman Formal'] ?? [];
            $coverStatus = $cover['status'] ?? '';
            $isCoverValid = $coverStatus === 'YA' || $coverStatus === '✓';
            $coverColor = $isCoverValid ? 'success' : 'warning';
          @endphp
          <div class="result-card bg-white rounded-xl border p-4 md:p-5 shadow-sm 
              @if($coverColor === 'success') border-green-200
              @else border-yellow-200 @endif">
            <div class="flex items-start mb-4">
              <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                  @if($coverColor === 'success') bg-green-100 text-green-500
                  @else bg-yellow-100 text-yellow-500 @endif">
                <i class="fas @if($coverColor === 'success') fa-check
                    @else fa-exclamation-triangle @endif"></i>
              </div>
              <div class="min-w-0 flex-1">
                <h3 class="font-semibold text-gray-800 break-words">Cover & Halaman Formal</h3>
                <p class="text-sm text-gray-600 break-words">Status: {{ $coverStatus }}</p>
              </div>
            </div>
            <p class="text-sm line-clamp-2
                @if($coverColor === 'success') text-green-600
                @else text-yellow-600 @endif font-medium break-words">
              {{ $cover['notes'] ?? 'Halaman formal tidak terdeteksi' }}
            </p>
          </div>
        </div>

        <!-- Recommendations -->
        @if(isset($results['recommendations']) && count($results['recommendations']) > 0)
        <div class="mb-8 p-6 bg-yellow-50 rounded-xl border border-yellow-200">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center break-words">
            <i class="fas fa-lightbulb text-yellow-500 mr-2"></i> Rekomendasi Perbaikan
          </h3>
          <ul class="list-disc list-inside space-y-2">
            @foreach($results['recommendations'] as $recommendation)
              <li class="text-gray-700 break-words">{{ $recommendation }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <!-- Document Info -->
        <div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center break-words">
            <i class="fas fa-info-circle mr-2"></i> Informasi Dokumen
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @php
            $docInfo = $results['document_info'] ?? [];
            @endphp
            
            <div class="flex justify-between">
              <span class="text-gray-600 break-words">Jenis Dokumen:</span>
              <span class="font-medium break-words">{{ $docInfo['jenis_dokumen'] ?? 'Tidak Diketahui' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 break-words">Total Halaman:</span>
              <span class="font-medium break-words">{{ $docInfo['total_halaman'] ?? 'Tidak Diketahui' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 break-words">Ukuran File:</span>
              <span class="font-medium break-words">{{ $docInfo['ukuran_file'] ?? 'Tidak Diketahui' }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600 break-words">Format File:</span>
              <span class="font-medium break-words">{{ $docInfo['format_file'] ?? 'Tidak Diketahui' }}</span>
            </div>
          </div>
        </div>
        @else
        <!-- Fallback jika tidak ada results -->
        <div class="text-center py-8">
          <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
          </div>
          <h3 class="text-xl font-semibold text-gray-800 mb-2 break-words">Data Hasil Tidak Tersedia</h3>
          <p class="text-gray-600 mb-4 break-words">Tidak dapat memuat hasil analisis.</p>
          <a href="{{ route('upload.form') }}" class="btn-hover bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition-all break-words">
            <i class="fas fa-upload mr-2"></i> Upload Ulang
          </a>
        </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 print-hidden">
          <a href="{{ route('upload.form') }}" class="flex-1 btn-hover bg-blue-500 hover:bg-blue-600 text-white text-center font-semibold py-3 px-4 rounded-lg transition-all break-words">
            <i class="fas fa-upload mr-2"></i> Analisis File Lain
          </a>
          <button onclick="saveResults()" class="flex-1 btn-hover bg-white border border-gray-300 hover:bg-gray-100 text-gray-800 font-semibold py-3 px-4 rounded-lg transition-all break-words">
            <i class="fas fa-download mr-2"></i> Simpan Hasil
          </button>
          <button onclick="window.print()" class="flex-1 btn-hover bg-purple-500 hover:bg-purple-600 text-white font-semibold py-3 px-4 rounded-lg transition-all break-words">
            <i class="fas fa-print mr-2"></i> Cetak Laporan
          </button>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-6 mt-8 print-hidden">
    <div class="max-w-6xl mx-auto px-4 text-center safe-area">
      <p class="text-gray-400 text-sm break-words">
        © 2025 Sistem Deteksi Kelengkapan Format Tugas Akhir - ITS.
      </p>
    </div>
  </footer>

  <script>
  // PDF.js variables
  let pdfDoc = null;
  let currentPage = 1;
  let scale = 1.0;
  const SCALE_STEP = 0.1;
  let canvas, ctx;

  // Initialize PDF.js
  document.addEventListener('DOMContentLoaded', function() {
      canvas = document.getElementById('pdf-canvas');
      ctx = canvas.getContext('2d');
      
      // Load PDF
      loadPDF('{{ route('serve.pdf', ['filename' => $filename]) }}');
      
      // Event listeners for buttons
      document.getElementById('prev-page').addEventListener('click', onPrevPage);
      document.getElementById('next-page').addEventListener('click', onNextPage);
      document.getElementById('zoom-in').addEventListener('click', zoomIn);
      document.getElementById('zoom-out').addEventListener('click', zoomOut);
  });

  // Load PDF with PDF.js
  function loadPDF(url) {
      console.log('Loading PDF from:', url);
      
      // Show loading indicator
      const loadingEl = document.getElementById('pdf-loading');
      if (loadingEl) loadingEl.style.display = 'flex';
      
      // Hide error if shown
      const errorEl = document.getElementById('pdf-error');
      if (errorEl) errorEl.classList.add('hidden');
      
      // Disable navigation buttons
      document.getElementById('prev-page').disabled = true;
      document.getElementById('next-page').disabled = true;
      
      // Configure PDF.js
      pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
      
      const loadingTask = pdfjsLib.getDocument(url);
      
      loadingTask.promise.then(function(pdf) {
          pdfDoc = pdf;
          
          // Update total pages
          document.getElementById('total-pages').textContent = pdf.numPages;
          
          // Enable navigation buttons
          document.getElementById('prev-page').disabled = false;
          document.getElementById('next-page').disabled = false;
          
          // Hide loading
          if (loadingEl) loadingEl.style.display = 'none';
          
          // Render first page
          renderPage(currentPage);
          
          console.log('PDF loaded successfully, total pages:', pdf.numPages);
      }).catch(function(error) {
          console.error('Error loading PDF:', error);
          
          // Hide loading, show error
          if (loadingEl) loadingEl.style.display = 'none';
          if (errorEl) errorEl.classList.remove('hidden');
      });
  }

  // Render a specific page
  function renderPage(pageNum) {
      if (!pdfDoc) return;
      
      // Update current page
      currentPage = pageNum;
      document.getElementById('current-page').textContent = currentPage;
      
      // Get page
      pdfDoc.getPage(pageNum).then(function(page) {
          // Calculate viewport
          const viewport = page.getViewport({ scale: scale });
          
          // Set canvas dimensions
          canvas.height = viewport.height;
          canvas.width = viewport.width;
          
          // Adjust container height if needed
          const container = document.getElementById('pdf-viewer-container');
          if (container) {
              container.scrollTop = 0;
          }
          
          // Render PDF page to canvas
          const renderContext = {
              canvasContext: ctx,
              viewport: viewport
          };
          
          page.render(renderContext).promise.then(function() {
              console.log('Page rendered:', pageNum);
              
              // Update navigation buttons state
              document.getElementById('prev-page').disabled = currentPage <= 1;
              document.getElementById('next-page').disabled = currentPage >= pdfDoc.numPages;
              
              // Update zoom level display
              document.getElementById('zoom-level').textContent = Math.round(scale * 100) + '%';
          });
      }).catch(function(error) {
          console.error('Error rendering page:', error);
      });
  }

  // Navigate to previous page
  function onPrevPage() {
      if (currentPage <= 1) return;
      goToPage(currentPage - 1);
  }

  // Navigate to next page
  function onNextPage() {
      if (!pdfDoc || currentPage >= pdfDoc.numPages) return;
      goToPage(currentPage + 1);
  }

  // Zoom in
  function zoomIn() {
      scale += SCALE_STEP;
      renderPage(currentPage);
  }

  // Zoom out
  function zoomOut() {
      if (scale > 0.2) {
          scale -= SCALE_STEP;
          renderPage(currentPage);
      }
  }

  // Navigate to specific page (this is called by the buttons)
  function goToPage(pageNumber) {
      if (!pdfDoc) return;
      
      // Validate page number
      const validPage = Math.max(1, Math.min(pageNumber, pdfDoc.numPages));
      
      // Scroll PDF container into view
      const pdfContainer = document.getElementById('pdf-container');
      if (pdfContainer) {
          pdfContainer.scrollIntoView({ 
              behavior: 'smooth', 
              block: 'center' 
          });
          
          // Highlight effect
          pdfContainer.style.transition = 'box-shadow 0.3s ease';
          pdfContainer.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.3)';
          
          setTimeout(() => {
              pdfContainer.style.boxShadow = '';
          }, 1000);
      }
      
      // Render the page
      renderPage(validPage);
  }

  // Retry loading PDF
  function retryLoadPDF() {
      const errorEl = document.getElementById('pdf-error');
      if (errorEl) errorEl.classList.add('hidden');
      
      loadPDF('{{ route('serve.pdf', ['filename' => $filename]) }}');
  }

  // Fungsi untuk navigasi ke halaman PDF tertentu (compatibility with existing buttons)
  function navigateToPDFPage(pageNumber) {
      // This is for compatibility with existing onclick handlers
      goToPage(pageNumber);
  }

  // Fungsi untuk menyimpan hasil
  function saveResults() {
      const button = event.target.closest('button') || event.target;
      const originalText = button.innerHTML;
      
      button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Menyimpan...';
      button.disabled = true;
      
      // Data dari PHP / Blade
      const resultsData = {
          filename: "{{ $filename ?? 'Nama File' }}",
          analysisDate: new Date().toLocaleString('id-ID'),
          score: {{ $results['score'] ?? 0 }},
          percentage: {{ $results['percentage'] ?? 0 }},
          status: "{{ $results['status'] ?? 'TIDAK DIKETAHUI' }}",
          details: @json($results['details'] ?? []),
          document_info: @json($results['document_info'] ?? [])
      };

      const blob = new Blob([JSON.stringify(resultsData, null, 2)], { type: 'application/json' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = 'hasil-analisis-{{ $filename ?? "dokumen" }}-' + new Date().getTime() + '.json';
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
      
      button.innerHTML = '<i class="fas fa-check mr-2"></i> Tersimpan!';
      setTimeout(() => {
          button.innerHTML = originalText;
          button.disabled = false;
      }, 2000);
  }

  // Mobile menu functionality
  document.addEventListener('DOMContentLoaded', function() {
      const mobileMenuButton = document.getElementById('mobileMenuButton');
      const mobileMenu = document.getElementById('mobileMenu');
      
      if (mobileMenuButton && mobileMenu) {
          // To tooggle mobile menu
          mobileMenuButton.addEventListener('click', function(e) {
              e.stopPropagation();
              mobileMenu.classList.toggle('hidden');
          });

          // Close mobile menu when clicking outside
          document.addEventListener('click', function(e) {
              if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target)) {
                  mobileMenu.classList.add('hidden');
              }
          });

          // Close mobile menu when clicking on a link
          const mobileMenuLinks = mobileMenu.querySelectorAll('a');
          mobileMenuLinks.forEach(link => {
              link.addEventListener('click', function() {
                  mobileMenu.classList.add('hidden');
              });
          });
      }

      // Prevent mobile menu from closing when clicking inside it
      if (mobileMenu) {
          mobileMenu.addEventListener('click', function(e) {
              e.stopPropagation();
          });
      }
  });

  // Handle window resize - close mobile menu on desktop
  window.addEventListener('resize', function() {
      const mobileMenu = document.getElementById('mobileMenu');
      if (window.innerWidth >= 768 && mobileMenu) {
          mobileMenu.classList.add('hidden');
      }
  });

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
  </script>
  <!-- Dark Mode Toggle Button -->
  <button id="darkModeToggle" class="fixed bottom-6 right-6 z-50 w-12 h-12 rounded-full bg-white dark:bg-gray-800 shadow-lg hover:shadow-xl border border-gray-200 dark:border-gray-700 flex items-center justify-center transition-all duration-300 hover:scale-110 print-hidden">
    <i id="darkModeIcon" class="fas fa-moon text-gray-700 dark:text-yellow-300 text-lg"></i>
  </button>
</body>
</html>