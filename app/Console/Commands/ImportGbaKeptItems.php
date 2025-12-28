<?php

namespace App\Console\Commands;

use App\Models\Console;
use App\Models\Listing;
use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGbaKeptItems extends Command
{
    protected $signature = 'import:gba-kept-items {--fresh : Clear existing GBA listings first}';

    protected $description = 'Import GBA listings from gba_kept_items_fixed_dates.json with custom variant names';

    public function handle()
    {
        $filePath = base_path('legacy-python/gba_kept_items_fixed_dates.json');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $data = json_decode(file_get_contents($filePath), true);
        $items = $data['items'] ?? [];

        if (empty($items)) {
            $this->error('No items found in file');
            return 1;
        }

        $console = Console::where('slug', 'game-boy-advance')->first();

        if (!$console) {
            $this->error('Game Boy Advance console not found');
            return 1;
        }

        if ($this->option('fresh')) {
            $this->info('Clearing existing GBA listings...');
            Listing::whereHas('variant', function ($query) use ($console) {
                $query->where('console_id', $console->id);
            })->delete();
        }

        $itemCount = count($items);
        $this->info("Importing {$itemCount} GBA items...");
        $importedCount = 0;
        $skippedCount = 0;
        $variantStats = [];

        DB::beginTransaction();

        try {
            foreach ($items as $item) {
                $variantSlug = $item['assigned_variant'];

                // Remove "standard-" prefix for basic variants
                $cleanSlug = str_replace('standard-', '', $variantSlug);
                $fullSlug = 'game-boy-advance/' . $cleanSlug;

                $variant = Variant::where('full_slug', $fullSlug)->first();

                if (!$variant) {
                    $this->warn("Variant not found: {$fullSlug} (from {$variantSlug})");
                    $skippedCount++;
                    continue;
                }

                Listing::updateOrCreate(
                    ['item_id' => $item['id']],
                    [
                        'variant_id' => $variant->id,
                        'title' => $item['title'],
                        'price' => $item['price'],
                        'sold_date' => $item['date'] ?? null,
                        'condition' => trim(str_replace('|', '', $item['condition'] ?? '')),
                        'url' => $item['url'],
                        'source' => 'ebay',
                        'status' => 'approved',
                        'reviewed_at' => now(),
                    ]
                );

                $variantStats[$variant->name] = ($variantStats[$variant->name] ?? 0) + 1;
                $importedCount++;
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
