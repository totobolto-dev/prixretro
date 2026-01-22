@extends('layout')

@section('title', 'Authentifier une console retrogaming : guide technique avancé | PrixRetro')

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
        <span>Authentification avancée</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">Authentifier une console retrogaming : guide technique avancé</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Créé le 22 janvier 2026 • Lecture 8 min
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">⚠️ Signes de contrefaçon</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Vis Phillips</strong> sur une Nintendo (devrait être tri-wing)
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Plastique trop brillant</strong> ou couleurs trop saturées
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Numéro de série</strong> absent, illisible ou incohérent
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong>Prix anormalement bas</strong> (50% sous le <a href="/" style="color: var(--accent-primary);">prix marché PrixRetro</a>)
                </li>
            </ul>
        </div>

        <p style="margin-bottom: 1.5rem;">
            Le marché du retrogaming est envahi de contrefaçons, de clones et de pièces de remplacement aftermarket.
            Pour un collectionneur sérieux, savoir authentifier une console à 100% est indispensable. Ce guide technique
            vous donne les outils pour détecter les fausses consoles, identifier les <abbr title="Coque de remplacement non-originale">reshells</abbr>
            et vérifier l'authenticité des composants.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Les numéros de série : votre premier outil</h2>

        <p style="margin-bottom: 1rem;">
            Toutes les consoles Nintendo, Sony et Sega possèdent un numéro de série unique gravé ou imprimé sur l'appareil.
            Ce numéro contient des informations sur la date de fabrication, l'usine de production et parfois la région de distribution.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Nintendo Game Boy Advance SP</h3>
        <p style="margin-bottom: 1rem;">
            Format du numéro de série : <code>JPN40XXXXXXX</code> ou <code>USN40XXXXXXX</code>
        </p>
        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1rem;">
            <li style="margin-bottom: 0.5rem;">Les 3 premières lettres indiquent la région (JPN = Japon, USN = USA, EUR = Europe)</li>
            <li style="margin-bottom: 0.5rem;">Les 2 chiffres suivants (40, 50, etc.) indiquent le modèle</li>
            <li style="margin-bottom: 0.5rem;">Les 7 chiffres suivants sont le numéro unique</li>
        </ul>
        <p style="margin-bottom: 1.5rem;">
            Un numéro de série effacé, illisible ou absent est un red flag majeur. Les consoles reconditionnées en Chine
            n'ont souvent pas de numéro de série valide. Consultez nos pages <a href="/game-boy-advance-sp" style="color: var(--accent-primary);">Game Boy Advance SP</a>
            pour comparer les prix des modèles authentiques.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">PlayStation 1 et 2</h3>
        <p style="margin-bottom: 1.5rem;">
            Sony utilise un code alphanumérique sous la console. Le format varie selon le modèle, mais tous incluent
            une lettre de révision (A, B, C...) qui indique la version hardware. Exemple : <code>SCPH-90004 CB</code>
            (PS2 Slim, révision C, région <abbr title="Phase Alternate Line - Format vidéo européen">PAL</abbr> Europe).
            Les fausses PS1/PS2 ont souvent des codes incohérents ou utilisent des révisions qui n'existent pas dans la région indiquée.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Sega Dreamcast</h3>
        <p style="margin-bottom: 1.5rem;">
            Les Dreamcast PAL ont un numéro de série commençant par "0" ou "1". Le deuxième chiffre indique l'année
            de fabrication (0 = 2000, 1 = 2001). Une Dreamcast avec un numéro commençant par "2" ou "3" est soit
            <abbr title="National Television System Committee - Format vidéo japonais/américain">NTSC</abbr>-J soit une contrefaçon.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Les vis : l'indice le plus négligé</h2>

        <p style="margin-bottom: 1rem;">
            Nintendo utilise des vis tri-wing (trèfle à trois branches) pour la plupart de ses consoles depuis les années 90.
            C'est une sécurité anti-ouverture pour le grand public. Les consoles aftermarket et les reshells chinois utilisent
            presque toujours des vis Phillips classiques.
        </p>

        <div style="background: var(--warning-bg, #fef3c7); padding: 1rem; border-radius: var(--radius); border: 1px solid var(--warning, #f59e0b); margin: 1.5rem 0;">
            <strong>⚠️ Règle d'or :</strong> Si vous voyez des vis Phillips sur une <a href="/game-boy-color" style="color: var(--accent-primary);">Game Boy Color</a>,
            une <a href="/game-boy-advance-sp" style="color: var(--accent-primary);">GBA SP</a> ou une <a href="/nintendo-ds" style="color: var(--accent-primary);">Nintendo DS</a>,
            c'est une coque de remplacement garantie.
        </div>

        <p style="margin-bottom: 1.5rem;">
            Attention cependant : une console authentique peut avoir été ouverte avec des vis de remplacement par un réparateur.
            Ce n'est pas forcément une contrefaçon, mais ça indique une modification. PlayStation et Sega utilisent principalement
            des vis Phillips classiques, donc ce critère ne s'applique pas à elles.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Les coques de remplacement : comment les identifier</h2>

        <p style="margin-bottom: 1rem;">
            Les coques aftermarket chinoises sont partout sur eBay et AliExpress. Elles permettent de "restaurer" une console
            abîmée en remplaçant sa coque, mais elles font chuter la valeur de collection de 20 à 40%.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Signes révélateurs</h3>
        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;"><strong>Plastique trop brillant</strong> : Les coques aftermarket ont un finish trop lisse et brillant, presque vitrifié. Les originales ont un léger grain.</li>
            <li style="margin-bottom: 0.5rem;"><strong>Couleurs saturées</strong> : Les couleurs sont souvent plus vives que l'original. Une GBC violette aftermarket tire vers le violet fluo.</li>
            <li style="margin-bottom: 0.5rem;"><strong>Ajustement imparfait</strong> : Les coques chinoises ont souvent des interstices visibles entre les pièces.</li>
            <li style="margin-bottom: 0.5rem;"><strong>Absence de marquage</strong> : Les coques originales ont des codes moulés à l'intérieur. Les aftermarket n'ont rien.</li>
            <li style="margin-bottom: 0.5rem;"><strong>Poids différent</strong> : Le plastique aftermarket est souvent 1 à 5g plus léger.</li>
        </ul>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Cas particulier : les coques transparentes</h3>
        <p style="margin-bottom: 1.5rem;">
            Les <a href="/game-boy-color/atomic-purple" style="color: var(--accent-primary);">Game Boy Color Atomic Purple</a>,
            <a href="/game-boy-color/ice-blue" style="color: var(--accent-primary);">Ice Blue</a> et autres transparentes sont massivement reshellées.
            Pour vérifier l'authenticité : regardez la texture du plastique par transparence (l'original a un effet légèrement "pailleté"),
            vérifiez les vis (tri-wing = plus de chances que ce soit d'origine), et comparez avec nos <a href="/game-boy-color/classement" style="color: var(--accent-primary);">
            prix de référence</a>.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Les autocollants et étiquettes d'origine</h2>

        <p style="margin-bottom: 1rem;">
            Les autocollants au dos des consoles (code-barres, certifications CE/FCC, avertissements) sont difficiles à reproduire fidèlement.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Nintendo</h3>
        <p style="margin-bottom: 1rem;">
            Les étiquettes Nintendo ont une police spécifique et une qualité d'impression professionnelle. Les contrefaçons ont souvent
            des polices légèrement différentes, des logos Nintendo mal alignés ou flous, ou du papier d'autocollant de mauvaise qualité
            (brillant au lieu de mat).
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Sony PlayStation</h3>
        <p style="margin-bottom: 1.5rem;">
            Les autocollants PlayStation originaux ont un hologramme difficile à reproduire. Si l'autocollant n'a pas d'hologramme
            ou s'il semble imprimé (au lieu d'être en relief), méfiez-vous. Une console authentique mais usagée peut avoir perdu
            ses autocollants - leur absence n'est pas une preuve de contrefaçon, mais leur présence en mauvaise qualité l'est.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Les éditions limitées : l'authentification ultime</h2>

        <p style="margin-bottom: 1rem;">
            Les consoles en édition limitée (Pikachu, Pokémon, Zelda, etc.) sont les plus contrefaites car les plus chères.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Game Boy Color Pikachu</h3>
        <p style="margin-bottom: 1rem;">L'originale a :</p>
        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1rem;">
            <li style="margin-bottom: 0.5rem;">Des joues rouges tamponnées (pas des autocollants)</li>
            <li style="margin-bottom: 0.5rem;">Un jaune spécifique (pas trop vif)</li>
            <li style="margin-bottom: 0.5rem;">Le logo Pikachu en sérigraphie (légèrement en relief au toucher)</li>
            <li style="margin-bottom: 0.5rem;">Numéro de série commençant par JPN ou USN selon la région</li>
        </ul>
        <p style="margin-bottom: 1.5rem;">
            Les fausses ont des autocollants à la place des sérigraphies, un jaune trop saturé, et pas de numéro de série valide.
            Comparez avec nos <a href="/game-boy-color" style="color: var(--accent-primary);">données de prix réels</a> pour éviter les arnaques.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Outils et ressources pour l'authentification</h2>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin: 1.5rem 0;">
            <h3 style="margin-bottom: 1rem;">🔗 Ressources recommandées</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong><a href="/" style="color: var(--accent-primary);">PrixRetro</a></strong> - Comparez les prix de marché pour détecter les offres suspectes
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Sega Retro</strong> - Wiki exhaustif sur les révisions Sega
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>PSX DataCenter</strong> - Base de données PlayStation complète
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong>r/Gamecollecting</strong> - Communauté Reddit pour authentification collaborative
                </li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Que faire si vous achetez une contrefaçon ?</h2>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Achat sur eBay</h3>
        <p style="margin-bottom: 1rem;">
            Ouvrez un litige dans les 30 jours. Motif : "Article non conforme à la description". eBay favorise presque
            toujours l'acheteur. Vous serez remboursé après retour.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Achat sur Leboncoin/Vinted</h3>
        <p style="margin-bottom: 1rem;">
            Plus compliqué. Si le vendeur a clairement menti (annoncé "console d'origine" alors que c'est un clone),
            vous pouvez demander un remboursement. Si le vendeur refuse, vous pouvez porter plainte pour escroquerie,
            mais c'est long et coûteux.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Achat en boutique</h3>
        <p style="margin-bottom: 1.5rem;">
            Vous avez 14 jours de rétractation (achat à distance) ou un recours en garantie légale de conformité (2 ans).
            Contactez la boutique avec des preuves (photos, comparaisons avec nos données).
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Conclusion : l'authentification est un investissement</h2>

        <p style="margin-bottom: 1rem;">
            Vérifier l'authenticité d'une console demande du temps et parfois des outils (tournevis tri-wing, lampe UV, loupe).
            Mais pour une collection de valeur, c'est indispensable.
        </p>

        <p style="margin-bottom: 1.5rem;">
            Une console authentique en bon état vaudra toujours plus qu'un reshell ou un clone, même si ce dernier fonctionne mieux.
            Les collectionneurs sérieux payent un premium pour l'authenticité. Utilisez nos <a href="/" style="color: var(--accent-primary);">
            données de prix en temps réel</a> pour repérer les offres anormalement basses qui cachent souvent des contrefaçons.
        </p>

        <p style="margin-bottom: 2rem;">
            En cas de doute, n'hésitez pas à demander l'avis de la communauté avant d'acheter. Un post sur r/Gamecollecting
            avec des photos détaillées vous donnera une réponse en quelques heures. Mieux vaut perdre une bonne affaire que
            d'acheter une contrefaçon.
        </p>

        <div class="back-link">
            <a href="/guides">← Retour aux guides</a>
        </div>
    </article>
</div>
@endsection
