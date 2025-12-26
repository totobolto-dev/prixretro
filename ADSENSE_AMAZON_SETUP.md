# 💰 AdSense + Amazon Setup Guide

**Goal:** Add multiple revenue streams immediately
**Effort:** 2-3 hours total
**Impact:** +300-800€/month when traffic scales

---

## 📋 Step 1: Google AdSense (30 minutes)

### Sign Up
1. Go to: https://www.google.com/adsense
2. Click "Get Started"
3. Enter: prixretro.com
4. Fill in your details (address, tax info)
5. Wait 1-2 weeks for approval

### Add Code to Template

**Location 1: Header (Auto Ads - Recommended)**
```html
<!-- Google AdSense Auto Ads -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-YOUR_PUBLISHER_ID"
     crossorigin="anonymous"></script>
```

**Location 2: Sidebar (Manual Ad Units)**
```html
<!-- AdSense Sidebar Ad -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-YOUR_PUBLISHER_ID"
     data-ad-slot="1234567890"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
```

**Location 3: In-Content (Between Sections)**
```html
<!-- AdSense In-Content Ad -->
<ins class="adsbygoogle"
     style="display:block; text-align:center;"
     data-ad-layout="in-article"
     data-ad-format="fluid"
     data-ad-client="ca-pub-YOUR_PUBLISHER_ID"
     data-ad-slot="0987654321"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
```

### Expected Revenue
- **3,000 visitors/month:** 50-100€/month
- **10,000 visitors/month:** 200-400€/month
- **50,000 visitors/month:** 800-1,500€/month

**RPM (Revenue per 1000 views):** €15-30 for French traffic

---

## 📦 Step 2: Amazon Partenaires (45 minutes)

### Sign Up
1. Go to: https://partenaires.amazon.fr
2. Click "Rejoindre" (Join)
3. Create account
4. Add website: prixretro.com
5. Wait for approval (usually 24-48h)

### Commission Rates (France)
- Video games: 4%
- Consoles (used): 4%
- Accessories: 4%
- Electronics: 2%

### Implementation Strategy

**Create Buying Guides (High Conversion)**

Example pages to create:
1. `meilleurs-jeux-gbc.html` - Best Game Boy Color games to buy
2. `accessoires-gba.html` - Best GBA accessories (chargers, cases)
3. `guide-achat-ds.html` - Nintendo DS buying guide

**Template for Buying Guide:**
```html
<h2>Top 10 Jeux Game Boy Color à Acheter en 2025</h2>

<div class="product-card">
    <img src="pokemon-gold.jpg" alt="Pokemon Gold">
    <h3>Pokémon Or (Gold)</h3>
    <p>Le RPG culte de Nintendo, compatible GBC.</p>
    <div class="price">~40-60€</div>
    <a href="https://www.amazon.fr/dp/PRODUCT_ID?tag=YOUR_AFFILIATE_TAG"
       class="buy-button"
       target="_blank"
       rel="nofollow noopener"
       onclick="trackAmazonClick('pokemon-gold')">
        Voir sur Amazon →
    </a>
</div>
```

**Add to Variant Pages:**
```html
<!-- After "Current Listings" section -->
<div class="recommendations">
    <h3>🎮 Jeux Recommandés pour {VARIANT_NAME}</h3>
    <p>Complétez votre collection avec ces jeux compatibles :</p>

    <div class="amazon-products">
        <!-- Product 1 -->
        <a href="https://amazon.fr/dp/XXX?tag=YOUR_TAG" class="amazon-card">
            <img src="game1.jpg" alt="Zelda DX">
            <div class="amazon-title">The Legend of Zelda: Link's Awakening DX</div>
            <div class="amazon-price">~35€</div>
        </a>

        <!-- Product 2 -->
        <a href="https://amazon.fr/dp/YYY?tag=YOUR_TAG" class="amazon-card">
            <img src="game2.jpg" alt="Pokemon Crystal">
            <div class="amazon-title">Pokémon Cristal</div>
            <div class="amazon-price">~50€</div>
        </a>
    </div>
</div>
```

### Expected Revenue
- **3,000 visitors/month:** 30-80€/month (1-2% conversion, 40€ avg sale, 4% commission)
- **10,000 visitors/month:** 100-250€/month
- **50,000 visitors/month:** 500-1,200€/month

**Conversion Rate:** 1-3% on buying guides, 0.5-1% on variant pages

---

## 🎨 Template Updates Needed

### File: template-v4-compact.html

