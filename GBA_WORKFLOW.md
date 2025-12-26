# Game Boy Advance Integration Workflow

**Created:** 2025-12-25
**Status:** In Progress
**Goal:** Add GBA as second console to scale from 9 to ~28 pages

---

## 📋 Complete Workflow

### Phase 1: Data Collection (✅ COMPLETED)

#### 1.1 Scrape Raw GBA Data
```bash
python3 scraper_gba.py
```

**Output:** `scraped_data_gba_raw.json` (396 items)

**What it does:**
- Scrapes ALL Game Boy Advance sold items from eBay.fr (last 3 months)
- Uses `.s-card.s-card--horizontal` selectors
- Filters by "game boy advance" or "gba" in title
- Saves raw data for manual categorization

**Success criteria:**
- ✅ 396 unique GBA items scraped
- ✅ No duplicates (tracked by item_id)

---

#### 1.2 Create Sorting Interface
```bash
python3 create_gba_variant_sorter.py
```

**Output:** `variant_sorter_gba.html`

**What it does:**
- Creates interactive HTML interface for manual sorting
- Loads 396 items from scraped_data_gba_raw.json
- Provides 19 suggested GBA variants
- Auto-saves progress to localStorage (key: `gba_sorter_progress`)

**Features:**
- Keyboard shortcuts: K (Keep), B (Bundle), P (Parts), R (Reject)
- Quick variant assignment: 1-9 keys
- Add custom variants on the fly
- Export to JSON when done

**Success criteria:**
- ✅ 396 items loaded
- ✅ Unique localStorage key (no collision with GBC sorter)
- ✅ All JavaScript functions working

---

### Phase 2: Manual Categorization (🔄 IN PROGRESS)

#### 2.1 Sort Items by Variant

**Steps:**
1. Open `variant_sorter_gba.html` in browser
2. For each item:
   - Assign variant (SP/Micro/Standard + color)
   - Mark status: Keep / Bundle / Parts / Reject
3. Auto-save happens every 30 seconds
4. When done, click "📤 Export Final"
5. Save JSON file (e.g., `gba_sorted_final.json`)

**Common GBA Variants:**
- **Standard:** purple, black, glacier, orange, pink
- **SP:** platinum, cobalt, flame, graphite, pearl-blue, pearl-pink, tribal, famicom, nes
- **Micro:** silver, black, blue, pink, famicom

**Challenge:**
- Metallic colors look very similar in photos (silver/platinum/pearl)
- Requires careful examination of each listing

**Expected Output:**
- ~60-80% marked as "Keep" (valid consoles)
- ~10-20% bundles (console + games)
- ~5-10% parts (broken/for parts)
- ~5-10% rejected (wrong item/duplicate)

**Expected Variants:**
- 15-19 different GBA variants
- Each variant should have 3+ listings minimum

---

### Phase 3: Data Processing (📋 PENDING)

#### 3.1 Process Sorted Data
```bash
python3 process_gba_sorted_data.py gba_sorted_final.json
```

**What it does:**
- Loads exported JSON from sorting interface
- Filters only "Keep" items
- Groups by assigned variant
- Calculates statistics (avg, min, max prices, price history)
- Creates scraped_data_gba.json (GBA only)
- Updates scraped_data_multiconsole.json (adds GBA section)

**Outputs:**
- `scraped_data_gba.json` - GBA variants only
- `scraped_data_multiconsole.json` - Multi-console structure
- `gba_categorized_items.json` - Full categorization details

**Example Output:**
```json
{
  "sp-platinum": {
    "variant_key": "sp-platinum",
    "variant_name": "SP Platinum",
    "description": "Game Boy Advance SP platinum...",
    "stats": {
      "avg_price": 85,
      "min_price": 55,
      "max_price": 150,
      "listing_count": 45
    },
    "listings": [...]
  }
}
```

---

### Phase 4: Multi-Console Migration (📋 PENDING)

#### 4.1 Migrate GBC Data to Multi-Console Format
```bash
python3 migrate_to_multiconsole.py
```

**What it does:**
- Converts flat scraped_data.json to multi-console structure
- Converts flat current_listings.json to multi-console structure
- Creates backups of original files
- Outputs: scraped_data_multiconsole.json, current_listings_multiconsole.json

**Important:**
- Review the new files before activating
- When ready:
  ```bash
  mv scraped_data.json scraped_data_old.json
  mv scraped_data_multiconsole.json scraped_data.json

  mv current_listings.json current_listings_old.json
  mv current_listings_multiconsole.json current_listings.json
  ```

---

### Phase 5: Current Listings Scraping (📋 PENDING)

#### 5.1 Scrape GBA Current Listings
```bash
python3 scraper_gba_current_listings.py
```

