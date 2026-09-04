@extends('layouts.app')

@section('title', 'Daftar Buku - Sistem Manajemen Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Daftar Koleksi Buku</h1>
            <p class="subtitle">Kelola seluruh katalog dan stok buku perpustakaan secara terpadu.</p>
        </div>
        <div class="page-header-actions">
            <a class="button button-primary" href="{{ route('books.create') }}">+ Tambah Buku</a>
            <a class="button button-secondary" href="{{ route('categories.index') }}">🏷️ Kelola Kategori</a>
        </div>
    </div>

    <!-- Dashboard Statistik Singkat Buku -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">📚</div>
            <div class="stat-info">
                <span class="stat-label">Total Judul Buku</span>
                <strong class="stat-value">{{ $totalBooks }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">📦</div>
            <div class="stat-info">
                <span class="stat-label">Total Stok Tersedia</span>
                <strong class="stat-value">{{ $totalStock ?? 0 }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #d97706;">🏷️</div>
            <div class="stat-info">
                <span class="stat-label">Total Kategori</span>
                <strong class="stat-value">{{ $totalCategories }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">✨</div>
            <div class="stat-info">
                <span class="stat-label">Buku Terbaru</span>
                <strong class="stat-value stat-title" title="{{ $latestBook ? $latestBook->title : 'Belum Ada Buku' }}">
                    {{ $latestBook ? $latestBook->title : '-' }}
                </strong>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="filter-card">
        <form action="{{ route('books.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search">
                <label for="search">Cari Buku</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Cari berdasarkan judul atau penulis..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-group filter-select">
                <label for="category">Kategori</label>
                <select id="category" name="category">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari & Filter</button>
                @if(request('search') || request('category'))
                    <a href="{{ route('books.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Buku & Empty States -->
    <div class="table-wrap">
        @if ($books->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📖</div>
                @if (request('search') || request('category'))
                    <h3>Buku yang kamu cari tidak ditemukan.</h3>
                    <p class="muted">Coba gunakan kata kunci pencarian lain atau ubah filter kategori.</p>
                    <a href="{{ route('books.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset Filter</a>
                @else
                    <h3>Belum ada buku yang tersedia.</h3>
                    <p class="muted">Mulai dengan menambahkan buku baru ke sistem Anda.</p>
                    <a href="{{ route('books.create') }}" class="button button-primary" style="margin-top: 12px;">+ Tambah Buku Pertama</a>
                @endif
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Judul Buku</th>
                        <th>Penulis</th>
                        <th>Tahun</th>
                        <th>Kategori</th>
                        <th>Stok</th>
                        <th style="width: 170px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($books as $index => $book)
                        <tr>
                            <td>{{ $books->firstItem() + $loop->index }}</td>
                            <td>
                                <strong>{{ $book->title }}</strong>
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>{{ $book->published_year }}</td>
                            <td>
                                <span class="badge badge-secondary">{{ $book->category_name }}</span>
                            </td>
                            <td>
                                @if($book->stock > 0)
                                    <span class="badge badge-success">{{ $book->stock }} eks</span>
                                @else
                                    <span class="badge badge-danger">Habis (0)</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    <a class="button button-info" href="{{ route('books.show', $book) }}">Detail</a>
                                    <a class="button button-edit" href="{{ route('books.edit', $book) }}">Edit</a>
                                    <form action="{{ route('books.destroy', $book) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button button-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

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
