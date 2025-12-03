<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar - FormatCheck ITS</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('css/formatcheck-its.css') }}">
  <link rel="stylesheet" href="css/formatcheck-its.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="flex flex-col min-h-screen no-horizontal-scroll">
  <!-- Notifikasi -->
  <div id="notification" class="fixed top-4 right-4 z-50 hidden max-w-sm">
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-lg notification">
      <div class="flex items-start">
        <i class="fas fa-exclamation-triangle mt-1 mr-3 flex-shrink-0"></i>
        <span class="block flex-1 break-words" id="notification-message"></span>
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
        <span class="block flex-1 break-words" id="success-message"></span>
      </div>
      <button onclick="hideSuccessNotification()" class="absolute top-2 right-2 p-1 text-green-500 hover:text-green-700">
        <i class="fas fa-times text-sm"></i>
      </button>
    </div>
  </div>

  <!-- Main Content -->
  <main class="flex-grow flex items-center justify-center p-4 py-8 safe-area">
    <div class="w-full max-w-2xl">
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden register-container card-hover">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white text-center">
          <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center floating">
              <i class="fas fa-graduation-cap text-blue-600 text-2xl"></i>
            </div>
          </div>
          <h1 class="text-2xl font-bold break-words">Bergabung dengan FormatCheck ITS</h1>
          <p class="text-blue-100 mt-2 break-words">Daftar akun untuk akses penuh fitur analisis format tugas akhir</p>
        </div>
        
        <!-- Form -->
        <div class="p-6 compact-form">
          <!-- Step Indicator -->
          <div class="step-indicator">
            <div class="step active">1</div>
            <div class="step">2</div>
            <div class="step">3</div>
          </div>
          
          <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf
            
            <!-- Step 1: Personal Information -->
            <div class="form-step active" id="step1">
              <h3 class="text-lg font-semibold mb-4 text-gray-800 break-words">Informasi Pribadi</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Nama Lengkap -->
                <div class="input-group">
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1 break-words">Nama Lengkap</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <input 
                      id="name" 
                      name="name" 
                      type="text" 
                      required 
                      class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-overflow-fix"
                      placeholder="Masukkan nama lengkap"
                      value="{{ old('name') }}"
                    >
                  </div>
                </div>
                
                <!-- NIM -->
                <div class="input-group">
                  <label for="nim" class="block text-sm font-medium text-gray-700 mb-1 break-words">NIM</label>
                  <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <i class="fas fa-id-card text-gray-400"></i>
                    </div>
                    <input 
                      id="nim" 
                      name="nim" 
                      type="text" 
                      required 
                      class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-overflow-fix"
                      placeholder="Masukkan NIM"
                      value="{{ old('nim') }}"
                    >
                  </div>
                </div>
              </div>
              
              <!-- Program Studi -->
              <div class="input-group mt-4">
                <label for="program_studi" class="block text-sm font-medium text-gray-700 mb-1 break-words">Program Studi</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-graduation-cap text-gray-400"></i>
                  </div>
                  <select 
                    id="program_studi" 
                    name="program_studi" 
                    required 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition appearance-none bg-white text-overflow-fix"
                  >
                    <option value="">Pilih Program Studi</option>
                    <option value="Teknik Informatika" {{ old('program_studi') == 'Teknik Informatika' ? 'selected' : '' }}>Teknik Informatika</option>
                    <option value="Sistem Informasi" {{ old('program_studi') == 'Sistem Informasi' ? 'selected' : '' }}>Sistem Informasi</option>
                    <option value="Teknik Komputer" {{ old('program_studi') == 'Teknik Komputer' ? 'selected' : '' }}>Teknik Komputer</option>
                    <option value="Teknik Elektro" {{ old('program_studi') == 'Teknik Elektro' ? 'selected' : '' }}>Teknik Elektro</option>
                    <option value="Teknik Mesin" {{ old('program_studi') == 'Teknik Mesin' ? 'selected' : '' }}>Teknik Mesin</option>
                    <option value="Teknik Sipil" {{ old('program_studi') == 'Teknik Sipil' ? 'selected' : '' }}>Teknik Sipil</option>
                    <option value="Arsitektur" {{ old('program_studi') == 'Arsitektur' ? 'selected' : '' }}>Arsitektur</option>
                    <option value="Desain Produk" {{ old('program_studi') == 'Desain Produk' ? 'selected' : '' }}>Desain Produk</option>
                    <option value="Lainnya" {{ old('program_studi') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                  </div>
                </div>
              </div>
              
              <div class="flex justify-end mt-6">
                <button type="button" class="next-step bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition break-words">
                  Selanjutnya <i class="fas fa-arrow-right ml-1"></i>
                </button>
              </div>
            </div>
            
            <!-- Step 2: Account Information -->
            <div class="form-step" id="step2">
              <h3 class="text-lg font-semibold mb-4 text-gray-800 break-words">Informasi Akun</h3>
              
              <!-- Email -->
              <div class="input-group">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1 break-words">Email ITS</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-envelope text-gray-400"></i>
                  </div>
                  <input 
                    id="email" 
                    name="email" 
                    type="email" 
                    required 
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-overflow-fix"
                    placeholder="nama@student.its.ac.id"
                    value="{{ old('email') }}"
                  >
                </div>
                <p class="text-xs text-gray-500 mt-1 break-words">Gunakan email institusi ITS Anda</p>
              </div>
              
              <!-- Password -->
              <div class="input-group mt-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1 break-words">Kata Sandi</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                  </div>
                  <input 
                    id="password" 
                    name="password" 
                    type="password" 
                    required 
                    class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-overflow-fix"
                    placeholder="Buat kata sandi"
                  >
                  <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                  </button>
                </div>
                <!-- Password Strength Indicator -->
                <div class="mt-2">
                  <div class="flex justify-between mb-1">
                    <span class="text-xs text-gray-500 break-words">Kekuatan kata sandi</span>
                    <span id="password-strength-text" class="text-xs text-gray-500 break-words"></span>
                  </div>
                  <div class="w-full bg-gray-200 rounded-full h-1.5">
                    <div id="password-strength-bar" class="password-strength h-1.5 rounded-full"></div>
                  </div>
                </div>
              </div>
              
              <!-- Confirm Password -->
              <div class="input-group mt-4">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1 break-words">Konfirmasi Kata Sandi</label>
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-lock text-gray-400"></i>
                  </div>
                  <input 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    type="password" 
                    required 
                    class="block w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition text-overflow-fix"
                    placeholder="Ulangi kata sandi"
                  >
                  <button type="button" id="togglePasswordConfirmation" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                  </button>
                </div>
                <div id="password-match" class="hidden mt-1">
                  <p class="text-xs text-green-600 break-words"><i class="fas fa-check mr-1"></i> Kata sandi cocok</p>
                </div>
                <div id="password-mismatch" class="hidden mt-1">
                  <p class="text-xs text-red-600 break-words"><i class="fas fa-times mr-1"></i> Kata sandi tidak cocok</p>
                </div>
              </div>
              
              <div class="flex justify-between mt-6 flex-wrap gap-2">
                <button type="button" class="prev-step text-gray-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition break-words">
                  <i class="fas fa-arrow-left mr-1"></i> Kembali
                </button>
                <button type="button" class="next-step bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition break-words">
                  Selanjutnya <i class="fas fa-arrow-right ml-1"></i>
                </button>
              </div>
            </div>
            
            <!-- Step 3: Terms & Submit -->
            <div class="form-step" id="step3">
              <h3 class="text-lg font-semibold mb-4 text-gray-800 break-words">Persetujuan</h3>
              
              <!-- Terms Agreement -->
              <div class="flex items-start mb-4">
                <input 
                  id="agree_terms" 
                  name="agree_terms" 
                  type="checkbox" 
                  required 
                  class="custom-checkbox mr-2 mt-1"
                >
                <label for="agree_terms" class="text-sm text-gray-700 break-words">
                  Saya setuju dengan 
                  <a href="#" class="text-blue-600 hover:text-blue-500 font-medium break-words">Syarat & Ketentuan</a> 
                  dan 
                  <a href="#" class="text-blue-600 hover:text-blue-500 font-medium break-words">Kebijakan Privasi</a>
                  FormatCheck ITS
                </label>
              </div>
              
              <!-- Benefits -->
              <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-blue-800 mb-2 break-words">Keuntungan Bergabung:</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                  <li class="flex items-center break-words"><i class="fas fa-check-circle mr-2 text-green-500"></i> Simpan riwayat analisis dokumen</li>
                  <li class="flex items-center break-words"><i class="fas fa-check-circle mr-2 text-green-500"></i> Lacak perkembangan kualitas format</li>
                  <li class="flex items-center break-words"><i class="fas fa-check-circle mr-2 text-green-500"></i> Notifikasi update panduan ITS</li>
                  <li class="flex items-center break-words"><i class="fas fa-check-circle mr-2 text-green-500"></i> Ekspor hasil analisis dalam berbagai format</li>
                </ul>
              </div>
              
              <div class="flex justify-between mt-6 flex-wrap gap-2">
                <button type="button" class="prev-step text-gray-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition break-words">
                  <i class="fas fa-arrow-left mr-1"></i> Kembali
                </button>
                
                <!-- Submit Button -->
                <button 
                  type="submit" 
                  id="submit-btn" 
                  class="py-2 px-6 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg font-semibold shadow-md hover:from-blue-600 hover:to-purple-600 transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed break-words"
                >
                  <i class="fas fa-user-plus mr-2"></i> 
                  <span id="submit-text">Daftar Akun</span>
                  <i id="submit-loading" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                </button>
              </div>
            </div>
          </form>
          
          <!-- Divider -->
          <div class="relative flex items-center my-4">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="flex-shrink mx-4 text-gray-500 text-sm break-words">atau daftar dengan</span>
            <div class="flex-grow border-t border-gray-300"></div>
          </div>
          
          <!-- SSO Options -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <button 
              type="button" 
              class="flex items-center justify-center py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
              <i class="fab fa-google text-red-500 mr-2"></i>
              <span class="text-sm font-medium text-gray-700 break-words">Google</span>
            </button>
            
            <button 
              type="button" 
              class="flex items-center justify-center py-2 px-4 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
              <i class="fas fa-university text-blue-500 mr-2"></i>
              <span class="text-sm font-medium text-gray-700 break-words">SSO ITS</span>
            </button>
          </div>
          
          <!-- Login Link -->
          <div class="mt-6 text-center">
            <p class="text-gray-600 text-sm break-words">
              Sudah punya akun? 
              <a href="{{ route('login.form') }}" class="text-blue-600 hover:text-blue-500 font-medium transition break-words">Masuk di sini</a>
            </p>
          </div>
          
          <!-- Error Messages -->
          @if($errors->any())
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
              <div class="flex items-center mb-2">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong class="font-medium break-words">Terjadi kesalahan:</strong>
              </div>
              <ul class="list-disc list-inside text-sm space-y-1">
                @foreach($errors->all() as $error)
                  <li class="break-words">{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          @if(session('error'))
            <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-start">
              <i class="fas fa-exclamation-circle mt-0.5 mr-3 flex-shrink-0"></i>
              <div class="break-words">{{ session('error') }}</div>
            </div>
          @endif

          @if(session('success'))
            <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-start">
              <i class="fas fa-check-circle mt-0.5 mr-3 flex-shrink-0"></i>
              <div class="break-words">{{ session('success') }}</div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-4 mt-8">
    <div class="max-w-2xl mx-auto px-4 text-center safe-area">
      <div class="flex flex-col md:flex-row justify-between items-center">
        <div class="mb-2 md:mb-0">
          <div class="flex items-center justify-center md:justify-start">
            <a href="{{ url('/') }}" class="flex items-center">
              <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-500 rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-graduation-cap text-white text-lg"></i>
              </div>
              <span class="text-xl font-bold break-words">FormatCheck ITS</span>
            </a>
          </div>
          <p class="text-gray-400 text-xs mt-1 break-words">Sistem Deteksi Kelengkapan Format Tugas Akhir</p>
        </div>
        
        <div class="flex space-x-4">
          <a href="#" class="text-gray-300 hover:text-white transition" title="Bantuan">
            <i class="fas fa-question-circle"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-white transition" title="Kontak">
            <i class="fas fa-envelope"></i>
          </a>
          <a href="#" class="text-gray-300 hover:text-white transition" title="Tentang">
            <i class="fas fa-info-circle"></i>
          </a>
        </div>
      </div>
      
      <div class="border-t border-gray-700 mt-2 pt-2">
        <p class="text-gray-400 text-xs break-words">
          © 2025 Sistem Deteksi Kelengkapan Format Tugas Akhir - ITS. All rights reserved.
        </p>
      </div>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const registerForm = document.getElementById('registerForm');
      const submitBtn = document.getElementById('submit-btn');
      const submitText = document.getElementById('submit-text');
      const submitLoading = document.getElementById('submit-loading');
      const togglePassword = document.getElementById('togglePassword');
      const togglePasswordConfirmation = document.getElementById('togglePasswordConfirmation');
      const passwordInput = document.getElementById('password');
      const passwordConfirmationInput = document.getElementById('password_confirmation');
      const passwordStrengthBar = document.getElementById('password-strength-bar');
      const passwordStrengthText = document.getElementById('password-strength-text');
      const passwordMatch = document.getElementById('password-match');
      const passwordMismatch = document.getElementById('password-mismatch');
      const notification = document.getElementById('notification');
      const notificationMessage = document.getElementById('notification-message');
      const successNotification = document.getElementById('success-notification');
      const successMessage = document.getElementById('success-message');
      
      // Multi-step form functionality
      const steps = document.querySelectorAll('.form-step');
      const stepIndicators = document.querySelectorAll('.step');
      let currentStep = 0;
      
      // Next step button
      document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', function() {
          if (validateStep(currentStep)) {
            goToStep(currentStep + 1);
          }
        });
      });
      
      // Previous step button
      document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', function() {
          goToStep(currentStep - 1);
        });
      });
      
      function goToStep(step) {
        // Hide current step
        steps[currentStep].classList.remove('active');
        stepIndicators[currentStep].classList.remove('active');
        
        // Show new step
        steps[step].classList.add('active');
        stepIndicators[step].classList.add('active');
        
        // Mark previous steps as completed
        for (let i = 0; i < step; i++) {
          stepIndicators[i].classList.add('completed');
        }
        
        currentStep = step;
      }
      
      function validateStep(step) {
        let isValid = true;
        
        if (step === 0) {
          // Validate step 1
          const name = document.getElementById('name').value;
          const nim = document.getElementById('nim').value;
          const programStudi = document.getElementById('program_studi').value;
          
          if (!name.trim()) {
            showNotification('Nama lengkap harus diisi');
            isValid = false;
          } else if (!nim.trim()) {
            showNotification('NIM harus diisi');
            isValid = false;
          } else if (!programStudi) {
            showNotification('Program studi harus dipilih');
            isValid = false;
          }
        } else if (step === 1) {
          // Validate step 2
          const email = document.getElementById('email').value;
          const password = document.getElementById('password').value;
          const passwordConfirm = document.getElementById('password_confirmation').value;
          
          if (!email.trim()) {
            showNotification('Email harus diisi');
            isValid = false;
          } else if (!email.endsWith('@student.its.ac.id')) {
            showNotification('Harus menggunakan email ITS (@student.its.ac.id)');
            isValid = false;
          } else if (!password) {
            showNotification('Kata sandi harus diisi');
            isValid = false;
          } else if (password.length < 8) {
            showNotification('Kata sandi minimal 8 karakter');
            isValid = false;
          } else if (password !== passwordConfirm) {
            showNotification('Konfirmasi kata sandi tidak cocok');
            isValid = false;
          }
        }
        
        return isValid;
      }
      
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
      
      // Toggle password confirmation visibility
      togglePasswordConfirmation.addEventListener('click', function() {
        const type = passwordConfirmationInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirmationInput.setAttribute('type', type);
        
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
      
      // Password strength checker
      passwordInput.addEventListener('input', function() {
        checkPasswordStrength(this.value);
        checkPasswordMatch();
      });
      
      // Password confirmation checker
      passwordConfirmationInput.addEventListener('input', checkPasswordMatch);
      
      function checkPasswordStrength(password) {
        let strength = 0;
        let text = '';
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;
        
        switch(strength) {
          case 0:
          case 1:
            text = 'Lemah';
            passwordStrengthBar.className = 'password-strength strength-weak';
            break;
          case 2:
            text = 'Cukup';
            passwordStrengthBar.className = 'password-strength strength-fair';
            break;
          case 3:
            text = 'Baik';
            passwordStrengthBar.className = 'password-strength strength-good';
            break;
          case 4:
            text = 'Kuat';
            passwordStrengthBar.className = 'password-strength strength-strong';
            break;
        }
        
        passwordStrengthText.textContent = text;
      }
      
      function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = passwordConfirmationInput.value;
        
        if (confirmPassword === '') {
          passwordMatch.classList.add('hidden');
          passwordMismatch.classList.add('hidden');
          return;
        }
        
        if (password === confirmPassword) {
          passwordMatch.classList.remove('hidden');
          passwordMismatch.classList.add('hidden');
        } else {
          passwordMatch.classList.add('hidden');
          passwordMismatch.classList.remove('hidden');
        }
      }
      
      // Form submission
      registerForm.addEventListener('submit', function(e) {
        if (!validateStep(2)) {
          e.preventDefault();
          return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitText.textContent = 'Mendaftarkan...';
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