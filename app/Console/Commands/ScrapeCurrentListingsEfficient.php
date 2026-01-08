<?php

namespace App\Console\Commands;

use App\Models\Console;
use App\Models\Variant;
use App\Models\CurrentListing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ScrapeCurrentListingsEfficient extends Command
{
    protected $signature = 'scrape:current-listings-efficient {--max-per-variant=6}';
    protected $description = 'Scrape eBay current listings efficiently (once per console)';

    public function handle()
    {
        $this->info('🚀 Efficient Current Listings Scraper');
        $this->line(str_repeat('=', 60));

        // Mark old listings as sold
        $oldCount = DB::table('current_listings')
            ->where('is_sold', false)
            ->update(['is_sold' => true, 'updated_at' => now()]);
        $this->info("🗑️  Marked {$oldCount} old listings as sold\n");

        // Get consoles with active variants
        $consoles = Console::where('is_active', true)
            ->with('variants')
            ->get();

        $this->info("📋 Scraping {$consoles->count()} consoles\n");

        $totalSaved = 0;
        $maxPerVariant = $this->option('max-per-variant');

        foreach ($consoles as $console) {
            $this->info("🎮 {$console->name}...");

            // Scrape once for entire console
            $allListings = $this->scrapeConsole($console);

            if ($allListings->isEmpty()) {
                $this->warn("  ⚠️  No listings found");
                continue;
            }

            $this->line("  📦 Found {$allListings->count()} total listings");

            // Match listings to variants
            $saved = $this->matchAndSaveListings($console, $allListings, $maxPerVariant);
            $totalSaved += $saved;

            $this->info("  ✅ Saved {$saved} listings across {$console->variants->count()} variants");

            sleep(5); // Rate limiting between consoles
        }

        $this->newLine();
        $this->line(str_repeat('=', 60));
        $this->info("✅ Complete! Saved {$totalSaved} current listings");

        return Command::SUCCESS;
    }

    private function scrapeConsole(Console $console)
    {
        $searchTerm = $console->search_term ?: $console->name;

        $url = 'https://www.ebay.fr/sch/i.html?' . http_build_query([
            '_nkw' => $searchTerm,
            '_sacat' => '139971', // Video game consoles
            '_sop' => '10', // Price: lowest first
            '_ipg' => '200', // Max items per page
        ]);

        try {
            $response = Http::timeout(20)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
                    'Referer' => 'https://www.ebay.fr/',
                ])
                ->get($url);

            if (!$response->successful()) {
                $this->error("  ❌ HTTP {$response->status()}");
                return collect([]);
            }

            return $this->parseListings($response->body());

        } catch (\Exception $e) {
            $this->error("  ❌ Error: " . $e->getMessage());
            return collect([]);
        }
    }

    private function parseListings(string $html)
    {
        $listings = collect([]);

        // eBay embeds listing data in JSON format within the page
        // Extract all listingId + associated data from embedded JSON
        preg_match_all('/"listingId":"(\d+)"/', $html, $idMatches);

        if (empty($idMatches[1])) {
            return $listings;
        }

        // For each listing ID, try to extract the full details
        foreach ($idMatches[1] as $itemId) {
            // Find the JSON block containing this listing
            // Pattern: look for title, price, and URL near this listingId
            $pattern = '/"listingId":"' . preg_quote($itemId, '/') . '"[^}]{0,2000}/';

            if (!preg_match($pattern, $html, $blockMatch)) {
                continue;
            }

            $block = $blockMatch[0];

            // Extract title from the JSON block
            if (!preg_match('/"title":"([^"]+)"/', $block, $titleMatch)) {
                continue;
            }

            $title = $this->decodeUnicode($titleMatch[1]);
            $title = html_entity_decode($title, ENT_QUOTES | ENT_HTML5, 'UTF-8');

            // Skip sponsored headers or empty titles
            if (empty($title) || str_contains(strtolower($title), 'shop on ebay')) {
                continue;
            }

            // Extract price - look for convertedCurrentPrice or similar
            $price = null;
            if (preg_match('/"convertedCurrentPrice":\{"value":([0-9.]+)/', $block, $priceMatch)) {
                $price = (float) $priceMatch[1];
            } elseif (preg_match('/"price":\{"value":([0-9.]+)/', $block, $priceMatch)) {
                $price = (float) $priceMatch[1];
            } elseif (preg_match('/([0-9]+[,.]?[0-9]*)\s*EUR/', $block, $priceMatch)) {
                $priceText = str_replace(',', '.', $priceMatch[1]);
                $price = (float) $priceText;
            }

            if (!$price || $price <= 0) {
                continue;
            }

            // Construct URL
            $itemUrl = "https://www.ebay.fr/itm/{$itemId}";

            $listings->push([
                'item_id' => $itemId,
                'title' => substr($title, 0, 255),
                'title_lower' => strtolower($title),
                'price' => $price,
                'url' => $itemUrl,
            ]);
        }

        // Remove duplicates by item_id
        return $listings->unique('item_id')->values();
    }

    private function decodeUnicode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $str);
    }

    private function matchAndSaveListings(Console $console, $allListings, int $maxPerVariant)
    {
        $saved = 0;

        foreach ($console->variants as $variant) {
            $searchTerms = is_array($variant->search_terms)
                ? $variant->search_terms
                : (json_decode($variant->search_terms, true) ?? []);

            if (empty($searchTerms)) {
                continue;
            }

            // Filter listings matching this variant's search terms
            $matchedListings = $allListings->filter(function($listing) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    if (str_contains($listing['title_lower'], strtolower($term))) {
                        return true;
                    }
                }
                return false;
            })->take($maxPerVariant);

            // Save matched listings
            foreach ($matchedListings as $listing) {
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
                    // Skip duplicates or errors
                }
            }
        }

        return $saved;
    }
}
