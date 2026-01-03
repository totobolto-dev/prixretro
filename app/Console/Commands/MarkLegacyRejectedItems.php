<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MarkLegacyRejectedItems extends Command
{
    protected $signature = 'listings:mark-legacy-rejected {--dry-run : Show what would be updated}';
    protected $description = 'Mark existing listings as rejected if they appear in legacy flagged_*.json files';

    public function handle()
    {
        $this->info('📝 Marking legacy rejected items...');

        $rejectedItemIds = [];

        // Collect all item IDs from flagged_*.json files
        $flaggedFiles = File::glob(base_path('_archive/prixretro-scraper/flagged_*.json'));

        foreach ($flaggedFiles as $file) {
            $this->info("Reading " . basename($file) . "...");
            $flaggedItems = json_decode(File::get($file), true);

            foreach ($flaggedItems as $item) {
                if (!isset($item['url'])) {
                    continue;
                }

                // Extract item_id from URL
                preg_match('/\/itm\/(\d+)/', $item['url'], $matches);
                if (isset($matches[1])) {
                    $rejectedItemIds[] = $matches[1];
                }
            }
        }

        $rejectedItemIds = array_unique($rejectedItemIds);
        $this->info("Found " . count($rejectedItemIds) . " unique rejected item IDs from legacy files");

        // Update existing listings to rejected
        $updated = 0;

        foreach ($rejectedItemIds as $itemId) {
            $listing = Listing::where('item_id', $itemId)->first();

            if (!$listing) {
                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("Would mark as rejected: {$itemId} - {$listing->title}");
                $updated++;
                continue;
            }

            $listing->update([
                'status' => 'rejected',
                'reviewed_at' => now(),
                'classification_status' => 'legacy',
            ]);

            $updated++;
        }

        if ($this->option('dry-run')) {
            $this->info('🔍 Dry run - no changes made');
        }

        $this->newLine();
        $this->info("✅ " . ($this->option('dry-run') ? 'Would mark' : 'Marked') . " {$updated} items as rejected");
        $this->info("These items were flagged in the legacy scraper and won't be reimported.");

        return 0;
    }
}
