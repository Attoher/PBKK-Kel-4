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
Route::post('/upload', [UploadController::class, 'processUpload'])->name('upload.process')->middleware(\App\Http\Middleware\CheckPythonEnv::class);
Route::get('/upload/test', [UploadController::class, 'testConnection'])->name('upload.test');

// Routes untuk analisis dokumen
Route::get('/analyze/{filename}', [DocumentAnalysisController::class, 'analyzeDocument'])->name('analyze.document')->middleware(\App\Http\Middleware\CheckPythonEnv::class);
Route::get('/results/{filename}', [DocumentAnalysisController::class, 'showResults'])->name('results');

// Route untuk download hasil
Route::get('/download/{filename}', [DocumentAnalysisController::class, 'downloadResults'])->name('download.results');

// Routes untuk history
Route::get('/history', [DocumentAnalysisController::class, 'showHistory'])->name('history');
Route::delete('/history/clear', [DocumentAnalysisController::class, 'clearHistory'])->name('history.clear');
Route::delete('/history/clear-old', [DocumentAnalysisController::class, 'clearOldHistory'])->name('history.clear.old');
Route::delete('/history/delete/{filename}', [DocumentAnalysisController::class, 'deleteHistoryItem'])->name('history.delete');

Route::post('/upload/chunk', [UploadController::class, 'uploadChunk'])->name('upload.chunk');
Route::post('/upload/merge', [UploadController::class, 'mergeChunks'])->name('upload.merge')->middleware(\App\Http\Middleware\CheckPythonEnv::class);

// DEBUG ROUTES - untuk cek logs dan environment di Railway
Route::get('/debug/logs', function () {
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
});

Route::get('/debug/env', function () {
    return response()->json([
        'APP_ENV' => env('APP_ENV'),
        'APP_DEBUG' => env('APP_DEBUG'),
        'SENOPATI_BASE_URL' => env('SENOPATI_BASE_URL', 'NOT SET'),
        'SENOPATI_MODEL' => env('SENOPATI_MODEL', 'NOT SET'),
        'OPENROUTER_BASE_URL' => env('OPENROUTER_BASE_URL', 'NOT SET'),
        'PHP_VERSION' => PHP_VERSION,
        'PYTHON_CHECK' => shell_exec('python --version 2>&1') ?: 'Python not found',
    ], JSON_PRETTY_PRINT);
});