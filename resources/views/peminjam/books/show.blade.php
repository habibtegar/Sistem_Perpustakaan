@extends('layouts.app')

@section('title', 'Detail Buku - ' . $book->title)

@section('content')
    <div class="page-header">
        <div>
            <h1>Detail Buku</h1>
            <p class="subtitle">Informasi lengkap dan sinopsis buku koleksi perpustakaan.</p>
        </div>
        <a class="button button-secondary" href="{{ route('peminjam.books.index') }}">&larr; Kembali ke Katalog</a>
    </div>

    <div class="detail-card">
        <div class="detail-card-header">
            <div style="display: flex; gap: 18px; align-items: flex-start;">
                <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" style="width: 80px; height: 115px; object-fit: cover; border-radius: 8px; border: 1px solid var(--line); box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
                <div>
                    <span class="badge badge-secondary">{{ $book->category_name }}</span>
                    <h2 class="detail-title">{{ $book->title }}</h2>
                    <p class="muted" style="margin: 4px 0 0;">Penulis: <strong>{{ $book->author }}</strong></p>
                </div>
            </div>
            <div>
                @if($book->stock > 0)
                    <span class="badge badge-success" style="font-size: 0.9rem; padding: 6px 12px;">
                        ✓ Tersedia ({{ $book->stock }} eksemplar)
                    </span>
                @else
                    <span class="badge badge-danger" style="font-size: 0.9rem; padding: 6px 12px;">
                        ✗ Stok Habis (Tidak Tersedia)
                    </span>
                @endif
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="detail-label">Penulis</span>
                <span class="detail-value">{{ $book->author }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Tahun Terbit</span>
                <span class="detail-value">{{ $book->published_year }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Kategori</span>
                <span class="detail-value">{{ $book->category_name }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Sisa Stok Fisik</span>
                <span class="detail-value">{{ $book->stock }} buku</span>
            </div>
        </div>

        <div class="detail-description-box">
            <h3>Sinopsis & Deskripsi Buku</h3>
            <div class="description">
                {{ $book->description ?: 'Belum ada ringkasan atau sinopsis untuk buku ini.' }}
            </div>
        </div>

        @if($book->stock > 0)
            <div style="margin-top: 32px; background: #f8fafc; border: 1px solid var(--line); border-radius: 12px; padding: 24px;">
                <h3 style="margin: 0 0 6px; font-size: 1.15rem; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <span>📅</span> Formulir Pengajuan Peminjaman
                </h3>
                <p class="muted" style="margin: 0 0 20px; font-size: 0.88rem;">
                    Tentukan tanggal rencana pengembalian buku sebelum mengajukan peminjaman.
                </p>

                <form action="{{ route('peminjam.borrow.store', $book) }}" method="POST">
                    @csrf

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label for="show_due_date" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; color: #1e293b;">
                                Rencana Tanggal Pengembalian <span style="color: var(--danger);">*</span>
                            </label>
                            <input
                                type="date"
                                id="show_due_date"
                                name="due_date"
                                value="{{ old('due_date', $defaultDueDate) }}"
                                min="{{ date('Y-m-d') }}"
                                required
                                style="width: 100%; padding: 10px 14px; border: 1px solid var(--line); border-radius: 8px; font-size: 0.95rem; background: #fff; color: #0f172a;"
                                onchange="updateDurationInfo(this.value, 'show_duration_badge')"
                            >
                            @error('due_date')
                                <p class="error" style="color: var(--danger); font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
                            @enderror

                            <!-- Quick Preset Duration Buttons -->
                            <div style="margin-top: 10px;">
                                <span style="font-size: 0.8rem; color: #64748b; font-weight: 500; display: block; margin-bottom: 6px;">
                                    Pilih Durasi Cepat:
                                </span>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <button type="button" class="preset-btn" onclick="applyPreset(3, 'show_due_date', 'show_duration_badge', this)">3 Hari</button>
                                    <button type="button" class="preset-btn preset-active" onclick="applyPreset({{ $defaultLoanDays }}, 'show_due_date', 'show_duration_badge', this)">{{ $defaultLoanDays }} Hari (Standar)</button>
                                    <button type="button" class="preset-btn" onclick="applyPreset(14, 'show_due_date', 'show_duration_badge', this)">14 Hari (2 Mgg)</button>
                                    <button type="button" class="preset-btn" onclick="applyPreset(30, 'show_due_date', 'show_duration_badge', this)">30 Hari (1 Bln)</button>
                                </div>
                            </div>

                            <div id="show_duration_badge" style="margin-top: 10px; font-size: 0.85rem; padding: 8px 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; color: #1e40af; display: flex; align-items: center; gap: 6px;">
                                ⏱️ Durasi Pinjam: <strong>{{ $defaultLoanDays }} hari</strong> &bull; Jatuh tempo: <strong>{{ \Carbon\Carbon::parse($defaultDueDate)->isoFormat('dddd, D MMMM Y') }}</strong>
                            </div>
                        </div>

                        <div>
                            <label for="show_notes" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; color: #1e293b;">
                                Catatan / Keperluan Pinjam (Opsional)
                            </label>
                            <textarea
                                id="show_notes"
                                name="notes"
                                rows="4"
                                placeholder="Contoh: Untuk tugas kelompok Bahasa Indonesia, persiapan olimpiade, dll."
                                style="width: 100%; padding: 10px 14px; border: 1px solid var(--line); border-radius: 8px; font-size: 0.95rem; background: #fff; resize: vertical; color: #0f172a;"
                            >{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="error" style="color: var(--danger); font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px; align-items: center; border-top: 1px solid var(--line); padding-top: 20px;">
                        <button type="submit" class="button button-primary" style="padding: 12px 28px; font-size: 1rem;">
                            📤 Ajukan Peminjaman Sekarang &rarr;
                        </button>
                        <a href="{{ route('peminjam.books.index') }}" class="button button-secondary">Kembali ke Katalog</a>
                    </div>
                </form>
            </div>
        @else
            <div class="form-actions" style="margin-top: 28px;">
                <button class="button button-secondary" disabled style="cursor: not-allowed; opacity: 0.7;">
                    Buku Sedang Tidak Tersedia (Stok Habis)
                </button>
                <a href="{{ route('peminjam.books.index') }}" class="button button-secondary">Kembali ke Katalog</a>
            </div>
        @endif
    </div>

    <style>
        .preset-btn {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 0.8rem;
            font-weight: 500;
            color: #334155;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .preset-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #eff6ff;
        }
        .preset-btn.preset-active {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            font-weight: 600;
        }
    </style>

    <script>
        function applyPreset(days, inputId, badgeId, btnElement) {
            const today = new Date();
            const targetDate = new Date();
            targetDate.setDate(today.getDate() + days);

            const yyyy = targetDate.getFullYear();
            const mm = String(targetDate.getMonth() + 1).padStart(2, '0');
            const dd = String(targetDate.getDate()).padStart(2, '0');
            const formatted = `${yyyy}-${mm}-${dd}`;

            const input = document.getElementById(inputId);
            if (input) {
                input.value = formatted;
            }

            // Update active state on sibling buttons
            if (btnElement && btnElement.parentElement) {
                btnElement.parentElement.querySelectorAll('.preset-btn').forEach(btn => {
                    btn.classList.remove('preset-active');
                });
                btnElement.classList.add('preset-active');
            }

            updateDurationInfo(formatted, badgeId);
        }

        function updateDurationInfo(dateStr, badgeId) {
            const badge = document.getElementById(badgeId);
            if (!badge || !dateStr) return;

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const targetDate = new Date(dateStr + 'T00:00:00');
            const diffTime = targetDate.getTime() - today.getTime();
            const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const formattedDate = targetDate.toLocaleDateString('id-ID', options);

            if (diffDays < 0) {
                badge.style.background = '#fef2f2';
                badge.style.borderColor = '#fecaca';
                badge.style.color = '#b91c1c';
                badge.innerHTML = `⚠️ Tanggal sudah lewat. Silakan pilih tanggal hari ini atau setelahnya.`;
            } else if (diffDays === 0) {
                badge.style.background = '#eff6ff';
                badge.style.borderColor = '#bfdbfe';
                badge.style.color = '#1e40af';
                badge.innerHTML = `⏱️ Durasi: <strong>Hari ini (1 Hari)</strong> &bull; Harus kembali: <strong>${formattedDate}</strong>`;
            } else {
                badge.style.background = '#eff6ff';
                badge.style.borderColor = '#bfdbfe';
                badge.style.color = '#1e40af';
                badge.innerHTML = `⏱️ Durasi Pinjam: <strong>${diffDays} hari</strong> &bull; Jatuh tempo: <strong>${formattedDate}</strong>`;
            }
        }
    </script>
@endsection
