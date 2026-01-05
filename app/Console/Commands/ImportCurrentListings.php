<?php

namespace App\Console\Commands;

use App\Models\CurrentListing;
use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportCurrentListings extends Command
{
    protected $signature = 'import:current-listings {file}';
    protected $description = 'Import scraped current listings from JSON file to database';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!Storage::exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $data = json_decode(Storage::get($filePath), true);

        if (!$data) {
            $this->error('Invalid JSON file');
            return 1;
        }

        $imported = 0;
        $skipped = 0;

        foreach ($data as $consoleSlug => $consoleData) {
            $this->info("Processing console: {$consoleData['console_name']}");

            foreach ($consoleData['listings'] as $item) {
                // Check if already exists
                if (CurrentListing::where('item_id', $item['item_id'])->exists()) {
                    $skipped++;
                    continue;
                }

                // Create as pending (no variant assigned yet)
                CurrentListing::create([
                    'variant_id' => null, // Will be assigned in admin
                    'item_id' => $item['item_id'],
                    'title' => $item['title'],
                    'price' => $item['price'],
                    'url' => $item['url'],
                    'status' => 'pending',
                    'is_sold' => false,
                    'last_seen_at' => now(),
                ]);

                $imported++;
            }
        }

        $this->info("✅ Imported: {$imported} | Skipped: {$skipped}");
        return 0;
    }
}
