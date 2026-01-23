@extends('layout')

@section('title', 'Guide d\'achat Nintendo NES 2026 - Quel modèle choisir | PrixRetro')

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
        <span>NES</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">Guide d'achat Nintendo NES - Quel modèle choisir en 2026</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Créé le 23 janvier 2026 • Lecture 6 min
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">💡 L'essentiel à retenir</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Prix moyen</strong>: 70-100€ pour une NES complète en bon état
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Meilleur modèle</strong>: Famicom AV (compact, sortie AV, pas de 10NES)
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Alternative</strong>: NES-001 top-loader (fiabilité supérieure)
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong>À éviter</strong>: Connecteur 72-pin usé (écran qui clignote)
                </li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Pourquoi acheter une NES en 2026 ?</h2>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            La NES est <strong>la console qui a ressuscité l'industrie du jeu vidéo</strong> après le krach de 1983. Avec 700+ jeux (Super Mario Bros., Zelda, Mega Man, Castlevania, Metroid), elle offre une <strong>ludothèque fondatrice</strong> du jeu vidéo moderne.
        </p>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Point crucial : la NES souffre d'un <strong>problème de connecteur 72-pin</strong> qui se desserre avec le temps. Symptôme : écran qui clignote, cartouche qu'il faut pousser/souffler. Solution : remplacement du connecteur (15€) ou achat d'un modèle top-loader/Famicom.
        </p>

        <div style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid #ef4444; padding: 1.5rem; border-radius: var(--radius); margin: 2rem 0;">
            <h4 style="margin-bottom: 1rem; color: #ef4444;">⚠️ Connecteur 72-pin : le point faible historique</h4>
            <p style="margin-bottom: 0;">Le connecteur des NES front-loader s'use systématiquement. Vérifiez :</p>
            <ul style="margin-top: 0.5rem; padding-left: 1.5rem;">
                <li>Lancement de cartouche sans manipulation (pas besoin de souffler/pousser)</li>
                <li>Image stable sans clignotements</li>
                <li>Connecteur de remplacement disponible (15€) mais nécessite démontage</li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Les différents modèles de NES</h2>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">NES Front-Loader (1985-1995) - Modèle "VCR"</h3>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Design gris "magnétoscope" avec trappe frontale.<br>
            <strong>Avantages</strong> : Iconique, compatible avec accessoires officiels (Zapper, Power Pad), facile à trouver.<br>
            <strong>Inconvénients</strong> : Connecteur 72-pin s'use (problème quasi-systématique), puce 10NES lockout (bloque jeux non-officiels).<br>
            <strong>Prix 2026</strong> : 70-100€<br>
            <strong>Recommandation</strong> : Acceptable si connecteur en bon état ou si vous acceptez de le remplacer (15€).
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">NES Top-Loader NES-101 (1993) - "Dogbone"</h3>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Redesign compact avec insertion cartouche par le dessus (top-loader).<br>
            <strong>Avantages</strong> : Connecteur cartouche FIABLE (pas de problème 72-pin), manette "dogbone" ergonomique, pas de 10NES lockout.<br>
            <strong>Inconvénients</strong> : Sortie RF uniquement (pas d'AV composite de série, mod requis), rare et chère en Europe.<br>
            <strong>Prix 2026</strong> : 150-250€ (USA import)<br>
            <strong>Recommandation</strong> : Meilleure console techniquement, mais prix prohibitif et mod AV nécessaire.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">Famicom (Japon, 1983-1994)</h3>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Version japonaise originale, design rouge/blanc compact.<br>
            <strong>Avantages</strong> : Prix abordable (40-70€), top-loader fiable, manettes intégrées (pas de connecteurs usés), pas de 10NES.<br>
            <strong>Inconvénients</strong> : Sortie RF uniquement (mod AV requis), manettes câblées (pas amovibles), cartouches incompatibles avec NES (adaptateur 60-pin requis).<br>
            <strong>Prix 2026</strong> : 40-70€<br>
            <strong>Recommandation</strong> : Excellent rapport qualité-prix si vous acceptez le mod AV (30-50€).
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem;">Famicom AV (Japon, 1993) - ⭐ LE MEILLEUR CHOIX</h3>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            Redesign de la Famicom avec sortie AV composite native.<br>
            <strong>Avantages</strong> : Top-loader fiable, sortie AV composite native (pas de mod), manettes amovibles, compact, pas de 10NES lockout.<br>
            <strong>Inconvénients</strong> : Rare (Japon uniquement), nécessite adaptateur 60-pin vers 72-pin pour jeux NES (20€).<br>
            <strong>Prix 2026</strong> : 80-130€<br>
            <strong>Recommandation</strong> : <strong>C'est LA console à acheter</strong> si vous trouvez. Cumule tous les avantages.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Points de vigilance avant achat</h2>

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
                    <td style="padding: 0.75rem;">Connecteur 72-pin</td>
                    <td style="padding: 0.75rem;">Insérer cartouche, lancer sans forcer (si front-loader)</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 0.75rem;">Écran stable</td>
                    <td style="padding: 0.75rem;">Pas de clignotements, image fixe</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 0.75rem;">Manettes</td>
                    <td style="padding: 0.75rem;">Tous boutons (A, B, Select, Start, D-pad)</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border);">
                    <td style="padding: 0.75rem;">Audio</td>
                    <td style="padding: 0.75rem;">Son mono clair, pas de grésillement</td>
                </tr>
                <tr>
                    <td style="padding: 0.75rem;">Alimentation</td>
                    <td style="padding: 0.75rem;">LED rouge s'allume</td>
                </tr>
            </tbody>
        </table>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Prix et meilleures offres 2026</h2>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            <strong>Fourchettes de prix actuelles</strong> (marché français) :
        </p>

        <ul style="line-height: 1.8; margin-bottom: 1.5rem; padding-left: 2rem;">
            <li><strong>NES Front-Loader complète</strong> : 70-100€</li>
            <li><strong>NES Top-Loader NES-101 (USA)</strong> : 150-250€</li>
            <li><strong>Famicom (Japon)</strong> : 40-70€</li>
            <li><strong>Famicom AV (Japon)</strong> : 80-130€</li>
            <li><strong>Manette NES</strong> : 15-25€</li>
            <li><strong>Connecteur 72-pin neuf</strong> : 10-15€</li>
            <li><strong>Adaptateur 60-pin vers 72-pin</strong> : 15-25€</li>
        </ul>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            💡 <strong>Astuce</strong> : Le marché japonais (Famicom) offre <strong>30-40% d'économies</strong> sur les consoles et 50%+ sur les jeux par rapport aux versions NES PAL.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Connectique moderne : adaptateurs HDMI</h2>

        <p style="line-height: 1.8; margin-bottom: 1.5rem;">
            La NES sort en <strong>RF</strong> ou <strong>AV composite</strong> (selon modèles). Pour la brancher en HDMI :
        </p>

        <ul style="line-height: 1.8; margin-bottom: 1.5rem; padding-left: 2rem;">
            <li><strong>Câble AV vers HDMI</strong> : Budget (10-15€, qualité moyenne)</li>
            <li><strong>RetroTINK-2X</strong> : Milieu de gamme (100€, excellent upscaling)</li>
            <li><strong>Hi-Def NES Kit</strong> : Haut de gamme (200€, HDMI natif, mod interne)</li>
            <li><strong>Analogue Nt Mini</strong> : Clone FPGA premium (500€+, qualité ultime)</li>
        </ul>

        <div style="background: var(--bg-card); border: 1px solid var(--border); padding: 1.5rem; border-radius: var(--radius); margin: 2rem 0;">
            <h4 style="margin-bottom: 1rem;">🎮 Flashcart : EverDrive N8 Pro</h4>
            <p style="margin-bottom: 0;">Les jeux NES originaux restent globalement abordables (20-50€ les titres populaires), mais les raretés explosent (500€+). L'<strong>EverDrive N8 Pro</strong> (120€) charge toute la ludothèque depuis carte SD avec compatibilité 99%. Mappers exotiques supportés.</p>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">🌍 Region Lock : NES vs Famicom</h2>

        <div style="background: #fef3c7; color: #92400e; padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
            <p style="margin-bottom: 1rem;"><strong>⚠️ NES et Famicom ont des connecteurs INCOMPATIBLES physiquement</strong></p>
            <ul style="list-style: disc; padding-left: 2rem; margin: 0;">
                <li style="margin-bottom: 0.5rem;">NES : connecteur 72-pin</li>
                <li style="margin-bottom: 0.5rem;">Famicom : connecteur 60-pin</li>
                <li style="margin-bottom: 0.5rem;">Solution : adaptateur 60-pin vers 72-pin (15-25€) ou vice-versa</li>
                <li style="margin-bottom: 0.5rem;">NES PAL a aussi une puce 10NES lockout (empêche jeux non-officiels), absente sur Famicom</li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Verdict : quel modèle acheter ?</h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin: 2rem 0;">
            <div style="background: linear-gradient(135deg, rgba(0, 255, 136, 0.1), rgba(0, 217, 255, 0.05)); border: 2px solid #00ff88; padding: 1.5rem; border-radius: var(--radius);">
                <h4 style="color: #00ff88; margin-bottom: 0.75rem;">🥇 Meilleur choix</h4>
                <p style="margin-bottom: 0.5rem;"><strong>Famicom AV</strong></p>
                <p style="font-size: 0.9rem; color: var(--text-secondary);">Top-loader, AV natif, 80-130€</p>
            </div>

            <div style="background: var(--bg-card); border: 2px solid var(--border); padding: 1.5rem; border-radius: var(--radius);">
                <h4 style="color: var(--accent-primary); margin-bottom: 0.75rem;">💰 Petit budget</h4>
                <p style="margin-bottom: 0.5rem;"><strong>Famicom + mod AV</strong></p>
                <p style="font-size: 0.9rem; color: var(--text-secondary);">Fiable, 70-120€ total</p>
            </div>

            <div style="background: var(--bg-card); border: 2px solid var(--border); padding: 1.5rem; border-radius: var(--radius);">
                <h4 style="color: var(--accent-primary); margin-bottom: 0.75rem;">🎯 Classique</h4>
                <p style="margin-bottom: 0.5rem;"><strong>NES Front-Loader</strong></p>
                <p style="font-size: 0.9rem; color: var(--text-secondary);">Iconique, 70-100€</p>
            </div>
        </div>

        @if(isset($console))
        <div style="background: var(--bg-card); border: 1px solid var(--accent-primary); padding: 1.5rem; border-radius: var(--radius); margin-top: 2rem;">
            <h3 style="margin-bottom: 1rem;">📊 Voir les prix du marché</h3>
            <p style="margin-bottom: 1rem;">Consultez les ventes réelles de <a href="/{{ $console->slug }}" style="color: var(--accent-primary); text-decoration: underline;">NES sur PrixRetro</a> pour connaître les prix moyens actuels.</p>
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
