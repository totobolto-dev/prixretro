# Scraping & Publishing Workflow

## 1. Scrape New Data (GBC/GBA/DS)

```bash
cd legacy-python

# Scrape GBC
python3 scraper_ebay.py
# Creates: scraped_data_gbc.json

# Scrape GBA
python3 scraper_gba.py
# Creates: scraped_data_gba.json

# Scrape DS (when ready)
python3 scraper_ds.py
# Creates: scraped_data_ds.json
```

## 2. Sort & Fix Dates

```bash
# Start local server for date editor
python3 -m http.server 8080

# Open in browser: http://localhost:8080/gba_date_editor.html
# 1. Edit dates
# 2. Export as: gba_kept_items_fixed_dates.json
# 3. Run: python3 update_gba_dates.py
```

## 3. Import to Local Laravel Database

```bash
cd ..  # Back to root

# Import GBC (merges with existing)
./vendor/bin/sail artisan import:scraped legacy-python/scraped_data_gbc.json

# Import GBA (merges with existing)
./vendor/bin/sail artisan import:scraped legacy-python/scraped_data_gba.json

# Import DS (merges with existing)
./vendor/bin/sail artisan import:scraped legacy-python/scraped_data_ds.json
```

## 4. Review in Admin Panel

```bash
# Open: http://localhost:8000/admin/listings
# - Review new listings
# - Approve/reject individually
# - Change status to "approved"
```

## 5. Sync to Production Database

```bash
./vendor/bin/sail artisan sync:production
```

**Done!** Live site at www.prixretro.com shows new data.

## Quick Commands

```bash
# Full workflow (after scraping):
cd legacy-python && python3 update_gba_dates.py && cd ..
./vendor/bin/sail artisan import:scraped legacy-python/scraped_data_gba.json
./vendor/bin/sail artisan sync:production
```

## Admin Access

- Local: http://localhost:8000/admin
- Live: https://www.prixretro.com/admin
- Login: prixretro@proton.me / password
