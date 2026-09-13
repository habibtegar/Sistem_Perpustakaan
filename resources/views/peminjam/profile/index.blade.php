@extends('layouts.app')

@section('title', 'Profil Saya - Sistem Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Profil Peminjam</h1>
            <p class="subtitle">Kelola informasi data keanggotaan dan keamanan akun Anda.</p>
        </div>
        <a class="button button-secondary" href="{{ route('peminjam.dashboard') }}">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="form-card">
        @if ($errors->any())
            <div class="alert alert-error">
                Periksa kembali isian formulir di bawah ini.
            </div>
        @endif

        <div style="background: #f8fafc; border: 1px solid var(--line); border-radius: 10px; padding: 18px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <span class="badge badge-primary" style="margin-bottom: 6px;">Status: {{ $member->status ?? 'Aktif' }}</span>
                <h3 style="margin: 0; color: #0f172a;">{{ $user->name }}</h3>
                <span class="muted" style="font-size: 0.88rem;">Username: <strong>{{ $user->username }}</strong></span>
            </div>
            <div style="text-align: right;">
                <span class="muted" style="font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">ID Anggota</span>
                <div style="font-size: 1.15rem; font-weight: 700; color: var(--primary);">
                    {{ $member->member_code ?? 'MBR-0000' }}
                </div>
            </div>
        </div>

        <form action="{{ route('peminjam.profile.update') }}" method="POST">
            @csrf

            <h4 style="margin: 0 0 16px; color: #0f172a; border-bottom: 1px solid var(--line); padding-bottom: 6px;">
                👤 Informasi Data Diri
            </h4>

            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required>
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email <span style="color: var(--danger);">*</span></label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                    @error('email') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="class">Kelas / Jurusan</label>
                    <input id="class" name="class" type="text" value="{{ old('class', $member->class ?? '') }}" placeholder="Contoh: XII IPA 1">
                    @error('class') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Nomor HP / WhatsApp</label>
                    <input id="phone" name="phone" type="text" value="{{ old('phone', $member->phone ?? '') }}" placeholder="Contoh: 081234567890">
                    @error('phone') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <h4 style="margin: 24px 0 16px; color: #0f172a; border-bottom: 1px solid var(--line); padding-bottom: 6px;">
                🔒 Keamanan & Ganti Password
            </h4>

            <div class="form-group">
                <label for="current_password">Password Saat Ini</label>
                <input id="current_password" name="current_password" type="password" placeholder="Hanya diisi jika ingin mengganti password">
                @error('current_password') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="new_password">Password Baru</label>
                    <input id="new_password" name="new_password" type="password" placeholder="Minimal 6 karakter">
                    @error('new_password') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                    <input id="new_password_confirmation" name="new_password_confirmation" type="password" placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <button type="submit" class="button button-primary">💾 Simpan Perubahan Profil</button>
                <a href="{{ route('peminjam.dashboard') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
