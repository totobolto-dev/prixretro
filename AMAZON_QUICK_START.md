# 🛒 Amazon Partenaires - Quick Start Guide

**Your Amazon Tag:** `prixretro-21` ✅ **ACTIVE!**

**STATUS:** ⚠️ **ON HOLD - FUTURE USE ONLY**

**Strategy Update (2025-12-26):**
- ❌ **DO NOT use for vintage consoles/games** (too many fakes, shady sellers)
- ✅ **FUTURE USE: NEW modern accessories only** (cases, cleaning kits, display stands)
- ✅ **Focus NOW on:** eBay affiliate + AdSense + B2B API (reach €2k without Amazon!)

**When to revisit:** Month 6+ when you have time to curate NEW accessory products carefully.

---

## 🚀 Step 1: Find Products on Amazon.fr (5 minutes)

### How to Find Good Products:

1. **Go to Amazon.fr**
2. **Search for retro gaming products:**
   - "game boy color"
   - "game boy advance sp"
   - "jeux game boy color"
   - "accessoires gameboy"

3. **Look for:**
   - ✅ Products with good reviews (4+ stars)
   - ✅ Reasonable prices (compare to eBay data)
   - ✅ "Amazon's Choice" or "Best Seller" badges
   - ✅ Available stock (not "Temporairement en rupture")

---

## 📝 Step 2: Get the ASIN (Product ID)

### What is an ASIN?
Amazon Standard Identification Number - unique code for each product.

### How to Find It:

**Method 1: Look in URL**
```
https://www.amazon.fr/Console-Game-Boy-Color/dp/B00005QD2R/ref=sr_1_3
                                                  ^^^^^^^^^^
                                                  This is the ASIN!
```

**Method 2: Scroll down on product page**
- Look for "Détails sur le produit"
- Find "ASIN: B00005QD2R"

**Method 3: Use SiteStripe toolbar**
- When logged into Amazon Partenaires
- Toolbar appears at top of Amazon.fr
- Shows ASIN automatically

---

## 🔗 Step 3: Create Affiliate Links

### Manual Method:
```
https://www.amazon.fr/dp/{ASIN}?tag=prixretro-21
```

**Example:**
- Product ASIN: `B00005QD2R`
- Your link: `https://www.amazon.fr/dp/B00005QD2R?tag=prixretro-21`

### Using the Script (Easier!):
```bash
python3 add_amazon_products.py add
```

Follow the prompts:
1. Choose console (GBC, GBA, etc.)
2. Choose category (console, game, accessory)
3. Enter product name
4. Paste ASIN
5. Enter price
6. Add short description

**Done!** The script creates the affiliate link automatically.

---

## 📊 Step 4: Generate HTML for Your Site

```bash
# Generate recommendations for Game Boy Color pages
python3 add_amazon_products.py generate game-boy-color

# Generate for Game Boy Advance
python3 add_amazon_products.py generate game-boy-advance
```

**Output:** Ready-to-use HTML with affiliate links!

Copy the HTML and paste into your template.

---

## 🎯 Top 10 Products to Add First

### Game Boy Color (Priority 1-5):
1. **Console GBC reconditionnée** (any variant)
   - Search: "game boy color reconditionné"
   - Price range: 60-100€

2. **Pokémon Cristal**
   - Search: "pokemon cristal game boy"
   - Price: ~40-60€

3. **Zelda Oracle of Seasons**
   - Search: "zelda oracle of seasons"
   - Price: ~35-50€

4. **Zelda Oracle of Ages**
   - Search: "zelda oracle of ages"
   - Price: ~35-50€

5. **Housse de protection GBC**
   - Search: "housse game boy color"
   - Price: ~10-15€

### Game Boy Advance (Priority 6-10):
6. **GBA SP Platinum**
   - Search: "game boy advance sp platinum"
   - Price: 80-120€

