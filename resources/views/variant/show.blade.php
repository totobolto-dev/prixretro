@extends('layout')

@section('title', $variant->name . ' - Prix & Historique | PrixRetro')

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

        <div class="listings-section">
            <h2>Ventes Récentes ({{ $statistics['count'] }} au total)</h2>

            <div class="cta-section">
                <a href="https://www.ebay.fr/sch/i.html?_nkw={{ urlencode(implode(' ', $variant->search_terms ?? [$variant->name])) }}&_sop=10"
                   target="_blank"
                   rel="nofollow noopener"
                   class="cta-button"
                   onclick="trackEbayClick('{{ $variant->slug }}')">
                    🔍 Voir les annonces actuelles sur eBay
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
                <a href="{{ $listing->url }}" class="listing-row" target="_blank" rel="nofollow noopener">
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
            <a href="https://www.ebay.fr/sch/i.html?_nkw={{ urlencode(implode(' ', $variant->search_terms ?? [$variant->name])) }}"
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
                        const title = chartData.titles[index];
                        return title.length > 50 ? title.substring(0, 50) + '...' : title;
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
