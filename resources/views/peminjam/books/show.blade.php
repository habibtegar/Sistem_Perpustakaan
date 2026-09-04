@extends('layouts.app')

@section('title', 'Detail Buku - ' . $book->title)

@section('content')
    <div class="page-header">
        <div>
            <h1>Detail Buku</h1>
            <p class="subtitle">Informasi lengkap dan sinopsis buku koleksi perpustakaan.</p>
        </div>
        <a class="button button-secondary" href="{{ route('peminjam.books.index') }}">&larr; Kembali ke Katalog</a>
    </div>

    <div class="detail-card">
        <div class="detail-card-header">
            <div style="display: flex; gap: 18px; align-items: flex-start;">
                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" style="width: 80px; height: 115px; object-fit: cover; border-radius: 8px; border: 1px solid var(--line); box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
                <div>
                    <span class="badge badge-secondary">{{ $book->category_name }}</span>
                    <h2 class="detail-title">{{ $book->title }}</h2>
                    <p class="muted" style="margin: 4px 0 0;">Penulis: <strong>{{ $book->author }}</strong></p>
                </div>
            </div>
            <div>
                @if($book->stock > 0)
                    <span class="badge badge-success" style="font-size: 0.9rem; padding: 6px 12px;">
                        ✓ Tersedia ({{ $book->stock }} eksemplar)
                    </span>
                @else
                    <span class="badge badge-danger" style="font-size: 0.9rem; padding: 6px 12px;">
                        ✗ Stok Habis (Tidak Tersedia)
                    </span>
                @endif
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Penulis</span>
                <span class="detail-value">{{ $book->author }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Tahun Terbit</span>
                <span class="detail-value">{{ $book->published_year }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Kategori</span>
                <span class="detail-value">{{ $book->category_name }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Sisa Stok Fisik</span>
                <span class="detail-value">{{ $book->stock }} buku</span>
            </div>
        </div>

        <div class="detail-description-box">
            <h3>Sinopsis & Deskripsi Buku</h3>
            <div class="description">
                {{ $book->description ?: 'Belum ada ringkasan atau sinopsis untuk buku ini.' }}
            </div>
        </div>

        <div class="form-actions" style="margin-top: 28px;">
            @if($book->stock > 0)
                <form action="{{ route('peminjam.borrow.store', $book) }}" method="POST" onsubmit="return confirm('Ajukan permohonan peminjaman buku {{ addslashes($book->title) }}?')">
                    @csrf
                    <button type="submit" class="button button-primary" style="padding: 12px 24px; font-size: 1rem;">
                        📤 Ajukan Peminjaman Buku Ini
                    </button>
                </form>
            @else
                <button class="button button-secondary" disabled style="cursor: not-allowed; opacity: 0.7;">
                    Buku Sedang Tidak Tersedia
                </button>
            @endif
            <a href="{{ route('peminjam.books.index') }}" class="button button-secondary">Kembali ke Katalog</a>
        </div>
    </div>
@endsection
