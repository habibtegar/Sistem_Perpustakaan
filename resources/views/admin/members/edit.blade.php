@extends('layouts.app')

@section('title', 'Edit Anggota - Administrator')

@section('content')
    <div class="page-header">
        <div>
            <h1>Edit Data Anggota</h1>
            <p class="subtitle">Perbarui data anggota: <strong>{{ $member->name }}</strong> ({{ $member->member_code }})</p>
        </div>
        <a class="button button-secondary" href="{{ route('admin.members.index') }}">&larr; Kembali</a>
    </div>

    <div class="form-card">
        @if ($errors->any())
            <div class="alert alert-error">
                Periksa kembali data formulir yang Anda masukkan.
            </div>
        @endif

        <form action="{{ route('admin.members.update', $member) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group">
                    <label for="member_code">ID / Nomor Anggota <span style="color: var(--danger);">*</span></label>
                    <input
                        id="member_code"
                        name="member_code"
                        type="text"
                        value="{{ old('member_code', $member->member_code) }}"
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
                        value="{{ old('name', $member->name) }}"
                        required
                    >
                    @error('name') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="email">Alamat Email <span style="color: var(--danger);">*</span></label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $member->email) }}"
                        required
                    >
                    @error('email') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="status">Status Keanggotaan <span style="color: var(--danger);">*</span></label>
                    <select id="status" name="status" required>
                        <option value="Aktif" {{ old('status', $member->status) === 'Aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="Nonaktif" {{ old('status', $member->status) === 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="class">Kelas / Jurusan</label>
                    <input
                        id="class"
                        name="class"
                        type="text"
                        value="{{ old('class', $member->class) }}"
                    >
                    @error('class') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Nomor HP / WhatsApp</label>
                    <input
                        id="phone"
                        name="phone"
                        type="text"
                        value="{{ old('phone', $member->phone) }}"
                    >
                    @error('phone') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="password">Ganti Password Akun <span class="muted">(opsional)</span></label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                >
                @error('password') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">Perbarui Data Anggota</button>
                <a href="{{ route('admin.members.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
