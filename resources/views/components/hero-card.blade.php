@props(['variant', 'rank' => null])

{{-- Hero Card for Homepage Carousel --}}
<a
    href="/{{ $variant->full_slug }}"
    class="block relative bg-bg-card rounded-lg overflow-hidden shadow-box-list hoverable-box group"
>
    {{-- Rank Badge --}}
    @if($rank)
    <div class="absolute top-3 left-3 z-10 bg-black/80 backdrop-blur-sm text-white px-3 py-1 rounded-full text-sm font-bold">
        #{{ $rank }}
    </div>
    @endif

    {{-- Image --}}
    <div class="aspect-[3/4] overflow-hidden bg-bg-darker">
        @if($variant->image)
        <img
            src="{{ $variant->image }}"
            alt="{{ $variant->display_name }}"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
        >
        @else
        <div class="w-full h-full flex items-center justify-center text-text-muted">
            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="p-4">
        <h3 class="font-semibold text-lg mb-2 line-clamp-2">{{ $variant->display_name }}</h3>

        @php
            $avgPrice = $variant->listings()->where('status', 'approved')->avg('price');
            $minPrice = $variant->listings()->where('status', 'approved')->min('price');
        @endphp

        <div class="flex items-center gap-2">
            <span class="text-xs text-text-secondary">à partir de:</span>
            @if($minPrice)
            <span class="text-2xl font-bold text-accent-green">{{ number_format($minPrice, 0) }}€</span>
            <span class="badge-hl">HL</span>
            @else
            <span class="text-sm text-text-muted">Prix non disponible</span>
            @endif
        </div>
    </div>

</a>
