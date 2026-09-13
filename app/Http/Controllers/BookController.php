<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function index(Request $request): View
    {
        $totalBooks = Book::count();
        $totalStock = Book::sum('stock');
        $totalCategories = Category::count() ?: Book::whereNotNull('category')->where('category', '!=', '')->distinct()->count('category');
        $latestBook = Book::latest()->first();

        $categories = Category::pluck('name')->toArray();
        if (empty($categories)) {
            $categories = ['Pelajaran', 'Cerita Rakyat', 'Novel', 'Komik', 'Lainnya'];
        }

        $query = Book::with('categoryRelation');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $cat = $request->category;
            $query->where(function ($q) use ($cat) {
                $q->where('category', $cat)
                  ->orWhereHas('categoryRelation', function ($cq) use ($cat) {
                      $cq->where('name', $cat);
                  });
            });
        }

        $books = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('books.index', compact(
            'books',
            'totalBooks',
            'totalStock',
            'totalCategories',
            'latestBook',
            'categories'
        ));
    }

    public function create(): View
    {
        $categories = Category::pluck('name')->toArray();
        if (empty($categories)) {
            $categories = ['Pelajaran', 'Cerita Rakyat', 'Novel', 'Komik', 'Lainnya'];
        }

        return view('books.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        // Cari atau buat ID kategori jika tabel categories tersedia
        $category = Category::where('name', $data['category'])->first();
        if ($category) {
            $data['category_id'] = $category->id;
        }

        Book::create($data);

        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan!');
    }

    public function show(Book $book): View
    {
        $book->load(['categoryRelation', 'activeBorrowings.member']);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book): View
    {
        $categories = Category::pluck('name')->toArray();
        if (empty($categories)) {
            $categories = ['Pelajaran', 'Cerita Rakyat', 'Novel', 'Komik', 'Lainnya'];
        }

        return view('books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book): RedirectResponse
    {
        $data = $this->validatedData($request);

        $category = Category::where('name', $data['category'])->first();
        if ($category) {
            $data['category_id'] = $category->id;
        }

        $book->update($data);

        return redirect()->route('books.index')->with('success', 'Buku berhasil diperbarui!');
    }

    public function destroy(Book $book): RedirectResponse
    {
        if ($book->activeBorrowings()->count() > 0) {
            return redirect()->route('books.index')->with('error', 'Buku tidak dapat dihapus karena sedang dipinjam!');
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus!');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'published_year' => ['required', 'integer', 'min:1000', 'max:' . (date('Y') + 1)],
            'category' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
        ], [
            'title.required' => 'Judul buku wajib diisi.',
            'author.required' => 'Penulis wajib diisi.',
            'published_year.required' => 'Tahun terbit wajib diisi.',
            'published_year.integer' => 'Tahun terbit harus berupa angka.',
            'category.required' => 'Kategori wajib diisi.',
            'stock.required' => 'Stok buku wajib diisi.',
            'stock.integer' => 'Stok buku harus berupa bilangan bulat.',
            'stock.min' => 'Stok buku tidak boleh bernilai negatif.',
        ]);
    }
}
