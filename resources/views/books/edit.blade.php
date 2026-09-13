@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Buku</h1>
            <p class="subtitle">Perbarui informasi buku yang dipilih.</p>
        </div>
    </div>

    <form class="form-card" action="{{ route('books.update', $book) }}" method="POST">
        @csrf
        @method('PUT')
        @include('books.partials.form', ['book' => $book])
        <div class="form-actions">
            <button class="button button-primary" type="submit">Simpan Perubahan</button>
            <a class="button button-secondary" href="{{ route('books.show', $book) }}">Batal</a>
        </div>
    </form>
@endsection
