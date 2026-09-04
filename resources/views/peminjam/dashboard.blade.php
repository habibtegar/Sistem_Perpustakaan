@extends('layouts.app')

@section('title', 'Dashboard Peminjam - Sistem Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Selamat Datang, {{ auth()->user()->name }}! 👋</h1>
            <p class="subtitle">Pantau status peminjaman buku, batas waktu pengembalian, dan jelajahi katalog buku.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('peminjam.books.index') }}" class="button button-primary">📚 Jelajahi Katalog Buku</a>
            <a href="{{ route('peminjam.my-borrowings') }}" class="button button-secondary">📖 Peminjaman Saya</a>
        </div>
    </div>

    <!-- Peringatan Keterlambatan / Jatuh Tempo Segera -->
    @if ($dueSoonLoans->isNotEmpty())
        <div class="alert alert-error" style="border-left: 4px solid var(--danger); margin-bottom: 24px;">
            <div>
                <strong>⚠️ Perhatian Pengembalian Buku:</strong>
                <span>Anda memiliki {{ $dueSoonLoans->count() }} buku yang jatuh tempo segera atau telah terlambat. Segera lakukan pengembalian ke perpustakaan untuk menghindari/menghentikan denda.</span>
            </div>
        </div>
    @endif

    <!-- Statistik Personal Peminjam -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">📖</div>
            <div class="stat-info">
                <span class="stat-label">Buku Sedang Dipinjam</span>
                <strong class="stat-value">{{ $totalActiveCount }} buku</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #fffbeb; color: #d97706;">⏳</div>
            <div class="stat-info">
                <span class="stat-label">Pengajuan Menunggu</span>
                <strong class="stat-value">{{ $totalPendingCount }} pengajuan</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: {{ $totalFineAmount > 0 ? '#fef2f2' : '#f0fdf4' }}; color: {{ $totalFineAmount > 0 ? '#dc2626' : '#16a34a' }};">💰</div>
            <div class="stat-info">
                <span class="stat-label">Total Tagihan Denda</span>
                <strong class="stat-value" style="color: {{ $totalFineAmount > 0 ? '#dc2626' : '#16a34a' }}">
                    Rp {{ number_format($totalFineAmount, 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #f5f3ff; color: #8b5cf6;">🆔</div>
            <div class="stat-info">
                <span class="stat-label">ID / Kode Anggota</span>
                <strong class="stat-value" style="font-size: 1.15rem;">{{ $member->member_code ?? '-' }}</strong>
            </div>
        </div>
    </div>

    <!-- Grid Status Pinjaman & Rekomendasi -->
    <div class="dashboard-grid">
        <!-- Buku yang Sedang Dipinjam Saat Ini -->
        <div class="widget-card">
            <div class="widget-header">
                <div class="widget-title">
                    <span>📖 Buku Yang Sedang Anda Pinjam</span>
                </div>
                <a href="{{ route('peminjam.my-borrowings') }}" class="button button-info button-sm">Lihat Detail</a>
            </div>
            @if ($activeLoans->isEmpty())
                <div class="empty-state" style="padding: 24px 0;">
                    <p class="muted">Anda sedang tidak meminjam buku apa pun saat ini.</p>
                    <a href="{{ route('peminjam.books.index') }}" class="button button-primary button-sm" style="margin-top: 10px;">+ Pinjam Buku Sekarang</a>
                </div>
            @else
                <div class="table-wrap" style="box-shadow: none; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Judul Buku</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeLoans as $trx)
                                <tr>
                                    <td>
                                        <strong>{{ $trx->book->title ?? '-' }}</strong><br>
                                        <small class="muted">{{ $trx->book->author ?? '' }}</small>
                                    </td>
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
                                        @if($trx->current_fine > 0)
                                            <strong style="color: #dc2626;">Rp {{ number_format($trx->current_fine, 0, ',', '.') }}</strong>
                                        @else
                                            <span class="muted">Rp 0</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Status Pengajuan Menunggu Persetujuan -->
        <div class="widget-card">
            <div class="widget-header">
                <div class="widget-title">
                    <span>⏳ Permohonan Menunggu Persetujuan</span>
                </div>
                <a href="{{ route('peminjam.my-borrowings', ['status' => 'Menunggu']) }}" class="button button-info button-sm">Semua Pengajuan</a>
            </div>
            @if ($pendingRequests->isEmpty())
                <div class="empty-state" style="padding: 24px 0;">
                    <p class="muted">Tidak ada permohonan peminjaman yang sedang menunggu.</p>
                </div>
            @else
                <div class="table-wrap" style="box-shadow: none; border: none;">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Judul Buku</th>
                                <th>Tgl Pengajuan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingRequests as $req)
                                <tr>
                                    <td><code>{{ $req->transaction_code }}</code></td>
                                    <td><strong>{{ $req->book->title ?? '-' }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($req->borrow_date)->format('d/m/Y') }}</td>
                                    <td><span class="badge badge-warning">⏳ Menunggu Admin</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Rekomendasi Buku Tersedia -->
        <div class="widget-card" style="grid-column: 1 / -1;">
            <div class="widget-header">
                <div class="widget-title">
                    <span>✨ Rekomendasi Koleksi Buku Tersedia</span>
                </div>
                <a href="{{ route('peminjam.books.index') }}" class="button button-info button-sm">Katalog Lengkap &rarr;</a>
            </div>
            @if ($recommendedBooks->isEmpty())
                <p class="muted">Belum ada buku tersedia.</p>
            @else
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 16px;">
                    @foreach ($recommendedBooks as $bk)
                        <div style="background: #f8fafc; border: 1px solid var(--line); border-radius: 10px; padding: 14px; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <img src="{{ $bk->cover_url }}" alt="{{ $bk->title }}" style="width: 100%; height: 140px; object-fit: cover; border-radius: 6px; margin-bottom: 10px;">
                                <span class="badge badge-secondary" style="font-size: 0.72rem;">{{ $bk->category_name }}</span>
                                <h4 style="font-size: 0.95rem; margin: 6px 0 4px; color: #0f172a;">{{ Str::limit($bk->title, 32) }}</h4>
                                <p class="muted" style="font-size: 0.82rem; margin: 0 0 10px;">{{ $bk->author }} ({{ $bk->published_year }})</p>
                            </div>
                            <div style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--line); padding-top: 10px;">
                                <span class="badge badge-success" style="font-size: 0.75rem;">Stok: {{ $bk->stock }}</span>
                                <a href="{{ route('peminjam.books.show', $bk) }}" class="button button-primary button-sm">
                                    Pinjam &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
