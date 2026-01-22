@extends('layout')

@section('title', $console->name . ' - Prix & Historique | PrixRetro')

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <span>{{ $console->name }}</span>
    </div>

    <div class="console-page-header">
        <h1>{{ $console->name }}</h1>

        @if($statistics['count'] > 0)
        <div class="value-prop-banner">
            <div class="value-prop-icon">💰</div>
            <div class="value-prop-content">
                <h3>Marché global {{ $console->name }}</h3>
                <p>Vue d'ensemble basée sur <strong>{{ $statistics['count'] }} ventes analysées</strong> pour toutes les variantes {{ $console->name }}. Découvrez les tendances du marché avant d'acheter.</p>
            </div>
        </div>
        @endif

        <div class="console-description-box">
            <p>{{ $autoDescription }}</p>
        </div>

        @if(isset($guideUrl))
        <div style="margin: 1.5rem 0; padding: 1rem; background: var(--bg-card); border: 1px solid var(--accent-primary); border-radius: var(--radius);">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 1.5rem;">📖</span>
                <div style="flex: 1;">
                    <div style="font-weight: 600; margin-bottom: 0.25rem;">Guide d'achat complet</div>
                    <div style="font-size: 0.9rem; color: var(--text-secondary);">Comment choisir sa {{ $console->name }}, éviter les arnaques, et trouver les meilleures offres</div>
                </div>
                <a href="{{ $guideUrl }}" style="background: var(--accent-primary); color: var(--bg-primary); padding: 0.5rem 1rem; border-radius: var(--radius); text-decoration: none; font-weight: 600; white-space: nowrap;">
                    Lire le guide →
                </a>
            </div>
        </div>
        @endif

        <div class="console-stats">
            {{ $console->variants->count() }} variantes •
            {{ $console->variants->sum('listings_count') }} ventes analysées
        </div>

        @if($console->variants->filter(fn($v) => $v->listings_count > 0)->count() >= 3)
        <div class="cta-section" style="margin-top: 1.5rem;">
            <a href="/{{ $console->slug }}/classement" class="cta-button">
                🏆 Voir le classement des variantes les plus vendues
            </a>
        </div>
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
            <h2>Évolution du Prix (Toutes Variantes)</h2>
            <canvas id="priceChart"></canvas>
        </div>

        <div class="listings-section">
            <h2>Ventes Récentes ({{ $statistics['count'] }} au total)</h2>

            <div class="listings-table">
                <div class="listings-header-row">
                    <div>Article vendu</div>
                    <div>Prix</div>
                    <div class="listing-date-compact">Date</div>
                    <div class="listing-source-compact">Source</div>
                </div>

                @foreach($recentListings as $listing)
                @php
                    $ebayAffiliateParams = 'mkcid=1&mkrid=709-53476-19255-0&campid=5339134703';
                @endphp
                <a href="{{ $listing->url }}?{{ $ebayAffiliateParams }}" class="listing-row" target="_blank" rel="nofollow noopener">
                    <div class="listing-title-compact">{{ $listing->title }}</div>
                    <div class="listing-price-compact">{{ number_format($listing->price, 0) }}€</div>
                    <div class="listing-date-compact">{{ $listing->sold_date?->format('d/m/Y') ?? 'N/A' }}</div>
                    <div class="listing-source-compact">{{ ucfirst($listing->source ?? 'eBay') }}</div>
                </a>
                @endforeach
            </div>
        </div>

        {{-- Most Collected Variants (Social Proof) --}}
        @if($mostCollected->count() > 0)
        <div style="margin: 3rem 0; background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border-color); padding: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                <span style="font-size: 2rem;">🔥</span>
                <div>
                    <h2 style="margin: 0;">Variantes Populaires</h2>
                    <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0; font-size: 0.95rem;">
                        Les variantes les plus suivies par nos collectionneurs
                    </p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                @foreach($mostCollected as $variant)
                <a href="/{{ $console->slug }}/{{ $variant->slug }}" style="text-decoration: none; color: inherit; background: var(--bg-darker); border-radius: var(--radius); padding: 1.25rem; border: 2px solid transparent; transition: all 0.2s;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <div style="font-weight: 600; font-size: 1.05rem;">{{ $variant->name }}</div>
                        <div style="display: flex; align-items: center; gap: 0.25rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 600;">
                            <span>👥</span>
                            <span>{{ $variant->collectors_count }}</span>
                        </div>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary);">
                        {{ $variant->collectors_count }} {{ $variant->collectors_count > 1 ? 'collectionneurs' : 'collectionneur' }}
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <h2 style="margin-top: 3rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Explorer par Variante</h2>
    @endif

    @php
        // Group variants by category for better organization
        $categorized = $console->variants->groupBy(function($variant) {
            if (str_starts_with($variant->slug, 'sp-')) {
                return 'SP';
            } elseif (str_starts_with($variant->slug, 'micro-')) {
                return 'Micro';
            } elseif (str_starts_with($variant->slug, 'lite-')) {
                return 'Lite';
            } elseif (str_starts_with($variant->slug, 'dsi-')) {
                return 'DSi';
            } elseif (str_starts_with($variant->slug, 'xl-')) {
                return 'XL';
            } else {
                return 'Standard';
            }
        });

        // Order categories
        $categoryOrder = ['Standard', 'SP', 'Micro', 'Lite', 'DSi', 'XL'];
        $orderedCategories = collect($categoryOrder)->filter(fn($cat) => isset($categorized[$cat]));
    @endphp

    @foreach($orderedCategories as $category)
        @if($categorized->has($category))
            @if($category !== 'Standard' || $orderedCategories->count() > 1)
                <h3 class="variant-category-title">{{ $category }}</h3>
            @endif
            <div class="variant-grid">
                @foreach($categorized[$category]->sortByDesc('listings_count') as $variant)
                <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-card">
                    <div class="variant-name">{{ $variant->short_name }}</div>
                    <div class="variant-stats">
                        @if($variant->listings_count > 0)
                            @php
                                $avgPrice = \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->avg('price');
                            @endphp
                            <span class="price">{{ number_format($avgPrice, 0) }}€</span>
                            <span>{{ $variant->listings_count }} ventes</span>
                        @else
                            <span class="no-data">Pas encore de données</span>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    @endforeach

    @if($relatedConsoles->count() > 0)
    <div class="related-consoles-section">
        <h2 style="margin-top: 3rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Consoles similaires</h2>
        <div class="variant-grid">
            @foreach($relatedConsoles as $relatedConsole)
            <a href="/{{ $relatedConsole->slug }}" class="variant-card">
                <div class="variant-name">{{ $relatedConsole->name }}</div>
                <div class="variant-stats">
                    <span>{{ $relatedConsole->variants_count }} variantes</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    <div class="back-link">
        <a href="/">← Retour à l'accueil</a>
    </div>
</div>
@endsection

@section('scripts')
@if(isset($statistics) && $statistics['count'] > 0 && count($chartData['labels']) > 0)
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
