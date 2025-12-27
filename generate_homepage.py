#!/usr/bin/env python3
"""
Generate multi-console homepage showing GBC and GBA
Shows featured variants per console with a clean layout
"""

import json

def load_gbc_data():
    """Load GBC data"""
    with open('scraped_data.json', 'r') as f:
        return json.load(f)

def load_gba_data():
    """Load GBA data"""
    with open('scraped_data_gba.json', 'r') as f:
        return json.load(f)

def generate_homepage():
    """Generate multi-console homepage"""

    # Load data
    gbc_data = load_gbc_data()
    gba_data = load_gba_data()

    # Select top variants per console (by listing count)
    def get_top_variants(data, limit=6):
        """Get top variants sorted by count"""
        sorted_variants = sorted(
            data.items(),
            key=lambda x: x[1].get('count', x[1].get('listing_count', 0)),
            reverse=True
        )
        return sorted_variants[:limit]

    gbc_top = get_top_variants(gbc_data, 6)
    gba_top = get_top_variants(gba_data, 6)

    html = f"""<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Prix et historique du retrogaming Nintendo. Game Boy Color et Game Boy Advance - Données basées sur les ventes réelles eBay.">
    <meta name="keywords" content="Game Boy Color, Game Boy Advance, prix, vente, occasion, retrogaming, Nintendo, console, collection, GBC, GBA">
    <meta name="author" content="PrixRetro - Prix du Rétrogaming">
    <meta name="robots" content="index, follow">

    <!-- Google AdSense -->
    <meta name="google-adsense-account" content="ca-pub-2791408282004471">

    <!-- Open Graph -->
    <meta property="og:title" content="PrixRetro - Prix Game Boy Color & Advance">
    <meta property="og:description" content="Découvrez le prix réel du marché pour Game Boy Color et Game Boy Advance. Données basées sur les ventes réelles eBay.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.prixretro.com/">
    <meta property="og:site_name" content="PrixRetro - Tracker Prix Retrogaming">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="PrixRetro - Prix Game Boy Color & Advance">
    <meta name="twitter:description" content="Prix réel du marché pour consoles Nintendo rétro">

    <title>PrixRetro - Prix Game Boy Color & Advance | Ventes eBay</title>

    <link rel="canonical" href="https://www.prixretro.com/">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {{
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "PrixRetro",
        "description": "Tracker de prix retrogaming basé sur les ventes réelles eBay",
        "url": "https://www.prixretro.com/",
        "publisher": {{
            "@type": "Organization",
            "name": "PrixRetro"
        }}
    }}
    </script>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4QPNVF0BRW"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){{dataLayer.push(arguments);}}
        gtag('js', new Date());
        gtag('config', 'G-4QPNVF0BRW');
    </script>

    <link rel="stylesheet" href="/styles.css">
    <style>
        .hero {{
            text-align: center;
            padding: 3rem 0;
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
        }}

        .hero h1 {{
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }}

        .tagline {{
            font-size: 1.1rem;
            color: var(--text-secondary);
            max-width: 700px;
            margin: 0 auto 1rem;
        }}

        .console-section {{
            padding: 3rem 0;
            border-bottom: 1px solid var(--border);
        }}

        .console-section:last-child {{
            border-bottom: none;
        }}

        .console-header {{
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }}

        .console-header h2 {{
            font-size: 1.8rem;
            color: var(--text-primary);
        }}

        .console-stats {{
            font-size: 0.9rem;
            color: var(--text-muted);
        }}

        .variant-grid {{
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }}

        .variant-card {{
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            border-radius: 3px;
            padding: 1.5rem;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }}

        .variant-card:hover {{
            border-color: var(--accent);
            transform: translateY(-2px);
        }}

        .variant-name {{
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }}

        .variant-stats {{
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-top: 1rem;
        }}

        .price {{
            color: var(--success);
            font-weight: 600;
        }}

        .view-all {{
            text-align: center;
        }}

        .view-all-btn {{
            display: inline-block;
            padding: 0.8rem 2rem;
            background: var(--accent);
            color: var(--bg-primary);
            text-decoration: none;
            border-radius: 3px;
            font-weight: 600;
            transition: opacity 0.2s;
        }}

        .view-all-btn:hover {{
            opacity: 0.9;
        }}
    </style>
</head>
<body>
    <div class="container">
        <div class="hero">
            <h1>PrixRetro</h1>
            <p class="tagline">
                Prix du marché pour Game Boy Color et Game Boy Advance<br>
                <span style="color: var(--text-muted); font-size: 0.95rem;">Basé sur {len(gbc_data) + len(gba_data)} variantes • {sum(v.get('count', v.get('listing_count', 0)) for v in list(gbc_data.values()) + list(gba_data.values()))} ventes analysées</span>
            </p>
        </div>

        <!-- Game Boy Color Section -->
        <div class="console-section">
            <div class="console-header">
                <h2>🎮 Game Boy Color</h2>
                <div class="console-stats">{len(gbc_data)} variantes • {sum(v.get('listing_count', v.get('count', 0)) for v in gbc_data.values())} ventes</div>
            </div>

            <div class="variant-grid">"""

    # Add GBC variants
    for variant_key, variant_data in gbc_top:
        name = variant_data.get('variant_name', variant_data.get('name', variant_key))
        avg_price = variant_data.get('avg_price', variant_data.get('stats', {}).get('avg_price', 0))
        count = variant_data.get('listing_count', variant_data.get('count', 0))

        html += f"""
                <a href="/game-boy-color-{variant_key}.html" class="variant-card">
                    <div class="variant-name">{name}</div>
                    <div class="variant-stats">
                        <span class="price">{int(avg_price)}€</span>
                        <span>{count} ventes</span>
                    </div>
                </a>"""

    html += f"""
            </div>

            <div class="view-all">
                <a href="#gbc-all" class="view-all-btn">Voir les {len(gbc_data)} variantes GBC</a>
            </div>
        </div>

        <!-- Game Boy Advance Section -->
        <div class="console-section">
            <div class="console-header">
                <h2>🎮 Game Boy Advance</h2>
                <div class="console-stats">{len(gba_data)} variantes • {sum(v.get('count', 0) for v in gba_data.values())} ventes</div>
            </div>

            <div class="variant-grid">"""

    # Add GBA variants
    for variant_key, variant_data in gba_top:
        name = variant_data.get('name', variant_key)
        avg_price = variant_data.get('avg_price', 0)
        count = variant_data.get('count', 0)

        html += f"""
                <a href="/game-boy-advance-{variant_key}.html" class="variant-card">
                    <div class="variant-name">{name}</div>
                    <div class="variant-stats">
                        <span class="price">{int(avg_price)}€</span>
                        <span>{count} ventes</span>
                    </div>
                </a>"""

    html += f"""
            </div>

            <div class="view-all">
                <a href="#gba-all" class="view-all-btn">Voir les {len(gba_data)} variantes GBA</a>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; padding: 2rem 0; color: var(--text-muted); font-size: 0.9rem;">
            <p>Données mises à jour quotidiennement depuis eBay.fr</p>
            <p style="margin-top: 0.5rem;">PrixRetro © 2024-2025 • Prix du Retrogaming</p>
        </div>
    </div>
</body>
</html>"""

    # Save homepage
    with open('output/index.html', 'w', encoding='utf-8') as f:
        f.write(html)

    print("✅ Homepage generated with both GBC and GBA!")
    print(f"   - Game Boy Color: {len(gbc_data)} variants")
    print(f"   - Game Boy Advance: {len(gba_data)} variants")
    print(f"   - Total: {len(gbc_data) + len(gba_data)} variants")

if __name__ == '__main__':
    generate_homepage()
