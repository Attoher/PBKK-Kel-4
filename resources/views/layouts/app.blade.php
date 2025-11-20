<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="api-base" content="{{ url('/') }}">
    <title>Deteksi Kelengkapan TA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Deteksi TA</a>
            <a class="nav-link text-white" href="{{ route('upload.form') }}">Upload</a>
        </div>
    </nav>
    <div class="container">
        @yield('content')
    </div>
    @stack('scripts')
</body>
</html>
