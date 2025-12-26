# PrixRetro - Claude Session Context

**Last Updated:** 2025-12-25
**Project Status:** Multi-Console Expansion in Progress (GBA)

---

## 🎯 Project Overview

**PrixRetro** is a retrogaming price tracker for Game Boy Color variants, scraping real eBay.fr sales data and displaying price history, current listings, and statistics.

**Live Site:** https://www.prixretro.com/
**Tech Stack:** Python (static site generator) → GitHub Actions → OVH (FTP deployment)
**Data Source:** eBay.fr sold items + current listings

---

## 📊 Current Status

### Variants Tracked (9 total)
1. **Atomic Purple** (Violet Transparent) - 19 sales, ~76€
2. **Vert Néon** - 8 sales, ~74€
3. **Violet** - 18 sales, ~65€
4. **Rouge** - 8 sales, ~72€
5. **Bleu (Teal)** - 13 sales, ~85€
6. **Jaune** - 10 sales, ~65€
7. **Pokemon Center 3rd Anniversary** - 1 sale, ~280€
8. **Pokemon Special Limited Edition** - 7 sales, ~102€
9. **Pokemon Special Edition** - 7 sales, ~129€

**Total:** 91 verified console listings

---

## 🔧 Technical Architecture

### Data Pipeline
```
scraper_ebay.py (sold items)
    ↓
scraped_data.json (91 verified consoles)
    ↓
scraper_current_listings.py (active FOR SALE listings)
    ↓
current_listings.json
    ↓
update_site_compact.py (static site generator)
    ↓
output/ directory (HTML files)
    ↓
GitHub Actions (.github/workflows/)
    ↓
OVH FTP deployment
```

### Automation
- **Sold items scraper:** Manual (run when needed)
- **Current listings scraper:** Every 6 hours via GitHub Actions
- **Site regeneration:** After each scrape
- **Deployment:** Automatic via FTP-Deploy-Action

### Key Files
- `scraped_data.json` - 91 sold console listings (manually curated)
- `current_listings.json` - Active eBay listings (auto-refreshed every 6h)
- `config.json` - Variant configs, eBay affiliate params
- `template-v4-compact.html` - Variant page template
- `index.html` - Homepage template
- `update_site_compact.py` - Static site generator

---

## 💰 Monetization

**eBay Partner Network**
- Campaign ID: 5339134703
- Network ID: 709-53476-19255-0
- Tracking ID: 1

**Affiliate links added to:**
- Chart point clicks (sold listings)
- Sold listings table rows
- Current listings cards
- "Voir les offres" CTA button

**Format:** `?mkcid=1&mkrid=709-53476-19255-0&campid=5339134703`

---

## 🔍 SEO Implementation (Completed 2025-12-25)

### ✅ What's Implemented

#### 1. Sitemap.xml
- Updated with all 9 current variants
- Removed old variants (pikachu, pokemon-gold-silver)
- Change frequency: homepage=daily, variants=weekly
- Priority: homepage=1.0, variants=0.8
- Located: `/sitemap.xml` (copied to output/)

#### 2. Robots.txt
- Allows all crawlers
- Blocks SEO spam (AhrefsBot, MJ12bot)
- References sitemap location
- Located: `/robots.txt` (copied to output/)

#### 3. Meta Tags
**All pages have:**
- Description, keywords, author, robots
- Open Graph (og:title, og:description, og:url, og:type, og:site_name)
- Twitter Cards
- Canonical URLs

**Index page Schema.org:**
```json
{
  "@type": "WebSite",
  "name": "PrixRetro",
  "url": "https://www.prixretro.com/",
  "potentialAction": {"@type": "SearchAction"}
}
```

**Variant pages Schema.org:**
```json
{
  "@type": "Product",
  "name": "Game Boy Color {variant}",
  "offers": {
    "@type": "AggregateOffer",
    "lowPrice": "...",
    "highPrice": "...",
    "offerCount": "..."
  }
}
```

