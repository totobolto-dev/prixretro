<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCollection extends Model
{
    protected $fillable = [
        'user_id',
        'variant_id',
        'completeness',
        'purchase_price',
        'purchase_date',
        'notes',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }

    // Get current market value for this variant
    public function getCurrentValue(): ?float
    {
        $listings = $this->variant->approvedListings();

        // Filter by completeness if specified
        if ($this->completeness) {
            $listings = $listings->where('completeness', $this->completeness);
        }

        $avgPrice = $listings->avg('price');

        return $avgPrice ? round($avgPrice, 2) : null;
    }

    // Calculate profit/loss
    public function getProfitLoss(): ?float
    {
        if (!$this->purchase_price) {
            return null;
        }

        $currentValue = $this->getCurrentValue();

        if (!$currentValue) {
            return null;
        }

        return round($currentValue - $this->purchase_price, 2);
    }
}
