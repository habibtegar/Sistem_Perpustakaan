<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::with(['member', 'book']);

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhereHas('member', function ($mq) use ($search) {
                      $mq->where('name', 'like', "%{$search}%")
                         ->orWhere('member_code', 'like', "%{$search}%");
                  })
                  ->orWhereHas('book', function ($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $today = Carbon::today()->toDateString();

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'Dikembalikan') {
                $query->where('status', 'Dikembalikan');
            } elseif ($status === 'Menunggu') {
                $query->where('status', 'Menunggu');
            } elseif ($status === 'Ditolak') {
                $query->where('status', 'Ditolak');
            } elseif ($status === 'Terlambat') {
                $query->where('status', 'Dipinjam')->where('due_date', '<', $today);
            } elseif ($status === 'Akan Jatuh Tempo') {
                $nearFuture = Carbon::today()->addDays(2)->toDateString();
                $query->where('status', 'Dipinjam')
                      ->where('due_date', '>=', $today)
                      ->where('due_date', '<=', $nearFuture);
            } elseif ($status === 'Dipinjam') {
                $query->where('status', 'Dipinjam');
            }
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $totalTransactions = Transaction::count();
        $totalFines = Transaction::sum('fine_amount');

        return view('admin.transactions.index', compact('transactions', 'totalTransactions', 'totalFines'));
    }
}
