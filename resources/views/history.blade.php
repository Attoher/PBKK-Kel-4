<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Riwayat Analisis - TAkCekIn ITS</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/loading-its.css') }}">
  <link rel="stylesheet" href="{{ asset('css/history-its.css') }}">
  <link rel="stylesheet" href="{{ asset('css/dark-its.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex flex-col min-h-screen no-horizontal-scroll">
  <!-- Navbar (desktop + mobile toggle) -->
  <nav class="navbar shadow-lg border-b border-gray-200/50 sticky top-0 z-50 will-change-transform">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 safe-area">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <a href="{{ url('/') }}" class="flex items-center">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
              </div>
              <span class="ml-3 text-xl font-bold text-gray-800 break-words">TAkCekIn ITS</span>
            </a>
          </div>

          <!-- Navigation Links (desktop) -->
          <div class="hidden md:ml-6 md:flex md:space-x-8">
            <a href="{{ route('upload.form') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium break-words">
              <i class="fas fa-upload mr-2"></i>
              Upload TA
            </a>
            <a href="{{ route('history') }}" class="border-b-2 border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium break-words">
              <i class="fas fa-history mr-2"></i>
              Riwayat
            </a>
          </div>
        </div>

        <!-- Right side -->
        <div class="flex items-center space-x-3">
          <div class="hidden md:flex items-center space-x-4">
            @auth
              <span class="text-sm text-gray-700 break-words">
                <i class="fas fa-user-circle mr-1"></i>
                {{ Auth::user()->name }}
              </span>
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
          <i class="fas fa-upload mr-3 text-blue-500"></i>Upload TA</a>
        <a href="{{ route('history') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 border-transparent text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words">
          <i class="fas fa-history mr-3 text-purple-500"></i>Riwayat</a>

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
            <a href="{{ route('login.form') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200 break-words">
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
        <p id="loadingMessage" class="text-gray-700 break-words">Memproses...</p>
      </div>
    </div>
  </div>

  <!-- Notification -->
  <div id="notification" class="notification hidden no-print">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-lg" id="notificationContent">
      <div class="flex items-start">
        <i class="fas fa-check-circle mt-1 mr-3 flex-shrink-0"></i>
        <span class="block flex-1 break-words" id="notificationMessage"></span>
      </div>
      <button onclick="hideNotification()" class="absolute top-3 right-3 p-1 text-green-500 hover:text-green-700 transition">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
  </div>

    <!-- Confirm Modal -->
    <div id="confirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 max-w-[90vw] shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2 break-words" id="modalTitle">Konfirmasi Hapus</h3>
                <div class="mt-2 px-4 md:px-7 py-3">
                    <p class="text-sm text-gray-500 break-words" id="modalMessage">Apakah Anda yakin ingin menghapus?</p>
                    <div id="modalDetails" class="text-xs text-gray-400 mt-2 text-left bg-gray-50 p-2 rounded max-h-32 overflow-y-auto hidden break-words"></div>
                </div>
                <div class="flex justify-center space-x-4 mt-4 flex-wrap gap-2">
                    <button id="confirmCancel" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition font-medium break-words">
                        Batal
                    </button>
                    <button id="confirmOk" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition font-medium break-words">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

  <!-- Main Content -->
  <main class="flex-grow py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 safe-area">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2 break-words">Riwayat Analisis Dokumen</h1>
        <p class="text-blue-100 text-lg break-words">Lihat sejarah analisis format Tugas Akhir Anda</p>
      </div>

      <!-- Stats Cards -->
      <div class="flex gap-4 mb-8 md:grid md:grid-cols-4 md:gap-6 overflow-x-auto pb-2 md:pb-0 custom-scrollbar">
        <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up w-64 flex-shrink-0" style="animation-delay: 0.1s">
          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-file-pdf text-blue-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-gray-800 break-words">{{ count($history) }}</div>
          <div class="text-sm text-gray-600 break-words">Total Analisis</div>
        </div>
        
        <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up w-64 flex-shrink-0" style="animation-delay: 0.2s">
          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-gray-800 break-words">
            {{ collect($history)->where('score', '>=', 8)->count() }}
          </div>
          <div class="text-sm text-gray-600 break-words">Layak Ajukan</div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up w-64 flex-shrink-0" style="animation-delay: 0.3s">
          <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-gray-800 break-words">
            {{ collect($history)->whereBetween('score', [6, 7.9])->count() }}
          </div>
          <div class="text-sm text-gray-600 break-words">Perlu Perbaikan</div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 text-center animate-fade-in-up w-64 flex-shrink-0" style="animation-delay: 0.4s">
          <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
            <i class="fas fa-times-circle text-red-500 text-xl"></i>
          </div>
          <div class="text-2xl font-bold text-gray-800 break-words">
            {{ collect($history)->where('score', '<', 6)->count() }}
          </div>
          <div class="text-sm text-gray-600 break-words">Tidak Layak</div>
        </div>
      </div>

      <!-- History List -->
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h2 class="text-xl font-bold text-gray-800 mb-2 sm:mb-0 break-words">Dokumen yang Telah Dianalisis</h2>
            <div class="flex flex-wrap gap-2">
              <button type="button" onclick="clearOldHistory()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center no-print break-words">
                    <i class="fas fa-clock mr-2"></i>Hapus Lama
              </button>
              <button onclick="exportHistory()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center no-print break-words">
                <i class="fas fa-download mr-2"></i>Export CSV
              </button>
              <button type="button" onclick="clearHistory()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center no-print break-words">
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
                        <h3 class="text-lg font-semibold text-gray-800 filename-truncate" title="{{ $item['filename'] }}">
                          {{ $item['filename'] }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 break-words">
                            <i class="fas fa-calendar mr-1"></i>
                            {{ \Illuminate\Support\Carbon::parse($item['date'])->format('d M Y H:i') }}
                          </span>
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 break-words">
                            <i class="fas fa-clock mr-1"></i>
                            {{ \Illuminate\Support\Carbon::parse($item['date'])->diffForHumans() }}
                          </span>
                          @if(isset($item['document_type']) && $item['document_type'] !== 'Tidak Diketahui')
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 break-words">
                            <i class="fas fa-file-alt mr-1"></i>
                            {{ $item['document_type'] }}
                          </span>
                          @endif
                          @if(!($item['file_exists'] ?? true))
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 break-words">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            File Tidak Ditemukan
                          </span>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <div class="flex items-center space-x-4 flex-wrap gap-2">
                    <!-- Score Badge -->
                    <div class="score-badge px-4 py-2 rounded-full font-semibold text-center min-w-24
                        @if($item['score'] >= 8) bg-green-100 text-green-800 border border-green-200
                        @elseif($item['score'] >= 6) bg-yellow-100 text-yellow-800 border border-yellow-200
                        @else bg-red-100 text-red-800 border border-red-200 @endif">
                      <div class="text-2xl font-bold break-words">{{ number_format($item['score'], 1) }}</div>
                      <div class="text-xs break-words">/ 10</div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2 no-print action-buttons">
                      @if($item['file_exists'] ?? true)
                      <a href="{{ route('results', ['filename' => $item['filename']]) }}" 
                         class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center group break-words">
                        <i class="fas fa-eye mr-2 group-hover:scale-110 transition-transform"></i>Lihat
                      </a>
                      <button onclick="downloadResults('{{ $item['filename'] }}')" 
                         class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all flex items-center group break-words">
                        <i class="fas fa-download mr-2 group-hover:scale-110 transition-transform"></i>Download
                      </button>
                      @else
                      <span class="bg-gray-400 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center cursor-not-allowed break-words" title="File tidak tersedia">
                        <i class="fas fa-eye-slash mr-2"></i>File Hilang
                      </span>
                      @endif
                      <button onclick="deleteHistoryItem('{{ $item['filename'] }}')" 
                         class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg text-sm transition-all flex items-center group break-words"
                         title="Hapus riwayat ini">
                        <i class="fas fa-trash group-hover:scale-110 transition-transform"></i>
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Status Bar -->
                <div class="mt-4">
                  <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span class="break-words">Status Kelayakan:</span>
                    <span class="font-medium break-words
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
              <div class="break-words">
                Menampilkan <span class="font-semibold">{{ count($history) }}</span> dari <span class="font-semibold">{{ count($history) }}</span> hasil analisis
              </div>
              <div class="mt-2 sm:mt-0">
                <span class="text-gray-500 break-words">Diurutkan berdasarkan: </span>
                <span class="font-semibold break-words">Tanggal Terbaru</span>
              </div>
            </div>

          @else
            <!-- Empty State -->
            <div class="text-center py-12">
              <div class="empty-state w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-history text-white text-3xl"></i>
              </div>
              <h3 class="text-xl font-semibold text-gray-800 mb-2 break-words">Belum Ada Riwayat Analisis</h3>
              <p class="text-gray-600 mb-6 max-w-md mx-auto break-words">
                Anda belum menganalisis dokumen Tugas Akhir. Upload dokumen pertama Anda untuk memulai analisis format.
              </p>
              <a href="{{ route('upload.form') }}" class="bg-gradient-to-r from-blue-500 to-purple-500 hover:from-blue-600 hover:to-purple-600 text-white font-semibold py-3 px-6 rounded-lg transition-all inline-flex items-center no-print break-words">
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
            <div class="ml-3 min-w-0">
              <h3 class="text-sm font-medium text-yellow-800 break-words">Tips Meningkatkan Skor</h3>
              <div class="text-sm text-yellow-700 mt-2 space-y-1">
                <div class="flex items-center break-words"><i class="fas fa-check mr-2 text-green-500"></i> Pastikan abstrak 200-300 kata (ID & EN)</div>
                <div class="flex items-center break-words"><i class="fas fa-check mr-2 text-green-500"></i> Gunakan font Times New Roman 12pt</div>
                <div class="flex items-center break-words"><i class="fas fa-check mr-2 text-green-500"></i> Atur margin sesuai standar ITS</div>
                <div class="flex items-center break-words"><i class="fas fa-check mr-2 text-green-500"></i> Tambahkan minimal 20 referensi</div>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 animate-fade-in-up">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-chart-line text-blue-500 text-xl mt-1"></i>
            </div>
            <div class="ml-3 min-w-0">
              <h3 class="text-sm font-medium text-blue-800 break-words">Progress Analisis</h3>
              <div class="text-sm text-blue-700 mt-2 space-y-1">
                <div class="flex justify-between break-words">
                  <span>Rata-rata skor:</span>
                  <span class="font-semibold break-words">{{ number_format(collect($history)->avg('score') ?? 0, 1) }}/10</span>
                </div>
                <div class="flex justify-between break-words">
                  <span>Dokumen terbaik:</span>
                  <span class="font-semibold break-words">{{ number_format(collect($history)->max('score') ?? 0, 1) }}/10</span>
                </div>
                <div class="flex justify-between break-words">
                  <span>Tren kualitas:</span>
                  <span class="font-semibold break-words
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
    <div class="max-w-7xl mx-auto px-4 safe-area">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-6 md:mb-0 text-center md:text-left">
          <div class="flex items-center justify-center md:justify-start">
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
        <p class="text-gray-400 text-sm break-words">
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
                        <span class="break-words">${message}</span>
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
                    <p class="text-gray-700 break-words">${message}</p>
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