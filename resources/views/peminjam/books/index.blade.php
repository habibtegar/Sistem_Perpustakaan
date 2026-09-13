@extends('layouts.app')

@section('title', 'Katalog Buku - Sistem Perpustakaan')

@section('content')
    <div class="page-header">
        <div>
            <h1>Katalog & Koleksi Buku</h1>
            <p class="subtitle">Temukan buku yang Anda minati dan ajukan peminjaman secara mudah.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('peminjam.my-borrowings') }}" class="button button-secondary">📖 Peminjaman Saya</a>
        </div>
    </div>

    <!-- Filter & Pencarian Buku -->
    <div class="filter-card">
        <form action="{{ route('peminjam.books.index') }}" method="GET" class="filter-form">
            <div class="filter-group filter-search">
                <label for="search">Cari Judul / Penulis</label>
                <input
                    id="search"
                    type="text"
                    name="search"
                    placeholder="Masukkan judul buku atau nama pengarang..."
                    value="{{ request('search') }}"
                >
            </div>

            <div class="filter-group filter-select">
                <label for="category_id">Kategori</label>
                <select id="category_id" name="category_id">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group filter-select">
                <label for="availability">Ketersediaan</label>
                <select id="availability" name="availability">
                    <option value="">Semua Status</option>
                    <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Tersedia Saja (> 0)</option>
                </select>
            </div>

            <div class="filter-buttons">
                <button type="submit" class="button button-primary">Cari Buku</button>
                @if(request('search') || request('category_id') || request('availability'))
                    <a href="{{ route('peminjam.books.index') }}" class="button button-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Grid Kartu Buku -->
    @if ($books->isEmpty())
        <div class="table-wrap">
            <div class="empty-state">
                <div class="empty-icon">📖</div>
                <h3>Buku yang Anda cari tidak ditemukan.</h3>
                <p class="muted">Coba ubah kata kunci pencarian atau ganti filter kategori.</p>
                <a href="{{ route('peminjam.books.index') }}" class="button button-secondary" style="margin-top: 12px;">Reset Pencarian</a>
            </div>
        </div>
    @else
        <div class="catalog-grid">
            @foreach ($books as $book)
                <div style="background: var(--surface); border: 1px solid var(--line); border-radius: 12px; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03); transition: transform 0.2s ease, box-shadow 0.2s ease;">
                    <div>
                        <div style="height: 180px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative;">
                            <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                            <div style="position: absolute; top: 10px; right: 10px;">
                                <span class="badge badge-secondary" style="backdrop-filter: blur(4px); background: rgba(255,255,255,0.9); font-weight: 700;">
                                    {{ $book->category_name }}
                                </span>
                            </div>
                        </div>

                        <div style="padding: 16px;">
                            <h3 style="font-size: 1.05rem; margin: 0 0 6px; color: #0f172a; line-height: 1.35;">
                                <a href="{{ route('peminjam.books.show', $book) }}" style="color: inherit;">
                                    {{ Str::limit($book->title, 40) }}
                                </a>
                            </h3>
                            <p class="muted" style="font-size: 0.85rem; margin: 0 0 10px;">
                                Penulis: <strong>{{ $book->author }}</strong> ({{ $book->published_year }})
                            </p>
                            @if($book->description)
                                <p style="font-size: 0.82rem; color: #64748b; margin: 0; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $book->description }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div style="padding: 14px 16px; border-top: 1px solid var(--line); background: #f8fafc; display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <div>
                            @if($book->stock > 0)
                                <span class="badge badge-success" style="font-size: 0.78rem;">Tersedia ({{ $book->stock }})</span>
                            @else
                                <span class="badge badge-danger" style="font-size: 0.78rem;">Tidak Tersedia</span>
                            @endif
                        </div>

                        <div>
                            @if($book->stock > 0)
                                <button
                                    type="button"
                                    class="button button-primary button-sm"
                                    onclick="openBorrowModal('{{ $book->id }}', '{{ addslashes($book->title) }}', '{{ addslashes($book->author) }}', '{{ addslashes($book->category_name) }}', '{{ $book->cover_url }}')"
                                >
                                    Ajukan Pinjam &rarr;
                                </button>
                            @else
                                <button class="button button-secondary button-sm" disabled style="cursor: not-allowed; opacity: 0.7;">
                                    Stok Habis
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Pagination -->
    @if ($books->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-custom">
                @if ($books->onFirstPage())
                    <span class="page-button disabled">&laquo; Sebelumnya</span>
                @else
                    <a href="{{ $books->previousPageUrl() }}" class="page-button">&laquo; Sebelumnya</a>
                @endif

                <span class="page-info">Halaman <strong>{{ $books->currentPage() }}</strong> dari <strong>{{ $books->lastPage() }}</strong></span>

                @if ($books->hasMorePages())
                    <a href="{{ $books->nextPageUrl() }}" class="page-button">Selanjutnya &raquo;</a>
                @else
                    <span class="page-button disabled">Selanjutnya &raquo;</span>
                @endif
            </div>
        </div>
    @endif

    <!-- Modal Form Pengajuan Peminjaman -->
    <div id="borrowModal" class="modal-backdrop" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 style="margin: 0; font-size: 1.2rem; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                    <span>📖</span> Ajukan Peminjaman Buku
                </h3>
                <button type="button" class="modal-close-btn" onclick="closeBorrowModal()">&times;</button>
            </div>

            <div class="modal-body">
                <!-- Preview Info Buku yang Dipilih -->
                <div style="display: flex; gap: 14px; align-items: center; background: #f8fafc; border: 1px solid var(--line); border-radius: 8px; padding: 12px; margin-bottom: 20px;">
                    <img id="modal_book_cover" src="" alt="Cover" style="width: 50px; height: 70px; object-fit: cover; border-radius: 6px; border: 1px solid var(--line);">
                    <div>
                        <span id="modal_book_category" class="badge badge-secondary" style="font-size: 0.75rem;">-</span>
                        <h4 id="modal_book_title" style="margin: 4px 0 2px; font-size: 1rem; color: #0f172a; line-height: 1.3;">-</h4>
                        <p class="muted" style="margin: 0; font-size: 0.82rem;">Penulis: <strong id="modal_book_author">-</strong></p>
                    </div>
                </div>

                <form id="modal_borrow_form" action="" method="POST">
                    @csrf

                    <div style="margin-bottom: 18px;">
                        <label for="modal_due_date" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; color: #1e293b;">
                            Rencana Tanggal Pengembalian <span style="color: var(--danger);">*</span>
                        </label>
                        <input
                            type="date"
                            id="modal_due_date"
                            name="due_date"
                            value="{{ $defaultDueDate }}"
                            min="{{ date('Y-m-d') }}"
                            required
                            style="width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; font-size: 0.95rem; background: #fff; color: #0f172a;"
                            onchange="updateModalDurationInfo(this.value)"
                        >

                        <!-- Pilihan Preset Durasi Cepat -->
                        <div style="margin-top: 8px;">
                            <span style="font-size: 0.78rem; color: #64748b; font-weight: 500; display: block; margin-bottom: 5px;">Pilihan Cepat Durasi:</span>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <button type="button" class="preset-btn" onclick="applyModalPreset(3, this)">3 Hari</button>
                                <button type="button" class="preset-btn preset-active" onclick="applyModalPreset({{ $defaultLoanDays }}, this)">{{ $defaultLoanDays }} Hari (Standar)</button>
                                <button type="button" class="preset-btn" onclick="applyModalPreset(14, this)">14 Hari (2 Mgg)</button>
                                <button type="button" class="preset-btn" onclick="applyModalPreset(30, this)">30 Hari (1 Bln)</button>
                            </div>
                        </div>

                        <div id="modal_duration_info" style="margin-top: 10px; font-size: 0.84rem; padding: 8px 12px; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; color: #1e40af;">
                            ⏱️ Durasi Pinjam: <strong>{{ $defaultLoanDays }} hari</strong> &bull; Jatuh tempo: <strong>{{ \Carbon\Carbon::parse($defaultDueDate)->isoFormat('dddd, D MMMM Y') }}</strong>
                        </div>
                    </div>

                    <div style="margin-bottom: 22px;">
                        <label for="modal_notes" style="display: block; font-weight: 600; margin-bottom: 6px; font-size: 0.9rem; color: #1e293b;">
                            Catatan / Keperluan Pinjam (Opsional)
                        </label>
                        <textarea
                            id="modal_notes"
                            name="notes"
                            rows="2"
                            placeholder="Contoh: Untuk tugas kelompok, referensi ujian, dll."
                            style="width: 100%; padding: 10px 12px; border: 1px solid var(--line); border-radius: 8px; font-size: 0.9rem; background: #fff; resize: vertical; color: #0f172a;"
                        ></textarea>
                    </div>

                    <div style="display: flex; gap: 10px; justify-content: flex-end;">
                        <button type="button" class="button button-secondary" onclick="closeBorrowModal()">Batal</button>
                        <button type="submit" class="button button-primary">
                            📤 Kirim Pengajuan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            z-index: 9999;
            animation: fadeIn 0.2s ease-out;
        }
        .modal-dialog {
            background: #ffffff;
            width: 100%;
            max-width: 520px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            animation: slideUp 0.25s ease-out;
        }
        .modal-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--line);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8fafc;
        }
        .modal-close-btn {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            color: #64748b;
            cursor: pointer;
            line-height: 1;
            padding: 4px;
            border-radius: 4px;
            transition: color 0.15s;
        }
        .modal-close-btn:hover {
            color: #0f172a;
        }
        .modal-body {
            padding: 20px 24px;
        }
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
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>

    <script>
        const defaultLoanDays = {{ $defaultLoanDays }};

        function openBorrowModal(bookId, title, author, category, coverUrl) {
            const modal = document.getElementById('borrowModal');
            const form = document.getElementById('modal_borrow_form');
            const bookTitle = document.getElementById('modal_book_title');
            const bookAuthor = document.getElementById('modal_book_author');
            const bookCategory = document.getElementById('modal_book_category');
            const bookCover = document.getElementById('modal_book_cover');

            form.action = `/borrow/${bookId}`;
            bookTitle.textContent = title;
            bookAuthor.textContent = author;
            bookCategory.textContent = category;
            bookCover.src = coverUrl;

            // Reset preset to default loan days
            const defaultDays = defaultLoanDays;
            const today = new Date();
            const targetDate = new Date();
            targetDate.setDate(today.getDate() + defaultDays);

            const yyyy = targetDate.getFullYear();
            const mm = String(targetDate.getMonth() + 1).padStart(2, '0');
            const dd = String(targetDate.getDate()).padStart(2, '0');
            const formatted = `${yyyy}-${mm}-${dd}`;

            const input = document.getElementById('modal_due_date');
            if (input) input.value = formatted;

            updateModalDurationInfo(formatted);

            // Set preset buttons active state
            document.querySelectorAll('#borrowModal .preset-btn').forEach(btn => {
                btn.classList.remove('preset-active');
                if (btn.textContent.includes(`${defaultDays} Hari`)) {
                    btn.classList.add('preset-active');
                }
            });

            modal.style.display = 'flex';
        }

        function closeBorrowModal() {
            const modal = document.getElementById('borrowModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        function applyModalPreset(days, btnElement) {
            const today = new Date();
            const targetDate = new Date();
            targetDate.setDate(today.getDate() + days);

            const yyyy = targetDate.getFullYear();
            const mm = String(targetDate.getMonth() + 1).padStart(2, '0');
            const dd = String(targetDate.getDate()).padStart(2, '0');
            const formatted = `${yyyy}-${mm}-${dd}`;

            const input = document.getElementById('modal_due_date');
            if (input) input.value = formatted;

            if (btnElement && btnElement.parentElement) {
                btnElement.parentElement.querySelectorAll('.preset-btn').forEach(btn => {
                    btn.classList.remove('preset-active');
                });
                btnElement.classList.add('preset-active');
            }

            updateModalDurationInfo(formatted);
        }

        function updateModalDurationInfo(dateStr) {
            const badge = document.getElementById('modal_duration_info');
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

        // Close on backdrop click
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('borrowModal');
            if (e.target === modal) {
                closeBorrowModal();
            }
        });

        // Close on Escape key
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBorrowModal();
            }
        });
    </script>
@endsection
