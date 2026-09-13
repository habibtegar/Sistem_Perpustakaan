<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $totalBooks = Book::count();
        $totalStock = Book::sum('stock');
        $categories = Category::orderBy('name', 'asc')->get();

        $query = Book::with('categoryRelation');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('stock_status')) {
            if ($request->stock_status === 'available') {
                $query->where('stock', '>', 0);
            } elseif ($request->stock_status === 'empty') {
                $query->where('stock', '<=', 0);
            }
        }

        $books = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('admin.books.index', compact(
            'books',
            'totalBooks',
            'totalStock',
            'categories'
        ));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'published_year' => ['required', 'integer', 'min:1000', 'max:' . (date('Y') + 1)],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Penulis wajib diisi.',
            'published_year.required' => 'Tahun terbit wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'stock.required' => 'Jumlah stok buku wajib diisi.',
            'stock.min' => 'Stok buku tidak boleh kurang dari 0.',
            'cover.image' => 'File cover harus berupa gambar.',
            'cover.max' => 'Ukuran file cover maksimal 2MB.',
        ]);

        $category = Category::find($request->category_id);
        $data['category'] = $category ? $category->name : 'Umum';

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public');
            $data['cover'] = $path;
        }

        Book::create($data);

        return redirect()->route('admin.books.index')->with('success', 'Buku baru berhasil ditambahkan ke katalog!');
    }

    public function show(Book $book): View
    {
        $book->load(['categoryRelation', 'activeBorrowings.member', 'transactions' => function ($q) {
            $q->with('member')->latest()->take(10);
        }]);

        return view('admin.books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name', 'asc')->get();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'published_year' => ['required', 'integer', 'min:1000', 'max:' . (date('Y') + 1)],
            'category_id' => ['required', 'exists:categories,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Penulis wajib diisi.',
            'published_year.required' => 'Tahun terbit wajib diisi.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'stock.required' => 'Jumlah stok buku wajib diisi.',
            'stock.min' => 'Stok buku tidak boleh kurang dari 0.',
            'cover.image' => 'File cover harus berupa gambar.',
            'cover.max' => 'Ukuran file cover maksimal 2MB.',
        ]);

        $category = Category::find($request->category_id);
        $data['category'] = $category ? $category->name : 'Umum';

        if ($request->hasFile('cover')) {
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }
            $path = $request->file('cover')->store('covers', 'public');
            $data['cover'] = $path;
        }

        $book->update($data);

        return redirect()->route('admin.books.index')->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->activeBorrowings()->count() > 0) {
            return redirect()->route('admin.books.index')->with('error', 'Buku tidak dapat dihapus karena masih ada yang sedang dipinjam!');
        }

        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil dihapus dari perpustakaan!');
    }
}
