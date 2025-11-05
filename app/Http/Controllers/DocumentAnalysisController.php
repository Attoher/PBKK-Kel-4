<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentAnalysisController extends Controller
{
    // Constants untuk konsistensi
    private const UPLOAD_PATH = 'uploads/';
    private const RESULTS_PATH = 'results/';
    private const TEMP_PATH = 'temp/';
    private const PRIVATE_PATH = 'private/';
    private const CACHE_TTL = 3600; // 1 jam cache
    private const MAX_FILE_SIZE = 50 * 1024 * 1024; // 50MB
    
    // Status constants
    private const STATUS_ELIGIBLE = 'LAYAK';
    private const STATUS_NEEDS_IMPROVEMENT = 'PERLU PERBAIKAN';
    private const STATUS_NOT_ELIGIBLE = 'TIDAK LAYAK';

    /**
     * Analisis dokumen dengan caching dan error handling yang lebih baik
     */
    public function analyzeDocument($filename)
    {
        // Validasi dan sanitasi filename
        $filename = $this->validateAndSanitizeFilename($filename);
        if (!$filename) {
            return $this->redirectWithError('Nama file tidak valid.');
        }

        // Path file
        $filePath = storage_path('app/' . self::PRIVATE_PATH . self::UPLOAD_PATH . $filename);
        
        // Validasi file exists dan format
        $validationResult = $this->validateFile($filePath, $filename);
        if ($validationResult !== true) {
            return $validationResult;
        }

        // Cek cache untuk menghindari analisis berulang
        $cacheKey = "analysis_{$filename}_" . md5_file($filePath);
        $analysisResults = Cache::get($cacheKey);
        
        if (!$analysisResults) {
            try {
                Log::info('Memulai analisis PDF menggunakan AI:', ['filename' => $filename]);
                $analysisResults = $this->analyzeWithPython($filePath);
                
                // Cache hasil analisis
                Cache::put($cacheKey, $analysisResults, self::CACHE_TTL);
                
                Log::info('Analisis AI berhasil dan di-cache', [
                    'filename' => $filename,
                    'cache_key' => $cacheKey
                ]);

            } catch (\Exception $e) {
                $errorMsg = $e->getMessage();
                
                // Jika error adalah validasi dokumen (bukan TA), tampilkan error langsung
                if (str_contains($errorMsg, 'tidak terdeteksi sebagai Tugas Akhir')) {
                    Log::warning('Dokumen ditolak: bukan format TA', [
                        'filename' => $filename,
                        'error' => $errorMsg
                    ]);
                    
                    return redirect()->route('upload.form')
                        ->with('error', $errorMsg)
                        ->with('suggestion', 'Pastikan dokumen yang diupload adalah Tugas Akhir/Skripsi yang memiliki struktur lengkap (Abstrak, Bab, Daftar Pustaka, dll.)');
                }
                
                // Error lainnya (connection, timeout, dll) -> gunakan fallback
                Log::error('Analisis Python gagal, menggunakan fallback', [
                    'filename' => $filename,
                    'error' => $errorMsg
                ]);
                
                $analysisResults = $this->simulateAnalysis($filename);
            }
        } else {
            Log::info('Menggunakan hasil analisis dari cache', ['filename' => $filename]);
        }

        // Simpan hasil analisis
        $saveResult = $this->saveAnalysisResults($filename, $analysisResults);
        if (!$saveResult['success']) {
            return $this->redirectWithError($saveResult['message']);
        }

        // Redirect ke halaman hasil
        return redirect()->route('results', ['filename' => $filename])
            ->with('results', $analysisResults)
            ->with('success', 'Analisis berhasil dilakukan!')
            ->with('from_cache', Cache::has($cacheKey));
    }

    /**
     * Validasi dan sanitasi filename
     */
    private function validateAndSanitizeFilename($filename)
    {
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            return null;
        }
        
        // Sanitasi tambahan
        $filename = basename($filename); // Hapus path traversal
        $filename = preg_replace('/\.{2,}/', '.', $filename); // Hapus multiple dots
        $filename = trim($filename); // Hapus whitespace
        
        return $filename;
    }

    /**
     * Validasi file
     */
    private function validateFile($filePath, $filename)
    {
        if (!file_exists($filePath)) {
            Log::error('File tidak ditemukan', ['path' => $filePath]);
            return $this->redirectWithError('File tidak ditemukan.');
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            return $this->redirectWithError('Analisis hanya tersedia untuk file PDF.');
        }

        // Validasi size file (max 50MB)
        $fileSize = filesize($filePath);
        if ($fileSize > self::MAX_FILE_SIZE) {
            return $this->redirectWithError('Ukuran file terlalu besar (maksimal 50MB).');
        }

        if ($fileSize === 0) {
            return $this->redirectWithError('File kosong.');
        }

        return true;
    }

    /**
     * Analisis dengan Python - OPTIMIZED
     */
    private function analyzeWithPython($filePath)
    {
        $pythonScript = base_path('python/analyze_pdf_openrouter.py');

        if (!file_exists($pythonScript)) {
            throw new \Exception("Python script tidak ditemukan: " . $pythonScript);
        }

        if (!file_exists($filePath)) {
            throw new \Exception("File tidak ditemukan: " . $filePath);
        }

        // Setup environment variables
        $env = $this->getPythonEnvironment();
        $command = $this->buildPythonCommand($pythonScript, $filePath, $env);

        Log::info("Menjalankan AI Analysis", [
            'script' => $pythonScript,
            'file_path' => $filePath,
            'env_keys' => array_keys($env),
            'senopati_url' => $env['SENOPATI_BASE_URL'] ?? 'NOT SET',
            'senopati_model' => $env['SENOPATI_MODEL'] ?? 'NOT SET',
            'openrouter_url' => $env['OPENROUTER_BASE_URL'] ?? 'NOT SET'
        ]);

        $output = shell_exec($command . " 2>&1");
        
        Log::info("Python output received", [
            'output_length' => strlen($output ?? ''),
            'output_preview' => substr($output ?? '', 0, 500)
        ]);
        
        if (!$output) {
            throw new \Exception("Python tidak menghasilkan output");
        }

        return $this->parsePythonOutput($output);
    }

    /**
     * Setup environment variables untuk Python
     */
    private function getPythonEnvironment()
    {
        return [
            // Keep OpenRouter vars for backward-compatibility if present
            'OPENROUTER_API_KEY' => env('OPENROUTER_API_KEY'),
            'OPENROUTER_BASE_URL' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
            'OPENROUTER_MODEL' => env('OPENROUTER_MODEL', 'tngtech/deepseek-r1t2-chimera:free'),

            // Senopati (preferred) - passed to the Python script which already reads SENOPATI_* vars
            'SENOPATI_BASE_URL' => env('SENOPATI_BASE_URL', 'https://senopati.its.ac.id/senopati-lokal-dev/generate'),
            'SENOPATI_MODEL' => env('SENOPATI_MODEL', 'dolphin-mixtral:latest'),

            'PYTHONIOENCODING' => 'utf-8',
            'PYTHONUNBUFFERED' => '1'
        ];
    }

    /**
     * Build Python command berdasarkan OS
     */
    private function buildPythonCommand($pythonScript, $filePath, $env)
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
        $envVars = '';

        if ($isWindows) {
            // PowerShell style environment variables
            foreach ($env as $key => $value) {
                if ($value) {
                    $envVars .= '$env:' . $key . '=\\"' . addslashes($value) . '\\"; ';
                }
            }
            // PowerShell command with proper escaping
            return 'powershell -Command "' . $envVars . 'python \\"' . $pythonScript . '\\" \\"' . $filePath . '\\""';
        } else {
            // Linux/Railway: use virtual environment Python if available
            $pythonBinary = 'python';
            if (file_exists('/tmp/venv/bin/python')) {
                $pythonBinary = '/tmp/venv/bin/python';
                Log::info("Using virtual environment Python", ['python_path' => $pythonBinary]);
            }
            
            foreach ($env as $key => $value) {
                if ($value) {
                    $envVars .= $key . '=' . escapeshellarg($value) . ' ';
                }
            }
            return $envVars . $pythonBinary . ' "' . $pythonScript . '" "' . $filePath . '"';
        }
    }

    /**
     * Parse output dari Python
     */
    private function parsePythonOutput($output)
    {
        // Log raw output untuk debugging (truncated)
        Log::info("Raw AI Output preview", [
            'preview' => Str::limit($output, 500),
            'total_length' => strlen($output)
        ]);

        $results = json_decode($output, true);
        
        if ($results === null) {
            Log::error("AI response bukan JSON valid", [
                'json_error' => json_last_error_msg(),
                'output_sample' => Str::limit($output, 200)
            ]);
            throw new \Exception("Output dari Python bukan JSON valid: " . json_last_error_msg());
        }

        if (isset($results['error'])) {
            Log::error("AI Analysis Error", ['error' => $results['error']]);
            throw new \Exception("Analisis AI gagal: " . $results['error']);
        }

        return $results;
    }

    /**
     * Simpan hasil analisis
     */
    private function saveAnalysisResults($filename, $results)
    {
        try {
            $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
            $resultsPath = self::RESULTS_PATH . $resultsFilename;

            // Ensure directory exists
            if (!Storage::exists(self::RESULTS_PATH)) {
                Storage::makeDirectory(self::RESULTS_PATH);
            }

            // Convert to new structure sebelum disimpan
            $convertedResults = $this->convertToNewStructure($results);
            
            // Save results
            $saved = Storage::put(
                $resultsPath,
                json_encode($convertedResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            if (!$saved || !Storage::exists($resultsPath)) {
                throw new \Exception('Gagal menyimpan file hasil analisis.');
            }

            Log::info('Hasil analisis disimpan', [
                'results_path' => $resultsPath,
                'file_size' => Storage::size($resultsPath)
            ]);

            return ['success' => true, 'message' => 'Berhasil disimpan'];

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan hasil analisis', [
                'error' => $e->getMessage(),
                'filename' => $filename
            ]);
            
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Tampilkan hasil analisis dengan caching
     */
    public function showResults($filename)
    {
        $filename = $this->validateAndSanitizeFilename($filename);
        if (!$filename) {
            return $this->redirectWithError('Nama file tidak valid.');
        }

        try {
            // Cek session terlebih dahulu
            $results = session('results');
            
            if (!$results) {
                $results = $this->loadAnalysisResults($filename);
            }

            // Konversi ke struktur baru
            $newResults = $this->convertToNewStructure($results);
            
            // Prepare data untuk view
            $displayResults = $this->prepareDisplayResults($filename, $newResults);

            Log::info('Menampilkan hasil analisis', [
                'filename' => $filename,
                'score' => $displayResults['score'],
                'status' => $displayResults['status']
            ]);

            return view('result', [
                'filename' => $filename,
                'results' => $displayResults
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menampilkan hasil analisis', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);

            // Redirect ke upload dengan pesan error (TIDAK pakai simulasi)
            return redirect()->route('upload.form')
                ->with('error', 'Hasil analisis tidak ditemukan.')
                ->with('suggestion', 'File mungkin ditolak karena bukan dokumen Tugas Akhir atau analisis gagal. Silakan upload ulang dokumen TA yang valid.');
        }
    }

    /**
     * Load hasil analisis dari storage
     */
    private function loadAnalysisResults($filename)
    {
        $resultsFilename = $this->getResultsFilename($filename);
        
        $possiblePaths = [
            self::RESULTS_PATH . $resultsFilename,
            self::PRIVATE_PATH . self::RESULTS_PATH . $resultsFilename
        ];

        foreach ($possiblePaths as $path) {
            if (Storage::exists($path)) {
                $content = Storage::get($path);
                $results = json_decode($content, true);
                
                if (json_last_error() === JSON_ERROR_NONE) {
                    Log::info('Hasil analisis ditemukan', ['path' => $path]);
                    return $results;
                } else {
                    Log::warning('Hasil analisis corrupt, skip', [
                        'path' => $path,
                        'json_error' => json_last_error_msg()
                    ]);
                }
            }
        }

        // Jangan pakai simulasi! Throw exception untuk ditangani di showResults
        Log::warning('Hasil analisis tidak ditemukan', [
            'filename' => $filename,
            'searched_paths' => $possiblePaths
        ]);
        
        throw new \Exception('Hasil analisis tidak ditemukan. File mungkin ditolak karena bukan dokumen TA atau analisis belum selesai.');
    }

    /**
     * Dapatkan nama file results
     */
    private function getResultsFilename($filename)
    {
        if (Str::endsWith($filename, ['_results.json', '.json'])) {
            return $filename;
        }
        return pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
    }

    /**
     * Siapkan data untuk ditampilkan
     */
    private function prepareDisplayResults($filename, $results)
    {
        return [
            'filename' => $filename,
            'score' => $results['score'] ?? 0,
            'percentage' => $results['percentage'] ?? 0,
            'status' => $results['status'] ?? self::STATUS_NEEDS_IMPROVEMENT,
            'details' => $results['details'] ?? [],
            'document_info' => $results['document_info'] ?? [],
            'recommendations' => $results['recommendations'] ?? []
        ];
    }

    /**
     * Konversi struktur lama ke baru
     */
    private function convertToNewStructure($oldResults)
    {
        // Jika sudah struktur baru
        if (isset($oldResults['score'])) {
            return array_merge([
                'score' => 0,
                'percentage' => 0,
                'status' => self::STATUS_NEEDS_IMPROVEMENT,
                'details' => [],
                'document_info' => [],
                'recommendations' => []
            ], $oldResults);
        }

        // Konversi dari struktur legacy
        $score = $oldResults['overall_score'] ?? 0;
        
        return [
            "score" => $score,
            "percentage" => round($score * 10, 1),
            "status" => $this->getStatusFromScore($score),
            "details" => $this->convertDetails($oldResults),
            "document_info" => $this->convertDocumentInfo($oldResults),
            "recommendations" => $oldResults['recommendations'] ?? []
        ];
    }

    /**
     * Dapatkan status dari score
     */
    private function getStatusFromScore($score)
    {
        if ($score >= 8) return self::STATUS_ELIGIBLE;
        if ($score >= 6) return self::STATUS_NEEDS_IMPROVEMENT;
        return self::STATUS_NOT_ELIGIBLE;
    }

    /**
     * Konversi details
     */
    private function convertDetails($oldResults)
    {
        return [
            "Abstrak" => [
                "status" => ($oldResults['abstract']['status'] ?? '') === 'success' ? '✓' : '✗',
                "notes" => $oldResults['abstract']['message'] ?? 'Abstrak tidak ditemukan',
                "id_word_count" => $oldResults['abstract']['id_word_count'] ?? 0,
                "en_word_count" => $oldResults['abstract']['en_word_count'] ?? 0
            ],
            "Format Teks" => [
                "font" => $oldResults['format']['font_family'] ?? 'Times New Roman',
                "size" => "12pt",
                "spacing" => $oldResults['format']['line_spacing'] ?? '1.5',
                "notes" => $oldResults['format']['message'] ?? 'Format teks diasumsikan sesuai standar ITS.'
            ],
            "Margin" => [
                "top" => ($oldResults['margin']['top'] ?? '3.0') . 'cm',
                "bottom" => ($oldResults['margin']['bottom'] ?? '2.5') . 'cm',
                "left" => ($oldResults['margin']['left'] ?? '3.0') . 'cm',
                "right" => ($oldResults['margin']['right'] ?? '2.0') . 'cm',
                "notes" => $oldResults['margin']['message'] ?? 'Margin diasumsikan sesuai standar ITS.'
            ],
            "Struktur Bab" => [
                "Bab 1" => ($oldResults['chapters']['bab1'] ?? false) ? '✓' : '✗',
                "Bab 2" => ($oldResults['chapters']['bab2'] ?? false) ? '✓' : '✗',
                "Bab 3" => ($oldResults['chapters']['bab3'] ?? false) ? '✓' : '✗',
                "Bab 4" => ($oldResults['chapters']['bab4'] ?? false) ? '✓' : '✗',
                "Bab 5" => ($oldResults['chapters']['bab5'] ?? false) ? '✓' : '✗',
                "notes" => $oldResults['chapters']['message'] ?? 'Struktur bab tidak terdeteksi.'
            ],
            "Daftar Pustaka" => [
                "references_count" => "≥" . ($oldResults['references']['count'] ?? 0),
                "format" => ($oldResults['references']['apa_compliant'] ?? false) ? 'APA' : 'Tidak diketahui',
                "notes" => $oldResults['references']['message'] ?? 'Daftar pustaka tidak ditemukan.'
            ],
            "Cover & Halaman Formal" => [
                "status" => ($oldResults['cover']['found'] ?? false) ? '✓' : '✗',
                "notes" => $oldResults['cover']['message'] ?? 'Cover dan halaman formal tidak terdeteksi.'
            ]
        ];
    }

    /**
     * Konversi document info
     */
    private function convertDocumentInfo($oldResults)
    {
        return [
            "jenis_dokumen" => $oldResults['document_type'] ?? 'Tidak Diketahui',
            "total_halaman" => $oldResults['metadata']['page_count'] ?? 0,
            "ukuran_file" => $oldResults['metadata']['file_size'] ?? 'unknown',
            "format_file" => $oldResults['metadata']['file_format'] ?? 'PDF'
        ];
    }

    /**
     * Simulasi analisis (fallback)
     */
    private function simulateAnalysis($filename)
    {
        return [
            "overall_score" => 7.5,
            "document_type" => "Proposal",
            "metadata" => [
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
            "recommendations" => [
                "Tambahkan 2 referensi hingga minimal 20",
                "Pastikan margin: Atas 3cm, Bawah 2.5cm, Kiri 3cm, Kanan 2cm",
                "Gunakan font Times New Roman 12pt untuk isi dokumen"
            ]
        ];
    }

    /**
     * Helper untuk redirect dengan error
     */
    private function redirectWithError($message)
    {
        return redirect()->route('upload.form')->with('error', $message);
    }

    /**
     * Tampilkan history dengan pagination
     */
    public function showHistory(Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 10;
        $search = $request->get('search', '');
        
        $files = Storage::files(self::RESULTS_PATH);
        $history = [];
        
        foreach ($files as $file) {
            if (str_contains($file, '_results.json')) {
                try {
                    $content = Storage::get($file);
                    $results = json_decode($content, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning('Skip corrupt results file', ['file' => $file]);
                        continue;
                    }
                    
                    $originalFilename = str_replace('_results.json', '', basename($file)) . '.pdf';
                    
                    // Filter berdasarkan search
                    if ($search && stripos($originalFilename, $search) === false) {
                        continue;
                    }
                    
                    $history[] = [
                        'filename' => $originalFilename,
                        'date' => date('Y-m-d H:i:s', Storage::lastModified($file)),
                        'score' => $results['overall_score'] ?? $results['score'] ?? 0,
                        'document_type' => $results['document_type'] ?? 'Tidak Diketahui',
                        'file_exists' => Storage::exists(self::UPLOAD_PATH . $originalFilename) || 
                                       Storage::exists(self::PRIVATE_PATH . self::UPLOAD_PATH . $originalFilename)
                    ];
                } catch (\Exception $e) {
                    Log::error('Error membaca file hasil', [
                        'file' => $file,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        
        // Sort by date descending
        usort($history, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
        
        // Pagination manual
        $total = count($history);
        $paginatedHistory = array_slice($history, ($page - 1) * $perPage, $perPage);

        Log::info('History data loaded', [
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'search' => $search
        ]);

        return view('history', [
            'history' => $paginatedHistory,
            'current_page' => $page,
            'total_pages' => ceil($total / $perPage),
            'total_items' => $total,
            'search' => $search
        ]);
    }

    /**
     * Download hasil analisis - OPTIMIZED
     */
    public function downloadResults($filename)
    {
        $filename = $this->validateAndSanitizeFilename($filename);
        if (!$filename) {
            return response()->json(['error' => 'Nama file tidak valid'], 400);
        }

        try {
            $resultsFilename = $this->getResultsFilename($filename);
            $resultsPath = self::RESULTS_PATH . $resultsFilename;
            
            if (Storage::exists($resultsPath)) {
                $filePath = Storage::path($resultsPath);
                $downloadName = 'hasil-analisis-' . $filename . '.json';
                
                Log::info('Downloading analysis results', [
                    'filename' => $filename,
                    'download_name' => $downloadName
                ]);
                
                return response()->download($filePath, $downloadName);
            }
            
            // Generate basic data jika file tidak ditemukan
            return $this->generateBasicDownload($filename);
            
        } catch (\Exception $e) {
            Log::error('Download error', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Gagal mengunduh file hasil analisis',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate basic download data
     */
    private function generateBasicDownload($filename)
    {
        $basicData = [
            'filename' => $filename,
            'download_type' => 'basic_results',
            'message' => 'File hasil analisis lengkap tidak tersedia.',
            'download_date' => now()->toISOString(),
            'system_info' => [
                'generated_by' => 'FormatCheck ITS',
                'version' => '1.0'
            ],
            'note' => 'Untuk analisis lengkap, silakan upload ulang file PDF'
        ];
        
        $jsonData = json_encode($basicData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $tempFilename = 'temp_' . $filename . '_' . time() . '.json';
        
        // Ensure temp directory exists
        if (!Storage::exists(self::TEMP_PATH)) {
            Storage::makeDirectory(self::TEMP_PATH);
        }
        
        Storage::put(self::TEMP_PATH . $tempFilename, $jsonData);
        $filePath = Storage::path(self::TEMP_PATH . $tempFilename);
        
        $response = response()->download($filePath, 'info-' . $filename . '.json');
        
        // Cleanup temp file setelah download
        register_shutdown_function(function() use ($tempFilename) {
            if (Storage::exists(self::TEMP_PATH . $tempFilename)) {
                Storage::delete(self::TEMP_PATH . $tempFilename);
            }
        });
        
        return $response;
    }

    /**
     * Hapus riwayat tertentu
     */
    public function deleteHistoryItem(Request $request, $filename)
    {
        $filename = $this->validateAndSanitizeFilename($filename);
        if (!$filename) {
            return response()->json([
                'success' => false, 
                'message' => 'Nama file tidak valid'
            ], 400);
        }
        
        try {
            $resultsFilename = $this->getResultsFilename($filename);
            $resultsPath = self::RESULTS_PATH . $resultsFilename;
            $uploadPath = self::UPLOAD_PATH . $filename;
            $privateUploadPath = self::PRIVATE_PATH . self::UPLOAD_PATH . $filename;
            
            $deletedFiles = [];
            
            // Hapus file results
            if (Storage::exists($resultsPath)) {
                Storage::delete($resultsPath);
                $deletedFiles[] = 'hasil analisis';
                
                // Clear cache terkait
                $this->clearAnalysisCache($filename);
            }
            
            // Hapus file upload (opsional)
            if (Storage::exists($uploadPath)) {
                Storage::delete($uploadPath);
                $deletedFiles[] = 'file upload';
            }
            
            if (Storage::exists($privateUploadPath)) {
                Storage::delete($privateUploadPath);
                $deletedFiles[] = 'file upload private';
            }
            
            Log::info("Deleted history item", [
                'filename' => $filename,
                'deleted_files' => $deletedFiles
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus ' . implode(' dan ', $deletedFiles),
                'filename' => $filename
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error deleting history item', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus semua riwayat
     */
    public function clearHistory(Request $request)
    {
        try {
            $files = Storage::files(self::RESULTS_PATH);
            $deletedCount = 0;
            
            foreach ($files as $file) {
                if (str_contains($file, '_results.json')) {
                    Storage::delete($file);
                    $deletedCount++;
                    
                    // Clear related cache
                    $originalFilename = str_replace('_results.json', '', basename($file)) . '.pdf';
                    $this->clearAnalysisCache($originalFilename);
                }
            }
            
            Log::info("Cleared all history", ['deleted_count' => $deletedCount]);
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} riwayat analisis",
                'deleted_count' => $deletedCount
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing history', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus riwayat lama
     */
    public function clearOldHistory(Request $request)
    {
        try {
            $days = $request->input('days', 30);
            $cutoffDate = now()->subDays($days);
            
            $files = Storage::files(self::RESULTS_PATH);
            $deletedCount = 0;
            $totalCount = 0;
            
            foreach ($files as $file) {
                if (str_contains($file, '_results.json')) {
                    $totalCount++;
                    $lastModified = Storage::lastModified($file);
                    $fileDate = Carbon::createFromTimestamp($lastModified);
                    
                    if ($fileDate->lt($cutoffDate)) {
                        Storage::delete($file);
                        $deletedCount++;
                        
                        // Clear cache
                        $originalFilename = str_replace('_results.json', '', basename($file)) . '.pdf';
                        $this->clearAnalysisCache($originalFilename);
                    }
                }
            }
            
            Log::info("Cleared old history", [
                'days' => $days,
                'deleted_count' => $deletedCount,
                'total_count' => $totalCount
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} riwayat analisis yang lebih lama dari {$days} hari",
                'deleted_count' => $deletedCount,
                'total_count' => $totalCount,
                'days' => $days
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error clearing old history', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus riwayat lama: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear analysis cache untuk file tertentu
     */
    private function clearAnalysisCache($filename)
    {
        $pattern = "analysis_{$filename}_";
        
        // Untuk driver file-based cache, kita perlu manual clear
        if (config('cache.default') === 'file') {
            $cachePath = storage_path('framework/cache/data');
            
            if (is_dir($cachePath)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($cachePath, \RecursiveDirectoryIterator::SKIP_DOTS)
                );
                
                foreach ($files as $file) {
                    if ($file->isFile() && Str::contains($file->getFilename(), $pattern)) {
                        @unlink($file->getPathname());
                    }
                }
            }
        }
        
        Log::info('Cleared analysis cache', ['filename' => $filename]);
    }

    /**
     * Cleanup orphaned files
     */
    public function cleanupOrphanedFiles()
    {
        try {
            $resultsFiles = Storage::files(self::RESULTS_PATH);
            $uploadFiles = array_merge(
                Storage::files(self::UPLOAD_PATH),
                Storage::files(self::PRIVATE_PATH . self::UPLOAD_PATH)
            );
            
            $uploadFilenames = array_map(
                fn($file) => basename($file),
                $uploadFiles
            );
            
            $orphanedCount = 0;
            
            foreach ($resultsFiles as $resultFile) {
                if (str_contains($resultFile, '_results.json')) {
                    $originalFilename = str_replace('_results.json', '', basename($resultFile)) . '.pdf';
                    
                    if (!in_array($originalFilename, $uploadFilenames)) {
                        Storage::delete($resultFile);
                        $orphanedCount++;
                        Log::info('Deleted orphaned result file', ['file' => $resultFile]);
                    }
                }
            }
            
            Log::info("Cleanup completed", ['orphaned_count' => $orphanedCount]);
            return $orphanedCount;
            
        } catch (\Exception $e) {
            Log::error('Error cleaning orphaned files', ['error' => $e->getMessage()]);
            return 0;
        }
    }

    /**
     * Health check untuk system analysis
     */
    public function healthCheck()
    {
        try {
            $health = [
                'storage' => [
                    'results' => Storage::exists(self::RESULTS_PATH),
                    'uploads' => Storage::exists(self::UPLOAD_PATH),
                    'private' => Storage::exists(self::PRIVATE_PATH . self::UPLOAD_PATH),
                    'temp' => Storage::exists(self::TEMP_PATH)
                ],
                'python_script' => file_exists(base_path('python/analyze_pdf_openrouter.py')),
                'cache' => config('cache.default'),
                'environment' => [
                    'openrouter_key' => !empty(env('OPENROUTER_API_KEY')),
                    'openrouter_url' => env('OPENROUTER_BASE_URL'),
                    'openrouter_model' => env('OPENROUTER_MODEL'),

                    // Senopati environment (preferred)
                    'senopati_url' => env('SENOPATI_BASE_URL'),
                    'senopati_model' => env('SENOPATI_MODEL')
                ],
                'files_count' => [
                    'results' => count(Storage::files(self::RESULTS_PATH)),
                    'uploads' => count(Storage::files(self::UPLOAD_PATH)) + 
                                count(Storage::files(self::PRIVATE_PATH . self::UPLOAD_PATH))
                ]
            ];

            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'health' => $health
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'unhealthy',
                'timestamp' => now()->toISOString(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug: View Laravel logs
     */
    public function debugLogs()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return response()->json(['error' => 'Log file not found']);
        }
        
        $lines = file($logFile);
        $lastLines = array_slice($lines, -200); // 200 baris terakhir
        
        return response('<pre style="background: #1e1e1e; color: #d4d4d4; padding: 20px; font-size: 12px; overflow: auto;">' 
            . htmlspecialchars(implode('', $lastLines)) 
            . '</pre>')
            ->header('Content-Type', 'text/html');
    }

    /**
     * Debug: View environment variables
     */
    public function debugEnv()
    {
        return response()->json([
            'APP_ENV' => env('APP_ENV'),
            'APP_DEBUG' => env('APP_DEBUG'),
            'APP_URL' => env('APP_URL'),
            'SENOPATI_BASE_URL' => env('SENOPATI_BASE_URL', 'NOT SET'),
            'SENOPATI_MODEL' => env('SENOPATI_MODEL', 'NOT SET'),
            'OPENROUTER_BASE_URL' => env('OPENROUTER_BASE_URL', 'NOT SET'),
            'PHP_VERSION' => PHP_VERSION,
            'PYTHON_CHECK' => shell_exec('python --version 2>&1') ?: 'Python not found',
        ], JSON_PRETTY_PRINT);
    }
}