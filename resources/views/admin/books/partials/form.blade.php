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
        <label for="category_id">Kategori <span style="color: var(--danger);">*</span></label>
        <select id="category_id" name="category_id" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id', $book->category_id ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        @error('category_id') <p class="error">{{ $message }}</p> @enderror
    </div>

    <div class="form-group">
        <label for="stock">Jumlah Stok Eksemplar <span style="color: var(--danger);">*</span></label>
        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $book->stock ?? 5) }}" placeholder="Contoh: 5" required>
        <p class="helper-text">Jumlah fisik buku yang siap dipinjamkan.</p>
        @error('stock') <p class="error">{{ $message }}</p> @enderror
    </div>
</div>

<div class="form-group">
    <label for="cover">Cover Buku <span class="muted">(opsional)</span></label>
    <input id="cover" name="cover" type="file" accept="image/*">
    <p class="helper-text">Format didukung: JPG, PNG, WEBP. Maksimal 2MB.</p>
    @if(isset($book) && $book->cover)
        <div style="margin-top: 8px; display: flex; align-items: center; gap: 10px;">
            <img src="{{ $book->cover_url }}" alt="Cover" style="width: 50px; height: 70px; object-fit: cover; border-radius: 4px; border: 1px solid var(--line);">
            <small class="muted">Cover saat ini terpasang.</small>
        </div>
    @endif
    @error('cover') <p class="error">{{ $message }}</p> @enderror
</div>

<div class="form-group">
    <label for="description">Deskripsi / Sinopsis <span class="muted">(opsional)</span></label>
    <textarea id="description" name="description" placeholder="Masukkan ringkasan atau sinopsis buku">{{ old('description', $book->description ?? '') }}</textarea>
    @error('description') <p class="error">{{ $message }}</p> @enderror
</div>
