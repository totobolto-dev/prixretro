<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;

class ExportRejectedItemIds extends Command
{
    protected $signature = 'listings:export-rejected-ids';
    protected $description = 'Export rejected item IDs to scrapers/rejected_item_ids.json for scraper to skip';

    public function handle()
    {
        $this->info('📤 Exporting rejected item IDs...');

        $rejectedIds = Listing::where('status', 'rejected')
            ->pluck('item_id')
            ->toArray();

        $filePath = base_path('scrapers/rejected_item_ids.json');

        file_put_contents(
            $filePath,
            json_encode($rejectedIds, JSON_PRETTY_PRINT)
        );

        $this->newLine();
        $this->info("✅ Exported {count($rejectedIds)} rejected item IDs");
        $this->info("📁 File: scrapers/rejected_item_ids.json");
        $this->newLine();
        $this->info("The scraper will now skip these items automatically.");

        return 0;
    }
}
