@extends('layouts.app')

@section('title', 'Kelola Kategori - Administrator')

@section('content')
    <div class="page-header">
        <div>
            <h1>Kelola Kategori Buku</h1>
            <p class="subtitle">Kelola klasifikasi dan pengelompokan buku dalam perpustakaan.</p>
        </div>
        <a class="button button-primary" href="{{ route('admin.categories.create') }}">+ Tambah Kategori</a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-card">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search" style="flex: 1;">
                <label for="search">Cari Kategori</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Cari nama kategori atau deskripsi..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari</button>
                @if(request('search'))
                    <a href="{{ route('admin.categories.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Kategori -->
    <div class="table-wrap">
        @if ($categories->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🏷️</div>
                @if (request('search'))
                    <h3>Kategori tidak ditemukan.</h3>
                    <p class="muted">Coba gunakan kata kunci pencarian yang lain.</p>
                    <a href="{{ route('admin.categories.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset Pencarian</a>
                @else
                    <h3>Belum ada kategori buku.</h3>
                    <p class="muted">Mulai dengan menambahkan kategori baru ke sistem Anda.</p>
                    <a href="{{ route('admin.categories.create') }}" class="button button-primary" style="margin-top: 12px;">+ Tambah Kategori Pertama</a>
                @endif
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th style="width: 140px;">Jumlah Buku</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $index => $category)
                        <tr>
                            <td>{{ $categories->firstItem() + $loop->index }}</td>
                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>
                            <td>{{ $category->description ?: '-' }}</td>
                            <td>
                                <span class="badge badge-primary">{{ $category->books_count }} buku</span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a class="button button-edit" href="{{ route('admin.categories.edit', $category) }}">Edit</a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
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
    @if ($categories->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-custom">
                @if ($categories->onFirstPage())
                    <span class="page-button disabled">&laquo; Sebelumnya</span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" class="page-button">&laquo; Sebelumnya</a>
                @endif

                <span class="page-info">Halaman <strong>{{ $categories->currentPage() }}</strong> dari <strong>{{ $categories->lastPage() }}</strong></span>

                @if ($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" class="page-button">Selanjutnya &raquo;</a>
                @else
                    <span class="page-button disabled">Selanjutnya &raquo;</span>
                @endif
            </div>
        </div>
    @endif
@endsection
