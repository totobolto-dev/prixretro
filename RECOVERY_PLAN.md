# PrixRetro Recovery & Priority Plan
**Created: 2026-01-25**

## Critical Context (DO NOT LOSE THIS)

### Personal Situation
- **Who**: You + partner, both in Finland (from France)
- **Health**: Both ASD/ADHD. You: cerebral palsy. Partner: endometriosis + chronic conditions
- **Employment**: Both currently jobless
- **Stakes**: This project is your lifeline for passive income
- **Goal**: 1000€/month (keep reducing as needed - started higher)
- **Timeline**: Need revenue in coming year

### Project Status
- **Current Revenue**: ~0€/month (affiliates in place but low traffic)
- **Tech**: Laravel 12, Filament 4, MySQL (OVH CloudDB), shared hosting
- **Data**: 656 listings, 202 variants, 69 consoles
- **Affiliates**: Amazon (tag: prixretro-21), eBay Partner Network

## Revenue Equation (Priority Order)

```
Traffic × Click-through × Conversion = Revenue
```

**Current Bottleneck**: LOW TRAFFIC
**Priority #1**: SEO optimization (not design polish)

## Immediate Action Items (Next 48 Hours)

### 1. Stabilize Dev/Prod Sync ⚠️ CRITICAL
**Problem**: Tailwind rebuild happened, potential mismatch with production
**Action**:
```bash
# Test current production state
ssh to production
php artisan config:clear
php artisan view:clear

# If needed, sync just the critical files:
- resources/css/app.css (new theme)
- resources/views/components/navbar.blade.php (logo)
- resources/views/components/layout.blade.php (removed settings-menu)
- resources/views/home.blade.php (new hero, relative times)
```

### 2. SEO Priorities (Revenue Drivers)
- [ ] Submit sitemap to Google Search Console (if not done)
- [ ] Check Google Analytics is tracking (GA_MEASUREMENT_ID in .env)
- [ ] Add more buying guide content (you have 21 guides - are they indexed?)
- [ ] Internal linking: console → variants → guides
- [ ] Meta descriptions for all variant pages
- [ ] Schema.org structured data (Product markup with prices)

### 3. Content That Drives Traffic
- [ ] Blog posts: "Prix Game Boy Color 2026", "Où acheter [console] occasion"
- [ ] Price trend articles (auto-generate from your data)
- [ ] Comparison pages: "Game Boy vs Game Boy Color prix"
- [ ] Location pages: "Consoles rétro occasion France"

### 4. Revenue Optimization (After traffic grows)
- [ ] A/B test Amazon affiliate placements
- [ ] Add Micromania/Fnac French affiliates
- [ ] Email signup for price alerts (capture emails)
- [ ] Display ads (PropellerAds/Adsterra) once traffic > 1000 visits/day

## What NOT To Do Right Now

- ❌ More design tweaks (current design is good enough)
- ❌ Collection tracker (no auth system yet)
- ❌ Console menu in nav (complex, not revenue-critical)
- ❌ Search implementation (nice-to-have, not critical)
- ❌ SVG icon sprites (aesthetic only)
- ❌ Image optimization project (do gradually)

## Technical Debt to Track

### Dev/Prod Differences
- `local.env` issue resolved (was overriding .env)
- Migrations run on dev (sessions table created)
- `condition` → `item_condition` column rename handled
- Tailwind config rebuilt in dev

### Commands You Need
```bash
# Import from production DB
./vendor/bin/sail artisan sync:from-production

# Deploy to production (auto via GitHub Actions)
git push origin main

# Manual sitemap regeneration
php artisan sitemap:generate

# Clear production caches (SSH to OVH)
php artisan config:clear && php artisan view:clear
```

## Token Budget Strategy

You have **20% weekly tokens left** (~131k remaining).

**Use tokens ONLY for**:
1. Critical SEO fixes (meta tags, schema, sitemap issues)
2. Content generation (guides, blog posts)
3. Revenue-impacting bugs
4. Analytics/tracking verification

**DO NOT use tokens for**:
- Design polish
- Refactoring
- "Nice-to-have" features
- Exploratory coding

## Weekly Revenue Checklist

Every Monday:
- [ ] Check Google Analytics: traffic trends
- [ ] Check Amazon affiliate dashboard: clicks, conversions
- [ ] Check eBay Partner Network: earnings
- [ ] Add 1-2 new buying guides or price trend articles
- [ ] Submit new content to Google Search Console for indexing
- [ ] Review top 10 traffic pages - optimize further

## Emergency Contacts

**Critical Files**:
- `/home/ganzu/Documents/web-apps/prixretro/CLAUDE.md` - Technical docs
- `/home/ganzu/Documents/web-apps/prixretro/RECOVERY_PLAN.md` - THIS FILE

**If Sessions Crash Again**:
1. Save this file externally (Google Docs, Notion, etc.)
2. Document what you were working on
3. New session: Start with "Read RECOVERY_PLAN.md and continue [task]"

**Admin Access**:
- URL: https://www.prixretro.com/admin
- Command to sync data: `php artisan sync:from-production`

## Next Session Priorities

1. **Verify production is stable** (test live site)
2. **Google Search Console** - submit sitemap, check indexing
3. **Content creation** - 2-3 high-traffic keyword articles
4. **Analytics verification** - is GA4 tracking correctly?

---

## Design Changes Just Completed (2026-01-25)

✅ Gold accent color from logo (#e8b44f)
✅ Logo in sticky nav (not text)
✅ Hero with description (no logo duplication)
✅ Better popular console stats (no repeated "30j")
✅ Relative timestamps ("il y a 3 heures")
✅ Removed settings menu/fabricant dropdown
✅ Removed "Deals" tab from nav
✅ Dark blue-purple theme matching logo
✅ Better contrast on links

**Status**: Dev environment tested and working. Ready to deploy to production.

---

## MCP Memory Lost - Critical Info to Re-Add

If you set up MCP memory again, add:
- Personal situation (jobless, disabled, Finland, survival project)
- Revenue goal: 1000€/month passive income
- Priority: Traffic > Design
- Affiliates: Amazon prixretro-21, eBay Partner Network
- Tech constraints: OVH shared hosting, no Redis, file-based cache
- Partner context: endometriosis, chronic conditions, also ASD/ADHD

**How to prevent future loss**:
1. Keep critical context in CLAUDE.md (checked into git)
2. Back up MCP memory externally if possible
3. Use this RECOVERY_PLAN.md as source of truth
4. Git commit often with descriptive messages

---

Remember: **Revenue > Perfection**. Ship features that drive traffic and conversions. Everything else is secondary.
