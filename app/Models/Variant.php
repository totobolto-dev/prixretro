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
        'rarity_level',
        'region',
        'is_special_edition',
        'is_default',
    ];

    protected $casts = [
        'search_terms' => 'array',
        'is_special_edition' => 'boolean',
        'is_default' => 'boolean',
        'current_listings_fetched_at' => 'datetime',
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
     * If the variant name already starts with the console name, use as-is to avoid duplication.
     * Otherwise, show console name + variant name.
     */
    public function getDisplayNameAttribute(): string
    {
        // Default variants: show only console name (e.g., "NES" not "NES NES")
        if ($this->is_default) {
            return $this->console->name;
        }

        // If variant name already contains console name, use as-is
        // (e.g., "Mega Drive (Europe PAL)" not "Mega Drive Mega Drive (Europe PAL)")
        if (str_starts_with($this->name, $this->console->name)) {
            return $this->name;
        }

        // Otherwise, concatenate console name + variant name
        // (e.g., "Game Boy Color" + "Blue" = "Game Boy Color Blue")
        return $this->console->name . ' ' . $this->name;
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

    /**
     * Get the image URL for this variant if it exists.
     * Convention: public/storage/variants/{console-slug}_{variant-slug}.{ext}
     * Checks for webp, avif, png, jpg, jpeg in that order (prefer modern formats).
     */
    public function getImageUrlAttribute(): ?string
    {
        $expectedFilename = $this->console->slug . '_' . $this->slug;
        $extensions = ['webp', 'avif', 'png', 'jpg', 'jpeg'];

        foreach ($extensions as $ext) {
            $filename = $expectedFilename . '.' . $ext;
            if (file_exists(public_path("storage/variants/{$filename}"))) {
                return "/storage/variants/{$filename}";
            }
        }

        return null; // No image found
    }

    /**
     * Check if variant has an image.
     */
    public function hasImage(): bool
    {
        return $this->image_url !== null;
    }

    /**
     * Generate a slug from variant name, removing console name prefix if present.
     * This keeps URLs clean: /mega-drive/europe-pal instead of /mega-drive/mega-drive-europe-pal
     *
     * @param string $variantName The variant name (e.g., "Mega Drive (Europe PAL)" or "Blue")
     * @param string $consoleName The console name (e.g., "Mega Drive")
     * @return string The slugified variant name without console prefix
     */
    public static function generateSlugFromName(string $variantName, string $consoleName): string
    {
        // If variant name starts with console name, strip it
        if (str_starts_with($variantName, $consoleName)) {
            // Remove console name and clean up any leading spaces/parentheses
            $cleanedName = trim(substr($variantName, strlen($consoleName)));
        } else {
            $cleanedName = $variantName;
        }

        return \Illuminate\Support\Str::slug($cleanedName);
    }
}
