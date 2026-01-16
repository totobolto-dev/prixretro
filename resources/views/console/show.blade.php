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

        <div class="console-description-box">
            <p>{{ $autoDescription }}</p>
        </div>

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
            <h3 class="variant-category-title">{{ $category }}</h3>
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

    <div class="back-link">
        <a href="/">← Retour à l'accueil</a>
    </div>
</div>
@endsection
