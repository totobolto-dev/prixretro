<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrentListing extends Model
{
    protected $fillable = [
        'variant_id',
        'item_id',
        'title',
        'price',
        'url',
        'is_sold',
        'last_seen_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_sold' => 'boolean',
        'last_seen_at' => 'datetime',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
