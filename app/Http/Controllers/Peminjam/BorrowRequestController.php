<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Member;
use App\Models\Setting;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BorrowRequestController extends Controller
{
    public function store(Request $request, Book $book): RedirectResponse
    {
        $user = Auth::user();
        $member = $user->member ?: Member::where('email', $user->email)->first();

        if (!$member) {
            return back()->with('error', 'Profil keanggotaan Anda belum terdaftar. Silakan hubungi admin perpustakaan.');
        }

        if ($member->status !== 'Aktif') {
            return back()->with('error', 'Status keanggotaan Anda sedang Nonaktif. Anda tidak dapat mengajukan peminjaman.');
        }

        if ($book->stock <= 0) {
            return back()->with('error', 'Maaf, stok buku ini sedang habis.');
        }

        // Cek apakah peminjam sudah punya permohonan 'Menunggu' untuk buku ini
        $existingPending = Transaction::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->where('status', 'Menunggu')
            ->first();

        if ($existingPending) {
            return back()->with('error', 'Anda sudah memiliki permohonan peminjaman untuk buku ini yang sedang menunggu persetujuan Admin.');
        }

        // Cek apakah peminjam sedang meminjam buku ini dan belum mengembalikannya
        $existingActive = Transaction::where('member_id', $member->id)
            ->where('book_id', $book->id)
            ->where('status', 'Dipinjam')
            ->first();

        if ($existingActive) {
            return back()->with('error', 'Anda saat ini sedang meminjam buku ini. Harap kembalikan terlebih dahulu.');
        }

        $defaultLoanDays = (int) Setting::get('default_loan_days', config('library.default_loan_days', 7));
        $borrowDate = Carbon::today();
        $dueDate = $borrowDate->copy()->addDays($defaultLoanDays);

        $todayCode = date('Ymd');
        $countToday = Transaction::whereDate('created_at', Carbon::today())->count() + 1;
        $transactionCode = 'TRX-' . $todayCode . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        Transaction::create([
            'transaction_code' => $transactionCode,
            'member_id' => $member->id,
            'book_id' => $book->id,
            'borrow_date' => $borrowDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'status' => 'Menunggu',
            'fine_amount' => 0,
            'notes' => $request->input('notes', 'Pengajuan peminjaman oleh anggota secara online.'),
        ]);

        return redirect()->route('peminjam.my-borrowings')->with('success', "Permohonan peminjaman buku '{$book->title}' berhasil diajukan! Menunggu persetujuan Admin.");
    }
}
