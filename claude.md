# PrixRetro Laravel Migration - Session Context

**Date:** 2025-12-27
**Current Phase:** Public Pages Live & Tested - All Core Features Working

## Progress Completed

### 1. UTF-8 Encoding Fix (DONE)
- Fixed French character corruption in all eBay scrapers
- Added `response.encoding = 'utf-8'` to 5 scraper files
- Deployed to production

### 2. Directory Reorganization (DONE)
- Moved all Python project files to `legacy-python/`
- Clean root directory ready for Laravel installation
- Current structure:
  ```
  ~/Documents/web-apps/prixretro/
  ├── .git/
  ├── .github/
  ├── _archive/
  ├── _cleanup/
  ├── legacy-python/ (all Python files here)
  ├── CNAME
  └── claude.md (this file)
  ```

### 3. Architecture Planning (DONE)
- 11-phase Laravel migration plan created
- Technology stack selected:
  - PHP 8.4 (latest stable)
  - Laravel 12 (latest)
  - Filament 4.3.1 (admin panel)
  - MySQL 8.0
  - Docker/Laravel Sail (local dev)
- Database schema designed (6 core tables)

### 4. Laravel 12 Installation (DONE ✅)
- Laravel 12.44.0 installed with Sail
- Running on http://localhost:8000
- MySQL database configured
- 6 database tables created with migrations
- 6 Eloquent models created with full relationships
- Filament 4.3.1 admin panel installed
- 4 Filament resources created (Console, Variant, Listing, CurrentListing)
- Admin user created: admin@prixretro.com / password

### 5. Data Import (DONE ✅)
- Import commands created:
  - `import:consoles` - Imports from config_multiconsole.json
  - `import:gba-listings` - Imports from scraped_data_gba.json
- Database populated:
  - 3 consoles (GBC, GBA, NDS)
  - 48 variants across all consoles
  - 36 GBA listings (all approved)
- All data accessible via Filament admin panel

### 6. Public Pages Built (DONE ✅)
- Routes created for clean URLs:
  - `/` - Homepage listing all consoles
  - `/{console}` - Console page (future)
  - `/{console}/{variant}` - Variant page with price data
- Controllers implemented:
  - `ConsoleController` - Homepage listing
  - `VariantController` - Price statistics & listings
- Blade templates created:
  - Layout with SEO meta tags, Google Analytics, AdSense
  - Homepage showing all consoles & variants
  - Variant page with:
    - Price statistics (avg, min, max, count)
    - Chart.js price trend chart
    - Recent listings table
    - eBay affiliate CTA button
- CSS and favicon copied from legacy output

### 7. Variant Name Cleanup (DONE ✅)
- Removed "Standard" prefix from basic GBA variants:
  - Standard Black → Black
  - Standard Glacier → Glacier
  - Standard Orange → Orange
  - Standard Pink → Pink
  - Standard Purple → Purple
- Updated slugs and full_slugs accordingly
- SP and Micro variants keep their prefixes (SP Flame, Micro Black, etc.)
- All variant pages tested and working correctly

### 8. Key Decisions Made

**URL Structure:**
- Use CLEAN URLs: `/gba/sp-flame` (no .html extension)
- NO 301 redirects needed (site has zero Google traffic)
- Fresh start with modern routes

**Hosting:**
- Local dev: Docker + Laravel Sail
- Production: Existing OVH Performance 1 hosting (2 vCore, 4GB RAM, SSH, Git, Cron)
- No VPS needed - saves €5/month

**Deployment Strategy:**
- File cache (not Redis - OVH limitation)
- Cron-based queue workers (`schedule:run` every minute)
- GitHub Actions for automated deployment

## Current Task: Docker Installation

### Docker Installation Status
You just installed Docker and added your user to the docker group.

**After restart, verify Docker:**
```bash
docker --version
docker compose version
docker ps
```

If docker group isn't active yet, run:
```bash
newgrp docker
```

## Accessing Your Data

### Filament Admin Panel
Visit **http://localhost:8000/admin**
- Login: `admin@prixretro.com` / `password`
- Manage consoles, variants, and listings
- Approve/reject scraped listings
- View price statistics

### Re-import Data (if needed)
```bash
# Re-import consoles and variants
./vendor/bin/sail artisan import:consoles --fresh

# Re-import GBA listings
./vendor/bin/sail artisan import:gba-listings --fresh
```

## Testing Your Site

### Visit Your Pages
Open your browser and visit:
- **Homepage**: http://localhost:8000
- **GBA SP Flame**: http://localhost:8000/game-boy-advance/sp-flame
- **GBA SP Cobalt**: http://localhost:8000/game-boy-advance/sp-cobalt
- **Admin Panel**: http://localhost:8000/admin

All pages are live with:
- Price statistics calculated on-the-fly
- Monthly price trend charts
- Recent listings tables
- eBay affiliate links
- Google Analytics tracking
- SEO meta tags

## Next Steps ← **NEXT**

### Import More Data
Currently only GBA listings are imported. Import the rest:
```bash
# Check what other data files exist
ls -lh legacy-python/scraped_data*.json

# Create import commands for GBC and NDS
./vendor/bin/sail artisan make:command ImportGbcListings
./vendor/bin/sail artisan make:command ImportNdsListings
```

### Optimize Performance
The site works but calculates statistics on every request:
```bash
# Create command to cache price statistics
./vendor/bin/sail artisan make:command CalculatePriceStatistics
```

This will pre-calculate and cache stats in the `price_statistics` table.

### Deploy to OVH
Once you're happy with local testing:
1. Configure GitHub Actions for auto-deployment
2. Set up OVH environment variables
3. Run migrations on production
4. Import data on production

