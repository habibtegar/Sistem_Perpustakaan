<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_code',
        'name',
        'class',
        'email',
        'phone',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function activeBorrowings(): HasMany
    {
        return $this->hasMany(Transaction::class)->where('status', 'Dipinjam');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }
}
