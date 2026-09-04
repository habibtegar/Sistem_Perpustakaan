<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $member = $user->member ?: Member::where('email', $user->email)->first();

        if (!$member) {
            return view('peminjam.history.index', [
                'transactions' => collect(),
                'totalTransactions' => 0,
                'totalFines' => 0,
            ]);
        }

        $query = Transaction::with('book')
            ->where('member_id', $member->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhereHas('book', function ($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        $totalTransactions = Transaction::where('member_id', $member->id)->count();
        $totalFines = Transaction::where('member_id', $member->id)->sum('fine_amount');

        return view('peminjam.history.index', compact('transactions', 'totalTransactions', 'totalFines'));
    }
}
