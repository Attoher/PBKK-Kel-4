<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Analisis - FormatCheck ITS</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      background: rgba(255, 255, 255, 0.95);
    }

    .history-card {
      transition: all 0.3s ease;
    }

    .history-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    .score-badge {
      transition: all 0.3s ease;
    }

    .empty-state {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    /* Loading spinner */
    .spinner {
      border: 3px solid #f3f3f3;
      border-top: 3px solid #3498db;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      animation: spin 1s linear infinite;
      margin: 0 auto;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Notification styles */
    .notification {
      position: fixed;
      top: 100px;
      right: 20px;
      z-index: 1000;
      animation: slideIn 0.3s ease-out;
      max-width: 400px;
    }

    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    .notification.hide {
      animation: slideOut 0.3s ease-in;
    }

    @keyframes slideOut {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(100%); opacity: 0; }
    }

    /* Custom scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
      width: 6px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 10px;
    }
    
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

    /* Animation for cards */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .animate-fade-in-up {
      animation: fadeInUp 0.6s ease-out forwards;
    }

    /* Print styles */
    @media print {
      .no-print {
        display: none !important;
      }
      
      body {
        background: white !important;
      }
      
      .bg-gradient-to-r {
        background: #0067ac !important;
        -webkit-print-color-adjust: exact;
      }
    }

    .spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        margin: 0 auto;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

  </style>
</head>
<body class="flex flex-col min-h-screen">
  <!-- Navbar (desktop + mobile toggle) -->
  <nav class="navbar shadow-lg border-b border-gray-200/50 sticky top-0 z-50 no-print will-change-transform">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <a href="{{ url('/') }}" class="flex items-center">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
              </div>
              <span class="ml-3 text-xl font-bold text-gray-800">FormatCheck ITS</span>
            </a>
          </div>

          <!-- Navigation Links (desktop) -->
          <div class="hidden md:ml-8 md:flex md:space-x-6">
            <a href="{{ route('upload.form') }}" class="nav-link border-transparent text-gray-600 hover:text-gray-900 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200">
              <i class="fas fa-upload mr-2 text-blue-500"></i>
              Upload TA
            </a>
            <a href="{{ route('history') }}" class="nav-link border-transparent text-gray-600 inline-flex items-center px-3 py-2 border-b-2 text-sm font-semibold transition-all duration-200 text-gray-900">
              <i class="fas fa-history mr-2 text-purple-500"></i>
              Riwayat
            </a>
          </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-3">
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
            @else
              <a href="{{ route('login.form') }}" class="text-sm text-gray-700 hover:text-blue-600 transition">
                <i class="fas fa-sign-in-alt mr-1"></i>Login
              </a>
            @endauth
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
    <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200 bg-white/95 backdrop-blur-lg no-print">
      <div class="pt-2 pb-4 space-y-1">
        <a href="{{ route('upload.form') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
          <i class="fas fa-upload mr-3 text-blue-500"></i>Upload TA</a>
        <a href="{{ route('history') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
          <i class="fas fa-history mr-3 text-purple-500"></i>Riwayat</a>

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

  <!-- Loading Overlay -->
  <div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 no-print">
    <div class="relative top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
      <div class="bg-white rounded-lg p-6 shadow-lg text-center min-w-48">
        <div class="spinner mb-4"></div>
        <p id="loadingMessage" class="text-gray-700">Memproses...</p>
      </div>
    </div>
  </div>

  <!-- Notification -->
  <div id="notification" class="notification hidden no-print">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-lg" id="notificationContent">
      <div class="flex items-start">
        <i class="fas fa-check-circle mt-1 mr-3 flex-shrink-0"></i>
        <span class="block flex-1" id="notificationMessage"></span>
      </div>
      <button onclick="hideNotification()" class="absolute top-3 right-3 p-1 text-green-500 hover:text-green-700 transition">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
  </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2" id="modalTitle">Konfirmasi Hapus</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500" id="modalMessage">Apakah Anda yakin ingin menghapus?</p>
                    <div id="modalDetails" class="text-xs text-gray-400 mt-2 text-left bg-gray-50 p-2 rounded max-h-32 overflow-y-auto hidden"></div>
                </div>
                <div class="flex justify-center space-x-4 mt-4">
                    <button id="confirmCancel" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition font-medium">
                        Batal
                    </button>
                    <button id="confirmOk" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition font-medium">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

  <!-- Main Content -->
  <main class="flex-grow py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Riwayat Analisis Dokumen</h1>
        <p class="text-blue-100 text-lg">Lihat sejarah analisis format Tugas Akhir Anda</p>
      </div>

      <!-- Stats Cards -->
      <div class="flex gap-4 mb-8 md:grid md:grid-cols-4 md:gap-6 overflow-x-auto pb-2 md:pb-0">
  <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up w-64 flex-shrink-0" style="animation-delay: 0.1s">
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-file-pdf text-blue-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-gray-800">{{ count($history) }}</div>
          <div class="text-sm text-gray-600">Total Analisis</div>
        </div>
  <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up w-64 flex-shrink-0" style="animation-delay: 0.2s">
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-gray-800">
            {{ collect($history)->where('score', '>=', 8)->count() }}
          </div>
          <div class="text-sm text-gray-600">Layak Ajukan</div>
        </div>

  <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up w-64 flex-shrink-0" style="animation-delay: 0.3s">
          <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-gray-800">
            {{ collect($history)->whereBetween('score', [6, 7.9])->count() }}
          </div>
          <div class="text-sm text-gray-600">Perlu Perbaikan</div>
        </div>

  <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up w-64 flex-shrink-0" style="animation-delay: 0.4s">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-times-circle text-red-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-gray-800">
            {{ collect($history)->where('score', '<', 6)->count() }}
          </div>
          <div class="text-sm text-gray-600">Tidak Layak</div>
        </div>
      </div>

      <!-- History List -->
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h2 class="text-xl font-bold text-gray-800 mb-2 sm:mb-0">Dokumen yang Telah Dianalisis</h2>
            <div class="flex flex-wrap gap-2">
              <button type="button" onclick="clearOldHistory()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center no-print">
                    <i class="fas fa-clock mr-2"></i>Hapus Lama
              </button>
              <button onclick="exportHistory()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center no-print">
                <i class="fas fa-download mr-2"></i>Export CSV
              </button>
              <button type="button" onclick="clearHistory()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center no-print">
                <i class="fas fa-trash mr-2"></i>Hapus Semua
              </button>
            </div>
          </div>
        </div>

        <div class="p-6">
          @if(count($history) > 0)
            <div class="space-y-4">
              @foreach($history as $index => $item)
              <div class="history-card bg-gray-50 rounded-xl border border-gray-200 p-6 animate-fade-in-up" 
                   data-filename="{{ $item['filename'] }}"
                   style="animation-delay: {{ ($index % 10) * 0.1 }}s">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                  <div class="flex-1 mb-4 lg:mb-0">
                    <div class="flex items-start space-x-4">
                      <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                          <i class="fas fa-file-pdf text-white text-lg"></i>
                        </div>
                      </div>
                      <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-800 truncate" title="{{ $item['filename'] }}">
                          {{ $item['filename'] }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ \Illuminate\Support\Carbon::parse($item['date'])->format('d M Y H:i') }}
                          </span>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <i class="fas fa-clock mr-1"></i>
                            {{ \Illuminate\Support\Carbon::parse($item['date'])->diffForHumans() }}
                          </span>
                          @if(isset($item['document_type']) && $item['document_type'] !== 'Tidak Diketahui')
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-file-alt mr-1"></i>
                            {{ $item['document_type'] }}
                          </span>
                          @endif
                          @if(!($item['file_exists'] ?? true))
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            File Tidak Ditemukan
                          </span>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex items-center space-x-4">
                    <!-- Score Badge -->
                    <div class="score-badge px-4 py-2 rounded-full font-semibold text-center min-w-24
                        @if($item['score'] >= 8) bg-green-100 text-green-800 border border-green-200
                        @elseif($item['score'] >= 6) bg-yellow-100 text-yellow-800 border border-yellow-200
                        @else bg-red-100 text-red-800 border border-red-200 @endif">
                      <div class="text-2xl font-bold">{{ number_format($item['score'], 1) }}</div>
                      <div class="text-xs">/ 10</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2 no-print">
                      @if($item['file_exists'] ?? true)
                      <a href="{{ route('results', ['filename' => $item['filename']]) }}" 
                         class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center group">
                        <i class="fas fa-eye mr-2 group-hover:scale-110 transition-transform"></i>Lihat
                      </a>
                      <button onclick="downloadResults('{{ $item['filename'] }}')" 
                         class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center group">
                        <i class="fas fa-download mr-2 group-hover:scale-110 transition-transform"></i>Download
                      </button>
                      @else
                      <span class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center cursor-not-allowed" title="File tidak tersedia">
                        <i class="fas fa-eye-slash mr-2"></i>File Hilang
                      </span>
                      @endif
                      <button onclick="deleteHistoryItem('{{ $item['filename'] }}')" 
                         class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg text-sm transition-all flex items-center group"
                         title="Hapus riwayat ini">
                        <i class="fas fa-trash group-hover:scale-110 transition-transform"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Status Bar -->
                <div class="mt-4">
                  <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Status Kelayakan:</span>
                    <span class="font-medium 
                      @if($item['score'] >= 8) text-green-600
                      @elseif($item['score'] >= 6) text-yellow-600
                      @else text-red-600 @endif">
                      @if($item['score'] >= 8) LAYAK DIAJUKAN
                      @elseif($item['score'] >= 6) PERLU PERBAIKAN
                      @else TIDAK LAYAK @endif
                    </span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-1000 ease-out
                      @if($item['score'] >= 8) bg-green-500
                      @elseif($item['score'] >= 6) bg-yellow-500
                      @else bg-red-500 @endif" 
                      style="width: {{ $item['score'] * 10 }}%">
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
            </div>

            <!-- Pagination Info -->
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600">
              <div>
                Menampilkan <span class="font-semibold">{{ count($history) }}</span> dari <span class="font-semibold">{{ count($history) }}</span> hasil analisis
              </div>
              <div class="mt-2 sm:mt-0">
                <span class="text-gray-500">Diurutkan berdasarkan: </span>
                <span class="font-semibold">Tanggal Terbaru</span>
              </div>
            </div>

          @else
            <!-- Empty State -->
            <div class="text-center py-12">
              <div class="empty-state w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-history text-white text-3xl"></i>
              </div>
              <h3 class="text-xl font-semibold text-gray-800 mb-2">Belum Ada Riwayat Analisis</h3>
              <p class="text-gray-600 mb-6 max-w-md mx-auto">
                Anda belum menganalisis dokumen Tugas Akhir. Upload dokumen pertama Anda untuk memulai analisis format.
              </p>
              <a href="{{ route('upload.form') }}" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-semibold py-3 px-6 rounded-lg transition-all inline-flex items-center no-print">
                <i class="fas fa-upload mr-2"></i>Upload Dokumen Pertama
              </a>
            </div>
          @endif
        </div>
      </div>

      <!-- Tips Section -->
      @if(count($history) > 0)
      <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 animate-fade-in-up">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-lightbulb text-yellow-500 text-xl mt-1"></i>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-yellow-800">Tips Meningkatkan Skor</h3>
              <div class="text-sm text-yellow-700 mt-2 space-y-1">
                <div class="flex items-center"><i class="fas fa-check mr-2 text-green-500"></i> Pastikan abstrak 200-300 kata (ID & EN)</div>
                <div class="flex items-center"><i class="fas fa-check mr-2 text-green-500"></i> Gunakan font Times New Roman 12pt</div>
                <div class="flex items-center"><i class="fas fa-check mr-2 text-green-500"></i> Atur margin sesuai standar ITS</div>
                <div class="flex items-center"><i class="fas fa-check mr-2 text-green-500"></i> Tambahkan minimal 20 referensi</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 animate-fade-in-up">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-chart-line text-blue-500 text-xl mt-1"></i>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">Progress Analisis</h3>
              <div class="text-sm text-blue-700 mt-2 space-y-1">
                <div class="flex justify-between">
                  <span>Rata-rata skor:</span>
                  <span class="font-semibold">{{ number_format(collect($history)->avg('score') ?? 0, 1) }}/10</span>
                </div>
                <div class="flex justify-between">
                  <span>Dokumen terbaik:</span>
                  <span class="font-semibold">{{ number_format(collect($history)->max('score') ?? 0, 1) }}/10</span>
                </div>
                <div class="flex justify-between">
                  <span>Tren kualitas:</span>
                  <span class="font-semibold 
                    @if((collect($history)->avg('score') ?? 0) >= 8) text-green-600
                    @elseif((collect($history)->avg('score') ?? 0) >= 6) text-yellow-600
                    @else text-red-600 @endif">
                    @if((collect($history)->avg('score') ?? 0) >= 8)
                      Excellent
                    @elseif((collect($history)->avg('score') ?? 0) >= 6)
                      Good
                    @else
                      Needs Improvement
                    @endif
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      @endif
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-8 mt-12 no-print">
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
          <a href="{{ route('upload.form') }}" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="Upload Baru">
            <i class="fas fa-upload text-xl"></i>
          </a>
          <a href="{{ route('history') }}" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="Riwayat">
            <i class="fas fa-history text-xl"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="Bantuan">
            <i class="fas fa-question-circle text-xl"></i>
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
        // ==================== MODAL MANAGEMENT ====================
        function showConfirmModal(title, message, action, filename = null, details = null) {
            
            // Update modal content
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;
            
            // Handle details
            const detailsElement = document.getElementById('modalDetails');
            if (details) {
                detailsElement.innerHTML = details;
                detailsElement.classList.remove('hidden');
            } else {
                detailsElement.classList.add('hidden');
            }
            
            // Store action and filename for confirmation
            window.currentAction = action;
            window.currentFilename = filename;
            
            // Show modal
            document.getElementById('confirmModal').classList.remove('hidden');
        }

        function hideConfirmModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            window.currentAction = null;
            window.currentFilename = null;
        }

        // ==================== NOTIFICATION SYSTEM ====================
        function showNotification(message, type = 'success') {
            // Remove existing notification
            const existingNotification = document.getElementById('notification');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Create new notification
            const notification = document.createElement('div');
            notification.id = 'notification';
            notification.className = 'fixed top-4 right-4 z-50 max-w-sm';
            
            const bgColor = type === 'success' ? 'bg-green-500' : 
                        type === 'error' ? 'bg-red-500' :
                        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';
            
            notification.innerHTML = `
                <div class="${bgColor} text-white p-4 rounded-lg shadow-lg">
                    <div class="flex items-center">
                        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}-circle mr-2"></i>
                        <span>${message}</span>
                        <button onclick="hideNotification()" class="ml-4">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                hideNotification();
            }, 5000);
        }

        function hideNotification() {
            const notification = document.getElementById('notification');
            if (notification) {
                notification.remove();
            }
        }

        // ==================== LOADING SYSTEM ====================
        function showLoading(message = 'Memproses...') {
            // Remove existing loading
            const existingLoading = document.getElementById('loadingOverlay');
            if (existingLoading) {
                existingLoading.remove();
            }
            
            // Create new loading
            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'loadingOverlay';
            loadingOverlay.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center';
            loadingOverlay.innerHTML = `
                <div class="bg-white rounded-lg p-6 shadow-lg text-center min-w-48">
                    <div class="spinner mb-4"></div>
                    <p class="text-gray-700">${message}</p>
                </div>
            `;
            
            document.body.appendChild(loadingOverlay);
        }

        function hideLoading() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
        }

        // ==================== HISTORY MANAGEMENT FUNCTIONS ====================
        function clearHistory() {
            const historyCount = {{ count($history) }};
            if (historyCount === 0) {
                showNotification('Tidak ada riwayat untuk dihapus.', 'warning');
                return;
            }
            
            showConfirmModal(
                'Hapus Semua Riwayat',
                `Anda akan menghapus semua ${historyCount} riwayat analisis. Tindakan ini tidak dapat dibatalkan.`,
                'clearAll'
            );
        }

        function clearOldHistory() {
            const historyCount = {{ count($history) }};
            if (historyCount === 0) {
                showNotification('Tidak ada riwayat untuk dihapus.', 'warning');
                return;
            }

            // Calculate old files count
            const cutoffDate = new Date();
            cutoffDate.setDate(cutoffDate.getDate() - 30);
            
            let oldFilesCount = 0;
            let oldFilesList = [];
            
            // Use unique variable names to avoid redeclaration
            @foreach($history as $index => $item)
                const fileDate_{{ $index }} = new Date('{{ $item['date'] }}');
                if (fileDate_{{ $index }} < cutoffDate) {
                    oldFilesCount++;
                    oldFilesList.push('{{ $item['filename'] }}');
                }
            @endforeach

            if (oldFilesCount === 0) {
                showNotification('Tidak ada riwayat yang lebih lama dari 30 hari.', 'info');
                return;
            }

            const details = `File yang akan dihapus (${oldFilesCount} file):<br>• ${oldFilesList.slice(0, 5).join('<br>• ')}${oldFilesList.length > 5 ? '<br>• ... dan ' + (oldFilesList.length - 5) + ' file lainnya' : ''}`;

            showConfirmModal(
                'Hapus Riwayat Lama',
                `Anda akan menghapus ${oldFilesCount} riwayat analisis yang lebih lama dari 30 hari.`,
                'clearOld',
                null,
                details
            );
        }

        function deleteHistoryItem(filename) {
            showConfirmModal(
                'Hapus Riwayat',
                `Anda akan menghapus riwayat analisis untuk file: "${filename}"`,
                'deleteItem',
                filename
            );
        }

        function exportHistory() {
            const historyData = @json($history);
            
            if (historyData.length === 0) {
                showNotification('Tidak ada data riwayat untuk di-export.', 'warning');
                return;
            }

            showLoading('Mengekspor data...');

            try {
                let csvContent = "Nama File,Tanggal Analisis,Skor,Status,Jenis Dokumen\n";
                
                historyData.forEach(item => {
                    const status = item.score >= 8 ? 'LAYAK DIAJUKAN' : 
                                item.score >= 6 ? 'PERLU PERBAIKAN' : 'TIDAK LAYAK';
                    const date = new Date(item.date).toLocaleDateString('id-ID');
                    const documentType = item.document_type || 'Tidak Diketahui';
                    
                    csvContent += `"${item.filename}","${date}",${item.score},"${status}","${documentType}"\n`;
                });

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                
                a.href = url;
                a.download = `riwayat-analisis-${new Date().getTime()}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                hideLoading();
                showNotification(`Data berhasil diexport (${historyData.length} records)`, 'success');
            } catch (error) {
                hideLoading();
                showNotification('Terjadi kesalahan saat export: ' + error.message, 'error');
            }
        }

        function downloadResults(filename) {
            console.log('Downloading results for:', filename);
            
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            
            // Show loading state on button
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyiapkan...';
            button.disabled = true;
            
            try {
                // Ambil data dari card yang diklik
                const itemElement = document.querySelector(`[data-filename="${filename}"]`);
                if (!itemElement) {
                    throw new Error('Data tidak ditemukan');
                }

                // Extract data dari card
                const score = parseFloat(itemElement.querySelector('.text-2xl.font-bold').textContent);
                const status = itemElement.querySelector('.font-medium').textContent.trim();
                const date = itemElement.querySelector('.bg-blue-100').textContent.replace('Kalender', '').trim();
                const documentType = itemElement.querySelector('.bg-purple-100')?.textContent.trim() || 'Tidak Diketahui';
                
                // Get file extension - FIXED VERSION
                const fileExtension = getFileExtension(filename);

                // Buat struktur data seperti di result page
                const resultsData = {
                    metadata: {
                        title: filename,
                        author: "Sistem Deteksi Format ITS",
                        page_count: "Tidak Diketahui",
                        file_size: "Tidak Diketahui", 
                        file_format: fileExtension.toUpperCase(),
                        analysis_date: new Date().toLocaleString('id-ID')
                    },
                    abstract: {
                        found: false,
                        id_word_count: 0,
                        en_word_count: 0,
                        status: "warning",
                        message: "Data abstrak tidak tersedia"
                    },
                    format: {
                        font_family: "Times New Roman",
                        line_spacing: "1",
                        status: "warning", 
                        message: "Format diasumsikan sesuai standar ITS"
                    },
                    margin: {
                        top: "3.0",
                        bottom: "2.5",
                        left: "3.0", 
                        right: "2.0",
                        status: "warning",
                        message: "Margin diasumsikan sesuai standar ITS"
                    },
                    chapters: {
                        bab1: false,
                        bab2: false,
                        bab3: false,
                        bab4: false,
                        bab5: false,
                        status: "warning",
                        message: "Struktur bab tidak tersedia"
                    },
                    references: {
                        count: 0,
                        min_references: 20,
                        apa_compliant: false,
                        status: "warning",
                        message: "Data referensi tidak tersedia"
                    },
                    cover: {
                        found: false,
                        status: "warning",
                        message: "Data cover tidak tersedia"
                    },
                    overall_score: score,
                    document_type: documentType,
                    recommendations: [
                        "Data lengkap hanya tersedia di halaman hasil analisis",
                        "Untuk melihat analisis detail, buka halaman hasil analisis",
                        `Skor dokumen: ${score}/10 (${status})`,
                        `Dianalisis pada: ${date}`
                    ],
                    download_info: {
                        downloaded_from: "History Page",
                        download_date: new Date().toISOString(),
                        original_filename: filename
                    }
                };

                // Create and download JSON file
                const blob = new Blob([JSON.stringify(resultsData, null, 2)], { 
                    type: 'application/json;charset=utf-8' 
                });
                
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                
                // Create safe filename for download
                const safeFilename = filename.replace(/\.[^/.]+$/, "").replace(/[^a-zA-Z0-9]/g, "_");
                a.download = `hasil-analisis-${safeFilename}-${new Date().getTime()}.json`;
                
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);
                
                // Show success state
                button.innerHTML = '<i class="fas fa-check mr-2"></i>Terdownload!';
                showNotification('Hasil analisis berhasil diunduh!', 'success');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 2000);
                
            } catch (error) {
                console.error('Download error:', error);
                
                // Show error state
                button.innerHTML = '<i class="fas fa-times mr-2"></i>Gagal!';
                showNotification('Gagal mengunduh hasil: ' + error.message, 'error');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }, 2000);
            }
        }

        // Helper function untuk get file extension - FIXED
        function getFileExtension(filename) {
            return filename.split('.').pop() || 'unknown';
        }

        // Helper function untuk get filename tanpa extension
        function getFileNameWithoutExtension(filename) {
            return filename.replace(/\.[^/.]+$/, "");
        }

        // Helper function untuk get file extension
        function pathinfo(filename) {
            return filename.split('.').pop();
        }

        // ==================== API FUNCTIONS ====================
        async function performClearHistory() {
            showLoading('Menghapus semua riwayat...');
            
            try {
                const response = await fetch('{{ route("history.clear") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                });
                
                const data = await response.json();
                hideLoading();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Gagal menghapus riwayat', 'error');
                }
            } catch (error) {
                hideLoading();
                showNotification('Terjadi kesalahan: ' + error.message, 'error');
            }
        }

        async function performClearOldHistory() {
            showLoading('Menghapus riwayat lama...');
            
            try {
                const response = await fetch('{{ route("history.clear.old") }}?days=30', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                });
                
                const data = await response.json();
                hideLoading();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    if (data.deleted_count > 0) {
                        setTimeout(() => location.reload(), 1500);
                    }
                } else {
                    showNotification(data.message || 'Gagal menghapus riwayat lama', 'error');
                }
            } catch (error) {
                hideLoading();
                showNotification('Terjadi kesalahan: ' + error.message, 'error');
            }
        }

        async function performDeleteItem(filename) {
            showLoading('Menghapus riwayat...');
            
            try {
                const encodedFilename = encodeURIComponent(filename);
                const response = await fetch(`/history/delete/${encodedFilename}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                    }
                });
                
                const data = await response.json();
                hideLoading();
                
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Remove from UI
                    const itemElement = document.querySelector(`[data-filename="${filename}"]`);
                    if (itemElement) {
                        itemElement.style.transition = 'all 0.3s ease';
                        itemElement.style.opacity = '0';
                        itemElement.style.height = '0';
                        itemElement.style.margin = '0';
                        itemElement.style.padding = '0';
                        itemElement.style.overflow = 'hidden';
                        
                        setTimeout(() => {
                            itemElement.remove();
                            checkEmptyState();
                        }, 300);
                    }
                } else {
                    showNotification(data.message || 'Gagal menghapus riwayat', 'error');
                }
            } catch (error) {
                hideLoading();
                showNotification('Terjadi kesalahan: ' + error.message, 'error');
            }
        }

        function checkEmptyState() {
            const remainingItems = document.querySelectorAll('.history-card').length;
            if (remainingItems === 0) {
                setTimeout(() => location.reload(), 1000);
            }
        }

        // ==================== INITIALIZATION ====================
        document.addEventListener('DOMContentLoaded', function() {
            
            // Initialize modal events
            document.getElementById('confirmOk').addEventListener('click', function() {
                
                if (window.currentAction === 'clearAll') {
                    performClearHistory();
                } else if (window.currentAction === 'clearOld') {
                    performClearOldHistory();
                } else if (window.currentAction === 'deleteItem') {
                    performDeleteItem(window.currentFilename);
                }
                hideConfirmModal();
            });
            
            document.getElementById('confirmCancel').addEventListener('click', function() {
                hideConfirmModal();
            });
            
            document.getElementById('confirmModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideConfirmModal();
                }
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    hideConfirmModal();
                }
            });

          // Mobile menu toggle (integrated)
          const mobileBtn = document.getElementById('mobileMenuButton');
          const mobileMenu = document.getElementById('mobileMenu');
          if (mobileBtn && mobileMenu) {
            mobileBtn.addEventListener('click', (ev) => {
              ev.stopPropagation();
              mobileMenu.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
              if (!e.target.closest('#mobileMenu') && !e.target.closest('#mobileMenuButton') && mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
              }
            });
          }
        });

        // ==================== GLOBAL FUNCTIONS ====================
        // Make sure these are available globally
        window.clearHistory = clearHistory;
        window.clearOldHistory = clearOldHistory;
        window.exportHistory = exportHistory;
        window.deleteHistoryItem = deleteHistoryItem;
        window.downloadResults = downloadResults;
        window.showConfirmModal = showConfirmModal;
        window.hideConfirmModal = hideConfirmModal;
        window.showNotification = showNotification;
        window.hideNotification = hideNotification;
    </script>