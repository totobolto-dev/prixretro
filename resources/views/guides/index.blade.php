@extends('layout')

@section('title', 'Guides d\'achat Retrogaming | PrixRetro')

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <span>Guides d'achat</span>
    </div>

    <h1 style="margin-bottom: 1rem;">Guides d'achat consoles retrogaming</h1>

    <p style="color: var(--text-secondary); margin-bottom: 3rem; max-width: 800px;">
        Découvrez nos guides pour acheter vos consoles retrogaming d'occasion sans vous faire avoir.
        Analyses de prix basées sur des centaines de ventes réelles, conseils d'experts et pièges à éviter.
    </p>

    <div class="variant-grid">
        @foreach($guides as $guide)
        <a href="/guides/{{ $guide['slug'] }}" class="variant-card">
            <div class="variant-name">{{ $guide['title'] }}</div>
            <div class="variant-stats">
                <span style="color: var(--text-secondary);">{{ $guide['console'] }}</span>
            </div>
            <p style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.5rem;">
                {{ $guide['description'] }}
            </p>
        </a>
        @endforeach
    </div>

    <div class="back-link">
        <a href="/">← Retour à l'accueil</a>
    </div>
</div>
@endsection
