# Laravel Migration Strategy - PrixRetro

## Executive Summary

**Goal**: Transition from static HTML to Laravel application while preserving SEO authority and expanding features.

**Timeline**: 3-6 months phased approach  
**Risk Level**: Low (using redirects to preserve SEO)  
**Business Impact**: Enable user features, better scalability, revenue optimization

---

## Phase 1: Infrastructure Setup (Week 1-2)

### Domain Strategy
- **Keep current setup**: prixretro.com on OVH hosting  
- **Laravel subdomain**: app.prixretro.com for development/testing
- **Final migration**: Replace static site with Laravel on main domain

### Technical Setup
```bash
# Laravel 10 installation
composer create-project laravel/laravel prixretro-laravel
cd prixretro-laravel

# Essential packages
composer require laravel/breeze  # Authentication
composer require spatie/laravel-sitemap  # SEO
composer require intervention/image  # Image processing
```

### Database Design
```sql
-- Core tables
CREATE TABLE consoles (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    brand VARCHAR(100),
    release_year INT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE variants (
    id BIGINT PRIMARY KEY,
    console_id BIGINT,
    name VARCHAR(255),
    slug VARCHAR(255) UNIQUE,
    description TEXT,
    rarity_level ENUM('common', 'uncommon', 'rare', 'very_rare'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (console_id) REFERENCES consoles(id)
);

CREATE TABLE price_data (
    id BIGINT PRIMARY KEY,
    variant_id BIGINT,
    price DECIMAL(10,2),
    source_url VARCHAR(500),
    sold_date DATE,
    condition VARCHAR(100),
    title TEXT,
    is_verified BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    FOREIGN KEY (variant_id) REFERENCES variants(id)
);
```

---

## Phase 2: Feature Parity (Week 3-4)

### URL Structure Preservation
**Critical for SEO**: Maintain exact same URLs

```php
// routes/web.php
Route::get('/game-boy-color-{variant}', [VariantController::class, 'show'])
    ->where('variant', '[a-z0-9-]+')
    ->name('variant.show');

Route::get('/', [HomeController::class, 'index'])->name('home');

// Sitemap preservation
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
```

### Data Migration
```php
// Migration script: import scraped_data_ultra_clean.json
class ImportCurrentDataCommand extends Command
{
    public function handle()
    {
        $data = json_decode(file_get_contents('scraped_data_ultra_clean.json'), true);
        
        $console = Console::create([
            'name' => 'Game Boy Color',
            'slug' => 'game-boy-color',
            'brand' => 'Nintendo',
            'release_year' => 1998
        ]);

        foreach ($data as $variantKey => $variantData) {
            $variant = Variant::create([
                'console_id' => $console->id,
                'name' => $variantData['variant_name'],
                'slug' => $variantKey,
                'description' => $variantData['description']
            ]);

            foreach ($variantData['listings'] as $listing) {
                PriceData::create([
                    'variant_id' => $variant->id,
                    'price' => $listing['price'],
                    'source_url' => $listing['url'],
                    'sold_date' => $listing['sold_date'],
                    'condition' => $listing['condition'],
                    'title' => $listing['title'],
                    'is_verified' => true
                ]);
            }
        }
    }
}
```

---

## Phase 3: Enhanced Features (Week 5-8)

### User Authentication & Profiles
```php
// User features
- Account registration/login
- Price alert subscriptions
- Wishlist functionality
- Personal collection tracking
- Selling history
```

### Advanced Analytics
```php
// Enhanced tracking
class AnalyticsService
{
    public function trackUserBehavior($action, $variant = null)
    {
        // Track with Laravel's built-in logging + Google Analytics
        // - Price alert registrations
        // - Wishlist additions
        // - Collection updates
        // - eBay click conversions by user segment
    }
}
```

### API Development
```php
// RESTful API for mobile app
Route::prefix('api/v1')->group(function () {
    Route::get('/variants', [ApiController::class, 'variants']);
    Route::get('/variants/{slug}/prices', [ApiController::class, 'priceHistory']);
    Route::post('/price-alerts', [ApiController::class, 'createAlert']);
});
```

---

## Phase 4: SEO Migration (Week 9-10)

### Pre-Migration Checklist
- [ ] All static URLs work in Laravel
- [ ] Meta tags identical to current site
- [ ] Sitemap generates correctly
- [ ] Analytics tracking preserved
- [ ] Page load times ≤ current static site

### Migration Process
```bash
# 1. Test on subdomain
# 2. Set up 301 redirects (if needed)
# 3. Replace static site atomically
# 4. Monitor Google Search Console

# Backup strategy
cp -r /current/static/site /backup/pre-laravel-$(date +%Y%m%d)
```

### Post-Migration Monitoring
- Google Analytics traffic patterns
- Search Console crawl errors
- Page load performance
- Affiliate click-through rates

---

## Phase 5: Revenue Optimization (Week 11-12)

### Enhanced Affiliate Tracking
```php
class AffiliateService
{
    public function generateEbayUrl($variant, $userId = null)
    {
        // User-specific affiliate tracking
        // A/B testing for CTA variations
        // Conversion funnel optimization
    }
}
```

### Price Alerts System
```php
// Email notifications when prices drop
class PriceAlertJob implements ShouldQueue
{
    public function handle()
    {
        $alerts = PriceAlert::with(['user', 'variant'])->active()->get();
        
        foreach ($alerts as $alert) {
            $currentPrice = $alert->variant->currentAveragePrice();
            if ($currentPrice <= $alert->target_price) {
                Mail::to($alert->user)->send(new PriceDropAlert($alert, $currentPrice));
            }
        }
    }
}
```

---

## Risk Mitigation

### SEO Risks
- **URL changes**: Use exact URL structure 
- **Content changes**: Preserve all meta tags, structured data
- **Performance**: Laravel caching, CDN integration
- **Downtime**: Blue-green deployment

### Technical Risks  
- **Data loss**: Multiple backups, staged migration
- **Performance regression**: Load testing, monitoring
- **Feature gaps**: Feature parity validation

### Business Risks
- **Revenue loss**: A/B testing, gradual rollout
- **User experience**: Beta testing with small user group

---

## Success Metrics

### SEO Preservation
- Organic traffic maintained (±5%)
- Search rankings stable
- Zero 404 errors
- Sitemap acceptance rate 100%

### Feature Adoption
- User registration rate >10%
- Price alert subscriptions >50 users/month
- Affiliate click-through rate improved by 15%

### Technical Performance
- Page load time <2s
- 99.9% uptime
- Zero critical bugs post-migration

---

## Technology Stack

### Core Laravel
- **Framework**: Laravel 10 (PHP 8.2)
- **Database**: MySQL 8.0
- **Queue**: Redis + Laravel Horizon
- **Cache**: Redis

### Frontend
- **CSS**: Tailwind CSS (maintain current dark theme)
- **JS**: Alpine.js + Chart.js (for price graphs)
- **Build**: Vite

### Infrastructure
- **Hosting**: OVH VPS (upgrade if needed)
- **CDN**: CloudFlare (already configured)
- **Monitoring**: Laravel Telescope + external monitoring

### SEO & Analytics
- **Sitemap**: Spatie Laravel Sitemap
- **SEO**: Preserve current meta tags + structured data
- **Analytics**: Google Analytics 4 + custom event tracking

---

## Next Immediate Steps

1. **Create Laravel project skeleton** (2 days)
2. **Design database schema** (1 day) 
3. **Import current data** (1 day)
4. **Build URL routing system** (2 days)
5. **Replicate current page templates** (3 days)

**Ready to proceed when you give the green light.**