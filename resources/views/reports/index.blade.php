@extends('layouts.app')

@section('title', 'Laporan Perpustakaan - Sistem Manajemen Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Laporan Perpustakaan</h1>
            <p class="subtitle">Rekapitulasi aktivitas sirkulasi buku dan penerimaan denda keterlambatan.</p>
        </div>
        <div class="page-header-actions">
            <button onclick="window.print()" class="button button-primary">🖨️ Cetak Laporan</button>
        </div>
    </div>

    <!-- Filter Periode Laporan -->
    <div class="filter-card">
        <form action="{{ route('reports.index') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <label for="start_date">Dari Tanggal</label>
                <input id="start_date" type="date" name="start_date" value="{{ $startDate }}">
            </div>

            <div class="filter-group">
                <label for="end_date">Sampai Tanggal</label>
                <input id="end_date" type="date" name="end_date" value="{{ $endDate }}">
            </div>

            <div class="filter-group filter-select">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Semua Status</option>
                    <option value="Dipinjam" {{ request('status') === 'Dipinjam' ? 'selected' : '' }}>Masih Dipinjam</option>
                    <option value="Dikembalikan" {{ request('status') === 'Dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Tampilkan Laporan</button>
            </div>
        </form>
    </div>

    <!-- Statistik Ringkasan Periode Ini -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">📚</div>
            <div class="stat-info">
                <span class="stat-label">Total Peminjaman</span>
                <strong class="stat-value">{{ $totalBorrowed }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">✅</div>
            <div class="stat-info">
                <span class="stat-label">Buku Dikembalikan</span>
                <strong class="stat-value">{{ $totalReturned }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fffbeb; color: #d97706;">⏳</div>
            <div class="stat-info">
                <span class="stat-label">Masih Dipinjam</span>
                <strong class="stat-value">{{ $totalActive }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fef2f2; color: #dc2626;">💰</div>
            <div class="stat-info">
                <span class="stat-label">Denda Terkumpul</span>
                <strong class="stat-value">Rp {{ number_format($totalFinesCollected, 0, ',', '.') }}</strong>
            </div>
        </div>
    </div>

    <!-- Header Khusus Print -->
    <div class="print-header" style="display: none;">
        <h2 style="margin-bottom: 4px;">LAPORAN SIRKULASI PERPUSTAKAAN</h2>
        <p style="margin: 0; color: #555;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} s.d. {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        <hr style="margin: 16px 0; border: 0; border-top: 2px solid #333;">
    </div>

    <!-- Tabel Data Laporan -->
    <div class="table-wrap">
        @if ($transactions->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">📑</div>
                <h3>Tidak ada data transaksi pada periode ini.</h3>
                <p class="muted">Silakan pilih rentang tanggal peminjaman yang lain.</p>
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Kode Trx</th>
                        <th>Anggota</th>
                        <th>Kelas</th>
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
                            <td>{{ $index + 1 }}</td>
                            <td><code>{{ $trx->transaction_code }}</code></td>
                            <td><strong>{{ $trx->member->name ?? '-' }}</strong></td>
                            <td>{{ $trx->member->class ?? '-' }}</td>
                            <td>{{ $trx->book->title ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($trx->due_date)->format('d/m/Y') }}</td>
                            <td>
                                {{ $trx->return_date ? \Carbon\Carbon::parse($trx->return_date)->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                @if($trx->status === 'Dikembalikan')
                                    <span class="badge badge-success">Dikembalikan</span>
                                @elseif($trx->calculated_status === 'Terlambat')
                                    <span class="badge badge-danger">Terlambat</span>
                                @else
                                    <span class="badge badge-primary">Dipinjam</span>
                                @endif
                            </td>
                            <td>
                                @if($trx->fine_amount > 0)
                                    Rp {{ number_format($trx->fine_amount, 0, ',', '.') }}
                                @else
                                    Rp 0
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f8fafc; font-weight: 700;">
                        <td colspan="9" style="text-align: right; padding: 14px 16px;">TOTAL PENERIMAAN DENDA:</td>
                        <td style="color: #dc2626; padding: 14px 16px;">
                            Rp {{ number_format($totalFinesCollected, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        @endif
    </div>
@endsection
