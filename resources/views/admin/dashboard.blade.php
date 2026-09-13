@extends('layouts.app')

@section('title', 'Dashboard Administrator - Sistem Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Dashboard Administrator</h1>
            <p class="subtitle">Pusat kendali dan analitik operasional perpustakaan.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.borrowings.create') }}" class="button button-primary">+ Peminjaman Langsung</a>
            <a href="{{ route('admin.books.create') }}" class="button button-secondary">+ Tambah Buku</a>
        </div>
    </div>

    <!-- Alert jika ada permohonan peminjaman baru -->
    @if ($pendingCount > 0)
        <div class="alert alert-warning" style="border-left: 4px solid var(--warning);">
            <span>
                🔔 Ada <strong>{{ $pendingCount }} permohonan peminjaman buku baru</strong> yang menunggu persetujuan Anda.
            </span>
            <a href="{{ route('admin.borrowings.index', ['status' => 'Menunggu']) }}" class="button button-primary button-sm" style="margin-left: 12px;">
                Tinjau Permohonan &rarr;
            </a>
        </div>
    @endif

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
                <span class="stat-label">Total Stok Tersedia</span>
                <strong class="stat-value">{{ number_format($totalStock, 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">📤</div>
            <div class="stat-info">
                <span class="stat-label">Sedang Dipinjam</span>
                <strong class="stat-value">{{ number_format($borrowedBooksCount, 0, ',', '.') }}</strong>
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
            <div class="stat-icon" style="background: #fef3c7; color: #d97706;">🏷️</div>
            <div class="stat-info">
                <span class="stat-label">Total Kategori</span>
                <strong class="stat-value">{{ number_format($totalCategories, 0, ',', '.') }}</strong>
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
                <a href="{{ route('admin.borrowings.index') }}" class="button button-info button-sm">Lihat Semua</a>
            </div>
            @if ($recentTransactions->isEmpty())
                <div class="empty-state" style="padding: 24px 0;">
                    <p class="muted">Belum ada aktivitas transaksi peminjaman.</p>
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
                                    <td>{{ Str::limit($trx->book->title ?? '-', 24) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') }}</td>
                                    <td>
                                        @php $stat = $trx->calculated_status; @endphp
                                        @if($stat === 'Menunggu')
                                            <span class="badge badge-warning">Menunggu</span>
                                        @elseif($stat === 'Dikembalikan')
                                            <span class="badge badge-success">Dikembalikan</span>
                                        @elseif($stat === 'Terlambat')
                                            <span class="badge badge-danger">Terlambat</span>
                                        @elseif($stat === 'Ditolak')
                                            <span class="badge badge-danger">Ditolak</span>
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

        <!-- Buku Terbaru Ditambahkan -->
        <div class="widget-card">
            <div class="widget-header">
                <div class="widget-title">
                    <span>📚 Buku Terbaru Ditambahkan</span>
                </div>
                <a href="{{ route('admin.books.index') }}" class="button button-info button-sm">Semua Buku</a>
            </div>
            @if ($latestBooks->isEmpty())
                <div class="empty-state" style="padding: 24px 0;">
                    <p class="muted">Belum ada buku dalam katalog.</p>
                </div>
            @else
                <div class="table-wrap" style="box-shadow: none; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Penulis</th>
                                <th>Kategori</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latestBooks as $book)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.books.show', $book) }}" style="font-weight: 600; color: var(--primary);">
                                            {{ Str::limit($book->title, 24) }}
                                        </a>
                                    </td>
                                    <td>{{ Str::limit($book->author, 18) }}</td>
                                    <td><span class="badge badge-secondary">{{ $book->category_name }}</span></td>
                                    <td>
                                        @if($book->stock > 0)
                                            <span class="badge badge-success">{{ $book->stock }}</span>
                                        @else
                                            <span class="badge badge-danger">0</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Peringatan Keterlambatan -->
        @if ($overdueTransactions->isNotEmpty())
            <div class="widget-card" style="grid-column: 1 / -1; border-left: 4px solid var(--danger);">
                <div class="widget-header">
                    <div class="widget-title" style="color: #dc2626;">
                        <span>⚠️ Perhatian: Peminjaman Yang Melewati Batas Waktu</span>
                    </div>
                    <a href="{{ route('admin.returns.index') }}" class="button button-danger button-sm">Proses Pengembalian</a>
                </div>
                <div class="table-wrap" style="box-shadow: none; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode Trx</th>
                                <th>Peminjam</th>
                                <th>Buku</th>
                                <th>Jatuh Tempo</th>
                                <th>Keterlambatan</th>
                                <th>Estimasi Denda</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($overdueTransactions as $trx)
                                <tr>
                                    <td><code>{{ $trx->transaction_code }}</code></td>
                                    <td><strong>{{ $trx->member->name ?? '-' }}</strong> ({{ $trx->member->phone ?: 'No HP -' }})</td>
                                    <td>{{ $trx->book->title ?? '-' }}</td>
                                    <td><strong style="color: #dc2626;">{{ \Carbon\Carbon::parse($trx->due_date)->format('d/m/Y') }}</strong></td>
                                    <td><span class="badge badge-danger">{{ $trx->days_late }} hari</span></td>
                                    <td><strong style="color: #dc2626;">Rp {{ number_format($trx->current_fine, 0, ',', '.') }}</strong></td>
                                    <td>
                                        <form action="{{ route('admin.returns.process', $trx) }}" method="POST" onsubmit="return confirm('Konfirmasi pengembalian buku?')">
                                            @csrf
                                            <button type="submit" class="button button-success button-sm">📥 Kembalikan</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endsection