## Database Schema Reference

### consoles
```sql
id, slug, name, short_name, search_term, ebay_category_id,
description, release_year, manufacturer, is_active, display_order,
timestamps
```

### variants
```sql
id, console_id, slug, name, full_slug (gba/sp-flame),
search_terms (JSON array), image_filename, rarity_level,
region, is_special_edition, timestamps
```

### listings (sold items)
```sql
id, variant_id, item_id, title, price, sold_date, condition,
url, thumbnail_url, source (ebay/leboncoin), is_outlier,
status (pending/approved/rejected), reviewed_at, timestamps
```

### current_listings (for sale)
```sql
id, variant_id, item_id, title, price, url, is_sold,
last_seen_at, timestamps
```

### scrape_jobs
```sql
id, variant_id, job_type (sold/current/images), status,
started_at, completed_at, items_found, error_message, timestamps
```

### price_statistics (cached)
```sql
id, variant_id, period (7d/30d/90d/all), avg_price, min_price,
max_price, median_price, count, last_calculated_at, timestamps
```

## Eloquent Model Relationships

```php
// Console model
public function variants(): HasMany
public function scrapeJobs(): HasMany

// Variant model
public function console(): BelongsTo
public function listings(): HasMany
public function approvedListings(): HasMany
public function currentListings(): HasMany
public function priceStatistics(): HasMany
public function scrapeJobs(): HasMany

// Listing model
public function variant(): BelongsTo
public function scopeApproved()
public function scopePending()
```

## Route Structure

```php
// routes/web.php
Route::get('/', [ConsoleController::class, 'index']);
Route::get('/{console:slug}', [ConsoleController::class, 'show']);
Route::get('/{console:slug}/{variant:slug}', [VariantController::class, 'show']);

// Examples:
// /gba → GBA console page
// /gba/sp-flame → SP Flame variant page with price data
```

## Data Import Strategy

### Phase 1: Import Config
```bash
# Import consoles and variants from config_multiconsole.json
./vendor/bin/sail artisan make:command ImportConsoles
```

### Phase 2: Import Historical Data
```bash
# Import scraped_data_gba.json → listings table
./vendor/bin/sail artisan make:command ImportGbaListings
```

### Phase 3: Import Current Listings
```bash
# Import current_listings.json → current_listings table
./vendor/bin/sail artisan make:command ImportCurrentListings
```

## Filament Admin Resources

**Create these resources:**
```bash
./vendor/bin/sail artisan make:filament-resource Console --generate
./vendor/bin/sail artisan make:filament-resource Variant --generate
./vendor/bin/sail artisan make:filament-resource Listing --generate
./vendor/bin/sail artisan make:filament-resource CurrentListing --generate
```

**Admin URL:** http://localhost/admin

**Replaces these HTML tools:**
- `variant_sorter_gba.html` → Listing Resource (approve/reject)
- `gba_date_editor.html` → Listing Resource (edit dates)
- Manual JSON editing → Console/Variant Resources

## Deployment to OVH Performance 1

**GitHub Actions workflow:**
```yaml
- name: Deploy to OVH
  run: |
    rsync -avz --exclude 'vendor' --exclude 'node_modules' ./ user@host:/path/
    ssh user@host "cd /path && composer install --no-dev"
    ssh user@host "cd /path && php artisan migrate --force"
    ssh user@host "cd /path && php artisan optimize"
```

**OVH Cron setup:**
```
* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1
```

## Useful Sail Commands

```bash
# Start containers
./vendor/bin/sail up -d

# Stop containers
./vendor/bin/sail down

# Run artisan commands
./vendor/bin/sail artisan [command]

# Run migrations
./vendor/bin/sail artisan migrate

# Fresh database
./vendor/bin/sail artisan migrate:fresh

# Tinker (Laravel REPL)
./vendor/bin/sail tinker

# Composer install
./vendor/bin/sail composer install

# NPM commands
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev

# Access MySQL
./vendor/bin/sail mysql

# View logs
./vendor/bin/sail logs -f
```

## Todo List Status

- [x] Design Laravel architecture and migration plan
- [x] Reorganize directory - move Python to legacy-python/
- [x] Install Docker for Laravel Sail
- [x] Install Laravel 12 in root directory
- [x] Build database migrations (6 tables)
- [x] Set up Filament 4 admin panel
- [x] Create Eloquent models with relationships
- [x] Create data import commands
- [x] Import existing data from legacy-python/
- [x] Build public pages (Blade templates)
- [x] Fix all routing and view errors
- [x] Clean up variant names (remove "Standard" prefix)
- [x] Test all pages with real data
- [ ] Import GBC and NDS listings ← **NEXT**
- [ ] Calculate & cache price statistics
- [ ] Configure deployment to OVH

## Questions to Revisit Later

1. Do we want to keep Python scrapers or rewrite in PHP?
   - PHP could use Symfony Panther or Goutte
   - Cron can run Python scripts for now

2. Should we add image scraping to Filament admin?
   - Could be a button "Scrape Images" on variant page

3. Price graph library?
   - Chart.js (simple)
   - ApexCharts (prettier)
   - Filament has Chart widgets built-in

## Reference Files

**Legacy Python files:** `legacy-python/`
- `config_multiconsole.json` - Console/variant config
- `scraped_data_gba.json` - GBA sold listings
- `current_listings.json` - Items for sale
- `scraper_ebay_universal.py` - Universal scraper
- `template-v4-compact.html` - Current page template

## Contact

If session continues, search this file for "NEXT" to find current task.

Last updated: 2025-12-27 after variant name cleanup completed
