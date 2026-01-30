<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Services\EbayBrowseService;
use Illuminate\Console\Command;

class EbayScrapeConsole extends Command
{
    protected $signature = 'ebay:scrape-console
                            {search_term : Search keywords (e.g., "game boy color")}
                            {console_slug : Console slug for output file (e.g., gbc, gba)}
                            {--days=7 : Number of days to scrape (default: 7)}
                            {--limit=1000 : Max items to scrape (default: 1000)}';

    protected $description = 'Scrape eBay sold listings using Browse API (OAuth 2.0) - outputs JSON like Python scraper';

    public function handle(EbayBrowseService $ebayService): int
    {
        $searchTerm = $this->argument('search_term');
        $consoleSlug = $this->argument('console_slug');
        $days = (int) $this->option('days');
        $limit = (int) $this->option('limit');

        $this->line("════════════════════════════════════════════════════════════════════");
        $this->info("🎮 SCRAPING EBAY.FR: " . strtoupper($searchTerm));
        $this->line("════════════════════════════════════════════════════════════════════");
        $this->newLine();

        $this->info("Settings:");
        $this->line("  Search: {$searchTerm}");
        $this->line("  Days: Last {$days} days");
        $this->line("  Limit: {$limit} items max");
        $this->line("  Output: scraped_data_{$consoleSlug}.json");
        $this->newLine();

        // Load existing item IDs to skip duplicates
        $seenIds = $this->loadExistingIds($consoleSlug);
        $this->info("📋 Loaded " . count($seenIds) . " existing item IDs to skip");
        $this->newLine();

        // Scrape items
        $allItems = [];
        $offset = 0;
        $pageSize = 200; // API max
        $page = 1;

        while (count($allItems) < $limit) {
            $this->info("📄 Page {$page} (offset: {$offset})");

            $result = $ebayService->findCompletedItems($searchTerm, $pageSize, $offset);

            if ($result['error']) {
                $this->error("  ❌ API Error: {$result['error']}");
                break;
            }

            if (empty($result['items'])) {
                $this->warn("  🏁 No more results");
                break;
            }

            $pageItems = 0;
            foreach ($result['items'] as $item) {
                $parsed = $ebayService->parseItem($item);

                // Skip invalid items (0€ prices, etc.)
                if ($parsed === null) {
                    continue;
                }

                // Skip if already seen
                $itemId = $parsed['ebay_item_id'];
                if (in_array($itemId, $seenIds)) {
                    continue;
                }

                // Convert to Python scraper format
                $itemData = [
                    'item_id' => $itemId,
                    'title' => $parsed['title'],
                    'price' => $parsed['price'],
                    'sold_date' => $parsed['sold_date'] ? $parsed['sold_date']->format('Y-m-d') : null,
                    'condition' => $parsed['item_condition'],
                    'url' => $parsed['url'],
                ];

                // Add thumbnail if available
                if (isset($parsed['thumbnail_url'])) {
                    $itemData['thumbnail_url'] = $parsed['thumbnail_url'];
                }

                $allItems[] = $itemData;
                $seenIds[] = $itemId;
                $pageItems++;

                if (count($allItems) >= $limit) {
                    break;
                }
            }

            $this->line("  ✅ Extracted {$pageItems} new items (total: " . count($allItems) . ")");

            // Stop if we got few items (end of results)
            if ($pageItems < 50) {
                $this->warn("  🏁 Last page reached (only {$pageItems} items)");
                break;
            }

            $offset += $pageSize;
            $page++;

            // Rate limiting (be nice to eBay)
            sleep(1);
        }

        // Save to JSON file (same format as Python scraper)
        $outputFile = "scraped_data_{$consoleSlug}.json";
        $outputPath = storage_path("app/{$outputFile}");
        file_put_contents($outputPath, json_encode($allItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->newLine();
        $this->line("════════════════════════════════════════════════════════════════════");
        $this->info("✅ SCRAPING COMPLETE");
        $this->line("════════════════════════════════════════════════════════════════════");
        $this->info("Total items: " . count($allItems));
        $this->info("Pages scraped: {$page}");
        $this->info("Saved to: storage/app/{$outputFile}");
        $this->newLine();

        $this->info("Next steps:");
        $this->line("  1. Import: php artisan import:raw storage/app/{$outputFile}");
        $this->line("  2. Classify: Visit /admin/sort-listings");
        $this->line("  3. Approve: Visit /admin/listings");
        $this->newLine();

        return Command::SUCCESS;
    }

    /**
     * Load existing item IDs from:
     * 1. Database (already imported) - if available
     * 2. Existing JSON file (scraped but not imported yet)
     * 3. Rejected items file
     */
    private function loadExistingIds(string $consoleSlug): array
    {
        $seenIds = [];

        // 1. From database (if available)
        try {
            $dbIds = Listing::pluck('item_id')->toArray();
            $seenIds = array_merge($seenIds, $dbIds);
        } catch (\Exception $e) {
            // Database not available (running locally), skip
        }

        // 2. From existing JSON file
        $jsonFile = storage_path("app/scraped_data_{$consoleSlug}.json");
        if (file_exists($jsonFile)) {
            $existingData = json_decode(file_get_contents($jsonFile), true);
            if ($existingData) {
                $jsonIds = array_column($existingData, 'item_id');
                $seenIds = array_merge($seenIds, $jsonIds);
            }
        }

        // 3. From rejected items
        $rejectedFile = base_path('scrapers/rejected_item_ids.json');
        if (file_exists($rejectedFile)) {
            $rejectedIds = json_decode(file_get_contents($rejectedFile), true);
            if ($rejectedIds) {
                $seenIds = array_merge($seenIds, $rejectedIds);
            }
        }

        return array_unique($seenIds);
    }
}
