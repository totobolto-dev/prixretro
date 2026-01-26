<x-layout>
    <x-slot:title>PrixRetro - Prix des Consoles Rétro d'Occasion</x-slot:title>
    <x-slot:description>{{ $metaDescription }}</x-slot:description>

    {{-- Hero Section --}}
    <div class="bg-gradient-to-b from-bg-secondary to-bg-primary border-b border-white/10">
        <div class="container mx-auto px-4 py-8">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-3xl md:text-4xl font-bold mb-3 text-text-primary">
                    Prix du Marché des Consoles Rétro
                </h1>
                <p class="text-base text-text-secondary mb-4 leading-relaxed">
                    Suivez les prix du marché secondaire pour vos consoles retrogaming préférées.
                    Historique complet des ventes eBay, tendances de prix, et guides d'achat.
                </p>
                <div class="flex items-center justify-center gap-6 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-accent-cyan font-bold">{{ $consoles->sum(fn($c) => $c->variants->count()) }}</span>
                        <span class="text-text-muted">variantes</span>
                    </div>
                    <div class="w-1 h-4 bg-white/20"></div>
                    <div class="flex items-center gap-2">
                        <span class="text-accent-cyan font-bold">{{ number_format($consoles->sum(fn($c) => $c->variants->sum('listings_count'))) }}</span>
                        <span class="text-text-muted">ventes analysées</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 py-8">

        {{-- Popular Consoles Section --}}
        {{-- Popular Consoles Carousel --}}
        <div class="mb-12">
            <div class="flex items-center justify-between mb-4">
                <h2 class="section-heading mb-0">🔥 Consoles Populaires</h2>
                <div class="flex gap-2">
                    <button onclick="prevSlide()" class="px-3 py-1 bg-bg-card hover:bg-bg-hover border border-white/10 text-sm">&larr;</button>
                    <button onclick="nextSlide()" class="px-3 py-1 bg-bg-card hover:bg-bg-hover border border-white/10 text-sm">&rarr;</button>
                </div>
            </div>

            <div class="carousel-container overflow-hidden relative">
                <div class="carousel-track flex transition-transform duration-300 ease-in-out">
                    @foreach($popularVariants->chunk(5) as $slideIndex => $slideVariants)
                    <div class="carousel-slide flex-shrink-0 w-full">
                        <div class="grid grid-cols-5 gap-4">
                            @foreach($slideVariants as $variant)
                            <a href="/{{ $variant->full_slug }}" class="block bg-bg-card hover:bg-bg-hover transition shadow-box-list group overflow-hidden">
                                {{-- Image (top half, no padding) --}}
                                <div class="aspect-square bg-bg-darker flex items-center justify-center overflow-hidden">
                                    @if($variant->image_url)
                                        <img src="{{ $variant->image_url }}"
                                             alt="{{ $variant->display_name }}"
                                             class="w-full h-full object-contain">
                                    @else
                                        <span class="text-text-muted text-xs">Pas d'image</span>
                                    @endif
                                </div>

                                {{-- Info (bottom half) --}}
                                <div class="p-3">
                                    <h3 class="font-semibold text-sm mb-2 line-clamp-2 leading-tight group-hover:text-accent-cyan transition">
                                        {{ $variant->display_name }}
                                    </h3>

                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-base">{{ number_format($variant->avg_price, 0) }}€</span>

                                        @if($variant->trend_percentage !== null)
                                            <span class="text-xs font-medium {{ $variant->trend_direction === 'down' ? 'text-green-500' : 'text-red-500' }}">
                                                {{ $variant->trend_direction === 'down' ? '↓' : '↑' }}{{ abs($variant->trend_percentage) }}%
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <script>
            let currentSlide = 0;
            const totalSlides = {{ $popularVariants->chunk(5)->count() }};

            function updateCarousel() {
                const track = document.querySelector('.carousel-track');
                track.style.transform = `translateX(-${currentSlide * 100}%)`;
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateCarousel();
            }

            function prevSlide() {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                updateCarousel();
            }
        </script>

        {{-- Two Column Layout: Latest Sales & Price Records --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">

            {{-- Latest Sales --}}
            <div>
                <h2 class="section-heading">🕒 Dernières Ventes</h2>

                <div class="space-y-2">
                    @foreach($latestSales->take(10) as $listing)
                        <a href="/{{ $listing->variant->full_slug }}"
                           class="flex items-center gap-4 p-3 bg-bg-card hover:bg-bg-hover transition shadow-box-list group">

                            {{-- eBay Thumbnail (no padding) --}}
                            <div class="w-16 h-16 flex-shrink-0 bg-bg-darker flex items-center justify-center overflow-hidden">
                                @if($listing->thumbnail_url)
                                <img src="{{ $listing->thumbnail_url }}"
                                     alt="{{ $listing->variant->display_name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\'text-xs text-text-muted\'>Pas d\'image</span>'">
                                @else
                                    <span class="text-xs text-text-muted">Pas d'image</span>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-sm mb-1 line-clamp-1 group-hover:text-accent-cyan transition">
                                    {{ $listing->variant->display_name }}
                                </h4>
                                <div class="flex items-center gap-2 text-xs text-text-secondary">
                                    <span>{{ $listing->sold_date ? $listing->sold_date->diffForHumans() : 'Récemment' }}</span>
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="flex-shrink-0">
                                <span class="text-lg font-bold text-accent-cyan">{{ number_format($listing->price, 0) }}€</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Price Records --}}
            <div>
                <h2 class="section-heading">💰 Records de Prix</h2>

                @php
                    $allRecords = collect();
                    foreach($priceRecords as $record) {
                        foreach($record['listings'] as $listing) {
                            $allRecords->push($listing);
                        }
                    }
                    $topRecords = $allRecords->sortByDesc('price')->take(10);
                @endphp

                <div class="space-y-2">
                    @foreach($topRecords as $listing)
                        <a href="/{{ $listing->variant->full_slug }}"
                           class="flex items-center gap-4 p-3 bg-bg-card hover:bg-bg-hover transition shadow-box-list group">

                            {{-- eBay Thumbnail (no padding) --}}
                            <div class="w-16 h-16 flex-shrink-0 bg-bg-darker flex items-center justify-center overflow-hidden">
                                @if($listing->thumbnail_url)
                                <img src="{{ $listing->thumbnail_url }}"
                                     alt="{{ $listing->variant->display_name }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\'text-xs text-text-muted\'>Pas d\'image</span>'">
                                @else
                                    <span class="text-xs text-text-muted">Pas d'image</span>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-sm mb-1 line-clamp-1 group-hover:text-accent-cyan transition">
                                    {{ $listing->variant->display_name }}
                                </h4>
                                <div class="flex items-center gap-2 text-xs text-text-secondary">
                                    <span>{{ $listing->sold_date ? $listing->sold_date->diffForHumans() : 'N/A' }}</span>
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="flex-shrink-0">
                                <span class="text-lg font-bold text-accent-cyan">{{ number_format($listing->price, 0) }}€</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Featured Guides Section --}}
        @if(isset($featuredGuides) && $featuredGuides->count() > 0)
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="section-heading mb-0">📚 Guides d'Achat</h2>
                <a href="/guides" class="text-accent-cyan hover:text-accent-green transition text-sm font-semibold">
                    Tous les guides →
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($featuredGuides as $guide)
                <a href="/guides/{{ $guide['slug'] }}" class="bg-bg-card p-6 hover:bg-bg-hover transition group shadow-box-list">
                    <h3 class="text-base font-semibold mb-2 group-hover:text-accent-cyan transition">
                        {{ $guide['title'] }}
                    </h3>
                    <p class="text-sm text-text-secondary">
                        Guide complet pour acheter votre {{ $guide['console'] ?? 'console' }}
                    </p>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- All Consoles Grid --}}
        <div class="mt-16">
            <h2 class="section-heading">Toutes les Consoles</h2>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                @foreach($consoles as $console)
                <a
                    href="/{{ $console->slug }}"
                    class="bg-bg-card p-4 hover:bg-bg-hover transition group shadow-box-list"
                >
                    <h3 class="font-semibold text-sm mb-2 group-hover:text-accent-cyan transition">
                        {{ $console->name }}
                    </h3>
                    <p class="text-xs text-text-secondary">
                        {{ $console->variants->sum('listings_count') }} ventes
                    </p>
                </a>
                @endforeach
            </div>
        </div>

    </div>

</x-layout>