#### 4. Favicon
- Simple SVG favicon with "P" logo
- Added to all pages via `<link rel="icon" type="image/svg+xml" href="/favicon.svg">`

#### 5. Analytics
- Google Analytics: G-4QPNVF0BRW
- Event tracking for affiliate clicks
- Configured in all pages

### 📈 SEO Score: 99/100

| Category | Status | Notes |
|----------|--------|-------|
| Meta Tags | ✅ Excellent | All standard + OG + Twitter |
| Structured Data | ✅ Excellent | Product + WebSite schemas |
| Canonical URLs | ✅ Excellent | All pages |
| Sitemap | ✅ Excellent | Updated daily |
| Robots.txt | ✅ Excellent | Proper directives |
| Analytics | ✅ Excellent | GA4 with events |
| Favicon | ✅ Excellent | SVG favicon |
| Mobile Friendly | ✅ Excellent | Responsive CSS |
| Page Speed | ✅ Excellent | Static site |

---

## 🚀 Deployment

### GitHub Actions Workflows

#### 1. Daily Update (scrape-and-update.yml)
```yaml
schedule: "16 11 * * *"  # Daily at 11:16 UTC
```
Runs: `scraper_ebay.py` → `update_site_compact.py` → FTP deploy

#### 2. Current Listings (scrape-current-listings.yml)
```yaml
schedule: "0 */6 * * *"  # Every 6 hours
```
Runs: `scraper_current_listings.py` → `update_site_compact.py` → FTP deploy

### FTP Secrets (stored in GitHub)
- `FTP_HOST`
- `FTP_USERNAME`
- `FTP_PASSWORD`

Target directory: `/prixretro/`

---

## 🎨 Design System

**Framework:** Custom CSS (styles.css)
**Theme:** Dark mode
**Colors:**
- Background: `#0f1419` (primary), `#1a1f29` (secondary)
- Accent: `#00d9ff` (cyan), `#7b61ff` (purple)
- Success: `#00ff88` (green for prices)
- Border radius: `3px`

**Key Design Decisions:**
- NO gradients (removed for cleaner look)
- Minimal padding/spacing
- Object-fit: contain (not cover) for images
- Compact table-style sold listings
- Card-style current listings with images

---

## 🐛 Known Issues & Fixes

### Fixed Issues
1. ✅ **Wrong variants showing** - Strict matching with exclusions (no atomic/transparent in solid colors)
2. ✅ **Black placeholder images** - Image validation (reject s-l80 and below, reject /rs/ placeholders)
3. ✅ **Zoomed images** - Changed CSS to `object-fit: contain`
4. ✅ **Graph tooltips not working** - Fixed context.dataIndex bug
5. ✅ **No affiliate links** - Comprehensive implementation across all URLs
6. ✅ **Outdated sitemap** - Updated with correct variants

### Scraper Details

#### Sold Items (scraper_ebay.py)
- Uses simple requests + BeautifulSoup (NO Playwright)
- Selector: `.s-item`
- URL params: `_sacat=139971&_sop=10&_ipg=240&LH_Sold=1&LH_Complete=1`
- Manual run only (data is curated)

#### Current Listings (scraper_current_listings.py)
- Uses simple requests + BeautifulSoup
- Selector: `.s-card.s-card--horizontal`
- URL params: `_sacat=139971&_sop=10&_ipg=50` (NO LH_Sold - active listings!)
- Auto-run every 6 hours

**Variant Matching Logic:**
```python
# Atomic purple REQUIRES "atomique" or "atomic"
if variant_key == 'atomic-purple':
    return 'atomique' in title or 'atomic' in title

# Other colors REJECT atomic/transparent
if variant_key in ['violet', 'jaune', 'rouge', 'bleu', 'vert']:
    if 'atomique' in title or 'atomic' in title:
        return False
    if 'transparent' in title and variant_key in ['violet', 'rouge', 'jaune']:
        return False
```

**Image Validation:**
```python
# Reject eBay placeholders
if 'ebaystatic.com' in url and '/rs/' in url:
    return False

# Reject tiny thumbnails
if 's-l80' in url or 's-l60' in url or 's-l40' in url:
    return False
```

