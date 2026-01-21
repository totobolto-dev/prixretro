@extends('layout')

@section('title', 'Page non trouvée - PrixRetro')

@section('content')
<div class="container">
    <div style="text-align: center; padding: 4rem 2rem;">
        <h1 style="font-size: 8rem; font-weight: 900; color: var(--accent-primary); margin: 0; line-height: 1;">404</h1>
        <h2 style="font-size: 2rem; margin: 1rem 0; color: var(--text-primary);">Page non trouvée</h2>
        <p style="font-size: 1.1rem; color: var(--text-muted); margin-bottom: 3rem;">
            Désolé, la page que vous recherchez n'existe pas ou a été déplacée.
        </p>

        <div style="margin: 3rem 0;">
            <h3 style="font-size: 1.3rem; margin-bottom: 1.5rem; color: var(--text-primary);">Peut-être cherchiez-vous :</h3>
            <div class="console-grid">
                @php
                    $suggestedConsoles = \App\Models\Console::where('is_active', true)
                        ->inRandomOrder()
                        ->limit(6)
                        ->get();
                @endphp
                @foreach($suggestedConsoles as $console)
                <a href="/{{ $console->slug }}" class="console-card">
                    <span class="console-name">{{ $console->name }}</span>
                </a>
                @endforeach
            </div>
        </div>

        <div style="margin-top: 3rem;">
            <a href="/" class="cta-button">← Retour à l'accueil</a>
        </div>
    </div>
</div>
@endsection
