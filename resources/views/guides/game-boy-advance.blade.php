@extends('layout')

@section('title', 'Guide Game Boy Advance : SP, Micro ou Classique ? | PrixRetro')

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
        <span>Game Boy Advance</span>
    </div>

    <article style="max-width: 800px;">
        <h1 style="margin-bottom: 1rem;">Game Boy Advance - Quel modèle choisir pour débuter ?</h1>

        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Créé le 21 janvier 2026 • Lecture 5 min
        </p>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border); margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; color: var(--accent-primary);">💡 L'essentiel</h3>
            <ul style="list-style: none; padding: 0;">
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong><abbr title="Game Boy Advance">GBA</abbr> Classique</strong>: 50-70€ - Pas de <abbr title="Écran éclairé par l'arrière pour jouer dans le noir">rétroéclairage</abbr>, ergonomie excellente
                </li>
                <li style="padding: 0.5rem 0; border-bottom: 1px solid var(--border);">
                    <strong><abbr title="Game Boy Advance">GBA</abbr> <abbr title="Special - Version améliorée">SP</abbr></strong>: {{ $avgPrice ?? '80-120' }}€ - <abbr title="Écran éclairé par l'arrière pour jouer dans le noir">Rétroéclairage</abbr>, format clapet, RECOMMANDÉ
                </li>
                <li style="padding: 0.5rem 0;">
                    <strong><abbr title="Game Boy Advance">GBA</abbr> Micro</strong>: 150-250€ - Ultra compact, écran impeccable, collection uniquement
                </li>
            </ul>
        </div>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;"><abbr title="Game Boy Advance">GBA</abbr> Classique : L'originale</h2>

        <p style="margin-bottom: 1.5rem;">
            La Game Boy Advance classique (2001) est confortable à prendre en main avec ses poignées
            latérales. Son défaut majeur : <strong>pas de <abbr title="Écran éclairé par l'arrière pour jouer dans le noir">rétroéclairage</abbr></strong>. Vous devez jouer
            près d'une fenêtre ou avec une lampe externe. Prix attractif mais expérience frustrante
            en 2026 quand on a l'habitude des écrans modernes.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;"><abbr title="Game Boy Advance">GBA</abbr> <abbr title="Special - Version améliorée">SP</abbr> : Le meilleur choix</h2>

        <p style="margin-bottom: 1rem;">
            La <abbr title="Special - Version améliorée">SP</abbr> (2003) corrige tous les défauts de l'originale : <abbr title="Écran éclairé par l'arrière pour jouer dans le noir">rétroéclairage</abbr>, batterie rechargeable,
            format clapet protégeant l'écran. C'est <strong>le modèle recommandé</strong> pour 95% des joueurs.
        </p>

        <p style="margin-bottom: 1rem; color: var(--text-secondary);">
            <strong>Attention</strong> : Il existe deux versions de la <abbr title="Special - Version améliorée">SP</abbr> :
        </p>

        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">
                <strong><abbr title="Advance Game System">AGS</abbr>-001 (frontlit)</strong> : Premier modèle avec éclairage frontal. Correct mais contraste moyen.
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong><abbr title="Advance Game System">AGS</abbr>-101 (backlit)</strong> : Modèle rare avec <abbr title="Écran éclairé par l'arrière pour jouer dans le noir">rétroéclairage</abbr> véritable. Écran magnifique, +30-50€ plus cher.
            </li>
        </ul>

        <p style="margin-bottom: 1.5rem;">
            Si vous trouvez une <abbr title="Advance Game System">AGS</abbr>-101 à bon prix, foncez. Sinon, une <abbr title="Advance Game System">AGS</abbr>-001 fera très bien l'affaire.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;"><abbr title="Game Boy Advance">GBA</abbr> Micro : La pièce de collection</h2>

        <p style="margin-bottom: 1.5rem;">
            Ultra compacte (10 cm), écran impeccable, design premium. La Micro est magnifique mais
            perd la rétrocompatibilité <abbr title="Game Boy">GB</abbr>/<abbr title="Game Boy Color">GBC</abbr>. Prix élevé (150-250€) réservé aux
            collectionneurs ou fans du format ultra-portable. Pas recommandé comme première <abbr title="Game Boy Advance">GBA</abbr>.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">🌍 Région Lock : Toutes les Game Boy sont region-free</h2>

        <div style="background: #d1fae5; color: #065f46; padding: 1.5rem; margin: 2rem 0; border-radius: var(--radius);">
            <p style="margin-bottom: 1rem;"><strong>✅ Excellente nouvelle : TOUTES les consoles Game Boy sont region-free !</strong></p>
            <ul style="list-style: disc; padding-left: 2rem; margin: 0;">
                <li style="margin-bottom: 0.5rem;">Game Boy (1989), Game Boy Color, <strong>Game Boy Advance</strong>, GBA SP, GBA Micro</li>
                <li style="margin-bottom: 0.5rem;">Versions chinoises (iQue) incluses</li>
                <li style="margin-bottom: 0.5rem;">Vous pouvez acheter une console japonaise et jouer aux jeux européens/américains sans problème</li>
                <li style="margin-bottom: 0.5rem;">Les jeux japonais sont souvent <strong>30-50% moins chers</strong> sur eBay.jp/Yahoo Auctions</li>
            </ul>
        </div>

        <h3 style="margin-top: 1.5rem; margin-bottom: 1rem;">💰 Avantages de l'import japonais</h3>

        <ul style="list-style: disc; padding-left: 2rem; margin-bottom: 1.5rem;">
            <li style="margin-bottom: 0.5rem;">
                <strong>Prix :</strong> GBA SP japonaise ~50-70€ vs 80-120€ version PAL
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>État :</strong> Culture japonaise = consoles mieux entretenues, moins de rayures
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>Coloris exclusifs :</strong> Famicom Edition (NES rouge/blanc), Pearl Blue, Pearl Pink jamais sortis en Europe
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>Jeux compatibles :</strong> Pokémon, Mario, Zelda fonctionnent parfaitement (même si texte en japonais)
            </li>
            <li style="margin-bottom: 0.5rem;">
                <strong>Risque :</strong> Notices en japonais uniquement, mais qui lit les notices en 2026 ? 😄
            </li>
        </ul>

        <p style="margin-bottom: 2rem; padding: 1rem; background: var(--bg-card); border-left: 3px solid var(--accent-primary); border-radius: var(--radius);">
            <strong>💡 Astuce</strong> : Recherchez "ゲームボーイアドバンスSP" (Game Boy Advance SP en japonais) sur eBay.jp pour trouver des offres 40-50€ en excellent état.
        </p>

        <h2 style="margin-top: 2.5rem; margin-bottom: 1rem; border-bottom: 2px solid var(--border); padding-bottom: 0.5rem;">Notre recommandation</h2>

        <p style="margin-bottom: 1rem;">
            <strong>Pour jouer</strong> : Game Boy Advance <abbr title="Special - Version améliorée">SP</abbr> (<abbr title="Advance Game System">AGS</abbr>-001 ou 101). Budget {{ $avgPrice ?? '80-120' }}€.
            C'est le meilleur compromis confort/prix/fonctionnalités.
        </p>

        <p style="margin-bottom: 2rem;">
            <strong>Budget serré</strong> : <abbr title="Game Boy Advance">GBA</abbr> classique + lampe externe. Économisez 30-40€ mais
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


@section('scripts')
@if(isset($faqSchema))
<!-- Schema.org FAQ Structured Data -->
<script type="application/ld+json">
@json($faqSchema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
</script>
@endif
@endsection
