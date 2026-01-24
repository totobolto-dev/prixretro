# eBay Finding API Setup & Usage Guide

## ✅ What's Done

1. **eBay Developer credentials approved** (Jan 2026)
2. **Webhook endpoint created** for marketplace deletion compliance
3. **Service class** (`EbayFindingService`) to interact with eBay API
4. **Test command** to verify API integration

---

## 🔑 Step 1: Add Credentials to .env

Add your eBay credentials to both local and production `.env` files:

Copy your credentials from the eBay Developer Portal (you received them earlier) and add to `.env`:

```bash
# Local: /home/ganzu/Documents/web-apps/prixretro/.env
# Production: SSH to OVH and edit /home/pwagrad/prixretro/.env

EBAY_APP_ID=your_app_id_from_ebay_portal
EBAY_DEV_ID=your_dev_id_from_ebay_portal
EBAY_CERT_ID=your_cert_id_from_ebay_portal
EBAY_VERIFICATION_TOKEN=  # Leave empty for now (only needed for webhook testing)
```

**⚠️ SECURITY:** Never commit these to git! They're already in `.gitignore` via `.env`

After adding to production, clear config cache:
```bash
ssh pwagrad@ssh.cluster069.hosting.ovh.net
cd /home/pwagrad/prixretro
php artisan config:clear
```

---

## 🧪 Step 2: Test the API (Local)

Once credentials are in `.env`, test the integration:

```bash
# Local testing
./vendor/bin/sail artisan ebay:test-search "game boy color atomic purple"
```

**Expected output:**
```
Searching eBay for: game boy color atomic purple

=== COMPLETED ITEMS (Sold Listings) ===
Found 127 total results (2 pages)

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Title: Console Nintendo Game Boy Color Atomic Purple Violet Transparent
Price: 89.99€
Sold: 2026-01-22 14:35
Condition: Occasion
URL: https://www.ebay.fr/itm/...

[... more results ...]

=== ACTIVE ITEMS (Current Listings) ===
Found 5 active listings

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Title: Game Boy Color Atomic Purple Console Complete
Price: 95.00€
Condition: Très bon état

✓ Test completed successfully!
```

If you see this, **the API is working!** 🎉

---

## 🔍 What the API Returns

### Completed Items (Sold Listings)
- **itemId**: eBay item ID
- **title**: Listing title
- **currentPrice**: Final sale price
- **endTime**: When it sold
- **condition**: Item condition (ID + display name)
- **viewItemURL**: Link to listing
- **shippingCost**: Shipping cost (if available)

### Active Items (Current Listings)
- Same fields but item is still for sale
- Price is current asking price, not sold price

---

## 📊 Rate Limits

- **5,000 calls/day** for Finding API
- Each call can return up to 100 items
- **Max 500,000 items/day** (5,000 × 100)
- For PrixRetro needs: ~50 calls/day (10-20 variants × 2-3 pages each)

---

## 🛠️ Next Steps: Create Scraper Command

Now that the API works, create a proper scraper:

### File: `app/Console/Commands/ScrapeSoldListings.php`

