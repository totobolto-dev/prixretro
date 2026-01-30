<?php

namespace App\Console\Commands;

use App\Models\Variant;
use App\Models\CurrentListing;
use App\Services\EbayBrowseService;
use Illuminate\Console\Command;

class FetchCurrentListings extends Command
{
    protected $signature = 'fetch:current-listings
                            {--variant= : Specific variant ID to fetch}
                            {--limit=10 : Max items per variant (default: 10)}';

    protected $description = 'Fetch current eBay listings for all variants using Browse API';

    public function handle(EbayBrowseService $ebayService): int
    {
        $limit = (int) $this->option('limit');
        $variantId = $this->option('variant');

        if ($variantId) {
            $variants = Variant::where('id', $variantId)->get();
        } else {
            $variants = Variant::whereHas('listings', function($q) {
                $q->where('status', 'approved');
            })->get();
        }

        if ($variants->isEmpty()) {
            $this->error('No variants found');
            return Command::FAILURE;
        }

        $this->info("🔄 Fetching current listings for {$variants->count()} variants...");
        $this->newLine();

        $totalFetched = 0;
        $totalNew = 0;
        $totalUpdated = 0;

        foreach ($variants as $variant) {
            $searchTerm = "{$variant->console->name} {$variant->name}";
            $this->line("📦 {$variant->console->name} - {$variant->name}");

            // Calculate price range (±20% of average loose price)
            $avgLoosePrice = \App\Models\Listing::where('variant_id', $variant->id)
                ->where('status', 'approved')
                ->where('completeness', 'loose')
                ->avg('price');

            $minPrice = null;
            $maxPrice = null;

            if ($avgLoosePrice) {
                $minPrice = $avgLoosePrice * 0.8;  // -20%
                $maxPrice = $avgLoosePrice * 1.2;  // +20%
                $this->line("  💰 Price range: {$minPrice}€ - {$maxPrice}€ (avg loose: {$avgLoosePrice}€)");
            } else {
                $this->line("  ⚠️  No loose price data, fetching without price filter");
            }

            // Fetch active listings from eBay
            $result = $ebayService->findActiveItems($searchTerm, $limit, 0, $minPrice, $maxPrice);

            if ($result['error']) {
                $this->error("  ❌ API Error: {$result['error']}");
                continue;
            }

            $fetched = 0;
            $new = 0;
            $updated = 0;

            foreach ($result['items'] as $item) {
                $parsed = $ebayService->parseItem($item);

                if ($parsed === null) {
                    continue;
                }

                // Check if listing already exists
                $existing = CurrentListing::where('item_id', $parsed['ebay_item_id'])->first();

                if ($existing) {
                    // Update price and last_seen_at
                    $existing->update([
                        'price' => $parsed['price'],
                        'last_seen_at' => now(),
                    ]);
                    $updated++;
                } else {
                    // Create new listing (pending approval)
                    $listingData = [
                        'variant_id' => $variant->id,
                        'item_id' => $parsed['ebay_item_id'],
                        'title' => $parsed['title'],
                        'price' => $parsed['price'],
                        'url' => $parsed['url'],
                        'status' => 'pending',
                        'is_sold' => false,
                        'last_seen_at' => now(),
                    ];

                    if (isset($parsed['thumbnail_url'])) {
                        $listingData['thumbnail_url'] = $parsed['thumbnail_url'];
                    }

                    CurrentListing::create($listingData);
                    $new++;
                }

                $fetched++;
            }

            $totalFetched += $fetched;
            $totalNew += $new;
            $totalUpdated += $updated;

            $this->line("  ✅ Fetched: {$fetched} | New: {$new} | Updated: {$updated}");

            // Rate limiting (be nice to eBay)
            sleep(2);
        }

        // Mark old listings as sold (not seen in last 24 hours)
        $markedSold = CurrentListing::where('is_sold', false)
            ->where('last_seen_at', '<', now()->subHours(24))
            ->update(['is_sold' => true]);

        $this->newLine();
        $this->info("✅ COMPLETE");
        $this->line("Total fetched: {$totalFetched}");
        $this->line("New listings: {$totalNew}");
        $this->line("Updated: {$totalUpdated}");
        $this->line("Marked as sold: {$markedSold}");

        return Command::SUCCESS;
    }
}
