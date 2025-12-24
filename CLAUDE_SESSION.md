# Claude Code Session Context

**Last Updated:** 2025-12-25 (Current session)
**Branch:** main
**Latest Commit:** 034514d - Fix variant display names
**New Features:** Playwright scraper + 6-hour auto-refresh via GitHub Actions

---

## 🎯 PROJECT STATUS

### ✅ COMPLETED FEATURES

#### 1. **Figma Design System** (2025-12-24)
- Applied exact Figma color palette (#1a1f29, #2a2f39, #6b7280, #9ca3af)
- Set 3px border-radius everywhere (user preference over Figma's 0px)
- Removed ALL gradients (text, backgrounds, hero)
- Reduced padding throughout (1rem standard)
- Removed emojis from headings and buttons
- Extracted CSS to external `styles.css` file

#### 2. **Price History Enhancements**
- ✅ **Source Column** added to table (eBay, Mercari, Facebook, etc.)
- ✅ **Item Titles in Tooltips** - shows full listing name on hover
- ✅ **Auto-tracking mode** - `interaction: { mode: 'nearest' }`
- ✅ **Green gradient fill** - `rgba(0, 255, 136, 0.1)`
- ✅ **Clickable points** - opens eBay listing
- Graph shows individual sales (not monthly averages)

#### 3. **Stats Grid**
- Changed from 2-column to 4-column layout
- Shows: Prix Moyen, Ventes Analysées, Prix Minimum, Prix Maximum
- Left-aligned text (Figma style)
- Compact sizing (1rem padding)

#### 4. **Current Listings Section** (✅ FIXED - Using Playwright)
- Scraper: `scraper_current_listings.py` (now using Playwright)
- **Automated refresh:** Every 6 hours via GitHub Actions
- Price filter: ±30% of average (filters outliers)
- Large image cards (bulkier than price history)
- "EN VENTE" badge
- Fallback placeholder image
- **SOLVED:** eBay anti-bot protection bypassed with headless browser

#### 5. **Automated Workflow**
- **GitHub Actions** (`.github/workflows/scrape-current-listings.yml`):
  - Runs every 6 hours (00:00, 06:00, 12:00, 18:00 UTC)
  - Scrapes current listings → regenerates site → auto-deploys
- **Local script** (`scripts/daily_update.py`):
  - Full manual automation: merge → scrape → regenerate → commit → push
  - `--no-push` flag for manual review
- Complete documentation in `DAILY_WORKFLOW.md`

#### 6. **Bug Fixes**
- ✅ Fixed unclosed `twitter:description` meta tag
- ✅ Fixed variant names (Atomic-Purple → Atomic Purple)
- ✅ Updated merge_sorted_data.py to use config['variants'][key]['name']

---

## 📂 PROJECT STRUCTURE

```
prixretro/
├── scraped_data.json              # Master data (sold items)
├── current_listings.json          # Active eBay listings (empty - eBay blocking)
├── sorted_items_YYYY-MM-DD.json   # Manual sorting export
├── config.json                    # Variant definitions
├── template-v4-compact.html       # HTML template
├── styles.css                     # Global CSS (Figma colors)
├── index.html                     # Homepage template
│
├── scraper_ebay.py               # Sold listings scraper
├── scraper_current_listings.py   # Current listings scraper (eBay blocking)
├── create_variant_sorter.py      # Generate sorting interface
├── merge_sorted_data.py          # Merge sorted data
├── update_site_compact.py        # Site generator
│
├── scripts/
│   └── daily_update.py           # Automated workflow
│
├── .github/workflows/
│   ├── deploy.yml                # FTP deployment to OVH
│   └── scrape-current-listings.yml  # Auto-scrape every 6 hours
│
├── output/                        # Generated website
│   ├── index.html
│   ├── game-boy-color-*.html     # 9 variant pages
│   ├── styles.css
│   └── .htaccess
│
├── data/                          # Archives
│   ├── bundles/
│   ├── parts/
│   └── rejected/
│
├── figma/                         # Figma design reference
│   └── src/app/components/
│
├── DAILY_WORKFLOW.md             # Complete workflow guide
├── CLAUDE_SESSION.md             # This file (session context)
└── README.md
```

---

## 🚀 DAILY WORKFLOW (Quick Reference)

### Automated (Recommended)
```bash
python3 scripts/daily_update.py          # Full workflow
python3 scripts/daily_update.py --no-push  # Review before push
```

### Manual Step-by-Step
```bash
# 1. Scrape sold items
python3 scraper_ebay.py

# 2. Sort by variant (manual)
python3 create_variant_sorter.py
open item_review_interface.html
# Use: K/B/P/R + 1-9, then export

# 3. Merge sorted data
python3 merge_sorted_data.py

# 4. Scrape current listings (⚠️ eBay blocking)
python3 scraper_current_listings.py

# 5. Regenerate site
python3 update_site_compact.py

# 6. Deploy
git add .
git commit -m "Daily update: $(date +%Y-%m-%d)"
git push origin main
```

---

## 🎨 DESIGN SYSTEM

### Colors (Figma Palette)
```css
--bg-primary: #0f1419;      /* Main background */
--bg-secondary: #1a1f29;    /* Cards, header */
--bg-card: #1a1f29;         /* Card backgrounds */
--accent-primary: #00d9ff;  /* Links */
--text-primary: #ffffff;    /* Main text */
--text-secondary: #6b7280;  /* Secondary text */
--text-muted: #9ca3af;      /* Muted text */
--success: #00ff88;         /* Prices (green) */
--border: #2a2f39;          /* Borders */
--radius: 3px;              /* Border radius */
```

### Component Spacing
- **Standard padding:** 1rem
- **Compact padding:** 0.75rem
- **Stats cards:** 1rem padding, 1rem gap
- **Listings table:** 0.75rem padding

### Typography
```css
h1: 2.4rem
h2: 1.25rem (weight: 500)
body: System fonts
```

---

## 🔧 KEY CODE PATTERNS

### Chart.js Configuration
```javascript
// Auto-tracking + tooltips with item titles
interaction: {
    mode: 'nearest',
    axis: 'x',
    intersect: false
},
tooltip: {
    callbacks: {
        title: function(context) {
            const index = context[0].dataIndex;
            const title = chartData.titles[index];
            return title.length > 50 ? title.substring(0, 50) + '...' : title;
        },
        afterLabel: function(context) {
            const index = context[0].dataIndex;
            return chartData.labels[index] + ' • Cliquer pour voir';
        }
    }
}
```

### Price Filtering (Current Listings)
```python
# Filter: only show listings within ±30% of average price
avg_price = stats['avg_price']
min_acceptable = avg_price * 0.7  # -30%
max_acceptable = avg_price * 1.3  # +30%

filtered_listings = [
    l for l in all_current_listings
    if min_acceptable <= l['price'] <= max_acceptable
]
```

### Variant Name Resolution
```python
# Always use config['variants'][key]['name']
variant_config = config['variants'].get(variant_key, {})
variant_name = variant_config.get('name', variant_key.replace('-', ' ').title())
```

---

## ⚠️ KNOWN ISSUES

### 1. ~~eBay Anti-Bot Protection (Current Listings)~~ ✅ FIXED
**Problem:** eBay returns "Nous sommes désolés..." page for active listings scraper

**Solution Implemented:** Playwright (headless browser automation)
- Using `async_playwright()` with Chromium
- Browser launch args: `--no-sandbox`, `--disable-setuid-sandbox`
- Realistic viewport (1920x1080) + Chrome User-Agent
- 2-second delays between variants
- Works perfectly in GitHub Actions environment

**GitHub Actions Automation:**
- Workflow: `.github/workflows/scrape-current-listings.yml`
- Schedule: Every 6 hours (cron: `0 */6 * * *`)
- Auto-commits and deploys when changes detected

**Local Testing:**
- Requires: `sudo playwright install-deps chromium`
- Note: System dependencies need sudo access
- Production: Use GitHub Actions (recommended)

---

## 📊 DATA FLOW

```
eBay Scraper (sold) → scraped_data.json (raw)
                           ↓
      Sorting Interface (manual)
                           ↓
      sorted_items_YYYY-MM-DD.json
                           ↓
         Merge Script
                           ↓
      scraped_data.json (cleaned + categorized)
                           ↓
      Current Listings Scraper (✅ Playwright - every 6h)
                           ↓
      current_listings.json
                           ↓
        Site Generator
                           ↓
         output/ (HTML)
                           ↓
       GitHub Actions
                           ↓
        OVH FTP Deploy
                           ↓
      prixretro.com (LIVE)
```

---

## 🔑 IMPORTANT NOTES

### Variant Keys vs Display Names
```json
// config.json
"atomic-purple": {
  "name": "Atomic Purple (Violet Transparent)",  // ✅ Use this
  "description": "...",
  "search_terms": ["game boy color atomic purple"]  // No dashes!
}

// scraped_data.json
"atomic-purple": {
  "variant_key": "atomic-purple",                      // Key (kebab-case)
  "variant_name": "Atomic Purple (Violet Transparent)", // Display (from config)
  "description": "..."
}
```

### Price Statistics (Trimmed Mean)
```python
# Remove top/bottom 10% to eliminate outliers
sorted_prices = sorted(all_prices)
trim_count = max(1, len(sorted_prices) // 10)
trimmed_prices = sorted_prices[trim_count:-trim_count]
avg_price = sum(trimmed_prices) / len(trimmed_prices)
```

### Git Workflow
```bash
# Feature branch (optional)
git checkout -b feature/new-feature

# Commit with co-author
git commit -m "Message

🤖 Generated with [Claude Code](https://claude.com/claude-code)

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"

# Push to main
git push origin main  # GitHub Actions auto-deploys
```

---

## 🐛 DEBUGGING COMMANDS

```bash
# Check variant names
jq -r '.[] | "\(.variant_key): \(.variant_name)"' scraped_data.json

# Check current listings scraper
python3 scraper_current_listings.py 2>&1 | head -50

# View generated chart code
grep -A 30 "interaction:" output/game-boy-color-atomic-purple.html

# Check GitHub Actions
gh run list --limit 5
gh run view --log

# Test site generator
python3 update_site_compact.py 2>&1 | grep -E "(Error|Warning|✅)"

# Count listings per variant
jq -r '.[] | "\(.variant_key): \(.stats.listing_count)"' scraped_data.json
```

---

## 📈 STATISTICS

**Current Data (as of 2025-12-24):**
- Total variants: 9
- Total sold listings: 91
- Average price: 105€
- Date range: Last 6 months
- Sources: eBay (100%)

**Variants:**
1. atomic-purple: 19 listings, avg 76€
2. vert: 11 listings
3. violet: 15 listings
4. rouge: 10 listings
5. bleu: 9 listings
6. jaune: 9 listings
7. pokemon-center-3rd-anniversary: 6 listings
8. pokemon-special-limited-edition: 7 listings
9. pokemon-special-edition: 5 listings

---

## 🎯 NEXT STEPS

### ~~Immediate (Fix eBay Blocking)~~ ✅ DONE
1. ~~Implement Selenium/Playwright for current listings scraper~~ ✅
2. ~~Set up GitHub Actions cron job for auto-refresh~~ ✅

### Short-term
- [ ] Add Leboncoin scraper
- [ ] Add Vinted scraper
- [ ] Add Facebook Marketplace scraper
- [ ] Condition-based price filtering
- [ ] Email alerts for price drops

### Long-term
- [ ] Laravel migration (dynamic pages)
- [ ] API endpoint for price data
- [ ] Mobile app
- [ ] Multi-platform aggregation
- [ ] User accounts & watchlists

---

## 💾 SESSION RECOVERY

If you need to restore context after restarting:

1. **Read this file:** `CLAUDE_SESSION.md`
2. **Check recent commits:** `git log --oneline -10`
3. **Review workflow:** `DAILY_WORKFLOW.md`
4. **Check git status:** `git status`
5. **Test generation:** `python3 update_site_compact.py`

---

## 🔗 USEFUL LINKS

- **Live Site:** https://www.prixretro.com
- **GitHub Repo:** https://github.com/totobolto-dev/prixretro
- **GitHub Actions:** https://github.com/totobolto-dev/prixretro/actions
- **Figma Reference:** `/home/ganzu/Documents/prixretro/prixretro/figma/`

---

## 📝 RECENT COMMITS

```
034514d - Fix variant display names to use proper human-readable format
5d130d2 - Fix current listings scraper to use scraped_data.json
adcd08e - Change current listings price filter to ±30% of average
c883220 - Add comprehensive features: source column, tooltips, current listings
328b2f8 - Implement Figma design system with clean, data-focused aesthetic
```

---

## 🤝 COLLABORATION NOTES

**Working with Claude Code:**
- This project uses Claude Code CLI
- Session can be resumed (context preserved)
- Todo list tracking for complex tasks
- Automated git commit messages with co-author tag

**Key Files for Claude:**
- This file: Session context and recovery
- DAILY_WORKFLOW.md: Complete workflow documentation
- README.md: Project overview
- config.json: Variant definitions

---

**End of session context. You can safely restart VSCode now! 🚀**
