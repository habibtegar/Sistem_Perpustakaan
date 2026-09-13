<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MyBorrowingController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $member = $user->member ?: Member::where('email', $user->email)->first();

        if (!$member) {
            return view('peminjam.borrowings.index', [
                'borrowings' => collect(),
                'activeCount' => 0,
                'pendingCount' => 0,
            ]);
        }

        $query = Transaction::with('book')
            ->where('member_id', $member->id)
            ->whereIn('status', ['Menunggu', 'Dipinjam', 'Ditolak']);

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

        $borrowings = $query->orderByRaw("FIELD(status, 'Menunggu', 'Dipinjam', 'Ditolak')")
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $activeCount = Transaction::where('member_id', $member->id)->where('status', 'Dipinjam')->count();
        $pendingCount = Transaction::where('member_id', $member->id)->where('status', 'Menunggu')->count();

        return view('peminjam.borrowings.index', compact('borrowings', 'activeCount', 'pendingCount'));
    }
}
