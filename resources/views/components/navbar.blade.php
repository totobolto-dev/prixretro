{{-- Main Navigation --}}
<nav class="sticky top-0 z-50 bg-nav border-b border-white/10 backdrop-blur-sm">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="/" class="flex items-center gap-2">
                <img src="/images/prixretro-logo.png" alt="PrixRetro" class="h-10">
            </a>

            {{-- Navigation Links --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="/" class="text-text-secondary hover:text-accent-cyan transition {{ request()->is('/') ? 'text-accent-cyan' : '' }}">
                    Accueil
                </a>
                <a href="/tendances" class="text-text-secondary hover:text-accent-cyan transition {{ request()->is('tendances') ? 'text-accent-cyan' : '' }}">
                    Tendances
                </a>
                <a href="/guides" class="text-text-secondary hover:text-accent-cyan transition {{ request()->is('guides*') ? 'text-accent-cyan' : '' }}">
                    Guides
                </a>
            </div>

            {{-- Search Bar (disabled for now) --}}
            <div class="flex-1 max-w-md mx-8 hidden">
                <form action="/search" method="GET" class="relative">
                    <input
                        type="search"
                        name="q"
                        placeholder="Rechercher une console..."
                        class="w-full bg-bg-card text-text-primary px-4 py-2 pr-10 border border-white/10 focus:border-accent-cyan outline-none transition"
                    >
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>
            </div>

            {{-- Mobile Menu Toggle --}}
            <button class="md:hidden p-2 text-text-secondary hover:text-accent-cyan transition" onclick="toggleMobileMenu()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>

        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden pb-4">
            <div class="flex flex-col gap-4">
                <a href="/" class="text-text-secondary hover:text-accent-cyan transition">Accueil</a>
                <a href="/tendances" class="text-text-secondary hover:text-accent-cyan transition">Tendances</a>
                <a href="/guides" class="text-text-secondary hover:text-accent-cyan transition">Guides</a>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}
</script>
