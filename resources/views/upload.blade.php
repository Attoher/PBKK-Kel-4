@extends('layouts.app')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h3 class="mb-3">Upload Buku TA (PDF)</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="ta_file" class="form-label">File PDF</label>
                <input type="file" class="form-control" name="ta_file" id="ta_file" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload & Analisis</button>
        </form>
    </div>
</div>
@endsection
