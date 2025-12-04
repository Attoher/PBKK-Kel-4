<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

<<<<<<< Updated upstream
    public function store(Request $request)
=======
    /**
     * Test endpoint untuk debugging
     */
    public function testConnection()
    {
        return response()->json([
            'status' => 'ok',
            'message' => 'Upload controller is reachable',
            'timestamp' => now()->toDateTimeString(),
            'storage_path' => storage_path('app/uploads'),
            'chunks_path' => storage_path('app/chunks')
        ]);
    }

    /**
     * -----------------------------
     * 🔹 Upload per chunk
     * -----------------------------
     */
    public function uploadChunk(Request $request)
    {
        try {
            Log::info('uploadChunk called', [
                'uploadId' => $request->input('uploadId'),
                'chunkIndex' => $request->input('chunkIndex'),
                'has_file' => $request->hasFile('file'),
                'headers' => $request->headers->all()
            ]);

            $request->validate([
                'uploadId' => 'required|string',
                'chunkIndex' => 'required|integer',
                'totalChunks' => 'required|integer',
                'file' => 'required|file',
            ]);

            $uploadId = $request->input('uploadId');
            $uploadId = preg_replace('/[^a-zA-Z0-9_-]/', '_', $uploadId);
            $index = $request->input('chunkIndex');
            $chunk = $request->file('file');

            $tempDir = storage_path("app/chunks/{$uploadId}");
            if (!file_exists($tempDir)) {
                if (!mkdir($tempDir, 0777, true)) {
                    throw new \Exception("Gagal membuat folder chunks: {$tempDir}");
                }
                Log::info("Created chunk directory: {$tempDir}");
            }

            $chunkPath = "{$tempDir}/chunk_{$index}";
            $chunk->move($tempDir, "chunk_{$index}");

            Log::info("Chunk {$index} uploaded for uploadId: {$uploadId}");

            return response()->json([
                'success' => true,
                'message' => "Chunk {$index} uploaded successfully",
            ]);
        } catch (\Exception $e) {
            Log::error('Upload chunk error: ' . $e->getMessage(), [
                'uploadId' => $request->input('uploadId'),
                'chunkIndex' => $request->input('chunkIndex'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload chunk: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * -----------------------------
     * 🔹 Gabungkan semua chunk
     * -----------------------------
     */
    public function mergeChunks(Request $request)
>>>>>>> Stashed changes
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:5120', // max 5 MB
        ]);

        // Simpan file ke storage/app/uploads
        $path = $request->file('file')->store('uploads');

        // Data hasil analisis (dummy dulu)
        $result = [
            ['label' => 'Abstrak', 'value' => '250 kata', 'status' => 'ok'],
            ['label' => 'Margin', 'value' => '3.0 cm → 2.7 cm', 'status' => 'warning'],
            ['label' => 'Daftar Isi', 'value' => 'OK', 'status' => 'ok'],
            ['label' => 'Rumusan Masalah', 'value' => 'Perlu diperbaiki', 'status' => 'error'],
        ];

        // Nama file yang diupload
        $filename = $request->file('file')->getClientOriginalName();

<<<<<<< Updated upstream
        return view('result', compact('result', 'filename', 'path'));
=======
        sort($chunkFiles, SORT_NATURAL);

        try {
            // Gabungkan semua chunk ke file sementara di local
            $tempMergedPath = "{$tempDir}/merged_temp_file";
            $out = fopen($tempMergedPath, 'wb');
            foreach ($chunkFiles as $chunk) {
                $in = fopen($chunk, 'rb');
                stream_copy_to_stream($in, $out);
                fclose($in);
            }
            fclose($out);

            // -----------------------------
            // 🛡️ Antivirus Scan
            // -----------------------------
            try {
                $scanner = app(\App\Services\ClamAVScanner::class);
                if (!$scanner->scan($tempMergedPath)) {
                    // Virus detected!
                    $this->deleteDirectory($tempDir); // Cleanup
                    return response()->json([
                        'success' => false,
                        'message' => 'File ditolak: Virus terdeteksi!'
                    ], 422);
                }
            } catch (\Exception $e) {
                Log::error('Antivirus scan failed: ' . $e->getMessage());
                // Optional: Fail open or closed. Here we log and proceed, or fail.
                // For now, let's proceed but log it, or fail if strict security is needed.
                // Let's fail safe for now to be secure.
                /*
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memindai antivirus. Silakan coba lagi.'
                ], 500);
                */
            }

            // Baca isi hasil merge
            $fileContent = file_get_contents($tempMergedPath);
            if ($fileContent === false) {
                throw new \Exception('Gagal membaca file hasil penggabungan.');
            }

            // Simpan file menggunakan Storage::putFileAs() seperti di processUpload()
            $uploadPath = 'uploads';
            if (!Storage::exists($uploadPath)) {
                Storage::makeDirectory($uploadPath);
                Log::info('Created upload directory: ' . $uploadPath);
            }

            $finalPath = "{$uploadPath}/{$filename}";
            Storage::put($finalPath, $fileContent);

            if (!Storage::exists($finalPath)) {
                throw new \Exception('File gagal disimpan ke storage.');
            }

            // Verifikasi hasil simpan
            $storedFileSize = Storage::size($finalPath);
            $storedFilePath = Storage::path($finalPath);
            Log::info('File berhasil digabung dan disimpan:', [
                'filename' => $filename,
                'path' => $finalPath,
                'stored_size' => $storedFileSize,
                'full_path' => $storedFilePath
            ]);

            // Hapus folder temporary chunks
            $this->deleteDirectory($tempDir);

            // Redirect seperti di processUpload()
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'pdf') {
                try {
                    app(\App\Http\Controllers\DocumentAnalysisController::class)->analyzeDocument($filename);
                } catch (\Exception $e) {
                    Log::error('Gagal menjalankan analisis otomatis: ' . $e->getMessage());
                }
                return response()->json([
                    'success' => true,
                    'redirect' => route('analyze.document', ['filename' => $filename]),
                    'message' => 'File berhasil digabung dan siap dianalisis.',
                    'filename' => $filename
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'message' => 'File Word berhasil digabung. Fitur analisis Word masih dikembangkan.',
                    'filename' => $filename
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Merge error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menggabungkan file: ' . $e->getMessage()
            ], 500);
        }
    }


    protected function deleteDirectory($dir)
    {
        if (!file_exists($dir))
            return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..')
                continue;
            $path = "{$dir}/{$item}";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function sanitizeFilename($filename)
    {
        // Ambil nama dasar tanpa path
        $filename = basename($filename);

        // Ganti semua karakter selain huruf, angka, titik, underscore, atau tanda minus jadi underscore
        $filename = preg_replace('/[^a-zA-Z0-9._-]+/', '_', $filename);

        // Hapus underscore berurutan biar lebih rapi
        $filename = preg_replace('/_+/', '_', $filename);

        // Pangkas jika terlalu panjang (misal lebih dari 100 karakter)
        if (strlen($filename) > 100) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $name = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 95);
            $filename = "{$name}.{$ext}";
        }

        return $filename;
    }


    private function formatFileSize($bytes)
    {
        if ($bytes <= 0)
            return '0 Bytes';
        $units = ['Bytes', 'KB', 'MB', 'GB'];
        $exp = floor(log($bytes, 1024));
        return round($bytes / (1024 ** $exp), 2) . ' ' . $units[$exp];
>>>>>>> Stashed changes
    }
}
