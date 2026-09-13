@extends('layouts.app')

@section('title', 'Pengembalian Buku - Sistem Manajemen Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Pengembalian Buku</h1>
            <p class="subtitle">Proses pengembalian buku dan perhitungan denda keterlambatan secara otomatis.</p>
        </div>
        <div class="page-header-actions">
            <span class="badge badge-secondary" style="font-size: 0.85rem; padding: 6px 12px;">
                Tarif Denda: <strong>Rp {{ number_format($fineRate, 0, ',', '.') }}/hari</strong>
            </span>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-card">
        <form action="{{ route('returns.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search" style="flex: 1;">
                <label for="search">Cari Pinjaman</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Cari kode transaksi, nama anggota, judul buku..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari</button>
                @if(request('search'))
                    <a href="{{ route('returns.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengembalian -->
    <div class="table-wrap">
        @if ($borrowings->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">🎉</div>
                @if (request('search'))
                    <h3>Data peminjaman tidak ditemukan.</h3>
                    <p class="muted">Coba cari dengan kata kunci lain.</p>
                    <a href="{{ route('returns.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset</a>
                @else
                    <h3>Tidak ada pinjaman buku yang perlu dikembalikan saat ini.</h3>
                    <p class="muted">Semua peminjaman telah diselesaikan dengan tertib.</p>
                @endif
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode Trx</th>
                        <th>Peminjam</th>
                        <th>Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Keterlambatan</th>
                        <th>Denda (Otomatis)</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($borrowings as $index => $trx)
                        @php
                            $daysLate = $trx->days_late;
                            $fine = $trx->current_fine;
                            $isOverdue = $daysLate > 0;
                        @endphp
                        <tr style="{{ $isOverdue ? 'background-color: #fffafa;' : '' }}">
                            <td>{{ $borrowings->firstItem() + $loop->index }}</td>
                            <td><code>{{ $trx->transaction_code }}</code></td>
                            <td>
                                <strong>{{ $trx->member->name ?? '-' }}</strong><br>
                                <small class="muted">{{ $trx->member->class ?? '' }}</small>
                            </td>
                            <td>
                                <strong>{{ $trx->book->title ?? '-' }}</strong>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') }}</td>
                            <td>
                                <strong style="color: {{ $isOverdue ? '#dc2626' : 'inherit' }};">
                                    {{ \Carbon\Carbon::parse($trx->due_date)->format('d/m/Y') }}
                                </strong>
                            </td>
                            <td>
                                @if ($isOverdue)
                                    <span class="badge badge-danger">Terlambat {{ $daysLate }} hari</span>
                                @elseif ($trx->calculated_status === 'Akan Jatuh Tempo')
                                    <span class="badge badge-warning">Akan Tempo</span>
                                @else
                                    <span class="badge badge-success">Tepat Waktu</span>
                                @endif
                            </td>
                            <td>
                                @if ($fine > 0)
                                    <strong style="color: #dc2626;">Rp {{ number_format($fine, 0, ',', '.') }}</strong>
                                @else
                                    <span class="muted">Rp 0</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('returns.process', $trx) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku {{ addslashes($trx->book->title ?? '') }} dari {{ addslashes($trx->member->name ?? '') }}? {{ $fine > 0 ? '\n\nTotal Denda: Rp ' . number_format($fine, 0, ',', '.') : '' }}')">
                                    @csrf
                                    <input type="hidden" name="return_date" value="{{ date('Y-m-d') }}">
                                    <button type="submit" class="button button-success button-sm">
                                        📥 Kembalikan
                                    </button>
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
