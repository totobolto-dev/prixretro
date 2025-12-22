# Google Analytics Setup for PrixRetro

## Quick Setup Steps

### 1. Create Google Analytics Account
1. Go to https://analytics.google.com
2. Create account for "PrixRetro"
3. Add property for "prixretro.com"
4. Get your GA4 Measurement ID (format: G-XXXXXXXXXX)

### 2. Update Template
Replace `G-XXXXXXXXXX` in `template-v3.html` with your real tracking ID

### 3. Regenerate Site
```bash
python3 update_site.py
```

### 4. Deploy and Test
- Upload to OVH (auto-deploy via GitHub Actions)
- Test with Google Tag Assistant Chrome extension
- Verify tracking in GA4 Real-time reports

## What We Track

### Page Views
- Automatic tracking of all variant pages
- User engagement metrics
- Traffic sources (organic, direct, referral)

### Affiliate Clicks
- eBay button clicks by variant
- Conversion tracking for revenue optimization
- A/B testing data for different CTA styles

### Key Metrics to Monitor
- **Traffic**: Organic search growth from SEO
- **Engagement**: Time on page, bounce rate
- **Conversions**: eBay click-through rates
- **Top variants**: Which Game Boy Colors get most traffic

## Revenue Optimization
1. Track which variants convert best
2. A/B test different price presentations
3. Monitor affiliate commission performance
4. Optimize high-traffic, low-conversion pages

## Laravel Migration Benefits
- User accounts = better tracking & personalization
- Price alerts = email conversion tracking  
- Wishlist = engagement metrics
- API = mobile app analytics