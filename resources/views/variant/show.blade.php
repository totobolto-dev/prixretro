@extends('layout')

@section('title')
{{ $variant->console->name }} {{ $variant->name }}@if(isset($statistics['count']) && $statistics['count'] > 0) - Prix ({{ number_format($statistics['avg_price'], 0) }}€)@endif | PrixRetro
@endsection

@section('meta_description')
@if(isset($statistics['count']) && $statistics['count'] > 0)Prix moyen {{ $variant->console->name }} {{ $variant->name }}: {{ number_format($statistics['avg_price'], 2) }}€ ({{ $statistics['count'] }} ventes). Historique et meilleures offres eBay.@else{{ $variant->console->name }} {{ $variant->name }} - Suivez les prix d'occasion sur eBay.@endif
@endsection

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <a href="/{{ $variant->console->slug }}">{{ $variant->console->name }}</a>
        <span>›</span>
        <span>{{ $variant->name }}</span>
    </div>

    <h1>{{ $variant->console->name }} {{ $variant->name }}</h1>

    @php
        // Get other variants for navigation
        $otherVariants = $variant->console->variants()
            ->where('id', '!=', $variant->id)
            ->withCount('listings')
            ->orderBy('name')
            ->get();

        // Check if ranking page is available
        $hasRanking = $variant->console->variants()
            ->whereHas('listings', function($q) {
                $q->where('status', 'approved');
            })
            ->count() >= 3;

        // Get current eBay listings for this variant
        $currentListings = \App\Models\CurrentListing::where('variant_id', $variant->id)
            ->where('is_sold', false)
            ->orderBy('price', 'asc')
            ->take(6)
            ->get();

        // eBay Partner Network affiliate parameters
        $ebayAffiliateParams = 'mkcid=1&mkrid=709-53476-19255-0&campid=5339134703';
    @endphp

    @if($hasRanking || $otherVariants->count() > 0)
    <div class="variant-navigation">
        @if($hasRanking)
        <a href="/{{ $variant->console->slug }}/classement" class="ranking-link">
            🏆 Classement des variantes
        </a>
        @endif

        @if($otherVariants->count() > 0)
        <div class="variant-selector">
            <label for="variant-select">Autres variantes:</label>
            <select id="variant-select" onchange="if(this.value) window.location.href=this.value">
                <option value="">{{ $variant->name }} (actuelle)</option>
                @foreach($otherVariants as $otherVariant)
                <option value="/{{ $variant->console->slug }}/{{ $otherVariant->slug }}">
                    {{ $otherVariant->name }}
                    @if($otherVariant->listings_count > 0)
                        ({{ $otherVariant->listings_count }} ventes)
                    @endif
                </option>
                @endforeach
            </select>
        </div>
        @endif
    </div>
    @endif

    {{-- Auto-generated SEO description --}}
    <div class="variant-description">
        <h2>À propos de {{ $variant->console->name }} {{ $variant->name }}</h2>
        <p>{{ $autoDescription }}</p>

        @if($statistics['count'] > 0)
        <h3>Guide d'achat</h3>
        <p>Sur le marché de l'occasion, l'état de la console est le facteur principal influençant le prix. Vérifiez toujours l'état de l'écran, le fonctionnement des boutons, et la présence de tous les accessoires d'origine.</p>
        @endif
    </div>

    @if($statistics['count'] > 0)
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Prix Moyen</div>
                <div class="stat-value">{{ number_format($statistics['avg_price'], 2) }}€</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Prix Min</div>
                <div class="stat-value">{{ number_format($statistics['min_price'], 2) }}€</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Prix Max</div>
                <div class="stat-value">{{ number_format($statistics['max_price'], 2) }}€</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Ventes Analysées</div>
                <div class="stat-value">{{ $statistics['count'] }}</div>
            </div>
        </div>

        <div class="chart-container">
            <h2>Évolution du Prix</h2>
            <canvas id="priceChart"></canvas>
        </div>

        @if($currentListings->count() > 0)
        <div class="current-listings-section">
            <h2>🛒 Actuellement en vente sur eBay ({{ $currentListings->count() }})</h2>
            <div class="current-listings-grid">
                @foreach($currentListings as $listing)
                <a href="{{ $listing->url }}?{{ $ebayAffiliateParams }}"
                   class="current-listing-card"
                   target="_blank"
                   rel="nofollow noopener"
                   onclick="trackEbayClick('current-{{ $variant->slug }}')">
                    <div class="current-listing-content">
                        <div class="current-listing-title">{{ $listing->title }}</div>
                        <div class="current-listing-meta">
                            <div class="current-listing-price">{{ number_format($listing->price, 0) }}€</div>
                            <div class="current-listing-badge">En vente</div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        @if(str_starts_with($variant->console->slug, 'game-boy') || str_starts_with($variant->console->slug, 'nintendo-ds') || str_starts_with($variant->console->slug, 'nintendo-2ds') || str_starts_with($variant->console->slug, 'nintendo-3ds'))
        <!-- Amazon Affiliate - Minimal Integration -->
        <div class="protection-section">
            <h2>💡 Protéger votre {{ $variant->console->name }}</h2>
            <p>Une console bien protégée conserve sa valeur. Nos données montrent un écart de prix moyen de <strong>+28%</strong> entre les consoles en parfait état et celles avec rayures visibles.</p>

            <div class="amazon-product">
                <div class="amazon-product-content">
                    <h3>💡 Housse de protection recommandée</h3>
                    <p><strong>Orzly pour Nintendo</strong> - Compatible New Nintendo 2DS XL, 3DS, New 3DS, Original DS, DSi, DS Lite, Game Boy Advance - Protection rigide EVA</p>
                    <div class="product-price">
                        <span class="price-label">Prix indicatif:</span>
                        <span class="price-value">~13,91€</span>
                    </div>
                    <a href="https://amzn.to/3Z0Y2mN"
                       target="_blank"
                       rel="nofollow noopener sponsored"
                       class="amazon-button"
                       onclick="trackAmazonClick('orzly-{{ $variant->slug }}')">
                        🛍️ Voir sur Amazon
                    </a>
                </div>
            </div>
            <p class="affiliate-note">* Lien affilié Amazon</p>
        </div>
        @endif

        <div class="listings-section">
            <h2>Ventes Récentes ({{ $statistics['count'] }} au total)</h2>

            <div class="cta-section">
                <a href="https://www.ebay.fr/sch/i.html?_nkw={{ urlencode(implode(' ', $variant->search_terms ?? [$variant->name])) }}&_sop=10&{{ $ebayAffiliateParams }}"
                   target="_blank"
                   rel="nofollow noopener"
                   class="cta-button"
                   onclick="trackEbayClick('search-{{ $variant->slug }}')">
                    🔍 Voir plus d'annonces sur eBay
                </a>
            </div>

            <div class="listings-table">
                <div class="listings-header-row">
                    <div>Article vendu</div>
                    <div>Prix</div>
                    <div class="listing-date-compact">Date</div>
                    <div class="listing-source-compact">Source</div>
                    <div class="listing-condition-compact">État</div>
                </div>

                @foreach($recentListings as $listing)
                <a href="{{ $listing->url }}?{{ $ebayAffiliateParams }}" class="listing-row" target="_blank" rel="nofollow noopener">
                    <div class="listing-title-compact">{{ $listing->title }}</div>
                    <div class="listing-price-compact">{{ number_format($listing->price, 0) }}€</div>
                    <div class="listing-date-compact">{{ $listing->sold_date?->format('d/m/Y') ?? 'N/A' }}</div>
                    <div class="listing-source-compact">{{ ucfirst($listing->source ?? 'eBay') }}</div>
                    <div class="listing-condition-compact">{{ $listing->condition ?? 'N/A' }}</div>
                </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="no-data">
            <p>Aucune donnée de prix disponible pour ce modèle pour le moment.</p>
            <a href="https://www.ebay.fr/sch/i.html?_nkw={{ urlencode(implode(' ', $variant->search_terms ?? [$variant->name])) }}&{{ $ebayAffiliateParams }}"
               target="_blank"
               rel="nofollow noopener"
               class="btn-primary">
                Voir sur eBay
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function trackAmazonClick(product) {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'click', {
            'event_category': 'affiliate',
            'event_label': 'amazon_' + product,
            'value': 1
        });
    }
}
</script>
@if($statistics['count'] > 0 && count($chartData['labels']) > 0)
<script>
const ctx = document.getElementById('priceChart').getContext('2d');
const chartData = @json($chartData);

