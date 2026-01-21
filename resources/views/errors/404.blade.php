@extends('layout')

@section('title', 'Page non trouvée - PrixRetro')

@section('content')
<div class="container">
    <div class="error-page">
        <h1 class="error-code">404</h1>
        <h2 class="error-title">Page non trouvée</h2>
        <p class="error-message">Désolé, la page que vous recherchez n'existe pas ou a été déplacée.</p>

        <div class="error-suggestions">
            <h3>Peut-être cherchiez-vous :</h3>
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

        <div class="error-actions">
            <a href="/" class="cta-button">← Retour à l'accueil</a>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.error-page {
    text-align: center;
    padding: 4rem 2rem;
}

.error-code {
    font-size: 8rem;
    font-weight: 900;
    color: var(--primary);
    margin: 0;
    line-height: 1;
}

.error-title {
    font-size: 2rem;
    margin: 1rem 0;
    color: var(--text);
}

.error-message {
    font-size: 1.1rem;
    color: var(--text-muted);
    margin-bottom: 3rem;
}

.error-suggestions {
    margin: 3rem 0;
}

.error-suggestions h3 {
    font-size: 1.3rem;
    margin-bottom: 1.5rem;
    color: var(--text);
}

.error-actions {
    margin-top: 3rem;
}
</style>
@endpush
