@extends('layout')

@section('title', 'Guide d\'achat Super Nintendo (SNES) 2026 - Quel modèle choisir | PrixRetro')

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
        <span>Super Nintendo</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">Guide d'achat Super Nintendo - Quel modèle choisir en 2026</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Créé le 23 janvier 2026 • Lecture 6 min
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">💡 L'essentiel à retenir</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Prix moyen</strong>: 80-120€ pour une SNES complète en bon état
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Meilleur modèle</strong>: SNES-CPU-01 ou SNES-CPU-02 (excellente fiabilité)
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Alternative compacte</strong>: Super Famicom Junior (Japon, region-lock facilement contournable)
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong>À éviter</strong>: Plastique très jauni, connecteur port manette desserré
                </li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Pourquoi acheter une Super Nintendo en 2026 ?</h2>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            La Super Nintendo est <strong>considérée par beaucoup comme la meilleure console 16-bit</strong> de l'histoire. Avec 1700+ jeux (dont Chrono Trigger, Super Metroid, Zelda: A Link to the Past, Super Mario World), la SNES offre une <strong>ludothèque légendaire</strong> impossible à égaler.
        </p>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Point crucial : la SNES est <strong>très fiable mécaniquement</strong>. Pas de lecteur optique à surveiller comme sur PS1/PS2. Le seul point faible est le <strong>jaunissement du plastique</strong> avec le temps (bromure de retardateur de flamme).
        </p>

        <div style="background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 217, 255, 0.1)); border-left: 4px solid #00ff88; padding: 1.5rem; border-radius: var(--radius); margin: 2rem 0;">
            <h4 style="margin-bottom: 1rem; color: var(--accent-primary);">🎯 Super Famicom : alternative japonaise économique</h4>
            <p style="margin-bottom: 0.5rem;">La version japonaise (Super Famicom) se trouve à <strong>40-70€</strong> contre 80-120€ pour la PAL. Les consoles sont souvent en <strong>excellent état</strong> et facilement déblocables (cartouche adaptateur 15€ ou modification plastique).</p>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Les différents modèles de Super Nintendo</h2>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">SNES européenne (1992-1998)</h3>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Design gris/violet, switchs éjecteur cartouche gris.<br>
            <strong>Avantages</strong> : Robuste, puce RGB de série (meilleure qualité d'image que NTSC composite), facile à réparer.<br>
            <strong>Inconvénients</strong> : Plastique jaunit avec le temps, 50Hz bridé (jeux 17% plus lents qu'en NTSC).<br>
            <strong>Prix 2026</strong> : 80-120€<br>
            <strong>Recommandation</strong> : Privilégiez les modèles SNES-CPU-01 ou SNES-CPU-02 (révisions les plus fiables).
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">Super Famicom (Japon, 1990-1998)</h3>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Design gris/violet identique à l'Europe mais switchs éjecteur violet/rouge.<br>
            <strong>Avantages</strong> : 60Hz natif (vitesse jeu correcte), prix 30-40% moins cher que PAL, état général supérieur.<br>
            <strong>Inconvénients</strong> : Region-lock physique (découpage plastique requis ou cartouche adaptateur).<br>
            <strong>Prix 2026</strong> : 40-70€<br>
            <strong>Recommandation</strong> : Excellent rapport qualité-prix si vous acceptez le déblocage.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">Super Famicom Junior (Japon, 1998)</h3>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Redesign compact de fin de vie.<br>
            <strong>Avantages</strong> : Design élégant, légère, pas de jaunissement (plastique différent).<br>
            <strong>Inconvénients</strong> : Pas de sortie S-Video (seulement composite), pas de port extension (pas d'accès aux fonctions spéciales de certains jeux).<br>
            <strong>Prix 2026</strong> : 60-90€<br>
            <strong>Recommandation</strong> : Pour les minimalistes acceptant la perte de qualité d'image.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">SNS-101 (USA, 1997) - "SNES Jr"</h3>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Version compacte américaine (équivalent du Super Famicom Junior).<br>
            <strong>Avantages</strong> : Design violet unique, 60Hz, pas de jaunissement.<br>
            <strong>Inconvénients</strong> : Rareté en Europe, pas de S-Video, pas de port extension.<br>
            <strong>Prix 2026</strong> : 80-150€ (import USA)<br>
            <strong>Recommandation</strong> : Pour collectionneurs uniquement (prix élevé pour les compromis).
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Comment identifier le modèle exact ?</h2>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Le <strong>numéro de modèle</strong> est gravé sous la console :
        </p>

        <ul style="line-height: 1.8; margin-bottom: 1.5rem; padding-left: 2rem;">
            <li><strong>SNSP-001</strong> : SNES européenne classique (meilleure révision)</li>
            <li><strong>SHVC-001</strong> : Super Famicom japonaise classique</li>
            <li><strong>SHVC-101</strong> : Super Famicom Junior (compact)</li>
            <li><strong>SNS-101</strong> : SNES Jr américaine (compact, violet)</li>
        </ul>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Points de vigilance avant achat</h2>

        <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; padding: 1.5rem; border-radius: var(--radius); margin: 2rem 0;">
            <h4 style="margin-bottom: 1rem; color: #ef4444;">⚠️ Jaunissement : le problème esthétique de la SNES</h4>
            <p style="margin-bottom: 0;">Le plastique ABS de la SNES jaunit avec le temps (UV + chaleur). Solutions :</p>
            <ul style="margin-top: 0.5rem; padding-left: 1.5rem;">
                <li>Accepter le jaunissement (n'affecte pas les performances)</li>
                <li>Retr0bright (blanchiment peroxyde, 4-8h de travail)</li>
                <li>Retro-painting (peinture spécialisée, 50-100€ service)</li>
                <li>Acheter une Super Famicom Junior (plastique ne jaunit pas)</li>
            </ul>
        </div>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            <strong>Checklist complète</strong> :
        </p>

        <table style="width: 100%; border-collapse: collapse; margin: 1.5rem 0;">
            <thead>
                <tr style="background: var(--bg-darker);">
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border);">Point à tester</th>
                    <th style="padding: 0.75rem; text-align: left; border-bottom: 2px solid var(--border);">Comment vérifier</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 0.75rem;">Connecteur cartouche</td>
                    <td style="padding: 0.75rem;">Insérer/retirer cartouche 3x, lancer jeu</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 0.75rem;">Manettes</td>
                    <td style="padding: 0.75rem;">Tous boutons + D-pad (8 directions)</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 0.75rem;">Audio/Vidéo</td>
                    <td style="padding: 0.75rem;">Son stéréo, image nette sans lignes</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 0.75rem;">Bouton Reset</td>
                    <td style="padding: 0.75rem;">Appuyer pendant une partie</td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem;">Jaunissement</td>
                    <td style="padding: 0.75rem;">Vérifier intensité (esthétique uniquement)</td>
                </tr>
            </tbody>
        </table>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Prix et meilleures offres 2026</h2>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            <strong>Fourchettes de prix actuelles</strong> (marché français) :
        </p>

        <ul style="line-height: 1.8; margin-bottom: 1.5rem; padding-left: 2rem;">
            <li><strong>Console seule</strong> : 60-90€</li>
            <li><strong>Console + câbles + 1 manette</strong> : 80-120€</li>
            <li><strong>Pack complet (boîte + manuels)</strong> : 180-300€</li>
            <li><strong>Super Famicom (import Japon)</strong> : 40-70€</li>
            <li><strong>Super Famicom Junior</strong> : 60-90€</li>
            <li><strong>SNES Jr (USA)</strong> : 80-150€</li>
            <li><strong>Manette officielle</strong> : 20-35€</li>
        </ul>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Connectique moderne : adaptateurs HDMI</h2>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            La SNES sort en <strong>RGB SCART</strong> (Europe) ou <strong>composite/S-Video</strong> (Japon/USA). Pour la brancher en HDMI :
        </p>

        <ul style="line-height: 1.8; margin-bottom: 1.5rem; padding-left: 2rem;">
            <li><strong>Câble SCART vers HDMI</strong> : Budget (15-25€, qualité correcte pour RGB)</li>
            <li><strong>RetroTINK-2X Pro</strong> : Milieu de gamme (100€, excellent upscaling)</li>
            <li><strong>RetroTINK-5X Pro</strong> : Haut de gamme (350€, upscaling professionnel)</li>
            <li><strong>OSSC (Open Source Scan Converter)</strong> : Alternative (150€, zero-lag)</li>
        </ul>

        <div style="background: var(--bg-card); border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius); margin: 2rem 0;">
            <h4 style="margin-bottom: 1rem;">🎮 Flashcart : SD2SNES / FXPak Pro</h4>
            <p style="margin-bottom: 0;">Les jeux SNES originaux coûtent <strong>50-200€ les titres recherchés</strong>. Le FXPak Pro (180€) charge tous les jeux depuis carte SD avec compatibilité 99%. Alternative : Everdrive SNES (120€, compatibilité 95%). Ces flashcarts préservent votre console (pas d'usure du connecteur cartouche).</p>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">🌍 Region Lock : SNES vs Super Famicom</h2>

        <div style="background: #fef3c7; color: #92400e; padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
            <p style="margin-bottom: 1rem;"><strong>⚠️ La SNES a un region-lock PHYSIQUE (forme des cartouches)</strong></p>
            <ul style="list-style: disc; padding-left: 2rem; margin: 0;">
                <li style="margin-bottom: 0.5rem;">Les cartouches japonaises ne rentrent pas dans une console PAL (et vice-versa)</li>
                <li style="margin-bottom: 0.5rem;">Solution 1 : Cartouche adaptateur (15-25€, plug-and-play)</li>
                <li style="margin-bottom: 0.5rem;">Solution 2 : Découpage plastique dans le slot cartouche (irréversible, gratuit)</li>
                <li style="margin-bottom: 0.5rem;">Électroniquement : NTSC et PAL sont compatibles (pas de lock régional logiciel)</li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Verdict : quel modèle acheter ?</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin: 2rem 0;">
            <div style="background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 217, 255, 0.05)); border: 2px solid #00ff88; padding: 1.5rem; border-radius: var(--radius);">
                <h4 style="color: #00ff88; margin-bottom: 0.75rem;">🥇 Meilleur choix</h4>
                <p style="margin-bottom: 0.5rem;"><strong>SNES PAL SNSP-001</strong></p>
                <p style="font-size: 0.9rem; color: var(--text-secondary);">RGB natif, fiable, 80-120€</p>
            </div>

            <div style="background: var(--bg-card); border: 2px solid var(--border); padding: 1.5rem; border-radius: var(--radius);">
                <h4 style="color: var(--accent-primary); margin-bottom: 0.75rem;">💰 Petit budget</h4>
                <p style="margin-bottom: 0.5rem;"><strong>Super Famicom SHVC-001</strong></p>
                <p style="font-size: 0.9rem; color: var(--text-secondary);">Import Japon, 60Hz, 40-70€</p>
            </div>

            <div style="background: var(--bg-card); border: 2px solid var(--border); padding: 1.5rem; border-radius: var(--radius);">
                <h4 style="color: var(--accent-primary); margin-bottom: 0.75rem;">🎯 Compact</h4>
                <p style="margin-bottom: 0.5rem;"><strong>Super Famicom Junior</strong></p>
                <p style="font-size: 0.9rem; color: var(--text-secondary);">Pas de jaunissement, 60-90€</p>
            </div>
        </div>

        @if(isset($console))
        <div style="background: var(--bg-card); border: 1px solid var(--accent-primary); padding: 1.5rem; border-radius: var(--radius); margin-top: 2rem;">
            <h3 style="margin-bottom: 1rem;">📊 Voir les prix du marché</h3>
            <p style="margin-bottom: 1rem;">Consultez les ventes réelles de <a href="/{{ $console->slug }}" style="color: var(--accent-primary); text-decoration: underline;">Super Nintendo sur PrixRetro</a> pour connaître les prix moyens actuels.</p>
        </div>
        @endif
    </article>

    <div class="back-link" style="margin-top: 3rem;">
        <a href="/guides">← Retour aux guides</a>
    </div>
</div>
@endsection

@section('scripts')
@if(isset($faqSchema))
<!-- Schema.org FAQ Structured Data -->
<script type="application/ld+json">
@json($faqSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
</script>
@endif
@endsection
