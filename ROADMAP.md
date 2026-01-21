# PrixRetro - Product Roadmap

**Goal**: Optimize for revenue growth through improved SEO, user retention, and conversion

**Current Bottleneck**: Low traffic → SEO is Priority #1

---

## Competitive Analysis

**Major Competitors**:
- PriceCharting (industry leader, 24k+ games, collection tracker)
- GameValueNow (24k games, 35+ consoles)
- RetroCharting (50+ systems, daily updates)
- RetroGamePrices (daily eBay data aggregation)

**Key Missing Features**:
1. Collection tracker (user accounts, saved consoles)
2. Price alert system (email notifications)
3. Condition-based pricing (Loose/CIB/Sealed)
4. eBay API integration (vs manual scraping)
5. Content strategy (buying guides, market analysis)

---

## Phase 1: Quick Wins (Week 1)

**Estimated Impact**: +30-50% organic traffic within 30 days

### 1. Mobile Optimization Audit ✓
- Verify responsive design on all pages
- Test performance on mobile devices
- Optimize images for faster loading

### 2. Buying Guides (Content Marketing)
**Target**: 3-5 articles for top consoles
- "Guide d'achat Game Boy Color - Comment choisir sa variante"
- "PS Vita d'occasion - Pièges à éviter et meilleures affaires"
- "Game Boy Advance - Quelle édition pour débuter la collection?"
- "Comment repérer une console retrogaming contrefaite"
- "Meilleures consoles retro à acheter en 2026 (budget €50-200)"

**SEO Benefits**:
- Long-tail keywords ("guide achat game boy color occasion")
- E-E-A-T signals (expertise, authority)
- Internal linking opportunities
- Featured snippet potential

### 3. Amazon + eBay Side-by-Side Display
**Current**: Amazon accessories on variant pages only
**New**: Show both options prominently:
- Left column: eBay listings (consoles)
- Right column: Amazon accessories (cases, cables, etc.)
- Increases monetization surface area
- User chooses, we earn either way

### 4. Enhanced "Price History" Section
- Move chart higher on page (above fold for top variants)
- Add price trend indicator (↑ +15% last 30 days)
- Add "Best time to buy" insight based on historical data
- Prominent CTA: "Get price alerts" (future feature)

---

## Phase 2: User Retention (Month 1)

**Estimated Impact**: +40% user retention, +25% conversion on alerts

### 1. Collection Tracker MVP
**Features**:
- User registration (email/password)
- Add consoles to "My Collection"
- Auto-calculate total collection value
- Track value changes over time
- Share collection publicly (social proof)

**Revenue Impact**: Repeat visits → more affiliate clicks

### 2. Email Price Alert System
**Features**:
- "Notify me when price drops below €X"
- Daily digest of watched consoles
- Weekly market summary (top gainers/losers)
- Affiliate links in emails

**Conversion Strategy**: Create urgency, drive clicks

### 3. eBay API Migration
**Replace**: Python scraper → Official eBay API
**Benefits**:
- Real-time data (vs daily scrapes)
- Better affiliate attribution
- No breaking changes
- Access to more metadata (condition, seller rating)

### 4. Condition-Based Pricing
**Add Fields**:
- `condition` enum: loose, complete, sealed
- Separate avg prices per condition
- Filter listings by condition on variant pages

**Why**: Collectors need this granularity

---

## Phase 3: Traffic Growth (Months 2-3)

**Estimated Impact**: 2-3x organic traffic from long-tail SEO

### 1. AI-Optimized Content Strategy
**Create 20+ guides** targeting:
- Buying guides per console family (10 articles)
- Investment guides ("Best ROI consoles 2026")
- Authentication guides ("Spot fake Pokémon cartridges")
- Market analysis ("Why GBA prices surged in 2025")
- Maintenance guides ("Clean yellowed plastic consoles")

**Optimize for**:
- ChatGPT/Gemini citations
- Google featured snippets
- Reddit r/retrogaming discussions

### 2. Social Proof Features
- "X collectors tracking this console"
- "Price increased/decreased for Y% of trackers"
- User reviews/notes on variants
- Community price predictions

### 3. Multi-Platform Presence
**Reddit**:
- Share market insights on r/retrogaming
- Answer collection questions, link to guides
- Build backlinks naturally

**TikTok** (consider):
- Short "price shock" videos (before/after)
- Hidden gem consoles under €50
- Drives younger audience

### 4. Advanced Analytics
**New Pages**:
- Market trends dashboard (top gainers/losers)
- Price prediction ML model (simple linear regression)
- "Investment score" per console
- Console price index (like stock market)

---

## Phase 4: Monetization Expansion (Ongoing)

### 1. Google AdSense
- Header bidding for premium ad slots
- Native ads between listings
- Sticky footer ads on mobile

### 2. Sponsored Listings
- Sellers pay to feature their listings
- "Verified Seller" badge program
- Premium placement in search

### 3. Premium Membership
- Ad-free experience
- Advanced price alerts (specific conditions)
- Export collection to CSV
- Early access to market reports

---

## Technical Debt & Infrastructure

### Priority Fixes
1. Schema.org implementation (fix approach to avoid 500 errors)
2. Caching strategy (Redis when possible, file-based for now)
3. Image optimization (WebP, lazy loading)
4. Database indexing audit
5. N+1 query optimization

### Future Improvements
1. Move from shared hosting to VPS (when revenue justifies)
2. CDN for static assets
3. Full-text search (Algolia or Meilisearch)
4. GraphQL API for mobile app (future)

---

## Success Metrics

**Track Weekly**:
- Organic traffic (Google Analytics)
- Affiliate clicks (eBay + Amazon)
- Conversion rate (clicks → sales)
- Email subscribers (once implemented)
- Collection tracker signups

**Monthly Revenue Goal**:
- Month 1: €100 (current: ~€0-20)
- Month 2: €250
- Month 3: €500
- Month 6: €1000 (TARGET)

**Key Multipliers**:
- Traffic × CTR × Conversion = Revenue
- Focus on ALL three simultaneously

---

## Implementation Notes

**Testing Protocol**:
1. Test locally with Sail
2. Commit to Git
3. Auto-deploy via GitHub Actions
4. Test live URL immediately
5. Monitor for 500 errors
6. Rollback if issues

**Code Quality**:
- Follow Laravel best practices
- No over-engineering (MVP first)
- Filament v4 patterns (check existing code)
- Mobile-first CSS

**SEO Checklist** (every new page):
- Unique meta title (< 60 chars)
- Unique meta description (< 160 chars)
- H1 heading (one per page)
- Internal links (3+ per page)
- Alt text on images
- Schema.org markup (when safe)
- Canonical URL

---

## Archived Ideas (Revisit Later)

- Chrome extension (price tracker overlay on eBay)
- Price comparison with local retailers (Leboncoin, Vinted)
- Affiliate deals with retro game shops
- YouTube channel (market analysis videos)
- Podcast (retro gaming investment)
- Mobile app (iOS/Android)
- API for third-party developers
- Shopify integration (sell own inventory)

---

**Last Updated**: 2026-01-21
**Next Review**: Weekly during Phase 1, Monthly after
