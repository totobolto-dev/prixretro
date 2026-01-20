@extends('layout')

@section('title')
{{ $variant->display_name }}@if(isset($statistics['count']) && $statistics['count'] > 0) - Prix ({{ number_format($statistics['avg_price'], 0) }}€)@endif | PrixRetro
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

    <h1>{{ $variant->display_name }}</h1>

    @if($statistics['count'] > 0)
    <div class="value-prop-banner">
        <div class="value-prop-icon">💰</div>
        <div class="value-prop-content">
            <h3>Prix basés sur des ventes réelles</h3>
            <p>Nos données proviennent de <strong>{{ $statistics['count'] }} ventes analysées</strong> sur eBay France. Évitez de payer trop cher en consultant les prix du marché avant d'acheter.</p>
        </div>
    </div>
    @endif

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
        <h2>À propos de {{ $variant->display_name }}</h2>
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

        {{-- Nintendo Portable Protection (Orzly Case) --}}
        @if((str_starts_with($variant->console->slug, 'game-boy') || str_starts_with($variant->console->slug, 'nintendo-ds') || str_starts_with($variant->console->slug, 'new-nintendo-2ds-xl') || str_starts_with($variant->console->slug, 'nintendo-3ds')) && $variant->console->slug !== 'nintendo-2ds')
        <div class="protection-section">
            <h2>💡 Protéger votre {{ $variant->console->name }}</h2>
            <p>Une console bien protégée conserve sa valeur. Les consoles en excellent état se vendent en moyenne <strong>28% plus cher</strong> que celles avec des rayures visibles.</p>

            <div class="amazon-product-card">
                <div class="amazon-badge">Recommandation</div>
                <h3>Housse de protection Orzly</h3>
                <p class="amazon-description">Protection rigide EVA compatible avec Game Boy Advance, DS, DS Lite, DSi, 3DS, New 3DS, et 2DS XL. Matériau anti-choc avec compartiments pour jeux.</p>

                <div class="amazon-details">
                    <div class="amazon-price">
                        <span class="amazon-price-label">Prix</span>
                        <span class="amazon-price-value">~14€</span>
                    </div>
                    <a href="https://amzn.to/3Z0Y2mN"
                       target="_blank"
                       rel="nofollow noopener sponsored"
                       class="amazon-cta"
                       onclick="trackAmazonClick('orzly-{{ $variant->slug }}')">
                        Voir sur Amazon
                    </a>
                </div>
                <p class="amazon-disclaimer">Lien affilié • Commission sans surcoût pour vous</p>
            </div>
        </div>
        @endif

        {{-- PlayStation Portable Protection --}}
        @if(str_starts_with($variant->console->slug, 'psp') || str_starts_with($variant->console->slug, 'ps-vita'))
        <div class="protection-section">
            <h2>💡 Protéger votre {{ $variant->console->name }}</h2>
            <p>Conservez votre console portable en parfait état. Une {{ $variant->console->name }} bien protégée maintient sa valeur et évite les rayures sur l'écran.</p>

            <div class="amazon-product-card">
                <div class="amazon-badge">Recommandation</div>
                <h3>Housse de protection rigide EVA</h3>
                <p class="amazon-description">Protection rigide EVA pour {{ $variant->console->name }}. Matériau anti-choc avec compartiments pour jeux et câbles. Compatible avec tous les modèles PSP et PS Vita.</p>

                <div class="amazon-details">
                    <div class="amazon-price">
                        <span class="amazon-price-label">Prix</span>
                        <span class="amazon-price-value">~12-15€</span>
                    </div>
                    <a href="https://www.amazon.fr/s?k=housse+protection+{{ str_replace(' ', '+', strtolower($variant->console->name)) }}&tag=prixretro-21"
                       target="_blank"
                       rel="nofollow noopener sponsored"
                       class="amazon-cta"
                       onclick="trackAmazonClick('case-{{ $variant->slug }}')">
                        Voir sur Amazon
                    </a>
                </div>
                <p class="amazon-disclaimer">Lien affilié • Commission sans surcoût pour vous</p>
            </div>
        </div>
        @endif

        {{-- HDMI Adapters for Retro Home Consoles --}}
        @if(in_array($variant->console->slug, ['playstation', 'playstation-2', 'ps-one', 'nintendo-64', 'gamecube', 'super-nintendo', 'mega-drive', 'saturn', 'dreamcast', 'nes', 'master-system']))
        <div class="protection-section">
            <h2>📺 Connecter sur TV moderne</h2>
            <p>Profitez de votre {{ $variant->console->name }} sur votre TV HDMI actuelle. Les adaptateurs HDMI offrent une image nette et éliminent les problèmes de compatibilité.</p>

            <div class="amazon-product-card">
                <div class="amazon-badge">Accessoire essentiel</div>
                <h3>
                    @if(in_array($variant->console->slug, ['playstation', 'playstation-2', 'ps-one']))
                        Adaptateur HDMI pour PlayStation 1/2
                    @elseif(in_array($variant->console->slug, ['nintendo-64', 'gamecube', 'super-nintendo']))
                        Adaptateur HDMI pour {{ $variant->console->name }}
                    @else
                        Adaptateur HDMI Sega
                    @endif
                </h3>
                <p class="amazon-description">Convertisseur vidéo et audio vers HDMI. Plug & play, pas de drivers nécessaires. Compatible avec toutes les TV HDMI modernes.</p>

                <div class="amazon-details">
                    <div class="amazon-price">
                        <span class="amazon-price-label">Prix</span>
                        <span class="amazon-price-value">~15-25€</span>
                    </div>
                    <a href="https://www.amazon.fr/s?k=adaptateur+hdmi+{{ str_replace(['-', ' '], '+', strtolower($variant->console->slug)) }}&tag=prixretro-21"
                       target="_blank"
                       rel="nofollow noopener sponsored"
                       class="amazon-cta"
                       onclick="trackAmazonClick('hdmi-{{ $variant->slug }}')">
                        Voir sur Amazon
                    </a>
                </div>
                <p class="amazon-disclaimer">Lien affilié • Commission sans surcoût pour vous</p>
            </div>
        </div>
        @endif

        {{-- Memory Cards for PlayStation 2 and GameCube --}}
        @if(in_array($variant->console->slug, ['playstation-2', 'gamecube']))
        <div class="protection-section">
            <h2>💾 Carte mémoire essentielle</h2>
            <p>@if($variant->console->slug === 'playstation-2')Impossible de sauvegarder vos parties sur PS2 sans carte mémoire. Les cartes 8MB sont le standard recommandé.@else Les Memory Cards GameCube sont indispensables pour sauvegarder votre progression dans les jeux.@endif</p>

            <div class="amazon-product-card">
                <div class="amazon-badge">Accessoire indispensable</div>
                <h3>
                    @if($variant->console->slug === 'playstation-2')
                        Carte mémoire PS2 8MB
                    @else
                        Memory Card GameCube 128MB
                    @endif
                </h3>
                <p class="amazon-description">
                    @if($variant->console->slug === 'playstation-2')
                        Carte mémoire officielle 8MB pour PlayStation 2. Compatible avec tous les modèles PS2. Capacité pour des dizaines de sauvegardes.
                    @else
                        Memory Card haute capacité pour GameCube. Compatible avec tous les jeux et consoles GameCube. Format 128MB pour une capacité maximale.
                    @endif
                </p>

                <div class="amazon-details">
                    <div class="amazon-price">
                        <span class="amazon-price-label">Prix</span>
                        <span class="amazon-price-value">~8-12€</span>
                    </div>
                    <a href="https://www.amazon.fr/s?k=carte+memoire+{{ str_replace(['-', ' '], '+', strtolower($variant->console->slug)) }}&tag=prixretro-21"
                       target="_blank"
                       rel="nofollow noopener sponsored"
                       class="amazon-cta"
                       onclick="trackAmazonClick('memory-{{ $variant->slug }}')">
                        Voir sur Amazon
                    </a>
                </div>
                <p class="amazon-disclaimer">Lien affilié • Commission sans surcoût pour vous</p>
            </div>
        </div>
        @endif

        <div class="listings-section">
            <h2>Ventes Récentes ({{ $statistics['count'] }} au total)</h2>

            <div class="cta-section">
                @php
                    $searchQuery = $variant->search_terms
                        ? implode(' ', $variant->search_terms)
                        : $variant->display_name;
                @endphp
                <a href="https://www.ebay.fr/sch/i.html?_nkw={{ urlencode($searchQuery) }}&_sop=10&{{ $ebayAffiliateParams }}"
                   target="_blank"
                   rel="nofollow noopener"
                   class="cta-button"
                   onclick="trackEbayClick('search-{{ $variant->slug }}')">
                    Voir les meilleures offres sur eBay
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
            @php
                $searchQuery = $variant->search_terms
                    ? implode(' ', $variant->search_terms)
                    : $variant->console->name . ' ' . $variant->name;
            @endphp
            <a href="https://www.ebay.fr/sch/i.html?_nkw={{ urlencode($searchQuery) }}&{{ $ebayAffiliateParams }}"
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
