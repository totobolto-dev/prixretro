<?php

namespace App\Console\Commands;

use App\Services\EbayFindingService;
use Illuminate\Console\Command;

class EbayTestSearch extends Command
{
    protected $signature = 'ebay:test-search {keywords}';
    protected $description = 'Test eBay Finding API search';

    public function handle(EbayFindingService $ebayService): int
    {
        $keywords = $this->argument('keywords');

        $this->info("Searching eBay for: {$keywords}");
        $this->newLine();

        // Test completed items
        $this->info('=== COMPLETED ITEMS (Sold Listings) ===');
        $result = $ebayService->findCompletedItems($keywords, 10);

        if ($result['error']) {
            $this->error("Error: {$result['error']}");
            return Command::FAILURE;
        }

        $this->info("Found {$result['totalEntries']} total results ({$result['totalPages']} pages)");
        $this->newLine();

        foreach ($result['items'] as $item) {
            $parsed = $ebayService->parseItem($item);

            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line("Title: {$parsed['title']}");
            $this->line("Price: {$parsed['price']}€");
            $this->line("Sold: {$parsed['sold_date']->format('Y-m-d H:i')}");
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

        $this->info("Found " . count($activeResult['items']) . " active listings");
        $this->newLine();

        foreach ($activeResult['items'] as $item) {
            $parsed = $ebayService->parseItem($item);

            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line("Title: {$parsed['title']}");
            $this->line("Price: {$parsed['price']}€");
            $this->line("Condition: {$parsed['item_condition']}");
            $this->newLine();
        }

        $this->info('✓ Test completed successfully!');

        return Command::SUCCESS;
    }
}
