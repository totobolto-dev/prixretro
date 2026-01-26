{{-- Settings Menu (Top Bar) --}}
<div class="bg-bg-secondary border-b border-white/10">
    <div class="container mx-auto px-4">
        <div class="flex justify-end items-center gap-6 py-2 text-sm">

            {{-- Manufacturer Filter --}}
            <div class="relative group">
                <button class="flex items-center gap-2 text-text-secondary hover:text-accent-cyan transition">
                    <span>Fabricant:</span>
                    <span class="font-semibold">Tous</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                {{-- Dropdown (hidden for now, can be activated later) --}}
                <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-bg-card shadow-lg py-2 z-[60]">
                    <a href="/?manufacturer=all" class="block px-4 py-2 hover:bg-bg-hover transition">Tous</a>
                    <a href="/?manufacturer=nintendo" class="block px-4 py-2 hover:bg-bg-hover transition">Nintendo</a>
                    <a href="/?manufacturer=sony" class="block px-4 py-2 hover:bg-bg-hover transition">Sony</a>
                    <a href="/?manufacturer=sega" class="block px-4 py-2 hover:bg-bg-hover transition">Sega</a>
                    <a href="/?manufacturer=microsoft" class="block px-4 py-2 hover:bg-bg-hover transition">Microsoft</a>
                </div>
            </div>

            {{-- Region Selector (for future use) --}}
            <div class="relative">
                <button class="flex items-center gap-2 text-text-secondary hover:text-accent-cyan transition">
                    <span>Région:</span>
                    <span class="font-semibold">France</span>
                </button>
            </div>

        </div>
    </div>
</div>
