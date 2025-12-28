@extends('layout')

@section('title', 'PrixRetro - Tracker de Prix Retrogaming')
@section('meta_description', 'Suivez les prix du marché de l\'occasion pour vos consoles retrogaming préférées. Game Boy Color, Game Boy Advance, Nintendo DS et plus encore.')

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
            <h2>🎮 {{ $console->name }}</h2>
            <div class="console-stats">
                {{ $console->variants->count() }} variantes •
                {{ $console->variants->sum('listings_count') }} ventes
            </div>
        </div>

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
