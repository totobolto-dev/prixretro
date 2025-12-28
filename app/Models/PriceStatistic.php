<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceStatistic extends Model
{
    protected $fillable = [
        'variant_id',
        'period',
        'avg_price',
        'min_price',
        'max_price',
        'median_price',
        'count',
        'last_calculated_at',
    ];

    protected $casts = [
        'avg_price' => 'decimal:2',
        'min_price' => 'decimal:2',
        'max_price' => 'decimal:2',
        'median_price' => 'decimal:2',
        'count' => 'integer',
        'last_calculated_at' => 'datetime',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
