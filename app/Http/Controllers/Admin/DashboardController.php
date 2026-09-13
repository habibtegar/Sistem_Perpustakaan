<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalBooks = Book::count();
        $totalStock = Book::sum('stock');
        $totalCategories = Category::count();
        $totalMembers = Member::count();

        // Total buku sedang dipinjam (status = 'Dipinjam')
        $borrowedBooksCount = Transaction::where('status', 'Dipinjam')->count();

        // Permohonan peminjaman menunggu persetujuan
        $pendingCount = Transaction::where('status', 'Menunggu')->count();

        // Buku terlambat (status 'Dipinjam' dan due_date < hari ini)
        $today = Carbon::today()->toDateString();
        $overdueCount = Transaction::where('status', 'Dipinjam')
            ->where('due_date', '<', $today)
            ->count();

        // Peminjaman terbaru (5 transaksi terakhir)
        $recentTransactions = Transaction::with(['member', 'book'])
            ->latest()
            ->take(5)
            ->get();

        // Buku terbaru (5 buku terakhir ditambahkan)
        $latestBooks = Book::with('categoryRelation')
            ->latest()
            ->take(5)
            ->get();

        // Daftar peminjaman terlambat (5 data)
        $overdueTransactions = Transaction::with(['member', 'book'])
            ->where('status', 'Dipinjam')
            ->where('due_date', '<', $today)
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalBooks',
            'totalStock',
            'borrowedBooksCount',
            'totalCategories',
            'totalMembers',
            'pendingCount',
            'overdueCount',
            'recentTransactions',
            'latestBooks',
            'overdueTransactions'
        ));
    }
}
