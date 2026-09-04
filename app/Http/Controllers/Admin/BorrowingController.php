<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Member;
use App\Models\Setting;
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
        $query = Transaction::with(['member', 'book']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default tampilkan yang Menunggu atau Dipinjam
            $query->whereIn('status', ['Menunggu', 'Dipinjam']);
        }

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

        $borrowings = $query->orderByRaw("FIELD(status, 'Menunggu', 'Dipinjam')")
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $pendingCount = Transaction::where('status', 'Menunggu')->count();
        $activeCount = Transaction::where('status', 'Dipinjam')->count();

        return view('admin.borrowings.index', compact('borrowings', 'pendingCount', 'activeCount'));
    }

    public function create(): View
    {
        $members = Member::active()->orderBy('name', 'asc')->get();
        $allBooks = Book::orderBy('title', 'asc')->get();

        $defaultBorrowDate = Carbon::today()->format('Y-m-d');
        $defaultLoanDays = (int) Setting::get('default_loan_days', config('library.default_loan_days', 7));
        $defaultDueDate = Carbon::today()->addDays($defaultLoanDays)->format('Y-m-d');

        $todayCode = date('Ymd');
        $countToday = Transaction::whereDate('created_at', Carbon::today())->count() + 1;
        $suggestedCode = 'TRX-' . $todayCode . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

        return view('admin.borrowings.create', compact(
            'members',
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
            'book_id.required' => 'Pilih buku yang akan dipinjam.',
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

            $book->decrement('stock', 1);

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

            return redirect()->route('admin.borrowings.index')->with('success', 'Peminjaman berhasil dicatat! Stok buku berkurang 1.');
        });
    }

    public function approve(Transaction $transaction): RedirectResponse
    {
        if ($transaction->status !== 'Menunggu') {
            return back()->with('error', 'Hanya pengajuan dengan status Menunggu yang dapat disetujui.');
        }

        return DB::transaction(function () use ($transaction) {
            $book = Book::lockForUpdate()->find($transaction->book_id);

            if (!$book || $book->stock <= 0) {
                return back()->with('error', 'Gagal menyetujui peminjaman: Stok buku saat ini habis.');
            }

            $book->decrement('stock', 1);

            $defaultLoanDays = (int) Setting::get('default_loan_days', config('library.default_loan_days', 7));
            $borrowDate = Carbon::today();
            $dueDate = $borrowDate->copy()->addDays($defaultLoanDays);

            $transaction->update([
                'borrow_date' => $borrowDate->toDateString(),
                'due_date' => $dueDate->toDateString(),
                'status' => 'Dipinjam',
                'admin_notes' => 'Disetujui oleh Admin pada ' . Carbon::now()->format('d/m/Y H:i'),
            ]);

            return redirect()->route('admin.borrowings.index')->with('success', "Pengajuan peminjaman untuk '{$transaction->book->title}' telah disetujui! Stok buku berkurang 1.");
        });
    }

    public function reject(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->status !== 'Menunggu') {
            return back()->with('error', 'Hanya pengajuan dengan status Menunggu yang dapat ditolak.');
        }

        $adminNotes = $request->input('admin_notes', 'Pengajuan ditolak oleh Admin.');

        $transaction->update([
            'status' => 'Ditolak',
            'admin_notes' => $adminNotes,
        ]);

        return redirect()->route('admin.borrowings.index')->with('success', "Pengajuan peminjaman untuk '{$transaction->book->title}' berhasil ditolak.");
    }
}
