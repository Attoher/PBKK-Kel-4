<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocumentAnalysisController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\AuthController;

// Halaman utama - Homepage
Route::get('/', function () {
    return view('homepage');
})->name('homepage');

// Routes untuk autentikasi
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes untuk upload
Route::get('/upload', [UploadController::class, 'showUploadForm'])->name('upload.form');
Route::post('/upload', [UploadController::class, 'processUpload'])->name('upload.process');
Route::get('/upload/test', [UploadController::class, 'testConnection'])->name('upload.test');

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