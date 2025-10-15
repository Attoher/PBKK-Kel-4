<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - FormatCheck ITS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
    
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }
    
    .login-container {
      transition: all 0.3s ease;
    }
    
    .input-group {
      transition: all 0.3s ease;
    }
    
    .input-group:focus-within {
      transform: translateY(-2px);
    }
    
    .card-hover {
      transition: all 0.3s ease;
    }
    
    .card-hover:hover {
      transform: translateY(-5px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }
    
    /* Animasi untuk notifikasi */
    @keyframes slideIn {
      from { transform: translateY(-20px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
    
    @keyframes slideOut {
      from { transform: translateY(0); opacity: 1; }
      to { transform: translateY(-20px); opacity: 0; }
    }
    
    .notification {
      animation: slideIn 0.3s ease-out;
    }
    
    .notification.hide {
      animation: slideOut 0.3s ease-in;
    }

    /* Floating animation */
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-10px); }
      100% { transform: translateY(0px); }
    }
    
    .floating {
      animation: float 5s ease-in-out infinite;
    }
    
    /* Custom checkbox */
    .custom-checkbox {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      width: 18px;
      height: 18px;
      border: 2px solid #d1d5db;
      border-radius: 4px;
      outline: none;
      cursor: pointer;
      position: relative;
      transition: all 0.3s;
    }
    
    .custom-checkbox:checked {
      background-color: #4f46e5;
      border-color: #4f46e5;
    }
    
    .custom-checkbox:checked::after {
      content: '';
      position: absolute;
      left: 4px;
      top: 1px;
      width: 5px;
      height: 10px;
      border: solid white;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }
  </style>
</head>
<body class="flex flex-col min-h-screen">
  <!-- Notifikasi -->
  <div id="notification" class="fixed top-4 right-4 z-50 hidden max-w-sm">
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
  <div id="success-notification" class="fixed top-4 right-4 z-50 hidden max-w-sm">
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
    <div class="flex flex-col md:flex-row items-center justify-center w-full max-w-6xl gap-8">
      <!-- Left side - Branding & Info -->
      <div class="md:w-1/2 text-center md:text-left mb-8 md:mb-0">
        <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-8 text-white card-hover">
          <div class="flex justify-center md:justify-start mb-6">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center floating">
              <i class="fas fa-graduation-cap text-blue-600 text-2xl"></i>
            </div>
          </div>
          <h1 class="text-4xl font-bold mb-4">FormatCheck ITS</h1>
          <p class="text-xl mb-6 text-blue-100">Sistem Deteksi Kelengkapan Format Tugas Akhir</p>
          <p class="text-blue-100 mb-8">Masuk ke akun Anda untuk memeriksa format tugas akhir sesuai panduan ITS dengan mudah dan cepat.</p>
          
          <div class="space-y-4">
            <div class="flex items-center">
              <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-check text-white text-sm"></i>
              </div>
              <span class="text-white">Deteksi otomatis struktur dokumen</span>
            </div>
            <div class="flex items-center">
              <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-check text-white text-sm"></i>
              </div>
              <span class="text-white">Analisis format sesuai panduan ITS</span>
            </div>
            <div class="flex items-center">
              <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3">
                <i class="fas fa-check text-white text-sm"></i>
              </div>
              <span class="text-white">Rekomendasi perbaikan detail</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Right side - Login Form -->
      <div class="md:w-1/2 max-w-md">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden login-container card-hover">
          <!-- Header -->
          <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white text-center">
            <h2 class="text-2xl font-bold">Masuk ke Akun Anda</h2>
            <p class="text-blue-100 mt-2">Gunakan Kredensial ITS Anda</p>
          </div>
          
          <!-- Form -->
          <div class="p-8">
            <form action="{{ route('login') }}" method="POST" class="space-y-6" id="loginForm">
              @csrf
              
              <!-- Email/NIM Field -->
              <div class="input-group">
                <label for="login" class="block text-sm font-medium text-gray-700 mb-2">Email atau NIM</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-user text-gray-400"></i>
                  </div>
                  <input 
                    id="login" 
                    name="login" 
                    type="text" 
                    required 
                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="nama@email.its.ac.id atau NIM"
                    autocomplete="username"
                    value="{{ old('email') }}"
                  >
                </div>
              </div>
              
              <!-- Password Field -->
              <div class="input-group">
                <div class="flex justify-between items-center mb-2">
                  <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                  <a href="#" class="text-sm text-blue-600 hover:text-blue-500 transition">Lupa kata sandi?</a>
                </div>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                  </div>
                  <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    required 
                    class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    placeholder="Masukkan kata sandi"
                    autocomplete="current-password"
                  >
                  <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                  </button>
                </div>
              </div>
              
              <!-- Remember Me & Options -->
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <input 
                    id="remember" 
                    name="remember" 
                    type="checkbox" 
                    class="custom-checkbox mr-2"
                    {{ old('remember') ? 'checked' : '' }}
                  >
                  <label for="remember" class="text-sm text-gray-700">Ingat saya</label>
                </div>

                    <div class="text-sm text-gray-600">
                        Belum punya akun? 
                    <a href="{{ route('register.form') }}" class="text-blue-600 hover:text-blue-500 font-medium transition">Daftar</a>
                </div>
              </div>
              
              <!-- Submit Button -->
              <button 
                type="submit" 
                id="submit-btn" 
                class="w-full py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg font-semibold shadow-md hover:from-blue-600 hover:to-purple-600 transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
              >
                <i class="fas fa-sign-in-alt mr-2"></i> 
                <span id="submit-text">Masuk</span>
                <i id="submit-loading" class="fas fa-spinner fa-spin ml-2 hidden"></i>
              </button>
              
              <!-- Divider -->
              <div class="relative flex items-center my-6">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="flex-shrink mx-4 text-gray-500 text-sm">atau masuk dengan</span>
                <div class="flex-grow border-t border-gray-300"></div>
              </div>
              
              <!-- SSO Options -->
              <div class="grid grid-cols-2 gap-4">
                <button 
                  type="button" 
                  class="flex items-center justify-center py-3 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                >
                  <i class="fab fa-google text-red-500 mr-2"></i>
                  <span class="text-sm font-medium text-gray-700">Google</span>
                </button>
                
                <button 
                  type="button" 
                  class="flex items-center justify-center py-3 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                >
                  <i class="fas fa-university text-blue-500 mr-2"></i>
                  <span class="text-sm font-medium text-gray-700">SSO ITS</span>
                </button>
              </div>
            </form>
            
            <!-- Error Messages -->
            @if($errors->any())
              <div class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
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
                <div class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-start">
                    <i class="fas fa-exclamation-circle mt-0.5 mr-3 flex-shrink-0"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->has('login'))
                <div class="mt-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong class="font-medium">Terjadi kesalahan:</strong>
                    </div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->get('login') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
          </div>
        </div>
        
        <!-- Guest Access Option -->
        <div class="mt-6 text-center">
          <p class="text-white text-sm">
            Ingin mencoba tanpa login? 
            <a href="{{ route('upload.form') }}" class="text-blue-200 hover:text-white font-medium underline transition">Akses sebagai tamu</a>
          </p>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-6 mt-8">
    <div class="max-w-6xl mx-auto px-4 text-center">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-4 md:mb-0">
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
          <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="Bantuan">
            <i class="fas fa-question-circle text-xl"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="Kontak">
            <i class="fas fa-envelope text-xl"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-white transition transform hover:scale-110" title="Tentang">
            <i class="fas fa-info-circle text-xl"></i>
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
    document.addEventListener('DOMContentLoaded', function() {
      const loginForm = document.getElementById('loginForm');
      const submitBtn = document.getElementById('submit-btn');
      const submitText = document.getElementById('submit-text');
      const submitLoading = document.getElementById('submit-loading');
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('password');
      const notification = document.getElementById('notification');
      const notificationMessage = document.getElementById('notification-message');
      const successNotification = document.getElementById('success-notification');
      const successMessage = document.getElementById('success-message');
      
      // Toggle password visibility
      togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle eye icon
        const icon = this.querySelector('i');
        if (type === 'password') {
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        } else {
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        }
      });
      
      // Form submission
      loginForm.addEventListener('submit', function(e) {
        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Memproses...';
        submitLoading.classList.remove('hidden');
        
        // Form will be submitted normally to the server
        // The loading state will be handled by the server response
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

      // Auto-hide success/error messages after page load
      @if(session('success') || session('error') || session('info'))
        setTimeout(() => {
          const messageElements = document.querySelectorAll('.bg-green-100, .bg-red-100, .bg-blue-100');
          messageElements.forEach(el => {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
          });
        }, 5000);
      @endif
    });
  </script>
</body>
</html>