<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Manajemen Perpustakaan</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    @vite(['resources/css/style.css'])
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-container {
            width: 100%;
            max-width: 440px;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-icon {
            font-size: 3rem;
            margin-bottom: 8px;
            display: inline-block;
        }
        .auth-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .auth-subtitle {
            color: #64748b;
            font-size: 0.9rem;
        }
        .auth-demo-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 12px;
            margin-top: 24px;
            font-size: 0.82rem;
            color: #475569;
        }
        .auth-demo-row {
            display: flex;
            justify-content: space-between;
            margin-top: 4px;
        }
        .auth-demo-badge {
            cursor: pointer;
            padding: 2px 6px;
            background: #e2e8f0;
            border-radius: 4px;
            font-weight: 600;
            transition: background 0.15s ease;
        }
        .auth-demo-badge:hover {
            background: #cbd5e1;
        }
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.88rem;
            color: #94a3b8;
        }
        .auth-footer a {
            color: #60a5fa;
            font-weight: 600;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <span class="auth-icon">📖</span>
                <h1 class="auth-title">Masuk ke Perpustakaan</h1>
                <p class="auth-subtitle">Gunakan akun Admin atau Peminjam Anda</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success" style="margin-bottom: 20px;">
                    <span>{{ session('success') }}</span>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error" style="margin-bottom: 20px;">
                    <span>{{ session('error') }}</span>
                    <button type="button" class="alert-close" onclick="this.parentElement.remove();">&times;</button>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="login">Username atau Email</label>
                    <input
                        id="login"
                        name="login"
                        type="text"
                        value="{{ old('login') }}"
                        placeholder="Contoh: admin atau nama@email.com"
                        required
                        autofocus
                    >
                    @error('login') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Masukkan password Anda"
                        required
                    >
                    @error('password') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; font-size: 0.88rem;">
                    <label style="display: flex; align-items: center; gap: 6px; font-weight: normal; cursor: pointer; margin: 0;">
                        <input type="checkbox" name="remember" style="width: auto;"> Ingat Saya
                    </label>
                </div>

                <button type="submit" class="button button-primary" style="width: 100%; padding: 12px; font-size: 1rem;">
                    Masuk Sekarang &rarr;
                </button>
            </form>


            <div style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #64748b;">
                Belum punya akun peminjam?
                <a href="{{ route('register') }}" style="color: var(--primary); font-weight: 600;">Daftar di sini</a>
            </div>
        </div>

        <div class="auth-footer">
            &copy; {{ date('Y') }} Sistem Manajemen Perpustakaan. Hak Cipta Dilindungi.
        </div>
    </div>

    <script>
        function fillCredentials(login, pass) {
            document.getElementById('login').value = login;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>
