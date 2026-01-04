@extends('layout')

@section('title', 'Quelle ' . $console->name . ' se vend le plus en France en ' . date('Y') . ' ? | PrixRetro')

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <a href="/{{ $console->slug }}">{{ $console->name }}</a>
        <span>›</span>
        <span>Classement des variantes</span>
    </div>

    <h1>Quelle {{ $console->name }} se vend le plus en France en {{ date('Y') }} ?</h1>

    <div class="content-intro">
        <p><strong>Analyse de {{ number_format($totalSales) }} ventes eBay récentes</strong> pour identifier les variantes de {{ $console->name }} les plus populaires sur le marché français du retrogaming.</p>
        <p>Nos données proviennent de ventes réelles (vendues et terminées) sur eBay.fr, mises à jour quotidiennement.</p>
    </div>

    @if($rankedVariants->count() > 0)
        <div class="ranking-section">
            <h2>🏆 Top {{ $rankedVariants->count() }} des variantes les plus vendues</h2>

            <div class="ranking-table">
                <div class="ranking-header-row">
                    <div class="rank-col">Rang</div>
                    <div class="variant-col">Variante</div>
                    <div class="sales-col">Ventes</div>
                    <div class="price-col">Prix Moyen</div>
                    <div class="range-col">Fourchette</div>
                </div>

                @foreach($rankedVariants as $item)
                <a href="/{{ $console->slug }}/{{ $item['variant']->slug }}" class="ranking-row">
                    <div class="rank-col">
                        <span class="rank-badge rank-{{ $item['rank'] }}">#{{ $item['rank'] }}</span>
                    </div>
                    <div class="variant-col">
                        <strong>{{ $item['variant']->name }}</strong>
                        @if($item['variant']->is_special_edition)
                            <span class="badge-special">Édition Spéciale</span>
                        @endif
                    </div>
                    <div class="sales-col">
                        <span class="sales-count">{{ number_format($item['sales_count']) }}</span>
                        <span class="sales-label">ventes</span>
                    </div>
                    <div class="price-col">
                        <span class="avg-price">{{ number_format($item['avg_price'], 2) }}€</span>
                    </div>
                    <div class="range-col">
                        <span class="price-range">{{ number_format($item['min_price'], 0) }}€ - {{ number_format($item['max_price'], 0) }}€</span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>

        <div class="insights-section">
            <h2>📊 Ce que nos données révèlent</h2>

            <div class="insight-card">
                <h3>Volume de ventes</h3>
                <p>La variante <strong>{{ $rankedVariants->first()['variant']->name }}</strong> représente <strong>{{ number_format(($rankedVariants->first()['sales_count'] / $totalSales) * 100, 1) }}%</strong> du marché avec {{ number_format($rankedVariants->first()['sales_count']) }} ventes analysées.</p>
            </div>

            <div class="insight-card">
                <h3>Écart de prix</h3>
                @php
                    $priceGap = (($rankedVariants->max('avg_price') - $rankedVariants->min('avg_price')) / $rankedVariants->min('avg_price')) * 100;
                @endphp
                <p>L'écart de prix moyen entre la variante la moins chère et la plus chère est de <strong>{{ number_format($priceGap, 0) }}%</strong>, reflétant les différences de rareté et de demande.</p>
            </div>

            <div class="insight-card">
                <h3>Prix moyen du marché</h3>
                <p>Le prix moyen toutes variantes confondues est de <strong>{{ number_format($avgConsolePrice, 2) }}€</strong> pour {{ number_format($totalSales) }} ventes récentes analysées.</p>
            </div>
        </div>

        <div class="cta-section">
            <h2>📈 Voir les prix détaillés</h2>
            <p>Cliquez sur une variante ci-dessus pour accéder à l'historique complet des prix, le graphique d'évolution, et les ventes récentes.</p>
            <a href="/{{ $console->slug }}" class="cta-button">
                ← Retour à {{ $console->name }}
            </a>
        </div>
    @else
        <div class="no-data">
            <p>Pas encore assez de données pour établir un classement fiable.</p>
            <a href="/{{ $console->slug }}" class="btn-primary">Retour à {{ $console->name }}</a>
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
@endsection
