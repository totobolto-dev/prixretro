# PrixRetro - Game Boy Color Price Tracker

## Project Structure

```
/
├── 📁 data/                     # Raw scraped data
│   └── scraped_data.json        # Original eBay scraping results
│
├── 📁 docs/                     # Documentation & guides  
│   ├── ANALYTICS_SETUP.md       # Google Analytics setup guide
│   ├── CONTENT_STRATEGY.md      # SEO content strategy
│   ├── LARAVEL_MIGRATION_STRATEGY.md  # Future Laravel plans
│   └── MANUAL_REVIEW.md         # Item classification guide
│
├── 📁 scripts/                  # Utility scripts
│   ├── categorize_all_data.py   # Categorize items for review
│   ├── deduplicate_and_compact.py  # Remove duplicates
│   └── generate_sitemap.py      # SEO sitemap generation
│
├── 📁 templates/                # HTML templates
│   └── template-v3.html         # Previous template (archived)
│
├── 📁 output/                   # Generated website files
│   ├── index.html               # Homepage
│   └── game-boy-color-*.html    # Variant pages
│
├── 📁 archived/                 # Historical files
│   ├── old_filters/             # Previous filtering attempts
│   ├── old_data/                # Intermediate data processing
│   ├── old_templates/           # Previous site generators  
│   └── reports/                 # Processing reports
│
├── 🔧 config.json               # eBay scraper configuration
├── 🐍 scraper_ebay.py           # Main eBay scraping script
├── 🐍 update_site_compact.py    # Site generator (current)
├── 📊 scraped_data_deduplicated.json  # Clean data (current)
├── 🎮 gameboy_color_specs.json  # Console specifications
├── 🏠 index.html                # Homepage template
├── 🎨 template-v4-compact.html  # Current compact template
└── 📄 README.md                 # This file
```

## Quick Start

### 1. Generate Website
```bash
python3 update_site_compact.py
```

### 2. Update Data (re-scrape eBay)
```bash
python3 scraper_ebay.py
python3 scripts/deduplicate_and_compact.py
python3 update_site_compact.py
```

### 3. Manual Data Review
1. Review `docs/MANUAL_REVIEW.md`
2. Classify items as consoles/games/parts
3. Update data classifications

## Current Status

✅ **Functional Website**: Compact display with real analytics  
✅ **Clean Data**: 879 deduplicated items across 8 variants  
✅ **SEO Foundation**: Sitemap, structured data, meta tags  
✅ **Analytics**: Google Analytics (G-4QPNVF0BRW) tracking  
✅ **Future Roadmap**: Laravel migration strategy documented  

## Data Quality

- **Original scraped**: 1,352 items
- **After deduplication**: 879 unique items  
- **Manual review needed**: Classify authentic consoles vs games/parts
- **Current live data**: Uses deduplicated dataset

## Key Files

### Active Development
- `scraper_ebay.py` - eBay scraping engine
- `update_site_compact.py` - Website generator  
- `template-v4-compact.html` - Current template (compact display)
- `scraped_data_deduplicated.json` - Clean deduplicated data
- `gameboy_color_specs.json` - Console technical specifications

### Configuration
- `config.json` - eBay scraper settings
- `index.html` - Homepage template
- `sitemap.xml` - SEO sitemap
- `robots.txt` - Search engine directives

### Utilities
- `scripts/categorize_all_data.py` - Smart categorization for manual review
- `scripts/deduplicate_and_compact.py` - Remove duplicates & create compact display
- `scripts/generate_sitemap.py` - Generate SEO sitemap

## Deployment

Site auto-deploys via GitHub Actions to OVH hosting when pushed to main branch.

**Live site**: https://prixretro.com

## Game Boy Color Variants

Currently tracking these variants:
- **Standard Colors**: Violet, Rouge, Bleu (Teal), Vert Néon, Jaune
- **Special Editions**: Atomic Purple, Pikachu Edition, Pokémon Gold/Silver

## Next Development Steps

1. **Manual data curation** - Classify authentic consoles vs games/parts
2. **Console specs integration** - Add technical details to variant pages  
3. **Content creation** - Implement SEO content strategy
4. **Laravel migration** - Transition to dynamic platform with user features