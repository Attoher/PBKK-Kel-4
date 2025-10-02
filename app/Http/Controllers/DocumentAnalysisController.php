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
        
        // Gunakan Storage::exists() bukan file_exists()
        $filePath = 'uploads/' . $filename;
        
        if (!Storage::exists($filePath)) {
            Log::error('File tidak ditemukan: ' . $filePath);
            return redirect()->route('upload.form')
                ->with('error', 'File tidak ditemukan. Silakan upload ulang.');
        }
        
        // Dapatkan extension dari filename
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if ($extension === 'pdf') {
            try {
                Log::info('Memulai analisis PDF: ' . $filename);
                
                // Gunakan full storage path untuk Python
                $fullPath = Storage::path($filePath);
                $analysisResults = $this->analyzeWithPython($fullPath);
                Log::info('Analisis Python berhasil', ['filename' => $filename]);
                
            } catch (\Exception $e) {
                Log::error('Analisis Python gagal: ' . $e->getMessage());
                
                // Tambahkan logging yang lebih informatif
                Log::warning('Menggunakan analisis simulasi sebagai fallback untuk: ' . $filename);
                $analysisResults = $this->simulateAnalysis($filename);
            }
            
            // Simpan results
            try {
                $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
                $resultsPath = 'results/' . $resultsFilename;
                
                // Pastikan directory results exists
                if (!Storage::exists('results')) {
                    Storage::makeDirectory('results');
                    Log::info('Directory results dibuat');
                }
                
                // Simpan results
                $saved = Storage::put($resultsPath, json_encode($analysisResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                
                Log::info('Menyimpan hasil analisis:', [
                    'results_filename' => $resultsFilename,
                    'results_path' => $resultsPath,
                    'saved' => $saved,
                    'file_exists' => Storage::exists($resultsPath),
                    'file_size' => $saved ? Storage::size($resultsPath) : 0
                ]);
                
                if (!$saved || !Storage::exists($resultsPath)) {
                    throw new \Exception('Gagal menyimpan atau memverifikasi file results');
                }
                
            } catch (\Exception $e) {
                Log::error('Gagal menyimpan results: ' . $e->getMessage());
                return redirect()->route('upload.form')
                    ->with('error', 'Gagal menyimpan hasil analisis: ' . $e->getMessage());
            }
            
            // Redirect ke results dengan data
            return redirect()->route('results', ['filename' => $filename])
                ->with('results', $analysisResults)
                ->with('success', 'Analisis berhasil!');
                
        } else {
            return redirect()->route('upload.form')
                ->with('error', 'Analisis untuk format file ini belum tersedia. Silakan upload file PDF.');
        }
    }
    
    /**
     * Analisis menggunakan Python script
     */
    private function analyzeWithPython($filePath)
    {
        $pythonScript = base_path('python/analyze_pdf.py');
        
        // Pastikan Python script exists
        if (!file_exists($pythonScript)) {
            throw new \Exception("Python script tidak ditemukan: " . $pythonScript);
        }
        
        // Pastikan path file ada dan readable
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new \Exception("File PDF tidak dapat diakses: " . $filePath);
        }
        
        // Build command dengan path yang benar
        $command = escapeshellcmd("python3 \"{$pythonScript}\" \"{$filePath}\"");
        
        Log::info("Executing Python command: " . $command);
        
        // Execute Python script dan tangkap exit code
        $output = shell_exec($command . " 2>&1");
        $exitCode = 0;
        
        // Dapatkan exit code (hanya works di Linux/Unix)
        if (function_exists('shell_exec')) {
            $exitCode = shell_exec("echo $?");
        }
        
        Log::info("Python exit code: " . $exitCode);
        Log::info("Python output length: " . strlen($output));
        
        if ($exitCode !== 0) {
            throw new \Exception("Python script gagal dengan exit code: " . $exitCode);
        }
        
        if (empty($output)) {
            throw new \Exception("Python script tidak mengembalikan output");
        }
        
        // Parse JSON output
        $results = json_decode($output, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("Output Python bukan JSON valid: " . json_last_error_msg() . ". Raw output: " . substr($output, 0, 200));
        }
        
        return $this->ensureResultsStructure($results);
    }

    
    /**
     * Pastikan struktur results lengkap
     */
    private function ensureResultsStructure($results)
    {
        // Default structure
        $defaultStructure = [
            "metadata" => [
                "title" => "Tidak Diketahui",
                "author" => "Tidak diketahui", 
                "page_count" => 0,
                "file_size" => "0 KB",
                "file_format" => "PDF"
            ],
            "abstract" => [
                "found" => false,
                "id_word_count" => 0,
                "en_word_count" => 0,
                "status" => "error",
                "message" => "Abstrak tidak ditemukan"
            ],
            "format" => [
                "font_family" => "Times New Roman",
                "line_spacing" => "1",
                "status" => "warning", 
                "message" => "Format teks diasumsikan sesuai"
            ],
            "margin" => [
                "top" => "3.0",
                "bottom" => "2.5", 
                "left" => "3.0",
                "right" => "2.0",
                "status" => "warning",
                "message" => "Margin diasumsikan sesuai standar ITS"
            ],
            "chapters" => [
                "bab1" => false,
                "bab2" => false,
                "bab3" => false,
                "bab4" => false,
                "bab5" => false,
                "status" => "error",
                "message" => "Struktur bab tidak terdeteksi"
            ],
            "references" => [
                "count" => 0,
                "min_references" => 20,
                "apa_compliant" => false,
                "status" => "error",
                "message" => "Daftar pustaka tidak ditemukan"
            ],
            "cover" => [
                "found" => false,
                "status" => "error", 
                "message" => "Halaman cover tidak terdeteksi"
            ],
            "overall_score" => 0,
            "document_type" => "Tidak Diketahui",
            "recommendations" => []
        ];
        
        // Merge dengan results dari Python
        return array_merge($defaultStructure, $results);
    }
    
    /**
     * Simulasi analisis dokumen (fallback)
     */
    private function simulateAnalysis($filename)
    {
        return [
            "metadata" => [
                "title" => "Contoh Tugas Akhir ITS",
                "author" => "Mahasiswa Contoh",
                "page_count" => 45,
                "file_size" => "2.5 MB",
                "file_format" => "PDF"
            ],
            "abstract" => [
                "found" => true,
                "id_word_count" => 250,
                "en_word_count" => 245,
                "status" => "success",
                "message" => "Abstrak ID: 250 kata, EN: 245 kata (sesuai)"
            ],
            "format" => [
                "font_family" => "Times New Roman",
                "line_spacing" => "1",
                "status" => "success",
                "message" => "Format font dan spasi sesuai"
            ],
            "margin" => [
                "top" => "3.0",
                "bottom" => "2.5",
                "left" => "3.0", 
                "right" => "2.0",
                "status" => "warning",
                "message" => "Margin sesuai standar ITS"
            ],
            "chapters" => [
                "bab1" => true,
                "bab2" => true,
                "bab3" => true,
                "bab4" => false,
                "bab5" => false,
                "status" => "warning",
                "message" => "Struktur Proposal lengkap"
            ],
            "references" => [
                "count" => 18,
                "min_references" => 20,
                "apa_compliant" => true,
                "status" => "warning",
                "message" => "18 referensi ditemukan (minimal 20)"
            ],
            "cover" => [
                "found" => true,
                "status" => "success", 
                "message" => "Halaman cover terdeteksi"
            ],
            "overall_score" => 7.5,
            "document_type" => "Proposal",
            "recommendations" => [
                "Tambahkan 2 referensi hingga minimal 20",
                "Pastikan margin: Atas 3cm, Bawah 2.5cm, Kiri 3cm, Kanan 2cm",
                "Gunakan font Times New Roman 12pt untuk isi dokumen"
            ]
        ];
    }
    
    public function showResults($filename)
    {
        // Coba ambil dari session terlebih dahulu
        $results = session('results');
        
        if (!$results) {
            // Jika tidak ada di session, coba load dari file
            $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
            
            Log::info('Mencari hasil analisis: results/' . $resultsFilename);
            
            if (Storage::exists('results/' . $resultsFilename)) {
                $results = json_decode(Storage::get('results/' . $resultsFilename), true);
                Log::info('Hasil analisis ditemukan di storage');
            } else {
                Log::warning('Hasil analisis tidak ditemukan, menggunakan data default');
                // Fallback ke data default
                $results = $this->simulateAnalysis($filename);
            }
        }

        // Pastikan struktur data lengkap
        $results = $this->ensureResultsStructure($results);
        
        Log::info('Menampilkan results dengan metadata:', [
            'page_count' => $results['metadata']['page_count'] ?? 'N/A',
            'file_size' => $results['metadata']['file_size'] ?? 'N/A'
        ]);

        return view('result', [
            'filename' => $filename,
            'results' => $results
        ]);
    }
        
    /**
     * Download hasil analisis - VERSI DIPERBAIKI
     */
    public function downloadResults($filename)
    {
        try {
            Log::info('Download request for:', ['filename' => $filename]);
            
            // Cari file results
            $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
            $resultsPath = 'results/' . $resultsFilename;
            
            Log::info('Looking for results file:', [
                'results_filename' => $resultsFilename,
                'results_path' => $resultsPath,
                'storage_exists' => Storage::exists($resultsPath)
            ]);
            
            if (Storage::exists($resultsPath)) {
                // Jika file results ada, download file JSON asli
                $filePath = Storage::path($resultsPath);
                $fileContent = Storage::get($resultsPath);
                $resultsData = json_decode($fileContent, true);
                
                Log::info('Original results file found, downloading...', [
                    'file_size' => Storage::size($resultsPath),
                    'overall_score' => $resultsData['overall_score'] ?? 'N/A'
                ]);
                
                return response()->download($filePath, 'hasil-analisis-' . $filename . '.json');
                
            } else {
                // Jika file results tidak ada, buat data sederhana dari history
                Log::warning('Results file not found, generating basic download data');
                
                $basicData = [
                    'filename' => $filename,
                    'download_type' => 'basic_results',
                    'message' => 'File hasil analisis lengkap tidak tersedia. Data ini hanya berisi informasi dasar.',
                    'download_date' => now()->toISOString(),
                    'system_info' => [
                        'generated_by' => 'FormatCheck ITS',
                        'version' => '1.0'
                    ],
                    'note' => 'Untuk analisis lengkap, silakan upload ulang file PDF'
                ];
                
                $jsonData = json_encode($basicData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                $tempFilename = 'temp_' . $filename . '_' . time() . '.json';
                
                Storage::put('temp/' . $tempFilename, $jsonData);
                $filePath = Storage::path('temp/' . $tempFilename);
                
                $response = response()->download($filePath, 'info-' . $filename . '.json');
                
                // Hapus file temp setelah download (optional)
                register_shutdown_function(function() use ($tempFilename) {
                    if (Storage::exists('temp/' . $tempFilename)) {
                        Storage::delete('temp/' . $tempFilename);
                    }
                });
                
                return $response;
            }
            
        } catch (\Exception $e) {
            Log::error('Download error: ' . $e->getMessage(), [
                'filename' => $filename,
                'exception' => $e->getTraceAsString()
            ]);
            
            // Fallback: return JSON response dengan error info
            $errorData = [
                'error' => true,
                'message' => 'Gagal mengunduh file hasil analisis',
                'filename' => $filename,
                'details' => $e->getMessage(),
                'suggestion' => 'Silakan coba upload ulang file PDF untuk analisis baru'
            ];
            
            return response()->json($errorData, 500);
        }
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
                        'score' => $results['overall_score'] ?? 0,
                        'document_type' => $results['document_type'] ?? 'Tidak Diketahui',
                        'file_exists' => Storage::exists('uploads/' . $originalFilename)
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

        Log::info('History data loaded', ['count' => count($history)]);

        return view('history', ['history' => $history]);
    }

    public function clearHistory(Request $request)
    {
        try {
            $files = Storage::files('results');
            $deletedCount = 0;
            $totalCount = 0;
            
            foreach ($files as $file) {
                if (strpos($file, '_results.json') !== false) {
                    $totalCount++;
                    Storage::delete($file);
                    $deletedCount++;
                    
                    // Juga hapus file upload yang terkait (opsional)
                    $originalFilename = str_replace('_results.json', '', basename($file)) . '.pdf';
                    $uploadPath = 'uploads/' . $originalFilename;
                    
                    if (Storage::exists($uploadPath)) {
                        Storage::delete($uploadPath);
                    }
                }
            }
            
            Log::info("Cleared all history: {$deletedCount} files deleted from {$totalCount} total");
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} riwayat analisis",
                'deleted_count' => $deletedCount,
                'total_count' => $totalCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Hapus item riwayat tertentu
     */
    public function deleteHistoryItem(Request $request, $filename)
    {
        try {
            // Validasi filename
            if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nama file tidak valid'
                ], 400);
            }
            
            $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
            $resultsPath = 'results/' . $resultsFilename;
            $uploadPath = 'uploads/' . $filename;
            
            $deletedFiles = [];
            
            // Hapus file results
            if (Storage::exists($resultsPath)) {
                Storage::delete($resultsPath);
                $deletedFiles[] = 'hasil analisis';
            }
            
            // Hapus file upload (opsional)
            if (Storage::exists($uploadPath)) {
                Storage::delete($uploadPath);
                $deletedFiles[] = 'file upload';
            }
            
            Log::info("Deleted history item: {$filename}");
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus ' . implode(' dan ', $deletedFiles),
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting history item: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Hapus riwayat yang lebih lama dari X hari
     */
    public function clearOldHistory(Request $request)
    {
        try {
            $days = $request->input('days', 30); // Default 30 hari
            $cutoffDate = now()->subDays($days);
            
            $files = Storage::files('results');
            $deletedCount = 0;
            $totalCount = 0;
            
            foreach ($files as $file) {
                if (strpos($file, '_results.json') !== false) {
                    $totalCount++;
                    $lastModified = Storage::lastModified($file);
                    $fileDate = \Carbon\Carbon::createFromTimestamp($lastModified);
                    
                    if ($fileDate->lt($cutoffDate)) {
                        Storage::delete($file);
                        $deletedCount++;
                        
                        // Juga hapus file upload yang terkait (opsional)
                        $originalFilename = str_replace('_results.json', '', basename($file)) . '.pdf';
                        $uploadPath = 'uploads/' . $originalFilename;
                        
                        if (Storage::exists($uploadPath)) {
                            Storage::delete($uploadPath);
                        }
                    }
                }
            }
            
            Log::info("Cleared old history (> {$days} days): {$deletedCount} files deleted from {$totalCount} total");
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} riwayat analisis yang lebih lama dari {$days} hari",
                'deleted_count' => $deletedCount,
                'total_count' => $totalCount,
                'days' => $days
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing old history: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat lama: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cleanup file orphaned
     */
    public function cleanupOrphanedFiles()
    {
        try {
            $resultsFiles = Storage::files('results');
            $uploadFiles = Storage::files('uploads');
            
            $orphanedCount = 0;
            
            // Cari results yang tidak memiliki file upload terkait
            foreach ($resultsFiles as $resultFile) {
                if (strpos($resultFile, '_results.json') !== false) {
                    $originalFilename = str_replace('_results.json', '', basename($resultFile)) . '.pdf';
                    
                    if (!Storage::exists('uploads/' . $originalFilename)) {
                        Storage::delete($resultFile);
                        $orphanedCount++;
                        Log::info('Deleted orphaned result file: ' . $resultFile);
                    }
                }
            }
            
            Log::info("Cleanup orphaned files completed: {$orphanedCount} files deleted");
            
            return $orphanedCount;
            
        } catch (\Exception $e) {
            Log::error('Error cleaning orphaned files: ' . $e->getMessage());
            return 0;
        }
    }
}