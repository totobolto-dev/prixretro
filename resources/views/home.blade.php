@extends('layout')

@section('title', 'PrixRetro - Tracker de Prix Retrogaming')

@section('content')
<div class="container">
    <div class="hero">
        <h1>PrixRetro</h1>
        <p class="tagline">
            Prix du marché pour consoles retrogaming d'occasion<br>
            <span style="color: var(--text-muted); font-size: 0.95rem;">
                {{ $consoles->sum(fn($c) => $c->variants->count()) }} variantes •
                {{ $consoles->sum(fn($c) => $c->variants->sum('listings_count')) }} ventes analysées
            </span>
        </p>
    </div>

    {{-- Compact Highlights Tables --}}
    <div class="highlights-compact">
        <div class="compact-table">
            <h3 class="compact-title">🕒 Dernières ventes</h3>
            <div class="compact-list">
                @foreach($latestSales->take(10) as $listing)
                <a href="/{{ $listing->variant->console->slug }}/{{ $listing->variant->slug }}" class="compact-row">
                    <span class="compact-name">{{ $listing->title }}</span>
                    <span class="compact-price">{{ number_format($listing->price, 0) }}€</span>
                </a>
                @endforeach
            </div>
        </div>

        <div class="compact-table">
            <h3 class="compact-title">💰 Records de prix</h3>
            <div class="compact-list">
                @php
                    $allRecords = collect();
                    foreach($priceRecords as $record) {
                        foreach($record['listings'] as $listing) {
                            $allRecords->push($listing);
                        }
                    }
                    $topRecords = $allRecords->sortByDesc('price')->take(10);
                @endphp
                @foreach($topRecords as $listing)
                <a href="/{{ $listing->variant->console->slug }}/{{ $listing->variant->slug }}" class="compact-row">
                    <span class="compact-name">{{ $listing->title }}</span>
                    <span class="compact-price compact-price-highlight">{{ number_format($listing->price, 0) }}€</span>
                </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Quick Navigation Overview --}}
    <div class="console-families-nav">
        <a href="#game-boy-family" class="family-nav-link">🎮 Famille Game Boy</a>
        <a href="#ds-family" class="family-nav-link">📱 Famille DS</a>
        <a href="#3ds-family" class="family-nav-link">🎯 Famille 3DS</a>
    </div>

    @php
        // Group consoles by family based on display_order
        $gameBoyFamily = $consoles->filter(fn($c) => $c->display_order >= 100 && $c->display_order < 200);
        $dsFamily = $consoles->filter(fn($c) => $c->display_order >= 200 && $c->display_order < 300);
        $threeDsFamily = $consoles->filter(fn($c) => $c->display_order >= 300 && $c->display_order < 400);
    @endphp

    {{-- Game Boy Family --}}
    @if($gameBoyFamily->count() > 0)
    <div class="console-family-section" id="game-boy-family">
        <h2 class="family-header">🎮 Famille Game Boy</h2>
        <p class="family-description">Les consoles portables qui ont révolutionné le jeu nomade (1989-2008)</p>

        <div class="console-grid">
            @foreach($gameBoyFamily as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-icon">🎮</div>
                    <div class="console-info">
                        <h3 class="console-name">
                            <a href="/{{ $console->slug }}">{{ $console->name }}</a>
                        </h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')">
                        <span class="expand-icon">▶</span>
                    </button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->name }}</span>
                            <span class="variant-data-compact">
                                @if($variant->listings_count > 0)
                                    @php
                                        $avgPrice = \App\Models\Listing::where('variant_id', $variant->id)
                                            ->where('status', 'approved')
                                            ->avg('price');
                                    @endphp
                                    <span class="price-compact">{{ number_format($avgPrice, 0) }}€</span>
                                    <span class="sales-compact">{{ $variant->listings_count }} ventes</span>
                                @else
                                    <span class="no-data-compact">Pas de données</span>
                                @endif
                            </span>
                        </a>
                        @endforeach
                    </div>

                    @php
                        $hasRanking = $console->variants->filter(fn($v) => $v->listings_count > 0)->count() >= 3;
                    @endphp
                    @if($hasRanking)
                    <div class="ranking-link-compact">
                        <a href="/{{ $console->slug }}/classement">🏆 Voir le classement</a>
                    </div>
                    @endif
                </div>
                @else
                <div class="no-variants">
                    <p>Console ajoutée récemment - données en cours de collecte</p>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- DS Family --}}
    @if($dsFamily->count() > 0)
    <div class="console-family-section" id="ds-family">
        <h2 class="family-header">📱 Famille Nintendo DS</h2>
        <p class="family-description">Les consoles à double écran tactile (2004-2013)</p>

        <div class="console-grid">
            @foreach($dsFamily as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-icon">📱</div>
                    <div class="console-info">
                        <h3 class="console-name">
                            <a href="/{{ $console->slug }}">{{ $console->name }}</a>
                        </h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')">
                        <span class="expand-icon">▶</span>
                    </button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->name }}</span>
                            <span class="variant-data-compact">
                                @if($variant->listings_count > 0)
                                    @php
                                        $avgPrice = \App\Models\Listing::where('variant_id', $variant->id)
                                            ->where('status', 'approved')
                                            ->avg('price');
                                    @endphp
                                    <span class="price-compact">{{ number_format($avgPrice, 0) }}€</span>
                                    <span class="sales-compact">{{ $variant->listings_count }} ventes</span>
                                @else
                                    <span class="no-data-compact">Pas de données</span>
                                @endif
                            </span>
                        </a>
                        @endforeach
                    </div>

                    @php
                        $hasRanking = $console->variants->filter(fn($v) => $v->listings_count > 0)->count() >= 3;
                    @endphp
                    @if($hasRanking)
                    <div class="ranking-link-compact">
                        <a href="/{{ $console->slug }}/classement">🏆 Voir le classement</a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- 3DS Family --}}
    @if($threeDsFamily->count() > 0)
    <div class="console-family-section" id="3ds-family">
        <h2 class="family-header">🎯 Famille Nintendo 3DS</h2>
        <p class="family-description">Les consoles avec effet 3D sans lunettes (2011-2020)</p>

        <div class="console-grid">
            @foreach($threeDsFamily as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-icon">🎯</div>
                    <div class="console-info">
                        <h3 class="console-name">
                            <a href="/{{ $console->slug }}">{{ $console->name }}</a>
                        </h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')">
                        <span class="expand-icon">▶</span>
                    </button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->name }}</span>
                            <span class="variant-data-compact">
                                @if($variant->listings_count > 0)
                                    @php
                                        $avgPrice = \App\Models\Listing::where('variant_id', $variant->id)
                                            ->where('status', 'approved')
                                            ->avg('price');
                                    @endphp
                                    <span class="price-compact">{{ number_format($avgPrice, 0) }}€</span>
                                    <span class="sales-compact">{{ $variant->listings_count }} ventes</span>
                                @else
                                    <span class="no-data-compact">Pas de données</span>
                                @endif
                            </span>
                        </a>
                        @endforeach
                    </div>

                    @php
                        $hasRanking = $console->variants->filter(fn($v) => $v->listings_count > 0)->count() >= 3;
                    @endphp
                    @if($hasRanking)
                    <div class="ranking-link-compact">
                        <a href="/{{ $console->slug }}/classement">🏆 Voir le classement</a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function toggleVariants(consoleSlug) {
    const variantsDiv = document.getElementById('variants-' + consoleSlug);
    const button = document.querySelector(`[data-console-slug="${consoleSlug}"] .expand-toggle`);
    const icon = button.querySelector('.expand-icon');

    if (variantsDiv.style.display === 'none') {
        variantsDiv.style.display = 'block';
        icon.textContent = '▼';
        button.setAttribute('aria-expanded', 'true');
    } else {
        variantsDiv.style.display = 'none';
        icon.textContent = '▶';
        button.setAttribute('aria-expanded', 'false');
    }
}
</script>
@endsection
