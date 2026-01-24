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
        // Get current eBay listings for urgency banner
        $urgentListings = \App\Models\CurrentListing::where('variant_id', $variant->id)
            ->where('is_sold', false)
            ->orderBy('price', 'asc')
            ->take(3)
            ->get();
        $ebayAffiliateParams = 'mkcid=1&mkrid=709-53476-19255-0&campid=5339134703';
    @endphp

    @if($urgentListings->count() > 0)
    <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; padding: 1rem 1.5rem; border-radius: var(--radius); margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem;">
            <span style="font-size: 1.5rem;">⚡</span>
            <div style="font-weight: 700; font-size: 1.1rem;">{{ $urgentListings->count() }} {{ $urgentListings->count() > 1 ? 'offres disponibles' : 'offre disponible' }} maintenant sur eBay</div>
        </div>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @foreach($urgentListings as $listing)
            <a href="{{ $listing->url }}?{{ $ebayAffiliateParams }}"
               target="_blank"
               rel="nofollow noopener"
               onclick="trackEbayClick('urgent-{{ $variant->slug }}')"
               style="display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0.75rem; background: rgba(255, 255, 255, 0.15); border-radius: calc(var(--radius) / 2); text-decoration: none; color: white; transition: background 0.2s;"
               onmouseover="this.style.background='rgba(255, 255, 255, 0.25)'"
               onmouseout="this.style.background='rgba(255, 255, 255, 0.15)'">
                <span style="font-size: 0.9rem; flex: 1;">{{ Str::limit($listing->title, 50) }}</span>
                <span style="font-weight: 700; white-space: nowrap; margin-left: 1rem; font-size: 1.1rem;">{{ number_format($listing->price, 0) }}€ →</span>
            </a>
            @endforeach
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

    @if(isset($guideUrl))
    <div style="margin: 1.5rem 0; padding: 1rem; background: var(--bg-card); border: 1px solid var(--accent-primary); border-radius: var(--radius);">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="font-size: 1.5rem;">📖</span>
            <div style="flex: 1;">
                <div style="font-weight: 600; margin-bottom: 0.25rem;">Guide d'achat {{ $variant->console->name }}</div>
                <div style="font-size: 0.9rem; color: var(--text-secondary);">Comment choisir sa variante, éviter les arnaques, et trouver les meilleures offres</div>
            </div>
            <a href="{{ $guideUrl }}" style="background: var(--accent-primary); color: var(--bg-primary); padding: 0.5rem 1rem; border-radius: var(--radius); text-decoration: none; font-weight: 600; white-space: nowrap;">
                Lire le guide →
            </a>
        </div>
    </div>
    @endif

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

        {{-- Condition-Based Pricing --}}
        @if(!empty($statsByCompleteness))
        <div style="margin: 2rem 0; background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border-color); padding: 1.5rem;">
            <h2 style="margin: 0 0 1rem 0;">💎 Prix par État</h2>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.95rem;">
                Les prix varient considérablement selon l'état de la console. Voici les moyennes observées :
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                @if(isset($statsByCompleteness['loose']))
                <div style="background: var(--bg-darker); border-radius: var(--radius); padding: 1.25rem; border-left: 4px solid #64748b;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <span style="font-size: 1.5rem;">⚪</span>
                        <div>
                            <div style="font-weight: 600; font-size: 1.1rem;">Loose</div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">Console seule</div>
                        </div>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.5rem;">
                        {{ number_format($statsByCompleteness['loose']['avg_price'], 0) }}€
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary);">
                        {{ $statsByCompleteness['loose']['count'] }} ventes •
                        {{ number_format($statsByCompleteness['loose']['min_price'], 0) }}-{{ number_format($statsByCompleteness['loose']['max_price'], 0) }}€
                    </div>
                </div>
                @endif

                @if(isset($statsByCompleteness['cib']))
                <div style="background: var(--bg-darker); border-radius: var(--radius); padding: 1.25rem; border-left: 4px solid #3b82f6;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <span style="font-size: 1.5rem;">📦</span>
                        <div>
                            <div style="font-weight: 600; font-size: 1.1rem;">CIB</div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">Complet en boîte</div>
                        </div>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.5rem;">
                        {{ number_format($statsByCompleteness['cib']['avg_price'], 0) }}€
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary);">
                        {{ $statsByCompleteness['cib']['count'] }} ventes •
                        {{ number_format($statsByCompleteness['cib']['min_price'], 0) }}-{{ number_format($statsByCompleteness['cib']['max_price'], 0) }}€
                    </div>
                </div>
                @endif

                @if(isset($statsByCompleteness['sealed']))
                <div style="background: var(--bg-darker); border-radius: var(--radius); padding: 1.25rem; border-left: 4px solid #f59e0b;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <span style="font-size: 1.5rem;">🔒</span>
                        <div>
                            <div style="font-weight: 600; font-size: 1.1rem;">Sealed</div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary);">Neuf scellé</div>
                        </div>
                    </div>
                    <div style="font-size: 2rem; font-weight: 700; color: var(--accent-primary); margin-bottom: 0.5rem;">
                        {{ number_format($statsByCompleteness['sealed']['avg_price'], 0) }}€
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary);">
                        {{ $statsByCompleteness['sealed']['count'] }} ventes •
                        {{ number_format($statsByCompleteness['sealed']['min_price'], 0) }}-{{ number_format($statsByCompleteness['sealed']['max_price'], 0) }}€
                    </div>
                </div>
                @endif
            </div>

            @php
                $priceMultiplier = null;
                if (isset($statsByCompleteness['loose']) && isset($statsByCompleteness['cib'])) {
                    $priceMultiplier = round($statsByCompleteness['cib']['avg_price'] / $statsByCompleteness['loose']['avg_price'], 1);
                }
            @endphp

            @if($priceMultiplier && $priceMultiplier > 1)
            <div style="margin-top: 1rem; padding: 1rem; background: var(--bg-primary); border-radius: var(--radius); font-size: 0.9rem;">
                <strong>💡 À savoir :</strong> Une version CIB vaut en moyenne <strong>{{ $priceMultiplier }}x plus</strong> qu'une version loose.
            </div>
            @endif
        </div>
        @endif

        {{-- Price Chart (moved higher) --}}
        <div class="chart-container" style="margin: 2rem 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h2 style="margin: 0;">📈 Évolution du Prix</h2>
                @if($priceTrend)
                <div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: {{ $priceTrend['direction'] === 'up' ? '#fee2e2' : '#d1fae5' }}; border-radius: var(--radius);">
                    <span style="font-size: 1.2rem;">{{ $priceTrend['direction'] === 'up' ? '↑' : '↓' }}</span>
                    <span style="font-weight: 600; color: {{ $priceTrend['direction'] === 'up' ? '#dc2626' : '#059669' }};">
                        {{ $priceTrend['percentage'] > 0 ? '+' : '' }}{{ $priceTrend['percentage'] }}%
                    </span>
                    <span style="font-size: 0.85rem; color: var(--text-secondary);">sur 30 jours</span>
                </div>
                @endif
            </div>

            @if($buyingInsight)
            <div style="padding: 1rem; background: var(--bg-card); border-left: 3px solid var(--accent-primary); margin-bottom: 1rem; border-radius: var(--radius);">
                <strong>💡 Conseil d'achat :</strong> {{ $buyingInsight }}
            </div>
            @endif

            @if($priceTrend && $priceTrend['direction'] === 'up' && $priceTrend['percentage'] > 10)
            <div style="padding: 1rem; background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%); color: white; border-radius: var(--radius); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.5rem;">🔥</span>
                <div>
                    <div style="font-weight: 700; margin-bottom: 0.25rem;">Prix en hausse de {{ $priceTrend['percentage'] }}% ce mois</div>
                    <div style="font-size: 0.9rem; opacity: 0.9;">Les prix augmentent. Acheter maintenant peut vous faire économiser.</div>
                </div>
            </div>
            @endif

            <canvas id="priceChart"></canvas>
        </div>

        {{-- Side-by-Side: eBay + Amazon Monetization --}}
        <div style="margin: 3rem 0;">
            <h2 style="text-align: center; margin-bottom: 2rem; font-size: 1.75rem;">Où acheter {{ $variant->display_name }} ?</h2>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;" class="side-by-side-monetization">
                {{-- Left: eBay Current Listings --}}
                <div style="background: var(--bg-card); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <span style="font-size: 2rem;">🛒</span>
                        <div>
                            <h3 style="margin: 0; font-size: 1.25rem;">eBay - Consoles d'occasion</h3>
                            <p style="margin: 0; color: var(--text-secondary); font-size: 0.85rem;">Prix réels du marché</p>
                        </div>
                    </div>

                    @if($currentListings->count() > 0)
                        <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1rem;">
                            @foreach($currentListings->take(3) as $listing)
                            <a href="{{ $listing->url }}?{{ $ebayAffiliateParams }}"
                               target="_blank"
                               rel="nofollow noopener"
                               onclick="trackEbayClick('current-{{ $variant->slug }}')"
                               style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--bg); border-radius: var(--radius); text-decoration: none; border: 1px solid var(--border); transition: border-color 0.2s;">
                                <span style="color: var(--text-primary); font-size: 0.9rem; flex: 1;">{{ Str::limit($listing->title, 60) }}</span>
                                <span style="color: var(--accent-primary); font-weight: 700; white-space: nowrap; margin-left: 1rem;">{{ number_format($listing->price, 0) }}€</span>
                            </a>
                            @endforeach
                        </div>
                        <a href="https://www.ebay.fr/sch/i.html?_nkw={{ urlencode($variant->console->name . ' ' . $variant->name) }}&{{ $ebayAffiliateParams }}"
                           target="_blank"
                           rel="nofollow noopener"
                           onclick="trackEbayClick('search-{{ $variant->slug }}')"
                           style="display: block; width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; text-align: center; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
                            Voir toutes les offres eBay →
                        </a>
                    @else
                        <p style="color: var(--text-secondary); margin-bottom: 1rem;">Aucune annonce active actuellement</p>
                        <a href="https://www.ebay.fr/sch/i.html?_nkw={{ urlencode($variant->console->name . ' ' . $variant->name) }}&{{ $ebayAffiliateParams }}"
                           target="_blank"
                           rel="nofollow noopener"
                           onclick="trackEbayClick('search-{{ $variant->slug }}')"
                           style="display: block; width: 100%; padding: 0.75rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; text-align: center; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
                            Rechercher sur eBay →
                        </a>
                    @endif
                </div>

                {{-- Right: Amazon Accessories --}}
                <div style="background: var(--bg-card); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--border);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <span style="font-size: 2rem;">📦</span>
                        <div>
                            <h3 style="margin: 0; font-size: 1.25rem;">Amazon - Accessoires neufs</h3>
                            <p style="margin: 0; color: var(--text-secondary); font-size: 0.85rem;">Protection et câbles</p>
                        </div>
                    </div>

                    @php
                        // Determine accessories based on console type
                        $accessories = [];
                        $consoleSlug = $variant->console->slug;

                        // Portable consoles - Protection cases
                        if (str_starts_with($consoleSlug, 'game-boy') ||
                            str_starts_with($consoleSlug, 'nintendo-ds') ||
                            str_starts_with($consoleSlug, 'nintendo-3ds') ||
                            str_starts_with($consoleSlug, 'psp') ||
                            str_starts_with($consoleSlug, 'ps-vita')) {
                            $accessories[] = [
                                'name' => 'Housse de protection rigide',
                                'price' => '12-15€',
                                'url' => 'https://www.amazon.fr/s?k=housse+protection+' . urlencode($variant->console->name) . '&tag=prixretro-21',
                            ];
                        }

                        // Home consoles - HDMI adapters
                        if (in_array($consoleSlug, ['playstation-1', 'playstation-2', 'nintendo-64', 'gamecube', 'super-nintendo', 'mega-drive', 'sega-saturn', 'dreamcast', 'master-system', 'nes'])) {
                            $accessories[] = [
                                'name' => 'Adaptateur HDMI',
                                'price' => '15-25€',
                                'url' => 'https://www.amazon.fr/s?k=adaptateur+hdmi+' . urlencode($variant->console->name) . '&tag=prixretro-21',
                            ];
                        }

                        // Memory cards
                        if (in_array($consoleSlug, ['playstation-2'])) {
                            $accessories[] = [
                                'name' => 'Carte mémoire 8MB',
                                'price' => '8-12€',
                                'url' => 'https://www.amazon.fr/s?k=carte+memoire+ps2&tag=prixretro-21',
                            ];
                        }

                        if (in_array($consoleSlug, ['gamecube'])) {
                            $accessories[] = [
                                'name' => 'Carte mémoire 128MB',
                                'price' => '8-12€',
                                'url' => 'https://www.amazon.fr/s?k=carte+memoire+gamecube&tag=prixretro-21',
                            ];
                        }
                    @endphp

                    @if(count($accessories) > 0)
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            @foreach($accessories as $accessory)
                            <a href="{{ $accessory['url'] }}"
                               target="_blank"
                               rel="nofollow noopener sponsored"
                               onclick="trackAmazonClick('accessory-{{ $variant->slug }}')"
                               style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--bg); border-radius: var(--radius); text-decoration: none; border: 1px solid var(--border); transition: border-color 0.2s;">
                                <span style="color: var(--text-primary); font-size: 0.9rem; flex: 1;">{{ $accessory['name'] }}</span>
                                <span style="color: #f59e0b; font-weight: 700; white-space: nowrap; margin-left: 1rem;">{{ $accessory['price'] }}</span>
                            </a>
                            @endforeach
                        </div>
                        <p style="margin-top: 1rem; margin-bottom: 0; color: var(--text-secondary); font-size: 0.75rem; text-align: center;">
                            Lien affilié • Commission sans surcoût pour vous
                        </p>
                    @else
                        <p style="color: var(--text-secondary);">Aucun accessoire recommandé pour cette console</p>
                    @endif
                </div>
            </div>
        </div>


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
                    <div class="listing-condition-compact">
                        @if($listing->completeness)
                            @if($listing->completeness === 'loose')⚪ Loose
                            @elseif($listing->completeness === 'cib')📦 CIB
                            @elseif($listing->completeness === 'sealed')🔒 Sealed
                            @endif
                        @else{{ $listing->item_condition ?? 'N/A' }}
                        @endif
                    </div>
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
@if($schemaData)
<!-- Schema.org Product Structured Data -->
<script type="application/ld+json">
@json($schemaData['product'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
</script>

<!-- Schema.org BreadcrumbList -->
<script type="application/ld+json">
@json($schemaData['breadcrumb'], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
</script>
@endif

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
