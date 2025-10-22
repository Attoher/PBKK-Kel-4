<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Test endpoint untuk cek apakah server berjalan
Route::get('/ping', function () {
    return response()->json([
        'status' => 'ok',
        'message' => 'Server is running',
        'timestamp' => now()->toDateTimeString()
    ]);
});

// Test endpoint untuk cek POST request
Route::post('/test-post', function (Request $request) {
    return response()->json([
        'status' => 'ok',
        'message' => 'POST request received',
        'data' => $request->all()
    ]);
});
