<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'published_year',
        'category',
        'category_id',
        'stock',
        'description',
    ];

    protected $casts = [
        'published_year' => 'integer',
        'stock' => 'integer',
    ];

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function activeBorrowings(): HasMany
    {
        return $this->hasMany(Transaction::class)->where('status', 'Dipinjam');
    }

    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }

    public function getCategoryNameAttribute(): string
    {
        if ($this->categoryRelation) {
            return $this->categoryRelation->name;
        }

        return $this->category ?: 'Umum';
    }
}
