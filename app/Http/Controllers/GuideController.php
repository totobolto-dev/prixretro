<?php

namespace App\Http\Controllers;

use App\Models\Console;

class GuideController extends Controller
{
    public function index()
    {
        $guides = [
            [
                'slug' => 'guide-achat-game-boy-color',
                'title' => 'Guide d\'achat Game Boy Color - Comment choisir sa variante',
                'description' => 'Découvrez les meilleures variantes de Game Boy Color à acheter en 2026, les prix moyens et les pièges à éviter.',
                'console' => 'Game Boy Color',
                'image' => '/images/guides/gbc-guide.jpg'
            ],
            [
                'slug' => 'ps-vita-occasion-guide',
                'title' => 'PS Vita d\'occasion - Pièges à éviter et meilleures affaires',
                'description' => 'Guide complet pour acheter une PS Vita d\'occasion sans se faire avoir. Prix, variantes et points de vigilance.',
                'console' => 'PS Vita',
                'image' => '/images/guides/psvita-guide.jpg'
            ],
            [
                'slug' => 'guide-game-boy-advance',
                'title' => 'Game Boy Advance - Quelle édition pour débuter la collection',
                'description' => 'SP, Micro ou classique ? Découvrez quelle Game Boy Advance choisir selon votre budget et vos préférences.',
                'console' => 'Game Boy Advance',
                'image' => '/images/guides/gba-guide.jpg'
            ],
            [
                'slug' => 'reperer-console-retrogaming-contrefaite',
                'title' => 'Comment repérer une console retrogaming contrefaite',
                'description' => 'Les signes qui ne trompent pas pour identifier une fausse console, des cartouches piratées et des accessoires non-officiels.',
                'console' => 'Général',
                'image' => '/images/guides/fake-detection.jpg'
            ],
            [
                'slug' => 'meilleures-consoles-retro-2026',
                'title' => 'Meilleures consoles retro à acheter en 2026',
                'description' => 'Notre sélection des consoles retrogaming qui offrent le meilleur rapport qualité-prix entre 50€ et 200€.',
                'console' => 'Général',
                'image' => '/images/guides/best-consoles.jpg'
            ],
            [
                'slug' => 'authentifier-console-retrogaming',
                'title' => 'Authentifier une console retrogaming : guide technique avancé',
                'description' => 'Apprenez à détecter les fausses consoles, identifier les reshells et vérifier l\'authenticité des composants avec nos techniques d\'experts.',
                'console' => 'Général',
                'image' => '/images/guides/authentication.jpg'
            ],
            [
                'slug' => 'nettoyer-console-retro-jaunie',
                'title' => 'Comment nettoyer et blanchir une console retrogaming jaunie',
                'description' => 'Méthode Retr0bright, précautions de sécurité et prévention du jaunissement. Guide complet pour restaurer vos consoles.',
                'console' => 'Général',
                'image' => '/images/guides/cleaning.jpg'
            ],
            [
                'slug' => 'pourquoi-prix-gba-ont-explose',
                'title' => 'Pourquoi les prix de la Game Boy Advance ont explosé',
                'description' => 'Analyse détaillée de la hausse +200% des prix GBA entre 2019 et 2026. Nostalgie, écran rétroéclairé, modding et émulation.',
                'console' => 'Game Boy Advance',
                'image' => '/images/guides/gba-analysis.jpg'
            ],
            [
                'slug' => 'investir-consoles-retrogaming',
                'title' => 'Investir dans les consoles retrogaming : ROI et perspectives 2026',
                'description' => 'Quelles consoles acheter pour un bon retour sur investissement ? Analyse du marché 2019-2026 et prédictions pour les 5 prochaines années.',
                'console' => 'Général',
                'image' => '/images/guides/investment.jpg'
            ],
        ];

        $metaDescription = "Guides d'achat pour consoles retrogaming d'occasion. Conseils d'experts, analyses de prix et recommandations pour bien acheter.";

        return view('guides.index', compact('guides', 'metaDescription'));
    }

