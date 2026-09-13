@extends('layouts.app')

@section('title', 'Peminjaman Langsung - Administrator')

@section('content')
    <div class="page-header">
        <div>
            <h1>Form Peminjaman Langsung</h1>
            <p class="subtitle">Catat peminjaman buku secara langsung di meja perpustakaan.</p>
        </div>
        <a class="button button-secondary" href="{{ route('admin.borrowings.index') }}">&larr; Kembali</a>
    </div>

    <div class="form-card">
        @if ($errors->any())
            <div class="alert alert-error">
                Periksa kembali data formulir peminjaman berikut.
            </div>
        @endif

        <form action="{{ route('admin.borrowings.store') }}" method="POST" id="borrowForm">
            @csrf

            <div class="form-group">
                <label for="transaction_code">Nomor Transaksi <span style="color: var(--danger);">*</span></label>
                <input
                    id="transaction_code"
                    name="transaction_code"
                    type="text"
                    value="{{ old('transaction_code', $suggestedCode ?? '') }}"
                    required
                >
                @error('transaction_code') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="member_id">Pilih Anggota Peminjam <span style="color: var(--danger);">*</span></label>
                @if($members->isEmpty())
                    <p class="error">Belum ada anggota aktif terdaftar. Silakan <a href="{{ route('admin.members.create') }}" style="text-decoration: underline;">tambah anggota</a> terlebih dahulu.</p>
                @else
                    <select id="member_id" name="member_id" required>
                        <option value="">-- Pilih Anggota --</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                [{{ $member->member_code }}] {{ $member->name }} {{ $member->class ? ' - ' . $member->class : '' }}
                            </option>
                        @endforeach
                    </select>
                @endif
                @error('member_id') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-group">
                <label for="book_id">Pilih Buku <span style="color: var(--danger);">*</span></label>
                @if($allBooks->isEmpty())
                    <p class="error">Belum ada buku di perpustakaan. Silakan <a href="{{ route('admin.books.create') }}" style="text-decoration: underline;">tambah buku</a> terlebih dahulu.</p>
                @else
                    <select id="book_id" name="book_id" required onchange="checkBookStock(this)">
                        <option value="">-- Pilih Buku --</option>
                        @foreach ($allBooks as $book)
                            <option
                                value="{{ $book->id }}"
                                data-stock="{{ $book->stock }}"
                                {{ old('book_id') == $book->id ? 'selected' : '' }}
                                {{ $book->stock <= 0 ? 'disabled' : '' }}
                            >
                                {{ $book->title }} (Penulis: {{ $book->author }}) - [Stok: {{ $book->stock }}] {{ $book->stock <= 0 ? ' - STOK HABIS' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <div id="stockBadge" style="margin-top: 8px;"></div>
                @endif
                @error('book_id') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label for="borrow_date">Tanggal Peminjaman <span style="color: var(--danger);">*</span></label>
                    <input
                        id="borrow_date"
                        name="borrow_date"
                        type="date"
                        value="{{ old('borrow_date', $defaultBorrowDate) }}"
                        required
                        onchange="updateDueDate(this.value)"
                    >
                    @error('borrow_date') <p class="error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="due_date">Tanggal Jatuh Tempo <span style="color: var(--danger);">*</span></label>
                    <input
                        id="due_date"
                        name="due_date"
                        type="date"
                        value="{{ old('due_date', $defaultDueDate) }}"
                        required
                    >
                    <p class="helper-text">Standar peminjaman saat ini adalah {{ \App\Models\Setting::get('default_loan_days', 7) }} hari.</p>
                    @error('due_date') <p class="error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Catatan Tambahan <span class="muted">(opsional)</span></label>
                <textarea id="notes" name="notes" placeholder="Catatan kondisi buku atau identitas jaminan...">{{ old('notes') }}</textarea>
                @error('notes') <p class="error">{{ $message }}</p> @enderror
            </div>

            <div class="form-actions">
                <button type="submit" id="btnSubmit" class="button button-primary">📤 Catat Peminjaman</button>
                <a href="{{ route('admin.borrowings.index') }}" class="button button-secondary">Batal</a>
            </div>
        </form>
    </div>

    <script>
        function checkBookStock(select) {
            const selectedOption = select.options[select.selectedIndex];
            const stock = selectedOption.getAttribute('data-stock');
            const badge = document.getElementById('stockBadge');
            const submitBtn = document.getElementById('btnSubmit');

            if (!selectedOption.value) {
                badge.innerHTML = '';
                submitBtn.disabled = false;
                return;
            }

            if (stock && parseInt(stock) > 0) {
                badge.innerHTML = '<span class="badge badge-success">✓ Tersedia ' + stock + ' eksemplar</span>';
                submitBtn.disabled = false;
            } else {
                badge.innerHTML = '<span class="badge badge-danger">✗ Stok habis! Buku tidak dapat dipinjam.</span>';
                submitBtn.disabled = true;
            }
        }

        function updateDueDate(borrowDateStr) {
            if (!borrowDateStr) return;
            const borrowDate = new Date(borrowDateStr);
            const loanDays = {{ (int) \App\Models\Setting::get('default_loan_days', 7) }};
            borrowDate.setDate(borrowDate.getDate() + loanDays);
            
            const yyyy = borrowDate.getFullYear();
            const mm = String(borrowDate.getMonth() + 1).padStart(2, '0');
            const dd = String(borrowDate.getDate()).padStart(2, '0');
            
            document.getElementById('due_date').value = `${yyyy}-${mm}-${dd}`;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const bookSelect = document.getElementById('book_id');
            if (bookSelect) {
                checkBookStock(bookSelect);
            }
        });
    </script>
@endsection
