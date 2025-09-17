<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/upload', [UploadController::class, 'index'])->name('upload.form');
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');
