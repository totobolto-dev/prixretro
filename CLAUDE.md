# PrixRetro - Project Instructions

**CRITICAL: This is a survival project. Goal: 1000€/month passive income. Optimize for revenue, not perfection.**

## Revenue Equation
```
Traffic × Click-through × Conversion = Revenue
```

**Current bottleneck**: LOW TRAFFIC → Priority #1 is SEO optimization

## Tech Stack
- Laravel 12.44.0 (PHP 8.4+), Filament 4.3.1
- MySQL 8.4 (OVH CloudDB: ba2247864-001.eu.clouddb.ovh.net:35831)
- OVH Performance 1 shared hosting (file-based cache, no Redis)
- GitHub Actions auto-deploy to `/prixretro/` on push to main

## Data Architecture
```
Console (GBC, GBA, DS)
  └─> Variant (color/edition, has full_slug like 'game-boy-color/atomic-purple')
      └─> Listing (scraped eBay sold item: title, price, sold_date, status)
```

## Revenue Features
- **Amazon Affiliates**: Tag `prixretro-21`, 5-8% commission
  - **Portable Consoles Protection**:
    - Nintendo: Game Boy (all), DS (all), 3DS (all) → Orzly case (~14€)
    - Sony: PSP, PS Vita → EVA hard cases (~12-15€)
  - **Home Consoles HDMI Adapters**:
    - PlayStation 1/2, Nintendo 64, GameCube, Super Nintendo
    - Sega: Mega Drive, Saturn, Dreamcast, Master System
    - Price range: ~15-25€
  - **Memory Cards**:
    - PlayStation 2 (8MB cards ~8-12€)
    - GameCube (128MB cards ~8-12€)
- **eBay Partner Network**: `mkcid=1&mkrid=709-53476-19255-0&campid=5339134703`
  - All sold listings clickable with affiliate params
  - Search links to eBay with pre-filled queries
  - Current listings section (up to 6 per variant)
  - **Urgency banners**: Top 3 cheapest listings shown prominently at page top
  - **Scarcity alerts**: Warning when prices rise >10% in 30 days
- **Ranking Pages**: `/{console-slug}/classement` for SEO
- **Sitemap**: `/public/sitemap.xml` (auto-regenerated daily at 3 AM UTC via GitHub Actions)
- **Collection Tracker**: DISABLED (no auth system - routes commented out, UI removed)

## Critical Technical Patterns

