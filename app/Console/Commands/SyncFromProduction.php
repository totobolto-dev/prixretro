<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Console;
use App\Models\Variant;
use App\Models\Listing;

class SyncFromProduction extends Command
{
    protected $signature = 'sync:from-production
                            {--console= : Sync only specific console slug}
                            {--dry-run : Show what would be synced without actually importing}';

    protected $description = 'Sync approved listings from production CloudDB to local database';

    private $stats = [
        'consoles_synced' => 0,
        'variants_synced' => 0,
        'listings_synced' => 0,
        'listings_skipped' => 0,
    ];

    public function handle()
    {
        $this->info('🔄 Syncing from Production CloudDB...');
        $this->newLine();

        // Connect to production database
        try {
            $productionDb = DB::connection('production');
            $productionDb->getPdo(); // Test connection
            $this->info('✅ Connected to production CloudDB');
        } catch (\Exception $e) {
            $this->error('❌ Failed to connect to production database');
            $this->error('   ' . $e->getMessage());
            $this->newLine();
            $this->warn('💡 Make sure you have configured the "production" connection in config/database.php');
            return Command::FAILURE;
        }

        $this->newLine();

        // Determine which consoles to sync
        if ($this->option('console')) {
            $consoleSlugs = [$this->option('console')];
            $this->info("📦 Syncing console: {$this->option('console')}");
        } else {
            $consoleSlugs = $productionDb->table('consoles')->pluck('slug')->toArray();
            $this->info('📦 Syncing all consoles (' . count($consoleSlugs) . ' total)');
        }

        $this->newLine();

        // Sync each console
        foreach ($consoleSlugs as $consoleSlug) {
            $this->syncConsole($productionDb, $consoleSlug);
        }

        // Display summary
        $this->newLine();
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('✅ SYNC COMPLETE');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Consoles synced', $this->stats['consoles_synced']],
                ['Variants synced', $this->stats['variants_synced']],
                ['Listings synced', $this->stats['listings_synced']],
                ['Listings skipped (duplicates)', $this->stats['listings_skipped']],
            ]
        );

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN - No data was actually imported');
        }

        return Command::SUCCESS;
    }

    private function syncConsole($productionDb, string $consoleSlug): void
    {
        // Get or create console locally
        $prodConsole = $productionDb->table('consoles')->where('slug', $consoleSlug)->first();

        if (!$prodConsole) {
            $this->warn("⚠️  Console not found in production: {$consoleSlug}");
            return;
        }

        $this->line("🎮 Syncing: {$prodConsole->name}");

        // Sync console
        if (!$this->option('dry-run')) {
            $localConsole = Console::updateOrCreate(
                ['slug' => $consoleSlug],
                [
                    'name' => $prodConsole->name,
                    'short_name' => $prodConsole->short_name ?? $prodConsole->name,
                    'search_term' => $prodConsole->search_term ?? $prodConsole->name,
                    'ebay_category_id' => $prodConsole->ebay_category_id ?? null,
                    'description' => $prodConsole->description ?? null,
                    'manufacturer' => $prodConsole->manufacturer ?? null,
                    'release_year' => $prodConsole->release_year ?? null,
                    'display_order' => $prodConsole->display_order ?? 0,
                    'is_active' => $prodConsole->is_active ?? true,
                ]
            );
        } else {
            $localConsole = Console::where('slug', $consoleSlug)->first();
            if (!$localConsole) {
                $this->line("   Would create console: {$prodConsole->name}");
            }
        }

        $this->stats['consoles_synced']++;

        // Get variants for this console from production
        $prodVariants = $productionDb->table('variants')
            ->where('console_id', $prodConsole->id)
            ->get();

        foreach ($prodVariants as $prodVariant) {
            $this->syncVariant($productionDb, $localConsole ?? null, $prodVariant);
        }
    }

    private function syncVariant($productionDb, ?Console $localConsole, object $prodVariant): void
    {
        // Sync variant
        if (!$this->option('dry-run') && $localConsole) {
            $localVariant = Variant::updateOrCreate(
                [
                    'console_id' => $localConsole->id,
                    'slug' => $prodVariant->slug,
                ],
                [
                    'name' => $prodVariant->name,
                    'full_slug' => $localConsole->slug . '/' . $prodVariant->slug,
                    'is_default' => $prodVariant->is_default ?? false,
                    'description' => $prodVariant->description,
                    'release_date' => $prodVariant->release_date,
                    'search_terms' => json_decode($prodVariant->search_terms),
                    'image_filename' => $prodVariant->image_filename,
                    'rarity_level' => $prodVariant->rarity_level,
                    'region' => $prodVariant->region,
                    'is_special_edition' => $prodVariant->is_special_edition,
                    'is_active' => $prodVariant->is_active,
                    'sort_order' => $prodVariant->sort_order,
                ]
            );
        } else {
            $localVariant = $localConsole ? Variant::where('slug', $prodVariant->slug)
                ->where('console_id', $localConsole->id)
                ->first() : null;

            if (!$localVariant) {
                $this->line("   Would create variant: {$prodVariant->name}");
            }
        }

        $this->stats['variants_synced']++;

        // Get APPROVED listings for this variant from production
        $prodListings = $productionDb->table('listings')
            ->where('variant_id', $prodVariant->id)
            ->where('status', 'approved')
            ->get();

        $this->line("   📊 {$prodVariant->name}: {$prodListings->count()} approved listings");

        foreach ($prodListings as $prodListing) {
            $this->syncListing($localVariant ?? null, $prodListing);
        }
    }

    private function syncListing(?Variant $localVariant, object $prodListing): void
    {
        if ($this->option('dry-run')) {
            // In dry-run, just count
            $this->stats['listings_synced']++;
            return;
        }

        if (!$localVariant) {
            // Can't import without local variant
            return;
        }

        // Check if listing already exists locally (by item_id)
        $existingListing = Listing::where('item_id', $prodListing->item_id)->first();

        if ($existingListing) {
            $this->stats['listings_skipped']++;
            return;
        }

        // Create listing locally
        Listing::create([
            'variant_id' => $localVariant->id,
            'console_slug' => $prodListing->console_slug,
            'item_id' => $prodListing->item_id,
            'title' => $prodListing->title,
            'price' => $prodListing->price,
            'sold_date' => $prodListing->sold_date,
            'item_condition' => $prodListing->item_condition ?? $prodListing->condition ?? null,
            'completeness' => $prodListing->completeness ?? null,
            'url' => $prodListing->url,
            'thumbnail_url' => $prodListing->thumbnail_url,
            'source' => $prodListing->source,
            'is_outlier' => $prodListing->is_outlier,
            'status' => 'approved', // Always approved since we only sync approved listings
            'classification_status' => $prodListing->classification_status,
            'reviewed_at' => $prodListing->reviewed_at,
            'created_at' => $prodListing->created_at,
            'updated_at' => $prodListing->updated_at,
        ]);

        $this->stats['listings_synced']++;
    }
}
