<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>@yield('title', 'Sistem Manajemen Perpustakaan')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    @vite(['resources/css/style.css'])
</head>
<body class="{{ auth()->check() ? 'has-sidebar' : 'auth-page' }}">
    @auth
        @php
            $user = auth()->user();
            $isAdmin = $user->role === 'admin';
        @endphp

        <!-- Mobile Sidebar Overlay Backdrop -->
        <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

        <!-- Sidebar (Desktop Fixed & Mobile Off-canvas Drawer) -->
        <aside class="app-sidebar" id="appSidebar">
            <div class="sidebar-header">
                <a class="sidebar-brand" href="{{ $isAdmin ? route('admin.dashboard') : route('peminjam.dashboard') }}">
                    <span class="brand-icon">📖</span>
                    <div class="brand-text">
                        <span class="brand-title">Perpustakaan</span>
                        <span class="brand-subtitle">{{ $isAdmin ? 'Panel Admin' : 'Portal Peminjam' }}</span>
                    </div>
                </a>
                <button type="button" class="sidebar-close-btn" onclick="closeSidebar()" aria-label="Tutup Menu">
                    &times;
                </button>
            </div>

            <!-- Role Badge Banner -->
            <div class="sidebar-role-badge">
                <span class="badge {{ $isAdmin ? 'badge-danger' : 'badge-primary' }}">
                    {{ $isAdmin ? '🛡️ ADMINISTRATOR' : '👤 PEMINJAM' }}
                </span>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="sidebar-nav">
                <ul class="sidebar-menu">
                    @if ($isAdmin)
                        <!-- Menu Admin -->
                        <li class="menu-category">Menu Utama</li>
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <span class="nav-icon">📊</span>
                                <span class="nav-label">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.books.index') }}" class="sidebar-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                                <span class="nav-icon">📚</span>
                                <span class="nav-label">Kelola Buku</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <span class="nav-icon">🏷️</span>
                                <span class="nav-label">Kategori Buku</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.members.index') }}" class="sidebar-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                                <span class="nav-icon">👥</span>
                                <span class="nav-label">Anggota Perpustakaan</span>
                            </a>
                        </li>

                        <li class="menu-category">Sirkulasi & Transaksi</li>
                        <li>
                            <a href="{{ route('admin.borrowings.index') }}" class="sidebar-link {{ request()->routeIs('admin.borrowings.*') ? 'active' : '' }}">
                                <span class="nav-icon">📤</span>
                                <span class="nav-label">Peminjaman</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.returns.index') }}" class="sidebar-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                                <span class="nav-icon">📥</span>
                                <span class="nav-label">Pengembalian</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.transactions.index') }}" class="sidebar-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                                <span class="nav-icon">📋</span>
                                <span class="nav-label">Riwayat Transaksi</span>
                            </a>
                        </li>

                        <li class="menu-category">Laporan & Sistem</li>
                        <li>
                            <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <span class="nav-icon">📑</span>
                                <span class="nav-label">Laporan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <span class="nav-icon">⚙️</span>
                                <span class="nav-label">Pengaturan</span>
                            </a>
                        </li>
                    @else
                        <!-- Menu Peminjam -->
                        <li class="menu-category">Aktivitas Saya</li>
                        <li>
                            <a href="{{ route('peminjam.dashboard') }}" class="sidebar-link {{ request()->routeIs('peminjam.dashboard') ? 'active' : '' }}">
                                <span class="nav-icon">🏠</span>
                                <span class="nav-label">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.books.index') }}" class="sidebar-link {{ request()->routeIs('peminjam.books.*') ? 'active' : '' }}">
                                <span class="nav-icon">📚</span>
                                <span class="nav-label">Daftar Buku</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.my-borrowings') }}" class="sidebar-link {{ request()->routeIs('peminjam.my-borrowings') ? 'active' : '' }}">
                                <span class="nav-icon">📖</span>
                                <span class="nav-label">Peminjaman Saya</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.history') }}" class="sidebar-link {{ request()->routeIs('peminjam.history') ? 'active' : '' }}">
                                <span class="nav-icon">📋</span>
                                <span class="nav-label">Riwayat Saya</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.profile') }}" class="sidebar-link {{ request()->routeIs('peminjam.profile') ? 'active' : '' }}">
                                <span class="nav-icon">👤</span>
                                <span class="nav-label">Profil Saya</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </nav>

            <!-- Sidebar User Profile & Logout Footer -->
            <div class="sidebar-footer">
                <div class="user-info-box">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="user-meta">
                        <span class="user-name" title="{{ $user->name }}">{{ $user->name }}</span>
                        <span class="user-role">{{ $isAdmin ? 'Administrator' : ($user->member->member_code ?? 'Peminjam') }}</span>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                    @csrf
                    <button type="submit" class="logout-btn" title="Keluar">
                        🚪 <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Wrapper (Topbar + Content Area) -->
        <div class="app-layout">
            <!-- Topbar Header -->
            <header class="app-topbar">
                <div class="topbar-left">
                    <button type="button" class="hamburger-btn" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Buka Menu">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <div class="topbar-page-info">
                        <span class="topbar-title">@yield('title', 'Sistem Perpustakaan')</span>
                    </div>
                </div>

                <div class="topbar-right">
                    <span class="badge {{ $isAdmin ? 'badge-danger' : 'badge-primary' }} topbar-role-badge">
                        {{ $isAdmin ? '🛡️ Admin' : '👤 Peminjam' }}
                    </span>
                    <div class="topbar-user">
                        <div class="topbar-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="topbar-username">{{ Str::limit($user->name, 16) }}</span>
                    </div>
                </div>
            </header>

            <!-- Main Page Content -->
            <main class="app-main-content">
                <div class="content-container">
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
                </div>
            </main>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('appSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                document.body.classList.toggle('sidebar-open');
            }

            function closeSidebar() {
                const sidebar = document.getElementById('appSidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar) sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }

            // Tutup sidebar otomatis saat menekan tombol ESC
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });

            // Tutup sidebar jika layar di-resize ke desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1024) {
                    closeSidebar();
                }
            });
        </script>
    @else
        <!-- Layout Guest / Auth Halaman Login & Register -->
        <main class="guest-container">
            @yield('content')
        </main>
    @endauth
</body>
</html>
