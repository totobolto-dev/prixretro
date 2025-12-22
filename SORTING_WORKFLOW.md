# PrixRetro - Sorting Workflow

Complete guide for scraping, sorting, and maintaining your Game Boy Color price data.

## 📋 Complete Workflow

### 1. Initial Scrape (First Time)
```bash
python3 scraper_ebay.py
```
- Scrapes 2 pages (~275 items) from eBay
- Saves to `scraped_data.json`
- **No filtering** - includes consoles, parts, bundles, everything
- Dates are correctly parsed

**Expected output:**
- `scraped_data.json` with ~275 items
- All items initially in "violet" variant (needs sorting)

---

### 2. Sort Items by Variant & Quality

**Open the sorting interface:**
```bash
python3 create_variant_sorter.py
# Then open: variant_sorter.html in your browser
```

**Sorting guide:**

#### Variant Assignment
For each item, select the correct Game Boy Color variant:
- `atomic-purple` - Violet transparent/clear
- `violet` - Standard purple
- `bleu` - Teal/turquoise
- `rouge` - Red
- `vert` - Green
- `jaune` - Yellow
- `pikachu` - Pikachu edition
- `pokemon-gold-silver` - Pokemon edition
- **+ Add new variant** if you find one not listed (e.g., "orange", "gold")

#### Quality Classification

**KEEP** - Single console items to include in price data:
- ✅ Console alone
- ✅ Console + battery/charger
- ✅ Console + 1 game (price not significantly affected)
- ✅ Working consoles

**BUNDLE** - Unclear items you'll decide on later:
- ⚠️ Multiple games included
- ⚠️ Complete-in-box (CIB) sets
- ⚠️ Multiple consoles
- ⚠️ Anything that might skew price

**REJECT** - Items to exclude:
- ❌ Parts only (screens, shells, buttons)
- ❌ Broken/HS (hors service)
- ❌ Accessories only (cables, chargers alone)
- ❌ Wrong items (Game Boy Advance, etc.)
- ❌ Empty boxes

#### Keyboard Shortcuts
- `K` = Mark as KEEP
- `B` = Mark as BUNDLE
- `R` = Mark as REJECT
- `Enter` = Open eBay listing
- `↑/↓` = Navigate items
- `1-9` = Quick variant select (if configured)

#### Filters
- **Status filter:** Show only pending/keep/bundle/reject
- **Variant filter:** Focus on one variant at a time
- **Search:** Find specific items by title

---

### 3. Export Your Sorted Data

Once you've sorted all items:
1. Click **"💾 Export Sorted Data"** button
2. Saves as `sorted_items_YYYY-MM-DD.json`
3. Download to your project folder

---

### 4. Merge Sorted Data Back

```bash
python3 merge_sorted_data.py sorted_items_2025-12-21.json
```

**What it does:**
- ✅ Updates `scraped_data.json` with your KEEP items
- ✅ Organizes items by variant (atomic-purple, violet, etc.)
- ✅ Adds any custom variants to `config.json`
- ✅ Saves BUNDLE items to `bundles_to_review.json` (decide later)
- ✅ Discards REJECT items
- ✅ Creates backup: `scraped_data_backup_TIMESTAMP.json`

**Expected output:**
```
📊 Sorting results:
   ✅ KEEP: 120 items
   ⚠️  BUNDLE: 30 items (saved separately)
   ❌ REJECT: 125 items (discarded)

📋 Summary by variant:
   • atomic-purple: 15 items, avg 75€
   • violet: 45 items, avg 65€
   • bleu: 25 items, avg 68€
   • rouge: 20 items, avg 70€
   • jaune: 15 items, avg 62€
```

---

### 5. Daily Updates (Incremental Scraping)

**Run daily or weekly:**
```bash
python3 scraper_ebay.py
```

**How incremental scraping works:**
1. Scraper loads existing `scraped_data.json`
2. Extracts all existing item_ids
3. Scrapes eBay (same 2 pages)
4. **Skips any item_id already in the file**
5. Only adds NEW items not seen before
6. Merges with existing data

**Example:**
- Day 1: Scrape 275 items → Keep 120
- Day 2: Scrape 275 items → Only 15 are new → Add 15 (total: 135)
- Day 3: Scrape 275 items → Only 8 are new → Add 8 (total: 143)

**After daily scrape:**
1. Open `variant_sorter.html` again
2. Filter: **"Pending only"** to see new items
3. Sort new items by variant
4. Export & merge again