```php
<?php

namespace App\Console\Commands;

use App\Models\Console;
use App\Models\Listing;
use App\Services\EbayFindingService;
use Illuminate\Console\Command;

class ScrapeSoldListings extends Command
{
    protected $signature = 'ebay:scrape-sold {--console= : Console slug to scrape}';
    protected $description = 'Scrape eBay sold listings using Finding API';

    public function handle(EbayFindingService $ebayService): int
    {
        $consoleSlug = $this->option('console');

        if ($consoleSlug) {
            $console = Console::where('slug', $consoleSlug)->first();
            if (!$console) {
                $this->error("Console not found: {$consoleSlug}");
                return Command::FAILURE;
            }
            $consoles = collect([$console]);
        } else {
            $consoles = Console::all();
        }

        foreach ($consoles as $console) {
            $this->info("Scraping {$console->name}...");

            foreach ($console->variants as $variant) {
                $keywords = $variant->search_terms
                    ? implode(' ', $variant->search_terms)
                    : "{$console->name} {$variant->name}";

                $this->line("  - {$variant->name}: {$keywords}");

                // Get first 2 pages (200 items)
                for ($page = 1; $page <= 2; $page++) {
                    $result = $ebayService->findCompletedItems($keywords, 100, $page);

                    if ($result['error']) {
                        $this->warn("    Error on page {$page}: {$result['error']}");
                        break;
                    }

                    $imported = 0;
                    foreach ($result['items'] as $item) {
                        $parsed = $ebayService->parseItem($item);

                        // Check if already exists
                        $exists = Listing::where('ebay_item_id', $parsed['ebay_item_id'])->exists();
                        if ($exists) {
                            continue;
                        }

                        // Create new listing (pending classification)
                        Listing::create([
                            'ebay_item_id' => $parsed['ebay_item_id'],
                            'title' => $parsed['title'],
                            'price' => $parsed['price'],
                            'sold_date' => $parsed['sold_date'],
                            'url' => $parsed['url'],
                            'source' => 'ebay',
                            'item_condition' => $parsed['item_condition'],
                            'status' => 'pending',
                            'variant_id' => null, // Will be assigned in admin
                        ]);

                        $imported++;
                    }

                    $this->line("    Page {$page}: {$imported} new listings");

                    // Rate limit: sleep 1 second between requests
                    if ($page < 2) {
                        sleep(1);
                    }
                }
            }

            $this->newLine();
        }

        $this->info('✓ Scraping completed!');
        return Command::SUCCESS;
    }
}
```

**Usage:**
```bash
# Scrape all consoles
php artisan ebay:scrape-sold

# Scrape specific console
php artisan ebay:scrape-sold --console=game-boy-color
```

---

## 🔄 Migration from Python Scraper

**Old workflow:**
1. Run Python scraper → Outputs JSON
2. Import JSON via admin panel
3. Manually classify listings

**New workflow:**
1. Run Laravel command → Direct database insert
2. Listings auto-created with `status=pending`
3. Use existing admin panel to classify

**Benefits:**
- ✅ No JSON file intermediary
- ✅ Official API (no scraping HTML)
- ✅ Better data quality (structured response)
- ✅ Rate limit tracking
- ✅ Error handling built-in

---

## 📅 Scheduling (Future)

Add to `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule): void
{
    // Scrape sold listings daily at 4 AM
    $schedule->command('ebay:scrape-sold')
        ->dailyAt('04:00')
        ->onOneServer()
        ->withoutOverlapping();
}
```

**OVH Cron job:**
```bash
0 4 * * * cd /home/pwagrad/prixretro && php artisan ebay:scrape-sold >> /dev/null 2>&1
```

---

## ❓ Troubleshooting

### "Invalid credentials"
- Check `.env` credentials are correct
- Run `php artisan config:clear`
- Verify App ID starts with your eBay username

### "Daily call limit exceeded"
- eBay limits: 5,000 calls/day
- Check usage: eBay Developer Portal → My Applications → PrixRetro → API Call Limits

### "No results found"
- eBay France uses `GLOBAL-ID=EBAY-FR`
- Verify keywords match actual listings
- Try broader search terms first

### "Connection timeout"
- API timeout set to 30 seconds
- Check internet connection
- Try again (eBay API can be slow sometimes)

---

## 🎯 Current Status

- [x] eBay credentials approved
- [x] Webhook endpoint created
- [x] Service class implemented
- [x] Test command working
- [ ] **TODO**: Create scraper command
- [ ] **TODO**: Test on production
- [ ] **TODO**: Schedule daily scraping
- [ ] **TODO**: Deprecate Python scraper

---

## 📖 API Documentation

**Official docs:**
- [Finding API Reference](https://developer.ebay.com/DevZone/finding/Concepts/FindingAPIGuide.html)
- [findCompletedItems](https://developer.ebay.com/DevZone/finding/CallRef/findCompletedItems.html)
- [Item Filters](https://developer.ebay.com/DevZone/finding/CallRef/types/ItemFilterType.html)

**Quick reference:**
- Site ID 71 = eBay France
- GLOBAL-ID = EBAY-FR
- SoldItemsOnly filter = completed listings only
- Max 100 results per page, 100 pages max
