@extends('layout')

@section('title')
Estimer la valeur de sa collection retrogaming - Guide 2026 | PrixRetro
@endsection

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <a href="/guides">Guides</a>
        <span>›</span>
        <span>Estimer sa collection</span>
    </div>

    <h1>Comment estimer la valeur de sa collection retrogaming</h1>

    <div style="background: var(--bg-card); border-left: 4px solid var(--accent-primary); padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
        <strong>📌 Méthode rapide</strong>
        <p style="margin-top: 0.5rem;">La valeur d'une collection retro = <strong>Somme (Prix marché × Coefficient état)</strong>. Utilisez <a href="/ma-collection" style="color: var(--accent-primary);">notre tracker de collection</a> pour calcul automatique basé sur ventes eBay réelles. États : <strong>Loose</strong> (×1.0), <strong>CIB</strong> (×1.5-2.5), <strong>Sealed</strong> (×3-5). Variante rare (édition limitée) = +50 à +300%. Condition physique parfaite vs usée = ±30%.</p>
    </div>

    <h2>🎯 Les 3 facteurs de valeur</h2>

    <h3>1. État de conservation (Impact : ×1 à ×5)</h3>

    <table style="width: 100%; border-collapse: collapse; margin: 2rem 0;">
        <thead style="background: var(--bg-darker);">
            <tr>
                <th style="padding: 1rem; text-align: left; border: 1px solid var(--border-color);">État</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid var(--border-color);">Description</th>
                <th style="padding: 1rem; text-align: center; border: 1px solid var(--border-color);">Multiplicateur</th>
            </tr>
        </thead>
        <tbody style="background: var(--bg-card);">
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Loose</strong></td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Console seule, sans boîte ni accessoires</td>
                <td style="padding: 1rem; text-align: center; border: 1px solid var(--border-color);">×1.0</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>CIB (Complete In Box)</strong></td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Boîte + notices + câbles d'origine</td>
                <td style="padding: 1rem; text-align: center; border: 1px solid var(--border-color);">×1.5 à ×2.5</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Sealed (Neuf scellé)</strong></td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Jamais ouvert, blister d'origine intact</td>
                <td style="padding: 1rem; text-align: center; border: 1px solid var(--border-color);">×3 à ×5</td>
            </tr>
        </tbody>
    </table>

    <div style="background: var(--bg-card); padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
        <p><strong>Exemple :</strong> <a href="/game-boy-color/atomic-purple" style="color: var(--accent-primary);">Game Boy Color Atomic Purple</a></p>
        <ul>
            <li>Loose (usée) : ~50€</li>
            <li>CIB (bon état) : ~120€ (×2.4)</li>
            <li>Sealed : ~350€ (×7)</li>
        </ul>
    </div>

    <h3>2. Rareté de la variante (Impact : +0% à +300%)</h3>

    <ul>
        <li><strong>Variantes standard (noire, blanche) :</strong> Prix de base</li>
        <li><strong>Couleurs populaires (bleue, rouge) :</strong> +10 à +30%</li>
        <li><strong>Éditions limitées régionales :</strong> +50 à +100%</li>
        <li><strong>Éditions collector (Pokémon, Zelda) :</strong> +100 à +300%</li>
        <li><strong>Prototypes/Press kits :</strong> +500 à +2000% (marché niche)</li>
    </ul>

    <h3>3. Condition physique (Impact : ±30%)</h3>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin: 2rem 0;">
        <div style="background: #d1fae5; color: #065f46; padding: 1.5rem; border-radius: var(--radius);">
            <h4 style="margin-top: 0;">✅ État MINT (+20 à +30%)</h4>
            <ul style="margin: 0; padding-left: 1.5rem;">
                <li>Aucune rayure visible</li>
                <li>Plastique non jauni</li>
                <li>Autocollants intacts</li>
                <li>Fonctionnement parfait</li>
            </ul>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border-color);">
            <h4 style="margin-top: 0;">👍 État BON (±0%)</h4>
            <ul style="margin: 0; padding-left: 1.5rem;">
                <li>Micro-rayures normales</li>
                <li>Léger jaunissement</li>
                <li>Autocollants partiels</li>
                <li>Fonctionne 100%</li>
            </ul>
        </div>

        <div style="background: #fee2e2; color: #991b1b; padding: 1.5rem; border-radius: var(--radius);">
            <h4 style="margin-top: 0;">❌ État USAGÉ (-20 à -30%)</h4>
            <ul style="margin: 0; padding-left: 1.5rem;">
                <li>Rayures profondes</li>
                <li>Jaunissement prononcé</li>
                <li>Autocollants absents</li>
                <li>Défauts mineurs (boutons)</li>
            </ul>
        </div>
    </div>

    <h2>📊 Méthodologie d'estimation</h2>

    <h3>Méthode 1 : Tracker PrixRetro (Recommandé)</h3>

    <ol>
        <li>Créer compte gratuit sur <a href="/ma-collection" style="color: var(--accent-primary);">PrixRetro Collection Tracker</a></li>
        <li>Ajouter chaque console à votre collection</li>
        <li>Renseigner état (Loose/CIB/Sealed) + prix d'achat si connu</li>
        <li><strong>Calcul automatique :</strong> Valeur actuelle basée sur ventes eBay récentes</li>
        <li>Suivi évolution : Profit/perte en temps réel</li>
    </ol>

    <div style="text-align: center; margin: 2rem 0;">
        <a href="/ma-collection" style="display: inline-block; background: var(--accent-primary); color: white; padding: 1rem 2rem; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
            🎮 Créer mon tracker de collection gratuit
        </a>
    </div>

    <h3>Méthode 2 : Estimation manuelle</h3>

    <div style="background: var(--bg-card); padding: 2rem; margin: 2rem 0; border-radius: var(--radius); font-family: monospace;">
        <p style="font-weight: 700; margin-bottom: 1rem;">Formule :</p>
        <p style="background: var(--bg-darker); padding: 1rem; border-radius: var(--radius);">
            <strong>Valeur</strong> = Prix Marché × Coeff État × Coeff Rareté × Coeff Condition
        </p>

        <p style="margin-top: 2rem; font-weight: 700;">Exemple : Game Boy Advance SP Flame Red CIB</p>
        <ul style="margin-top: 0.5rem;">
            <li>Prix marché loose : 60€</li>
            <li>× 2.0 (CIB)</li>
            <li>× 1.2 (coloris recherché)</li>
            <li>× 1.1 (état MINT)</li>
            <li>= <strong>158€</strong></li>
        </ul>
    </div>

    <h2>🔍 Sources de prix fiables</h2>

    <ol>
        <li><strong><a href="/" style="color: var(--accent-primary);">PrixRetro</a> :</strong> Ventes eBay France analysées (notre site)</li>
        <li><strong>eBay "Objets vendus" :</strong> Filtrer par état, regarder derniers 30 jours</li>
        <li><strong>PriceCharting (international) :</strong> US/JAP, convertir en euros (×0.92 environ)</li>
        <li><strong>Leboncoin :</strong> Prix demandés (souvent 10-20% au-dessus marché)</li>
        <li><strong>Groupes Facebook collectionneurs :</strong> Estimations communautaires</li>
    </ol>

    <div style="background: #fee2e2; color: #991b1b; padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
        <strong>⚠️ Sources à éviter :</strong>
        <ul style="margin-top: 0.5rem;">
            <li>Amazon (prix neutre/revendeurs = 2-3x prix réel)</li>
            <li>Vinted (sous-évaluations fréquentes)</li>
            <li>Forums retrogaming (estimations biaisées par nostalgie)</li>
            <li>Boutiques physiques (marge commerciale +30-50%)</li>
        </ul>
    </div>

    <h2>💰 Cas pratiques d'estimation</h2>

    <h3>Collection Nintendo portable (15 consoles)</h3>

    <table style="width: 100%; border-collapse: collapse; margin: 2rem 0; font-size: 0.9rem;">
        <thead style="background: var(--bg-darker);">
            <tr>
                <th style="padding: 0.75rem; text-align: left; border: 1px solid var(--border-color);">Console</th>
                <th style="padding: 0.75rem; text-align: center; border: 1px solid var(--border-color);">État</th>
                <th style="padding: 0.75rem; text-align: right; border: 1px solid var(--border-color);">Valeur</th>
            </tr>
        </thead>
        <tbody style="background: var(--bg-card);">
            <tr>
                <td style="padding: 0.75rem; border: 1px solid var(--border-color);">5× Game Boy DMG (loose)</td>
                <td style="padding: 0.75rem; text-align: center; border: 1px solid var(--border-color);">Bon</td>
                <td style="padding: 0.75rem; text-align: right; border: 1px solid var(--border-color);">5 × 35€ = 175€</td>
            </tr>
            <tr>
                <td style="padding: 0.75rem; border: 1px solid var(--border-color);">3× Game Boy Color (loose, dont 1 Atomic Purple)</td>
                <td style="padding: 0.75rem; text-align: center; border: 1px solid var(--border-color);">Bon</td>
                <td style="padding: 0.75rem; text-align: right; border: 1px solid var(--border-color);">2 × 40€ + 1 × 55€ = 135€</td>
            </tr>
            <tr>
                <td style="padding: 0.75rem; border: 1px solid var(--border-color);">4× Game Boy Advance (loose)</td>
                <td style="padding: 0.75rem; text-align: center; border: 1px solid var(--border-color);">Bon</td>
                <td style="padding: 0.75rem; text-align: right; border: 1px solid var(--border-color);">4 × 45€ = 180€</td>
            </tr>
            <tr>
                <td style="padding: 0.75rem; border: 1px solid var(--border-color);">2× GBA SP (CIB)</td>
                <td style="padding: 0.75rem; text-align: center; border: 1px solid var(--border-color);">MINT</td>
                <td style="padding: 0.75rem; text-align: right; border: 1px solid var(--border-color);">2 × 140€ = 280€</td>
            </tr>
            <tr>
                <td style="padding: 0.75rem; border: 1px solid var(--border-color);">1× Game Boy Micro (CIB, Famicom Edition)</td>
                <td style="padding: 0.75rem; text-align: center; border: 1px solid var(--border-color);">MINT</td>
                <td style="padding: 0.75rem; text-align: right; border: 1px solid var(--border-color);">350€</td>
            </tr>
            <tr style="background: var(--bg-darker); font-weight: 700;">
                <td colspan="2" style="padding: 0.75rem; border: 1px solid var(--border-color);">TOTAL COLLECTION</td>
                <td style="padding: 0.75rem; text-align: right; border: 1px solid var(--border-color);">1 120€</td>
            </tr>
        </tbody>
    </table>

    <h2>📈 Évolution de valeur (2020-2026)</h2>

    <div style="background: var(--bg-card); padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
        <p><strong>Consoles ayant le plus progressé (loose) :</strong></p>
        <ul>
            <li><strong>Game Boy Advance SP :</strong> 25€ (2020) → 70€ (2026) = <span style="color: #10b981; font-weight: 700;">+180%</span></li>
            <li><strong>Game Boy Micro :</strong> 80€ (2020) → 200€ (2026) = <span style="color: #10b981; font-weight: 700;">+150%</span></li>
            <li><strong>Nintendo DS Lite (Zelda) :</strong> 60€ (2020) → 140€ (2026) = <span style="color: #10b981; font-weight: 700;">+133%</span></li>
            <li><strong>PS Vita Slim :</strong> 80€ (2020) → 150€ (2026) = <span style="color: #10b981; font-weight: 700;">+88%</span></li>
        </ul>

        <p style="margin-top: 1.5rem;"><strong>Consoles stables :</strong></p>
        <ul>
            <li><strong>Game Boy DMG :</strong> 30-35€ (stable)</li>
            <li><strong>PSP 3000 :</strong> 60-70€ (stable)</li>
            <li><strong>Nintendo DS Phat :</strong> 25-30€ (stable)</li>
        </ul>
    </div>

    <h2>🎯 Maximiser la valeur de revente</h2>

    <h3>Avant de vendre</h3>

    <ul>
        <li><strong>Nettoyage complet :</strong> Alcool isopropylique 90%, coton-tiges, Magic Eraser pour plastique</li>
        <li><strong>Retrobrighting (jaunissement) :</strong> Peroxyde d'hydrogène 12% + UV 6-8h (guide séparé)</li>
        <li><strong>Micro-réparations :</strong> Boutons qui collent (démonter, nettoyer membranes)</li>
        <li><strong>Photos professionnelles :</strong> Fond neutre, lumière naturelle, 8-10 angles différents</li>
        <li><strong>Description exhaustive :</strong> Défauts mentionnés = confiance acheteur</li>
    </ul>

    <h3>Timing de vente</h3>

    <ul>
        <li><strong>Novembre-Décembre :</strong> +15-25% (cadeaux Noël)</li>
        <li><strong>Juin-Août :</strong> -10% (été = demande baisse)</li>
        <li><strong>Après annonces Nintendo :</strong> +10-30% temporaire (hype rétro)</li>
        <li><strong>Fin de mois :</strong> Éviter (acheteurs fauchés)</li>
    </ul>

    <h3>Plateformes de vente (commissions)</h3>

    <table style="width: 100%; border-collapse: collapse; margin: 2rem 0;">
        <thead style="background: var(--bg-darker);">
            <tr>
                <th style="padding: 1rem; text-align: left; border: 1px solid var(--border-color);">Plateforme</th>
                <th style="padding: 1rem; text-align: center; border: 1px solid var(--border-color);">Commission</th>
                <th style="padding: 1rem; text-align: left; border: 1px solid var(--border-color);">Avantages</th>
            </tr>
        </thead>
        <tbody style="background: var(--bg-card);">
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Leboncoin</strong></td>
                <td style="padding: 1rem; text-align: center; border: 1px solid var(--border-color);">0% (gratuit)</td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Remise en main propre, pas de frais</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>eBay</strong></td>
                <td style="padding: 1rem; text-align: center; border: 1px solid var(--border-color);">~12-15%</td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Audience internationale, protections vendeur</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Vinted</strong></td>
                <td style="padding: 1rem; text-align: center; border: 1px solid var(--border-color);">5% + 0.7€</td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Jeune audience, vente rapide</td>
            </tr>
            <tr>
                <td style="padding: 1rem; border: 1px solid var(--border-color);"><strong>Groupes Facebook</strong></td>
                <td style="padding: 1rem; text-align: center; border: 1px solid var(--border-color);">0% (gratuit)</td>
                <td style="padding: 1rem; border: 1px solid var(--border-color);">Communauté passionnée, estimations justes</td>
            </tr>
        </tbody>
    </table>

    <h2>🧮 Calculateur rapide</h2>

    <div style="background: var(--bg-darker); padding: 2rem; margin: 2rem 0; border-radius: var(--radius);">
        <p style="font-weight: 700; margin-bottom: 1rem;">Exemple : Votre <a href="/game-boy-color" style="color: var(--accent-primary);">Game Boy Color</a> vaut combien ?</p>

        <ol>
            <li>Prix loose moyen = <a href="/game-boy-color" style="color: var(--accent-primary);">40€ (voir sur PrixRetro)</a></li>
            <li>CIB ? ×2 = 80€</li>
            <li>Coloris rare (Atomic Purple) ? +40% = 112€</li>
            <li>État MINT ? +20% = 134€</li>
        </ol>

        <p style="margin-top: 1.5rem; font-size: 1.2rem; font-weight: 700;">→ Estimation finale : <span style="color: var(--accent-primary);">130-140€</span></p>
    </div>

    <div style="background: var(--bg-card); border-left: 4px solid var(--accent-primary); padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
        <strong>💡 Conseil expert</strong>
        <p style="margin-top: 0.5rem;">Ne vendez pas en urgence. Les consoles prennent 5-15% de valeur par an (sauf saturation marché). Une collection de 500€ aujourd'hui = 650€ dans 2 ans. Utilisez <a href="/ma-collection" style="color: var(--accent-primary);">notre tracker</a> pour suivre automatiquement l'évolution et vendre au meilleur moment.</p>
    </div>

    <div style="text-align: center; margin: 3rem 0;">
        <a href="/tendances" style="display: inline-block; background: var(--accent-primary); color: white; padding: 1rem 2rem; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
            📊 Consulter les tendances du marché retrogaming
        </a>
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
