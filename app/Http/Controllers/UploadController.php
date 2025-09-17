<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
                $recentUploads[] = [
                    'name' => basename($file),
                    'uploaded_at' => date('Y-m-d H:i:s', Storage::lastModified($file)),
                    'size' => $this->formatFileSize(Storage::size($file))
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
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240'
        ]);
        
        // Simpan file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            
            // Generate unique filename
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $originalName);
            
            try {
                // Simpan file menggunakan storeAs dengan path yang jelas
                $path = $file->storeAs('uploads', $filename);
                
                // Log informasi penyimpanan
                Log::info('File disimpan di: ' . $path);
                Log::info('Full path: ' . storage_path('app/' . $path));
                
                // Pastikan file benar-benar ada
                if (Storage::exists('uploads/' . $filename)) {
                    Log::info('File berhasil disimpan dan diverifikasi');
                    
                    // Redirect ke halaman analisis
                    return redirect()->route('analyze.document', ['filename' => $filename])
                        ->with('success', 'File berhasil diupload. Sedang menganalisis...');
                } else {
                    Log::error('File tidak ditemukan setelah disimpan: uploads/' . $filename);
                    return back()->with('error', 'Gagal menyimpan file. Silakan coba lagi.');
                }
                
            } catch (\Exception $e) {
                Log::error('Error menyimpan file: ' . $e->getMessage());
                return back()->with('error', 'Gagal mengupload file: ' . $e->getMessage());
            }
        }
        
        return back()->with('error', 'Tidak ada file yang diupload.');
    }
    
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
