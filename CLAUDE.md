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
  - Currently: Only Game Boy Color variant pages
  - TODO: Expand to ALL high-traffic variants (GBA, DS)
- **Ranking Pages**: `/{console-slug}/classement` for SEO
- **Sitemap**: `/public/sitemap.xml` (31 pages, regenerate after adding variants)

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

### Scraping & Data Import
```bash
# 1. Scrape eBay (legacy Python - being migrated to Laravel)
cd legacy-python && python3 scraper_ebay.py

# 2. Import via admin panel
# Login: https://www.prixretro.com/admin
# Click "Import Scraped Data" → Select console → Review → Approve
# Click "Sync to Production" to push to CloudDB
```

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
- ✅ Amazon affiliates (GBC only)
- ✅ Ranking pages
- ✅ Sitemap
- 🔄 **TODO**: Migrate Python scrapers to Laravel commands
- 🔄 **TODO**: Replace static HTML with Laravel views
- 🔄 **TODO**: Expand affiliate links to GBA/DS
- 🔄 **TODO**: Google Analytics + AdSense
- 🔄 **TODO**: SEO meta tags + structured data