### Filament v4
Use `Filament\Tables\Actions\Action` (NOT HeaderAction - doesn't exist in v4)

### OVH Reverse Proxy
```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->trustProxies(at: '*');
})
```

### Environment Variables
- Use `config()` helper, NEVER `env()` in application code
- Production: `CACHE_STORE=file`, `VIEW_COMPILED_PATH=/home/pwagrad/prixretro/storage/framework/views`

### eBay API Credentials
```bash
# .env (credentials stored securely, not in repo)
EBAY_APP_ID=your_app_id
EBAY_DEV_ID=your_dev_id
EBAY_CERT_ID=your_cert_id
EBAY_VERIFICATION_TOKEN=your_verification_token
```

**APIs Available:**
- Finding API: Search completed/active listings (what we use for scraping)
- Shopping API: Get item details
- Browse API: Modern REST alternative
- **Webhook**: `/webhooks/ebay/account-deletion` (CSRF exempt, marketplace deletion compliance)

### FilamentUser Interface
```php
// app/Models/User.php
class User extends Authenticatable implements FilamentUser {
    public function canAccessPanel(Panel $panel): bool {
        return true;
    }
}
```

## Common Workflows

### Deploy to Production
```bash
git add . && git commit -m "Message" && git push  # Auto-deploys via GitHub Actions
```

### Sitemap Regeneration
- **Automatic**: Daily at 3 AM UTC via `.github/workflows/sitemap.yml`
- **Manual**: `php artisan sitemap:generate` (requires database access)
- **Manual trigger**: GitHub Actions → sitemap workflow → Run workflow

### Scraping & Data Import
```bash
# 1. Scrape eBay using official Finding API (NEW - Jan 2026)
php artisan ebay:scrape-sold-listings

# OR legacy Python scraper (deprecated)
cd legacy-python && python3 scraper_ebay.py

# 2. Import via admin panel
# Login: https://www.prixretro.com/admin
# Click "Import Scraped Data" → Select console → Review → Approve
# Click "Sync to Production" to push to CloudDB
```

**eBay Finding API Usage:**
- Endpoint: `https://svcs.ebay.com/services/search/FindingService/v1`
- Operation: `findCompletedItems` (sold listings)
- Filters: `SoldItemsOnly=true`, `ListingType=FixedPrice,Auction`
- Rate limit: 5,000 calls/day
- Returns: itemId, title, sellingStatus.currentPrice, listingInfo.endTime

### Local Development
```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

### Clear Caches (Production SSH)
```bash
php artisan config:clear
php artisan view:clear
```

## Progressive Disclosure (Where to Find Things)
- **Models**: `app/Models/{Console,Variant,Listing,User}.php`
- **Filament Resources**: `app/Filament/Resources/{Resource}/`
- **Admin Pages**: `app/Filament/Pages/SortListings.php`
- **Commands**: `app/Console/Commands/`
- **Scrapers**: `legacy-python/*.py` (outputs to `scraped_data_*.json`)
- **Migrations**: `database/migrations/`
- **Routes**: `routes/web.php`
- **Views**: `resources/views/`
- **Deployment**: `.github/workflows/deploy.yml`

## Known Issues & Solutions

### 403 Forbidden After Login
**Cause**: User doesn't implement FilamentUser
**Solution**: Add `implements FilamentUser` + `canAccessPanel()` method

### Redirects to Localhost
**Cause**: Proxy headers not trusted
**Solution**: Enable `$middleware->trustProxies(at: '*')` in `bootstrap/app.php`

### "Please provide a valid cache path"
**Cause**: Missing `config/view.php`
**Solution**: Create config with `VIEW_COMPILED_PATH` defined

### Class "HeaderAction" not found
**Cause**: Using Filament v3 syntax in v4
**Solution**: Use `Action` class instead

### .env Not Loading
**Check**: Config cached in `bootstrap/cache/config.php`
**Solution**: `php artisan config:clear`

## OVH Hosting Constraints
- No Redis (use file-based cache)
- No sudo/limited shell access
- Can't clear OPcache manually
- Must trust proxy headers for HTTPS redirects

## SEO Priorities (For Revenue Growth)
1. **Meta tags**: Title, description, Open Graph for all variant pages
2. **Structured data**: Schema.org Product markup with price, availability
3. **Internal linking**: Console → Variants → Ranking pages
4. **Content**: Add unique descriptions per variant (not just scraped data)
5. **Performance**: Optimize images, lazy loading, CDN (future)
6. **Sitemap**: Keep updated, submit to Google Search Console
7. **Keywords**: Target long-tail French queries ("prix game boy color atomic purple d'occasion")

## Admin Panel
- URL: https://www.prixretro.com/admin
- Resources: Consoles, Variants, Listings, Current Listings
- SortListings page: Classify scraped items → assign to variants
- Actions: Import Scraped Data, Sync to Production, Bulk Approve/Reject

## Deployment Exclusions
`.git*`, `.github/`, `node_modules/`, `tests/`, `storage/logs/`, `legacy-python/`, `_archive/`

## Migration Status
- ✅ Admin panel (Filament)
- ✅ Data import workflow
- ✅ Amazon affiliates (ALL consoles - portable + home)
- ✅ Ranking pages
- ✅ Sitemap (auto-regenerated daily)
- ✅ Google Analytics 4 (configurable via .env)
- ✅ SEO meta tags + Schema.org (Product, CollectionPage, BreadcrumbList, FAQPage)
- ✅ 21 comprehensive buying guides
- ✅ Conversion optimization (urgency banners, scarcity alerts)
- ✅ eBay Developer API credentials approved
- 🔄 **TODO**: Migrate Python scrapers to Laravel commands using eBay Finding API
- 🔄 **TODO**: Add PropellerAds/Adsterra display ads
- 🔄 **TODO**: Add Micromania + Fnac French affiliates
- 🔄 **TODO**: Email price alert signup
- ⏸️ **PAUSED**: Collection tracker (waiting for auth system)
- ⏸️ **PAUSED**: AdSense (pending approval)

## Filament v4 Common Patterns (CRITICAL!)

### Bulk Actions - Use Filament\Actions, NOT Filament\Tables\Actions
**This error has occurred 12+ times - always check existing code first!**

```php
// ❌ WRONG - Will cause "Class not found" error
use Filament\Tables\Actions\BulkAction;
\Filament\Tables\Actions\BulkAction::make('name')

// ✅ CORRECT - Filament v4
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;

->bulkActions([
    BulkActionGroup::make([
        BulkAction::make('approve')
            ->action(function ($records) { ... }),
        BulkAction::make('change_variant')
            ->form([
                Select::make('console_slug')
                    ->options(...)
                    ->required()
                    ->live(),
                Select::make('variant_id')
                    ->options(function (callable $get) { ... })
                    ->required(),
            ])
            ->action(function (Collection $records, array $data) { ... }),
        DeleteBulkAction::make(),
    ]),
])
```

### Filter Customization
```php
SelectFilter::make('variant')
    ->relationship('variant', 'name')
    ->searchable()
    ->getOptionLabelFromRecordUsing(fn ($record) =>
        $record->console->name . ' - ' . $record->name
    )
```

### Preserve Query Parameters on Edit
```php
// In EditRecord page
public function mount(int | string $record): void {
    parent::mount($record);
    $referrer = request()->headers->get('referer');
    if ($referrer && str_contains($referrer, '/admin/listings')) {
        session(['listings_return_url' => $referrer]);
    }
}

protected function getRedirectUrl(): string {
    $returnUrl = session('listings_return_url');
    session()->forget('listings_return_url');
    return $returnUrl ?? $this->getResource()::getUrl('index');
}
```

**Reference**: See `app/Filament/Resources/Listings/Tables/ListingsTable.php` for working examples.
