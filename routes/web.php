<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/upload', function () {
    return view('upload');
})->name('upload.form');

Route::post('/upload', function (Request $request) {
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        // Nanti logika AI analisis format bisa ditaruh di sini

        return view('result', ['filename' => $filename]);
    }
    return back()->with('error', 'Tidak ada file diupload');
})->name('upload.process');
