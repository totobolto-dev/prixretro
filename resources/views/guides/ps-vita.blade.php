@extends('layout')

@section('title', 'PS Vita d\'occasion : Guide d\'achat et pièges à éviter | PrixRetro')

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
        <span>PS Vita</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">PS Vita d'occasion - Pièges à éviter et meilleures affaires</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Créé le 21 janvier 2026 • Lecture 5 min
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">💡 L'essentiel à retenir</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Prix moyen</strong>: {{ $avgPrice ?? '120-180' }}€ pour une console en bon état
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Modèle recommandé</strong>: PCH-2000 (Slim) - meilleure autonomie, plus léger
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>Point critique</strong>: Cartes mémoires propriétaires très chères
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong>Attention</strong>: Comptes <abbr title="PlayStation Network - Réseau en ligne Sony">PSN</abbr> liés, écrans tactiles rayés
                </li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">PS Vita 1000 vs 2000 : Quelle différence ?</h2>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">PCH-1000 (Fat) - 100-150€</h3>
        <p style="margin-bottom: 1rem;">
            Le modèle original avec écran <abbr title="Organic Light-Emitting Diode - Écran avec meilleurs contrastes">OLED</abbr> offre de meilleures couleurs et contrastes.
            Cependant, il est plus lourd et a une moins bonne autonomie (4-5h).
            Préférez ce modèle si vous jouez principalement chez vous et que la qualité d'image
            est importante pour vous.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">PCH-2000 (Slim) - 120-180€</h3>
        <p style="margin-bottom: 1.5rem;">
            Version améliorée : 20% plus léger, autonomie de 6-7h, micro-USB standard pour la recharge.
            L'écran <abbr title="Liquid Crystal Display - Écran à cristaux liquides">LCD</abbr> est moins impressionnant que l'<abbr title="Organic Light-Emitting Diode - Écran avec meilleurs contrastes">OLED</abbr> mais reste très correct.
            <strong>C'est notre recommandation</strong> pour un usage nomade.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Le problème des cartes mémoires</h2>

        <p style="margin-bottom: 1rem;">
            Sony a utilisé des cartes mémoires propriétaires pour la Vita, et elles coûtent une fortune :
        </p>

        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">8 GB : 15-25€</li>
            <li style="margin-bottom: 0.5rem;">16 GB : 30-40€</li>
            <li style="margin-bottom: 0.5rem;">32 GB : 60-80€</li>
            <li style="margin-bottom: 0.5rem;">64 GB : 100-150€</li>
        </ul>

        <p style="margin-bottom: 1.5rem;">
            <strong>Conseil</strong> : Achetez une console avec carte mémoire incluse si possible.
            16 GB minimum pour avoir de la marge. Les lots console + jeux + carte mémoire sont souvent
            plus intéressants que d'acheter séparément.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Points de vigilance</h2>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--warning);">⚠️ Écran tactile et arrière</h3>
        <p style="margin-bottom: 1rem;">
            La Vita a un écran tactile avant ET arrière. Vérifiez qu'ils fonctionnent tous les deux.
            L'écran se raye facilement, inspectez les photos attentivement.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--warning);">⚠️ Compte <abbr title="PlayStation Network - Réseau en ligne Sony">PSN</abbr> lié</h3>
        <p style="margin-bottom: 1rem;">
            Demandez au vendeur de retirer son compte <abbr title="PlayStation Network - Réseau en ligne Sony">PSN</abbr> avant l'envoi. Une Vita liée à un compte
            que vous ne connaissez pas est inutilisable pour télécharger vos propres jeux.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--warning);">⚠️ Boutons et joysticks</h3>
        <p style="margin-bottom: 1rem;">
            Les joysticks analogiques peuvent devenir imprécis avec l'usage intensif (drift).
            Difficile à vérifier avant achat sans tester, mais posez la question au vendeur.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--warning);">⚠️ Batteries gonflées</h3>
        <p style="margin-bottom: 1.5rem;">
            Les Vita stockées longtemps sans utilisation peuvent avoir des batteries gonflées.
            Si la coque arrière est bombée ou se détache, FUYEZ. C'est un risque d'incendie.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Verdict final</h2>

        <p style="margin-bottom: 1rem;">
            La PS Vita est une excellente console portable avec un catalogue de qualité (<abbr title="Jeux de rôle">RPG</abbr> japonais,
            <abbr title="Romans visuels interactifs">visual novels</abbr>, indie games). Cependant, le prix des cartes mémoires propriétaires reste un frein.
        </p>

        <p style="margin-bottom: 2rem;">
            <strong>Budget recommandé</strong> : 180-220€ tout compris (console Slim + carte mémoire 16 GB + 2-3 jeux).
            C'est plus cher qu'une 3DS mais l'expérience vaut le coup pour les fans de <abbr title="Jeux de rôle">RPG</abbr>.
        </p>

        @if($console)
        <div style="background: var(--bg-secondary); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--accent-primary); margin: 2rem 0;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">📈 Suivez l'évolution des prix</h3>
            <p style="margin-bottom: 1.5rem; color: var(--text-secondary);">
                Consultez notre page dédiée pour voir l'historique complet des prix et les ventes récentes.
            </p>
            <a href="/{{ $console->slug }}" style="display: inline-block; background: var(--accent-primary); color: var(--bg-primary); padding: 0.75rem 1.5rem; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
                Voir les prix PS Vita →
            </a>
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
