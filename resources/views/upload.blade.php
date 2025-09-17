<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload TA - Deteksi Format</title>
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
  </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">
  <!-- Notifikasi -->
  <div id="notification" class="fixed top-4 right-4 z-50 hidden">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative shadow-lg notification">
      <span class="block sm:inline" id="notification-message"></span>
      <button onclick="hideNotification()" class="absolute top-0 right-0 p-2">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>

  <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white text-center">
      <h1 class="text-3xl font-bold mb-2">Cek Format Tugas Akhir</h1>
      <p class="text-blue-100">Unggah file tugas akhir kamu untuk memeriksa kelengkapan format</p>
    </div>
    
    <div class="md:flex">
      <!-- Upload Section -->
      <div class="md:w-1/2 p-8">
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
            <label for="file" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition cursor-pointer">
              Pilih File
            </label>
          </div>

          <!-- File Preview -->
          <div id="file-preview" class="p-4 bg-gray-50 rounded-lg hidden">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <i class="far fa-file-pdf text-red-500 text-2xl mr-3"></i>
                <div>
                  <p id="file-name" class="font-medium text-gray-800"></p>
                  <p id="file-size" class="text-sm text-gray-500"></p>
                </div>
              </div>
              <button type="button" id="remove-file" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <!-- Progress Bar (Initially Hidden) -->
          <div id="progress-container" class="hidden">
            <div class="flex justify-between mb-1">
              <span class="text-sm font-medium text-blue-600">Mengupload...</span>
              <span id="progress-percentage" class="text-sm font-medium text-blue-600">0%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
              <div id="progress-bar" class="progress-bar bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
            </div>
          </div>

          <button type="submit" id="submit-btn" class="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg font-semibold shadow-md hover:from-blue-600 hover:to-purple-600 transition-all flex items-center justify-center">
            <i class="fas fa-search mr-2"></i> Analisis Dokumen
          </button>

          <!-- Error Messages -->
          @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
              <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
              {{ session('error') }}
            </div>
          @endif
        </form>
      </div>
      
      <!-- Requirements Section -->
      <div class="md:w-1/2 bg-gray-50 p-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Kelengkapan yang Diperiksa</h2>
        <p class="text-gray-600 mb-6">Sistem akan memeriksa dokumen Anda berdasarkan kriteria berikut:</p>
        
        <div class="space-y-4">
          <div class="requirement-item flex items-start">
            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
              <i class="fas fa-check text-green-500"></i>
            </div>
            <div>
              <h3 class="font-medium text-gray-800">Abstrak</h3>
              <p class="text-sm text-gray-600">Minimal 200 kata, maksimal 300 kata</p>
            </div>
          </div>
          
          <div class="requirement-item flex items-start">
            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
              <i class="fas fa-list text-blue-500"></i>
            </div>
            <div>
              <h3 class="font-medium text-gray-800">Daftar Isi</h3>
              <p class="text-sm text-gray-600">Format yang konsisten dan lengkap</p>
            </div>
          </div>
          
          <div class="requirement-item flex items-start">
            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
              <i class="fas fa-file-alt text-purple-500"></i>
            </div>
            <div>
              <h3 class="font-medium text-gray-800">Bab 1 Pendahuluan</h3>
              <p class="text-sm text-gray-600">Latar belakang, rumusan masalah, tujuan</p>
            </div>
          </div>
          
          <div class="requirement-item flex items-start">
            <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mr-3">
              <i class="fas fa-ruler text-yellow-500"></i>
            </div>
            <div>
              <h3 class="font-medium text-gray-800">Format Margin</h3>
              <p class="text-sm text-gray-600">Atas: 4cm, Bawah: 3cm, Kiri: 4cm, Kanan: 3cm</p>
            </div>
          </div>
          
          <div class="requirement-item flex items-start">
            <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
              <i class="fas fa-book text-red-500"></i>
            </div>
            <div>
              <h3 class="font-medium text-gray-800">Daftar Pustaka</h3>
              <p class="text-sm text-gray-600">Minimal 20 referensi, format konsisten</p>
            </div>
          </div>
        </div>
        
        <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
          <div class="flex">
            <div class="flex-shrink-0">
              <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-3">
              <h3 class="text-sm font-medium text-blue-800">Tips</h3>
              <p class="text-sm text-blue-600 mt-1">Pastikan dokumen tidak dipassword dan dapat dibuka untuk hasil analisis yang optimal.</p>
            </div>
          </div>
        </div>

        <!-- Recent Uploads (jika ada) -->
        @if(isset($recentUploads) && count($recentUploads) > 0)
        <div class="mt-6">
        <h3 class="text-md font-semibold text-gray-800 mb-3 flex items-center">
            <i class="fas fa-history mr-2 text-blue-500"></i> Upload Terbaru
        </h3>
        <div class="space-y-2 max-h-40 overflow-y-auto">
            @foreach($recentUploads as $recent)
            <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition">
            <div class="flex items-center truncate">
                <i class="far fa-file-pdf text-red-500 mr-3"></i>
                <div class="truncate">
                <p class="text-sm font-medium text-gray-800 truncate" title="{{ $recent['name'] }}">
                    {{ \Illuminate\Support\Str::limit($recent['name'], 20) }}
                </p>
                <p class="text-xs text-gray-500">{{ $recent['size'] }}</p>
                </div>
            </div>
            <span class="text-xs text-gray-400 whitespace-nowrap">{{ \Illuminate\Support\Carbon::parse($recent['uploaded_at'])->diffForHumans() }}</span>
            </div>
            @endforeach
        </div>
        </div>
        @endif
      </div>
    </div>
  </div>

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
      const uploadForm = document.getElementById('uploadForm');
      const notification = document.getElementById('notification');
      const notificationMessage = document.getElementById('notification-message');
      
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
      });
      
      function handleFiles(files) {
        if (files.length > 0) {
          const file = files[0];
          const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
          
          if (!validTypes.includes(file.type)) {
            showNotification('Format file tidak didukung. Silakan upload file PDF atau Word.');
            return;
          }
          
          if (file.size > 10 * 1024 * 1024) {
            showNotification('Ukuran file terlalu besar. Maksimal 10MB.');
            return;
          }
          
          // Display file info
          fileName.textContent = file.name;
          fileSize.textContent = formatFileSize(file.size);
          filePreview.classList.remove('hidden');
          
          // Change icon based on file type
          const fileIcon = filePreview.querySelector('i');
          if (file.type === 'application/pdf') {
            fileIcon.className = 'far fa-file-pdf text-red-500 text-2xl mr-3';
          } else {
            fileIcon.className = 'far fa-file-word text-blue-500 text-2xl mr-3';
          }
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
      uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!fileInput.files.length) {
          showNotification('Silakan pilih file terlebih dahulu.');
          return;
        }
        
        // Show progress bar
        progressContainer.classList.remove('hidden');
        submitBtn.disabled = true;
        
        // Simulate progress
        let width = 0;
        const interval = setInterval(() => {
          if (width >= 100) {
            clearInterval(interval);
            // Submit the form after progress completes
            uploadForm.submit();
          } else {
            width += 5;
            progressBar.style.width = width + '%';
            progressPercentage.textContent = width + '%';
          }
        }, 100);
      });
      
      // Notification functions
      window.showNotification = function(message) {
        notificationMessage.textContent = message;
        notification.classList.remove('hidden');
        notification.classList.remove('hide');
        notification.classList.add('notification');
        
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
    });
  </script>
</body>
</html>
