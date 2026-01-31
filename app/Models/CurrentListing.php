<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CurrentListing extends Model
{
    protected static function booted(): void
    {
        static::updated(function (CurrentListing $listing) {
            // When a listing is marked as sold, create a pending Listing entry
            if ($listing->isDirty('is_sold') && $listing->is_sold) {
                Listing::create([
                    'variant_id' => $listing->variant_id,
                    'item_id' => $listing->item_id,
                    'title' => $listing->title,
                    'price' => $listing->price,
                    'sold_date' => now(),
                    'completeness' => 'loose', // Default, can be changed during review
                    'status' => 'pending',
                    'url' => $listing->url,
                    'thumbnail_url' => $listing->thumbnail_url,
                ]);
            }
        });
    }

    protected $fillable = [
        'variant_id',
        'item_id',
        'title',
        'price',
        'url',
        'thumbnail_url',
        'status',
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
