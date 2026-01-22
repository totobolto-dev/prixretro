<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ $metaDescription ?? 'PrixRetro - Tracker de prix pour le rétrogaming' }}">
    <meta name="keywords" content="retrogaming, prix, occasion, nintendo, game boy">
    <meta name="author" content="PrixRetro - Prix du Rétrogaming">
    <meta name="robots" content="index, follow">

    <!-- Google AdSense Verification -->
    <meta name="google-adsense-account" content="ca-pub-2791408282004471">

    <!-- Open Graph -->
    <meta property="og:title" content="@yield('title', 'PrixRetro - Tracker Prix Retrogaming')">
    <meta property="og:description" content="{{ $metaDescription ?? 'Prix et historique des ventes de consoles retrogaming' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="PrixRetro">
    <meta property="og:image" content="{{ asset('images/prixretro-logo.png') }}">
    <meta property="og:locale" content="fr_FR">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title', 'PrixRetro - Tracker Prix Retrogaming')">
    <meta name="twitter:description" content="{{ $metaDescription ?? 'Prix et historique des ventes de consoles retrogaming' }}">
    <meta name="twitter:image" content="{{ asset('images/prixretro-logo.png') }}">

    <title>@yield('title', 'PrixRetro - Tracker Prix Retrogaming')</title>

    <!-- Canonical URL - Force HTTPS -->
    <link rel="canonical" href="https://www.prixretro.com{{ request()->getPathInfo() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4QPNVF0BRW"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-4QPNVF0BRW');

        function trackEbayClick(variant) {
            gtag('event', 'click', {
                'event_category': 'affiliate',
                'event_label': 'ebay_' + variant,
                'value': 1
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="/styles.css">

    @yield('head')
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="/" class="logo">
                    <img src="/images/prixretro-logo.png" alt="PrixRetro" class="logo-image">
                </a>
                <nav>
                    <a href="/">Accueil</a>
                    <a href="/tendances">Tendances</a>
                    <a href="/guides">Guides</a>
                    @auth
                        <a href="{{ route('collection.index') }}">Ma Collection</a>
                    @endauth
                </nav>
            </div>
        </div>
    </header>

    @yield('content')

    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} PrixRetro - Tracker de prix pour le rétrogaming</p>
            <p>Prix moyens basés sur les ventes eBay récentes. Données mises à jour quotidiennement.</p>
            <p style="font-size: 0.8rem; color: #6b7280; margin-top: 0.5rem;">
                Ce site contient des liens affiliés Amazon et eBay. Nous touchons une petite commission si vous achetez via ces liens, sans surcoût pour vous.
            </p>
        </div>
    </footer>

    @yield('scripts')
</body>
</html>
