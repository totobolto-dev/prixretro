<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Listing extends Model
{
    protected $fillable = [
        'variant_id',
        'item_id',
        'title',
        'price',
        'sold_date',
        'condition',
        'url',
        'thumbnail_url',
        'source',
        'is_outlier',
        'status',
        'reviewed_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sold_date' => 'date',
        'is_outlier' => 'boolean',
        'reviewed_at' => 'datetime',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }
}
