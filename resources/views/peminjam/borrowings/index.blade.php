@extends('layouts.app')

@section('title', 'Peminjaman Saya - Sistem Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Peminjaman Saya</h1>
            <p class="subtitle">Pantau status peminjaman aktif dan permohonan buku yang sedang Anda ajukan.</p>
        </div>
        <a class="button button-primary" href="{{ route('peminjam.books.index') }}">+ Ajukan Pinjaman Baru</a>
    </div>

    <!-- Statistik Peminjaman Pribadi -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">📖</div>
            <div class="stat-info">
                <span class="stat-label">Buku Sedang Dipinjam</span>
                <strong class="stat-value">{{ $activeCount }} buku</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fffbeb; color: #d97706;">⏳</div>
            <div class="stat-info">
                <span class="stat-label">Permohonan Menunggu</span>
                <strong class="stat-value">{{ $pendingCount }}</strong>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-card">
        <form action="{{ route('peminjam.my-borrowings') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search" style="flex: 2;">
                <label for="search">Cari Pinjaman Saya</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Cari kode transaksi atau judul buku..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-group filter-select">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Semua Status Aktif</option>
                    <option value="Menunggu" {{ request('status') === 'Menunggu' ? 'selected' : '' }}>⏳ Menunggu Persetujuan</option>
                    <option value="Dipinjam" {{ request('status') === 'Dipinjam' ? 'selected' : '' }}>📤 Sedang Dipinjam</option>
                    <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>✗ Ditolak</option>
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari Data</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('peminjam.my-borrowings') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Peminjaman Saya -->
    <div class="table-wrap">
        @if ($borrowings->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📖</div>
                @if (request('search') || request('status'))
                    <h3>Tidak ditemukan data peminjaman yang cocok.</h3>
                    <p class="muted">Coba ubah filter atau kata kunci pencarian Anda.</p>
                    <a href="{{ route('peminjam.my-borrowings') }}" class="button button-secondary" style="margin-top: 12px;">Reset Filter</a>
                @else
                    <h3>Anda belum memiliki peminjaman aktif atau pengajuan buku.</h3>
                    <p class="muted">Silakan cari buku di katalog dan ajukan peminjaman pertama Anda.</p>
                    <a href="{{ route('peminjam.books.index') }}" class="button button-primary" style="margin-top: 12px;">📚 Lihat Katalog Buku</a>
                @endif
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode Trx</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam / Pengajuan</th>
                        <th>Batas Jatuh Tempo</th>
                        <th>Status</th>
                        <th>Keterlambatan / Denda</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($borrowings as $index => $trx)
                        <tr style="{{ $trx->status === 'Menunggu' ? 'background-color: #fffdf5;' : '' }}">
                            <td>{{ $borrowings->firstItem() + $loop->index }}</td>
                            <td><code>{{ $trx->transaction_code }}</code></td>
                            <td>
                                <strong>{{ $trx->book->title ?? '-' }}</strong><br>
                                <small class="muted">{{ $trx->book->author ?? '' }}</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') }}</td>
                            <td>
                                @if($trx->status === 'Menunggu')
                                    <span class="muted">Dihitung setelah disetujui</span>
                                @else
                                    <strong style="color: {{ $trx->calculated_status === 'Terlambat' ? '#dc2626' : 'inherit' }};">
                                        {{ \Carbon\Carbon::parse($trx->due_date)->format('d/m/Y') }}
                                    </strong>
                                @endif
                            </td>
                            <td>
                                @php $stat = $trx->calculated_status; @endphp
                                @if($stat === 'Menunggu')
                                    <span class="badge badge-warning">⏳ Menunggu Persetujuan</span>
                                @elseif($stat === 'Terlambat')
                                    <span class="badge badge-danger">Terlambat ({{ $trx->days_late }} hr)</span>
                                @elseif($stat === 'Ditolak')
                                    <span class="badge badge-danger">✗ Ditolak</span>
                                @elseif($stat === 'Akan Jatuh Tempo')
                                    <span class="badge badge-warning">Akan Tempo</span>
                                @else
                                    <span class="badge badge-primary">Dipinjam</span>
                                @endif
                            </td>
                            <td>
                                @if($trx->current_fine > 0)
                                    <strong style="color: #dc2626;">Rp {{ number_format($trx->current_fine, 0, ',', '.') }}</strong>
                                @else
                                    <span class="muted">Rp 0</span>
                                @endif
                            </td>
                            <td>
                                @if($trx->admin_notes)
                                    <small style="color: #475569;">{{ $trx->admin_notes }}</small>
                                @else
                                    <small class="muted">-</small>
                                @endif
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
