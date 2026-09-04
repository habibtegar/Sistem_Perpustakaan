<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReturnController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::with(['member', 'book'])
            ->where('status', 'Dipinjam');

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

        $borrowings = $query->orderBy('due_date', 'asc')->paginate(10)->withQueryString();
        $today = Carbon::today()->toDateString();
        $overdueCount = Transaction::where('status', 'Dipinjam')->where('due_date', '<', $today)->count();
        $fineRate = config('library.fine_per_day', 1000);

        return view('returns.index', compact('borrowings', 'overdueCount', 'fineRate'));
    }

    public function process(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->status === 'Dikembalikan') {
            return redirect()->route('returns.index')->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
        }

        $returnDate = $request->filled('return_date') 
            ? Carbon::parse($request->return_date) 
            : Carbon::today();

        $dueDate = Carbon::parse($transaction->due_date)->startOfDay();
        $returnDateStart = $returnDate->copy()->startOfDay();

        $daysLate = 0;
        $fineAmount = 0;
        $rate = (int) config('library.fine_per_day', 1000);

        if ($returnDateStart->greaterThan($dueDate)) {
            $daysLate = $dueDate->diffInDays($returnDateStart);
            $fineAmount = $daysLate * $rate;
        }

        DB::transaction(function () use ($transaction, $returnDate, $fineAmount) {
            $book = Book::lockForUpdate()->find($transaction->book_id);
            if ($book) {
                $book->increment('stock', 1);
            }

            $transaction->update([
                'return_date' => $returnDate->toDateString(),
                'status' => 'Dikembalikan',
                'fine_amount' => $fineAmount,
            ]);
        });

        $message = "Buku '{$transaction->book->title}' berhasil dikembalikan! Stok bertambah 1.";
        if ($fineAmount > 0) {
            $message .= " Dikenakan denda keterlambatan {$daysLate} hari: Rp " . number_format($fineAmount, 0, ',', '.');
        }

        return redirect()->route('returns.index')->with('success', $message);
    }
}
