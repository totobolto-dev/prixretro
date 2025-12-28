# eBay Scrapers

Python-based scraper for collecting sold listing data from eBay.fr.

## Setup

1. Install Python dependencies:
```bash
pip install -r requirements.txt
```

## Usage

### Via Artisan Commands (Recommended)

```bash
# Scrape Game Boy Color
./vendor/bin/sail artisan scrape:gbc --max-pages=50

# Scrape Game Boy Advance
./vendor/bin/sail artisan scrape:gba --max-pages=50

# Scrape Nintendo DS
./vendor/bin/sail artisan scrape:ds --max-pages=50
```

### Direct Python (Advanced)

```bash
cd scrapers
python3 ebay_scraper.py "game boy color" gbc --max-pages=50
```

## Output

Scraped data is saved to `storage/app/scraped_data_{console}.json`

## Workflow

1. **Scrape**: `php artisan scrape:gbc`
2. **Import**: Use admin panel "Import Scraped Data" button
3. **Review**: Approve/reject listings in admin
4. **Sync**: Push to production with "Sync to Production" button
