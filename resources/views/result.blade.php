<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hasil Analisis TA</title>
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
  </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 py-8">
  <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-full max-w-4xl">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white text-center">
      <h1 class="text-3xl font-bold mb-2">Hasil Analisis Tugas Akhir</h1>
      <p class="text-blue-100">Dokumen: <span class="font-medium" id="filename">{{ $filename }}</span></p>
    </div>
    
    <div class="p-8">
        @if(isset($results) && is_array($results))
        <!-- Score Summary -->
        <div class="flex flex-col md:flex-row items-center justify-between mb-10 p-6 bg-blue-50 rounded-xl border border-blue-200">
            <div class="flex items-center mb-4 md:mb-0">
                <div class="relative w-24 h-24 mr-4">
                    <svg class="w-full h-full" viewBox="0 0 36 36">
                        <path class="circle-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#eee" stroke-width="3.8"/>
                        <path class="circle" stroke="@if($results['overall_score'] >= 8) #10b981 @elseif($results['overall_score'] >= 6) #f59e0b @else #ef4444 @endif" 
                            stroke-dasharray="{{ $results['overall_score'] * 10 }}, 100" 
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" 
                            fill="none" stroke-width="3.8"/>
                        <text x="18" y="22" text-anchor="middle" fill="@if($results['overall_score'] >= 8) #10b981 @elseif($results['overall_score'] >= 6) #f59e0b @else #ef4444 @endif" 
                            font-size="10" font-weight="bold">{{ $results['overall_score'] }}/10</text>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Skor Kelengkapan</h2>
                    <p class="text-gray-600">Dokumen Anda memenuhi {{ number_format($results['overall_score'] * 10, 1) }}% persyaratan</p>
                    <p class="text-sm text-gray-500">{{ $results['metadata']['title'] ?? 'Judul tidak ditemukan' }}</p>
                </div>
            </div>
            <div class="px-4 py-2 rounded-full font-semibold 
                @if($results['overall_score'] >= 8) bg-green-100 text-green-800
                @elseif($results['overall_score'] >= 6) bg-yellow-100 text-yellow-800
                @else bg-red-100 text-red-800 @endif">
                <i class="fas @if($results['overall_score'] >= 8) fa-check-circle 
                    @elseif($results['overall_score'] >= 6) fa-exclamation-triangle 
                    @else fa-times-circle @endif mr-2"></i>
                @if($results['overall_score'] >= 8) LAYAK DIAJUKAN
                @elseif($results['overall_score'] >= 6) PERLU PERBAIKAN
                @else TIDAK LAYAK @endif
            </div>
        </div>

        <!-- Detail Analysis -->
        <h2 class="text-xl font-bold text-gray-800 mb-6">Detail Analisis</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <!-- Abstract Card -->
            <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
                @if($results['abstract']['status'] === 'success') border-green-200
                @elseif($results['abstract']['status'] === 'warning') border-yellow-200
                @else border-red-200 @endif">
                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                        @if($results['abstract']['status'] === 'success') bg-green-100 text-green-500
                        @elseif($results['abstract']['status'] === 'warning') bg-yellow-100 text-yellow-500
                        @else bg-red-100 text-red-500 @endif">
                        <i class="fas @if($results['abstract']['status'] === 'success') fa-check
                            @elseif($results['abstract']['status'] === 'warning') fa-exclamation-triangle
                            @else fa-times @endif"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Abstrak</h3>
                        <p class="text-sm text-gray-600">
                            @if($results['abstract']['found'])
                            {{ $results['abstract']['word_count'] }} kata
                            @else
                            Tidak ditemukan
                            @endif
                        </p>
                    </div>
                </div>
                <div class="bg-gray-100 rounded-full h-2.5 mb-2">
                    <div class="h-2.5 rounded-full 
                        @if($results['abstract']['status'] === 'success') bg-green-500
                        @elseif($results['abstract']['status'] === 'warning') bg-yellow-500
                        @else bg-red-500 @endif" 
                        style="width: @if($results['abstract']['found']) 100% @else 0% @endif">
                    </div>
                </div>
                <p class="text-sm 
                    @if($results['abstract']['status'] === 'success') text-green-600
                    @elseif($results['abstract']['status'] === 'warning') text-yellow-600
                    @else text-red-600 @endif font-medium">
                    {{ $results['abstract']['message'] }}
                </p>
            </div>

            <!-- Table of Contents Card -->
            <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
                @if($results['table_of_contents']['status'] === 'success') border-green-200
                @else border-yellow-200 @endif">
                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                        @if($results['table_of_contents']['status'] === 'success') bg-green-100 text-green-500
                        @else bg-yellow-100 text-yellow-500 @endif">
                        <i class="fas @if($results['table_of_contents']['status'] === 'success') fa-check
                            @else fa-exclamation-triangle @endif"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Daftar Isi</h3>
                        <p class="text-sm text-gray-600">
                            @if($results['table_of_contents']['found'])
                            Ditemukan
                            @else
                            Tidak ditemukan
                            @endif
                        </p>
                    </div>
                </div>
                <p class="text-sm 
                    @if($results['table_of_contents']['status'] === 'success') text-green-600
                    @else text-yellow-600 @endif font-medium">
                    {{ $results['table_of_contents']['message'] }}
                </p>
            </div>

            <!-- References Card -->
            <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
                @if($results['references']['status'] === 'success') border-green-200
                @elseif($results['references']['status'] === 'warning') border-yellow-200
                @else border-red-200 @endif">
                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                        @if($results['references']['status'] === 'success') bg-green-100 text-green-500
                        @elseif($results['references']['status'] === 'warning') bg-yellow-100 text-yellow-500
                        @else bg-red-100 text-red-500 @endif">
                        <i class="fas @if($results['references']['status'] === 'success') fa-check
                            @elseif($results['references']['status'] === 'warning') fa-exclamation-triangle
                            @else fa-times @endif"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Daftar Pustaka</h3>
                        <p class="text-sm text-gray-600">
                            {{ $results['references']['count'] }} referensi
                            (minimal {{ $results['references']['min_references'] }})
                        </p>
                    </div>
                </div>
                <div class="bg-gray-100 rounded-full h-2.5 mb-2">
                    <div class="h-2.5 rounded-full 
                        @if($results['references']['status'] === 'success') bg-green-500
                        @elseif($results['references']['status'] === 'warning') bg-yellow-500
                        @else bg-red-500 @endif" 
                        style="width: {{ min(100, ($results['references']['count'] / $results['references']['min_references']) * 100) }}%">
                    </div>
                </div>
                <p class="text-sm 
                    @if($results['references']['status'] === 'success') text-green-600
                    @elseif($results['references']['status'] === 'warning') text-yellow-600
                    @else text-red-600 @endif font-medium">
                    {{ $results['references']['message'] }}
                </p>
            </div>

            <!-- Chapters Card -->
            <div class="result-card bg-white rounded-xl border p-5 shadow-sm 
                @if($results['chapters']['status'] === 'success') border-green-200
                @else border-yellow-200 @endif">
                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center mr-3
                        @if($results['chapters']['status'] === 'success') bg-green-100 text-green-500
                        @else bg-yellow-100 text-yellow-500 @endif">
                        <i class="fas @if($results['chapters']['status'] === 'success') fa-check
                            @else fa-exclamation-triangle @endif"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Struktur Bab</h3>
                        <p class="text-sm text-gray-600">
                            @if($results['chapters']['introduction']) Pendahuluan ✓ @else Pendahuluan ✗ @endif
                            @if($results['chapters']['methodology']) Metodologi ✓ @else Metodologi ✗ @endif
                        </p>
                    </div>
                </div>
                <p class="text-sm 
                    @if($results['chapters']['status'] === 'success') text-green-600
                    @else text-yellow-600 @endif font-medium">
                    {{ $results['chapters']['message'] }}
                </p>
            </div>
        </div>
        @endif
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
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
    
    <!-- Footer -->
    <div class="bg-gray-100 p-4 text-center text-gray-600 text-sm">
      <p>© 2025 Sistem Deteksi Kelengkapan Tugas Akhir - ITS</p>
    </div>
  </div>

  <script>
    // Data hasil analisis (dalam aplikasi nyata, ini akan berasal dari backend)
    const analysisResults = {
      filename: "{{ $filename }}",
      aspects: [
        {
          name: "Abstrak",
          description: "Jumlah kata: Sedang dianalisis",
          score: 0,
          maxScore: 10,
          status: "processing",
          message: "Menganalisis abstrak...",
          recommendation: ""
        },
        {
          name: "Margin",
          description: "Atas: Sedang dianalisis",
          score: 0,
          maxScore: 10,
          status: "processing",
          message: "Memeriksa margin...",
          recommendation: ""
        },
        {
          name: "Daftar Isi",
          description: "Format: Sedang dianalisis",
          score: 0,
          maxScore: 10,
          status: "processing",
          message: "Memeriksa daftar isi...",
          recommendation: ""
        },
        {
          name: "Rumusan Masalah",
          description: "Kejelasan: Sedang dianalisis",
          score: 0,
          maxScore: 10,
          status: "processing",
          message: "Menganalisis rumusan masalah...",
          recommendation: ""
        },
        {
          name: "Daftar Pustaka",
          description: "Jumlah referensi: Sedang dianalisis",
          score: 0,
          maxScore: 10,
          status: "processing",
          message: "Memeriksa daftar pustaka...",
          recommendation: ""
        },
        {
          name: "Format Penulisan",
          description: "Konsistensi: Sedang dianalisis",
          score: 0,
          maxScore: 10,
          status: "processing",
          message: "Memeriksa format penulisan...",
          recommendation: ""
        }
      ],
      recommendations: []
    };

    // Simulasi proses analisis
    document.addEventListener('DOMContentLoaded', function() {
      // Mulai simulasi analisis
      simulateAnalysis();
    });

    // Fungsi untuk mensimulasikan proses analisis
    function simulateAnalysis() {
      let progress = 0;
      const totalSteps = 20;
      const progressInterval = setInterval(() => {
        progress += 1;
        
          (progress <= 5) {
          // Fase awal - memuat data
          updateStatus("MEMPROSES", "bg-blue-100 text-blue-800", "fas fa-spinner animate-spin");
          document.getElementById('score-description').textContent = `Memuat dokumen... (${progress * 5}%)`;
        } 
        else if (progress <= 15) {
          // Fase analisis
          updateStatus("MENGANALISIS", "bg-yellow-100 text-yellow-800", "fas fa-search");
          document.getElementById('score-description').textContent = `Menganalisis konten... (${progress * 5}%)`;
          
          // Update beberapa hasil analisis
          if (progress === 10) {
            updateAspect(0, 9, "Jumlah kata: 248 kata", "Sangat baik! Memenuhi syarat jumlah kata.", "success");
            updateAspect(2, 8, "Format konsisten", "Format daftar isi sudah sesuai ketentuan.", "success");
          }
          else if (progress === 12) {
            updateAspect(1, 4, "Atas: 3.0cm → 4.0cm", "Perlu penyesuaian margin atas dan kiri.", "danger");
            updateAspect(3, 6, "Kurang spesifik", "Perlu diperjelas dan lebih spesifik.", "warning");
          }
          else if (progress === 14) {
            updateAspect(4, 7, "Jumlah: 18 referensi", "Perlu tambahan 2 referensi lagi.", "warning");
            updateAspect(5, 8, "Konsistensi baik", "Format penulisan sudah konsisten.", "success");
          }
        } 
        else if (progress < totalSteps) {
          // Fase finalisasi
          updateStatus("MENYELESAIKAN", "bg-green-100 text-green-800", "fas fa-check-circle");
          document.getElementById('score-description').textContent = `Menyelesaikan analisis... (${progress * 5}%)`;
        } 
        else {
          // Selesai
          clearInterval(progressInterval);
          finalizeAnalysis();
        }
        
        // Update progress circle
        const circumference = 2 * Math.PI * 15.9155;
        const dashoffset = circumference - (progress * 5 / 100) * circumference;
        document.querySelector('.circle').style.strokeDasharray = `${circumference} ${circumference}`;
        document.querySelector('.circle').style.strokeDashoffset = dashoffset;
        
      }, 300);
    }

    // Fungsi untuk mengupdate status analisis
    function updateStatus(text, bgColor, icon) {
      const statusLabel = document.getElementById('status-label');
      statusLabel.className = `${bgColor} px-4 py-2 rounded-full font-semibold flex items-center justify-center`;
      statusLabel.innerHTML = `<i class="${icon} mr-2"></i> ${text}`;
    }

    // Fungsi untuk mengupdate aspek analisis
    function updateAspect(index, score, description, message, status) {
      const aspect = analysisResults.aspects[index];
      aspect.score = score;
      aspect.description = description;
      aspect.message = message;
      aspect.status = status;
      
      renderAspects();
    }

    // Fungsi untuk merender aspek analisis
    function renderAspects() {
      const container = document.getElementById('results-container');
      container.innerHTML = '';
      
      analysisResults.aspects.forEach((aspect, index) => {
        const aspectElement = document.createElement('div');
        aspectElement.className = `result-card bg-white rounded-xl border border-gray-200 p-5 shadow-sm animate-fade-in delay-${index < 4 ? index * 100 : 400}`;
        
        let statusColor, statusIcon;
        switch(aspect.status) {
          case 'success':
            statusColor = 'bg-green-100 text-green-500';
            statusIcon = 'fa-check';
            break;
          case 'warning':
            statusColor = 'bg-yellow-100 text-yellow-500';
            statusIcon = 'fa-exclamation-triangle';
            break;
          case 'danger':
            statusColor = 'bg-red-100 text-red-500';
            statusIcon = 'fa-times';
            break;
          default:
            statusColor = 'bg-blue-100 text-blue-500';
            statusIcon = 'fa-spinner animate-spin';
        }
        
        const percentage = (aspect.score / aspect.maxScore) * 100;
        
        aspectElement.innerHTML = `
          <div class="flex items-start mb-4">
            <div class="flex-shrink-0 w-10 h-10 ${statusColor} rounded-full flex items-center justify-center mr-3">
              <i class="fas ${statusIcon}"></i>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">${aspect.name}</h3>
              <p class="text-sm text-gray-600">${aspect.description}</p>
            </div>
          </div>
          <div class="bg-gray-100 rounded-full h-2.5 mb-1">
            <div class="progress-bar h-2.5 rounded-full ${
              percentage >= 80 ? 'bg-green-500' : 
              percentage >= 60 ? 'bg-yellow-500' : 'bg-red-500'
            }" style="width: ${percentage}%"></div>
          </div>
          <p class="text-sm ${
            percentage >= 80 ? 'text-green-600' : 
            percentage >= 60 ? 'text-yellow-600' : 'text-red-600'
          } font-medium">${aspect.message}</p>
        `;
        
        container.appendChild(aspectElement);
      });
    }

    // Fungsi untuk menyelesaikan analisis
    function finalizeAnalysis() {
      // Hitung skor total
      const totalScore = analysisResults.aspects.reduce((sum, aspect) => sum + aspect.score, 0);
      const maxTotalScore = analysisResults.aspects.reduce((sum, aspect) => sum + aspect.maxScore, 0);
      const finalPercentage = Math.round((totalScore / maxTotalScore) * 100);
      
      // Update skor
      document.getElementById('score-text').textContent = `${totalScore}/${maxTotalScore}`;
      document.getElementById('score-description').textContent = `Dokumen Anda memenuhi ${finalPercentage}% persyaratan`;
      
      // Update status
      if (finalPercentage >= 80) {
        updateStatus("LAYAK DIAJUKAN", "bg-green-100 text-green-800", "fas fa-check-circle");
      } else if (finalPercentage >= 60) {
        updateStatus("PERLU PERBAIKAN", "bg-yellow-100 text-yellow-800", "fas fa-exclamation-triangle");
      } else {
        updateStatus("TIDAK LAYAK", "bg-red-100 text-red-800", "fas fa-times-circle");
      }
      
      // Generate rekomendasi
      generateRecommendations();
      
      // Animate progress bars
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
    }

    // Fungsi untuk menghasilkan rekomendasi
    function generateRecommendations() {
      const recommendationsList = document.getElementById('recommendations-list');
      recommendationsList.innerHTML = '';
      
      analysisResults.recommendations = [
        "Perbaiki margin atas menjadi 4cm dan margin kiri menjadi 4cm",
        "Perjelas rumusan masalah agar lebih spesifik dan terarah",
        "Tambahkan 2 referensi terkait untuk memperkuat landasan teori",
        "Periksa konsistensi format heading dan sub-heading"
      ];
      
      analysisResults.recommendations.forEach(rec => {
        const li = document.createElement('li');
        li.textContent = rec;
        li.className = 'text-gray-700';
        recommendationsList.appendChild(li);
      });
    }

    // Fungsi untuk menyimpan hasil
    function saveResults() {
      // Simulasi penyimpanan
      const button = event.target;
      const originalText = button.innerHTML;
      
      button.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i> Menyimpan...';
      button.disabled = true;
      
      setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check mr-2"></i> Tersimpan!';
        setTimeout(() => {
          button.innerHTML = originalText;
          button.disabled = false;
        }, 2000);
      }, 1500);
    }
  </script>
</body>
</html>