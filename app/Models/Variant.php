<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Variant extends Model
{
    protected $fillable = [
        'console_id',
        'slug',
        'name',
        'full_slug',
        'search_terms',
        'image_filename',
        'rarity_level',
        'region',
        'is_special_edition',
        'is_default',
    ];

    protected $casts = [
        'search_terms' => 'array',
        'is_special_edition' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function console(): BelongsTo
    {
        return $this->belongsTo(Console::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function approvedListings(): HasMany
    {
        return $this->hasMany(Listing::class)->where('status', 'approved');
    }

    public function currentListings(): HasMany
    {
        return $this->hasMany(CurrentListing::class);
    }

    public function priceStatistics(): HasMany
    {
        return $this->hasMany(PriceStatistic::class);
    }

    public function scrapeJobs(): HasMany
    {
        return $this->hasMany(ScrapeJob::class);
    }

    /**
     * Get the full display name for this variant (for page titles, etc.).
     * If it's the default variant, show only the console name.
     * Otherwise, show console name + variant name.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->is_default
            ? $this->console->name
            : $this->console->name . ' ' . $this->name;
    }

    /**
     * Get the short display name for this variant (for lists, cards, etc.).
     * If it's the default variant, show only the console name.
     * Otherwise, show just the variant name.
     */
    public function getShortNameAttribute(): string
    {
        return $this->is_default ? $this->console->name : $this->name;
    }
}
