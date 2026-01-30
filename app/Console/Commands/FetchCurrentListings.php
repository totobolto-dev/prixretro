<?php

namespace App\Console\Commands;

use App\Models\Variant;
use App\Models\CurrentListing;
use App\Services\EbayBrowseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

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

        // Get all rejected item_ids for this variant to blacklist them
        $rejectedItemIds = CurrentListing::where('status', 'rejected')
            ->pluck('item_id')
            ->toArray();

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

            // Keep fetching until we get the desired number of approved items
            $offset = 0;
            $pageSize = 50; // Fetch more per page to account for blacklist filtering
            $maxPages = 5; // Don't fetch forever
            $page = 1;

            $fetched = 0;
            $new = 0;
            $updated = 0;
            $skippedRejected = 0;
            $skippedBlacklist = 0;

            while ($new < $limit && $page <= $maxPages) {
                // Fetch active listings from eBay
                $result = $ebayService->findActiveItems($searchTerm, $pageSize, $offset, $minPrice, $maxPrice);

                if ($result['error']) {
                    $this->error("  ❌ API Error: {$result['error']}");
                    break;
                }

                if (empty($result['items'])) {
                    $this->line("  ⚠️  No items returned (page {$page}, total: {$result['total']})");
                    break;
                }

                $this->line("  📄 Page {$page}: Found {$result['total']} total items, processing " . count($result['items']) . " items");

                foreach ($result['items'] as $item) {
                $parsed = $ebayService->parseItem($item);

                if ($parsed === null) {
                    continue;
                }

                // Skip rejected item IDs (already marked as rejected in previous fetches)
                if (in_array($parsed['ebay_item_id'], $rejectedItemIds)) {
                    $skippedRejected++;
                    continue;
                }

                // Blacklist non-console items (games, cartridges, cases, etc.)
                $titleLower = strtolower($parsed['title']);
                $blacklist = ['jeu', 'jeux', 'game', 'cartouche', 'étui', 'housse', 'manette', 'controller', 'cable', 'chargeur', 'alimentation'];
                $isBlacklisted = false;
                foreach ($blacklist as $word) {
                    if (str_contains($titleLower, $word)) {
                        $isBlacklisted = true;
                        break;
                    }
                }

                if ($isBlacklisted) {
                    $skippedBlacklist++;
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
                    // Create new listing (auto-approved after blacklist filtering)
                    $listingData = [
                        'variant_id' => $variant->id,
                        'item_id' => $parsed['ebay_item_id'],
                        'title' => $parsed['title'],
                        'price' => $parsed['price'],
                        'url' => $parsed['url'],
                        'status' => 'approved',
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

                    // Stop if we have enough new items
                    if ($new >= $limit) {
                        break;
                    }
                }

                $offset += $pageSize;
                $page++;

                // Break if no more items in this page
                if (count($result['items']) < $pageSize) {
                    break;
                }

                // Rate limiting between pages
                sleep(1);
            }

            $totalFetched += $fetched;
            $totalNew += $new;
            $totalUpdated += $updated;

            // Update variant's last fetched timestamp
            $variant->update(['current_listings_fetched_at' => now()]);

            $this->line("  ✅ Fetched: {$fetched} | New: {$new} | Updated: {$updated}");
            if ($skippedRejected > 0 || $skippedBlacklist > 0) {
                $this->line("  ⏭️  Skipped: {$skippedRejected} rejected, {$skippedBlacklist} blacklisted");
            }

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
