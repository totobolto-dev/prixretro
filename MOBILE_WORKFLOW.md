# PrixRetro - Mobile Workflow Guide

Complete workflow for scraping, sorting, and publishing data from your mobile phone.

## 📱 Mobile-Friendly Workflow (New & Improved!)

### Universal Workflow: Scrape → Import → Sort → Publish

**✨ Everything works from mobile now - progress syncs between devices!**

---

### Step 1: Scrape eBay Data

**From Admin Panel (Mobile):**
1. Login: **https://www.prixretro.com/admin/listings**
2. Click **"Scrape eBay"** button
3. Select console (GBC/GBA/DS)
4. Wait for completion notification

**From Terminal (if preferred):**
```bash
php artisan scrape:gbc   # Game Boy Color
php artisan scrape:gba   # Game Boy Advance
php artisan scrape:ds    # Nintendo DS
```

---

### Step 2: Import Raw Data

**Two Import Options:**

#### Option A: Pre-sorted Data (GBC/GBA)
For data that already has variants assigned:
1. Click **"Import Scraped Data"** button
2. Select console
3. Done - items go straight to Listings for review

#### Option B: Raw Unsorted Data (DS, Mixed Data)
For data that needs console/variant classification:
1. Click **"Import Raw Data (for sorting)"** button
2. Select file (e.g., Nintendo DS raw)
3. Items imported as "unclassified" status
4. **Go to Sort Listings page** (next step)

---

### Step 3: Sort & Classify Items

**Visit: https://www.prixretro.com/admin/sort-listings**

**Features:**
- ✅ **Server-side progress** - Resume on any device (mobile/laptop)
- ✅ **Mobile-optimized** interface
- ✅ **Real-time filtering** by status, console, search
- ✅ **Progress bar** shows completion percentage

**For Each Item:**
1. Select **Console Type** (DS Lite, DSi, 3DS, etc.)
2. Select **Variant** (color/edition)
3. Click **Save & Next** or **Reject & Next**
4. Progress auto-saves to database

**Keyboard Shortcuts (Desktop):**
- `Enter`: Save & Next
- `Space`: Skip
- `←`: Previous
- `R`: Reject

---

### Step 4: Review & Approve

**Visit: https://www.prixretro.com/admin/listings**

1. Filter by **"Pending Only"**
2. Review classified listings
3. **Bulk Actions:**
   - Select multiple rows
   - Click **Approve Selected** or **Reject Selected**
4. Or edit individually

---

### Step 5: Publish to Production

**From Admin Panel:**
1. Click **"Sync to Production"** button
2. Confirm sync
3. Done! 🎉

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

## 🔍 Google Search Console - Sitemap Submission

**Yes, you should manually submit your sitemap:**

1. Visit: https://search.google.com/search-console
2. Select your property (prixretro.com)
3. Go to: **Sitemaps** (left sidebar)
4. Enter: `https://www.prixretro.com/sitemap.xml`
5. Click **Submit**

**Status Check:**
- Sitemap updated: ✅ 2026-01-01
- URLs in sitemap: 23 (9 GBC + 13 GBA + homepage)
- Google will crawl within 24-48 hours

---

**Last Updated**: 2026-01-01
**Current Phase**: Server-side sorting workflow + Mobile-first admin panel
