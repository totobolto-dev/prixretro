@extends('layout')

@section('title', 'Comment nettoyer et blanchir une console retrogaming jaunie | PrixRetro')

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
        <span>Nettoyer le plastique jauni</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">Comment nettoyer et blanchir une console retrogaming jaunie</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Créé le 22 janvier 2026 • Lecture 7 min
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">💡 L'essentiel à retenir</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Cause du jaunissement</strong> : Oxydation des retardateurs de flamme dans le plastique <abbr title="Acrylonitrile Butadiène Styrène - Plastique utilisé pour les consoles">ABS</abbr>
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Solution efficace</strong> : Retr0bright (peroxyde d'hydrogène + UV)
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Impact sur la valeur</strong> : Le jaunissement réduit le prix de 20 à 40%
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong>Risque</strong> : Re-jaunissement possible si exposé aux UV après traitement
                </li>
            </ul>
        </div>

        <p style="margin-bottom: 1.5rem;">
            Le jaunissement du plastique est l'ennemi numéro un des collectionneurs de consoles retro. Une <a href="/super-nintendo" style="color: var(--accent-primary);">Super Nintendo</a>
            grise devenue beige, une <a href="/playstation-1" style="color: var(--accent-primary);">PlayStation</a> blanche virant au crème, une
            <a href="/game-boy-color" style="color: var(--accent-primary);">Game Boy Color</a> qui perd son éclat... Ce phénomène naturel
            réduit la valeur de la console mais il existe des solutions efficaces pour redonner à vos consoles leur blancheur d'origine.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Pourquoi le plastique jaunit-il ?</h2>

        <p style="margin-bottom: 1rem;">
            Le jaunissement est causé par l'oxydation des retardateurs de flamme (bromés) présents dans le plastique ABS utilisé
            par Nintendo, Sony et Sega dans les années 90. Ces additifs réagissent aux UV, à la chaleur, à l'humidité et à la
            fumée de cigarette en créant une décoloration permanente.
        </p>

        <p style="margin-bottom: 1rem;">
            Ce n'est pas de la saleté qu'on peut essuyer. C'est une transformation chimique de la surface du plastique. Les consoles
            stockées dans des greniers chauds ou exposées au soleil jaunissent rapidement. Celles conservées dans des pièces sombres
            et fraîches gardent leur couleur d'origine plus longtemps.
        </p>

        <p style="margin-bottom: 1.5rem;">
            Certaines pièces jaunissent plus que d'autres sur une même console. Sur une Super Nintendo, le dessus jaunit souvent
            plus vite que le dessous, simplement parce qu'il était davantage exposé à la lumière. Ce jaunissement inégal est
            particulièrement inesthétique.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Méthode 1 : le nettoyage basique (jaunissement léger)</h2>

        <p style="margin-bottom: 1rem;">
            Avant de tenter des méthodes agressives, commencez par un simple nettoyage en profondeur. Parfois, ce qui ressemble
            à du jaunissement est en réalité une accumulation de crasse, de nicotine ou de poussière grasse.
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin: 1.5rem 0;">
            <h3 style="margin-bottom: 1rem;">🧰 Matériel nécessaire</h3>
            <ul style="list-style: disc; padding-left: 2rem;">
                <li style="margin-bottom: 0.5rem;">Alcool isopropylique à 90% ou plus</li>
                <li style="margin-bottom: 0.5rem;">Chiffons microfibres</li>
                <li style="margin-bottom: 0.5rem;">Cotons-tiges</li>
                <li>Brosse à dents souple</li>
            </ul>
        </div>

        <p style="margin-bottom: 1rem;">
            Démontez la coque de la console (conservez les vis dans une boîte). Nettoyez chaque pièce avec l'alcool isopropylique,
            en insistant sur les zones les plus sales. Les cotons-tiges permettent d'atteindre les rainures et les gravures.
            Laissez sécher complètement avant de remonter.
        </p>

        <p style="margin-bottom: 1.5rem;">
            Cette méthode ne supprime pas le vrai jaunissement chimique, mais elle retire la couche superficielle de saleté qui
            l'accentue. Résultat : la console sera plus claire de plusieurs tons sans intervention chimique. C'est sans risque
            et ça vaut toujours le coup d'essayer en premier.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Méthode 2 : le Retr0bright (jaunissement moyen à sévère)</h2>

        <p style="margin-bottom: 1rem;">
            Le Retr0bright est la méthode de référence pour blanchir le plastique jauni. Elle utilise du <abbr title="H₂O₂ - Eau oxygénée">peroxyde d'hydrogène</abbr>
            et des UV pour inverser la réaction chimique responsable du jaunissement.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Ingrédients</h3>
        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">Peroxyde d'hydrogène 12% (volume 40) - disponible en pharmacie ou coiffure</li>
            <li style="margin-bottom: 0.5rem;">Agent épaississant : amidon de maïs (Maïzena) ou gel de coiffure oxydant</li>
            <li>Lumière UV : soleil direct ou lampe UV</li>
        </ul>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Préparation et application</h3>
        <p style="margin-bottom: 1rem;">
            Mélangez le peroxyde avec l'agent épaississant pour obtenir une pâte crémeuse. La consistance doit permettre
            d'enduire le plastique sans que ça coule.
        </p>

        <p style="margin-bottom: 1rem;">
            Démontez complètement la coque. Retirez tous les composants électroniques, autocollants et étiquettes. Appliquez
            la pâte uniformément sur toutes les pièces en plastique à traiter, en couche épaisse (3-5mm).
        </p>

        <p style="margin-bottom: 1rem;">
            Placez les pièces enduites sous une source UV. En plein soleil d'été, comptez 3 à 6 heures. Sous une lampe UV,
            ça peut prendre 8 à 12 heures. Surveillez régulièrement : le plastique blanchit progressivement.
        </p>

        <p style="margin-bottom: 1.5rem;">
            Rincez abondamment à l'eau claire dès que la couleur d'origine est revenue. Séchez soigneusement. Remontez la console.
        </p>

        <div style="background: var(--warning-bg, #fef3c7); padding: 1rem; border-radius: var(--radius); border: 1px solid var(--warning, #f59e0b); margin: 1.5rem 0;">
            <h3 style="margin-bottom: 0.5rem;">⚠️ Précautions importantes</h3>
            <ul style="list-style: disc; padding-left: 2rem;">
                <li style="margin-bottom: 0.5rem;">Portez des gants. Le peroxyde à 12% brûle la peau.</li>
                <li style="margin-bottom: 0.5rem;">Travaillez en extérieur ou dans une pièce ventilée.</li>
                <li style="margin-bottom: 0.5rem;">Ne chauffez JAMAIS le peroxyde (risque d'explosion).</li>
                <li style="margin-bottom: 0.5rem;">Ne laissez pas le produit sécher sur le plastique (risque de taches blanches).</li>
                <li>Testez d'abord sur une petite zone non visible.</li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Risques et limites du Retr0bright</h2>

        <p style="margin-bottom: 1rem;">
            Le Retr0bright n'est pas une solution miracle. Il comporte plusieurs risques dont il faut avoir conscience.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Re-jaunissement</h3>
        <p style="margin-bottom: 1rem;">
            Le plastique traité peut re-jaunir en quelques mois ou années s'il est exposé aux UV. Pour éviter ça, conservez
            la console dans un endroit sombre après traitement.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Fragilisation du plastique</h3>
        <p style="margin-bottom: 1rem;">
            Le peroxyde attaque légèrement la structure du plastique. Une console traitée plusieurs fois devient plus cassante.
            À éviter sur des pièces déjà fissurées.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Résultat inégal</h3>
        <p style="margin-bottom: 1rem;">
            Si l'application n'est pas parfaitement uniforme, vous risquez d'obtenir des taches plus claires ou plus foncées.
            Le marbrage est difficile à corriger.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Perte de brillance</h3>
        <p style="margin-bottom: 1.5rem;">
            Le Retr0bright a tendance à rendre le plastique légèrement mat. Pour restaurer la brillance, il faut polir après traitement.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Alternative : le remplacement de la coque</h2>

        <p style="margin-bottom: 1rem;">
            Si le jaunissement est trop avancé ou que vous ne voulez pas prendre de risques chimiques, remplacer la coque est
            une option viable. Des coques neuves de remplacement (aftermarket) existent pour la plupart des consoles populaires.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Avantages</h3>
        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1rem;">
            <li style="margin-bottom: 0.5rem;">Résultat immédiat et garanti</li>
            <li style="margin-bottom: 0.5rem;">Plastique neuf, pas fragile</li>
            <li>Choix de couleurs (transparentes, personnalisées)</li>
        </ul>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Inconvénients</h3>
        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">Coût élevé (20 à 60€ selon la console)</li>
            <li style="margin-bottom: 0.5rem;">Qualité variable selon les fabricants</li>
            <li>Perd l'authenticité (moins de valeur pour la revente - voir notre <a href="/guides/authentification-console-retrogaming" style="color: var(--accent-primary);">guide d'authentification</a>)</li>
        </ul>

        <p style="margin-bottom: 1.5rem;">
            Pour une collection personnelle, c'est acceptable. Pour revendre, gardez la coque d'origine même jaunie : les puristes
            préfèrent l'authenticité. Consultez nos <a href="/" style="color: var(--accent-primary);">données de prix</a> pour voir
            l'impact réel sur la valeur.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Prévention : éviter le jaunissement futur</h2>

        <p style="margin-bottom: 1rem;">
            Une fois votre console blanchie, voici comment préserver le résultat.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Conservation optimale</h3>
        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">Rangez dans un endroit sombre, sec et frais (température stable autour de 18-20°C)</li>
            <li style="margin-bottom: 0.5rem;">Évitez l'exposition directe au soleil ou aux néons</li>
            <li style="margin-bottom: 0.5rem;">Utilisez des housses anti-UV si vous exposez vos consoles en vitrine</li>
            <li>Ne fumez pas à proximité (la nicotine accélère le jaunissement)</li>
        </ul>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Entretien régulier</h3>
        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">Dépoussiérez avec un chiffon microfibre sec tous les mois</li>
            <li style="margin-bottom: 0.5rem;">Nettoyez à l'alcool isopropylique une fois par an</li>
            <li>Évitez les produits ménagers à base de javel ou d'ammoniaque</li>
        </ul>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Verdict : faut-il blanchir ses consoles ?</h2>

        <p style="margin-bottom: 1rem;">
            Ça dépend de votre objectif. Si vous collectionnez pour revendre, le Retr0bright peut augmenter la valeur de 20 à 40%
            pour une console moyennement jaunie. Mais prévenez toujours l'acheteur que la console a été traitée chimiquement.
        </p>

        <p style="margin-bottom: 1rem;">
            Si vous collectionnez pour vous, c'est une question d'esthétique personnelle. Certains préfèrent la patine naturelle
            du temps, d'autres veulent du "comme neuf". Les deux approches se valent.
        </p>

        <p style="margin-bottom: 2rem;">
            Une chose est sûre : ne négligez jamais l'entretien de base. Un simple nettoyage à l'alcool isopropylique régulier
            fait des miracles et évite que le problème s'aggrave. C'est gratuit, sans risque, et ça préserve la valeur de votre
            collection selon nos <a href="/" style="color: var(--accent-primary);">analyses de prix</a>.
        </p>

        <div style="background: var(--warning-bg, #fef3c7); padding: 1rem; border-radius: var(--radius); border: 1px solid var(--warning, #f59e0b); margin: 2rem 0;">
            <strong>⚠️ Attention :</strong> Ce guide est fourni à titre informatif. L'auteur et PrixRetro ne peuvent être tenus
            responsables des dégâts causés à vos consoles par l'utilisation de produits chimiques. Procédez à vos propres risques.
        </div>

        <div class="back-link">
            <a href="/guides">← Retour aux guides</a>
        </div>
    </article>
</div>
@endsection
