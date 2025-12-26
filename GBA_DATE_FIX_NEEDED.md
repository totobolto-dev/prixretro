# 🚨 GBA Date Issue - FIX BEFORE LAUNCH

**Reported:** 2025-12-26
**Status:** Known issue
**Priority:** HIGH (affects price graphs and sorting)

---

## Problem

The scraped GBA data (`scraped_data_gba_raw.json`) has **incorrect sold dates**.

**Impact:**
- ❌ Price graphs will show wrong timeline
- ❌ "Recent sales" sorting won't work
- ❌ Can't analyze price trends accurately

---

## Root Cause (To Investigate)

Possible issues in `scraper_gba.py`:

1. **Date parsing regex broken** (lines 166-200)
   - French month names might not match eBay's format
   - Date parsing might be failing silently

2. **Defaulting to today's date** (line 205)
   ```python
   if not sold_date:
       sold_date = datetime.now().strftime('%Y-%m-%d')  # ← All failures become "today"!
   ```

3. **eBay changed their HTML structure**
   - Selector `.su-styled-text.POSITIVE` might be wrong
   - Date might be in different element now

---

## How to Fix

### Option 1: Re-scrape with Fixed Parser (BEFORE sorting)

**If dates are ALL wrong:**
1. Fix date parsing in `scraper_gba.py`
2. Re-run scraper: `python3 scraper_gba.py`
3. Get fresh 396 items with correct dates
4. THEN sort in variant_sorter_gba.html

**Pros:** Clean data from the start
**Cons:** Have to re-scrape

---

### Option 2: Fix During Image Download (AFTER sorting)

**If only SOME dates are wrong:**
1. When downloading images with `download_listing_images.py`
2. Visit each listing page anyway
3. Parse the REAL date from the listing page
4. Update the date in JSON

**Enhancement needed in `download_listing_images.py`:**
```python
def get_all_images_from_listing(item_url, session):
    """Visit listing page and extract images + REAL DATE"""

    # ... existing image extraction ...

    # NEW: Extract actual sold date from listing page
    date_elem = soup.select_one('.ACTUAL_DATE_SELECTOR')
    if date_elem:
        real_date = parse_date(date_elem.get_text())
        return (image_urls, real_date)  # Return both!
```

**Pros:** Fix dates while getting images (kill 2 birds)
**Cons:** More complex

---

### Option 3: Manual Date Check (Quick & Dirty)

**For each variant after sorting:**
1. Open a few eBay URLs from sorted data
2. Check if dates look correct
3. If dates are systematically off, apply offset:
   ```python
   # If all dates are 30 days in future, subtract 30 days
   from datetime import datetime, timedelta

   for item in items:
       old_date = datetime.strptime(item['sold_date'], '%Y-%m-%d')
       new_date = old_date - timedelta(days=30)
       item['sold_date'] = new_date.strftime('%Y-%m-%d')
   ```

**Pros:** Quick fix if pattern is simple
**Cons:** Only works if offset is consistent

---

## Action Items

### BEFORE Processing Sorted Data:

- [ ] **Check a few GBA items manually**
  - Open 5-10 URLs from `scraped_data_gba_raw.json`
  - Compare `sold_date` in JSON vs actual date on eBay
  - Determine if issue is systemic or random

- [ ] **If ALL dates wrong:**
  - Fix `scraper_gba.py` date parser
  - Re-scrape 396 items
  - Re-create sorting interface
  - User re-sorts (painful but necessary)

- [ ] **If SOME dates wrong:**
  - Note which ones are wrong
  - Fix during image download step
  - Or fix in post-processing

- [ ] **If dates are OFFSET (e.g., all 30 days off):**
  - Apply systematic correction
  - Update all dates with offset

---

## Where to Check

**File:** `scraper_gba.py`
**Lines:** 158-206 (date parsing logic)

**Current logic:**
```python
# Extract date parts
parts = date_text.lower().replace('.', '').split()
day = None
month = None
year = None

for i, part in enumerate(parts):
    # Look for day (number)
    if part.isdigit() and 1 <= int(part) <= 31 and day is None:
        day = part.zfill(2)
    # Look for month (French/English abbreviation)
    elif part in months_fr:
        month = months_fr[part]
    # Look for year (4 digits)
    elif part.isdigit() and len(part) == 4:
        year = part

if day and month and year:
    sold_date = f"{year}-{month}-{day}"
```

**Test with actual eBay date format:**
- Check what eBay actually shows: "Vendu le 20 déc. 2024" vs "20 déc 2024" vs other?
- Verify month abbreviations match

---

## Quick Test

```bash
# Open scraped data
cat scraped_data_gba_raw.json | jq '.[0].sold_date'

# Expected: Date in past (e.g., "2024-11-15", "2024-12-01")
# If you see: "2025-12-26" (today) → dates are defaulting!
# If you see: "2025-01-25" (future) → parsing is broken!
```

---

## Remember When Processing GBA:

**AFTER user finishes sorting, BEFORE running `process_gba_sorted_data.py`:**

1. ✅ Verify dates are correct (spot check 10 items)
2. ✅ Fix if needed (re-scrape or apply correction)
3. ✅ THEN process sorted data
4. ✅ THEN download images

**Don't launch GBA with wrong dates!** Price graphs will be meaningless.

---

## Status: TO DO

- [ ] User finishes GBA sorting
- [ ] Check dates in sorted export
- [ ] Fix dates if needed
- [ ] Process sorted data with correct dates
- [ ] Launch GBA pages

**This issue is tracked. Won't forget! 👍**
