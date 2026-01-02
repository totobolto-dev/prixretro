<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;

class ImportRawScrapedData extends Command
{
    protected $signature = 'import:raw {file} {--dry-run : Show what would be imported}';
    protected $description = 'Import raw scraped data as unclassified listings (for sorting later)';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $this->info("📥 Importing raw data from: {$filePath}");

        try {
            $jsonData = json_decode(file_get_contents($filePath), true);

            if (!$jsonData) {
                $this->error('Invalid JSON file');
                return 1;
            }

            $imported = 0;
            $skipped = 0;

            // Handle both array of items and nested structure
            $items = $this->extractItems($jsonData);

            foreach ($items as $item) {
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

                // Import as unclassified listing
                Listing::create([
                    'variant_id' => null,  // Not yet classified
                    'console_slug' => null,  // To be assigned during sorting
                    'item_id' => $item['item_id'],
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'sold_date' => $item['sold_date'] ?? null,
                    'condition' => $item['condition'] ?? null,
                    'url' => $item['url'] ?? null,
                    'thumbnail_url' => $item['thumbnail_url'] ?? null,
                    'source' => 'ebay',
                    'status' => 'pending',  // Will need review after classification
                    'classification_status' => 'unclassified',  // Needs sorting first
                    'is_outlier' => false,
                ]);

                $imported++;
            }

            if ($this->option('dry-run')) {
                $this->info('🔍 Dry run - no changes made');
            }

            $this->newLine();
            $this->info("✅ Would import: {$imported} new listings");
            $this->info("⏭️  Skipped: {$skipped} existing listings");
            $this->newLine();
            $this->info('Next steps:');
            $this->info('  1. Visit: /admin/sort-listings');
            $this->info('  2. Classify items by console and variant');
            $this->info('  3. Review and approve in /admin/listings');

            return 0;

        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Extract items from JSON data
     * Handles both flat arrays and nested structures
     */
    private function extractItems(array $data): array
    {
        // If it's already a flat array of items
        if (isset($data[0]) && isset($data[0]['item_id'])) {
            return $data;
        }

        // If it's a nested structure (variant_key => {items: []})
        $items = [];
        foreach ($data as $key => $value) {
            if (isset($value['items'])) {
                $items = array_merge($items, $value['items']);
            } elseif (isset($value['listings'])) {
                $items = array_merge($items, $value['listings']);
            }
        }

        // If still empty, might be flat structure
        if (empty($items) && isset($data['item_id'])) {
            return [$data];
        }

        return $items;
    }
}
