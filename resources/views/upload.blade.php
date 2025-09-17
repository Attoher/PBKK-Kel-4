<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cek Format TA</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex items-center justify-center min-h-screen bg-gradient-to-br from-purple-500 to-blue-500">
  <div class="bg-white rounded-2xl shadow-lg p-8 w-[500px] text-center">
    <h1 class="text-2xl font-bold mb-2">Cek Format Tugas Akhir</h1>
    <p class="text-gray-600 mb-6">Unggah file tugas akhir kamu untuk memeriksa formatnya</p>

    <form action="{{ route('upload.process') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
      @csrf
      <label for="file" 
             class="flex flex-col items-center justify-center border-2 border-dashed border-gray-400 rounded-lg p-6 cursor-pointer hover:border-blue-500">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-500 mb-2" viewBox="0 0 20 20" fill="currentColor">
          <path d="M4 3a2 2 0 00-2 2v2a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4z" />
          <path fill-rule="evenodd" d="M3 9a1 1 0 011-1h12a1 1 0 011 1v6a2 2 0 01-2 2H5a2 2 0 01-2-2V9zm3 2a1 1 0 100 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
        </svg>
        <p class="text-gray-600">Tarik file PDF atau Word ke sini</p>
        <p class="text-xs text-gray-400">Dukungan: PDF & Word</p>
        <input id="file" name="file" type="file" class="hidden" required>
      </label>

      <!-- Preview filename -->
      <div id="file-preview" class="text-sm text-gray-700 mt-2 hidden"></div>

      <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
        Analisis Dokumen
      </button>
    </form>
  </div>

  <script>
    const input = document.getElementById('file');
    const preview = document.getElementById('file-preview');

    input.addEventListener('change', function () {
      if (this.files && this.files.length > 0) {
        preview.textContent = "File dipilih: " + this.files[0].name;
        preview.classList.remove('hidden');
      } else {
        preview.textContent = "";
        preview.classList.add('hidden');
      }
    });
  </script>
</body>
</html>
