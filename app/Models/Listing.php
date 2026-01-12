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
        'url_validation_status',
        'url_redirect_target',
        'url_validation_error',
        'url_validated_at',
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
        'url_validated_at' => 'datetime',
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

    public function scopeUrlValid(Builder $query): Builder
    {
        return $query->where('url_validation_status', 'valid');
    }

    public function scopeUrlInvalid(Builder $query): Builder
    {
        return $query->where('url_validation_status', 'invalid');
    }

    public function scopeUrlNotValidated(Builder $query): Builder
    {
        return $query->whereNull('url_validation_status')
            ->orWhere('url_validation_status', 'pending');
    }

    public function isUrlValid(): bool
    {
        return $this->url_validation_status === 'valid';
    }

    public function needsUrlValidation(): bool
    {
        return is_null($this->url_validation_status)
            || $this->url_validation_status === 'pending'
            || $this->url_validation_status === 'error';
    }
}
