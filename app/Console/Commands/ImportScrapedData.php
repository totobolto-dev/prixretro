<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportScrapedData extends Command
{
    protected $signature = 'import:scraped {file} {--dry-run : Show what would be imported without actually importing}';
    protected $description = 'Import scraped eBay data from JSON file to database';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("📥 Importing from: {$filePath}");

        try {
            $jsonData = json_decode(file_get_contents($filePath), true);

            if (!$jsonData) {
                $this->error('Invalid JSON file');
                return 1;
            }

            $imported = 0;
            $skipped = 0;
            $notFound = 0;

            foreach ($jsonData as $variantSlug => $variantData) {
                // Find variant by slug (try with and without "standard-" prefix)
                $variant = Variant::where('slug', $variantSlug)->first();

                if (!$variant && str_starts_with($variantSlug, 'standard-')) {
                    // Try without the "standard-" prefix
                    $cleanSlug = substr($variantSlug, strlen('standard-'));
                    $variant = Variant::where('slug', $cleanSlug)->first();
                }

                if (!$variant) {
                    $this->warn("⚠️  Variant not found: {$variantSlug}");
                    $notFound += count($variantData['items'] ?? []);
                    continue;
                }

                foreach ($variantData['items'] as $item) {
                    // Check if already exists
                    $exists = Listing::where('item_id', $item['item_id'])->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    if ($this->option('dry-run')) {
                        $this->line("Would import: {$item['title']} ({$item['price']}€)");
                        $imported++;
                        continue;
                    }

                    // Import new listing
                    Listing::create([
                        'variant_id' => $variant->id,
                        'item_id' => $item['item_id'],
                        'title' => $item['title'],
                        'price' => $item['price'],
                        'sold_date' => $item['sold_date'] ?? null,
                        'condition' => $item['condition'] ?? null,
                        'url' => $item['url'] ?? null,
                        'thumbnail_url' => $item['thumbnail_url'] ?? null,
                        'source' => 'ebay',
                        'status' => 'pending',
                        'is_outlier' => false,
                    ]);

                    $imported++;
                }
            }

            if ($this->option('dry-run')) {
                $this->info('🔍 Dry run - no changes made');
            }

            $this->newLine();
            $this->info("✅ Would import: {$imported} new listings");
            $this->info("⏭️  Skipped: {$skipped} existing listings");

            if ($notFound > 0) {
                $this->warn("⚠️  Not found: {$notFound} items (variant missing in DB)");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }
}
