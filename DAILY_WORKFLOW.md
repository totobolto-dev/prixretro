# PrixRetro Daily Workflow

Complete guide for daily price data updates.

---

## 📋 Quick Start (Automated)

### Option 1: Fully Automated
```bash
python3 scripts/daily_update.py
```

This runs everything:
1. Merges sorted data
2. Scrapes current listings with images
3. Regenerates website
4. Commits and pushes to GitHub
5. GitHub Actions deploys to OVH

### Option 2: No Auto-Push (Review First)
```bash
python3 scripts/daily_update.py --no-push
```

Same as above but doesn't push - lets you review changes first.

---

## 📝 Manual Workflow (Step by Step)

### 1. Scrape New Sold Listings
```bash
python3 scraper_ebay.py
```

- Scrapes eBay sold listings (last 6 months)
- Appends to `scraped_data.json`
- Tracks seen item_ids to avoid duplicates

### 2. Sort New Items by Variant
```bash
python3 create_variant_sorter.py
open item_review_interface.html
```

**Keyboard shortcuts:**
- `K` = KEEP
- `B` = BUNDLE
- `P` = PARTS
- `R` = REJECT
- `1-9` = Assign to variant (atomic-purple, vert, violet, etc.)
- `←/→` = Navigate items
- Auto-saves progress to localStorage

**Export when done:**
- Click "Export Sorted Data" button
- Saves to `sorted_items_YYYY-MM-DD.json`

### 3. Merge Sorted Data
```bash
python3 merge_sorted_data.py
```

- Reads `sorted_items_*.json` files
- Updates `scraped_data.json` with categorized items
- Calculates trimmed mean prices
- Generates price_history by month

### 4. Scrape Current Listings (Optional)
```bash
python3 scraper_current_listings.py
```

- Scrapes 5 active eBay listings per variant
- Gets images, prices, conditions
- Saves to `current_listings.json`

### 5. Regenerate Website
```bash
python3 update_site_compact.py
```

- Generates all variant HTML pages
- Creates homepage
- Copies `styles.css`
- Output in `output/` directory

### 6. Commit and Push
```bash
git add .
git commit -m "Daily update: YYYY-MM-DD"
git push origin main
```

GitHub Actions automatically deploys to OVH via FTP.

---

## 🗂️ File Structure

```
prixretro/
├── scraped_data.json              # Master data (sold items)
├── current_listings.json          # Active eBay listings
├── sorted_items_YYYY-MM-DD.json   # Manual sorting export
├── config.json                    # Variant definitions
├── template-v4-compact.html       # HTML template
├── styles.css                     # Global CSS
├── scraper_ebay.py               # Sold listings scraper
├── scraper_current_listings.py   # Current listings scraper
├── create_variant_sorter.py      # Generate sorting interface
├── merge_sorted_data.py          # Merge sorted data
├── update_site_compact.py        # Site generator
├── scripts/
│   └── daily_update.py           # Automated workflow
├── output/                        # Generated website
│   ├── index.html
│   ├── game-boy-color-*.html
│   └── styles.css
└── data/                          # Archives
    ├── bundles/
    ├── parts/
    └── rejected/
```

---

## 🔄 Data Flow

```
eBay Scraper (sold)
     ↓
scraped_data.json (all items)
     ↓
Sorting Interface (manual)
     ↓
sorted_items_YYYY-MM-DD.json
     ↓
Merge Script
     ↓
scraped_data.json (cleaned + categorized)
     ↓
Current Listings Scraper
     ↓
current_listings.json
     ↓
Site Generator
     ↓
output/ (HTML files)
     ↓
GitHub Actions
     ↓
OVH FTP Deploy
     ↓
prixretro.com (LIVE)
```

---

## 🎯 Key Features

### Price History
- Individual sale points (not monthly averages)
- Clickable graph points → eBay URLs
- Auto-tracking tooltip (hover nearest point)
- Green gradient area fill
- Shows item title in tooltip

### Sold Listings Table
- Compact 5-column layout
- Columns: Title, Price, Date, Source, Condition
- Clickable rows → eBay URLs
- Source column for future multi-platform support

### Current Listings
- 5 active eBay listings per variant
- Large cards with images
- Bulkier design than price history
- "EN VENTE" badge
- Fallback image if none available

### Stats Grid
- 4-column layout
- Prix Moyen (trimmed mean)
- Ventes Analysées
- Prix Minimum
- Prix Maximum

---

## 🛠️ Troubleshooting

### Scraper Issues
```bash
# Check seen items
cat .ebay_scraper_progress.json | jq '.seen_item_ids | length'

# Reset seen items (careful!)
rm .ebay_scraper_progress.json
```

### Sorting Interface Not Saving
- Check browser console for errors
- Clear localStorage: `localStorage.clear()`
- Refresh and start over

### Site Not Deploying
```bash
# Check GitHub Actions
gh run list --limit 5

# View latest run logs
gh run view --log
```

### Missing Images
- Images scraped from eBay's CDN
- Fallback to gray placeholder if missing
- Check `current_listings.json` for `image_url` field

---

## 📊 Stats Calculation

### Trimmed Mean (outlier removal)
```python
# Remove top/bottom 10% of prices
sorted_prices = sorted(all_prices)
trim_count = max(1, len(sorted_prices) // 10)
trimmed = sorted_prices[trim_count:-trim_count]
avg_price = sum(trimmed) / len(trimmed)
```

### Price History
```python
# Group by month, calculate average
for listing in listings:
    month_key = listing['sold_date'][:7]  # "2025-12"
    month_prices[month_key].append(listing['price'])

price_history = {
    month: sum(prices) / len(prices)
    for month, prices in month_prices.items()
}
```

---

## 🚀 Production Tips

1. **Run daily** to keep data fresh (GitHub Actions cron job recommended)
2. **Review sorted data** before merging to catch misclassifications
3. **Archive bundles/parts** for future analysis
4. **Monitor eBay** for new variant releases
5. **Update config.json** when adding new variants

---

## 🔐 Credentials

### eBay Partner Network
Edit `config.json`:
```json
{
  "ebay_partner": {
    "campaign_id": "YOUR_CAMPAIGN_ID",
    "network_id": "709-53476-19255-0",
    "tracking_id": "1"
  }
}
```

### GitHub Actions (FTP)
Set repository secrets:
- `FTP_SERVER`
- `FTP_USERNAME`
- `FTP_PASSWORD`

---

## 📈 Future Enhancements

- [ ] Scrape Leboncoin, Vinted, Facebook Marketplace
- [ ] Add condition-based price filtering
- [ ] Email alerts for price drops
- [ ] API endpoint for price data
- [ ] Mobile app
- [ ] Laravel migration for dynamic pages

---

**Last updated:** 2025-12-24
