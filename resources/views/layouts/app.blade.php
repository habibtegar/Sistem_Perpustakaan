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
    <nav class="navbar">
        <div class="nav-inner">
            <a class="brand" href="{{ route('dashboard') }}">
                📖 Sistem Perpustakaan
            </a>
            <ul class="nav-menu">
                <li>
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        📊 Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('books.index') }}" class="nav-link {{ request()->routeIs('books.*') ? 'active' : '' }}">
                        📚 Buku
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        🏷️ Kategori
                    </a>
                </li>
                <li>
                    <a href="{{ route('members.index') }}" class="nav-link {{ request()->routeIs('members.*') ? 'active' : '' }}">
                        👥 Anggota
                    </a>
                </li>
                <li>
                    <a href="{{ route('borrowings.index') }}" class="nav-link {{ request()->routeIs('borrowings.*') ? 'active' : '' }}">
                        📤 Peminjaman
                    </a>
                </li>
                <li>
                    <a href="{{ route('returns.index') }}" class="nav-link {{ request()->routeIs('returns.*') ? 'active' : '' }}">
                        📥 Pengembalian
                    </a>
                </li>
                <li>
                    <a href="{{ route('transactions.index') }}" class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}">
                        📋 Riwayat
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        📑 Laporan
                    </a>
                </li>
            </ul>
        </div>
    </nav>

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
