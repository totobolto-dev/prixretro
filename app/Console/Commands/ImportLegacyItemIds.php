<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportLegacyItemIds extends Command
{
    protected $signature = 'import:legacy-item-ids {--dry-run : Show what would be imported}';
    protected $description = 'Import item_ids from legacy JSON files to prevent reimporting already processed items';

    public function handle()
    {
        $this->info('📥 Importing legacy item IDs...');

        $imported = 0;
        $skipped = 0;

        // 1. Import from scraped_data.json (approved items)
        $scrapedDataPath = base_path('_archive/prixretro-scraper/scraped_data.json');
        if (File::exists($scrapedDataPath)) {
            $this->info('Processing scraped_data.json...');
            $data = json_decode(File::get($scrapedDataPath), true);

            foreach ($data as $variantKey => $variantData) {
                if (!isset($variantData['listings'])) {
                    continue;
                }

                foreach ($variantData['listings'] as $listing) {
                    if (!isset($listing['item_id'])) {
                        continue;
                    }

                    // Check if already exists
                    if (Listing::where('item_id', $listing['item_id'])->exists()) {
                        $skipped++;
                        continue;
                    }

                    if ($this->option('dry-run')) {
                        $this->line("Would import (approved): {$listing['item_id']} - {$listing['title']}");
                        $imported++;
                        continue;
                    }

                    // Create as approved listing (legacy processed items)
                    try {
                        Listing::create([
                            'variant_id' => null,
                            'console_slug' => 'game-boy-color',
                            'item_id' => $listing['item_id'],
                            'title' => $listing['title'] ?? '',
                            'price' => $listing['price'] ?? null,
                            'sold_date' => $listing['sold_date'] ?? null,
                            'condition' => $listing['condition'] ?? null,
                            'url' => $listing['url'] ?? null,
                            'thumbnail_url' => null,
                            'source' => 'ebay',
                            'status' => 'approved',
                            'classification_status' => 'legacy',
                            'reviewed_at' => now(),
                            'is_outlier' => false,
                        ]);

                        $imported++;
                    } catch (\Exception $e) {
                        $this->error("Failed to import {$listing['item_id']}: " . $e->getMessage());
                        $skipped++;
                    }
                }
            }
        }

        // 2. Import from flagged_*.json files (rejected items)
        $flaggedFiles = File::glob(base_path('_archive/prixretro-scraper/flagged_*.json'));

        foreach ($flaggedFiles as $file) {
            $this->info("Processing " . basename($file) . "...");
            $flaggedItems = json_decode(File::get($file), true);

            foreach ($flaggedItems as $item) {
                // Extract item_id from URL
                if (!isset($item['url'])) {
                    continue;
                }

                preg_match('/\/itm\/(\d+)/', $item['url'], $matches);
                if (!isset($matches[1])) {
                    continue;
                }

                $itemId = $matches[1];

                // Check if already exists
                if (Listing::where('item_id', $itemId)->exists()) {
                    $skipped++;
                    continue;
                }

                if ($this->option('dry-run')) {
                    $this->line("Would import (rejected): {$itemId} - {$item['title']}");
                    $imported++;
                    continue;
                }

                // Create as rejected listing
                try {
                    Listing::create([
                        'variant_id' => null,
                        'console_slug' => 'game-boy-color',
                        'item_id' => $itemId,
                        'title' => $item['title'] ?? '',
                        'price' => $item['price'] ?? null,
                        'sold_date' => null,
                        'condition' => null,
                        'url' => $item['url'] ?? null,
                        'thumbnail_url' => null,
                        'source' => 'ebay',
                        'status' => 'rejected',
                        'classification_status' => 'legacy',
                        'reviewed_at' => now(),
                        'is_outlier' => false,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $this->error("Failed to import rejected {$itemId}: " . $e->getMessage());
                    $skipped++;
                }
            }
        }

        if ($this->option('dry-run')) {
            $this->info('🔍 Dry run - no changes made');
            $this->newLine();
            $this->info("✅ Would import: {$imported} legacy item IDs");
        } else {
            $this->newLine();
            $this->info("✅ Imported: {$imported} legacy item IDs");
        }

        $this->info("⏭️  Skipped: {$skipped} already in database");
        $this->newLine();
        $this->info('This prevents reimporting already processed Game Boy Color items.');

        return 0;
    }
}
