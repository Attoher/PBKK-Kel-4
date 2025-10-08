<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentAnalysisController;
use App\Http\Controllers\UploadController;

// Halaman utama
Route::get('/', function () {
    return redirect()->route('upload.form');
});

// Routes untuk upload
Route::get('/upload', [UploadController::class, 'showUploadForm'])->name('upload.form');
Route::post('/upload', [UploadController::class, 'processUpload'])->name('upload.process');

// Routes untuk analisis dokumen
Route::get('/analyze/{filename}', [DocumentAnalysisController::class, 'analyzeDocument'])->name('analyze.document');
Route::get('/results/{filename}', [DocumentAnalysisController::class, 'showResults'])->name('results');



// Route untuk download hasil
Route::get('/download/{filename}', [DocumentAnalysisController::class, 'downloadResults'])->name('download.results');

// Routes untuk history
Route::get('/history', [DocumentAnalysisController::class, 'showHistory'])->name('history');
Route::delete('/history/clear', [DocumentAnalysisController::class, 'clearHistory'])->name('history.clear');
Route::delete('/history/clear-old', [DocumentAnalysisController::class, 'clearOldHistory'])->name('history.clear.old');
Route::delete('/history/delete/{filename}', [DocumentAnalysisController::class, 'deleteHistoryItem'])->name('history.delete');

Route::post('/upload/chunk', [UploadController::class, 'uploadChunk'])->name('upload.chunk');
Route::post('/upload/merge', [UploadController::class, 'mergeChunks'])->name('upload.merge');
