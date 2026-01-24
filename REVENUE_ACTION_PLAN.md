# 🎯 Revenue Action Plan - Target: 1,000€/mois

**Situation:** Urgent revenue needed. Partner lost job.
**Current:** eBay + Amazon affiliates only, AdSense pending
**Goal:** 1,000€/month through diversified revenue streams

---

## ✅ COMPLETED (Just Deployed)

### Conversion Rate Optimization
- ✅ Fixed duplicate "prix par état" section
- ✅ Disabled collection tracker (no auth system)
- ✅ Added urgency banner showing top 3 eBay listings at page top
- ✅ Added scarcity alert when prices rise >10% in 30 days
- ✅ Removed hidden duplicate listings section

**Expected impact:** +15-25% click-through rate on affiliate links

---

## 🚨 IMMEDIATE ACTIONS (This Week - €50-150/month)

### 1. Add Display Ads (NO TRAFFIC MINIMUM)
**Why:** AdSense is pending, but these accept you immediately

**PropellerAds** ([Zero traffic requirement](https://cropink.com/best-ad-networks-for-small-publishers))
- Sign up: https://propellerads.com
- Add 2 units: Top banner (728x90) + Sidebar (300x250)
- Expected CPM: €1-3 → €50-100/month at current traffic

**Adsterra** (Alternative, same terms)
- Sign up: https://adsterra.com
- Similar revenue potential

**Action:** Pick ONE, sign up today, implement tomorrow

---

### 2. Add French Retro Gaming Affiliates (€100-200/month)

**Micromania** ([5-10% commission via Kwanko](https://www.kwanko.com/program-directory/program/affiliate/affiliation/))
- France's #1 video game retailer
- Just launched "Retromania" for retro buybacks
- Sign up: https://www.kwanko.com
- Add button: "🛒 Neuf chez Micromania" next to eBay

**Fnac** ([2-10% commission via Awin](https://ui.awin.com/merchant-profile/12665))
- €100 average basket, 2% conversion rate
- Sign up: https://ui.awin.com
- Add search link for each console

**Implementation location:** Modify `resources/views/variant/show.blade.php` → Add third column in monetization section

**Expected:** 50 clicks/day × 2% conversion × €80 basket × 7% commission = €5.60/day = €168/month

---

### 3. Email Collection (Future Revenue Channel)

**Setup Mailchimp Free** (up to 500 contacts)
- Sign up: https://mailchimp.com
- Create embedded form: "🔔 Alerte prix - Soyez notifié des baisses"
- Place above price chart on variant pages
- GDPR: Add checkbox "J'accepte de recevoir les alertes prix (désinscription à tout moment)"

**Weekly email content:**
- Top 3 price drops this week (with affiliate links)
- "Console de la semaine" spotlight
- Link to new guides

**Revenue potential:** [Newsletter median time to first dollar: 66 days](https://www.beehiiv.com/blog/the-state-of-newsletters-2026)
**Expected:** Build 100-200 emails/month → Monetize in 2-3 months

---

## 📊 SHORT-TERM (Next 2 Weeks - €200-400/month)

### 4. Reddit Traffic Strategy

**Target subreddits:**
- r/retrogaming (millions of views)
- r/Gameboy, r/n64, r/psx, r/3DS, r/PSP

**Strategy:** [Follow 10% self-promotion rule](https://www.cloutboost.com/blog/how-to-market-a-video-game-on-reddit-the-complete-2025-guide-for-game-developers)
- 9 helpful comments for every 1 promotional post
- Post format: "I analyzed 500 GBC sales - here's what shocked me"
- Include 1-2 charts from your data
- Link to specific variant page in post or comment

**Posting schedule:**
- Monday: r/retrogaming (helpful comment on someone's post)
- Wednesday: r/Gameboy (analysis post with data)
- Friday: r/retrogaming (comment + share your guide)
- Repeat with different consoles weekly

**Expected:** 500-1,000 new visitors/month → +€50-100 affiliate revenue

---

### 5. TikTok Price Shock Content

**Platform potential:** [TikTok Gaming pays €5-7 per 1,000 views](https://metricool.com/monetize-tiktok/) (France eligible)

**Content ideas:**
1. "Cette Game Boy vaut COMBIEN en 2026? 😱" → Price reveal format
2. "Top 5 consoles qui ont EXPLOSÉ en prix" → Countdown with visuals
3. "J'ai analysé 1000 ventes - voici les consoles à acheter MAINTENANT"
4. Before/After: "Prix en 2020 vs 2026" shock value

**Setup:**
- Film with phone (no equipment needed)
- Use trending audio
- Hashtags: #retrogaming #gameboy #nintendo #prixretro
- Bio link → PrixRetro homepage

**Posting schedule:** 3 videos/week (15 min each to create)

**Expected:** 1-2 videos go viral (50K+ views) → Traffic spike + €50-150 Creator Fund

---

### 6. CDKeys Digital Affiliate

**Why:** [5% commission on digital games](https://backlinko.com/gaming-affiliate-programs)

**Add section to console pages:**
```
"🎮 Jeux digitaux pour {{ console_name }}"
Link to CDKeys search for that console
Example: PSP page → PSP games on CDKeys
```

**Best consoles:** PSP, PS Vita, 3DS, DS (still active digital markets)

**Implementation:** Add below Amazon/eBay section

**Expected:** €30-50/month from digital game purchases

---

## 💰 MEDIUM-TERM (Next Month - €300-500/month)

### 7. Direct Seller Partnerships

**Opportunity:** [Price comparison sites earn via CPC + revenue share](https://sozodesign.co.uk/learn/how-to-monetise-a-price-comparison-website/)

**Target French retro shops:**
- Console Occasion
- Retro Game Store
- GameCash
- Easy Cash

**Pitch email template:**
```
Bonjour,

Je gère PrixRetro.com, un site de prix de référence pour le rétrogaming
avec 5,000+ visiteurs/mois ciblés (collectionneurs français).

Je propose une visibilité "Vendeur vérifié" sur toutes les pages de
consoles que vous vendez, soit:
- Option A: 50€/mois forfait
- Option B: 5% commission sur ventes trackées via notre lien

Intéressé pour discuter?

Cordialement,
[Ton nom]
```

**Expected:** 2-3 partnerships × €50-100/month = €150-300/month

---

### 8. Premium Listing Model (Future)

**Concept:** Sellers pay to feature their listings

**Implementation:**
- Add "Annonces Premium" section above regular listings
- Charge €5-10 per listing for 30-day featured placement
- Accept via Stripe/PayPal

**Target:** Private sellers with rare variants (Sealed GBC, graded consoles)

**Expected:** 10 premium listings/month × €7.50 = €75/month

---

## 📈 TRAFFIC ACCELERATION (Ongoing)

### SEO Quick Wins
- ✅ 21 comprehensive guides (DONE)
- ✅ Schema.org markup (DONE)
- ⏳ Get backlinks: Email French retro bloggers to review site
- ⏳ Guest post on JeuxVideo.com forums

### Social Media
- Create Twitter account → Share daily price insights
- Facebook groups: "Retrogaming France", "Game Boy Collectors FR"
- Instagram: Console photos with price overlays

### Paid Advertising (When budget allows)
- Google Ads: Target "prix game boy color" (low CPC ~€0.20)
- Facebook Ads: Retarget site visitors

---

## 💡 REVENUE PROJECTIONS

| Month | Source | Expected Revenue |
|-------|--------|------------------|
| **Month 1** | PropellerAds | €75 |
| | Micromania + Fnac | €120 |
| | eBay (optimized CTR) | €50 |
| | Amazon | €40 |
| | **TOTAL** | **€285** |
| **Month 2** | Display ads | €100 |
| | FR affiliates | €180 |
| | eBay + Amazon | €120 |
| | Reddit traffic boost | €50 |
| | CDKeys | €40 |
| | **TOTAL** | **€490** |
| **Month 3** | Display ads | €150 |
| | FR affiliates | €250 |
| | eBay + Amazon | €180 |
| | TikTok Creator Fund | €100 |
| | Direct partnerships | €150 |
| | CDKeys | €60 |
| | Newsletter (starts) | €30 |
| | **TOTAL** | **€920** |
| **Month 4** | All streams optimized | **€1,100+** |

---

## 🎯 THIS WEEK CHECKLIST

- [ ] Sign up PropellerAds or Adsterra (30 min)
- [ ] Add display ads to layout.blade.php (1 hour)
- [ ] Sign up Micromania via Kwanko (20 min)
- [ ] Sign up Fnac via Awin (20 min)
- [ ] Add Micromania/Fnac buttons to variant page (2 hours)
- [ ] Set up Mailchimp free account (30 min)
- [ ] Create email signup form (1 hour)
- [ ] Add email form to variant pages (30 min)
- [ ] Post first Reddit analysis on r/retrogaming (1 hour)
- [ ] Create TikTok account "PrixRetro" (15 min)
- [ ] Film first 3 TikTok videos (1 hour)

**Total time:** ~10 hours
**Expected immediate impact:** +€100-150/month by next week

---

## 📞 SOURCES

All recommendations based on research:
- [PropellerAds no minimum traffic](https://cropink.com/best-ad-networks-for-small-publishers)
- [Micromania 5-10% commission](https://www.kwanko.com/program-directory/program/affiliate/affiliation/)
- [Fnac 2-10% via Awin](https://ui.awin.com/merchant-profile/12665)
- [Newsletter monetization: 66-day median](https://www.beehiiv.com/blog/the-state-of-newsletters-2026)
- [Reddit 10% self-promotion rule](https://www.cloutboost.com/blog/how-to-market-a-video-game-on-reddit-the-complete-2025-guide-for-game-developers)
- [TikTok Gaming €5-7 per 1K views](https://metricool.com/monetize-tiktok/)
- [CDKeys 5% commission](https://backlinko.com/gaming-affiliate-programs)
- [Price comparison revenue models](https://sozodesign.co.uk/learn/how-to-monetise-a-price-comparison-website/)

---

**Remember:** Revenue = Traffic × CTR × Conversion × Commission
Focus on ALL 4 metrics, not just traffic.

Good luck! Tu vas y arriver. 💪
