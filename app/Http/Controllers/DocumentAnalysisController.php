<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DocumentAnalysisController extends Controller
{
    public function analyzeDocument($filename)
    {
        // Validasi filename
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            Log::error('Nama file tidak valid: ' . $filename);
            return redirect()->route('upload.form')
                ->with('error', 'Nama file tidak valid.');
        }
        
        $filePath = storage_path('app/uploads/' . $filename);
        
        // Log path file untuk debugging
        Log::info('Mencari file di: ' . $filePath);
        
        // Pastikan file exists
        if (!file_exists($filePath)) {
            Log::error('File tidak ditemukan: ' . $filePath);
            
            // Coba cek menggunakan Storage
            if (!Storage::exists('uploads/' . $filename)) {
                Log::error('File juga tidak ditemukan melalui Storage: uploads/' . $filename);
                
                // Tampilkan semua file di direktori uploads untuk debugging
                $files = Storage::files('uploads');
                Log::info('Files in uploads directory: ' . implode(', ', $files));
                
                return redirect()->route('upload.form')
                    ->with('error', 'File tidak ditemukan. Silakan upload ulang.');
            }
        }
        
        // Tentukan jenis file
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        if ($extension === 'pdf') {
            try {
                // Simulasikan analisis dokumen (untuk sementara)
                $analysisResults = $this->simulateAnalysis($filename);
                
                // Simpan hasil analisis
                $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
                Storage::put('results/' . $resultsFilename, json_encode($analysisResults));
                
                Log::info('Hasil analisis disimpan: results/' . $resultsFilename);
                
                // Redirect ke halaman results dengan data filename
                return redirect()->route('results', ['filename' => $filename]);
                
            } catch (\Exception $e) {
                Log::error('Gagal menganalisis dokumen: ' . $e->getMessage());
                return redirect()->route('upload.form')
                    ->with('error', 'Gagal menganalisis dokumen: ' . $e->getMessage());
            }
        } else {
            // Handle file Word (doc, docx)
            return redirect()->route('upload.form')
                ->with('error', 'Analisis untuk format file Word belum tersedia. Silakan upload file PDF.');
        }
    }
    
    /**
     * Simulasi analisis dokumen (sementara)
     */
    private function simulateAnalysis($filename)
    {
        // Hasil analisis simulasi
        return [
            "metadata" => [
                "title" => "Contoh Tugas Akhir",
                "author" => "Mahasiswa Contoh",
                "pages" => rand(40, 100),
                "filename" => $filename
            ],
            "abstract" => [
                "found" => true,
                "word_count" => rand(200, 300),
                "status" => "success",
                "message" => "Jumlah kata abstrak sesuai"
            ],
            "table_of_contents" => [
                "found" => true,
                "status" => "success",
                "message" => "Daftar isi ditemukan"
            ],
            "formatting" => [
                "margin_issues" => ["Margin atas: 3.0cm (harusnya 4.0cm)"],
                "font_consistency" => true,
                "status" => "warning",
                "message" => "Ada masalah margin"
            ],
            "chapters" => [
                "introduction" => true,
                "methodology" => true,
                "results" => true,
                "conclusion" => true,
                "status" => "success",
                "message" => "Semua bab penting ditemukan"
            ],
            "references" => [
                "count" => rand(20, 40),
                "min_references" => 20,
                "status" => "success",
                "message" => "Jumlah referensi mencukupi"
            ],
            "overall_score" => rand(70, 95) / 10,
            "recommendations" => [
                "Perbaiki margin atas menjadi 4cm",
                "Tambahkan 2 referensi terkini",
                "Periksa konsistensi format heading"
            ]
        ];
    }
    
    public function showResults($filename)
    {
        // Load hasil analisis
        $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
        
        Log::info('Mencari hasil analisis: results/' . $resultsFilename);
        
        if (!Storage::exists('results/' . $resultsFilename)) {
            Log::error('Hasil analisis tidak ditemukan: results/' . $resultsFilename);
            
            // Jika hasil analisis tidak ditemukan, redirect ke analisis
            return redirect()->route('analyze.document', ['filename' => $filename])
                ->with('error', 'Hasil analisis tidak ditemukan. Sedang menganalisis ulang...');
        }
        
        $analysisResults = json_decode(Storage::get('results/' . $resultsFilename), true);
        
        return view('result', [
            'filename' => $filename,
            'results' => $analysisResults
        ]);
    }
    
    public function downloadResults($filename)
    {
        $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
        
        if (!Storage::exists('results/' . $resultsFilename)) {
            return redirect()->route('results', ['filename' => $filename])
                ->with('error', 'Hasil analisis tidak ditemukan.');
        }
        
        $analysisResults = json_decode(Storage::get('results/' . $resultsFilename), true);
        
        // Generate PDF report (sederhana dulu)
        $html = "<h1>Hasil Analisis Dokumen: $filename</h1>";
        $html .= "<p>Skor: " . $analysisResults['overall_score'] . "/10</p>";
        $html .= "<h2>Rekomendasi:</h2><ul>";
        foreach ($analysisResults['recommendations'] as $rec) {
            $html .= "<li>$rec</li>";
        }
        $html .= "</ul>";
        
        // Untuk sekarang, kita simpan sebagai HTML sederhana
        $reportFilename = pathinfo($filename, PATHINFO_FILENAME) . '_report.html';
        Storage::put('reports/' . $reportFilename, $html);
        
        return response()->download(storage_path('app/reports/' . $reportFilename));
    }
    
    public function showHistory()
    {
        // Get list of analyzed files
        $files = Storage::files('results');
        $history = [];
        
        foreach ($files as $file) {
            if (strpos($file, '_results.json') !== false) {
                try {
                    $content = Storage::get($file);
                    $results = json_decode($content, true);
                    
                    $originalFilename = str_replace('_results.json', '', basename($file)) . '.pdf';
                    
                    $history[] = [
                        'filename' => $originalFilename,
                        'date' => date('Y-m-d H:i:s', Storage::lastModified($file)),
                        'score' => $results['overall_score'] ?? 0
                    ];
                } catch (\Exception $e) {
                    Log::error('Error membaca file hasil: ' . $file . ' - ' . $e->getMessage());
                }
            }
        }
        
        // Sort by date descending
        usort($history, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return view('history', ['history' => $history]);
    }
}
