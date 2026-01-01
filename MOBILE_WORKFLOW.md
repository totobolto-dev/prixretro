# PrixRetro - Mobile Workflow Guide

Complete workflow for scraping, sorting, and publishing data from your mobile phone.

## 📱 Mobile-Friendly Workflow

### Option 1: DS Data Sorting (Current Task)

**Status**: 1,257 DS items scraped, ready to sort

#### Step 1: Sort DS Data (Mobile Browser)
1. Visit: **https://www.prixretro.com/ds_sorter.html**
2. For each item:
   - Select **Console Type** (DS Lite, DSi, 3DS, etc.)
   - Select **Variant** (color/edition)
   - Click **Keep** or **Reject**
3. Progress auto-saves to your browser
4. When done, click **Export JSON**
5. Download saves as `ds_sorted_data.json`

#### Step 2: Process Sorted Data (Laptop)
```bash
# Move downloaded file to project
mv ~/Downloads/ds_sorted_data.json /path/to/prixretro/storage/app/

# Process the sorted data
python3 scripts/process_ds_sorted.py

# This creates: storage/app/scraped_data_ds_processed.json
```

#### Step 3: Import to Database (Admin Panel or SSH)
```bash
# Via SSH/Terminal
php artisan import:scraped storage/app/scraped_data_ds_processed.json

# OR via Admin Panel
# Login to: https://www.prixretro.com/admin/listings
# Click: "Import Scraped Data" button
# Select: "Nintendo DS (processed)"
```

#### Step 4: Review & Publish (Mobile/Desktop)
1. Visit: **https://www.prixretro.com/admin/listings**
2. Review imported listings
3. Bulk approve/reject as needed
4. Click **Sync to Production** button

---

### Option 2: Scrape & Import GBC/GBA (Admin Panel)

**Perfect for mobile - no terminal needed!**

#### From Admin Panel (Mobile-Friendly)
1. Login: **https://www.prixretro.com/admin/listings**

2. **Scrape eBay**:
   - Click "Scrape eBay" button
   - Select console (GBC/GBA/DS)
   - Wait for completion notification

3. **Import Scraped Data**:
   - Click "Import Scraped Data" button
   - Select console
   - Confirm import

4. **Review Listings**:
   - Filter by "Pending Only"
   - Click into each listing
   - Change status to "Approved" or "Rejected"
   - OR: Select multiple → Bulk Actions → Approve/Reject

5. **Sync to Production**:
   - Click "Sync to Production" button
   - Confirm sync
   - Done!

---

## 🎮 Available Admin Panel Actions

### Header Actions (Top of Page)
- **🔍 Scrape eBay**: Run Python scraper for GBC/GBA/DS
- **📥 Import Scraped Data**: Import JSON files to database
- **☁️ Sync to Production**: Push approved listings to live CloudDB

### Bulk Actions (Select Rows)
- **✓ Approve Selected**: Approve multiple listings at once
- **✗ Reject Selected**: Reject multiple listings at once

---

## 📊 DS Data Breakdown

**Total Items**: 1,257

| Console | Count | % |
|---------|-------|---|
| 3DS XL | 263 | 20.9% |
| DS Lite | 250 | 19.9% |
| 3DS | 183 | 14.6% |
| 2DS | 128 | 10.2% |
| DS Original | 116 | 9.2% |
| Other/Unknown | 169 | 13.4% |

**Action Required**: Sort into console types and variants using ds_sorter.html

---

## 🔧 Terminal Commands (If Needed)

### Scraping
```bash
# Scrape Game Boy Color
php artisan scrape:gbc

# Scrape Game Boy Advance
php artisan scrape:gba

# Scrape Nintendo DS
php artisan scrape:ds
```

### Import
```bash
# Import GBC data
php artisan import:scraped storage/app/scraped_data_gbc.json

# Import GBA data
php artisan import:scraped storage/app/scraped_data_gba.json

# Import DS processed data
php artisan import:scraped storage/app/scraped_data_ds_processed.json
```

### Sync to Production
```bash
php artisan sync:production
```

---

## 📝 Current Status

### ✅ Live Data
- **Game Boy Color**: 9 variants, ~91 items
- **Game Boy Advance**: 13 variants, data live

### 🚧 Pending
- **Nintendo DS**: 1,257 items scraped, needs sorting
  - Use: https://www.prixretro.com/ds_sorter.html

### 🔄 Next Steps
1. Sort DS data on mobile (30-60 min)
2. Export and process sorted data
3. Import to database
4. Review and approve
5. Sync to production

---

## 🎯 Quick Tips

### Mobile Sorting Best Practices
- **DS Lite** was most popular (~70% of items)
- **DSi** has cameras, DS Lite doesn't
- Check title carefully for "XL", "New", "2DS"
- Use search box to find specific colors
- Filter by console type to focus on one at a time

### Keyboard Shortcuts (Desktop)
- `Ctrl+S`: Save progress in sorter
- Arrow keys: Navigate in admin panel

### Admin Panel Mobile Tips
- Landscape mode works better for tables
- Use filters to reduce clutter
- Bulk actions save time - select multiple rows

---

## 📞 Support

If something breaks:
- Check `/admin/listings` for import status
- Check `storage/logs/laravel.log` for errors
- Re-run commands with verbose flag: `-v`

---

**Last Updated**: 2026-01-01
**Current Phase**: DS data sorting + Mobile workflow setup
