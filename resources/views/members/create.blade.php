@extends('layouts.app')

@section('title', 'Tambah Anggota - Sistem Manajemen Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Tambah Anggota Baru</h1>
            <p class="subtitle">Daftarkan siswa atau peminjam baru ke dalam sistem perpustakaan.</p>
        </div>
        <a class="button button-secondary" href="{{ route('members.index') }}">&larr; Kembali</a>
    </div>

    <div class="form-card">
        @if ($errors->any())
            <div class="alert alert-error">
                Periksa kembali data formulir yang Anda masukkan.
            </div>
        @endif

        <form action="{{ route('members.store') }}" method="POST">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="member_code">ID / Nomor Anggota <span style="color: var(--danger);">*</span></label>
                    <input
                        id="member_code"
                        name="member_code"
                        type="text"
                        value="{{ old('member_code', $suggestedCode ?? '') }}"
                        placeholder="Contoh: MBR-0001"
                        required
                    >
                    @error('member_code') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="name">Nama Lengkap <span style="color: var(--danger);">*</span></label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap anggota"
                        required
                    >
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="class">Kelas / Jurusan</label>
                    <input
                        id="class"
                        name="class"
                        type="text"
                        value="{{ old('class') }}"
                        placeholder="Contoh: XII IPA 2 / Guru / Staf"
                    >
                    @error('class') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status Keanggotaan <span style="color: var(--danger);">*</span></label>
                    <select id="status" name="status" required>
                        <option value="Aktif" {{ old('status', 'Aktif') === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ old('status') === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="phone">Nomor HP / WhatsApp</label>
                    <input
                        id="phone"
                        name="phone"
                        type="text"
                        value="{{ old('phone') }}"
                        placeholder="Contoh: 081234567890"
                    >
                    @error('phone') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="Contoh: nama@domain.com"
                    >
                    @error('email') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Simpan Anggota</button>
                <a href="{{ route('members.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
