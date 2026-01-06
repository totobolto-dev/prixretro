<?php

namespace App\Console\Commands;

use App\Models\Console;
use App\Models\Variant;
use App\Models\CurrentListing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScrapeCurrentListings extends Command
{
    protected $signature = 'scrape:current-listings {--max-per-variant=10}';
    protected $description = 'Scrape current eBay listings for all variants';

    public function handle()
    {
        $this->info('🚀 Current Listings Scraper for PrixRetro');
        $this->line(str_repeat('=', 60));

        // Mark all existing listings as sold
        $this->markOldListings();

        // Get all active variants
        $variants = Variant::with('console')
            ->whereHas('console', fn($q) => $q->where('is_active', true))
            ->orderBy('console_id')
            ->orderBy('id')
            ->get();

        $this->info("\n📋 Found {$variants->count()} variants to scrape\n");

        $maxPerVariant = $this->option('max-per-variant');
        $totalFound = 0;
        $totalSaved = 0;

        $bar = $this->output->createProgressBar($variants->count());
        $bar->start();

        foreach ($variants as $variant) {
            $listings = $this->scrapeVariant($variant, $maxPerVariant);

            if ($listings->count() > 0) {
                $saved = $this->saveListings($variant, $listings);
                $totalFound += $listings->count();
                $totalSaved += $saved;
            }

            $bar->advance();
            sleep(3); // Be nice to eBay
        }

        $bar->finish();

        $this->newLine(2);
        $this->line(str_repeat('=', 60));
        $this->info("✅ Scraping complete!");
        $this->info("📊 Total listings found: {$totalFound}");
        $this->info("💾 Total saved to database: {$totalSaved}");

        return Command::SUCCESS;
    }

    private function markOldListings()
    {
        $count = DB::table('current_listings')
            ->where('is_sold', false)
            ->update(['is_sold' => true, 'updated_at' => now()]);

        $this->info("🗑️  Marked {$count} old listings as sold");
    }

    private function scrapeVariant(Variant $variant, int $maxItems = 10)
    {
        $console = $variant->console;
        $searchTerm = $console->search_term ?: $console->name;

        // Build eBay URL for active listings
        $url = 'https://www.ebay.fr/sch/i.html?' . http_build_query([
            '_nkw' => $searchTerm,
            '_sacat' => '139971',
            '_sop' => '10',  // Price lowest first
            '_ipg' => '100',
            // No LH_Sold - we want active listings
        ]);

        // Make request with realistic headers
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'fr-FR,fr;q=0.9',
                    'Referer' => 'https://www.ebay.fr/',
                ])
                ->get($url);

            if (!$response->successful()) {
                return collect([]);
            }

            return $this->parseListings($response->body(), $variant, $maxItems);

        } catch (\Exception $e) {
            $this->error("  ❌ Error scraping {$variant->name}: " . $e->getMessage());
            return collect([]);
        }
    }

    private function parseListings(string $html, Variant $variant, int $maxItems = 10)
    {
        $listings = collect([]);

        // Match .s-card elements (new eBay structure)
        // data-listingid can come before or after class attribute
        preg_match_all('/<li[^>]*data-listingid="([^"]+)"[^>]*class="[^"]*s-card[^"]*"[^>]*>(.*?)<\/li>/s', $html, $items, PREG_SET_ORDER);

        $searchTerms = is_array($variant->search_terms) ? $variant->search_terms : (json_decode($variant->search_terms, true) ?? []);

        foreach ($items as $item) {
            if ($listings->count() >= $maxItems) {
                break;
            }

            $itemId = $item[1];
            $itemHtml = $item[2];

            // Extract title (new structure: <span class="su-styled-text primary default">)
            if (!preg_match('/<span class="su-styled-text primary default">([^<]+)<\/span>/s', $itemHtml, $titleMatch)) {
                continue;
            }
            $title = html_entity_decode(trim($titleMatch[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Skip shop headers
            if (str_contains($title, 'Shop on eBay') || empty($title)) {
                continue;
            }

            // Basic console filter
            $titleLower = strtolower($title);
            $consoleName = strtolower($variant->console->name);
            if (!str_contains($titleLower, $consoleName) &&
                !str_contains($titleLower, 'game boy') &&
                !str_contains($titleLower, 'gameboy')) {
                continue;
            }

            // Match variant search terms
            if (!empty($searchTerms)) {
                $matched = false;
                foreach ($searchTerms as $term) {
                    if (str_contains($titleLower, strtolower($term))) {
                        $matched = true;
                        break;
                    }
                }
                if (!$matched) {
                    continue;
                }
            }

            // Extract URL
            if (!preg_match('/<a class="s-card__link"[^>]+href="([^"]+)"/', $itemHtml, $urlMatch)) {
                continue;
            }
            $itemUrl = explode('?', $urlMatch[1])[0]; // Remove query params

            // Extract price (new structure: <span class="su-styled-text primary bold large-1 s-card__price">)
            if (!preg_match('/<span class="[^"]*s-card__price[^"]*">([^<]+)<\/span>/', $itemHtml, $priceMatch)) {
                continue;
            }
            $priceText = trim($priceMatch[1]);
            $priceText = str_replace(['EUR', ',', ' '], ['', '.', ''], $priceText);

            // Handle price ranges - take first price
            if (str_contains($priceText, 'à')) {
                $priceText = explode('à', $priceText)[0];
            }

            $price = (float) trim($priceText);

            if ($price <= 0) {
                continue;
            }

            $listings->push([
                'item_id' => $itemId,
                'title' => substr($title, 0, 255),
                'price' => $price,
                'url' => substr($itemUrl, 0, 500),
            ]);
        }

        return $listings;
    }

    private function saveListings(Variant $variant, $listings)
    {
        $saved = 0;

        foreach ($listings as $listing) {
            try {
                CurrentListing::updateOrCreate(
                    [
                        'variant_id' => $variant->id,
                        'item_id' => $listing['item_id'],
                    ],
                    [
                        'title' => $listing['title'],
                        'price' => $listing['price'],
                        'url' => $listing['url'],
                        'is_sold' => false,
                        'last_seen_at' => now(),
                    ]
                );
                $saved++;
            } catch (\Exception $e) {
                $this->error("  ⚠️  Error saving listing: " . $e->getMessage());
            }
        }

        return $saved;
    }
}
