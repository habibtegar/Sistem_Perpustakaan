<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peminjam - Sistem Manajemen Perpustakaan</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logo.svg') }}">
    @vite(['resources/css/style.css'])
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 20px;
        }
        .auth-container {
            width: 100%;
            max-width: 500px;
        }
        .auth-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }
        .auth-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .auth-icon {
            font-size: 2.8rem;
            margin-bottom: 6px;
            display: inline-block;
        }
        .auth-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .auth-subtitle {
            color: #64748b;
            font-size: 0.88rem;
        }
        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.88rem;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <span class="auth-icon">📝</span>
                <h1 class="auth-title">Daftar Akun Peminjam</h1>
                <p class="auth-subtitle">Daftarkan diri Anda untuk meminjam buku perpustakaan</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-error" style="margin-bottom: 20px;">
                    Periksa kembali isian formulir pendaftaran di bawah ini.
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Budi Pratama" required autofocus>
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="username">Username <span style="color: var(--danger);">*</span></label>
                        <input id="username" name="username" type="text" value="{{ old('username') }}" placeholder="Contoh: budipratama" required>
                        @error('username') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span style="color: var(--danger);">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Contoh: budi@email.com" required>
                        @error('email') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="class">Kelas / Jurusan</label>
                        <input id="class" name="class" type="text" value="{{ old('class') }}" placeholder="Contoh: XII IPA 1">
                        @error('class') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">No. HP / WA</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}" placeholder="Contoh: 081234567890">
                        @error('phone') <p class="error">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="password">Password <span style="color: var(--danger);">*</span></label>
                        <input id="password" name="password" type="password" placeholder="Minimal 6 karakter" required>
                        @error('password') <p class="error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password <span style="color: var(--danger);">*</span></label>
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password" required>
                    </div>
                </div>

                <button type="submit" class="button button-primary" style="width: 100%; padding: 12px; font-size: 1rem; margin-top: 10px;">
                    Daftar Sekarang &rarr;
                </button>
            </form>

            <div style="text-align: center; margin-top: 20px; font-size: 0.9rem; color: #64748b;">
                Sudah punya akun?
                <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600;">Masuk di sini</a>
            </div>
        </div>

        <div class="auth-footer">
            &copy; {{ date('Y') }} Sistem Manajemen Perpustakaan
        </div>
    </div>
</body>
</html>
