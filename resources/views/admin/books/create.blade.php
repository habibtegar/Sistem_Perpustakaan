@extends('layouts.app')

@section('title', 'Tambah Buku Baru - Administrator')

@section('content')
    <div class="page-header">
        <div>
            <h1>Tambah Buku Baru</h1>
            <p class="subtitle">Lengkapi formulir untuk menambahkan buku baru ke katalog perpustakaan.</p>
        </div>
        <a class="button button-secondary" href="{{ route('admin.books.index') }}">&larr; Kembali</a>
    </div>

    <form class="form-card" action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.books.partials.form')
        <div class="form-actions">
            <button class="button button-primary" type="submit">Simpan Buku</button>
            <a class="button button-secondary" href="{{ route('admin.books.index') }}">Batal</a>
        </div>
    </form>
@endsection
