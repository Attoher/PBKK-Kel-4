<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf,doc,docx|max:5120', // max 5 MB
        ]);

        // Simpan file ke storage/app/uploads
        $path = $request->file('file')->store('uploads');

        // Data hasil analisis (dummy dulu)
        $result = [
            ['label' => 'Abstrak', 'value' => '250 kata', 'status' => 'ok'],
            ['label' => 'Margin', 'value' => '3.0 cm → 2.7 cm', 'status' => 'warning'],
            ['label' => 'Daftar Isi', 'value' => 'OK', 'status' => 'ok'],
            ['label' => 'Rumusan Masalah', 'value' => 'Perlu diperbaiki', 'status' => 'error'],
        ];

        // Nama file yang diupload
        $filename = $request->file('file')->getClientOriginalName();

        return view('result', compact('result', 'filename', 'path'));
    }
}
