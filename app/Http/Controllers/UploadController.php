<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload'); // menuju ke resources/views/upload.blade.php
    }

    public function store(Request $request)
    {
        $request->validate([
            'ta_file' => 'required|mimes:pdf|max:20480',
        ]);

        $path = $request->file('ta_file')->store('ta_files');

        // Dummy hasil analisis (nanti bisa diganti pakai AI/PDF parser)
        $analysis = [
            'cover' => true,
            'abstrak' => true,
            'daftar_isi' => false,
            'bab' => [
                'BAB I' => true,
                'BAB II' => true,
                'BAB III' => false,
            ],
            'daftar_pustaka' => true,
            'skor' => 75,
        ];

        return view('result', [
            'filePath' => $path,
            'analysis' => $analysis,
        ]);
    }
}
