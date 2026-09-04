@extends('layouts.app')

@section('title', 'Peminjaman Buku - Sistem Manajemen Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Peminjaman Buku Aktif</h1>
            <p class="subtitle">Kelola dan pantau daftar buku yang saat ini sedang dipinjam oleh anggota.</p>
        </div>
        <div class="page-header-actions">
            <a class="button button-primary" href="{{ route('borrowings.create') }}">+ Pinjam Buku Baru</a>
            <a class="button button-secondary" href="{{ route('returns.index') }}">📥 Form Pengembalian</a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-card">
        <form action="{{ route('borrowings.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search" style="flex: 1;">
                <label for="search">Cari Peminjaman</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Cari nomor transaksi, nama anggota, atau judul buku..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari Data</button>
                @if(request('search'))
                    <a href="{{ route('borrowings.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Peminjaman Aktif -->
    <div class="table-wrap">
        @if ($borrowings->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📤</div>
                @if (request('search'))
                    <h3>Peminjaman tidak ditemukan.</h3>
                    <p class="muted">Coba gunakan kata kunci pencarian yang lain.</p>
                    <a href="{{ route('borrowings.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset Pencarian</a>
                @else
                    <h3>Tidak ada peminjaman buku yang sedang aktif.</h3>
                    <p class="muted">Semua buku saat ini berada di perpustakaan atau belum ada transaksi aktif.</p>
                    <a href="{{ route('borrowings.create') }}" class="button button-primary" style="margin-top: 12px;">+ Catat Peminjaman Baru</a>
                @endif
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode Trx</th>
                        <th>Peminjam (Anggota)</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($borrowings as $index => $trx)
                        <tr>
                            <td>{{ $borrowings->firstItem() + $loop->index }}</td>
                            <td><code>{{ $trx->transaction_code }}</code></td>
                            <td>
                                <strong>{{ $trx->member->name ?? '-' }}</strong><br>
                                <small class="muted">{{ $trx->member->class ?? '' }} ({{ $trx->member->member_code ?? '' }})</small>
                            </td>
                            <td>
                                <strong>{{ $trx->book->title ?? '-' }}</strong><br>
                                <small class="muted">{{ $trx->book->author ?? '' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($trx->due_date)->format('d/m/Y') }}</strong>
                            </td>
                            <td>
                                @php $stat = $trx->calculated_status; @endphp
                                @if($stat === 'Terlambat')
                                    <span class="badge badge-danger">Terlambat ({{ $trx->days_late }} hr)</span>
                                @elseif($stat === 'Akan Jatuh Tempo')
                                    <span class="badge badge-warning">Akan Tempo</span>
                                @else
                                    <span class="badge badge-primary">Dipinjam</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('returns.process', $trx) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku {{ addslashes($trx->book->title ?? '') }} dari {{ addslashes($trx->member->name ?? '') }}?')">
                                    @csrf
                                    <button type="submit" class="button button-success button-sm">📥 Kembalikan</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Pagination -->
    @if ($borrowings->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-custom">
                @if ($borrowings->onFirstPage())
                    <span class="page-button disabled">&laquo; Sebelumnya</span>
                @else
                    <a href="{{ $borrowings->previousPageUrl() }}" class="page-button">&laquo; Sebelumnya</a>
                @endif

                <span class="page-info">Halaman <strong>{{ $borrowings->currentPage() }}</strong> dari <strong>{{ $borrowings->lastPage() }}</strong></span>

                @if ($borrowings->hasMorePages())
                    <a href="{{ $borrowings->nextPageUrl() }}" class="page-button">Selanjutnya &raquo;</a>
                @else
                    <span class="page-button disabled">Selanjutnya &raquo;</span>
                @endif
            </div>
        </div>
    @endif
@endsection
