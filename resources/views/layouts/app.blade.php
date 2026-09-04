<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Manajemen Perpustakaan')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    @vite(['resources/css/style.css'])
</head>
<body>
    @auth
        @php
            $user = auth()->user();
            $isAdmin = $user->role === 'admin';
        @endphp
        <nav class="navbar">
            <div class="nav-inner">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <a class="brand" href="{{ $isAdmin ? route('admin.dashboard') : route('peminjam.dashboard') }}">
                        📖 Perpustakaan
                    </a>
                    <span class="badge {{ $isAdmin ? 'badge-danger' : 'badge-primary' }}" style="font-size: 0.72rem; letter-spacing: 0.05em; text-transform: uppercase;">
                        {{ $isAdmin ? '🛡️ ADMIN' : '👤 PEMINJAM' }}
                    </span>
                </div>

                <ul class="nav-menu">
                    @if ($isAdmin)
                        <!-- Navigasi Khusus Admin -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                📊 Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.books.index') }}" class="nav-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                                📚 Buku
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                🏷️ Kategori
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.members.index') }}" class="nav-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                                👥 Anggota
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.borrowings.index') }}" class="nav-link {{ request()->routeIs('admin.borrowings.*') ? 'active' : '' }}">
                                📤 Peminjaman
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.returns.index') }}" class="nav-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                                📥 Pengembalian
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.transactions.index') }}" class="nav-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                                📋 Riwayat
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.reports.index') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                📑 Laporan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                ⚙️ Pengaturan
                            </a>
                        </li>
                    @else
                        <!-- Navigasi Khusus Peminjam -->
                        <li>
                            <a href="{{ route('peminjam.dashboard') }}" class="nav-link {{ request()->routeIs('peminjam.dashboard') ? 'active' : '' }}">
                                🏠 Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.books.index') }}" class="nav-link {{ request()->routeIs('peminjam.books.*') ? 'active' : '' }}">
                                📚 Daftar Buku
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.my-borrowings') }}" class="nav-link {{ request()->routeIs('peminjam.my-borrowings') ? 'active' : '' }}">
                                📖 Peminjaman Saya
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.history') }}" class="nav-link {{ request()->routeIs('peminjam.history') ? 'active' : '' }}">
                                📋 Riwayat Saya
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.profile') }}" class="nav-link {{ request()->routeIs('peminjam.profile') ? 'active' : '' }}">
                                👤 Profil
                            </a>
                        </li>
                    @endif

                    <!-- Logout Button -->
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                            @csrf
                            <button type="submit" class="nav-link" style="background: transparent; border: none; cursor: pointer; color: #f87171;">
                                🚪 Keluar
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
    @endauth

    <main class="container">
        @if (session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                <span>{{ session('error') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
