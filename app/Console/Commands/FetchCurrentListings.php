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

        // Update API usage stats (this costs 1 API call)
        $this->updateApiUsageStats($ebayService);

        return Command::SUCCESS;
    }

    /**
     * Fetch and cache API usage statistics
     */
    protected function updateApiUsageStats(EbayBrowseService $ebayService): void
    {
        $this->newLine();
        $this->info("📊 Updating API usage stats...");

        $stats = $this->fetchApiStats($ebayService);

        if ($stats) {
            Cache::forever('ebay_api_usage', $stats);
            Cache::forever('ebay_api_usage_updated_at', now());

            $remaining = ($stats['limit'] ?? 0) - ($stats['used'] ?? 0);
            $this->line("  Remaining: {$remaining} / {$stats['limit']} calls");
        } else {
            $this->warn("  Failed to fetch API stats");
        }
    }

    /**
     * Fetch API usage statistics from eBay
     */
    protected function fetchApiStats(EbayBrowseService $ebayService): ?array
    {
        $appId = config('services.ebay.app_id');
        $certId = config('services.ebay.cert_id');

        if (!$appId || !$certId) {
            return null;
        }

        try {
            // Get OAuth token
            $credentials = base64_encode("{$appId}:{$certId}");
            $tokenResponse = \Illuminate\Support\Facades\Http::asForm()
                ->withHeaders([
                    'Authorization' => "Basic {$credentials}",
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->post('https://api.ebay.com/identity/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://api.ebay.com/oauth/api_scope',
                ]);

            if ($tokenResponse->failed()) {
                return null;
            }

            $token = $tokenResponse->json()['access_token'] ?? null;
            if (!$token) {
                return null;
            }

            // Get rate limit status
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
            ])->get('https://api.ebay.com/developer/analytics/v1/rate_limit/');

            if ($response->failed()) {
                return null;
            }

            $data = $response->json();

            // Find Browse API stats
            $resources = $data['rateLimits'] ?? [];
            foreach ($resources as $resource) {
                foreach ($resource['resources'] ?? [] as $res) {
                    if (str_contains($res['name'] ?? '', 'buy.browse')) {
                        $rates = $res['rates'] ?? [];
                        foreach ($rates as $rate) {
                            if ($rate['rateLimit'] ?? '' === 'per day') {
                                return [
                                    'used' => $rate['count'] ?? 0,
                                    'limit' => $rate['limit'] ?? 5000,
                                ];
                            }
                        }
                    }
                }
            }

            return ['used' => 0, 'limit' => 5000];

        } catch (\Exception $e) {
            return null;
        }
    }
}
