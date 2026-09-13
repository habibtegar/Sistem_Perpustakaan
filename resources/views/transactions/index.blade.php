@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Sistem Manajemen Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Riwayat Transaksi</h1>
            <p class="subtitle">Semua riwayat peminjaman dan pengembalian buku di perpustakaan.</p>
        </div>
        <a class="button button-primary" href="{{ route('borrowings.create') }}">+ Peminjaman Baru</a>
    </div>

    <!-- Statistik Transaksi -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">📋</div>
            <div class="stat-info">
                <span class="stat-label">Total Transaksi</span>
                <strong class="stat-value">{{ $totalTransactions }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fef2f2; color: #dc2626;">💰</div>
            <div class="stat-info">
                <span class="stat-label">Total Denda Terkumpul</span>
                <strong class="stat-value">Rp {{ number_format($totalFines, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-card">
        <form action="{{ route('transactions.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search">
                <label for="search">Cari Transaksi</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Cari kode transaksi, nama anggota, judul buku..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-group filter-select">
                <label for="status">Filter Status</label>
                <select id="status" name="status">
                    <option value="">Semua Status</option>
                    <option value="Dipinjam" {{ request('status') === 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="Akan Jatuh Tempo" {{ request('status') === 'Akan Jatuh Tempo' ? 'selected' : '' }}>Akan Jatuh Tempo</option>
                    <option value="Terlambat" {{ request('status') === 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                    <option value="Dikembalikan" {{ request('status') === 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari & Filter</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('transactions.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Riwayat Transaksi -->
    <div class="table-wrap">
        @if ($transactions->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                @if (request('search') || request('status'))
                    <h3>Transaksi tidak ditemukan.</h3>
                    <p class="muted">Coba ubah kata kunci atau filter status pencarian Anda.</p>
                    <a href="{{ route('transactions.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset Filter</a>
                @else
                    <h3>Belum ada riwayat transaksi.</h3>
                    <p class="muted">Mulai dengan mencatat peminjaman buku pertama.</p>
                    <a href="{{ route('borrowings.create') }}" class="button button-primary" style="margin-top: 12px;">+ Catat Peminjaman</a>
                @endif
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode Trx</th>
                        <th>Anggota</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $index => $trx)
                        <tr>
                            <td>{{ $transactions->firstItem() + $loop->index }}</td>
                            <td><code>{{ $trx->transaction_code }}</code></td>
                            <td>
                                <strong>{{ $trx->member->name ?? '-' }}</strong><br>
                                <small class="muted">{{ $trx->member->member_code ?? '' }}</small>
                            </td>
                            <td>
                                <strong>{{ $trx->book->title ?? '-' }}</strong><br>
                                <small class="muted">{{ $trx->book->author ?? '' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($trx->due_date)->format('d/m/Y') }}</td>
                            <td>
                                @if($trx->return_date)
                                    <span style="color: #15803d; font-weight: 500;">
                                        {{ \Carbon\Carbon::parse($trx->return_date)->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php $stat = $trx->calculated_status; @endphp
                                @if($stat === 'Dikembalikan')
                                    <span class="badge badge-success">Dikembalikan</span>
                                @elseif($stat === 'Terlambat')
                                    <span class="badge badge-danger">Terlambat ({{ $trx->days_late }} hr)</span>
                                @elseif($stat === 'Akan Jatuh Tempo')
                                    <span class="badge badge-warning">Akan Tempo</span>
                                @else
                                    <span class="badge badge-primary">Dipinjam</span>
                                @endif
                            </td>
                            <td>
                                @php $finalFine = $trx->status === 'Dikembalikan' ? $trx->fine_amount : $trx->current_fine; @endphp
                                @if($finalFine > 0)
                                    <strong style="color: #dc2626;">Rp {{ number_format($finalFine, 0, ',', '.') }}</strong>
                                @else
                                    <span class="muted">Rp 0</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Pagination -->
    @if ($transactions->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-custom">
                @if ($transactions->onFirstPage())
                    <span class="page-button disabled">&laquo; Sebelumnya</span>
                @else
                    <a href="{{ $transactions->previousPageUrl() }}" class="page-button">&laquo; Sebelumnya</a>
                @endif

                <span class="page-info">Halaman <strong>{{ $transactions->currentPage() }}</strong> dari <strong>{{ $transactions->lastPage() }}</strong></span>

                @if ($transactions->hasMorePages())
                    <a href="{{ $transactions->nextPageUrl() }}" class="page-button">Selanjutnya &raquo;</a>
                @else
                    <span class="page-button disabled">Selanjutnya &raquo;</span>
                @endif
            </div>
        </div>
    @endif
@endsection