7. **Pokémon Émeraude**
   - Search: "pokemon emeraude gba"
   - Price: ~50-70€

8. **Zelda Minish Cap**
   - Search: "zelda minish cap"
   - Price: ~40-60€

9. **Chargeur GBA SP**
   - Search: "chargeur game boy advance sp"
   - Price: ~8-12€

10. **Housse GBA SP**
    - Search: "housse game boy advance sp"
    - Price: ~12-18€

---

## 🔥 Quick Workflow (15 minutes total)

### For Each Product:

1. **Find on Amazon.fr** (1 min)
   - Search for product
   - Open product page

2. **Get ASIN** (10 seconds)
   - Copy from URL: `amazon.fr/dp/ASIN`

3. **Add to Database** (1 min)
   ```bash
   python3 add_amazon_products.py add
   ```
   - Enter details
   - Script saves it

4. **Repeat for 5-10 products**

5. **Generate HTML** (10 seconds)
   ```bash
   python3 add_amazon_products.py generate game-boy-color
   ```

6. **Copy HTML to template** (1 min)
   - Paste into `template-v4-compact.html`
   - Or use `template-v5-multi-revenue.html` (already has structure!)

---

## 📋 Product Database Structure

**File:** `amazon_products.json`

**Structure:**
```json
{
  "game-boy-color": {
    "consoles": [...],
    "games": [...],
    "accessories": [...]
  },
  "game-boy-advance": {
    "consoles": [...],
    "games": [...],
    "accessories": [...]
  }
}
```

**Already pre-populated** with product templates - just replace ASINs!

---

## 🛠️ Available Commands

```bash
# Add a new product (interactive)
python3 add_amazon_products.py add

# List all products in database
python3 add_amazon_products.py list

# Generate HTML for a console
python3 add_amazon_products.py generate game-boy-color
python3 add_amazon_products.py generate game-boy-advance

# Show help
python3 add_amazon_products.py help
```

---

## 💡 Pro Tips

### Finding the Best Products:
- ✅ Look for "Amazon's Choice" badge
- ✅ Check reviews (4+ stars with 50+ reviews)
- ✅ Compare prices to your eBay data
- ✅ Prefer products with Prime shipping

### Link Strategy:
- **Consoles:** Link to refurbished or used (matches your audience)
- **Games:** Link to original cartridges or compilations
- **Accessories:** Link to universal/compatible items (higher availability)

### Conversion Optimization:
- Add 3-6 products per page (not too many!)
- Mix consoles + games + accessories (different price points)
- Put lowest price items first (impulse buys)

---

## 📊 Expected Revenue

### With 10 Products Added:
- 5,000 monthly visitors
- 5% click rate = 250 clicks
- 3% conversion = 7-8 sales
- Average cart: 60€
- Commission: 4%

**Monthly revenue:** ~€15-20

### With 30 Products + Buying Guides:
- 10,000 monthly visitors
- 7% click rate = 700 clicks (guides have higher CTR)
- 4% conversion = 28 sales
- Average cart: 70€

**Monthly revenue:** ~€80-100

---

## 🎯 Next Steps

**TODAY (while sorting GBA):**
1. ✅ Amazon account approved (`prixretro-21`)
2. 🎯 Find 5 Game Boy Color products
3. 🎯 Add ASINs to `amazon_products.json` (using script)
4. 🎯 Generate HTML snippets

**TOMORROW:**
1. Add HTML to template
2. Regenerate site
3. Deploy
4. Start earning!

---

## 📞 Need Help?

**Dashboard:** https://partenaires.amazon.fr
**Your Tag:** `prixretro-21`

**Check earnings:**
1. Log in to dashboard
2. Click "Rapports" (Reports)
3. See clicks, conversions, revenue

**First payment:**
- Minimum: €25 earned
- Timing: 60 days after month end
- Method: Bank transfer (IBAN)

---

**LET'S START EARNING! 🚀**
