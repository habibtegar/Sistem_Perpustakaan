@extends('layouts.app')

@section('title', 'Pengaturan Perpustakaan - Administrator')

@section('content')
    <div class="page-header">
        <div>
            <h1>Pengaturan Perpustakaan</h1>
            <p class="subtitle">Konfigurasi tarif denda keterlambatan, batas waktu peminjaman, dan profil perpustakaan.</p>
        </div>
        <a class="button button-secondary" href="{{ route('admin.dashboard') }}">&larr; Kembali ke Dashboard</a>
    </div>

    <div class="form-card">
        @if ($errors->any())
            <div class="alert alert-error">
                Periksa kembali data formulir pengaturan yang Anda masukkan.
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf

            <h3 style="margin-bottom: 16px; font-size: 1.1rem; color: #0f172a; padding-bottom: 8px; border-bottom: 1px solid var(--line);">
                💰 Kebijakan Denda & Peminjaman
            </h3>

            <div class="form-grid">
                <div class="form-group">
                    <label for="fine_per_day">Tarif Denda per Hari (Rupiah) <span style="color: var(--danger);">*</span></label>
                    <input
                        id="fine_per_day"
                        name="fine_per_day"
                        type="number"
                        min="0"
                        step="100"
                        value="{{ old('fine_per_day', $finePerDay) }}"
                        required
                    >
                    <p class="helper-text">Jumlah denda per eksemplar per hari setelah melewati tanggal jatuh tempo.</p>
                    @error('fine_per_day') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="default_loan_days">Durasi Standar Peminjaman (Hari) <span style="color: var(--danger);">*</span></label>
                    <input
                        id="default_loan_days"
                        name="default_loan_days"
                        type="number"
                        min="1"
                        max="30"
                        value="{{ old('default_loan_days', $defaultLoanDays) }}"
                        required
                    >
                    <p class="helper-text">Batas durasi pinjam otomatis yang dihitung sistem saat peminjaman dibuat.</p>
                    @error('default_loan_days') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <h3 style="margin: 28px 0 16px; font-size: 1.1rem; color: #0f172a; padding-bottom: 8px; border-bottom: 1px solid var(--line);">
                🏫 Informasi Perpustakaan
            </h3>

            <div class="form-group">
                <label for="library_name">Nama Perpustakaan / Institusi <span style="color: var(--danger);">*</span></label>
                <input
                    id="library_name"
                    name="library_name"
                    type="text"
                    value="{{ old('library_name', $libraryName) }}"
                    required
                >
                @error('library_name') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="library_address">Alamat / Lokasi Perpustakaan</label>
                <textarea id="library_address" name="library_address" placeholder="Masukkan alamat lengkap perpustakaan">{{ old('library_address', $libraryAddress) }}</textarea>
                @error('library_address') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">💾 Simpan Pengaturan</button>
                <a href="{{ route('admin.dashboard') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
