{{-- Footer --}}
<footer class="bg-bg-darker border-t border-white/10 mt-20">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

            {{-- About --}}
            <div>
                <h3 class="text-lg font-bold bg-gradient-to-r from-accent-cyan to-accent-green bg-clip-text text-transparent mb-4">
                    PrixRetro
                </h3>
                <p class="text-text-secondary text-sm">
                    Comparez les prix des consoles rétro d'occasion et trouvez les meilleures offres sur eBay France.
                </p>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-text-primary font-semibold mb-4">Liens Rapides</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/" class="text-text-secondary hover:text-accent-cyan transition">Accueil</a></li>
                    <li><a href="/deals" class="text-text-secondary hover:text-accent-cyan transition">Deals</a></li>
                    <li><a href="/tendances" class="text-text-secondary hover:text-accent-cyan transition">Tendances</a></li>
                    <li><a href="/guides" class="text-text-secondary hover:text-accent-cyan transition">Guides</a></li>
                </ul>
            </div>

            {{-- Consoles Populaires --}}
            <div>
                <h4 class="text-text-primary font-semibold mb-4">Consoles Populaires</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/game-boy-color" class="text-text-secondary hover:text-accent-cyan transition">Game Boy Color</a></li>
                    <li><a href="/playstation-2" class="text-text-secondary hover:text-accent-cyan transition">PlayStation 2</a></li>
                    <li><a href="/nintendo-64" class="text-text-secondary hover:text-accent-cyan transition">Nintendo 64</a></li>
                    <li><a href="/sega-dreamcast" class="text-text-secondary hover:text-accent-cyan transition">Dreamcast</a></li>
                </ul>
            </div>

            {{-- Legal --}}
            <div>
                <h4 class="text-text-primary font-semibold mb-4">Légal</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/mentions-legales" class="text-text-secondary hover:text-accent-cyan transition">Mentions Légales</a></li>
                    <li><a href="/politique-confidentialite" class="text-text-secondary hover:text-accent-cyan transition">Confidentialité</a></li>
                    <li><a href="/contact" class="text-text-secondary hover:text-accent-cyan transition">Contact</a></li>
                </ul>
            </div>

        </div>

        <div class="mt-8 pt-8 border-t border-white/10 text-center text-sm text-text-secondary">
            <p>&copy; {{ date('Y') }} PrixRetro. Tous droits réservés.</p>
            <p class="mt-2 text-xs">
                Prix et données extraits d'eBay France. PrixRetro n'est pas affilié à eBay.
            </p>
        </div>
    </div>
</footer>