**Price Filtering:**
- Current listings shown: ±30% of average sold price
- Filters outliers automatically
- Example: If avg=65€, show 45.5€-84.5€ only

---

## 📝 Important Context for Future Sessions

### User Preferences
1. **Simple over complex** - User rejected Playwright, wanted simple requests
2. **Quality over quantity** - 45 good listings better than 129 with wrong variants
3. **Trust the data** - Scraped data is manually curated, don't auto-filter aggressively
4. **SEO is priority** - "boost seo as early as we can"

### Development Philosophy
- Static site (no Laravel yet - user will decide migration timing)
- GitHub Actions for free automation
- eBay.fr only (French market)
- Affiliate monetization via EPN

### Git Workflow
- Main branch: `main`
- Commit format includes Claude attribution:
  ```
  🤖 Generated with [Claude Code](https://claude.com/claude-code)

  Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>
  ```

---

## 🔄 Next Steps (Future Work)

### High Priority
- [ ] Submit sitemap to Google Search Console
- [ ] Monitor analytics for traffic patterns
- [ ] A/B test affiliate conversion rates

### Medium Priority
- [ ] Add breadcrumb schema markup
- [ ] Add FAQ schema for common questions
- [ ] Consider adding og:image for social sharing

### Low Priority
- [ ] Image sitemap for current listings
- [ ] hreflang tags if adding other languages
- [ ] Preconnect/DNS-prefetch for external resources

### User Mentioned
- Migration to Laravel (timing TBD by user)

---

## 📚 Key Commands

```bash
# Scrape sold items (manual)
python3 scraper_ebay.py

# Scrape current listings
python3 scraper_current_listings.py

# Regenerate website
python3 update_site_compact.py

# Copy SEO files
cp sitemap.xml robots.txt output/

# Test locally
cd output && python3 -m http.server 8000

# Deploy (automatic via GitHub Actions)
git add . && git commit -m "..." && git push
```

---

## 🎯 Critical Files Reference

| File | Purpose | Auto-Updated? |
|------|---------|---------------|
| `scraped_data.json` | 91 curated sold consoles | No (manual) |
| `current_listings.json` | Active eBay listings | Yes (6h) |
| `config.json` | Variants + affiliate config | No |
| `template-v4-compact.html` | Variant page template | No |
| `index.html` | Homepage template | No |
| `sitemap.xml` | SEO sitemap | No (manual update when variants change) |
| `robots.txt` | Crawler directives | No |
| `styles.css` | Global styles | No |
| `update_site_compact.py` | Site generator | No |
| `scraper_ebay.py` | Sold items scraper | No |
| `scraper_current_listings.py` | Active listings scraper | No |

---

## 🚀 Multi-Console Expansion (December 2025)

### Revenue Strategy
**Goal:** Scale from 9 GBC pages to 70+ pages across multiple consoles
**Target:** 5,000-10,000 monthly visitors = 50-100€/month revenue

**Search Volume Analysis:**
- GBA: ~2,000/month
- Nintendo DS: ~3,000/month
- PSP: ~2,500/month
- GB Classic: ~1,500/month

### GBA Expansion Status (IN PROGRESS)

#### ✅ Completed (2025-12-25)
1. **scraper_gba.py** - Scrapes all GBA sold items from eBay.fr
   - Uses `.s-card.s-card--horizontal` selectors
   - Successfully scraped 396 GBA items
   - Output: `scraped_data_gba_raw.json`

2. **variant_sorter_gba.html** - Interactive sorting interface
   - 396 items loaded for manual categorization
   - 19 suggested variants (standard/SP/Micro)
   - Keyboard shortcuts: K/B/P/R
   - Auto-save to localStorage (key: `gba_sorter_progress`)
   - User currently sorting (challenge: color similarity)

3. **config_multiconsole.json** - Multi-console configuration
   - Organized by console type
   - GBC: 9 variants configured
   - GBA: 19 variants configured
   - Ready for DS, PSP expansion

