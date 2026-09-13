<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Member;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $member = $user->member ?: Member::where('email', $user->email)->first();

        $activeLoans = collect();
        $pendingRequests = collect();
        $dueSoonLoans = collect();
        $totalActiveCount = 0;
        $totalPendingCount = 0;
        $totalFineAmount = 0;

        if ($member) {
            // Buku yang sedang aktif dipinjam
            $activeLoans = Transaction::with('book')
                ->where('member_id', $member->id)
                ->where('status', 'Dipinjam')
                ->orderBy('due_date', 'asc')
                ->get();

            $totalActiveCount = $activeLoans->count();

            // Permohonan peminjaman yang menunggu persetujuan
            $pendingRequests = Transaction::with('book')
                ->where('member_id', $member->id)
                ->where('status', 'Menunggu')
                ->latest()
                ->get();

            $totalPendingCount = $pendingRequests->count();

            // Buku yang harus segera dikembalikan (jatuh tempo <= 2 hari ke depan atau terlambat)
            $today = Carbon::today();
            $dueSoonLoans = $activeLoans->filter(function ($trx) use ($today) {
                $dueDate = Carbon::parse($trx->due_date);
                return $dueDate->lessThanOrEqualTo($today->copy()->addDays(2));
            });

            // Total denda aktif
            $totalFineAmount = $activeLoans->sum(function ($trx) {
                return $trx->current_fine;
            });
        }

        // Rekomendasi buku terbaru yang tersedia untuk dipinjam
        $recommendedBooks = Book::with('categoryRelation')
            ->where('stock', '>', 0)
            ->latest()
            ->take(4)
            ->get();

        return view('peminjam.dashboard', compact(
            'member',
            'activeLoans',
            'pendingRequests',
            'dueSoonLoans',
            'totalActiveCount',
            'totalPendingCount',
            'totalFineAmount',
            'recommendedBooks'
        ));
    }
}
