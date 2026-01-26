@props(['listing'])

{{-- Compact Deal Card --}}
<a
    href="/{{ $listing->variant->full_slug }}"
    class="flex items-center gap-4 p-3 bg-bg-card rounded-lg hover:bg-bg-hover transition group"
>
    {{-- Thumbnail --}}
    <div class="w-20 h-20 flex-shrink-0 bg-bg-darker rounded overflow-hidden">
        @if($listing->variant->image)
        <img
            src="{{ $listing->variant->image }}"
            alt="{{ $listing->variant->display_name }}"
            class="w-full h-full object-cover"
            loading="lazy"
        >
        @else
        <div class="w-full h-full flex items-center justify-center text-text-muted">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        @endif
    </div>

    {{-- Info --}}
    <div class="flex-1 min-w-0">
        <h4 class="font-semibold text-sm mb-1 line-clamp-1 group-hover:text-accent-cyan transition">
            {{ $listing->variant->display_name }}
        </h4>
        <div class="flex items-center gap-3 text-xs text-text-secondary">
            <span>{{ $listing->sold_date ? $listing->sold_date->diffForHumans() : 'Récemment' }}</span>
            <span>eBay France</span>
        </div>
    </div>

    {{-- Price --}}
    <div class="flex items-center gap-2 flex-shrink-0">
        @php
            $minPrice = $listing->variant->listings()->where('status', 'approved')->min('price');
            $isHistoricalLow = $listing->price && $minPrice && abs($listing->price - $minPrice) < 1;
        @endphp

        @if($isHistoricalLow)
        <span class="badge-hl">HL</span>
        @endif

        <span class="text-xl font-bold text-accent-green">{{ number_format($listing->price, 0) }}€</span>
    </div>

</a>
