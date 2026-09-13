@extends('layouts.app')

@section('title', 'Tambah Kategori - Sistem Manajemen Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Tambah Kategori</h1>
            <p class="subtitle">Buat kategori baru untuk mengelompokkan koleksi buku perpustakaan.</p>
        </div>
        <a class="button button-secondary" href="{{ route('categories.index') }}">&larr; Kembali</a>
    </div>

    <div class="form-card">
        @if ($errors->any())
            <div class="alert alert-error">
                Periksa kembali form di bawah ini.
            </div>
        @endif

        <form action="{{ route('categories.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name">Nama Kategori <span style="color: var(--danger);">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Sains Populer" required autofocus>
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi <span class="muted">(opsional)</span></label>
                <textarea id="description" name="description" placeholder="Penjelasan singkat mengenai kategori ini...">{{ old('description') }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Simpan Kategori</button>
                <a href="{{ route('categories.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