---

## 🔄 Handling Edge Cases

### About Bundle Items

Bundles are saved to `bundles_to_review.json`. Review them later:

```bash
# Check bundle items
cat bundles_to_review.json | jq '.[] | {title, price}'

# Decision for each bundle:
# - Console + 1-2 games → Manually subtract ~20€, add to variant
# - Console + many games → Ignore (too expensive)
# - Multiple consoles → Ignore completely
```

### About Redirecting URLs

**Why some eBay URLs redirect to "in stock" items:**
- Seller relisted the exact same console
- eBay redirects old sold listing → new active listing
- This is normal, just note the price/date from your scraped data

### About Missing Dates

Items with `sold_date: "2025-12-20"` (today) likely:
- Just listed, no sold date yet
- Or date parsing failed

**What to do:**
- Mark as REJECT if unsure
- Or manually check eBay URL to verify sold date

---

## 📁 File Structure After Sorting

```
/
├── scraped_data.json              # Current clean data (KEEP items only)
├── scraped_data_backup_*.json     # Auto backups before merge
├── bundles_to_review.json         # Items marked as BUNDLE
├── variant_sorter.html            # Sorting interface
├── config.json                    # Variants config (updated with custom variants)
└── sorted_items_*.json            # Your exported sorts (archive these)
```

---

## ⚡ Quick Reference

### First time setup:
```bash
python3 scraper_ebay.py                      # Scrape
python3 create_variant_sorter.py             # Generate interface
# Open variant_sorter.html → Sort → Export
python3 merge_sorted_data.py sorted_items_*.json
```

### Daily updates:
```bash
python3 scraper_ebay.py                      # Scrape new items
# Open variant_sorter.html → Filter "Pending" → Sort new items → Export
python3 merge_sorted_data.py sorted_items_*.json
python3 update_site_compact.py               # Generate website
```

### Check current data:
```bash
# See item counts
python3 -c "import json; d=json.load(open('scraped_data.json')); \
print('\n'.join(f'{k}: {len(v[\"listings\"])} items' for k,v in d.items()))"

# See price averages
python3 -c "import json; d=json.load(open('scraped_data.json')); \
print('\n'.join(f'{k}: {v[\"stats\"][\"avg_price\"]}€' for k,v in d.items()))"
```

---

## 🎯 Tips for Efficient Sorting

1. **Start with filters:**
   - Sort by variant first (one color at a time)
   - Use "Pending only" to focus on unsorted items

2. **Use keyboard shortcuts:**
   - `K` for obvious consoles
   - `R` for obvious junk
   - `B` for "not sure, decide later"

3. **Open eBay links when unsure:**
   - Press `Enter` on an item
   - Check if it's really a console or bundle
   - Look at photos to identify variant

4. **Batch similar items:**
   - Sort all "atomic purple" first
   - Then all "violet", etc.
   - Faster to focus on one variant at a time

5. **Don't overthink bundles:**
   - When unsure → mark as BUNDLE
   - Review bundles later when you have time
   - Better to be cautious than include bad data

---

## 🐛 Troubleshooting

### "No items to sort"
- Check `scraped_data.json` exists
- Run `python3 scraper_ebay.py` first

### "Sorted file not found"
- Make sure you exported from variant_sorter.html
- Check filename matches: `sorted_items_YYYY-MM-DD.json`

### "Incremental scraping not skipping items"
- Ensure `scraped_data.json` exists (created by merge script)
- Check item_ids are preserved in the file

### "Too many items still showing"
- Use filters: "Pending only" after first sort
- Or delete `scraped_data.json` to start fresh

---

## 📊 Expected Data Quality

After proper sorting:
- **Keep rate:** ~40-50% (120-135 out of 275)
- **Bundle rate:** ~10-15% (30-40 items to review later)
- **Reject rate:** ~35-50% (parts, broken, wrong items)

**Price ranges by variant (typical):**
- Atomic Purple: 70-90€
- Standard colors: 55-75€
- Pikachu edition: 100-150€
- Pokemon editions: 80-120€

---

## 💡 Future Automation Ideas

Once you're comfortable with the workflow:
1. Auto-classify obvious items (e.g., "broken" → REJECT)
2. ML model to suggest variants based on title
3. Price outlier detection (auto-flag bundles)
4. Automated daily scrape + notification of new items
