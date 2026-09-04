@extends('layouts.app')

@section('title', 'Detail Anggota - ' . $member->name)

@section('content')
    <div class="page-header">
        <div>
            <h1>Detail Anggota</h1>
            <p class="subtitle">Profil anggota, akun pengguna, dan riwayat sirkulasi peminjaman buku.</p>
        </div>
        <div class="page-header-actions">
            <a class="button button-secondary" href="{{ route('admin.members.index') }}">&larr; Kembali</a>
            <a class="button button-edit" href="{{ route('admin.members.edit', $member) }}">Edit Data</a>
        </div>
    </div>

    <div class="detail-card">
        <div class="detail-card-header">
            <div>
                <span class="badge {{ $member->status === 'Aktif' ? 'badge-success' : 'badge-danger' }}">
                    Status: {{ $member->status }}
                </span>
                <h2 class="detail-title">{{ $member->name }}</h2>
                <span class="muted">Kode Anggota: <code>{{ $member->member_code }}</code></span>
            </div>
            <div>
                @if($member->user)
                    <span class="badge badge-primary">Username: {{ $member->user->username }}</span>
                @else
                    <span class="badge badge-secondary">Belum ada akun login</span>
                @endif
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Kelas / Jurusan</span>
                <span class="detail-value">{{ $member->class ?: '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">No. Telepon / WA</span>
                <span class="detail-value">{{ $member->phone ?: '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Email Akun</span>
                <span class="detail-value">{{ $member->email ?: '-' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Terdaftar Sejak</span>
                <span class="detail-value">{{ $member->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        <!-- Riwayat Transaksi Anggota -->
        <h3 style="margin: 24px 0 12px; font-size: 1.1rem; color: #0f172a;">Riwayat Peminjaman Buku</h3>
        @if ($member->transactions->isEmpty())
            <div class="empty-state" style="padding: 24px 0; background: #f8fafc; border-radius: 8px;">
                <p class="muted">Anggota ini belum pernah meminjam buku.</p>
            </div>
        @else
            <div class="table-wrap" style="box-shadow: none;">
                <table>
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Jatuh Tempo</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Denda</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($member->transactions as $trx)
                            <tr>
                                <td><code>{{ $trx->transaction_code }}</code></td>
                                <td><strong>{{ $trx->book->title ?? '-' }}</strong></td>
                                <td>{{ \Carbon\Carbon::parse($trx->borrow_date)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($trx->due_date)->format('d/m/Y') }}</td>
                                <td>
                                    {{ $trx->return_date ? \Carbon\Carbon::parse($trx->return_date)->format('d/m/Y') : '-' }}
                                </td>
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
                                    @elseif($stat === 'Akan Jatuh Tempo')
                                        <span class="badge badge-warning">Akan Tempo</span>
                                    @else
                                        <span class="badge badge-primary">Dipinjam</span>
                                    @endif
                                </td>
                                <td>
                                    @php $finalFine = $trx->status === 'Dikembalikan' ? $trx->fine_amount : $trx->current_fine; @endphp
                                    @if($finalFine > 0)
                                        <span style="color: #dc2626; font-weight: 600;">
                                            Rp {{ number_format($finalFine, 0, ',', '.') }}
                                        </span>
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
@endsection
