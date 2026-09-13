@extends('layouts.app')

@section('title', 'Detail Buku - ' . $book->title)

@section('content')
    <div class="page-header">
        <div>
            <h1>Detail Buku</h1>
            <p class="subtitle">Informasi lengkap dan status sirkulasi buku dalam perpustakaan.</p>
        </div>
        <div class="page-header-actions">
            <a class="button button-secondary" href="{{ route('books.index') }}">&larr; Kembali</a>
            <a class="button button-edit" href="{{ route('books.edit', $book) }}">Edit Buku</a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-card-header">
            <div>
                <span class="badge badge-secondary">{{ $book->category_name }}</span>
                <h2 class="detail-title">{{ $book->title }}</h2>
            </div>
            <div>
                @if($book->stock > 0)
                    <span class="badge badge-success" style="font-size: 0.9rem; padding: 6px 12px;">
                        ✓ Tersedia ({{ $book->stock }} eks)
                    </span>
                @else
                    <span class="badge badge-danger" style="font-size: 0.9rem; padding: 6px 12px;">
                        ✗ Stok Habis (0 eks)
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
                <span class="detail-label">Sisa Stok</span>
                <span class="detail-value">{{ $book->stock }} eksemplar</span>
            </div>
        </div>

        <div class="detail-description-box">
            <h3>Sinopsis / Deskripsi</h3>
            <div class="description">
                {{ $book->description ?: 'Tidak ada deskripsi untuk buku ini.' }}
            </div>
        </div>

        <!-- Daftar Peminjaman Aktif untuk Buku ini -->
        @if($book->activeBorrowings && $book->activeBorrowings->count() > 0)
            <div style="margin-top: 28px;">
                <h3 style="margin-bottom: 12px; font-size: 1.05rem;">Sedang Dipinjam Oleh:</h3>
                <div class="table-wrap" style="box-shadow: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama Anggota</th>
                                <th>Kelas</th>
                                <th>Tgl Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($book->activeBorrowings as $ab)
                                <tr>
                                    <td><strong>{{ $ab->member->name ?? '-' }}</strong></td>
                                    <td>{{ $ab->member->class ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ab->borrow_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($ab->due_date)->format('d/m/Y') }}</td>
                                    <td>
                                        @php $stat = $ab->calculated_status; @endphp
                                        @if($stat === 'Terlambat')
                                            <span class="badge badge-danger">Terlambat ({{ $ab->days_late }} hr)</span>
                                        @elseif($stat === 'Akan Jatuh Tempo')
                                            <span class="badge badge-warning">Akan Tempo</span>
                                        @else
                                            <span class="badge badge-primary">Dipinjam</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="form-actions" style="margin-top: 24px;">
            @if($book->stock > 0)
                <a href="{{ route('borrowings.create') }}" class="button button-primary">+ Pinjamkan Buku Ini</a>
            @endif
            <a href="{{ route('books.index') }}" class="button button-secondary">Kembali ke Daftar Buku</a>
        </div>
    </div>
@endsection