    public function showGameBoyColorGuide()
    {
        $console = Console::where('slug', 'game-boy-color')->first();

        // Get price data for variants with 20+ sales
        $variantPrices = [];
        if ($console) {
            foreach ($console->variants as $variant) {
                $listings = $variant->listings()->where('status', 'approved')->get();
                if ($listings->count() >= 20) {
                    $variantPrices[$variant->slug] = round($listings->avg('price'));
                }
            }
        }

        $metaDescription = "Guide d'achat complet Game Boy Color 2026 : meilleures variantes, prix moyens, pièges à éviter. Analyse de 100+ ventes pour acheter malin.";

        return view('guides.game-boy-color', compact('console', 'metaDescription', 'variantPrices'));
    }

    public function showPSVitaGuide()
    {
        $console = Console::where('slug', 'ps-vita')->first();

        // Get price data
        $avgPrice = null;
        if ($console) {
            $allListings = $console->variants()->with('listings')->get()->flatMap->listings->where('status', 'approved');
            if ($allListings->count() >= 20) {
                $avgPrice = round($allListings->avg('price'));
            }
        }

        $metaDescription = "PS Vita d'occasion : guide pour éviter les pièges. Prix des différents modèles, points de vigilance et meilleures affaires en 2026.";

        return view('guides.ps-vita', compact('console', 'metaDescription', 'avgPrice'));
    }

    public function showGameBoyAdvanceGuide()
    {
        $console = Console::where('slug', 'game-boy-advance')->first();

        // Get price data
        $avgPrice = null;
        if ($console) {
            $allListings = $console->variants()->with('listings')->get()->flatMap->listings->where('status', 'approved');
            if ($allListings->count() >= 20) {
                $avgPrice = round($allListings->avg('price'));
            }
        }

        $metaDescription = "Game Boy Advance : quel modèle choisir en 2026 ? Comparatif GBA, SP et Micro avec prix moyens et recommandations pour débuter votre collection.";

        return view('guides.game-boy-advance', compact('console', 'metaDescription', 'avgPrice'));
    }

    public function showFakeDetectionGuide()
    {
        $metaDescription = "Comment repérer les fausses consoles retrogaming et cartouches piratées. Guide complet avec photos et points de vigilance pour acheter serein.";

        return view('guides.fake-detection', compact('metaDescription'));
    }

    public function showBestConsoles2026()
    {
        // Get consoles with price data (20+ sales)
        $consolesWithData = Console::where('is_active', true)
            ->with(['variants.listings' => function($query) {
                $query->where('status', 'approved');
            }])
            ->get()
            ->mapWithKeys(function($console) {
                $allListings = $console->variants->flatMap->listings;
                $count = $allListings->count();

                if ($count >= 20) {
                    return [$console->slug => [
                        'avg_price' => round($allListings->avg('price')),
                        'count' => $count
                    ]];
                }
                return [];
            });

        $metaDescription = "Top 10 des meilleures consoles retrogaming à acheter en 2026 entre 50€ et 200€. Sélection basée sur l'analyse de milliers de ventes réelles.";

        return view('guides.best-consoles-2026', compact('consolesWithData', 'metaDescription'));
    }

    public function showAuthenticationGuide()
    {
        $metaDescription = "Guide technique avancé pour authentifier une console retrogaming. Numéros de série, vis tri-wing, reshells, composants internes et éditions limitées.";

        return view('guides.authentifier-console-retrogaming', compact('metaDescription'));
    }

    public function showCleaningGuide()
    {
        $metaDescription = "Comment nettoyer et blanchir le plastique jauni des consoles retrogaming. Méthode Retr0bright, risques, précautions et prévention du jaunissement.";

        return view('guides.nettoyer-console-retro-jaunie', compact('metaDescription'));
    }

    public function showGBAPriceAnalysis()
    {
        $metaDescription = "Analyse complète de la hausse +200% des prix Game Boy Advance (2019-2026). Nostalgie, écran rétroéclairé, modding, émulation et prédictions de marché.";

        return view('guides.pourquoi-prix-gba-ont-explose', compact('metaDescription'));
    }

    public function showInvestmentGuide()
    {
        $metaDescription = "Investir dans les consoles retrogaming : ROI, meilleures valeurs (GBA SP, PS Vita, Dreamcast), pièges à éviter et stratégies de revente en 2026.";

        return view('guides.investir-consoles-retrogaming', compact('metaDescription'));
    }
}
