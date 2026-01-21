@extends('layout')

@section('title', 'Top 10 Meilleures Consoles Retro 2026 (50-200€) | PrixRetro')

@section('head')
<style>
abbr[title] {
    text-decoration: underline dotted;
    cursor: help;
    color: var(--accent-primary);
}
</style>
@endsection

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
            Publié le {{ date('j F Y') }} • Lecture 5 min
        </p>

        <p style="margin-bottom: 2rem;">
            Vous voulez vous lancer dans le retrogaming mais ne savez pas quelle console choisir ?
            Voici notre sélection des <strong>meilleures consoles d'occasion</strong> à acheter en 2026,
            classées par budget et type d'expérience. Tous les prix sont basés sur l'analyse de centaines
            de ventes réelles.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Consoles Portables</h2>

        <h3 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--accent-primary);">Budget 50-80€ : Les points d'entrée</h3>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">Game Boy Color</h4>
            @php
                $price = $consolesWithData['game-boy-color']['avg_price'] ?? 65;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                Catalogue immense (900+ jeux), compatible <abbr title="Game Boy">GB</abbr> originale, indestructible.
                Défaut : pas de <abbr title="Écran éclairé par l'arrière pour jouer dans le noir">rétroéclairage</abbr>. Idéal pour découvrir les classiques Nintendo à petit prix.
            </p>
            <a href="/game-boy-color" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">Nintendo DS Lite</h4>
            @php
                $price = $consolesWithData['nintendo-ds']['avg_price'] ?? 60;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                Double écran tactile, <abbr title="Écran éclairé par l'arrière pour jouer dans le noir">rétroéclairage</abbr>, compatible <abbr title="Game Boy Advance">GBA</abbr>. Énorme catalogue (1800+ jeux DS + <abbr title="Game Boy Advance">GBA</abbr>).
                Attention aux charnières cassées (problème fréquent).
            </p>
            <a href="/nintendo-ds" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <h3 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--accent-primary);">Budget 80-120€ : Le sweet spot</h3>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">Game Boy Advance SP</h4>
            @php
                $price = $consolesWithData['game-boy-advance']['avg_price'] ?? 90;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                <strong>LE meilleur rapport qualité-prix du marché.</strong> <abbr title="Écran éclairé par l'arrière pour jouer dans le noir">Rétroéclairage</abbr>, format clapet,
                batterie rechargeable, compatible <abbr title="Game Boy">GB</abbr>/<abbr title="Game Boy Color">GBC</abbr>/<abbr title="Game Boy Advance">GBA</abbr>. Presque parfaite.
            </p>
            <a href="/game-boy-advance" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">Nintendo 3DS</h4>
            @php
                $price = $consolesWithData['nintendo-3ds']['avg_price'] ?? 110;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                3D sans lunettes (désactivable), compatible DS, énorme bibliothèque exclusive.
                Les XL sont plus confortables mais +20-30€. <abbr title="Boutique en ligne Nintendo (fermée)">eShop</abbr> fermé mais la bibliothèque physique suffit.
            </p>
            <a href="/nintendo-3ds" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">PSP (PlayStation Portable)</h4>
            @php
                $price = $consolesWithData['psp']['avg_price'] ?? 90;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                Écran superbe, puissance respectable, catalogue de qualité (God of War, GTA, Monster Hunter).
                Modèle 3000 recommandé (écran meilleur, plus léger).
            </p>
            <a href="/psp" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <h3 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--accent-primary);">Budget 120-200€ : Premium portable</h3>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">PS Vita</h4>
            @php
                $price = $consolesWithData['ps-vita']['avg_price'] ?? 150;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                Écran magnifique, joysticks doubles, écran tactile arrière. Parfaite pour <abbr title="Jeux de rôle">RPG</abbr> japonais et <abbr title="Romans visuels interactifs">visual novels</abbr>.
                <strong>Problème</strong> : Cartes mémoires propriétaires chères (16 GB = +30€).
            </p>
            <a href="/ps-vita" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1.5rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Consoles de Salon</h2>

        <h3 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--accent-primary);">Budget 50-90€</h3>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">PlayStation 2</h4>
            @php
                $price = $consolesWithData['playstation-2']['avg_price'] ?? 75;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                La console la plus vendue de tous les temps. Catalogue absurde (4000+ jeux), rétrocompatible PS1,
                lecteur DVD. Les Slim sont plus fiables que les <abbr title="Modèle original plus volumineux">Fat</abbr>.
            </p>
            <a href="/playstation-2" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">Wii</h4>
            @php
                $price = $consolesWithData['wii']['avg_price'] ?? 65;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                La console familiale par excellence. Catalogue énorme, rétrocompatible GameCube (premiers modèles),
                <abbr title="Service de téléchargement de jeux rétro Nintendo">Virtual Console</abbr>. Facile à trouver et pas chère.
            </p>
            <a href="/wii" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <h3 style="margin-top: 2rem; margin-bottom: 1rem; color: var(--accent-primary);">Budget 80-150€</h3>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">GameCube</h4>
            @php
                $price = $consolesWithData['gamecube']['avg_price'] ?? 100;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                Design iconique, exclusivités Nintendo de qualité (Smash Bros, Zelda, Metroid Prime).
                Les jeux sont chers mais la console est solide. Prévoyez câble HDMI non-officiel.
            </p>
            <a href="/gamecube" style="color: var(--accent-primary); text-decoration: none;">Voir les prix →</a>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 1.5rem;">
            <h4 style="color: var(--accent-primary); margin-bottom: 0.5rem;">Xbox 360</h4>
            @php
                $price = $consolesWithData['xbox-360']['avg_price'] ?? 80;
            @endphp
            <p style="color: var(--text-secondary); margin-bottom: 0.5rem;"><strong>Prix moyen</strong>: {{ $price }}€</p>
            <p style="margin-bottom: 0.5rem;">
                Excellente génération pour les <abbr title="Jeux de tir à la première personne">FPS</abbr> et action games. Les Slim/E sont plus fiables (évitez les <abbr title="Modèle original plus volumineux">Fat</abbr> avec <abbr title="Red Ring of Death - Panne fréquente Xbox 360">RROD</abbr>).
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
