@extends('layouts.app')

@section('title', 'Kelola Peminjaman - Administrator')

@section('content')
    <div class="page-header">
        <div>
            <h1>Kelola Peminjaman Buku</h1>
            <p class="subtitle">Setujui pengajuan peminjaman online atau buat peminjaman langsung di tempat.</p>
        </div>
        <div class="page-header-actions">
            <a class="button button-primary" href="{{ route('admin.borrowings.create') }}">+ Peminjaman Langsung</a>
            <a class="button button-secondary" href="{{ route('admin.returns.index') }}">📥 Pengembalian Buku</a>
        </div>
    </div>

    <!-- Filter Tab Status -->
    <div class="filter-card">
        <form action="{{ route('admin.borrowings.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search" style="flex: 2;">
                <label for="search">Cari Peminjaman</label>
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
                    <option value="">Semua (Aktif & Menunggu)</option>
                    <option value="Menunggu" {{ request('status') === 'Menunggu' ? 'selected' : '' }}>⏳ Menunggu Persetujuan ({{ $pendingCount }})</option>
                    <option value="Dipinjam" {{ request('status') === 'Dipinjam' ? 'selected' : '' }}>📤 Sedang Dipinjam ({{ $activeCount }})</option>
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari & Filter</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.borrowings.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Peminjaman -->
    <div class="table-wrap">
        @if ($borrowings->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📤</div>
                @if (request('search') || request('status'))
                    <h3>Data peminjaman tidak ditemukan.</h3>
                    <p class="muted">Coba ubah kata kunci atau filter status.</p>
                    <a href="{{ route('admin.borrowings.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset Filter</a>
                @else
                    <h3>Tidak ada peminjaman aktif atau permohonan baru.</h3>
                    <p class="muted">Semua permohonan telah diproses atau belum ada peminjaman aktif.</p>
                @endif
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Kode Trx</th>
                        <th>Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pengajuan / Pinjam</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th style="width: 200px;">Aksi Persetujuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($borrowings as $index => $trx)
                        <tr style="{{ $trx->status === 'Menunggu' ? 'background-color: #fffdf5;' : '' }}">
                            <td>{{ $borrowings->firstItem() + $loop->index }}</td>
                            <td><code>{{ $trx->transaction_code }}</code></td>
                            <td>
                                <strong>{{ $trx->member->name ?? '-' }}</strong><br>
                                <small class="muted">{{ $trx->member->class ?? '' }} ({{ $trx->member->member_code ?? '' }})</small>
                            </td>
                            <td>
                                <strong>{{ $trx->book->title ?? '-' }}</strong><br>
                                <small class="muted">Sisa Stok: {{ $trx->book->stock ?? 0 }} eks</small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($trx->due_date)->format('d/m/Y') }}</td>
                            <td>
                                @php $stat = $trx->calculated_status; @endphp
                                @if($stat === 'Menunggu')
                                    <span class="badge badge-warning">⏳ Menunggu</span>
                                @elseif($stat === 'Terlambat')
                                    <span class="badge badge-danger">Terlambat ({{ $trx->days_late }} hr)</span>
                                @elseif($stat === 'Akan Jatuh Tempo')
                                    <span class="badge badge-warning">Akan Tempo</span>
                                @else
                                    <span class="badge badge-primary">Dipinjam</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    @if($trx->status === 'Menunggu')
                                        <!-- Tombol Setujui -->
                                        <form action="{{ route('admin.borrowings.approve', $trx) }}" method="POST" onsubmit="return confirm('Setujui peminjaman buku {{ addslashes($trx->book->title ?? '') }} untuk {{ addslashes($trx->member->name ?? '') }}?\nStok buku akan berkurang 1.')">
                                            @csrf
                                            <button type="submit" class="button button-success button-sm">
                                                ✓ Setujui
                                            </button>
                                        </form>

                                        <!-- Tombol Tolak -->
                                        <form action="{{ route('admin.borrowings.reject', $trx) }}" method="POST" onsubmit="return confirm('Tolak permohonan peminjaman ini?')">
                                            @csrf
                                            <button type="submit" class="button button-danger button-sm">
                                                ✗ Tolak
                                            </button>
                                        </form>
                                    @elseif($trx->status === 'Dipinjam')
                                        <!-- Tombol Kembalikan Cepat -->
                                        <form action="{{ route('admin.returns.process', $trx) }}" method="POST" onsubmit="return confirm('Kembalikan buku ini? Stok akan bertambah 1.')">
                                            @csrf
                                            <button type="submit" class="button button-info button-sm">
                                                📥 Kembalikan
                                            </button>
                                        </form>
                                    @endif
                                </div>
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
