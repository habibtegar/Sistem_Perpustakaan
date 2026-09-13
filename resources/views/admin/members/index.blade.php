@extends('layouts.app')

@section('title', 'Kelola Anggota - Administrator')

@section('content')
    <div class="page-header">
        <div>
            <h1>Manajemen Anggota Perpustakaan</h1>
            <p class="subtitle">Kelola data keanggotaan dan akun login peminjam perpustakaan.</p>
        </div>
        <a class="button button-primary" href="{{ route('admin.members.create') }}">+ Tambah Anggota</a>
    </div>

    <!-- Statistik Singkat Anggota -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">👥</div>
            <div class="stat-info">
                <span class="stat-label">Total Anggota</span>
                <strong class="stat-value">{{ $totalMembers }}</strong>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">✅</div>
            <div class="stat-info">
                <span class="stat-label">Anggota Aktif</span>
                <strong class="stat-value">{{ $activeMembers }}</strong>
            </div>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="filter-card">
        <form action="{{ route('admin.members.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search">
                <label for="search">Cari Anggota</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Cari nama, ID anggota, username, kelas, no HP..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-group filter-select">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">Semua Status</option>
                    <option value="Aktif" {{ request('status') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="Nonaktif" {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari & Filter</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.members.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Anggota -->
    <div class="table-wrap">
        @if ($members->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">👥</div>
                @if (request('search') || request('status'))
                    <h3>Anggota tidak ditemukan.</h3>
                    <p class="muted">Coba gunakan kata kunci lain atau ubah filter status.</p>
                    <a href="{{ route('admin.members.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset Filter</a>
                @else
                    <h3>Belum ada data anggota.</h3>
                    <p class="muted">Daftarkan anggota baru agar dapat meminjam buku secara mandiri.</p>
                    <a href="{{ route('admin.members.create') }}" class="button button-primary" style="margin-top: 12px;">+ Tambah Anggota Pertama</a>
                @endif
            </div>
        @else
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>ID Anggota</th>
                        <th>Nama & Username</th>
                        <th>Kelas / Jurusan</th>
                        <th>Kontak (HP / Email)</th>
                        <th>Status</th>
                        <th>Pinjaman Aktif</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($members as $index => $member)
                        <tr>
                            <td>{{ $members->firstItem() + $loop->index }}</td>
                            <td>
                                <code>{{ $member->member_code }}</code>
                            </td>
                            <td>
                                <strong>{{ $member->name }}</strong><br>
                                <small class="muted">@if($member->user) 👤 {{ $member->user->username }} @else <em>Tanpa Akun</em> @endif</small>
                            </td>
                            <td>{{ $member->class ?: '-' }}</td>
                            <td>
                                <div>{{ $member->phone ?: '-' }}</div>
                                @if($member->email)
                                    <small class="muted">{{ $member->email }}</small>
                                @endif
                            </td>
                            <td>
                                @if($member->status === 'Aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                @if($member->active_borrowings_count > 0)
                                    <span class="badge badge-warning">{{ $member->active_borrowings_count }} buku</span>
                                @else
                                    <span class="badge badge-secondary">0 buku</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions">
                                    <a class="button button-info" href="{{ route('admin.members.show', $member) }}">Detail</a>
                                    <a class="button button-edit" href="{{ route('admin.members.edit', $member) }}">Edit</a>
                                    <form action="{{ route('admin.members.destroy', $member) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini beserta akun loginnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="button button-danger" type="submit">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <!-- Pagination -->
    @if ($members->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-custom">
                @if ($members->onFirstPage())
                    <span class="page-button disabled">&laquo; Sebelumnya</span>
                @else
                    <a href="{{ $members->previousPageUrl() }}" class="page-button">&laquo; Sebelumnya</a>
                @endif

                <span class="page-info">Halaman <strong>{{ $members->currentPage() }}</strong> dari <strong>{{ $members->lastPage() }}</strong></span>

                @if ($members->hasMorePages())
                    <a href="{{ $members->nextPageUrl() }}" class="page-button">Selanjutnya &raquo;</a>
                @else
                    <span class="page-button disabled">Selanjutnya &raquo;</span>
                @endif
            </div>
        </div>
    @endif
@endsection
