@extends('layouts.app')

@section('title', 'Katalog Buku - Sistem Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Katalog & Koleksi Buku</h1>
            <p class="subtitle">Temukan buku yang Anda minati dan ajukan peminjaman secara mudah.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('peminjam.my-borrowings') }}" class="button button-secondary">📖 Peminjaman Saya</a>
        </div>
    </div>

    <!-- Filter & Pencarian Buku -->
    <div class="filter-card">
        <form action="{{ route('peminjam.books.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search">
                <label for="search">Cari Judul / Penulis</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Masukkan judul buku atau nama pengarang..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-group filter-select">
                <label for="category_id">Kategori</label>
                <select id="category_id" name="category_id">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group filter-select">
                <label for="availability">Ketersediaan</label>
                <select id="availability" name="availability">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Tersedia Saja (> 0)</option>
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari Buku</button>
                @if(request('search') || request('category_id') || request('availability'))
                    <a href="{{ route('peminjam.books.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Grid Kartu Buku -->
    @if ($books->isEmpty())
        <div class="table-wrap">
            <div class="empty-state">
                <div class="empty-icon">📖</div>
                <h3>Buku yang Anda cari tidak ditemukan.</h3>
                <p class="muted">Coba ubah kata kunci pencarian atau ganti filter kategori.</p>
                <a href="{{ route('peminjam.books.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset Pencarian</a>
            </div>
        </div>
    @else
        <div class="catalog-grid">
            @foreach ($books as $book)
                <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    <div>
                        <div style="height: 180px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            <div style="position: absolute; top: 10px; right: 10px;">
                                <span class="badge badge-secondary" style="backdrop-filter: blur(4px); background: rgba(255,255,255,0.9); font-weight: 700;">
                                    {{ $book->category_name }}
                                </span>
                            </div>
                        </div>

                        <div style="padding: 16px;">
                            <h3 style="font-size: 1.05rem; margin: 0 0 6px; color: #0f172a; line-height: 1.35;">
                                <a href="{{ route('peminjam.books.show', $book) }}" style="color: inherit;">
                                    {{ Str::limit($book->title, 40) }}
                                </a>
                            </h3>
                            <p class="muted" style="font-size: 0.85rem; margin: 0 0 10px;">
                                Penulis: <strong>{{ $book->author }}</strong> ({{ $book->published_year }})
                            </p>
                            @if($book->description)
                                <p style="font-size: 0.82rem; color: #64748b; margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $book->description }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div style="padding: 14px 16px; border-top: 1px solid var(--line); background: #f8fafc; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <div>
                            @if($book->stock > 0)
                                <span class="badge badge-success" style="font-size: 0.78rem;">Tersedia ({{ $book->stock }})</span>
                            @else
                                <span class="badge badge-danger" style="font-size: 0.78rem;">Tidak Tersedia</span>
                            @endif
                        </div>

                        <div>
                            @if($book->stock > 0)
                                <form action="{{ route('peminjam.borrow.store', $book) }}" method="POST" onsubmit="return confirm('Ajukan peminjaman buku {{ addslashes($book->title) }}?')">
                                    @csrf
                                    <button type="submit" class="button button-primary button-sm">
                                        Ajukan Pinjam &rarr;
                                    </button>
                                </form>
                            @else
                                <button class="button button-secondary button-sm" disabled style="cursor: not-allowed; opacity: 0.7;">
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Pagination -->
    @if ($books->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-custom">
                @if ($books->onFirstPage())
                    <span class="page-button disabled">&laquo; Sebelumnya</span>
                @else
                    <a href="{{ $books->previousPageUrl() }}" class="page-button">&laquo; Sebelumnya</a>
                @endif

                <span class="page-info">Halaman <strong>{{ $books->currentPage() }}</strong> dari <strong>{{ $books->lastPage() }}</strong></span>

                @if ($books->hasMorePages())
                    <a href="{{ $books->nextPageUrl() }}" class="page-button">Selanjutnya &raquo;</a>
                @else
                    <span class="page-button disabled">Selanjutnya &raquo;</span>
                @endif
            </div>
        </div>
    @endif
@endsection
