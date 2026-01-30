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

        {{-- Main 3-Column Unified Block --}}
        <div class="bg-bg-card shadow-box-list overflow-hidden mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">

                {{-- Column 1: Image (3 cols) --}}
                <div class="lg:col-span-3 p-6">
                    <div class="aspect-square bg-bg-darker flex items-center justify-center overflow-hidden">
                        @if($variant->image_url)
                            <img src="{{ $variant->image_url }}"
                                 alt="{{ $variant->display_name }}"
                                 class="w-full h-full object-contain">
                        @else
                            <span class="text-text-muted text-sm">Pas d'image</span>
                        @endif
                    </div>
                </div>

                {{-- Separator Line --}}
                <div class="hidden lg:block relative w-px">
                    <div class="absolute inset-y-0 left-0 w-px bg-gradient-to-b from-transparent via-accent-cyan/20 to-transparent"></div>
                </div>

                {{-- Column 2: À Propos (6 cols) --}}
                <div class="lg:col-span-6 p-6 border-l border-white/5 lg:border-l-0">
                    <h1 class="text-3xl font-bold mb-6">{{ $variant->display_name }}</h1>

                    <div class="prose prose-invert max-w-none text-sm">
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
                           class="px-4 py-2 bg-bg-darker hover:bg-bg-hover border border-accent-cyan/30 hover:border-accent-cyan transition text-sm flex items-center gap-2">
                            <span>🏆</span> Classement des variantes
                        </a>
                        @endif

                        @if($otherVariants->count() > 0)
                        <select onchange="if(this.value) window.location.href=this.value"
                                class="px-4 py-2 bg-bg-darker border border-white/10 hover:border-accent-cyan transition text-sm cursor-pointer">
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

                {{-- Separator Line --}}
                <div class="hidden lg:block relative w-px">
                    <div class="absolute inset-y-0 left-0 w-px bg-gradient-to-b from-transparent via-accent-cyan/20 to-transparent"></div>
                </div>

                {{-- Column 3: Stats with Tabs (3 cols) --}}
                <div class="lg:col-span-3 p-6 border-t border-white/5 lg:border-t-0 lg:border-l-0">
                    @php
                        $statsByCompleteness = [];
                        if (isset($statistics) && $statistics['count'] >= 5) {
                            foreach(['loose', 'cib', 'sealed'] as $comp) {
                                $count = \App\Models\Listing::where('variant_id', $variant->id)
                                    ->where('status', 'approved')
                                    ->where('completeness', $comp)
                                    ->count();
                                if ($count >= 5) {
                                    $statsByCompleteness[$comp] = [
                                        'count' => $count,
                                        'avg' => \App\Models\Listing::where('variant_id', $variant->id)
                                            ->where('status', 'approved')
                                            ->where('completeness', $comp)
                                            ->avg('price'),
                                        'min' => \App\Models\Listing::where('variant_id', $variant->id)
                                            ->where('status', 'approved')
                                            ->where('completeness', $comp)
                                            ->min('price'),
                                        'max' => \App\Models\Listing::where('variant_id', $variant->id)
                                            ->where('status', 'approved')
                                            ->where('completeness', $comp)
                                            ->max('price'),
                                    ];
                                }
                            }
                        }
                    @endphp

                    {{-- Tabs --}}
                    @if(count($statsByCompleteness) > 0)
                    <div class="flex gap-2 mb-4 border-b border-white/10 pb-2">
                        @foreach($statsByCompleteness as $comp => $stats)
                        <button onclick="showTab('{{ $comp }}')"
                                class="tab-btn px-3 py-1 text-sm hover:text-accent-cyan transition"
                                data-tab="{{ $comp }}">
                            @if($comp === 'loose') ⚪ Loose
                            @elseif($comp === 'cib') 📦 CIB
                            @else 🔒 Sealed
                            @endif
                        </button>
                        @endforeach
                    </div>

                    {{-- Stats Content --}}
                    @foreach($statsByCompleteness as $comp => $stats)
                    <div class="tab-content {{ $loop->first ? 'block' : 'hidden' }}" data-tab="{{ $comp }}">
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-text-muted mb-1">Prix Moyen</div>
                                <div class="text-2xl font-bold text-accent-cyan">
                                    {{ number_format($stats['avg'], 0) }}€
                                </div>
                            </div>
                            <div class="border-t border-white/10 pt-3 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-muted">Prix Min</span>
                                    <span class="font-semibold">{{ number_format($stats['min'], 0) }}€</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-muted">Prix Max</span>
                                    <span class="font-semibold">{{ number_format($stats['max'], 0) }}€</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-text-muted">Ventes</span>
                                    <span class="font-semibold">{{ $stats['count'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach

                    <script>
                        function showTab(tab) {
                            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
                            document.querySelector(`.tab-content[data-tab="${tab}"]`).classList.remove('hidden');
                            document.querySelectorAll('.tab-btn').forEach(btn => {
                                if (btn.dataset.tab === tab) {
                                    btn.classList.add('text-accent-cyan', 'border-b-2', 'border-accent-cyan');
                                } else {
                                    btn.classList.remove('text-accent-cyan', 'border-b-2', 'border-accent-cyan');
                                }
                            });
                        }
                        document.addEventListener('DOMContentLoaded', () => {
                            const firstTab = document.querySelector('.tab-btn');
                            if (firstTab) showTab(firstTab.dataset.tab);
                        });
                    </script>
                    @else
                    <div class="text-center py-8 text-text-muted text-sm">
                        Pas assez de données<br>pour afficher les stats
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chart + Guide Side by Side --}}
        @if($statistics['count'] > 0)
        <div class="grid grid-cols-1 {{ $guideUrl ? 'lg:grid-cols-2' : '' }} gap-8 mb-12">

            {{-- Price Chart --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="section-heading mb-0 flex items-center gap-2">
                        📈 Évolution du Prix
                    </h2>
                    @if($priceTrend && isset($priceTrend['percentage']))
                    <div class="flex items-center gap-2 px-3 py-1 {{ $priceTrend['direction'] === 'down' ? 'bg-green-500/10 text-green-400' : 'bg-red-500/10 text-red-400' }}">
                        <span class="font-semibold">{{ $priceTrend['direction'] === 'down' ? '↓' : '↑' }} {{ abs($priceTrend['percentage']) }}%</span>
                        <span class="text-xs opacity-75">30j</span>
                    </div>
                    @endif
                </div>

                <div class="bg-bg-card p-4 shadow-box-list">
                    <canvas id="priceChart" class="w-full" style="height: 280px;"></canvas>
                </div>

                @if(isset($buyingInsight) && is_array($buyingInsight) && isset($buyingInsight['recommendation']))
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

        {{-- eBay Current Listings --}}
        @php
            $currentListings = \App\Models\CurrentListing::where('variant_id', $variant->id)
                ->where('is_sold', false)
                ->where('status', 'approved')
                ->orderBy('price', 'asc')
                ->take(5)
                ->get();
            $ebayAffiliateParams = 'mkcid=1&mkrid=709-53476-19255-0&campid=5339134703';
        @endphp

        @if($currentListings->count() > 0)
        <div class="mb-12">
            <h2 class="section-heading">🛒 Acheter sur eBay</h2>

            <div class="grid grid-cols-5 gap-3 mb-4">
                @foreach($currentListings as $listing)
                <a href="{{ $listing->url }}?{{ $ebayAffiliateParams }}"
                   target="_blank"
                   rel="nofollow noopener"
                   class="block bg-bg-card hover:bg-bg-hover transition shadow-box-list group overflow-hidden">
                    @if($listing->thumbnail_url)
                    <div class="bg-bg-darker flex items-center justify-center overflow-hidden" style="height: 150px;">
                        <img src="{{ $listing->thumbnail_url }}"
                             alt="{{ $listing->title }}"
                             class="w-full h-full object-contain">
                    </div>
                    @endif
                    <div class="p-2">
                        <div class="text-xs line-clamp-2 mb-1 group-hover:text-accent-cyan transition min-h-[2rem]">
                            {{ $listing->title }}
                        </div>
                        <div class="font-bold text-accent-cyan">
                            {{ number_format($listing->price, 0) }}€
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <a href="https://www.ebay.fr/sch/i.html?_nkw={{ urlencode($variant->console->name . ' ' . $variant->name) }}&{{ $ebayAffiliateParams }}"
               target="_blank"
               rel="nofollow noopener"
               class="block w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-500 hover:to-blue-600 text-white font-semibold text-center transition">
                Voir toutes les offres eBay →
            </a>
        </div>
        @endif

        {{-- Amazon Accessories --}}
        <div class="mb-12">
            <h2 class="section-heading">🛡️ Accessoires Amazon</h2>

            <div class="bg-bg-card p-6 shadow-box-list">
                @php
                    $isPortable = in_array($variant->console->slug, [
                        'game-boy', 'game-boy-pocket', 'game-boy-light', 'game-boy-color',
                        'game-boy-advance', 'game-boy-advance-sp', 'game-boy-micro',
                        'nintendo-ds', 'nintendo-ds-lite', 'nintendo-dsi', 'nintendo-dsi-xl',
                        'nintendo-3ds', 'nintendo-3ds-xl', 'new-nintendo-3ds', 'new-nintendo-3ds-xl',
                        'nintendo-2ds', 'new-nintendo-2ds-xl',
                        'psp', 'psp-go', 'ps-vita', 'ps-vita-slim'
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


        {{-- Sold Listings Table --}}
        @if($statistics['count'] > 0)
        <div class="mb-12">
            <h2 class="section-heading">📊 Historique des Ventes</h2>
            <div class="bg-bg-card shadow-box-list overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-white/10">
                        <tr class="text-left">
                            <th class="p-3 text-text-muted font-semibold">Date</th>
                            <th class="p-3 text-text-muted font-semibold">Prix</th>
                            <th class="p-3 text-text-muted font-semibold">État</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentListings->take(20) as $listing)
                        <tr class="border-b border-white/5 hover:bg-bg-hover transition">
                            <td class="p-3 text-text-secondary">{{ $listing->sold_date->format('d/m/Y') }}</td>
                            <td class="p-3 font-semibold text-accent-cyan">{{ number_format($listing->price, 0) }}€</td>
                            <td class="p-3 text-xs">
                                @if($listing->completeness === 'cib') <span class="px-2 py-1 bg-blue-500/20 text-blue-400">📦 CIB</span>
                                @elseif($listing->completeness === 'sealed') <span class="px-2 py-1 bg-orange-500/20 text-orange-400">🔒 Sealed</span>
                                @else <span class="px-2 py-1 bg-gray-500/20 text-gray-400">⚪ Loose</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Other Variants Grid --}}
        @if($otherVariants->count() > 0)
        <div>
            <h2 class="section-heading">🎮 Autres Variantes de {{ $variant->console->name }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($otherVariants as $other)
                <a href="/{{ $other->full_slug }}" class="block bg-bg-card hover:bg-bg-hover p-3 transition shadow-box-list group">
                    @if($other->image_url)
                    <div class="aspect-square bg-bg-darker mb-2 flex items-center justify-center overflow-hidden">
                        <img src="{{ $other->image_url }}" alt="{{ $other->name }}" class="w-full h-full object-contain">
                    </div>
                    @endif
                    <h4 class="text-sm font-semibold mb-1 line-clamp-2 group-hover:text-accent-cyan transition">{{ $other->name }}</h4>
                    <p class="text-xs text-text-muted">{{ $other->listings_count }} ventes</p>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>

    {{-- Chart.js Script --}}
    @if($statistics['count'] > 0 && isset($chartData))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('priceChart');
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    data: chartData.prices,
                    borderColor: '#00d9ff',
                    backgroundColor: 'rgba(0, 217, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#00d9ff',
                    pointBorderColor: '#0f0f1e',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    pointHoverBorderWidth: 3
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
                            window.open(url + '?mkcid=1&mkrid=709-53476-19255-0&campid=5339134703', '_blank', 'noopener,noreferrer');
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        enabled: true,
                        mode: 'nearest',
                        intersect: false,
                        backgroundColor: '#1a1a2e',
                        titleColor: '#ffffff',
                        bodyColor: '#00d9ff',
                        borderColor: '#00d9ff',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return chartData.titles ? chartData.titles[index] : '';
                            },
                            label: function(context) {
                                return context.parsed.y + '€';
                            },
                            afterLabel: function() {
                                return 'Cliquer pour voir sur eBay';
                            }
                        }
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
