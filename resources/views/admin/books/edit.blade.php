@extends('layouts.app')

@section('title', 'Edit Buku - ' . $book->title)

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Informasi Buku</h1>
            <p class="subtitle">Perbarui data atau stok buku: <strong>{{ $book->title }}</strong></p>
        </div>
        <a class="button button-secondary" href="{{ route('admin.books.index') }}">&larr; Kembali</a>
    </div>

    <form class="form-card" action="{{ route('admin.books.update', $book) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.books.partials.form', ['book' => $book])
        <div class="form-actions">
            <button class="button button-primary" type="submit">Simpan Perubahan</button>
            <a class="button button-secondary" href="{{ route('admin.books.show', $book) }}">Batal</a>
        </div>
    </form>
@endsection
