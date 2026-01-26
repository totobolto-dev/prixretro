@props(['selected' => 'all'])

{{-- Platform Filter Pills --}}
<div class="flex flex-wrap gap-3 py-6">

    <a
        href="/?platform=all"
        class="px-6 py-2 rounded-full font-semibold transition {{ $selected === 'all' ? 'bg-accent-cyan text-bg-primary' : 'border border-white/20 text-text-secondary hover:border-accent-cyan hover:text-accent-cyan' }}"
    >
        Toutes
    </a>

    <a
        href="/?platform=nintendo"
        class="px-6 py-2 rounded-full font-semibold transition {{ $selected === 'nintendo' ? 'bg-accent-cyan text-bg-primary' : 'border border-white/20 text-text-secondary hover:border-accent-cyan hover:text-accent-cyan' }}"
    >
        Nintendo
    </a>

    <a
        href="/?platform=sony"
        class="px-6 py-2 rounded-full font-semibold transition {{ $selected === 'sony' ? 'bg-accent-cyan text-bg-primary' : 'border border-white/20 text-text-secondary hover:border-accent-cyan hover:text-accent-cyan' }}"
    >
        Sony
    </a>

    <a
        href="/?platform=sega"
        class="px-6 py-2 rounded-full font-semibold transition {{ $selected === 'sega' ? 'bg-accent-cyan text-bg-primary' : 'border border-white/20 text-text-secondary hover:border-accent-cyan hover:text-accent-cyan' }}"
    >
        Sega
    </a>

    <a
        href="/?platform=microsoft"
        class="px-6 py-2 rounded-full font-semibold transition {{ $selected === 'microsoft' ? 'bg-accent-cyan text-bg-primary' : 'border border-white/20 text-text-secondary hover:border-accent-cyan hover:text-accent-cyan' }}"
    >
        Microsoft
    </a>

</div>
