<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportGbaListings extends Command
{
    protected $signature = 'import:gba-listings {--fresh : Wipe existing listings before import}';

    protected $description = 'Import GBA listings from legacy-python/scraped_data_gba.json';

    public function handle()
    {
        $dataPath = base_path('legacy-python/scraped_data_gba.json');

        if (!File::exists($dataPath)) {
            $this->error("Data file not found: {$dataPath}");
            return 1;
        }

        $data = json_decode(File::get($dataPath), true);

        if ($this->option('fresh')) {
            $this->info('Wiping existing listings...');
            Listing::query()->delete();
        }

        $totalListings = 0;
        $skippedVariants = 0;

        foreach ($data as $variantSlug => $variantData) {
            $fullSlug = 'game-boy-advance/' . $variantSlug;

            $variant = Variant::where('full_slug', $fullSlug)->first();

            if (!$variant) {
                $this->warn("Variant not found: {$fullSlug} - skipping");
                $skippedVariants++;
                continue;
            }

            $items = $variantData['items'] ?? [];
            $count = count($items);

            $this->info("Importing {$count} listings for: {$variant->name}");

            foreach ($items as $item) {
                Listing::updateOrCreate(
                    ['item_id' => $item['item_id']],
                    [
                        'variant_id' => $variant->id,
                        'title' => $item['title'],
                        'price' => $item['price'],
                        'sold_date' => $item['sold_date'] ?? null,
                        'condition' => $item['condition'] ?? null,
                        'url' => $item['url'],
                        'thumbnail_url' => $item['thumbnail_url'] ?? null,
                        'source' => 'ebay',
                        'is_outlier' => false,
                        'status' => 'approved',
                        'reviewed_at' => now(),
                    ]
                );

                $totalListings++;
            }
        }

        $this->newLine();
        $this->info("Import complete!");
        $this->info("Total listings imported: {$totalListings}");

        if ($skippedVariants > 0) {
            $this->warn("Skipped variants (not found in database): {$skippedVariants}");
        }

        return 0;
    }
}
