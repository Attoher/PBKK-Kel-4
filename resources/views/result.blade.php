@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h3 class="mb-3">Hasil Analisis</h3>

        <p><strong>File disimpan di:</strong> {{ $filePath }}</p>

        <h5>Kelengkapan:</h5>
        <ul>
            <li>Cover: {{ $analysis['cover'] ? '✅ Ada' : '❌ Tidak ada' }}</li>
            <li>Abstrak: {{ $analysis['abstrak'] ? '✅ Ada' : '❌ Tidak ada' }}</li>
            <li>Daftar Isi: {{ $analysis['daftar_isi'] ? '✅ Ada' : '❌ Tidak ada' }}</li>
            <li>Bab:
                <ul>
                    @foreach ($analysis['bab'] as $bab => $status)
                        <li>{{ $bab }}: {{ $status ? '✅ Ada' : '❌ Tidak ada' }}</li>
                    @endforeach
                </ul>
            </li>
            <li>Daftar Pustaka: {{ $analysis['daftar_pustaka'] ? '✅ Ada' : '❌ Tidak ada' }}</li>
        </ul>

        <h4 class="mt-3">Skor Kelengkapan: {{ $analysis['skor'] }} / 100</h4>

        <a href="{{ route('upload.form') }}" class="btn btn-secondary mt-3">Upload Ulang</a>
    </div>
</div>
@endsection
