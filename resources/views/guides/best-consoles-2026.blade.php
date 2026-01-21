@extends('layout')

@section('title', 'Top 10 Meilleures Consoles Retro 2026 (50-200€) | PrixRetro')

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <a href="/guides">Guides</a>
        <span>›</span>
        <span>Meilleures consoles 2026</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">Meilleures consoles retrogaming à acheter en 2026</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Publié le {{ date('j F Y') }} • Lecture 10 min
        </p>

        <p style="margin-bottom: 2rem;">
            Vous voulez vous lancer dans le retrogaming mais ne savez pas quelle console choisir ?
            Voici notre sélection des <strong>10 meilleures consoles d'occasion</strong> à acheter en 2026,
            classées par budget et type d'expérience. Tous les prix sont basés sur l'analyse de centaines
            de ventes réelles.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Budget 50-80€ : Les points d'entrée</h2>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">1. Game Boy Color</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 60-70€</p>
            <p style="margin-bottom: 0.5rem;">
                Catalogue immense (900+ jeux), compatible Game Boy originale, indestructible.
                Défaut : pas de rétroéclairage. Idéal pour découvrir les classiques Nintendo à petit prix.
            </p>
            <a href="/game-boy-color" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">2. Nintendo DS Lite</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 50-70€</p>
            <p style="margin-bottom: 0.5rem;">
                Double écran tactile, rétroéclairage, compatible GBA. Énorme catalogue (1800+ jeux DS + GBA).
                Attention aux charnières cassées (problème fréquent).
            </p>
            <a href="/nintendo-ds" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Budget 80-120€ : Le sweet spot</h2>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">3. Game Boy Advance SP</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 80-100€</p>
            <p style="margin-bottom: 0.5rem;">
                <strong>LE meilleur rapport qualité-prix du marché.</strong> Rétroéclairage, format clapet,
                batterie rechargeable, compatible GB/GBC/GBA. Presque parfaite.
            </p>
            <a href="/game-boy-advance" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">4. Nintendo 3DS</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 90-130€</p>
            <p style="margin-bottom: 0.5rem;">
                3D sans lunettes (désactivable), compatible DS, énorme ludothèque exclusive.
                Les XL sont plus confortables mais +20-30€. eShop fermé mais la bibliothèque physique suffit.
            </p>
            <a href="/nintendo-3ds" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">5. PSP (PlayStation Portable)</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 70-110€</p>
            <p style="margin-bottom: 0.5rem;">
                Écran superbe, puissance respectable, catalogue de qualité (God of War, GTA, Monster Hunter).
                Modèle 3000 recommandé (écran meilleur, plus léger).
            </p>
            <a href="/psp" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Budget 120-200€ : Premium portable</h2>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">6. PS Vita</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 120-180€</p>
            <p style="margin-bottom: 0.5rem;">
                Écran magnifique, joysticks doubles, écran tactile arrière. Parfaite pour RPG japonais et visual novels.
                <strong>Problème</strong> : Cartes mémoires propriétaires chères (16 GB = +30€).
            </p>
            <a href="/ps-vita" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Consoles de salon : Budget 80-150€</h2>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">7. PlayStation 2</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 60-90€</p>
            <p style="margin-bottom: 0.5rem;">
                La console la plus vendue de tous les temps. Catalogue absurde (4000+ jeux), rétrocompatible PS1,
                lecteur DVD. Les Slim sont plus fiables que les Fat.
            </p>
            <a href="/playstation-2" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">8. GameCube</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 80-120€</p>
            <p style="margin-bottom: 0.5rem;">
                Design iconique, exclusivités Nintendo de qualité (Smash Bros, Zelda, Metroid Prime).
                Les jeux sont chers mais la console est solide. Prévoyez câble HDMI aftermarket.
            </p>
            <a href="/gamecube" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">9. Wii</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 50-80€</p>
            <p style="margin-bottom: 0.5rem;">
                La console familiale par excellence. Catalogue énorme, rétrocompatible GameCube (premiers modèles),
                Virtual Console. Facile à trouver et pas chère.
            </p>
            <a href="/wii" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h3 style="color: var(--accent-primary); margin-bottom: 0.5rem;">10. Xbox 360</h3>
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: 60-100€</p>
            <p style="margin-bottom: 0.5rem;">
                Excellente génération pour les FPS et action games. Les Slim/E sont plus fiables (évitez les Fat avec RROD).
                Beaucoup de jeux à petit prix.
            </p>
            <a href="/xbox-360" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Notre recommandation globale</h2>

        <p style="margin-bottom: 1rem;">
            <strong>Pour débuter</strong> : Game Boy Advance SP ou Nintendo DS Lite. Budget raisonnable,
            catalogues immenses, expérience parfaite en 2026.
        </p>

        <p style="margin-bottom: 1rem;">
            <strong>Pour la collection</strong> : PS2 + GameCube. Vous couvrez 80% des classiques des années 2000.
        </p>

        <p style="margin-bottom: 2rem;">
            <strong>Budget illimité</strong> : PS Vita pour le portable, Xbox 360 pour le salon.
            Deux consoles sous-estimées avec des catalogues exceptionnels.
        </p>

        <div style="background: var(--bg-secondary); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--accent-primary); margin: 2rem 0;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">📊 Comparez les prix en temps réel</h3>
            <p style="margin-bottom: 1.5rem; color: var(--text-secondary);">
                Suivez l'évolution des prix pour toutes ces consoles sur notre tracker.
            </p>
            <a href="/" style="display: inline-block; background: var(--accent-primary); color: var(--bg-primary); padding: 0.75rem 1.5rem; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
                Voir toutes les consoles →
            </a>
        </div>
    </article>

    <div class="back-link" style="margin-top: 3rem;">
        <a href="/guides">← Retour aux guides</a>
    </div>
</div>
@endsection
