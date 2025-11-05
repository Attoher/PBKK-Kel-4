<?php

use Illuminate\Support\Facades\Route;

/**
 * DEBUG ROUTES - HANYA UNTUK DEVELOPMENT
 * Uncomment di routes/web.php untuk enable
 */

// Lihat 100 baris terakhir dari laravel.log
Route::get('/debug/logs', function () {
    if (env('APP_ENV') !== 'production') {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return response()->json(['error' => 'Log file not found']);
        }
        
        $lines = file($logFile);
        $lastLines = array_slice($lines, -100);
        
        return response('<pre>' . implode('', $lastLines) . '</pre>')
            ->header('Content-Type', 'text/html');
    }
    
    return response()->json(['error' => 'Only available in development mode']);
});

// Lihat environment variables (AMAN - tidak show sensitive data)
Route::get('/debug/env', function () {
    return response()->json([
        'APP_ENV' => env('APP_ENV'),
        'APP_DEBUG' => env('APP_DEBUG'),
        'SENOPATI_BASE_URL' => env('SENOPATI_BASE_URL') ? 'SET ✓' : 'NOT SET ✗',
        'SENOPATI_MODEL' => env('SENOPATI_MODEL') ? 'SET ✓' : 'NOT SET ✗',
        'PHP_VERSION' => PHP_VERSION,
        'PYTHON_AVAILABLE' => shell_exec('python --version 2>&1') ? 'YES ✓' : 'NO ✗',
    ]);
});
