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
            [
                'slug' => 'guide-achat-nintendo-ds',
                'title' => 'Guide d\'achat Nintendo DS - Quelle version choisir en 2026',
                'description' => 'DS, DS Lite, DSi ou DSi XL ? Comparatif complet des 4 modèles Nintendo DS avec prix, avantages et inconvénients pour bien choisir.',
                'console' => 'Nintendo DS',
                'image' => '/images/guides/ds-guide.jpg'
            ],
            [
                'slug' => 'psp-ou-ps-vita-quelle-console-acheter',
                'title' => 'PSP ou PS Vita - Quelle console portable Sony acheter en 2026',
                'description' => 'Comparatif détaillé PSP vs PS Vita : prix, ludothèque, homebrew, écrans. Quel modèle choisir selon votre budget et vos besoins.',
                'console' => 'Sony Portable',
                'image' => '/images/guides/psp-vita.jpg'
            ],
            [
                'slug' => 'tester-console-occasion-avant-achat',
                'title' => 'Comment tester une console d\'occasion avant achat - Checklist 2026',
                'description' => 'Checklist complète en 10 minutes pour tester une console d\'occasion : écran, boutons, lecteur, audio. Ne vous faites plus avoir !',
                'console' => 'Général',
                'image' => '/images/guides/testing.jpg'
            ],
            [
                'slug' => 'estimer-valeur-collection-retrogaming',
                'title' => 'Estimer la valeur de sa collection retrogaming - Guide 2026',
                'description' => 'Méthodes et outils pour évaluer précisément votre collection de consoles retrogaming. Prix par état, rareté et timing de revente optimal.',
                'console' => 'Général',
                'image' => '/images/guides/valuation.jpg'
            ],
            [
                'slug' => 'guide-achat-playstation-1',
                'title' => 'Guide d\'achat PlayStation 1 - Quel modèle choisir en 2026',
                'description' => 'PS1 originale ou PS one ? Comparatif des modèles SCPH, prix moyens et points de vigilance pour acheter la meilleure PlayStation 1.',
                'console' => 'PlayStation 1',
                'image' => '/images/guides/ps1-guide.jpg'
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

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Quel est le prix moyen d\'une Game Boy Color en 2026 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Le prix moyen d\'une Game Boy Color d\'occasion varie de 50€ à 100€ selon l\'état et la variante. Les modèles transparents (Atomic Purple, Teal) sont plus chers que les coloris standards.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quelle est la meilleure variante de Game Boy Color à acheter ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Pour débuter, les variantes standard (Jaune, Bleu, Rouge) offrent le meilleur rapport qualité-prix (50-70€). Pour les collectionneurs, l\'Atomic Purple est iconique mais plus chère (80-100€).'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'La Game Boy Color est-elle region-lock ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Non, la Game Boy Color est totalement region-free. Vous pouvez acheter des consoles japonaises 30-50% moins chères et jouer à tous les jeux européens sans problème.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Que vérifier avant d\'acheter une Game Boy Color d\'occasion ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Vérifiez l\'écran (pas de lignes verticales), la trappe de piles (présence et état des contacts), les boutons (réactivité) et le son. Testez avec un jeu pour confirmer que tout fonctionne.'
                    ]
                ]
            ]
        ];

        return view('guides.game-boy-color', compact('console', 'metaDescription', 'variantPrices', 'faqSchema'));
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

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Quel est le prix d\'une PS Vita d\'occasion en 2026 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Le prix moyen d\'une PS Vita d\'occasion varie de 100€ à 180€. Le modèle Slim (PCH-2000) est généralement 20-30€ plus cher que le modèle original (PCH-1000) grâce à son autonomie supérieure.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Faut-il acheter une PS Vita 1000 ou 2000 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'La PS Vita 2000 (Slim) offre +1h d\'autonomie, un port micro-USB standard et est plus légère. La 1000 a un écran OLED supérieur. Pour jouer en 2026, privilégiez la 2000 pour le confort au quotidien.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quels sont les pièges à éviter en achetant une PS Vita d\'occasion ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Vérifiez l\'écran tactile arrière (souvent défectueux), les joysticks analogiques (drift), et le compte PSN (doit être déconnecté). Évitez les consoles avec burn-in sur l\'écran OLED du modèle 1000.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'La PS Vita est-elle region-lock ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Les jeux physiques PS Vita sont region-free. Cependant, les DLC et jeux dématérialisés sont liés à la région du compte PSN. Une console japonaise peut lire des jeux européens sans problème.'
                    ]
                ]
            ]
        ];

        return view('guides.ps-vita', compact('console', 'metaDescription', 'avgPrice', 'faqSchema'));
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

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Faut-il acheter une GBA, GBA SP ou GBA Micro ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'La GBA SP est le meilleur choix : écran rétroéclairé (AGS-101), batterie rechargeable, format clamshell. La GBA classique nécessite un mod d\'écran. Le Micro est compact mais cher et sans rétrocompatibilité GB/GBC.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quel est le prix moyen d\'une Game Boy Advance en 2026 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Prix moyens 2026 : GBA classique 50-80€, GBA SP AGS-001 (sans backlight) 60-90€, GBA SP AGS-101 (backlight) 120-180€, GBA Micro 100-150€. Les prix ont triplé depuis 2019.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Différence entre GBA SP AGS-001 et AGS-101 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'L\'AGS-001 a un écran frontlit (éclairage devant l\'écran, moins lumineux). L\'AGS-101 a un backlight (rétroéclairage, beaucoup plus confortable). Vérifiez le numéro de série pour identifier le modèle.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'La Game Boy Advance est-elle region-lock ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Non, la GBA est totalement region-free. Les consoles japonaises fonctionnent parfaitement avec les jeux européens et coûtent 30-50% moins cher à l\'import.'
                    ]
                ]
            ]
        ];

        return view('guides.game-boy-advance', compact('console', 'metaDescription', 'avgPrice', 'faqSchema'));
    }

    public function showFakeDetectionGuide()
    {
        $metaDescription = "Comment repérer les fausses consoles retrogaming et cartouches piratées. Guide complet avec photos et points de vigilance pour acheter serein.";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Comment reconnaître une fausse Game Boy ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Vérifiez le plastique (contrefaçons plus brillantes), les vis (Nintendo utilise des vis tri-wing spécifiques), l\'écran (qualité inférieure), et le numéro de série. Les boutons des fakes manquent de précision.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Les cartouches GBA contrefaites fonctionnent-elles ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Oui, mais les sauvegardes peuvent disparaître sans prévenir. Les cartouches fake GBA sont reconnaissables à leur étiquette mal imprimée, PCB différent et absence du sceau Nintendo imprimé sur le circuit.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quelles consoles sont les plus contrefaites ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Les plus contrefaites : GBA SP (surtout AGS-101), Game Boy Pocket, DS Lite. Les contrefaçons viennent principalement de marketplace en ligne. Évitez les prix 30%+ en dessous du marché.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Comment vérifier l\'authenticité d\'une console avant achat ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Demandez des photos du numéro de série, vérifiez le type de vis (tri-wing Nintendo), comparez le poids (fakes plus légers), testez la console avec un jeu original. Méfiez-vous des consoles "neuves" à prix cassé.'
                    ]
                ]
            ]
        ];

        return view('guides.fake-detection', compact('metaDescription', 'faqSchema'));
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

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Quelle est la meilleure console retro pour débuter en 2026 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'La Nintendo DS Lite offre le meilleur rapport qualité-prix : 60-90€, ludothèque immense (DS + GBA), region-free, et console robuste. Alternative : GBA SP si vous préférez le portable pur (80-120€).'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quel budget prévoir pour une console retrogaming ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Budget 2026 : portables 50-150€ (GBC, GBA, DS, PSP), consoles de salon 80-200€ (PS2, GameCube, N64, Dreamcast). Les éditions limitées et consoles neuves coûtent 2-3x plus cher.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quelles consoles retro prennent le plus de valeur ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Top investissement 2026 : GBA SP AGS-101 (+250% depuis 2019), PS Vita (+180%), Dreamcast éditions limitées (+150%). Les consoles region-free portables prennent le plus de valeur.'
                    ]
                ]
            ]
        ];

        return view('guides.best-consoles-2026', compact('consolesWithData', 'metaDescription', 'faqSchema'));
    }

    public function showAuthenticationGuide()
    {
        $metaDescription = "Guide technique avancé pour authentifier une console retrogaming. Numéros de série, vis tri-wing, reshells, composants internes et éditions limitées.";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Comment identifier un reshell (coque modifiée) ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Les reshells ont des couleurs trop vives, plastique plus brillant, raccords imparfaits entre les coques, et vis Phillips au lieu de tri-wing. Ouvrez la console pour vérifier le PCB et le numéro de série interne.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Comment vérifier l\'authenticité d\'une édition limitée ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Vérifiez le numéro de série (format spécifique par édition), comparez les sérigraphies avec des photos officielles, examinez le certificat d\'authenticité, et cross-check le numéro avec les bases de données de collectionneurs.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Que révèlent les numéros de série Nintendo ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Les numéros de série Nintendo indiquent : le modèle exact (AGS-001 vs 101), la date de fabrication, l\'usine de production, et la région. Ils permettent de détecter les incohérences entre la coque et l\'électronique.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Comment reconnaître un écran IPS aftermarket ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Les écrans IPS aftermarket ont des couleurs ultra-saturées, brightness réglable (non-stock), pas de ghosting, et nécessitent souvent un shell modifié. Vérifiez le menu de réglages (ajout non-officiel) et le câble ribbon.'
                    ]
                ]
            ]
        ];

        return view('guides.authentifier-console-retrogaming', compact('metaDescription', 'faqSchema'));
    }

    public function showCleaningGuide()
    {
        $metaDescription = "Comment nettoyer et blanchir le plastique jauni des consoles retrogaming. Méthode Retr0bright, risques, précautions et prévention du jaunissement.";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Pourquoi les consoles retrogaming jaunissent-elles ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Le jaunissement est causé par l\'oxydation des retardateurs de flamme (ABS + BFR) sous l\'effet des UV. La fumée de cigarette et la chaleur accélèrent le processus. Certaines coques jaunissent plus que d\'autres selon le lot de fabrication.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'La méthode Retr0bright fonctionne-t-elle vraiment ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Oui, Retr0bright (peroxyde d\'hydrogène 12% + UV) peut restaurer le plastique blanc. ATTENTION : risques de sur-blanchiment, fragilisation du plastique, et jaunissement qui revient en 1-2 ans sans stockage optimal.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Comment prévenir le jaunissement des consoles ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Stockez vos consoles à l\'abri de la lumière directe (UV), dans une pièce fraîche (<25°C), sans fumée. Utilisez des vitrines avec verre anti-UV pour l\'exposition. Le jaunissement est irréversible mais ralentissable.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Puis-je nettoyer une console sans Retr0bright ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Oui : alcool isopropylique à 70% pour le nettoyage de surface, magic eraser (gomme magique) pour taches légères, désassembler pour nettoyer contacts et PCB. Le nettoyage améliore l\'apparence sans risquer la coque.'
                    ]
                ]
            ]
        ];

        return view('guides.nettoyer-console-retro-jaunie', compact('metaDescription', 'faqSchema'));
    }

    public function showGBAPriceAnalysis()
    {
        $metaDescription = "Analyse complète de la hausse +200% des prix Game Boy Advance (2019-2026). Nostalgie, écran rétroéclairé, modding, émulation et prédictions de marché.";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Pourquoi les prix de la GBA ont-ils triplé depuis 2019 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Convergence de facteurs : génération nostalgique (30-35 ans) avec pouvoir d\'achat, confinement 2020, reconnaissance critique de la bibliothèque GBA, modding IPS accessible, et raréfaction des consoles en bon état.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Les prix GBA vont-ils continuer à monter ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Prédiction 2026-2030 : stabilisation pour GBA classique, hausse continue pour SP AGS-101 (+30-50%) et Micro (+50-80%). Les éditions limitées CIB deviendront inaccessibles (>500€).'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Faut-il acheter une GBA en 2026 ou attendre ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Achetez maintenant si vous voulez jouer. Les prix ne baisseront pas : demande structurelle (nostalgie + qualité ludothèque) et offre qui diminue (vieillissement, casse). Privilégiez SP AGS-101 pour la revente.'
                    ]
                ]
            ]
        ];

        return view('guides.pourquoi-prix-gba-ont-explose', compact('metaDescription', 'faqSchema'));
    }

    public function showInvestmentGuide()
    {
        $metaDescription = "Investir dans les consoles retrogaming : ROI, meilleures valeurs (GBA SP, PS Vita, Dreamcast), pièges à éviter et stratégies de revente en 2026.";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Quelles consoles retrogaming sont un bon investissement en 2026 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Meilleur ROI potentiel : PS Vita OLED CIB (+180% depuis 2019), GBA SP AGS-101 éditions limitées, Dreamcast versions japonaises rares, DS Lite Zelda/Pokémon CIB. Évitez les consoles communes (PS2, GBC standard).'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'CIB ou loose : quel format pour investir ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Privilégiez CIB (Complete In Box) ou Sealed. Le multiplicateur CIB vs loose est de 1.8x en moyenne en 2026, jusqu\'à 3x pour éditions limitées. Les loose plafonnent, les CIB continuent de monter.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quel est le meilleur timing pour revendre une console retrogaming ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Peak de revente : 20-25 ans après sortie (nostalgie maximale). Évitez les releases de remasters officiels (-20-40% sur les prix). Vendez en novembre-décembre (Noël) pour +15-25% de plus-value.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Combien investir dans les consoles retrogaming ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Pour un portefeuille diversifié : budget 2000-5000€ répartis sur 8-12 consoles CIB. ROI moyen 5 ans : +50-150% (meilleur que l\'inflation). Ne dépassez pas 10% de votre épargne totale (actif illiquide).'
                    ]
                ]
            ]
        ];

        return view('guides.investir-consoles-retrogaming', compact('metaDescription', 'faqSchema'));
    }

    public function showNintendoDSGuide()
    {
        $metaDescription = "Guide d'achat Nintendo DS 2026 : comparatif DS/DS Lite/DSi/DSi XL avec prix moyens, avantages et inconvénients. Quel modèle choisir selon votre budget.";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Quelle Nintendo DS acheter en 2026 : DS, DS Lite, DSi ou DSi XL ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'DS Lite est le meilleur choix : rétrocompatibilité GBA, écran lumineux, 60-90€. DSi a des caméras mais pas de slot GBA. DSi XL a un grand écran (5 pouces) idéal pour adultes. Évitez la DS originale (écran trop sombre).'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quel est le prix moyen d\'une Nintendo DS en 2026 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Prix moyens 2026 : DS Lite 60-90€, DSi 50-80€, DSi XL 80-120€. Les éditions limitées (Zelda, Pokémon) valent 2-3x plus cher. Les prix ont augmenté de 40% depuis 2020.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'La Nintendo DS est-elle region-lock ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'La DS et DS Lite sont 100% region-free. Le DSi a un region-lock sur les jeux DSi-enhanced et le DSiWare (environ 20 jeux physiques concernés). Pour 99% de la ludothèque, les DSi japonaises fonctionnent en Europe.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Que vérifier avant d\'acheter une DS d\'occasion ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Vérifiez les charnières (cassent facilement sur DS Lite), l\'écran tactile (testez tous les coins), le son, le slot GBA (si DS Lite), et les boutons L/R (souvent défectueux). Évitez les consoles avec peinture écaillée.'
                    ]
                ]
            ]
        ];

        return view('guides.guide-achat-nintendo-ds', compact('metaDescription', 'faqSchema'));
    }

    public function showPSPVSVitaGuide()
    {
        $metaDescription = "PSP ou PS Vita : comparatif détaillé 2026. Prix, ludothèque, homebrew, écrans. Quel modèle portable Sony choisir selon vos besoins.";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'PSP ou PS Vita : quelle console choisir en 2026 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'PS Vita pour les jeux modernes (écran tactile, graphismes PS3-level) et homebrews avancés. PSP si budget serré (40-70€ vs 100-180€) ou nostalgie années 2000. La Vita lit aussi les jeux PSP via PSN.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quelle est la différence de ludothèque entre PSP et PS Vita ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'PSP : 1900+ jeux (classiques PS1, exclus comme Crisis Core, Monster Hunter). PS Vita : 1500+ jeux natifs + catalogue PSP rétrocompatible. Pour les JRPG et visual novels, Vita est supérieure. Pour émulation PSX, PSP suffit.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quel modèle de PSP ou PS Vita acheter ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'PSP : privilégiez la 3000 (écran meilleur, micro intégré). Vita : modèle 2000 Slim pour autonomie (+1h) et micro-USB, ou 1000 OLED pour qualité d\'image. Évitez PSP Go (pas de UMD) et Vita TV (pas portable).'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Le homebrew est-il plus facile sur PSP ou PS Vita ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'PSP : CFW très simple à installer, émulation NES/SNES/GBA fluide. PS Vita : h-encore² plus complexe mais permet émulation jusqu\'à PS1/N64 et streaming PC. En 2026, la scène homebrew Vita est plus active.'
                    ]
                ]
            ]
        ];

        return view('guides.psp-ou-ps-vita-quelle-console-acheter', compact('metaDescription', 'faqSchema'));
    }

    public function showTestingGuide()
    {
        $metaDescription = "Checklist complète pour tester une console d'occasion avant achat. Écran, boutons, lecteur, audio : ne vous faites plus avoir en 10 minutes chrono.";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Comment tester une console d\'occasion en 10 minutes ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Checklist rapide : 1) Inspection visuelle (fissures, vis manquantes), 2) Allumage et écran (pixels morts, lignes), 3) Tous les boutons, 4) Audio (haut-parleurs et jack), 5) Lecteur (disque/cartouche), 6) Ports (charge, accessoires). Emmenez un jeu de test.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quels sont les défauts cachés les plus courants sur consoles d\'occasion ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Top défauts cachés : joystick drift (DS, PS Vita), charnières fissurées (DS Lite), écran tactile mort (zones non réactives), trappe piles absente (GBC/GBA), lecteur optique défaillant (PS2/GameCube). Testez TOUT avant paiement.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Comment tester l\'écran d\'une console portable d\'occasion ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Lancez un jeu avec fond blanc (menu) pour voir pixels morts, puis fond noir pour backlight bleed. Bougez la console pour détecter lignes verticales (ribbon cable). Testez luminosité max et tactile sur toute surface (DS/Vita).'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Dois-je acheter une console non testée ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Évitez les "non testées" ou "pour pièces" sauf si vous savez réparer. 60-70% nécessitent des réparations coûteuses. Si prix -50% du marché et vous acceptez le risque : OK pour GBA/GBC (réparations simples), KO pour PS2/GameCube (complexe).'
                    ]
                ]
            ]
        ];

        return view('guides.tester-console-occasion-avant-achat', compact('metaDescription', 'faqSchema'));
    }

    public function showValuationGuide()
    {
        $metaDescription = "Comment estimer la valeur de votre collection retrogaming : méthodes de calcul, coefficients par état (Loose/CIB/Sealed), timing de revente optimal.";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Comment calculer la valeur de ma collection retrogaming ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Méthode : 1) Listez chaque console avec son état (Loose/CIB/Sealed), 2) Cherchez les prix sur PrixRetro, eBay sold listings, PriceCharting, 3) Appliquez coefficients d\'état, 4) Soustraire 15-20% pour revente rapide ou viser 100% du marché si patient.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quelle est la différence de prix entre Loose, CIB et Sealed ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Multiplicateurs 2026 : CIB = Loose × 1.8 en moyenne, Sealed = Loose × 2.5-4. Pour éditions limitées : CIB peut atteindre × 3-5. État "Très bon" vaut 20-30% de plus que "Bon". Les rayures d\'écran divisent par 2.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Où vendre ma collection retrogaming au meilleur prix ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Prix décroissants : Enchères eBay (100% valeur marché, 12.8% frais), Vinted/Leboncoin (90-100%, pas de frais, risque arnaques), Reprise boutiques (60-70%, immédiat). Pour collections > 2000€, enchères spécialisées (Heritage, Catawiki).'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Comment augmenter la valeur de ma collection ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Stratégies : Racheter les boîtes manquantes (+80% valeur), nettoyer sans Retr0bright (+10-15%), remplacer coques usées (+20%), documenter provenance (factures, certificats), photographier avant/après restauration, attendre +2-3 ans (marché haussier).'
                    ]
                ]
            ]
        ];

        return view('guides.estimer-valeur-collection-retrogaming', compact('metaDescription', 'faqSchema'));
    }

    public function showPlayStation1Guide()
    {
        $console = Console::where('slug', 'playstation')->first();

        $metaDescription = "Guide d'achat PlayStation 1 2026 : comparatif PS1 originale vs PS one, modèles SCPH, prix moyens et points de vigilance. Quel modèle choisir pour jouer ?";

        $faqSchema = [
            '@context' => 'https://schema.org/',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Quel est le meilleur modèle de PlayStation 1 à acheter ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Pour jouer : SCPH-7502 (fiable, moddable, 50-70€). Pour le design compact : PS one SCPH-102 (60-90€). Pour petit budget : import japonais SCPH-5500 (40-60€). Évitez les SCPH-9002 (protection anti-modchip forte).'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quel est le prix d\'une PlayStation 1 en 2026 ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Prix moyens 2026 : console seule 35-60€, console + accessoires 50-80€, pack complet en boîte 120-200€. PS one SCPH-102 : 60-100€. Éditions limitées : 150-400€.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Comment savoir si le lecteur CD de ma PS1 fonctionne ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'Testez avec plusieurs jeux (CD noir et argenté), vérifiez le lancement, l\'absence du message "disque non reconnu", et lancez des cinématiques FMV qui sollicitent le lecteur. Le lecteur est le point faible n°1 des PS1.'
                    ]
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Quelle différence entre PS1 et PS one ?',
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => 'La PS one (2000) est 45% plus petite, avec alimentation externe et design redessiné. Elle n\'a pas de port parallèle (limite le modding). La PS1 originale est plus robuste mais volumineuse. Performances identiques.'
                    ]
                ]
            ]
        ];

        return view('guides.guide-achat-playstation-1', compact('console', 'metaDescription', 'faqSchema'));
    }
}
