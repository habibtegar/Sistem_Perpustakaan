<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Member;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $query = Transaction::with(['member', 'book'])
            ->whereBetween('borrow_date', [$startDate, $endDate]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('borrow_date', 'desc')->get();

        $totalBorrowed = $transactions->where('status', 'Dipinjam')->count();
        $totalReturned = $transactions->where('status', 'Dikembalikan')->count();
        $totalFinesCollected = $transactions->sum('fine_amount');

        // Buku paling sering dipinjam
        $popularBooks = Book::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(5)
            ->get();

        // Anggota paling aktif meminjam
        $activeMembers = Member::withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(5)
            ->get();

        // Buku yang sedang terlambat
        $today = Carbon::today()->toDateString();
        $overdueLoans = Transaction::with(['member', 'book'])
            ->where('status', 'Dipinjam')
            ->where('due_date', '<', $today)
            ->get();

        return view('admin.reports.index', compact(
            'transactions',
            'startDate',
            'endDate',
            'totalBorrowed',
            'totalReturned',
            'totalFinesCollected',
            'popularBooks',
            'activeMembers',
            'overdueLoans'
        ));
    }
}
