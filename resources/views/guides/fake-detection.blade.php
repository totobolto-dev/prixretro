@extends('layout')

@section('title', 'Comment repérer une fausse console retrogaming | Guide PrixRetro')

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <a href="/guides">Guides</a>
        <span>›</span>
        <span>Détecter les contrefaçons</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">Comment repérer une console retrogaming contrefaite</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Créé le 21 janvier 2026 • Lecture 5 min
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--warning);">⚠️ Signes d'alerte</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">Prix anormalement bas (50% sous le marché)</li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">Photos floues ou génériques (stock photos)</li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">Vendeur avec stock important de consoles "neuves"</li>
                <li style="padding: 0.5rem 0;">Plastique trop brillant ou couleurs légèrement différentes</li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Les contrefaçons courantes</h2>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Coques remplacées (reshells)</h3>
        <p style="margin-bottom: 1.5rem;">
            Beaucoup de Game Boy ont leur coque d'origine remplacée par des coques aftermarket chinoises.
            Ce n'est pas forcément grave (la console fonctionne), mais ça réduit la valeur. Les coques aftermarket
            ont souvent des couleurs trop vives, du plastique plus brillant et des vis Phillips au lieu de tri-wing.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Cartouches piratées</h3>
        <p style="margin-bottom: 1.5rem;">
            Les cartouches Pokémon sont massivement contrefaites. Vérifiez : l'étiquette (impression floue),
            le plastique (plus léger, plus brillant), le circuit imprimé (visible par transparence avec une lampe forte).
            Une vraie cartouche a un code imprimé sur l'étiquette et une encoche sur le côté droit.
        </p>

        <h3 style="margin-top: 1.5rem; margin-bottom: 0.75rem; color: var(--accent-primary);">Clones de consoles</h3>
        <p style="margin-bottom: 1.5rem;">
            Les "NES Classic" et "SNES Classic" ont été massivement clonées. Si le prix est très bas et que le
            vendeur en a plusieurs, c'est probablement un clone chinois avec des jeux piratés. La qualité de build
            et la compatibilité sont aléatoires.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Comment se protéger</h2>

        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">
                <strong>Demandez des photos détaillées</strong> : Avant d'acheter, demandez au vendeur des photos
                nettes des vis, du numéro de série, des ports, et de l'intérieur si possible.
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>Vérifiez le poids</strong> : Les contrefaçons sont souvent plus légères car elles utilisent
                moins de composants ou du plastique de moindre qualité.
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>Comparez les prix</strong> : Si c'est 50% moins cher que la moyenne, il y a une raison.
                Utilisez notre site pour connaître les prix du marché.
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>Privilégiez les vendeurs établis</strong> : Sur eBay, choisissez des vendeurs avec historique
                positif et garantie satisfait ou remboursé.
            </li>
        </ul>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Verdict</h2>

        <p style="margin-bottom: 2rem;">
            Les contrefaçons existent, mais avec un peu de vigilance, vous les éviterez facilement.
            La règle d'or : <strong>si c'est trop beau pour être vrai, c'est probablement faux</strong>.
            Achetez auprès de vendeurs de confiance, comparez les prix sur notre site, et n'hésitez pas
            à poser des questions avant d'acheter.
        </p>

        <div style="background: var(--bg-secondary); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--accent-primary); margin: 2rem 0;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">📊 Comparez les prix du marché</h3>
            <p style="margin-bottom: 1.5rem; color: var(--text-secondary);">
                Utilisez notre base de données pour savoir si un prix est normal ou suspect.
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


@section('scripts')
@if(isset($faqSchema))
<!-- Schema.org FAQ Structured Data -->
<script type="application/ld+json">
@json($faqSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
</script>
@endif
@endsection
