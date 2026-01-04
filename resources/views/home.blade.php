@extends('layout')

@section('title', 'PrixRetro - Tracker de Prix Retrogaming')

@section('content')
<div class="container">
    <div class="hero">
        <h1>PrixRetro</h1>
        <p class="tagline">
            Prix du marché pour consoles retrogaming Nintendo<br>
            <span style="color: var(--text-muted); font-size: 0.95rem;">
                Basé sur {{ $consoles->sum(fn($c) => $c->variants->count()) }} variantes •
                {{ $consoles->sum(fn($c) => $c->variants->sum('listings_count')) }} ventes analysées
            </span>
        </p>
    </div>

    @foreach($consoles as $console)
    <div class="console-section">
        <div class="console-header">
            <h2><a href="/{{ $console->slug }}" style="color: inherit; text-decoration: none; transition: color 0.2s;">🎮 {{ $console->name }}</a></h2>
            <div class="console-stats">
                {{ $console->variants->count() }} variantes •
                {{ $console->variants->sum('listings_count') }} ventes
            </div>
        </div>

        @php
            $hasEnoughDataForRanking = $console->variants->filter(fn($v) => $v->listings_count > 0)->count() >= 3;
        @endphp

        @if($hasEnoughDataForRanking)
        <div class="ranking-link-section">
            <a href="/{{ $console->slug }}/classement" class="ranking-link-home">
                🏆 Voir le classement des variantes
            </a>
        </div>
        @endif

        <div class="variant-grid">
            @foreach($console->variants->sortByDesc('listings_count')->take(9) as $variant)
            <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-card">
                <div class="variant-name">{{ $variant->name }}</div>
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

        @if($console->variants->count() > 9)
        <div class="view-all">
            <a href="/{{ $console->slug }}" class="view-all-btn">
                Voir toutes les variantes ({{ $console->variants->count() }})
            </a>
        </div>
        @endif
    </div>
    @endforeach
</div>
@endsection
