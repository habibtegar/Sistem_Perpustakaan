<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>@yield('title', 'Sistem Manajemen Perpustakaan')</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    @vite(['resources/css/style.css'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
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
                    <span class="brand-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                    </span>
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
                    @if($isAdmin)
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:middle;margin-right:4px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        ADMINISTRATOR
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:middle;margin-right:4px"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                        PEMINJAM
                    @endif
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
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg></span>
                                <span class="nav-label">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.books.index') }}" class="sidebar-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
                                <span class="nav-label">Kelola Buku</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg></span>
                                <span class="nav-label">Kategori Buku</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.members.index') }}" class="sidebar-link {{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                                <span class="nav-label">Anggota Perpustakaan</span>
                            </a>
                        </li>

                        <li class="menu-category">Sirkulasi & Transaksi</li>
                        <li>
                            <a href="{{ route('admin.borrowings.index') }}" class="sidebar-link {{ request()->routeIs('admin.borrowings.*') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/><path d="M9 10h6"/><path d="M12 7v6"/></svg></span>
                                <span class="nav-label">Peminjaman</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.returns.index') }}" class="sidebar-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/><path d="m9 13 3 3 3-3"/><path d="M12 9v7"/></svg></span>
                                <span class="nav-label">Pengembalian</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.transactions.index') }}" class="sidebar-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg></span>
                                <span class="nav-label">Riwayat Transaksi</span>
                            </a>
                        </li>

                        <li class="menu-category">Laporan & Sistem</li>
                        <li>
                            <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 13h2"/><path d="M8 17h2"/><path d="M14 13h2"/><path d="M14 17h2"/></svg></span>
                                <span class="nav-label">Laporan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg></span>
                                <span class="nav-label">Pengaturan</span>
                            </a>
                        </li>
                    @else
                        <!-- Menu Peminjam -->
                        <li class="menu-category">Aktivitas Saya</li>
                        <li>
                            <a href="{{ route('peminjam.dashboard') }}" class="sidebar-link {{ request()->routeIs('peminjam.dashboard') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                                <span class="nav-label">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.books.index') }}" class="sidebar-link {{ request()->routeIs('peminjam.books.*') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg></span>
                                <span class="nav-label">Daftar Buku</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.my-borrowings') }}" class="sidebar-link {{ request()->routeIs('peminjam.my-borrowings') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/><path d="m9 9.5 3 3 3-3"/></svg></span>
                                <span class="nav-label">Peminjaman Saya</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.history') }}" class="sidebar-link {{ request()->routeIs('peminjam.history') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg></span>
                                <span class="nav-label">Riwayat Saya</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('peminjam.profile') }}" class="sidebar-link {{ request()->routeIs('peminjam.profile') ? 'active' : '' }}">
                                <span class="nav-icon"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg></span>
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span>Logout</span>
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
                        @if($isAdmin)
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:middle;margin-right:3px"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            Admin
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:middle;margin-right:3px"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                            Peminjam
                        @endif
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
