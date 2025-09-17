<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Hasil Analisis</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-purple-500 to-blue-500">
  <div class="bg-white rounded-2xl shadow-lg p-8 w-[500px]">
    <h1 class="text-2xl font-bold mb-4 text-center">Hasil Analisis Dokumen</h1>

    <p class="mb-4 text-gray-700">File diupload: 
      <span class="font-semibold text-blue-600">{{ $filename }}</span>
    </p>

    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <span>Abstrak</span>
        <span class="text-green-600 font-bold">250 kata</span>
      </div>
      <div class="flex items-center justify-between">
        <span>Margin</span>
        <span class="text-red-600 font-bold">3.0 cm → 2.7 cm</span>
      </div>
      <div class="flex items-center justify-between">
        <span>Daftar Isi</span>
        <span class="text-green-600 font-bold">OK</span>
      </div>
      <div class="flex items-center justify-between">
        <span>Rumusan Masalah</span>
        <span class="text-red-600 font-bold">Perlu diperbaiki</span>
      </div>
    </div>

    <div class="mt-6 text-center">
      <a href="{{ route('upload.form') }}" 
        class="block w-full bg-blue-600 text-white py-2 rounded-lg shadow hover:bg-blue-700 transition text-center">
        Upload File Lainnya
    </a>
    </div>
  </div>
</body>
</html>
