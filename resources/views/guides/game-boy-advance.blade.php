@extends('layout')

@section('title', 'Guide Game Boy Advance : SP, Micro ou Classique ? | PrixRetro')

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <a href="/guides">Guides</a>
        <span>›</span>
        <span>Game Boy Advance</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">Game Boy Advance - Quel modèle choisir pour débuter ?</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Publié le {{ date('j F Y') }} • Lecture 7 min
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">💡 L'essentiel</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>GBA Classique</strong>: 50-70€ - Pas de rétroéclairage, ergonomie excellente
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong>GBA SP</strong>: 80-120€ - Rétroéclairage, format clapet, RECOMMANDÉ
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong>GBA Micro</strong>: 150-250€ - Ultra compact, écran impeccable, collection uniquement
                </li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">GBA Classique : L'originale</h2>

        <p style="margin-bottom: 1.5rem;">
            La Game Boy Advance classique (2001) est confortable à prendre en main avec ses poignées
            latérales. Son défaut majeur : <strong>pas de rétroéclairage</strong>. Vous devez jouer
            près d'une fenêtre ou avec une lampe externe. Prix attractif mais expérience frustrante
            en 2026 quand on a l'habitude des écrans modernes.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">GBA SP : Le meilleur choix</h2>

        <p style="margin-bottom: 1rem;">
            La SP (2003) corrige tous les défauts de l'originale : rétroéclairage, batterie rechargeable,
            format clapet protégeant l'écran. C'est <strong>le modèle recommandé</strong> pour 95% des joueurs.
        </p>

        <p style="margin-bottom: 1rem; color: var(--text-secondary);">
            <strong>Attention</strong> : Il existe deux versions de la SP :
        </p>

        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">
                <strong>AGS-001 (frontlit)</strong> : Premier modèle avec éclairage frontal. Correct mais contraste moyen.
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>AGS-101 (backlit)</strong> : Modèle rare avec rétroéclairage véritable. Écran magnifique, +30-50€ plus cher.
            </li>
        </ul>

        <p style="margin-bottom: 1.5rem;">
            Si vous trouvez une AGS-101 à bon prix, foncez. Sinon, une AGS-001 fera très bien l'affaire.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">GBA Micro : La pièce de collection</h2>

        <p style="margin-bottom: 1.5rem;">
            Ultra compacte (10 cm), écran impeccable, design premium. La Micro est magnifique mais
            perd la rétrocompatibilité Game Boy/Game Boy Color. Prix élevé (150-250€) réservé aux
            collectionneurs ou fans du format ultra-portable. Pas recommandé comme première GBA.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Notre recommandation</h2>

        <p style="margin-bottom: 1rem;">
            <strong>Pour jouer</strong> : Game Boy Advance SP (AGS-001 ou 101). Budget 80-120€.
            C'est le meilleur compromis confort/prix/fonctionnalités.
        </p>

        <p style="margin-bottom: 2rem;">
            <strong>Budget serré</strong> : GBA classique + lampe externe. Économisez 30-40€ mais
            préparez-vous à une expérience moins pratique.
        </p>

        @if($console)
        <div style="background: var(--bg-secondary); padding: 2rem; border-radius: var(--radius); border: 1px solid var(--accent-primary); margin: 2rem 0;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">📈 Comparez les prix</h3>
            <a href="/{{ $console->slug }}" style="display: inline-block; background: var(--accent-primary); color: var(--bg-primary); padding: 0.75rem 1.5rem; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
                Voir les prix Game Boy Advance →
            </a>
        </div>
        @endif
    </article>

    <div class="back-link" style="margin-top: 3rem;">
        <a href="/guides">← Retour aux guides</a>
    </div>
</div>
@endsection
