<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
        
        // Peminjaman aktif
        $activeBorrowingsCount = Transaction::where('status', 'Dipinjam')->count();
        $totalMembers = Member::count();

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

        // Daftar peminjaman terlambat (5 data)
        $overdueTransactions = Transaction::with(['member', 'book'])
            ->where('status', 'Dipinjam')
            ->where('due_date', '<', $today)
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // Buku paling sering dipinjam
        $popularBooks = Book::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalBooks',
            'totalStock',
            'activeBorrowingsCount',
            'totalMembers',
            'overdueCount',
            'recentTransactions',
            'overdueTransactions',
            'popularBooks'
        ));
    }
}
