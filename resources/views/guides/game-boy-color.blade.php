@extends('layout')

@section('title', 'Guide d\'achat Game Boy Color 2026 - Comment choisir | PrixRetro')

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
        <span>Game Boy Color</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">Guide d'achat Game Boy Color - Comment choisir sa variante en 2026</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Créé le 21 janvier 2026 • Lecture 5 min
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">💡 L'essentiel à retenir</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Prix moyen</strong>: {{ $console ? number_format($console->variants->flatMap->listings->where('status', 'approved')->avg('price') ?? 60, 0) : '60' }}€ pour une console en bon état
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Variantes les plus recherchées</strong>: Atomic Purple, Teal, Kiwi
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Meilleur rapport qualité-prix</strong>: Modèles standards (Jaune, Rouge, Bleu)
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong>À éviter</strong>: Consoles sans trappe de piles ou avec écran rayé
                </li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Pourquoi acheter une Game Boy Color en 2026 ?</h2>

        <p style="margin-bottom: 1rem;">
            La Game Boy Color reste l'une des consoles portables les plus accessibles du marché retrogaming.
            Avec un catalogue de plus de 900 jeux et une compatibilité <abbr title="Game Boy">GB</abbr> originale, elle offre
            une bibliothèque immense pour un prix contenu.
        </p>

        <p style="margin-bottom: 1.5rem;">
            Contrairement à la <abbr title="Game Boy Advance">GBA</abbr> <abbr title="Special - Version améliorée">SP</abbr>, la <abbr title="Game Boy Color">GBC</abbr> n'a pas de <abbr title="Écran éclairé par l'arrière pour jouer dans le noir">rétroéclairage</abbr>, ce qui la rend
            moins pratique en conditions de faible luminosité. Cependant, elle reste très populaire
            auprès des collectionneurs pour sa variété de couleurs emblématiques.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Les différentes variantes : laquelle choisir ?</h2>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Variantes standards (50-70€)</h3>
        <p style="margin-bottom: 1rem;">
            Les modèles <strong>Jaune (Dandelion)</strong>, <strong>Rouge (Berry)</strong> et <strong>Bleu (Teal)</strong>
            sont les plus abordables. Parfaits pour débuter sans exploser votre budget. L'écran et les boutons
            sont identiques sur tous les modèles, seule la couleur change.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Variantes colorées recherchées (70-120€)</h3>
        <p style="margin-bottom: 1rem;">
            <strong>Atomic Purple</strong> est la variante la plus recherchée et culte. Son plastique translucide
            permet de voir les composants internes, ce qui plaît énormément aux collectionneurs.
            D'autres couleurs populaires comme <strong>Teal</strong>, <strong>Kiwi</strong> et <strong>Grape</strong>
            sont également prisées pour leur rareté.
            Comptez 20-30€ de plus qu'un modèle standard.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Éditions spéciales (100-200€+)</h3>
        <p style="margin-bottom: 1.5rem;">
            Les Game Boy Color Pokémon (Pikachu, Gold/Silver) et les éditions japonaises limitées
            atteignent des prix élevés. Réservé aux collectionneurs passionnés avec un budget conséquent.
        </p>

        @if($console && count($variantPrices) > 0)
        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin: 2rem 0;">
            <h3 style="margin-bottom: 1rem;">📊 Prix moyens par variante (données réelles)</h3>
            <ul style="list-style: none; padding: 0;">
                @foreach($variantPrices as $slug => $price)
                    @php
                        $variant = $console->variants->where('slug', $slug)->first();
                    @endphp
                    @if($variant)
                    <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between;">
                        <span>{{ $variant->short_name }}</span>
                        <strong>{{ $price }}€</strong>
                    </li>
                    @endif
                @endforeach
            </ul>
            <p style="margin-top: 1rem; margin-bottom: 0; color: var(--text-secondary); font-size: 0.85rem;">
                <a href="/{{ $console->slug }}" style="color: var(--accent-primary);">
                    Voir toutes les variantes Game Boy Color avec historique complet →
                </a>
            </p>
        </div>
        @endif

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Points de vigilance avant l'achat</h2>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--warning);">⚠️ L'écran</h3>
        <p style="margin-bottom: 1rem;">
            L'écran <abbr title="Liquid Crystal Display - Écran à cristaux liquides">LCD</abbr> se raye facilement. Vérifiez bien les photos pour détecter rayures et pixels morts.
            Un écran rayé n'est pas rédhibitoire pour jouer, mais réduit significativement la valeur de revente.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--warning);">⚠️ La trappe de piles</h3>
        <p style="margin-bottom: 1rem;">
            Beaucoup de <abbr title="Game Boy Color">GBC</abbr> vendues ont perdu leur trappe de piles. Cela fonctionne parfaitement avec du scotch,
            mais c'est moche et dévalorise la console. Privilégiez les modèles complets.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--warning);">⚠️ L'oxydation des contacts</h3>
        <p style="margin-bottom: 1rem;">
            Les contacts de piles peuvent s'oxyder avec le temps, surtout si des piles ont coulé.
            Demandez au vendeur si la console s'allume correctement. Un nettoyage à l'alcool isopropylique
            suffit généralement.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--warning);">⚠️ Le son</h3>
        <p style="margin-bottom: 1.5rem;">
            Le haut-parleur peut grésiller sur les modèles très utilisés. Testez le son si possible,
            ou prévoyez d'utiliser des écouteurs.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Où acheter sa Game Boy Color ?</h2>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">eBay (Recommandé)</h3>
        <p style="margin-bottom: 1rem;">
            eBay offre la plus grande sélection et permet de voir l'historique des ventes pour juger
            si le prix est correct. Préférez les vendeurs avec beaucoup d'évaluations positives.
            La protection acheteur eBay vous protège en cas de problème.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Leboncoin / Vinted</h3>
        <p style="margin-bottom: 1rem;">
            Bonnes affaires possibles en négociant, mais aucune garantie. Testez la console sur place
            avant de payer. Méfiez-vous des vendeurs sans historique qui proposent plusieurs consoles
            (possible professionnel déguisé).
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Boutiques retrogaming spécialisées</h3>
        <p style="margin-bottom: 1.5rem;">
            Prix plus élevés mais consoles nettoyées, testées et souvent garanties 3-6 mois.
            Bon choix si vous voulez zéro risque et ne pas vous embêter.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Accessoires indispensables</h2>

        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">
                <strong>Piles rechargeables AA</strong> : La <abbr title="Game Boy Color">GBC</abbr> consomme beaucoup (10-15h d'autonomie).
                Investissez dans des piles rechargeables pour économiser sur le long terme.
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>Housse de protection</strong> : Protège la console des rayures pendant le transport.
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>Lampe externe</strong> : Si vous jouez souvent dans des endroits peu éclairés,
                une lampe clip-on améliore grandement l'expérience (ou passez directement à la <abbr title="Game Boy Advance">GBA</abbr> <abbr title="Special - Version améliorée">SP</abbr>).
            </li>
        </ul>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">🌍 Région Lock : Game Boy Color region-free</h2>

        <div style="background: #d1fae5; color: #065f46; padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
            <p style="margin-bottom: 1rem;"><strong>✅ La Game Boy Color est entièrement region-free !</strong></p>
            <ul style="list-style: disc; padding-left: 2rem; margin: 0;">
                <li style="margin-bottom: 0.5rem;">Compatible avec toutes les cartouches Game Boy et Game Boy Color du monde entier</li>
                <li style="margin-bottom: 0.5rem;">GBC japonaise lit jeux US/EUR sans problème (et vice-versa)</li>
                <li style="margin-bottom: 0.5rem;">Import japonais souvent <strong>30-40% moins cher</strong></li>
                <li style="margin-bottom: 0.5rem;">Excellent état général (culture japonaise de préservation)</li>
            </ul>
        </div>

        <p style="margin-bottom: 1.5rem; padding: 1rem; background: var(--bg-card); border-left: 3px solid var(--accent-primary); border-radius: var(--radius);">
            <strong>💡 Bon plan</strong> : Les coloris translucides (Atomic Purple, Ice Blue) sont souvent moins chers au Japon. Économie possible : 20-30€ vs versions PAL.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Verdict final</h2>

        <p style="margin-bottom: 1rem;">
            La Game Boy Color est un excellent point d'entrée dans le retrogaming portable.
            Pour <strong>60-80€</strong>, vous aurez une console fiable avec un immense catalogue de jeux.
        </p>

        <p style="margin-bottom: 1rem;">
            <strong>Notre recommandation</strong> : Si c'est votre première <abbr title="Game Boy Color">GBC</abbr>, prenez un modèle standard
            (Jaune, Rouge ou Bleu) en bon état avec sa trappe de piles. Vous économiserez 20-30€ par rapport
            aux modèles translucides sans perdre en expérience de jeu.
        </p>

        <p style="margin-bottom: 2rem;">
            <strong>Pour les collectionneurs</strong> : Visez l'Atomic Purple ou les éditions Pokémon,
            mais attendez une bonne affaire. Ces variantes prennent de la valeur avec le temps.
        </p>

        @if($console)
        <div style="background: var(--bg-secondary); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--accent-primary); margin: 2rem 0;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">📈 Suivez l'évolution des prix</h3>
            <p style="margin-bottom: 1.5rem; color: var(--text-secondary);">
                Consultez notre page dédiée pour voir l'historique complet des prix et les ventes récentes.
            </p>
            <a href="/{{ $console->slug }}" style="display: inline-block; background: var(--accent-primary); color: var(--bg-primary); padding: 0.75rem 1.5rem; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
                Voir les prix Game Boy Color →
            </a>
        </div>
        @endif
    </article>

    <div class="back-link" style="margin-top: 3rem;">
        <a href="/guides">← Retour aux guides</a>
    </div>
</div>
@endsection
