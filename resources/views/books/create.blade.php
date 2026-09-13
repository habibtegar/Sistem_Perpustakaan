@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
    <div class="page-header">
        <div>
            <h1>Tambah Buku</h1>
            <p class="subtitle">Masukkan informasi buku baru.</p>
        </div>
    </div>

    <form class="form-card" action="{{ route('books.store') }}" method="POST">
        @csrf
        @include('books.partials.form')
        <div class="form-actions">
            <button class="button button-primary" type="submit">Simpan Buku</button>
            <a class="button button-secondary" href="{{ route('books.index') }}">Batal</a>
        </div>
    </form>
@endsection
