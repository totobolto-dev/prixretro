<x-layout
    :title="$variant->display_name . (isset($statistics['count']) && $statistics['count'] > 0 ? ' - Prix (' . number_format($statistics['avg_price'], 0) . '€)' : '') . ' | PrixRetro'"
    :description="$metaDescription ?? 'Prix et historique du ' . $variant->display_name">

    {{-- Breadcrumb --}}
    <div class="container mx-auto px-4 py-4">
        <div class="flex items-center gap-2 text-sm text-text-muted">
            <a href="/" class="hover:text-accent-cyan transition">Accueil</a>
            <span>›</span>
            <a href="/{{ $variant->console->slug }}" class="hover:text-accent-cyan transition">{{ $variant->console->name }}</a>
            <span>›</span>
            <span class="text-text-primary">{{ $variant->name }}</span>
        </div>
    </div>

    <div class="container mx-auto px-4 pb-12">

        {{-- Compact Trust Banner --}}
        @if($statistics['count'] > 0)
        <div class="bg-bg-card border border-accent-cyan/20 px-4 py-2 mb-6 flex items-center gap-3">
            <span class="text-accent-cyan text-lg">💰</span>
            <p class="text-sm text-text-secondary">
                <strong class="text-text-primary">{{ $statistics['count'] }} ventes analysées</strong> sur eBay France pour vous aider à éviter de payer trop cher
            </p>
        </div>
        @endif

        {{-- Main 3-Column Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">

            {{-- Column 1: Image (3 cols) --}}
            <div class="lg:col-span-3">
                <div class="aspect-square bg-bg-card flex items-center justify-center overflow-hidden shadow-box-list">
                    @if($variant->image_url)
                        <img src="{{ $variant->image_url }}"
                             alt="{{ $variant->display_name }}"
                             class="w-full h-full object-contain">
                    @else
                        <span class="text-text-muted text-sm">Pas d'image</span>
                    @endif
                </div>
            </div>

            {{-- Column 2: À Propos (6 cols) --}}
            <div class="lg:col-span-6">
                <h1 class="text-3xl font-bold mb-6">{{ $variant->display_name }}</h1>

                <div class="prose prose-invert max-w-none">
                    {!! $autoDescription ?? '' !!}
                </div>

                {{-- Navigation --}}
                @php
                    $otherVariants = $variant->console->variants()
                        ->where('id', '!=', $variant->id)
                        ->withCount('listings')
                        ->orderBy('name')
                        ->get();

                    $hasRanking = $variant->console->variants()
                        ->whereHas('listings', function($q) {
                            $q->where('status', 'approved');
                        })
                        ->count() >= 3;
                @endphp

                @if($hasRanking || $otherVariants->count() > 0)
                <div class="mt-6 flex flex-wrap gap-3">
                    @if($hasRanking)
                    <a href="/{{ $variant->console->slug }}/classement"
                       class="px-4 py-2 bg-bg-card hover:bg-bg-hover border border-accent-cyan/30 hover:border-accent-cyan transition text-sm flex items-center gap-2">
                        <span>🏆</span> Classement des variantes
                    </a>
                    @endif

                    @if($otherVariants->count() > 0)
                    <select onchange="if(this.value) window.location.href=this.value"
                            class="px-4 py-2 bg-bg-card border border-white/10 hover:border-accent-cyan transition text-sm cursor-pointer">
                        <option value="">{{ $variant->name }} (actuelle)</option>
                        @foreach($otherVariants as $other)
                        <option value="/{{ $other->full_slug }}">
                            {{ $other->name }} ({{ $other->listings_count }} ventes)
                        </option>
                        @endforeach
                    </select>
                    @endif
                </div>
                @endif
            </div>

            {{-- Column 3: Stats with Tabs (3 cols) --}}
            <div class="lg:col-span-3">
                @php
                    $statsByCompleteness = [];
                    if (isset($statistics) && $statistics['count'] >= 5) {
                        $statsByCompleteness = [
                            'loose' => [
                                'count' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'loose')
                                    ->count(),
                                'avg' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'loose')
                                    ->avg('price'),
                                'min' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'loose')
                                    ->min('price'),
                                'max' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'loose')
                                    ->max('price'),
                            ],
                            'cib' => [
                                'count' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'cib')
                                    ->count(),
                                'avg' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'cib')
                                    ->avg('price'),
                                'min' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'cib')
                                    ->min('price'),
                                'max' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'cib')
                                    ->max('price'),
                            ],
                            'sealed' => [
                                'count' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'sealed')
                                    ->count(),
                                'avg' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'sealed')
                                    ->avg('price'),
                                'min' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'sealed')
                                    ->min('price'),
                                'max' => \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', 'sealed')
                                    ->max('price'),
                            ],
                        ];
                    }

                    // Default to loose if no specific data
                    $defaultStats = $statistics['count'] >= 5 && $statsByCompleteness['loose']['count'] >= 5
                        ? $statsByCompleteness['loose']
                        : $statistics;
                @endphp

                <div class="bg-bg-card p-4 shadow-box-list">
                    {{-- Tabs --}}
                    @if($statistics['count'] >= 5 && ($statsByCompleteness['loose']['count'] >= 5 || $statsByCompleteness['cib']['count'] >= 5 || $statsByCompleteness['sealed']['count'] >= 5))
                    <div class="flex gap-2 mb-4 border-b border-white/10 pb-2">
                        @if($statsByCompleteness['loose']['count'] >= 5)
                        <button onclick="showTab('loose')"
                                class="tab-btn px-3 py-1 text-sm hover:text-accent-cyan transition"
                                data-tab="loose">
                            ⚪ Loose
                        </button>
                        @endif
                        @if($statsByCompleteness['cib']['count'] >= 5)
                        <button onclick="showTab('cib')"
                                class="tab-btn px-3 py-1 text-sm hover:text-accent-cyan transition"
                                data-tab="cib">
                            📦 CIB
                        </button>
                        @endif
                        @if($statsByCompleteness['sealed']['count'] >= 5)
                        <button onclick="showTab('sealed')"
                                class="tab-btn px-3 py-1 text-sm hover:text-accent-cyan transition"
                                data-tab="sealed">
                            🔒 Sealed
                        </button>
                        @endif
                    </div>
                    @endif

                    {{-- Stats --}}
                    <div class="space-y-4">
                        @if($statistics['count'] >= 5)
                            @foreach(['loose', 'cib', 'sealed'] as $completeness)
                                @if($statsByCompleteness[$completeness]['count'] >= 5)
                                <div class="tab-content {{ $loop->first ? 'block' : 'hidden' }}" data-tab="{{ $completeness }}">
                                    <div class="space-y-3">
                                        <div>
                                            <div class="text-xs text-text-muted mb-1">Prix Moyen</div>
                                            <div class="text-2xl font-bold text-accent-cyan">
                                                {{ number_format($statsByCompleteness[$completeness]['avg'], 0) }}€
                                            </div>
                                        </div>

                                        <div class="border-t border-white/10 pt-3 space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-text-muted">Prix Min</span>
                                                <span class="font-semibold">{{ number_format($statsByCompleteness[$completeness]['min'], 0) }}€</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-text-muted">Prix Max</span>
                                                <span class="font-semibold">{{ number_format($statsByCompleteness[$completeness]['max'], 0) }}€</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-text-muted">Ventes</span>
                                                <span class="font-semibold">{{ $statsByCompleteness[$completeness]['count'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        @else
                            {{-- Not enough data --}}
                            <div class="text-center py-8 text-text-muted text-sm">
                                Pas assez de données<br>pour afficher les stats
                            </div>
                        @endif
                    </div>
                </div>

                <script>
                    function showTab(tab) {
                        // Hide all tab contents
                        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                        // Show selected tab
                        document.querySelector(`.tab-content[data-tab="${tab}"]`).classList.remove('hidden');
                        // Update button styles
                        document.querySelectorAll('.tab-btn').forEach(btn => {
                            if (btn.dataset.tab === tab) {
                                btn.classList.add('text-accent-cyan', 'border-b-2', 'border-accent-cyan');
                            } else {
                                btn.classList.remove('text-accent-cyan', 'border-b-2', 'border-accent-cyan');
                            }
                        });
                    }
                    // Activate first tab on load
                    document.addEventListener('DOMContentLoaded', () => {
                        const firstTab = document.querySelector('.tab-btn');
                        if (firstTab) showTab(firstTab.dataset.tab);
                    });
                </script>
            </div>
        </div>

        {{-- Chart + Guide Side by Side --}}
        @if($statistics['count'] > 0)
        <div class="grid grid-cols-1 {{ $guideUrl ? 'lg:grid-cols-2' : '' }} gap-8 mb-12">

            {{-- Price Chart --}}
            <div>
                <h2 class="section-heading flex items-center gap-2">
                    📈 Évolution du Prix
                </h2>

                @if($priceTrend && isset($priceTrend['percentage']))
                <div class="mb-4 flex items-center gap-3 text-sm">
                    <span class="text-text-muted">30 derniers jours:</span>
                    <span class="font-semibold {{ $priceTrend['direction'] === 'down' ? 'text-green-500' : 'text-red-500' }}">
                        {{ $priceTrend['direction'] === 'down' ? '↓' : '↑' }} {{ abs($priceTrend['percentage']) }}%
                    </span>
                </div>
                @endif

                <div class="bg-bg-card p-4 shadow-box-list">
                    <canvas id="priceChart" class="w-full" style="height: 280px;"></canvas>
                </div>

                @if($buyingInsight && $buyingInsight['recommendation'])
                <div class="mt-4 p-3 {{ $buyingInsight['is_good_time'] ? 'bg-green-500/10 border-green-500/30' : 'bg-orange-500/10 border-orange-500/30' }} border">
                    <p class="text-sm {{ $buyingInsight['is_good_time'] ? 'text-green-400' : 'text-orange-400' }}">
                        💡 {{ $buyingInsight['recommendation'] }}
                    </p>
                </div>
                @endif
            </div>

            {{-- Buying Guide --}}
            @if($guideUrl)
            <div>
                <h2 class="section-heading">📚 Guide d'Achat</h2>

                <div class="bg-bg-card p-6 shadow-box-list">
                    <h3 class="text-lg font-bold mb-3">Comment acheter ce modèle ?</h3>
                    <p class="text-sm text-text-secondary mb-4">
                        Consultez notre guide complet avec conseils d'achat, points de vigilance et bonnes pratiques.
                    </p>
                    <a href="{{ $guideUrl }}"
                       class="inline-block px-6 py-3 bg-accent-cyan hover:bg-accent-cyan/90 text-bg-primary font-semibold transition">
                        Lire le guide →
                    </a>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- eBay + Amazon Side by Side --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">

            {{-- eBay Current Listings --}}
            @php
                $currentListings = \App\Models\CurrentListing::where('variant_id', $variant->id)
                    ->where('is_sold', false)
                    ->orderBy('price', 'asc')
                    ->take(6)
                    ->get();
                $ebayAffiliateParams = 'mkcid=1&mkrid=709-53476-19255-0&campid=5339134703';
            @endphp

            <div>
                <h2 class="section-heading">🛒 Annonces eBay</h2>

                @if($currentListings->count() > 0)
                <div class="space-y-2">
                    @foreach($currentListings as $listing)
                    <a href="{{ $listing->url }}?{{ $ebayAffiliateParams }}"
                       target="_blank"
                       rel="nofollow noopener"
                       class="flex items-center justify-between p-3 bg-bg-card hover:bg-bg-hover transition shadow-box-list group">
                        <span class="text-sm flex-1 line-clamp-1 group-hover:text-accent-cyan transition">
                            {{ $listing->title }}
                        </span>
                        <span class="font-bold text-accent-cyan ml-4">
                            {{ number_format($listing->price, 0) }}€
                        </span>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="bg-bg-card p-6 text-center text-text-muted shadow-box-list">
                    Aucune annonce disponible actuellement
                </div>
                @endif
            </div>

            {{-- Amazon Accessories --}}
            <div>
                <h2 class="section-heading">🛡️ Accessoires Recommandés</h2>

                <div class="bg-bg-card p-6 shadow-box-list">
                    @php
                        $isPortable = in_array($variant->console->slug, [
                            'game-boy', 'game-boy-pocket', 'game-boy-light', 'game-boy-color',
                            'game-boy-advance', 'game-boy-advance-sp', 'game-boy-micro',
                            'nintendo-ds', 'nintendo-ds-lite', 'nintendo-dsi', 'nintendo-dsi-xl',
                            'nintendo-3ds', 'nintendo-3ds-xl', 'new-nintendo-3ds', 'new-nintendo-3ds-xl',
                            'nintendo-2ds', 'new-nintendo-2ds-xl',
                            'psp', 'psp-go', 'ps-vita', 'ps-vita-slim',
                            'game-gear', 'atari-lynx', 'neo-geo-pocket', 'neo-geo-pocket-color',
                            'wonderswan', 'wonderswan-color', 'virtual-boy'
                        ]);
                    @endphp

                    @if($isPortable)
                        <h3 class="font-bold mb-2">Housse de Protection</h3>
                        <p class="text-sm text-text-secondary mb-4">
                            Protégez votre console lors de vos déplacements
                        </p>
                    @else
                        <h3 class="font-bold mb-2">Adaptateur HDMI</h3>
                        <p class="text-sm text-text-secondary mb-4">
                            Branchez votre console sur TV moderne
                        </p>
                    @endif

                    <a href="https://www.amazon.fr/s?tag=prixretro-21&keywords={{ urlencode($variant->console->name . ' ' . ($isPortable ? 'housse protection' : 'adaptateur HDMI')) }}"
                       target="_blank"
                       rel="nofollow noopener sponsored"
                       class="inline-block w-full text-center px-6 py-3 bg-accent-cyan hover:bg-accent-cyan/90 text-bg-primary font-semibold transition">
                        Voir sur Amazon →
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Chart.js Script --}}
    @if($statistics['count'] > 0 && isset($chartData))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('priceChart');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartData['labels']),
                datasets: [{
                    label: 'Prix (€)',
                    data: @json($chartData['prices']),
                    borderColor: '#00d9ff',
                    backgroundColor: 'rgba(0, 217, 255, 0.1)',
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a1a2e',
                        titleColor: '#fff',
                        bodyColor: '#00d9ff',
                        borderColor: '#00d9ff',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: value => value + '€',
                            color: '#9ca3af'
                        },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    },
                    x: {
                        ticks: { color: '#9ca3af' },
                        grid: { color: 'rgba(255, 255, 255, 0.05)' }
                    }
                }
            }
        });
    </script>
    @endif

</x-layout>