**Add to <head> section:**
```html
<!-- REVENUE STREAMS -->

<!-- Google AdSense Auto Ads -->
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-{ADSENSE_ID}"
     crossorigin="anonymous"></script>

<!-- Track Amazon clicks -->
<script>
function trackAmazonClick(product) {
    gtag('event', 'click', {
        'event_category': 'affiliate',
        'event_label': 'amazon_' + product,
        'value': 1
    });
}
</script>
```

**Add sidebar ad (after price stats):**
```html
<!-- Sidebar AdSense -->
<div class="ad-sidebar">
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="ca-pub-{ADSENSE_ID}"
         data-ad-slot="{SIDEBAR_SLOT}"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
    <script>
         (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>
```

**Add in-content ad (before current listings):**
```html
<!-- In-Content AdSense -->
<div class="ad-content">
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-format="fluid"
         data-ad-layout-key="-fb+5w+4e-db+86"
         data-ad-client="ca-pub-{ADSENSE_ID}"
         data-ad-slot="{CONTENT_SLOT}"></ins>
    <script>
         (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>
```

**Add Amazon section (after current listings):**
```html
{AMAZON_RECOMMENDATIONS}
```

---

## 📊 Revenue Projection (All Streams)

### Scenario: 10,000 monthly visitors

| Revenue Stream | Monthly | How |
|----------------|---------|-----|
| eBay Affiliate | 150€ | 100 clicks × 1.5€ commission |
| Amazon Affiliate | 150€ | 30 sales × 50€ × 10% conversion |
| AdSense | 300€ | 10k views × €30 RPM |
| **TOTAL** | **600€** | Passive, no extra work |

### Scenario: 50,000 monthly visitors

| Revenue Stream | Monthly | How |
|----------------|---------|-----|
| eBay Affiliate | 500€ | 500 clicks × 1€ commission |
| Amazon Affiliate | 800€ | 150 sales × 60€ × 9% conversion |
| AdSense | 1,200€ | 50k views × €24 RPM |
| **TOTAL** | **2,500€** | Just from ads + affiliates! |

**Add B2B (5 customers × 400€):** +2,000€ = **4,500€/month total** 🔥

---

## ✅ Quick Implementation Checklist

### This Week
- [ ] Sign up for Google AdSense
- [ ] Sign up for Amazon Partenaires
- [ ] Add placeholder code to template (`{ADSENSE_ID}` variables)
- [ ] Test layout with ads (use test mode)

### Next Week (After Approval)
- [ ] Replace placeholders with real IDs
- [ ] Create 3 buying guide pages
- [ ] Add Amazon product recommendations to variant pages
- [ ] Monitor revenue in dashboards

### Month 2
- [ ] Optimize ad placements (A/B test)
- [ ] Create 10 more buying guides (SEO content)
- [ ] Add seasonal content ("Cadeaux Noël retrogaming")

---

## 🎓 Best Practices

### AdSense
- **Don't overdo it:** Max 3-4 ads per page
- **Auto ads are best:** Let Google optimize placement
- **Mobile matters:** 60% of traffic is mobile
- **Don't click your own ads:** Instant ban

### Amazon
- **Use proper images:** Better click-through
- **Update links:** Products go out of stock
- **Disclose affiliation:** Required by law in France
- **Target buying intent:** Guides > variant pages

### Compliance
```html
<!-- Add to footer -->
<p class="affiliate-disclosure">
    PrixRetro participe au Programme Partenaires d'Amazon EU et
    au Programme eBay Partner Network. Nous percevons des commissions
    sur les achats qualifiés.
</p>
```

---

## 💡 Pro Tips

1. **Buying Guides Rank Better**
   - "Meilleur Game Boy Color 2025"
   - "Quel Game Boy Advance acheter"
   - These get MORE traffic than variant pages

2. **Seasonal Content**
   - "Idées cadeaux retrogaming Noël 2025"
   - "Black Friday consoles rétro"
   - These convert at 3-5%

3. **Comparison Pages**
   - "GBA vs GBA SP : Lequel choisir?"
   - "DS Lite vs DSi : Comparatif 2025"
   - Amazon loves comparison shopping

4. **Bundle Recommendations**
   - Console + 5 games + charger
   - Higher cart value = higher commission

---

## 🚀 Next: Enhanced Template

I'll create an updated template with:
- AdSense placeholders
- Amazon recommendation sections
- Multi-marketplace price comparison
- B2B API teaser

**File:** `template-v5-multi-revenue.html`

---

**Created:** 2025-12-25
**Priority:** HIGH - Easy money
**Timeline:** 3 hours setup, revenue starts in 2-4 weeks
**Impact:** +300-800€/month minimum
