<?php

namespace App\Http\Controllers;

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
            if ($request->status === 'Dikembalikan') {
                $query->where('status', 'Dikembalikan');
            } elseif ($request->status === 'Dipinjam') {
                $query->where('status', 'Dipinjam');
            }
        }

        $transactions = $query->orderBy('borrow_date', 'desc')->get();

        $totalBorrowed = $transactions->count();
        $totalReturned = $transactions->where('status', 'Dikembalikan')->count();
        $totalActive = $transactions->where('status', 'Dipinjam')->count();
        $totalFinesCollected = $transactions->sum('fine_amount');

        return view('reports.index', compact(
            'transactions',
            'startDate',
            'endDate',
            'totalBorrowed',
            'totalReturned',
            'totalActive',
            'totalFinesCollected'
        ));
    }
}
