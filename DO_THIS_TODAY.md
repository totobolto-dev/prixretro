# ⚡ DO THIS TODAY (1 Hour = +700€/Month Potential)

**Priority:** HIGHEST 🔥
**Time:** 60 minutes
**Impact:** +700€/month revenue potential
**Difficulty:** Super easy

---

## Step 1: Google AdSense (30 minutes)

### Sign Up
1. Go to: https://www.google.com/adsense
2. Click **"Get Started"**
3. Enter **your website:** prixretro.com
4. Enter **your email**
5. Choose **France** as country
6. Click **Save and continue**

### Fill In Details
7. **Address:** Your business/home address
8. **Phone:** Your phone number
9. **Accept terms:** Check the boxes
10. Click **Submit**

### Add Code to Website (DO THIS EVEN BEFORE APPROVAL)
11. Copy the AdSense code they give you (looks like this):
```html
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-1234567890"
     crossorigin="anonymous"></script>
```

12. **SAVE THIS CODE** - you'll need it!

### Wait for Approval
- **Timeline:** 1-2 weeks
- **What they check:** Real content, real traffic, no policy violations
- **Your advantage:** You have real content (variant pages), real traffic, clean site ✅

### Revenue Potential
- **10k visitors:** 200€/month
- **50k visitors:** 800€/month
- **100k visitors:** 1,500€/month

---

## Step 2: Amazon Partenaires (30 minutes)

### Sign Up
1. Go to: https://partenaires.amazon.fr
2. Click **"Rejoindre gratuitement"** (Join for free)
3. **Sign in** with existing Amazon account OR create new one
4. Enter **website:** prixretro.com

### Fill In Profile
5. **Who are you:**
   - Individual
   - Content creator / Blogger

6. **What's your website about:**
   - Retro gaming
   - Price tracking
   - Buying guides

7. **How do you drive traffic:**
   - SEO / Organic search
   - Social media
   - Content marketing

8. **Monthly visitors:** 1,000-5,000 (current) or 5,000-10,000 (conservative projection)

### Get Your Affiliate Tag
9. After approval, you'll get a **tracking ID** like: `prixretro-21`
10. **SAVE THIS** - you'll use it in all Amazon links!

### Wait for Approval
- **Timeline:** 24-48 hours (much faster than AdSense!)
- **What they check:** Real website, real content
- **Your advantage:** Clean site, clear purpose ✅

### Link Format
```
https://www.amazon.fr/dp/PRODUCT_ID?tag=YOUR_TAG-21
```

Example:
```
https://www.amazon.fr/dp/B00005QD2R?tag=prixretro-21
```

### Revenue Potential
- **10k visitors:** 100€/month (1% conversion, 60€ avg, 4% commission)
- **50k visitors:** 500€/month
- **With good buying guides:** 800€/month

---

## Step 3: Update Templates (AFTER Approval)

### When AdSense Approved (Week 2-3)

**File:** `template-v5-multi-revenue.html`

**Replace this:**
```html
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client={ADSENSE_ID}"
```

**With your actual ID:**
```html
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-YOUR_ACTUAL_ID"
```

**Get Ad Slot IDs:**
1. Go to AdSense dashboard
2. Click "Ads" → "By ad unit"
3. Create 2 ad units:
   - "PrixRetro - Top" (Display ad)
   - "PrixRetro - In-Content" (In-article ad)
4. Copy the slot IDs (look like: `1234567890`)
5. Replace `{ADSENSE_SLOT_TOP}` and `{ADSENSE_SLOT_CONTENT}` in template

### When Amazon Approved (Week 1)

**In all Amazon links, add:**
```
?tag=YOUR_TAG-21
```

**Example product recommendation:**
```html
<a href="https://www.amazon.fr/dp/B00005QD2R?tag=prixretro-21"
   target="_blank"
   rel="nofollow noopener"
   onclick="trackAmazonClick('pokemon-gold')">
    Acheter sur Amazon →
</a>
```

---

## Step 4: Deploy (After Template Updates)

```bash
# Switch to new template
cp template-v5-multi-revenue.html template-v4-compact.html

# Regenerate site
python3 update_site_compact.py

# Deploy
git add .
git commit -m "Add AdSense + Amazon affiliate integration"
git push
```

---

## 📊 Expected Timeline & Revenue

| Week | Action | Revenue |
|------|--------|---------|
| **Week 0** (TODAY) | Sign up AdSense + Amazon | 0€ |
| **Week 1** | Amazon approved, add links | +50€/month |
| **Week 2-3** | AdSense approved, add code | +250€/month |
| **Week 4** | Both live, traffic growing | +400€/month |
| **Month 2** | Create buying guides | +600€/month |
| **Month 3** | Traffic hits 20k | +900€/month |

**By Month 3:** 900€/month from ads + affiliates alone!
**Combined with B2B (Month 4):** 900€ + 1,200€ = **2,100€/month** ✅

---

## ✅ Checklist for TODAY

### Morning (30 min)
- [ ] Sign up Google AdSense
- [ ] Fill in profile completely
- [ ] Copy AdSense code
- [ ] Save it somewhere safe

### Afternoon (30 min)
- [ ] Sign up Amazon Partenaires
- [ ] Fill in profile completely
- [ ] Note your tracking ID
- [ ] Save it somewhere safe

### Optional (1 hour)
- [ ] Test Leboncoin scraper: `python3 scraper_leboncoin.py`
- [ ] Check what data you get
- [ ] Read INFRASTRUCTURE_BUILT.md

---

## 🚫 Common Mistakes to Avoid

### AdSense
❌ **DON'T** click your own ads (instant ban)
❌ **DON'T** ask others to click (ban)
❌ **DON'T** put "Click here" near ads (policy violation)
✅ **DO** place ads naturally in content
✅ **DO** use Auto Ads (Google optimizes)
✅ **DO** focus on quality content

### Amazon
❌ **DON'T** hide affiliate disclosure (required by law)
❌ **DON'T** use shortened links (breaks attribution)
❌ **DON'T** just spam links (low conversion)
✅ **DO** write helpful buying guides
✅ **DO** recommend quality products
✅ **DO** update links regularly (products go OOS)

---

## 💡 Pro Tips

### AdSense Approval Tips
- Need **20-30 quality pages** (you have this with GBC + GBA!)
- Need **original content** (you have real data, not copied!)
- Need **clear navigation** (you have this!)
- Need **privacy policy** (add this if missing!)

### Amazon Approval Tips
- Much easier than AdSense
- Usually approved in 24-48h
- Make sure website is accessible
- Have at least 10 pages of content

### After Approval
- **AdSense:** Wait 1-2 days for ads to start showing
- **Amazon:** Can start adding links immediately
- **Revenue:** First payment after 30-60 days

---

## 🎯 The Bottom Line

**Time investment:** 1 hour TODAY
**Potential revenue:** +700€/month when traffic scales
**ROI:** Literally HUNDREDS of euros per hour of work!

**This is the EASIEST money you'll make in this entire journey.**

Just sign up, wait for approval, add the codes. That's it.

**Do it TODAY while you're taking a break from sorting GBA! 💪**

---

## 📞 Need Help?

**AdSense help:** https://support.google.com/adsense
**Amazon help:** https://affiliate-program.amazon.fr/help

**Common questions:**
- "Do I need a company?" → No, individual is fine
- "Do I need to pay?" → No, both are 100% free
- "What if I get rejected?" → Fix issues and reapply
- "How long to get paid?" → 30-60 days after first earnings

**Just do it. Sign up today. Future you will thank you! 🚀**
