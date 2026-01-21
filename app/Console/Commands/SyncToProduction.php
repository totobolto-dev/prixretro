<?php

namespace App\Console\Commands;

use App\Models\Console;
use App\Models\Listing;
use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncToProduction extends Command
{
    protected $signature = 'sync:production {--dry-run : Show what would be synced without actually syncing}';
    protected $description = 'Sync approved listings from local DB to production CloudDB';

    public function handle()
    {
        $this->info('🚀 Syncing to Production CloudDB...');

        // Connect to production database
        $productionDb = $this->getProductionConnection();

        if (!$productionDb) {
            $this->error('Failed to connect to production database');
            return 1;
        }

        // Get recently approved listings (last 30 days)
        $approvedListings = Listing::with('variant.console')
            ->where('status', 'approved')
            ->where('updated_at', '>=', now()->subDays(30))
            ->get();

        if ($approvedListings->isEmpty()) {
            $this->info('✅ No new approved listings to sync');
            return 0;
        }

        $this->info("Found {$approvedListings->count()} approved listings to sync");

        if ($this->option('dry-run')) {
            $this->table(
                ['Variant', 'Title', 'Price', 'Date'],
                $approvedListings->map(fn($l) => [
                    $l->variant->name,
                    substr($l->title, 0, 50),
                    $l->price . '€',
                    $l->sold_date?->format('Y-m-d'),
                ])
            );
            $this->info('🔍 Dry run - no changes made');
            return 0;
        }

        $synced = 0;
        $skipped = 0;

        foreach ($approvedListings as $listing) {
            // Find matching variant in production by full_slug
            $prodVariant = $productionDb->table('variants')
                ->where('full_slug', $listing->variant->full_slug)
                ->first();

            if (!$prodVariant) {
                $this->warn("⚠️  Variant not found in production: {$listing->variant->full_slug}");
                $skipped++;
                continue;
            }

            // Check if listing already exists
            $exists = $productionDb->table('listings')
                ->where('item_id', $listing->item_id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Insert into production
            $productionDb->table('listings')->insert([
                'variant_id' => $prodVariant->id,
                'console_slug' => $listing->console_slug,
                'item_id' => $listing->item_id,
                'title' => $listing->title,
                'price' => $listing->price,
                'sold_date' => $listing->sold_date,
                'item_condition' => $listing->item_condition,
                'completeness' => $listing->completeness,
                'url' => $listing->url,
                'thumbnail_url' => $listing->thumbnail_url,
                'source' => $listing->source ?? 'ebay',
                'is_outlier' => $listing->is_outlier,
                'status' => 'approved',
                'reviewed_at' => $listing->reviewed_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $synced++;
        }

        $this->info("✅ Synced {$synced} new listings");
        $this->info("⏭️  Skipped {$skipped} existing listings");

        return 0;
    }

    private function getProductionConnection()
    {
        try {
            return DB::connection('production');
        } catch (\Exception $e) {
            $this->error("Connection error: " . $e->getMessage());
            return null;
        }
    }
}
