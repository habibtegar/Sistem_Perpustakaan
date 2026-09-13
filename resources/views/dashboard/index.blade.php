@extends('layouts.app')

@section('title', 'Dashboard - Sistem Manajemen Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard Perpustakaan</h1>
            <p class="subtitle">Ringkasan aktivitas dan status koleksi perpustakaan secara real-time.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('borrowings.create') }}" class="button button-primary">+ Peminjaman Baru</a>
            <a href="{{ route('returns.index') }}" class="button button-secondary">📥 Pengembalian</a>
        </div>
    </div>

    <!-- Statistik Utama -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">📚</div>
            <div class="stat-info">
                <span class="stat-label">Total Judul Buku</span>
                <strong class="stat-value">{{ number_format($totalBooks, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">📦</div>
            <div class="stat-info">
                <span class="stat-label">Buku Tersedia (Stok)</span>
                <strong class="stat-value">{{ number_format($totalStock, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">📤</div>
            <div class="stat-info">
                <span class="stat-label">Peminjaman Aktif</span>
                <strong class="stat-value">{{ number_format($activeBorrowingsCount, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">👥</div>
            <div class="stat-info">
                <span class="stat-label">Total Anggota</span>
                <strong class="stat-value">{{ number_format($totalMembers, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: {{ $overdueCount > 0 ? '#fef2f2' : '#f8fafc' }}; color: {{ $overdueCount > 0 ? '#dc2626' : '#94a3b8' }};">⏰</div>
            <div class="stat-info">
                <span class="stat-label">Buku Terlambat</span>
                <strong class="stat-value" style="color: {{ $overdueCount > 0 ? '#dc2626' : 'inherit' }}">
                    {{ number_format($overdueCount, 0, ',', '.') }}
                </strong>
            </div>
        </div>
    </div>

    <!-- Widgets Grid -->
    <div class="dashboard-grid">
        <!-- Peminjaman Terbaru -->
        <div class="widget-card">
            <div class="widget-header">
                <div class="widget-title">
                    <span>📤 Peminjaman Terbaru</span>
                </div>
                <a href="{{ route('borrowings.index') }}" class="button button-info button-sm">Lihat Semua</a>
            </div>
            @if ($recentTransactions->isEmpty())
                <div class="empty-state" style="padding: 24px 0;">
                    <p class="muted">Belum ada aktivitas peminjaman terbaru.</p>
                </div>
            @else
                <div class="table-wrap" style="box-shadow: none; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tgl Pinjam</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentTransactions as $trx)
                                <tr>
                                    <td>
                                        <strong>{{ $trx->member->name ?? '-' }}</strong><br>
                                        <small class="muted">{{ $trx->member->member_code ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span>{{ Str::limit($trx->book->title ?? '-', 24) }}</span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') }}</td>
                                    <td>
                                        @php $stat = $trx->calculated_status; @endphp
                                        @if($stat === 'Dikembalikan')
                                            <span class="badge badge-success">Dikembalikan</span>
                                        @elseif($stat === 'Terlambat')
                                            <span class="badge badge-danger">Terlambat</span>
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
            @endif
        </div>

        <!-- Buku Terlambat -->
        <div class="widget-card">
            <div class="widget-header">
                <div class="widget-title">
                    <span style="color: #dc2626;">⚠️ Buku Yang Terlambat</span>
                </div>
                <a href="{{ route('returns.index') }}" class="button button-info button-sm">Proses Kembali</a>
            </div>
            @if ($overdueTransactions->isEmpty())
                <div class="empty-state" style="padding: 24px 0;">
                    <p class="muted" style="color: #16a34a;">🎉 Bagus! Tidak ada buku yang sedang terlambat.</p>
                </div>
            @else
                <div class="table-wrap" style="box-shadow: none; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Jatuh Tempo</th>
                                <th>Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overdueTransactions as $trx)
                                <tr>
                                    <td>
                                        <strong>{{ $trx->member->name ?? '-' }}</strong>
                                    </td>
                                    <td>{{ Str::limit($trx->book->title ?? '-', 22) }}</td>
                                    <td>
                                        <span style="color: #dc2626; font-weight: 600;">
                                            {{ \Carbon\Carbon::parse($trx->due_date)->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-danger">
                                            Rp {{ number_format($trx->current_fine, 0, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Buku Paling Sering Dipinjam -->
        <div class="widget-card" style="grid-column: 1 / -1;">
            <div class="widget-header">
                <div class="widget-title">
                    <span>🌟 Buku Paling Sering Dipinjam</span>
                </div>
                <a href="{{ route('books.index') }}" class="button button-info button-sm">Katalog Lengkap</a>
            </div>
            @if ($popularBooks->isEmpty())
                <div class="empty-state" style="padding: 24px 0;">
                    <p class="muted">Belum ada riwayat peminjaman buku.</p>
                </div>
            @else
                <div class="table-wrap" style="box-shadow: none; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Peringkat</th>
                                <th>Judul Buku</th>
                                <th>Penulis</th>
                                <th>Kategori</th>
                                <th>Sisa Stok</th>
                                <th>Total Dipinjam</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($popularBooks as $index => $book)
                                <tr>
                                    <td>
                                        @if($index === 0) 🥇 <strong>1</strong>
                                        @elseif($index === 1) 🥈 <strong>2</strong>
                                        @elseif($index === 2) 🥉 <strong>3</strong>
                                        @else #{{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('books.show', $book) }}" style="font-weight: 600; color: var(--primary);">
                                            {{ $book->title }}
                                        </a>
                                    </td>
                                    <td>{{ $book->author }}</td>
                                    <td><span class="badge badge-secondary">{{ $book->category_name }}</span></td>
                                    <td>
                                        @if($book->stock > 0)
                                            <span class="badge badge-success">{{ $book->stock }} eks</span>
                                        @else
                                            <span class="badge badge-danger">Habis</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong style="color: var(--primary);">{{ $book->transactions_count }} kali</strong>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