new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.labels,
        datasets: [{
            label: 'Prix de vente',
            data: chartData.prices,
            borderColor: '#00ff88',
            backgroundColor: 'rgba(0, 255, 136, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.3,
            pointRadius: 4,
            pointBackgroundColor: '#00ff88',
            pointBorderColor: '#0f1419',
            pointBorderWidth: 2,
            pointHoverRadius: 7,
            pointHoverBorderWidth: 3,
            pointHoverBackgroundColor: '#00ff88',
            pointHoverBorderColor: '#00d9ff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'nearest',
            axis: 'x',
            intersect: false
        },
        onClick: (event, activeElements) => {
            if (activeElements.length > 0) {
                const index = activeElements[0].index;
                const url = chartData.urls[index];
                if (url) {
                    window.open(url, '_blank', 'noopener,noreferrer');
                }
            }
        },
        layout: {
            padding: {
                top: 15,
                right: 15,
                bottom: 5,
                left: 5
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                enabled: true,
                mode: 'nearest',
                intersect: false,
                backgroundColor: '#1a1f29',
                titleColor: '#ffffff',
                bodyColor: '#00ff88',
                borderColor: '#2a2f39',
                borderWidth: 1,
                padding: 12,
                displayColors: false,
                titleFont: {
                    size: 11,
                    weight: 'normal'
                },
                bodyFont: {
                    size: 14,
                    weight: '600'
                },
                callbacks: {
                    title: function(context) {
                        const index = context[0].dataIndex;
                        return chartData.titles[index];
                    },
                    label: function(context) {
                        return context.parsed.y + '€';
                    },
                    afterLabel: function(context) {
                        const index = context.dataIndex;
                        return chartData.labels[index] + ' • Cliquer pour voir';
                    }
                }
            }
        },
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    color: '#6b7280',
                    maxRotation: 0,
                    autoSkipPadding: 20
                }
            },
            y: {
                beginAtZero: false,
                grid: {
                    color: '#2a2f39',
                    drawBorder: false
                },
                ticks: {
                    color: '#6b7280',
                    callback: function(value) {
                        return value + '€';
                    }
                }
            }
        }
    }
});
</script>
@endif
@endsection
