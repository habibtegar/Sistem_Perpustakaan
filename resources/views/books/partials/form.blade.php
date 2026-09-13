@if ($errors->any())
    <div class="alert alert-error">
        Periksa kembali data yang ditandai di bawah.
    </div>
@endif

<div class="form-group">
    <label for="title">Judul Buku <span style="color: var(--danger);">*</span></label>
    <input id="title" name="title" type="text" value="{{ old('title', $book->title ?? '') }}" placeholder="Masukkan judul buku" required autofocus>
    @error('title') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="form-grid">
    <div class="form-group">
        <label for="author">Penulis <span style="color: var(--danger);">*</span></label>
        <input id="author" name="author" type="text" value="{{ old('author', $book->author ?? '') }}" placeholder="Masukkan nama penulis" required>
        @error('author') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label for="published_year">Tahun Terbit <span style="color: var(--danger);">*</span></label>
        <input id="published_year" name="published_year" type="number" value="{{ old('published_year', $book->published_year ?? date('Y')) }}" placeholder="Contoh: 2024" min="1000" max="{{ date('Y') + 1 }}" required>
        @error('published_year') <p class="error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="form-grid">
    <div class="form-group">
        <label for="category">Kategori <span style="color: var(--danger);">*</span></label>
        @php
            $defaultCategories = ['Pelajaran', 'Cerita Rakyat', 'Novel', 'Komik', 'Lainnya'];
            $optCategories = isset($categories) && count($categories) ? array_unique(array_merge($categories, $defaultCategories)) : $defaultCategories;
            $currentCat = old('category', $book->category ?? ($book->categoryRelation->name ?? ''));
        @endphp
        <select id="category" name="category" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($optCategories as $cat)
                <option value="{{ $cat }}" {{ $currentCat == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
        @error('category') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label for="stock">Jumlah Stok Eksemplar <span style="color: var(--danger);">*</span></label>
        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $book->stock ?? 5) }}" placeholder="Contoh: 5" required>
        <p class="helper-text">Jumlah fisik buku yang siap dipinjamkan.</p>
        @error('stock') <p class="error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="form-group">
    <label for="description">Deskripsi <span class="muted">(opsional)</span></label>
    <textarea id="description" name="description" placeholder="Masukkan ringkasan atau sinopsis buku">{{ old('description', $book->description ?? '') }}</textarea>
    @error('description') <p class="error">{{ $message }}</p> @enderror
</div>
