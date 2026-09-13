@extends('layouts.app')

@section('title', 'Edit Kategori - Administrator')

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Kategori</h1>
            <p class="subtitle">Perbarui informasi kategori: <strong>{{ $category->name }}</strong></p>
        </div>
        <a class="button button-secondary" href="{{ route('admin.categories.index') }}">&larr; Kembali</a>
    </div>

    <div class="form-card">
        @if ($errors->any())
            <div class="alert alert-error">
                Periksa kembali form di bawah ini.
            </div>
        @endif

        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Nama Kategori <span style="color: var(--danger);">*</span></label>
                <input id="name" name="name" type="text" value="{{ old('name', $category->name) }}" required>
                @error('name') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="description">Deskripsi <span class="muted">(opsional)</span></label>
                <textarea id="description" name="description">{{ old('description', $category->description) }}</textarea>
                @error('description') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Perbarui Kategori</button>
                <a href="{{ route('admin.categories.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
