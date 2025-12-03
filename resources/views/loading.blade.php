    <!-- Loading Overlay -->
  <div id="loadingOverlay" class="fixed inset-0 z-[100] hidden bg-black/40 backdrop-blur-sm">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
    
    <!-- Loading Spinner dengan Logo ITS -->
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
      <div class="relative">
        <!-- Logo ITS di Tengah -->
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
          <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
            <i class="fas fa-graduation-cap text-white text-2xl"></i>
          </div>
        </div>
      </div>
      
      <!-- Loading Text -->
      <div class="mt-8 text-center">
        <p class="text-white text-xl font-semibold mb-2 animate-pulse">TAkCekIn ITS</p>
        <p class="text-blue-200">Memproses permintaan Anda...</p>
        
        <!-- Progress Bar -->
        <div class="mt-4 w-64 bg-white/20 rounded-full h-2 overflow-hidden">
          <div id="loadingProgress" class="h-full bg-gradient-to-r from-blue-400 to-purple-500 rounded-full loading-progress"></div>
        </div>
        
        <!-- Loading Dots Animation -->
        <div class="mt-3 flex justify-center space-x-1">
          <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce"></div>
          <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce animation-delay-150"></div>
          <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce animation-delay-300"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Loading Toast Notification -->
  <div id="loadingToast" class="fixed top-24 right-6 z-[90] hidden">
    <div class="bg-gradient-to-r from-blue-500/90 to-purple-600/90 backdrop-blur-sm text-white px-6 py-4 rounded-xl shadow-xl border border-white/20 flex items-center space-x-3">
      <div class="relative">
        <div class="w-6 h-6 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
      </div>
      <div>
        <p class="font-medium">Sedang memproses...</p>
        <p class="text-sm text-blue-100">Harap tunggu sebentar</p>
      </div>
    </div>
  </div>

  
  <script>
    // Loading Manager untuk homepage - DIPERBAIKI
    class HomepageLoadingManager {
      constructor() {
        this.overlay = document.getElementById('loadingOverlay');
        this.toast = document.getElementById('loadingToast');
        this.cancelBtn = document.getElementById('cancelLoading');
        this._autoHideTimer = null;

        if (this.cancelBtn) {
          this.cancelBtn.addEventListener('click', () => this.hideOverlay());
        }
        
        // Pastikan loading disembunyikan saat page load
        window.addEventListener('load', () => {
          this.hideOverlay();
        });
      }
      
      showOverlay(message = 'Memproses permintaan Anda...') {
        if (this.overlay) {
          this.overlay.classList.remove('hidden');
          
          // Update message jika ada
          const messageEl = this.overlay.querySelector('p.text-blue-200');
          if (messageEl && message) {
            messageEl.textContent = message;
          }
          
          // Auto-hide fallback: hanya 3 detik untuk mencegah stuck
          if (this._autoHideTimer) clearTimeout(this._autoHideTimer);
          this._autoHideTimer = setTimeout(() => {
            this.hideOverlay();
          }, 3000); // Diperpendek dari 5 detik
        }
      }
      
      hideOverlay() {
        if (this.overlay) {
          this.overlay.classList.add('hidden');
          if (this._autoHideTimer) {
            clearTimeout(this._autoHideTimer);
            this._autoHideTimer = null;
          }
        }
      }
      
      showToast(message = 'Sedang memproses...', duration = 2000) { // Diperpendek
        if (this.toast) {
          this.toast.classList.remove('hidden');
          this.toast.style.animation = 'slide-in-right 0.3s ease-out';
          
          // Update message
          const messageEl = this.toast.querySelector('p.font-medium');
          if (messageEl && message) {
            messageEl.textContent = message;
          }
          
          // Auto hide setelah durasi
          if (duration > 0) {
            setTimeout(() => this.hideToast(), duration);
          }
        }
      }
      
      hideToast() {
        if (this.toast) {
          this.toast.style.animation = 'slide-out-right 0.3s ease-in';
          setTimeout(() => {
            this.toast.classList.add('hidden');
            this.toast.style.animation = '';
          }, 300);
        }
      }
    }
    
    // Initialize loading manager
    const homepageLoading = new HomepageLoadingManager();
    
    // HAPUS atau KOMENTARI event listener untuk tombol CTA yang bermasalah
    // Event listener ini menyebabkan loading muncul terus-menerus
    
    /*
    // KOMENTARI BAGIAN INI
    document.querySelectorAll('.btn-hover').forEach(button => {
      const href = button.getAttribute('href') || '';
      if (!href) return;

      button.addEventListener('click', function (e) {
        const lower = href.toLowerCase();

        if (lower.includes('login')) {
          homepageLoading.showToast('Mengarahkan ke halaman login...', 1500);
          return;
        }

        if (lower.includes('upload')) {
          homepageLoading.showOverlay('Mempersiapkan halaman upload...');
          return;
        }
      });
    });
    */
    
    // GANTI dengan event listener yang lebih aman hanya untuk link internal
    document.querySelectorAll('a.btn-hover').forEach(link => {
      link.addEventListener('click', function(e) {
        // Hanya tampilkan loading jika link adalah internal (bukan #anchor)
        const href = this.getAttribute('href');
        
        if (href && !href.startsWith('#') && !href.includes('youtube.com')) {
          // Cek apakah link mengarah ke halaman upload
          if (href.includes('upload') || href.includes('login')) {
            homepageLoading.showToast('Mengarahkan...', 1000);
          }
        }
      });
    });
    
    // Pastikan loading disembunyikan setelah halaman selesai load
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(() => {
        homepageLoading.hideOverlay();
      }, 500);
    });
    
    // Juga sembunyikan saat halaman siap
    if (document.readyState === 'complete') {
      homepageLoading.hideOverlay();
    }
    
    // Force hide loading after page is fully loaded
    window.addEventListener('load', function() {
      setTimeout(() => {
        homepageLoading.hideOverlay();
        homepageLoading.hideToast();
      }, 100);
    });

    // Also hide immediately if page is already loaded
    if (document.readyState === 'complete') {
      homepageLoading.hideOverlay();
      homepageLoading.hideToast();
    }
  </script>