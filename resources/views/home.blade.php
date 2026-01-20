@extends('layout')

@section('title', 'PrixRetro - Tracker de Prix Retrogaming')

@section('content')
<div class="container">
    <div class="hero">
        <img src="/images/prixretro-logo.png" alt="PrixRetro" class="hero-logo">
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
        <a href="#nintendo-home" class="family-nav-link">Nintendo (salon)</a>
        <a href="#playstation" class="family-nav-link">PlayStation</a>
        <a href="#sega" class="family-nav-link">Sega</a>
        <a href="#xbox" class="family-nav-link">Xbox</a>
        <a href="#wii" class="family-nav-link">Wii</a>
        <a href="#psp" class="family-nav-link">PSP</a>
        <a href="#vita" class="family-nav-link">PS Vita</a>
        <a href="#atari" class="family-nav-link">Atari</a>
        <a href="#neo-geo" class="family-nav-link">Neo Geo</a>
        <a href="#pc-engine" class="family-nav-link">PC Engine</a>
        <a href="#game-boy-family" class="family-nav-link">Game Boy</a>
        <a href="#ds-family" class="family-nav-link">DS</a>
        <a href="#3ds-family" class="family-nav-link">3DS</a>
        <a href="#other" class="family-nav-link">Autres</a>
    </div>

    @php
        // Group consoles by family based on display_order
        $nintendoHome = $consoles->filter(fn($c) => $c->display_order >= 10 && $c->display_order < 20);
        $playstation = $consoles->filter(fn($c) => $c->display_order >= 20 && $c->display_order < 30);
        $sega = $consoles->filter(fn($c) => $c->display_order >= 30 && $c->display_order < 40);
        $xbox = $consoles->filter(fn($c) => $c->display_order >= 40 && $c->display_order < 50);
        $wii = $consoles->filter(fn($c) => $c->display_order >= 50 && $c->display_order < 60);
        $psp = $consoles->filter(fn($c) => $c->display_order >= 60 && $c->display_order < 62);
        $vita = $consoles->filter(fn($c) => $c->display_order >= 62 && $c->display_order < 70);
        $gameGear = $consoles->filter(fn($c) => $c->display_order >= 70 && $c->display_order < 80);
        $atari = $consoles->filter(fn($c) => $c->display_order >= 80 && $c->display_order < 90);
        $neoGeo = $consoles->filter(fn($c) => $c->display_order >= 90 && $c->display_order < 100);
        $pcEngine = $consoles->filter(fn($c) => $c->display_order >= 96 && $c->display_order < 98);
        $wonderswan = $consoles->filter(fn($c) => $c->display_order >= 98 && $c->display_order < 100);
        $gameBoyFamily = $consoles->filter(fn($c) => $c->display_order >= 100 && $c->display_order < 120);
        $dsFamily = $consoles->filter(fn($c) => $c->display_order >= 120 && $c->display_order < 140);
        $threeDsFamily = $consoles->filter(fn($c) => $c->display_order >= 140 && $c->display_order < 160);
        $otherConsoles = $consoles->filter(fn($c) => $c->display_order >= 160);
    @endphp

    {{-- Nintendo Home Consoles --}}
    @if($nintendoHome->count() > 0)
    <div class="console-family-section" id="nintendo-home">
        <h2 class="family-header">Consoles Nintendo de salon</h2>
        <p class="family-description">Les consoles de salon classiques de Nintendo (1985-2002)</p>

        <div class="console-grid">
            @foreach($nintendoHome as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
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
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- PlayStation Family --}}
    @if($playstation->count() > 0)
    <div class="console-family-section" id="playstation">
        <h2 class="family-header">Famille PlayStation</h2>
        <p class="family-description">Les consoles PlayStation de Sony (1995-2006)</p>

        <div class="console-grid">
            @foreach($playstation as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
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
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- Sega Family --}}
    @if($sega->count() > 0)
    <div class="console-family-section" id="sega">
        <h2 class="family-header">Famille Sega</h2>
        <p class="family-description">Les consoles légendaires de Sega (1986-1999)</p>

        <div class="console-grid">
            @foreach($sega as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
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
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- Xbox Family --}}
    @if($xbox->count() > 0)
    <div class="console-family-section" id="xbox">
        <h2 class="family-header">Famille Xbox</h2>
        <p class="family-description">Les consoles Xbox de Microsoft (2001-2005)</p>
        <div class="console-grid">
            @foreach($xbox as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-info">
                        <h3 class="console-name"><a href="/{{ $console->slug }}">{{ $console->name }}</a></h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')"><span class="expand-icon">▶</span></button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- Wii --}}
    @if($wii->count() > 0)
    <div class="console-family-section" id="wii">
        <h2 class="family-header">Nintendo Wii</h2>
        <p class="family-description">Console révolutionnaire avec détection de mouvement (2006)</p>
        <div class="console-grid">
            @foreach($wii as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-info">
                        <h3 class="console-name"><a href="/{{ $console->slug }}">{{ $console->name }}</a></h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')"><span class="expand-icon">▶</span></button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- PSP Family --}}
    @if($psp->count() > 0)
    <div class="console-family-section" id="psp">
        <h2 class="family-header">PlayStation Portable</h2>
        <p class="family-description">Les consoles portables de Sony (2004-2009)</p>
        <div class="console-grid">
            @foreach($psp as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-info">
                        <h3 class="console-name"><a href="/{{ $console->slug }}">{{ $console->name }}</a></h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')"><span class="expand-icon">▶</span></button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- PS Vita --}}
    @if($vita->count() > 0)
    <div class="console-family-section" id="vita">
        <h2 class="family-header">PlayStation Vita</h2>
        <p class="family-description">Console portable HD de Sony (2011-2013)</p>
        <div class="console-grid">
            @foreach($vita as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-info">
                        <h3 class="console-name"><a href="/{{ $console->slug }}">{{ $console->name }}</a></h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')"><span class="expand-icon">▶</span></button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- Atari Family --}}
    @if($atari->count() > 0)
    <div class="console-family-section" id="atari">
        <h2 class="family-header">Famille Atari</h2>
        <p class="family-description">Les consoles pionnières d'Atari (1977-1993)</p>
        <div class="console-grid">
            @foreach($atari as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-info">
                        <h3 class="console-name"><a href="/{{ $console->slug }}">{{ $console->name }}</a></h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')"><span class="expand-icon">▶</span></button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- Neo Geo Family --}}
    @if($neoGeo->count() > 0)
    <div class="console-family-section" id="neo-geo">
        <h2 class="family-header">Famille Neo Geo</h2>
        <p class="family-description">Les systèmes Neo Geo de SNK (1990-1999)</p>
        <div class="console-grid">
            @foreach($neoGeo as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-info">
                        <h3 class="console-name"><a href="/{{ $console->slug }}">{{ $console->name }}</a></h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')"><span class="expand-icon">▶</span></button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- PC Engine --}}
    @if($pcEngine->count() > 0)
    <div class="console-family-section" id="pc-engine">
        <h2 class="family-header">PC Engine / TurboGrafx</h2>
        <p class="family-description">Console 16-bit de NEC (1987)</p>
        <div class="console-grid">
            @foreach($pcEngine as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-info">
                        <h3 class="console-name"><a href="/{{ $console->slug }}">{{ $console->name }}</a></h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')"><span class="expand-icon">▶</span></button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- Game Boy Family --}}
    @if($gameBoyFamily->count() > 0)
    <div class="console-family-section" id="game-boy-family">
        <h2 class="family-header">Famille Game Boy</h2>
        <p class="family-description">Les consoles portables qui ont révolutionné le jeu nomade (1989-2008)</p>

        <div class="console-grid">
            @foreach($gameBoyFamily as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
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
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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
        <h2 class="family-header">Famille Nintendo DS</h2>
        <p class="family-description">Les consoles à double écran tactile (2004-2013)</p>

        <div class="console-grid">
            @foreach($dsFamily as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
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
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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
        <h2 class="family-header">Famille Nintendo 3DS</h2>
        <p class="family-description">Les consoles avec effet 3D sans lunettes (2011-2020)</p>

        <div class="console-grid">
            @foreach($threeDsFamily as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
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
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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

    {{-- Other Consoles --}}
    @if($otherConsoles->count() > 0)
    <div class="console-family-section" id="other">
        <h2 class="family-header">Autres Consoles</h2>
        <p class="family-description">Consoles rares et collectors (1978-2000)</p>
        <div class="console-grid">
            @foreach($otherConsoles as $console)
            <div class="console-card" data-console-slug="{{ $console->slug }}">
                <div class="console-card-header">
                    <div class="console-info">
                        <h3 class="console-name"><a href="/{{ $console->slug }}">{{ $console->name }}</a></h3>
                        <div class="console-meta">
                            <span class="variant-count">{{ $console->variants->count() }} variant{{ $console->variants->count() > 1 ? 's' : 'e' }}</span>
                            <span class="sales-count">{{ $console->variants->sum('listings_count') }} ventes</span>
                        </div>
                    </div>
                    @if($console->variants->count() > 0)
                    <button class="expand-toggle" onclick="toggleVariants('{{ $console->slug }}')"><span class="expand-icon">▶</span></button>
                    @endif
                </div>

                @if($console->variants->count() > 0)
                <div class="console-variants" id="variants-{{ $console->slug }}" style="display: none;">
                    <div class="variants-table">
                        @foreach($console->variants->sortByDesc('listings_count') as $variant)
                        <a href="/{{ $console->slug }}/{{ $variant->slug }}" class="variant-row-compact">
                            <span class="variant-name-compact">{{ $variant->short_name }}</span>
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
