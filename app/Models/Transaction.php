<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'member_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'fine_amount',
        'notes',
        'admin_notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'integer',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Menghitung jumlah hari keterlambatan.
     */
    public function getDaysLateAttribute(): int
    {
        if (in_array($this->status, ['Menunggu', 'Ditolak'])) {
            return 0;
        }

        $dueDate = Carbon::parse($this->due_date)->startOfDay();
        $targetDate = $this->return_date 
            ? Carbon::parse($this->return_date)->startOfDay() 
            : Carbon::now()->startOfDay();

        if ($targetDate->greaterThan($dueDate)) {
            return $dueDate->diffInDays($targetDate);
        }

        return 0;
    }

    /**
     * Menghitung estimasi/nilai denda berdasarkan hari keterlambatan dan tarif denda.
     */
    public function getCurrentFineAttribute(): int
    {
        if ($this->status === 'Dikembalikan' && $this->return_date) {
            return (int) $this->fine_amount;
        }

        if (in_array($this->status, ['Menunggu', 'Ditolak'])) {
            return 0;
        }

        $rate = (int) Setting::get('fine_per_day', config('library.fine_per_day', 1000));
        return $this->days_late * $rate;
    }

    /**
     * Menentukan status dinamis transaksi:
     * - Menunggu
     * - Ditolak
     * - Dikembalikan
     * - Terlambat (jika belum dikembalikan dan hari ini > due_date)
     * - Akan Jatuh Tempo (jika belum dikembalikan dan jatuh tempo dalam <= 2 hari)
     * - Dipinjam
     */
    public function getCalculatedStatusAttribute(): string
    {
        if ($this->status === 'Menunggu') {
            return 'Menunggu';
        }

        if ($this->status === 'Ditolak') {
            return 'Ditolak';
        }

        if ($this->status === 'Dikembalikan' || $this->return_date) {
            return 'Dikembalikan';
        }

        $now = Carbon::now()->startOfDay();
        $dueDate = Carbon::parse($this->due_date)->startOfDay();

        if ($now->greaterThan($dueDate)) {
            return 'Terlambat';
        }

        if ($now->diffInDays($dueDate, false) <= 2 && $now->diffInDays($dueDate, false) >= 0) {
            return 'Akan Jatuh Tempo';
        }

        return 'Dipinjam';
    }
}
