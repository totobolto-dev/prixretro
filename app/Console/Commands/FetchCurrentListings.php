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
                            {--limit=5 : Max items per variant (default: 5)}';

    protected $description = 'Fetch current eBay listings for all variants using Browse API';

    public function handle(EbayBrowseService $ebayService): int
    {
        $limit = (int) $this->option('limit');
        $variantId = $this->option('variant');

        if ($variantId) {
            $variants = Variant::where('id', $variantId)->get();
        } else {
            // Skip variants that already have 5+ approved current listings
            $variants = Variant::whereHas('listings', function($q) {
                $q->where('status', 'approved');
            })
            ->withCount(['currentListings' => function($q) {
                $q->where('status', 'approved')
                  ->where('is_sold', false);
            }])
            ->having('current_listings_count', '<', 5)
            ->get();
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
            // Use custom search_term if available, otherwise use console + variant name
            // Support multiple search terms separated by comma
            $searchTerms = [];
            if ($variant->search_term) {
                $searchTerms = array_map('trim', explode(',', $variant->search_term));
                $this->line("📦 {$variant->console->name} - {$variant->name} (search terms: " . implode(', ', $searchTerms) . ")");
            } else {
                $searchTerms = ["{$variant->console->name} {$variant->name}"];
                $this->line("📦 {$variant->console->name} - {$variant->name}");
            }

            // Calculate price range (±20% of average loose price)
            $avgLoosePrice = \App\Models\Listing::where('variant_id', $variant->id)
                ->where('status', 'approved')
                ->where('completeness', 'loose')
                ->avg('price');

            $minPrice = null;
            $maxPrice = null;

            if ($avgLoosePrice) {
                $minPrice = round($avgLoosePrice * 0.8);  // -20%
                $maxPrice = round($avgLoosePrice * 1.2);  // +20%
                $this->line("  💰 Price range: {$minPrice}€ - {$maxPrice}€ (avg loose: " . round($avgLoosePrice, 2) . "€)");
            } else {
                $this->line("  ⚠️  No loose price data, fetching without price filter");
            }

            // Count existing approved current listings for this variant
            $existingCount = CurrentListing::where('variant_id', $variant->id)
                ->where('status', 'approved')
                ->where('is_sold', false)
                ->count();

            // Calculate how many more we need
            $needed = max(0, $limit - $existingCount);

            if ($needed === 0) {
                $this->line("  ✅ Already has {$limit} listings, skipping");
                continue;
            }

            $this->line("  📊 Current: {$existingCount} | Need: {$needed} more");

            $fetched = 0;
            $new = 0;
            $updated = 0;
            $skippedRejected = 0;
            $skippedBlacklist = 0;

            // Try each search term until we get enough results
            foreach ($searchTerms as $searchTermIndex => $searchTerm) {
                if ($new >= $needed) {
                    break; // We have enough, stop trying other search terms
                }

                if (count($searchTerms) > 1) {
                    $this->line("  🔍 Trying search term " . ($searchTermIndex + 1) . "/" . count($searchTerms) . ": '{$searchTerm}'");
                }

                // Keep fetching until we get the desired number of approved items
                $offset = 0;
                $pageSize = 50; // Fetch more per page to account for blacklist filtering
                $maxPages = 10; // Don't fetch forever
                $page = 1;

                while ($new < $needed && $page <= $maxPages) {
                    // Fetch active listings from eBay
                    $result = $ebayService->findActiveItems($searchTerm, $pageSize, $offset, $minPrice, $maxPrice);

                if ($result['error']) {
                    $this->error("  ❌ API Error: {$result['error']}");
                    break;
                }

                if (empty($result['items'])) {
                    if ($result['total'] === 0 && $minPrice !== null) {
                        $this->line("  ⚠️  No items in price range {$minPrice}€-{$maxPrice}€. Try wider range or check search term.");
                    } else {
                        $this->line("  ⚠️  No items returned (page {$page}, total: {$result['total']})");
                    }
                    break;
                }

                $this->line("  📄 Page {$page}: Found {$result['total']} total items, processing " . count($result['items']) . " items");

                foreach ($result['items'] as $item) {
                $parsed = $ebayService->parseItem($item);

                if ($parsed === null) {
                    $this->line("    ⚠️  Failed to parse item");
                    continue;
                }

                $this->line("    📦 {$parsed['price']}€ - {$parsed['title']}");

                // Skip rejected item IDs (already marked as rejected in previous fetches)
                if (in_array($parsed['ebay_item_id'], $rejectedItemIds)) {
                    $skippedRejected++;
                    $this->line("       ❌ SKIPPED: Previously rejected");
                    continue;
                }

                // Blacklist non-console items (games, cartridges, cases, etc.)
                // Use word boundaries to avoid false positives (e.g., "Game Boy" shouldn't match "game")
                $titleLower = strtolower($parsed['title']);

                // Global blacklist
                $globalBlacklist = [
                    '\bjeu\b', '\bjeux\b',  // French: game/games
                    '\bcartouche\b', '\bcartridge\b',  // Cartridge
                    '\bétui\b', '\bhousse\b', '\bcase\b', '\bsac\b',  // Case/pouch/bag
                    '\bmanette\b', '\bcontroller\b',  // Controller
                    '\bcable\b', '\bcâble\b', '\bchargeur\b', '\balimentation\b',  // Cables/chargers
                    '\bjaquette\b', '\bboîte\b seule', '\bboite\b seule',  // Box only
                    '\bpièce\b détachée', '\bpiece\b détachée',  // Spare parts
                    '\blot\b.*\bjeux\b', '\bjeux\b.*\blot\b',  // Game lots
                    'pokemon\s+version\s+(jaune|rouge|bleu|vert|or|argent|cristal)',  // Pokemon games for GBC
                ];

                // Variant-specific blacklist (exact substring matching with spaces)
                $variantBlacklist = [];
                if ($variant->blacklist_terms) {
                    $variantBlacklist = array_map('trim', explode(',', $variant->blacklist_terms));
                }

                $isBlacklisted = false;
                $blacklistReason = null;

                // Check global blacklist (regex patterns)
                foreach ($globalBlacklist as $pattern) {
                    if (preg_match('/' . $pattern . '/i', $titleLower)) {
                        $isBlacklisted = true;
                        $blacklistReason = "global pattern: {$pattern}";
                        break;
                    }
                }

                // Check variant blacklist (exact substring matching)
                if (!$isBlacklisted && count($variantBlacklist) > 0) {
                    foreach ($variantBlacklist as $term) {
                        if (str_contains($titleLower, strtolower($term))) {
                            $isBlacklisted = true;
                            $blacklistReason = "variant blacklist: '{$term}'";
                            break;
                        }
                    }
                }

                if ($isBlacklisted) {
                    $skippedBlacklist++;
                    $this->line("       ❌ BLACKLISTED: {$blacklistReason}");
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
                    $this->line("       ✏️  UPDATED (already had this one)");
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
                    $this->line("       ✅ ADDED ({$new}/{$needed})");
                }

                    $fetched++;

                    // Stop if we have enough new items
                    if ($new >= $needed) {
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
            } // End foreach search terms

            $totalFetched += $fetched;
            $totalNew += $new;
            $totalUpdated += $updated;

            // Update variant's last fetched timestamp
            $variant->update(['current_listings_fetched_at' => now()]);

            // Count final total
            $finalCount = CurrentListing::where('variant_id', $variant->id)
                ->where('status', 'approved')
                ->where('is_sold', false)
                ->count();

            $this->line("  ✅ Fetched: {$fetched} | New: {$new} | Updated: {$updated} | Total now: {$finalCount}");
            if ($skippedRejected > 0 || $skippedBlacklist > 0) {
                $this->line("  ⏭️  Skipped: {$skippedRejected} rejected, {$skippedBlacklist} blacklisted");
            }

            if ($finalCount < $limit) {
                $this->line("  ⚠️  Only {$finalCount}/{$limit} listings (not enough valid eBay results)");
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
