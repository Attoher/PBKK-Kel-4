<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadController extends Controller
{
    public function showUploadForm()
    {
        $files = Storage::files('uploads');
        $recentUploads = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
                $filename = basename($file);
                $recentUploads[] = [
                    'name' => $filename,
                    'uploaded_at' => date('Y-m-d H:i:s', Storage::lastModified($file)),
                    'size' => $this->formatFileSize(Storage::size($file)),
                    'url' => route('analyze.document', ['filename' => $filename])
                ];
            }
        }

        usort($recentUploads, fn($a, $b) => strtotime($b['uploaded_at']) - strtotime($a['uploaded_at']));
        $recentUploads = array_slice($recentUploads, 0, 5);

        return view('upload', compact('recentUploads'));
    }

    /**
     * -----------------------------
     * 🔹 Upload per chunk
     * -----------------------------
     */
    public function uploadChunk(Request $request)
    {
        $request->validate([
            'uploadId' => 'required|string',
            'chunkIndex' => 'required|integer',
            'totalChunks' => 'required|integer',
            'file' => 'required|file',
        ]);

        $uploadId = $request->input('uploadId');
        $index = $request->input('chunkIndex');
        $chunk = $request->file('file');

        $tempDir = storage_path("app/chunks/{$uploadId}");
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        $chunkPath = "{$tempDir}/chunk_{$index}";
        $chunk->move($tempDir, "chunk_{$index}");

        Log::info("Chunk {$index} uploaded for uploadId: {$uploadId}");

        return response()->json([
            'success' => true,
            'message' => "Chunk {$index} uploaded successfully",
        ]);
    }

    /**
     * -----------------------------
     * 🔹 Gabungkan semua chunk
     * -----------------------------
     */
   public function mergeChunks(Request $request)
    {
        $request->validate([
            'uploadId' => 'required|string',
            'fileName' => 'required|string',
        ]);

        $uploadId = $request->input('uploadId');
        $originalName = $request->input('fileName');
        $filename = time() . '_' . $this->sanitizeFilename($originalName);

        $tempDir = storage_path("app/chunks/{$uploadId}");
        $chunkFiles = glob("{$tempDir}/chunk_*");

        if (empty($chunkFiles)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada potongan file ditemukan.'
            ], 400);
        }

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
                            \Log::error('Gagal menjalankan analisis otomatis: ' . $e->getMessage());
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
        if (!file_exists($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = "{$dir}/{$item}";
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function sanitizeFilename($filename)
    {
        $filename = basename($filename);
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
        if (strlen($filename) > 100) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $name = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 95);
            $filename = "{$name}.{$ext}";
        }
        return $filename;
    }

    private function formatFileSize($bytes)
    {
        if ($bytes <= 0) return '0 Bytes';
        $units = ['Bytes', 'KB', 'MB', 'GB'];
        $exp = floor(log($bytes, 1024));
        return round($bytes / (1024 ** $exp), 2) . ' ' . $units[$exp];
    }
}
