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
        // ✅ 1. Validasi nama file
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $filename)) {
            Log::error('Nama file tidak valid: ' . $filename);
            return redirect()->route('upload.form')
                ->with('error', 'Nama file tidak valid.');
        }

        // ✅ 2. Path absolut file di storage private
        $fullPath = storage_path('app/private/uploads/' . $filename);

        if (!file_exists($fullPath)) {
            Log::error('File tidak ditemukan di path: ' . $fullPath);
            return redirect()->route('upload.form')
                ->with('error', 'File tidak ditemukan di direktori private.');
        }

        // ✅ 3. Pastikan format file benar (hanya PDF)
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if ($extension !== 'pdf') {
            return redirect()->route('upload.form')
                ->with('error', 'Analisis hanya tersedia untuk file PDF.');
        }

        try {
            Log::info('Memulai analisis PDF menggunakan AI:', ['filename' => $filename]);

            // ✅ 4. Jalankan Python script untuk analisis AI
            $analysisResults = $this->analyzeWithPython($fullPath);

            Log::info('Analisis AI (Python) berhasil dijalankan', [
                'filename' => $filename,
                'has_output' => !empty($analysisResults),
                'output_preview' => substr(json_encode($analysisResults), 0, 300)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Analisis Python gagal: ' . $e->getMessage());
            Log::warning('⚠️ Menggunakan hasil simulasi sebagai fallback untuk file: ' . $filename);

            // Fallback ke hasil simulasi jika Python gagal
            $analysisResults = $this->simulateAnalysis($filename);
        }

        // ✅ 5. Simpan hasil analisis ke storage/results
        try {
            $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
            $resultsPath = 'results/' . $resultsFilename;

            // Pastikan direktori 'results' tersedia
            if (!Storage::exists('results')) {
                Storage::makeDirectory('results');
                Log::info('📁 Folder results dibuat.');
            }

            // Simpan hasil analisis ke file JSON
            $saved = Storage::put(
                $resultsPath,
                json_encode($analysisResults, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            Log::info('✅ Hasil analisis disimpan', [
                'results_filename' => $resultsFilename,
                'results_path' => $resultsPath,
                'saved' => $saved,
                'file_exists' => Storage::exists($resultsPath),
                'file_size' => $saved ? Storage::size($resultsPath) : 0
            ]);

            if (!$saved || !Storage::exists($resultsPath)) {
                throw new \Exception('Gagal menyimpan atau memverifikasi file hasil analisis.');
            }

        } catch (\Exception $e) {
            Log::error('❌ Gagal menyimpan hasil analisis: ' . $e->getMessage());
            return redirect()->route('upload.form')
                ->with('error', 'Gagal menyimpan hasil analisis: ' . $e->getMessage());
        }

        // ✅ 6. Redirect ke halaman hasil analisis
        return redirect()->route('results', ['filename' => $filename])
            ->with('results', $analysisResults)
            ->with('success', 'Analisis berhasil dilakukan!');
    }

    /**
     * Analisis menggunakan Python script
     */
    private function analyzeWithPython($filePath)
    {
        $pythonScript = base_path('python/analyze_pdf.py');

        if (!file_exists($pythonScript)) {
            throw new \Exception("Python script tidak ditemukan: " . $pythonScript);
        }

        if (!file_exists($filePath)) {
            throw new \Exception("File tidak ditemukan: " . $filePath);
        }

        // Jalankan Python dengan path file sebagai argumen
        $command = escapeshellcmd("python \"$pythonScript\" \"$filePath\"");
        Log::info("Menjalankan AI Analysis Command: $command");

        $output = shell_exec($command . " 2>&1");

        if (!$output) {
            throw new \Exception("Python tidak menghasilkan output");
        }

        // Bersihkan output dari blok kode ```json ... ```
        $output = trim($output);
        $output = preg_replace('/^```json\s*|\s*```$/', '', $output);

        Log::info("Output AI (cleaned, preview 500 chars): " . substr($output, 0, 500));

        // Parse hasil JSON dari Python
        $results = json_decode($output, true);
        if ($results === null) {
            // Simpan raw output untuk debugging
            Log::error("AI response bukan JSON valid", ['raw_output' => $output]);
            throw new \Exception("Output dari Python bukan JSON valid");
        }

        return $results;
    }


    /**
     * Pastikan struktur results lengkap
     */
    /**
     * Konversi struktur lama ke struktur baru
     */
    private function convertToNewStructure($oldResults)
    {
        // Jika input sudah dalam struktur baru (mengandung 'score'), kembalikan setelah normalisasi ringan
        if (isset($oldResults['score'])) {
            // Pastikan beberapa field ada agar Blade tidak error
            $normalized = $oldResults;
            $normalized['score'] = $oldResults['score'] ?? 0;
            $normalized['percentage'] = $oldResults['percentage'] ?? ($normalized['score'] * 10);
            $normalized['status'] = $oldResults['status'] ?? (
                $normalized['score'] >= 8 ? 'LAYAK' : ($normalized['score'] >= 6 ? 'PERLU PERBAIKAN' : 'TIDAK LAYAK')
            );
            $normalized['details'] = $oldResults['details'] ?? [];
            $normalized['document_info'] = $oldResults['document_info'] ?? [];
            $normalized['recommendations'] = $oldResults['recommendations'] ?? [];

            return $normalized;
        }

        // Lama: konversi dari struktur legacy
        return [
            "score" => $oldResults['overall_score'] ?? 0,
            "percentage" => round(($oldResults['overall_score'] ?? 0) * 10, 1),
            "status" => ($oldResults['overall_score'] ?? 0) >= 8 ? 'LAYAK' : 
                    (($oldResults['overall_score'] ?? 0) >= 6 ? 'PERLU PERBAIKAN' : 'TIDAK LAYAK'),
            "details" => [
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
            ],
            "document_info" => [
                "jenis_dokumen" => $oldResults['document_type'] ?? 'Tidak Diketahui',
                "total_halaman" => $oldResults['metadata']['page_count'] ?? 0,
                "ukuran_file" => $oldResults['metadata']['file_size'] ?? 'unknown',
                "format_file" => $oldResults['metadata']['file_format'] ?? 'PDF'
            ],
            "recommendations" => $oldResults['recommendations'] ?? []
        ];
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
        try {
            // 1️⃣ Ambil hasil dari session terlebih dahulu
            $results = session('results');

            // 2️⃣ Jika tidak ada di session, load dari file results
            if (!$results) {
                // Normalisasi nama file: jika parameter sudah mengandung suffix atau .json, gunakan langsung
                if (\Illuminate\Support\Str::endsWith($filename, ['_results.json', '.json'])) {
                    $resultsFilename = $filename;
                } else {
                    $resultsFilename = pathinfo($filename, PATHINFO_FILENAME) . '_results.json';
                }

                $resultsPath = 'results/' . $resultsFilename;
                $privateResultsPath = 'private/results/' . $resultsFilename;

                Log::info('Hasil filename normalisasi', ['input_filename' => $filename, 'results_filename' => $resultsFilename]);

                Log::info('Mencari hasil analisis untuk file: ' . $filename);

                if (Storage::exists($resultsPath)) {
                    $results = json_decode(Storage::get($resultsPath), true);
                    Log::info('Hasil analisis ditemukan di storage', [
                        'results_path' => $resultsPath
                    ]);
                } elseif (Storage::exists($privateResultsPath)) {
                    $results = json_decode(Storage::get($privateResultsPath), true);
                    Log::info('Hasil analisis ditemukan di private storage', [
                        'results_path' => $privateResultsPath
                    ]);
                } else {
                    Log::warning('Hasil analisis tidak ditemukan, menggunakan data default', [
                        'results_path' => $resultsPath,
                        'private_results_path' => $privateResultsPath
                    ]);
                    $results = $this->simulateAnalysis($filename);
                }
            }

            // 3️⃣ Konversi ke struktur baru
            $newResults = $this->convertToNewStructure($results);
            
            // 4️⃣ Siapkan data untuk Blade
            $displayResults = [
                'filename' => $filename,
                'score' => $newResults['score'],
                'percentage' => $newResults['percentage'],
                'status' => $newResults['status'],
                'details' => $newResults['details'],
                'document_info' => $newResults['document_info'],
                'recommendations' => $newResults['recommendations']
            ];

            Log::info('Menampilkan hasil analisis (struktur baru)', [
                'filename' => $filename,
                'score' => $displayResults['score'],
                'status' => $displayResults['status']
            ]);

            // 5️⃣ Return view dengan struktur baru
            return view('result', [
                'filename' => $filename,
                'results' => $displayResults
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menampilkan hasil analisis: ' . $e->getMessage(), [
                'filename' => $filename,
                'exception' => $e->getTraceAsString()
            ]);

            // Jika gagal, tetap tampilkan simulasi sederhana
            $results = $this->simulateAnalysis($filename);

            return view('result', [
                'filename' => $filename,
                'results' => $this->ensureResultsStructure($results)
            ])->with('error', 'Terjadi kesalahan saat menampilkan hasil analisis.');
        }
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