**What it does:**
- Scrapes active FOR SALE GBA listings from eBay.fr
- Matches items to GBA variants
- Validates images (rejects placeholders)
- Saves to current_listings_gba.json

**Expected Output:**
- 10-15 current listings per variant
- High-quality images only
- Prices within reasonable range of averages

---

### Phase 6: Site Generation (📋 PENDING)

#### 6.1 Update Site Generator

**Option A: Modify existing update_site_compact.py**
- Add multi-console support
- Maintain backward compatibility

**Option B: Create new update_site_multiconsole.py**
- Clean implementation for multi-console
- Keep old version as fallback

**New Features Needed:**
- Console category pages (e.g., `/game-boy-advance/`)
- Console-aware variant URLs (`/game-boy-advance-sp-platinum.html`)
- Updated homepage showing console categories
- Multi-console sitemap generation

---

#### 6.2 Generate GBA Pages
```bash
python3 update_site_compact.py
# OR
python3 update_site_multiconsole.py
```

**Expected Output:**
- 15-19 new GBA variant pages
- Updated homepage with console categories
- Updated sitemap.xml with GBA URLs
- All pages with correct affiliate links

---

### Phase 7: Deployment (📋 PENDING)

#### 7.1 Test Locally
```bash
cd output
python3 -m http.server 8000
# Open http://localhost:8000
```

**Checklist:**
- [ ] All GBA variant pages load correctly
- [ ] Prices display accurately
- [ ] Charts work (Chart.js)
- [ ] Current listings show with images
- [ ] Affiliate links present and correct
- [ ] Mobile responsive
- [ ] No console errors in browser

---

#### 7.2 Deploy to Production
```bash
# Copy SEO files
cp sitemap.xml robots.txt output/

# Commit and push
git add .
git commit -m "Add Game Boy Advance variants

- 396 GBA items scraped and manually categorized
- 15-19 new variant pages generated
- Multi-console architecture implemented
- Updated homepage with console categories
- Updated sitemap with GBA URLs

🤖 Generated with [Claude Code](https://claude.com/claude-code)

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"

git push
```

**GitHub Actions will:**
1. Run scrapers
2. Regenerate site
3. Deploy via FTP to OVH

---

## 🎯 Success Metrics

### Immediate Goals (After GBA Launch)
- **Pages:** 9 GBC + 18 GBA = 27 pages total
- **Traffic:** Target 2,000-3,000 monthly visitors (from 500-1,000)
- **Revenue:** Target 20-40€/month (from ~10€/month)
- **Data Quality:** 95%+ accuracy in variant categorization

### Long-Term Goals (Q1 2026)
- **Consoles:** GBC + GBA + DS + PSP = 4 consoles
- **Pages:** 70+ variant pages
- **Traffic:** 5,000-10,000 monthly visitors
- **Revenue:** 50-100€/month

---

## 🔧 Troubleshooting

### Issue: Browser shows old GBC data instead of GBA
**Solution:** Hard refresh (Ctrl+Shift+R or Cmd+Shift+R)
- Clear localStorage if needed: DevTools → Application → Local Storage

### Issue: No items visible in variant_sorter_gba.html
**Solution:**
- Check browser console for errors
- Verify file size (should be ~140KB)
- Ensure all JavaScript functions loaded
- Check localStorage key is `gba_sorter_progress`

### Issue: process_gba_sorted_data.py fails
**Solution:**
- Ensure exported JSON has correct format
- Check all "keep" items have assigned_variant
- Verify config_multiconsole.json exists

### Issue: Site generator fails with multi-console data
**Solution:**
- Verify scraped_data.json format matches expected structure
- Check all placeholders in templates exist
- Ensure config.json or config_multiconsole.json is valid JSON

---

## 📝 Key Files Reference

| File | Purpose | Auto-Updated? |
|------|---------|---------------|
| `scraped_data_gba_raw.json` | Raw GBA scrape (396 items) | No |
| `variant_sorter_gba.html` | Sorting interface | No |
| `scraped_data_gba.json` | Processed GBA data | After sorting |
| `current_listings_gba.json` | GBA current listings | Yes (will be) |
| `config_multiconsole.json` | Multi-console config | No |
| `scraped_data_multiconsole.json` | Multi-console data | After migration |

---

## 🚀 Next Console (After GBA)

**Priority Order:**
1. **Nintendo DS** (~3k/month searches) - Highest search volume
2. **PSP** (~2.5k/month searches) - Strong collector interest
3. **Game Boy Classic** (~1.5k/month searches) - Completes GB family

**Use same workflow for each:**
1. Create scraper_[console].py
2. Create variant_sorter_[console].html
3. Manual categorization
4. Process sorted data
5. Update site generator
6. Deploy

---

**Created by:** Claude Sonnet 4.5
**Last Updated:** 2025-12-25
**Status:** Phase 2 in progress (user sorting 396 items)
