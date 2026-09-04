@extends('layouts.app')

@section('title', 'Kelola Buku - Administrator')

@section('content')
    <div class="page-header">
        <div>
            <h1>Kelola Koleksi Buku</h1>
            <p class="subtitle">Tambah, ubah, atur stok eksemplar, dan kelola seluruh buku perpustakaan.</p>
        </div>
        <div class="page-header-actions">
            <a class="button button-primary" href="{{ route('admin.books.create') }}">+ Tambah Buku</a>
            <a class="button button-secondary" href="{{ route('admin.categories.index') }}">🏷️ Kategori</a>
        </div>
    </div>

    <!-- Statistik Singkat Buku -->
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
                <strong class="stat-value">{{ $totalStock }}</strong>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="filter-card">
        <form action="{{ route('admin.books.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search">
                <label for="search">Cari Buku</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Cari berdasarkan judul atau nama penulis..."
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
                <label for="stock_status">Filter Stok</label>
                <select id="stock_status" name="stock_status">
                    <option value="">Semua Stok</option>
                    <option value="available" {{ request('stock_status') === 'available' ? 'selected' : '' }}>Tersedia (> 0)</option>
                    <option value="empty" {{ request('stock_status') === 'empty' ? 'selected' : '' }}>Habis (0)</option>
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari & Filter</button>
                @if(request('search') || request('category_id') || request('stock_status'))
                    <a href="{{ route('admin.books.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Buku -->
    <div class="table-wrap">
        @if ($books->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📖</div>
                @if (request('search') || request('category_id') || request('stock_status'))
                    <h3>Buku tidak ditemukan.</h3>
                    <p class="muted">Coba ubah kata kunci atau pengaturan filter pencarian Anda.</p>
                    <a href="{{ route('admin.books.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset Filter</a>
                @else
                    <h3>Belum ada buku dalam koleksi.</h3>
                    <p class="muted">Mulai dengan menambahkan buku pertama ke perpustakaan.</p>
                    <a href="{{ route('admin.books.create') }}" class="button button-primary" style="margin-top: 12px;">+ Tambah Buku Pertama</a>
                @endif
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Cover</th>
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
                                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" style="width: 36px; height: 50px; object-fit: cover; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            </td>
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
                                    <a class="button button-info" href="{{ route('admin.books.show', $book) }}">Detail</a>
                                    <a class="button button-edit" href="{{ route('admin.books.edit', $book) }}">Edit</a>
                                    <form action="{{ route('admin.books.destroy', $book) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
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
