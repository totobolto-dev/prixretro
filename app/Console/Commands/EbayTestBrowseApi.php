<?php

namespace App\Console\Commands;

use App\Services\EbayBrowseService;
use Illuminate\Console\Command;

class EbayTestBrowseApi extends Command
{
    protected $signature = 'ebay:test-browse {keywords}';
    protected $description = 'Test eBay Browse API search (OAuth 2.0)';

    public function handle(EbayBrowseService $ebayService): int
    {
        $keywords = $this->argument('keywords');

        $this->info("Searching eBay Browse API for: {$keywords}");
        $this->newLine();

        // Test completed items
        $this->info('=== COMPLETED ITEMS (Sold Listings - Last 90 Days) ===');
        $result = $ebayService->findCompletedItems($keywords, 10);

        if ($result['error']) {
            $this->error("Error: {$result['error']}");
            return Command::FAILURE;
        }

        $this->info("Found {$result['total']} total results");
        $this->newLine();

        foreach ($result['items'] as $item) {
            $parsed = $ebayService->parseItem($item);

            // Skip invalid items (0€ prices filtered in parseItem)
            if ($parsed === null) {
                continue;
            }

            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line("Title: {$parsed['title']}");
            $this->line("Price: {$parsed['price']}€");
            if ($parsed['sold_date']) {
                $this->line("Sold: {$parsed['sold_date']->format('Y-m-d H:i')}");
            }
            $this->line("Condition: {$parsed['item_condition']}");
            $this->line("URL: {$parsed['url']}");
            $this->newLine();
        }

        // Test active items
        $this->info('=== ACTIVE ITEMS (Current Listings) ===');
        $activeResult = $ebayService->findActiveItems($keywords, 5);

        if ($activeResult['error']) {
            $this->error("Error: {$activeResult['error']}");
            return Command::FAILURE;
        }

        $this->info("Found {$activeResult['total']} active listings");
        $this->newLine();

        foreach ($activeResult['items'] as $item) {
            $parsed = $ebayService->parseItem($item);

            // Skip invalid items
            if ($parsed === null) {
                continue;
            }

            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line("Title: {$parsed['title']}");
            $this->line("Price: {$parsed['price']}€");
            $this->line("Condition: {$parsed['item_condition']}");
            $this->newLine();
        }

        $this->info('✓ Browse API test completed successfully!');

        return Command::SUCCESS;
    }
}
