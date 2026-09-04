<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Member;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BorrowingController extends Controller
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

        $borrowings = $query->orderBy('borrow_date', 'desc')->paginate(10)->withQueryString();
        $totalActive = Transaction::where('status', 'Dipinjam')->count();

        return view('borrowings.index', compact('borrowings', 'totalActive'));
    }

    public function create(): View
    {
        $members = Member::active()->orderBy('name', 'asc')->get();
        $books = Book::where('stock', '>', 0)->orderBy('title', 'asc')->get();
        $allBooks = Book::orderBy('title', 'asc')->get();

        $defaultBorrowDate = Carbon::today()->format('Y-m-d');
        $defaultLoanDays = (int) config('library.default_loan_days', 7);
        $defaultDueDate = Carbon::today()->addDays($defaultLoanDays)->format('Y-m-d');

        // Saran nomor transaksi
        $todayCode = date('Ymd');
        $countToday = Transaction::whereDate('created_at', Carbon::today())->count() + 1;
        $suggestedCode = 'TRX-' . $todayCode . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        return view('borrowings.create', compact(
            'members',
            'books',
            'allBooks',
            'defaultBorrowDate',
            'defaultDueDate',
            'suggestedCode'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'transaction_code' => ['required', 'string', 'max:50', 'unique:transactions,transaction_code'],
            'member_id' => ['required', 'exists:members,id'],
            'book_id' => ['required', 'exists:books,id'],
            'borrow_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:borrow_date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'transaction_code.required' => 'Nomor transaksi wajib diisi.',
            'transaction_code.unique' => 'Nomor transaksi sudah digunakan.',
            'member_id.required' => 'Pilih anggota peminjam.',
            'member_id.exists' => 'Anggota tidak ditemukan.',
            'book_id.required' => 'Pilih buku yang akan dipinjam.',
            'book_id.exists' => 'Buku tidak ditemukan.',
            'borrow_date.required' => 'Tanggal pinjam wajib diisi.',
            'due_date.required' => 'Tanggal jatuh tempo wajib diisi.',
            'due_date.after_or_equal' => 'Tanggal jatuh tempo harus sama atau setelah tanggal pinjam.',
        ]);

        $member = Member::findOrFail($request->member_id);
        if ($member->status !== 'Aktif') {
            return back()->withInput()->withErrors(['member_id' => 'Anggota sedang tidak aktif dan tidak dapat meminjam buku.']);
        }

        return DB::transaction(function () use ($request) {
            $book = Book::lockForUpdate()->findOrFail($request->book_id);

            if ($book->stock <= 0) {
                return back()->withInput()->withErrors(['book_id' => 'Stok buku habis! Buku tidak dapat dipinjam.']);
            }

            // Kurangi stok buku
            $book->decrement('stock', 1);

            // Buat record transaksi
            Transaction::create([
                'transaction_code' => $request->transaction_code,
                'member_id' => $request->member_id,
                'book_id' => $request->book_id,
                'borrow_date' => $request->borrow_date,
                'due_date' => $request->due_date,
                'status' => 'Dipinjam',
                'fine_amount' => 0,
                'notes' => $request->notes,
            ]);

            return redirect()->route('borrowings.index')->with('success', 'Buku berhasil dipinjam! Stok buku berkurang 1.');
        });
    }
}
