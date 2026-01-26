<!DOCTYPE html>
<html lang="fr" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'PrixRetro - Prix des Consoles Rétro d\'Occasion' }}</title>

    <!-- SEO Meta Tags -->
    <meta name="description" content="{{ $description ?? 'Comparez les prix des consoles rétro d\'occasion sur eBay France. Historique des prix, meilleures offres et tendances du marché.' }}">
    <meta name="keywords" content="consoles rétro, prix occasion, Game Boy, PlayStation, Nintendo, Sega, historique prix">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $title ?? 'PrixRetro' }}">
    <meta property="og:description" content="{{ $description ?? 'Comparez les prix des consoles rétro' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?? 'PrixRetro' }}">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Google Analytics -->
    @if(config('services.google_analytics.id'))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_analytics.id') }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ config('services.google_analytics.id') }}');
    </script>
    @endif

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{ $head ?? '' }}
</head>
<body class="bg-bg-primary text-text-primary min-h-screen antialiased">

    <!-- Main Navigation -->
    <x-navbar />

    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-footer />

    {{ $scripts ?? '' }}
</body>
</html>
