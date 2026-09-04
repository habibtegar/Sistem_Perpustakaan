<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookCatalogController extends Controller
{
    public function index(Request $request): View
    {
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

        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->where('stock', '>', 0);
            }
        }

        $books = $query->orderBy('title', 'asc')->paginate(12)->withQueryString();
        $totalAvailable = Book::where('stock', '>', 0)->count();

        return view('peminjam.books.index', compact('books', 'categories', 'totalAvailable'));
    }

    public function show(Book $book): View
    {
        $book->load('categoryRelation');
        return view('peminjam.books.show', compact('book'));
    }
}