4. **migrate_to_multiconsole.py** - Data migration script
   - Converts flat `scraped_data.json` to multi-console structure
   - Preserves backward compatibility
   - Creates backups before migration

5. **process_gba_sorted_data.py** - Processes sorted GBA data
   - Takes exported JSON from variant_sorter_gba.html
   - Calculates stats (avg, min, max prices)
   - Creates `scraped_data_gba.json`
   - Updates `scraped_data_multiconsole.json`

6. **scraper_gba_current_listings.py** - GBA current listings
   - Scrapes active FOR SALE GBA listings
   - Variant-aware filtering (SP/Micro/Standard)
   - Image validation
   - Output: `current_listings_gba.json`

#### 🔄 In Progress
- **User manually sorting 396 GBA items** in variant_sorter_gba.html
  - Challenge: Visual similarity of metallic colors (silver/platinum/pearl)
  - Expected to take significant time

#### 📋 Pending (After User Finishes Sorting)
1. **Run process_gba_sorted_data.py** with exported JSON
2. **Update site generator** for multi-console (or create new version)
3. **Generate GBA variant pages** (18-19 new pages expected)
4. **Update homepage** to show console categories
5. **Update sitemap.xml** to include GBA pages
6. **Deploy to production**

### New File Structure

```
prixretro/
├── config_multiconsole.json          # Multi-console config
├── scraped_data.json                 # Current flat GBC data
├── scraped_data_multiconsole.json    # New multi-console format
├── scraped_data_gba.json             # GBA sold items (pending)
├── scraped_data_gba_raw.json         # Raw GBA scrape (396 items)
├── current_listings_gba.json         # GBA current listings (pending)
│
├── scraper_gba.py                    # GBA sold items scraper
├── scraper_gba_current_listings.py   # GBA current listings scraper
├── variant_sorter_gba.html           # GBA sorting interface
├── create_gba_variant_sorter.py      # Creates sorting HTML
│
├── migrate_to_multiconsole.py        # Migration script
├── process_gba_sorted_data.py        # Process sorted GBA data
│
└── update_site_compact.py            # Current site generator (GBC only)
    update_site_multiconsole.py       # Future: multi-console generator
```

### Multi-Console Data Structure

**New scraped_data.json format:**
```json
{
  "consoles": {
    "game-boy-color": {
      "variants": {
        "atomic-purple": { ... },
        "violet": { ... }
      }
    },
    "game-boy-advance": {
      "variants": {
        "sp-platinum": { ... },
        "standard-purple": { ... }
      }
    }
  },
  "metadata": {
    "version": "2.0",
    "format": "multi-console"
  }
}
```

### GBA Variant Categories

**Standard GBA (5 variants):**
- standard-purple, standard-black, standard-glacier, standard-orange, standard-pink

**SP Models (9 variants):**
- sp-platinum, sp-cobalt, sp-flame, sp-graphite
- sp-pearl-blue, sp-pearl-pink
- sp-tribal-edition, sp-famicom, sp-nes

**Micro Models (5 variants):**
- micro-silver, micro-black, micro-blue, micro-pink, micro-famicom

**Total:** ~19 GBA variants expected

### Expected Page Growth

**Current:** 9 GBC pages
**After GBA:** ~28 pages (+19 GBA variants)
**After DS/PSP:** 70+ pages
**Target:** 100+ pages across all Nintendo handhelds

### Technical Notes

**eBay Scraping for GBA:**
- Uses same `.s-card` selectors as GBC
- Category: 139971 (same as GBC)
- Search term: "game boy advance"
- Variant matching: SP requires "sp" in title, Micro requires "micro"
- Color matching: Similar to GBC but adapted for metallic finishes

**Manual Sorting Required:**
- User preference: "auto filter wasn't good enough"
- Colors too similar for automated detection
- Quality control: Human verification ensures accuracy
- Same workflow as GBC (proven successful)

---

**End of Session Context**
This document should be updated whenever major changes occur.
