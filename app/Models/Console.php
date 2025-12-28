<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Console extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'short_name',
        'search_term',
        'ebay_category_id',
        'description',
        'release_year',
        'manufacturer',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'release_year' => 'integer',
        'display_order' => 'integer',
    ];

    public function variants(): HasMany
    {
        return $this->hasMany(Variant::class);
    }

    public function scrapeJobs(): HasMany
    {
        return $this->hasManyThrough(ScrapeJob::class, Variant::class);
    }
}
