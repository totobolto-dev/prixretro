<?php

namespace App\Console\Commands;

use App\Models\Console;
use App\Models\Listing;
use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGbcListings extends Command
{
    protected $signature = 'import:gbc-listings {--fresh : Clear existing GBC listings first}';

    protected $description = 'Import GBC listings from scraped_data.json';

    public function handle()
    {
        $filePath = base_path('legacy-python/scraped_data.json');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $data = json_decode(file_get_contents($filePath), true);

        if (empty($data)) {
            $this->error('No data found in file');
            return 1;
        }

        $console = Console::where('slug', 'game-boy-color')->first();

        if (!$console) {
            $this->error('Game Boy Color console not found');
            return 1;
        }

        if ($this->option('fresh')) {
            $this->info('Clearing existing GBC listings...');
            Listing::whereHas('variant', function ($query) use ($console) {
                $query->where('console_id', $console->id);
            })->delete();
        }

        $this->info("Importing GBC listings from " . count($data) . " variants...");
        $importedCount = 0;
        $skippedCount = 0;
        $variantStats = [];

        DB::beginTransaction();

        try {
            foreach ($data as $variantKey => $variantData) {
                $fullSlug = 'game-boy-color/' . $variantKey;
                $variant = Variant::where('full_slug', $fullSlug)->first();

                if (!$variant) {
                    $this->warn("Variant not found: {$fullSlug}");
                    $skippedListings = count($variantData['listings'] ?? []);
                    $skippedCount += $skippedListings;
                    continue;
                }

                $listings = $variantData['listings'] ?? [];

                foreach ($listings as $listing) {
                    Listing::updateOrCreate(
                        ['item_id' => $listing['item_id']],
                        [
                            'variant_id' => $variant->id,
                            'title' => $listing['title'],
                            'price' => $listing['price'],
                            'sold_date' => $listing['sold_date'] ?? null,
                            'condition' => $listing['condition'] ?? null,
                            'url' => $listing['url'],
                            'source' => 'ebay',
                            'status' => 'approved',
                            'reviewed_at' => now(),
                        ]
                    );

                    $variantStats[$variant->name] = ($variantStats[$variant->name] ?? 0) + 1;
                    $importedCount++;
                }
            }

            DB::commit();

            $this->info("\n✅ Import complete!");
            $this->info("Imported: {$importedCount} listings");
            if ($skippedCount > 0) {
                $this->warn("Skipped: {$skippedCount} listings (variant not found)");
            }

            $this->info("\nListings per variant:");
            foreach ($variantStats as $variantName => $count) {
                $this->info("  - {$variantName}: {$count}");
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: {$e->getMessage()}");
            return 1;
        }

        return 0;
    }
}
