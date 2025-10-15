<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload TA - Deteksi Format ITS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }
    
    .file-upload-container {
      transition: all 0.3s ease;
    }
    
    .file-upload-container.drag-over {
      transform: scale(1.02);
      border-color: #667eea;
      background-color: #f0f4ff;
    }
    
    .progress-bar {
      transition: width 0.5s ease-in-out;
    }
    
    .requirement-item {
      transition: all 0.3s ease;
    }
    
    .requirement-item:hover {
      transform: translateX(5px);
    }
    
    /* Animasi untuk notifikasi */
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
      from { transform: translateX(0); opacity: 1; }
      to { transform: translateX(100%); opacity: 0; }
    }
    
    .notification {
      animation: slideIn 0.3s ease-out;
    }
    
    .notification.hide {
      animation: slideOut 0.3s ease-in;
    }

    /* Navbar styles */
    .navbar {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, 0.95);
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
  </style>
</head>
<body class="flex flex-col min-h-screen">
  <!-- Navbar (desktop + mobile toggle) -->
  <nav class="navbar shadow-lg border-b border-gray-200/50 sticky top-0 z-50 will-change-transform">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <!-- Logo -->
          <div class="flex-shrink-0 flex items-center">
            <a href="{{ url('/') }}" class="flex items-center">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg flex items-center justify-center shadow-lg">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
              </div>
              <span class="ml-3 text-xl font-bold text-gray-800">FormatCheck ITS</span>
            </a>
          </div>

          <!-- Navigation Links (desktop) -->
          <div class="hidden md:ml-6 md:flex md:space-x-8">
            <a href="{{ route('upload.form') }}" class="border-b-2 border-blue-500 text-gray-900 inline-flex items-center px-1 pt-1 text-sm font-medium">
              <i class="fas fa-upload mr-2"></i>
              Upload TA
            </a>
            <a href="{{ route('history') }}" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
              <i class="fas fa-history mr-2"></i>
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
    <div id="mobileMenu" class="md:hidden hidden border-t border-gray-200 bg-white/95 backdrop-blur-lg">
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
            <a href="{{ route('login.form') }}" class="nav-link block pl-4 pr-4 py-3 border-l-4 text-base font-medium text-gray-700 hover:text-blue-600 hover:border-blue-600 hover:bg-blue-50 transition-all duration-200">
              <i class="fas fa-right-to-bracket mr-3"></i>Login
            </a>
          @endguest
        </div>
      </div>
    </div>
  </nav>

  <!-- Notifikasi -->
  <div id="notification" class="fixed top-20 right-4 z-50 hidden max-w-sm">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-lg notification">
      <div class="flex items-start">
        <i class="fas fa-exclamation-triangle mt-1 mr-3 flex-shrink-0"></i>
        <span class="block flex-1" id="notification-message"></span>
      </div>
      <button onclick="hideNotification()" class="absolute top-2 right-2 p-1 text-red-500 hover:text-red-700">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
  </div>

  <!-- Success Notification -->
  <div id="success-notification" class="fixed top-20 right-4 z-50 hidden max-w-sm">
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-lg notification">
      <div class="flex items-start">
        <i class="fas fa-check-circle mt-1 mr-3 flex-shrink-0"></i>
        <span class="block flex-1" id="success-message"></span>
      </div>
      <button onclick="hideSuccessNotification()" class="absolute top-2 right-2 p-1 text-green-500 hover:text-green-700">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
  </div>

  <!-- Main Content -->
  <main class="flex-grow flex items-center justify-center p-4 py-8">
    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-6xl">
      <!-- Header -->
      <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white text-center">
        <h1 class="text-3xl font-bold mb-2">Cek Format Tugas Akhir ITS</h1>
        <p class="text-blue-100">Unggah file tugas akhir untuk memeriksa kelengkapan format sesuai panduan ITS</p>
      </div>
      
      <div class="md:flex">
        <!-- Upload Section -->
        <div class="md:w-1/2 p-8 border-r border-gray-200">
          <form action="{{ route('upload.process') }}" method="POST" enctype="multipart/form-data" class="space-y-6" id="uploadForm">
            @csrf
            
            <div class="file-upload-container border-2 border-dashed border-gray-300 rounded-xl p-8 text-center cursor-pointer transition-all duration-300 hover:border-blue-400 hover:bg-blue-50"
                 id="dropArea">
              <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                  <i class="fas fa-file-upload text-blue-500 text-2xl"></i>
                </div>
              </div>
              <p class="text-gray-600 mb-2">Tarik file ke sini atau klik untuk mengupload</p>
              <p class="text-xs text-gray-400 mb-4">Format yang didukung: PDF, DOC, DOCX (Maks. 10MB)</p>
              <input id="file" name="file" type="file" class="hidden" required accept=".pdf,.doc,.docx">
              <label for="file" class="inline-block px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition cursor-pointer font-medium">
                Pilih File
              </label>
            </div>

            <!-- File Preview -->
            <div id="file-preview" class="p-4 bg-gray-50 rounded-lg hidden border border-gray-200">
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <i class="far fa-file-pdf text-red-500 text-2xl mr-3"></i>
                  <div>
                    <p id="file-name" class="font-medium text-gray-800 text-sm"></p>
                    <p id="file-size" class="text-xs text-gray-500 mt-1"></p>
                  </div>
                </div>
                <button type="button" id="remove-file" class="text-gray-400 hover:text-gray-600 transition">
                  <i class="fas fa-times"></i>
                </button>
              </div>
            </div>

            <!-- Progress Bar (Initially Hidden) -->
            <div id="progress-container" class="hidden">
              <div class="flex justify-between mb-2">
                <span class="text-sm font-medium text-blue-600">Mengupload...</span>
                <span id="progress-percentage" class="text-sm font-medium text-blue-600">0%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div id="progress-bar" class="progress-bar bg-blue-600 h-2.5 rounded-full transition-all duration-300" style="width: 0%"></div>
              </div>
              <p class="text-xs text-gray-500 mt-2 text-center">Harap tunggu, file sedang diproses...</p>
            </div>

            <button type="submit" id="submit-btn" class="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg font-semibold shadow-md hover:from-blue-600 hover:to-purple-600 transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-search mr-2"></i> 
              <span id="submit-text">Analisis Dokumen</span>
              <i id="submit-loading" class="fas fa-spinner fa-spin ml-2 hidden"></i>
            </button>

            <!-- Error Messages -->
            @if($errors->any())
              <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center mb-2">
                  <i class="fas fa-exclamation-circle mr-2"></i>
                  <strong class="font-medium">Terjadi kesalahan:</strong>
                </div>
                <ul class="list-disc list-inside text-sm">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            @if(session('error'))
              <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-start">
                <i class="fas fa-exclamation-circle mt-0.5 mr-3 flex-shrink-0"></i>
                <div>{{ session('error') }}</div>
              </div>
            @endif

            @if(session('success'))
              <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-start">
                <i class="fas fa-check-circle mt-0.5 mr-3 flex-shrink-0"></i>
                <div>{{ session('success') }}</div>
              </div>
            @endif

            @if(session('info'))
              <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg flex items-start">
                <i class="fas fa-info-circle mt-0.5 mr-3 flex-shrink-0"></i>
                <div>{{ session('info') }}</div>
              </div>
            @endif
          </form>

          <!-- Recent Uploads -->
          @if(isset($recentUploads) && count($recentUploads) > 0)
          <div class="mt-8">
            <h3 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
              <i class="fas fa-history mr-2 text-blue-500"></i> Upload Terbaru
            </h3>
            <div class="space-y-2 max-h-100 overflow-y-auto custom-scrollbar pr-2">
              @foreach($recentUploads as $recent)
              <a href="{{ $recent['url'] ?? '#' }}" class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition group">
                <div class="flex items-center min-w-0 flex-1">
                  <i class="far fa-file-pdf text-red-500 mr-3 text-lg flex-shrink-0"></i>
                  <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-800 truncate" title="{{ $recent['name'] }}">
                      {{ \Illuminate\Support\Str::limit($recent['name'], 25) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">{{ $recent['size'] }}</p>
                  </div>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap ml-2 group-hover:text-blue-500 transition">
                  {{ \Illuminate\Support\Carbon::parse($recent['uploaded_at'])->diffForHumans() }}
                </span>
              </a>
              @endforeach
            </div>
          </div>
          @endif
        </div>
        
        <!-- Requirements Section -->
        <div class="md:w-1/2 bg-gray-50 p-8">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Kelengkapan Format ITS</h2>
          <p class="text-gray-600 mb-6">Sistem memeriksa dokumen berdasarkan panduan format ITS:</p>
          
          <div class="space-y-4 mb-6">
            <div class="requirement-item flex items-start p-3 rounded-lg hover:bg-white transition">
              <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3 mt-1">
                <i class="fas fa-file-alt text-green-500 text-sm"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-medium text-gray-800">Struktur Dokumen</h3>
                <p class="text-sm text-gray-600 mt-1">Cover, Abstrak (ID+EN), Daftar Isi, Bab 1-3 (Proposal) / Bab 1-5 (Laporan)</p>
              </div>
            </div>
            
            <div class="requirement-item flex items-start p-3 rounded-lg hover:bg-white transition">
              <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-1">
                <i class="fas fa-font text-blue-500 text-sm"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-medium text-gray-800">Format Teks</h3>
                <p class="text-sm text-gray-600 mt-1">Times New Roman 12pt, spasi 1, margin 3-2.5-3-2cm</p>
              </div>
            </div>
            
            <div class="requirement-item flex items-start p-3 rounded-lg hover:bg-white transition">
              <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3 mt-1">
                <i class="fas fa-book text-purple-500 text-sm"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-medium text-gray-800">Abstrak</h3>
                <p class="text-sm text-gray-600 mt-1">200-300 kata, Bahasa Indonesia & Inggris</p>
              </div>
            </div>
            
            <div class="requirement-item flex items-start p-3 rounded-lg hover:bg-white transition">
              <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3 mt-1">
                <i class="fas fa-list-ol text-yellow-500 text-sm"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-medium text-gray-800">Bab 1 Pendahuluan</h3>
                <p class="text-sm text-gray-600 mt-1">Latar belakang, rumusan masalah, batasan, tujuan, manfaat</p>
              </div>
            </div>
            
            <div class="requirement-item flex items-start p-3 rounded-lg hover:bg-white transition">
              <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3 mt-1">
                <i class="fas fa-graduation-cap text-red-500 text-sm"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-medium text-gray-800">Daftar Pustaka</h3>
                <p class="text-sm text-gray-600 mt-1">Format APA Edisi ke-7, sitasi konsisten</p>
              </div>
            </div>

            <div class="requirement-item flex items-start p-3 rounded-lg hover:bg-white transition">
              <div class="flex-shrink-0 w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3 mt-1">
                <i class="fas fa-palette text-indigo-500 text-sm"></i>
              </div>
              <div class="flex-1">
                <h3 class="font-medium text-gray-800">Cover & Halaman Judul</h3>
                <p class="text-sm text-gray-600 mt-1">Background biru ITS, font Trebuchet MS, teks putih</p>
              </div>
            </div>
          </div>
          
          <!-- Tips Section -->
          <div class="p-4 bg-blue-50 rounded-lg border border-blue-200 mb-6">
            <div class="flex">
              <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-lg mt-0.5"></i>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Panduan Format ITS</h3>
                <p class="text-sm text-blue-600 mt-1">Pastikan dokumen mengikuti Buku Panduan Format Tugas Akhir ITS. Cover menggunakan Trebuchet MS dengan background biru ITS.</p>
              </div>
            </div>
          </div>

          <!-- Quick Stats -->
          <div class="grid grid-cols-2 gap-4 text-center">
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
              <div class="text-2xl font-bold text-blue-600">5</div>
              <div class="text-sm text-gray-600 mt-1">Bab Wajib</div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
              <div class="text-2xl font-bold text-green-600">200-300</div>
              <div class="text-sm text-gray-600 mt-1">Kata Abstrak</div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
              <div class="text-2xl font-bold text-purple-600">A4</div>
              <div class="text-sm text-gray-600 mt-1">Ukuran Kertas</div>
            </div>
            <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
              <div class="text-2xl font-bold text-orange-600">APA 7</div>
              <div class="text-sm text-gray-600 mt-1">Format Pustaka</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="max-w-6xl mx-auto px-4">
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
    document.addEventListener('DOMContentLoaded', function() {
      const fileInput = document.getElementById('file');
      const dropArea = document.getElementById('dropArea');
      const filePreview = document.getElementById('file-preview');
      const fileName = document.getElementById('file-name');
      const fileSize = document.getElementById('file-size');
      const removeFileBtn = document.getElementById('remove-file');
      const progressContainer = document.getElementById('progress-container');
      const progressBar = document.getElementById('progress-bar');
      const progressPercentage = document.getElementById('progress-percentage');
      const submitBtn = document.getElementById('submit-btn');
      const submitText = document.getElementById('submit-text');
      const submitLoading = document.getElementById('submit-loading');
      const uploadForm = document.getElementById('uploadForm');
      const notification = document.getElementById('notification');
      const notificationMessage = document.getElementById('notification-message');
      const successNotification = document.getElementById('success-notification');
      const successMessage = document.getElementById('success-message');
      
      // Drag and drop functionality
      ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
      });
      
      function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
      }
      
      ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
      });
      
      ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
      });
      
      function highlight() {
        dropArea.classList.add('drag-over');
      }
      
      function unhighlight() {
        dropArea.classList.remove('drag-over');
      }
      
      dropArea.addEventListener('drop', handleDrop, false);
      
      function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        
        if (files.length) {
          fileInput.files = files;
          handleFiles(files);
        }
      }
      
      fileInput.addEventListener('change', function() {
        handleFiles(this.files);
      });
      
      removeFileBtn.addEventListener('click', function() {
        fileInput.value = '';
        filePreview.classList.add('hidden');
        dropArea.classList.remove('hidden');
        submitBtn.disabled = false;
      });
      
      function handleFiles(files) {
        if (files.length > 0) {
          const file = files[0];
          const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
          const validExtensions = ['.pdf', '.doc', '.docx'];
          
          // Check file extension as fallback
          const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
          const isValidType = validTypes.includes(file.type) || validExtensions.includes(fileExtension);
          
          if (!isValidType) {
            showNotification('Format file tidak didukung. Silakan upload file PDF (.pdf) atau Word (.doc, .docx).');
            fileInput.value = '';
            return;
          }

          
          if (file.size === 0) {
            showNotification('File kosong. Silakan pilih file lain.');
            fileInput.value = '';
            return;
          }
          
          // Display file info
          fileName.textContent = file.name;
          fileSize.textContent = formatFileSize(file.size);
          filePreview.classList.remove('hidden');
          
          // Change icon based on file type
          const fileIcon = filePreview.querySelector('i');
          if (file.type === 'application/pdf' || fileExtension === '.pdf') {
            fileIcon.className = 'far fa-file-pdf text-red-500 text-2xl mr-3';
          } else {
            fileIcon.className = 'far fa-file-word text-blue-500 text-2xl mr-3';
          }

          // Show success message for valid file
          showSuccessNotification('File valid. Klik "Analisis Dokumen" untuk melanjutkan.');
        }
      }
      
      function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
      }
      
      // Form submission with progress simulation
      // Form submission with chunked upload
      uploadForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!fileInput.files.length) {
          showNotification('Silakan pilih file terlebih dahulu.');
          return;
        }

        const file = fileInput.files[0];
        const chunkSize = 1 * 1024 * 1024; // 1MB per chunk
        const totalChunks = Math.ceil(file.size / chunkSize);

        progressContainer.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressPercentage.textContent = '0%';
        submitBtn.disabled = true;
        submitText.textContent = 'Mengupload...';
        submitLoading.classList.remove('hidden');

        const uploadId = Date.now() + '_' + file.name; // unique upload session ID
        let uploadedChunks = 0;

        for (let start = 0; start < file.size; start += chunkSize) {
          const end = Math.min(file.size, start + chunkSize);
          const chunk = file.slice(start, end);
          const formData = new FormData();
          formData.append('file', chunk);
          formData.append('uploadId', uploadId);
          formData.append('fileName', file.name);
          formData.append('chunkIndex', Math.floor(start / chunkSize));
          formData.append('totalChunks', totalChunks);

          try {
            const response = await fetch('{{ route('upload.chunk') }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
              },
              body: formData
            });

            if (!response.ok) throw new Error('Gagal upload chunk');
          } catch (error) {
            showNotification('Terjadi kesalahan saat mengupload file.');
            submitBtn.disabled = false;
            submitText.textContent = 'Analisis Dokumen';
            submitLoading.classList.add('hidden');
            return;
          }

          uploadedChunks++;
          const percent = Math.floor((uploadedChunks / totalChunks) * 100);
          progressBar.style.width = percent + '%';
          progressPercentage.textContent = percent + '%';
        }

        // Setelah semua chunk dikirim, minta server untuk gabungkan
        try {
          const mergeResponse = await fetch('{{ route('upload.merge') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value
            },
            body: JSON.stringify({ uploadId, fileName: file.name })
          });

          if (!mergeResponse.ok) throw new Error('Gagal menggabungkan file');

          const result = await mergeResponse.json();

          // Jika sukses, redirect ke halaman hasil
          if (result.success && result.filename) {
              window.location.href = `/results/${result.filename}`;
          } else {
              showNotification(result.message || 'Gagal menyelesaikan upload.');
          }


        } catch (error) {
          showNotification('Terjadi kesalahan saat menggabungkan file.');
        } finally {
          submitBtn.disabled = false;
          submitText.textContent = 'Analisis Dokumen';
          submitLoading.classList.add('hidden');
        }
      });

      
      // Notification functions
      window.showNotification = function(message) {
        notificationMessage.textContent = message;
        notification.classList.remove('hidden');
        notification.classList.remove('hide');
        
        // Auto hide after 5 seconds
        setTimeout(() => {
          hideNotification();
        }, 5000);
      };
      
      window.hideNotification = function() {
        notification.classList.add('hide');
        setTimeout(() => {
          notification.classList.add('hidden');
        }, 300);
      };

      window.showSuccessNotification = function(message) {
        successMessage.textContent = message;
        successNotification.classList.remove('hidden');
        successNotification.classList.remove('hide');
        
        // Auto hide after 3 seconds
        setTimeout(() => {
          hideSuccessNotification();
        }, 3000);
      };
      
      window.hideSuccessNotification = function() {
        successNotification.classList.add('hide');
        setTimeout(() => {
          successNotification.classList.add('hidden');
        }, 300);
      };

      // Auto-hide success messages after page load
      @if(session('success') || session('info'))
        setTimeout(() => {
          const successElements = document.querySelectorAll('.bg-green-100, .bg-blue-100');
          successElements.forEach(el => {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
          });
        }, 5000);
      @endif
      
      // Mobile menu toggle (integrated)
      const mobileBtn = document.getElementById('mobileMenuButton');
      const mobileMenu = document.getElementById('mobileMenu');
      if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', (ev) => {
          ev.stopPropagation();
          mobileMenu.classList.toggle('hidden');
        });

        // Close when clicking outside (only when open)
        document.addEventListener('click', (e) => {
          if (!e.target.closest('#mobileMenu') && !e.target.closest('#mobileMenuButton') && mobileMenu && !mobileMenu.classList.contains('hidden')) {
            mobileMenu.classList.add('hidden');
          }
        });
      }
    });
  </script>
</body>
</html>