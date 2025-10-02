<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class UploadController extends Controller
{
    public function showUploadForm()
    {
        // Get recent uploads
        $files = Storage::files('uploads');
        $recentUploads = [];
        
        foreach ($files as $file) {
            // Only show PDF files in recent uploads
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
        
        // Sort by date descending and take only 5 most recent
        usort($recentUploads, function($a, $b) {
            return strtotime($b['uploaded_at']) - strtotime($a['uploaded_at']);
        });
        
        $recentUploads = array_slice($recentUploads, 0, 5);
        
        return view('upload', compact('recentUploads'));
    }
    
    public function processUpload(Request $request)
    {
        // Validasi file
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240' // 10MB max
        ], [
            'file.required' => 'Silakan pilih file untuk diupload.',
            'file.file' => 'File yang diupload tidak valid.',
            'file.mimes' => 'Hanya file PDF, DOC, dan DOCX yang didukung.',
            'file.max' => 'Ukuran file maksimal 10MB.'
        ]);
        
        try {
            // Simpan file
            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $originalName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                
                Log::info('File upload attempt:', [
                    'original_name' => $originalName,
                    'size' => $fileSize,
                    'mime_type' => $mimeType
                ]);
                
                // Validasi tambahan untuk file size
                if ($fileSize > 10 * 1024 * 1024) { // 10MB in bytes
                    return back()->with('error', 'Ukuran file terlalu besar. Maksimal 10MB.');
                }
                
                // Generate safe filename
                $filename = time() . '_' . $this->sanitizeFilename($originalName);
                
                // **PERBAIKAN: Gunakan Storage untuk semua operasi file**
                $uploadPath = 'uploads';
                
                // Pastikan direktori uploads exists
                if (!Storage::exists($uploadPath)) {
                    Storage::makeDirectory($uploadPath);
                    Log::info('Created upload directory: ' . $uploadPath);
                }
                
                // **PERBAIKAN: Simpan file menggunakan putFileAs untuk handling yang lebih baik**
                $path = Storage::putFileAs($uploadPath, $file, $filename);
                
                // Verifikasi file tersimpan
                if (!Storage::exists($path)) {
                    throw new \Exception('File gagal disimpan ke storage.');
                }
                
                $storedFileSize = Storage::size($path);
                $storedFilePath = Storage::path($path);
                $fileExists = Storage::exists($path);
                
                Log::info('File successfully stored:', [
                    'filename' => $filename,
                    'path' => $path,
                    'stored_size' => $storedFileSize,
                    'full_path' => $storedFilePath,
                    'file_exists' => $fileExists ? 'YES' : 'NO',
                    'is_readable' => Storage::getVisibility($path)
                ]);
                
                // **PERBAIKAN: Gunakan Storage untuk verifikasi, bukan file_exists()**
                if (!Storage::exists($path)) {
                    throw new \Exception('File tidak dapat diakses setelah disimpan.');
                }
                
                // Check if file is readable menggunakan Storage
                try {
                    $testContent = Storage::get($path);
                    if ($testContent === false) {
                        throw new \Exception('File tidak dapat dibaca.');
                    }
                    Log::info('File readability test: PASSED');
                } catch (\Exception $e) {
                    throw new \Exception('File tidak dapat dibaca: ' . $e->getMessage());
                }
                
                // **PERBAIKAN: Konsisten dalam penanganan file type**
                if (pathinfo($filename, PATHINFO_EXTENSION) === 'pdf') {
                    Log::info('Redirecting to analysis for PDF file:', ['filename' => $filename]);
                    
                    return redirect()
                        ->route('analyze.document', ['filename' => $filename])
                        ->with('success', 'File berhasil diupload! Sedang menganalisis dokumen...');
                } else {
                    // Untuk file Word, simpan dan beri pesan
                    Log::info('Word file uploaded but analysis not available:', ['filename' => $filename]);
                    return back()->with('info', 'File Word berhasil diupload. Fitur analisis untuk Word sedang dalam pengembangan.');
                }
                
            } else {
                Log::warning('No file in upload request');
                return back()->with('error', 'Tidak ada file yang diupload.');
            }
            
        } catch (\Exception $e) {
            Log::error('Upload processing error: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            
            $errorMessage = 'Gagal mengupload file: ' . $e->getMessage();
            
            // User-friendly error messages
            if (str_contains($e->getMessage(), 'disk full')) {
                $errorMessage = 'Gagal mengupload file: Storage disk penuh.';
            } elseif (str_contains($e->getMessage(), 'permission denied')) {
                $errorMessage = 'Gagal mengupload file: Permission denied. Silakan cek permission folder storage.';
            }
            
            return back()->with('error', $errorMessage);
        }
    }
    
    /**
     * Sanitize filename untuk keamanan
     */
    private function sanitizeFilename($filename)
    {
        // Remove path traversal characters
        $filename = basename($filename);
        
        // Replace spaces and special characters
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);
        
        // Limit length
        if (strlen($filename) > 100) {
            $extension = pathinfo($filename, PATHINFO_EXTENSION);
            $name = pathinfo($filename, PATHINFO_FILENAME);
            $name = substr($name, 0, 95); // Reserve 5 chars for extension and dot
            $filename = $name . '.' . $extension;
        }
        
        return $filename;
    }
    
    /**
     * Format file size untuk tampilan yang user-friendly
     */
    private function formatFileSize($bytes)
    {
        if ($bytes == 0) {
            return '0 Bytes';
        }
        
        $units = ['Bytes', 'KB', 'MB', 'GB'];
        $base = 1024;
        $exponent = (int) floor(log($bytes, $base));
        
        return round($bytes / pow($base, $exponent), 2) . ' ' . $units[$exponent];
    }
}