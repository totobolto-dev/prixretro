<?php

namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\Listing;
use App\Services\ConsoleDescriptionGenerator;

class ConsoleController extends Controller
{
    public function index()
    {
        $consoles = Console::with(['variants' => function ($query) {
            $query->withCount(['listings' => function ($q) {
                $q->where('status', 'approved');
            }]);
        }])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        // Latest sales (last 20 approved listings)
        $latestSales = Listing::with(['variant.console'])
            ->where('status', 'approved')
            ->whereNotNull('variant_id')
            ->orderByDesc('sold_date')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Price records per console (top 3 most expensive per console)
        $priceRecords = [];
        foreach ($consoles as $console) {
            $topListings = Listing::with('variant')
                ->whereHas('variant', function ($query) use ($console) {
                    $query->where('console_id', $console->id);
                })
                ->where('status', 'approved')
                ->orderByDesc('price')
                ->limit(3)
                ->get();

            if ($topListings->count() > 0) {
                $priceRecords[$console->slug] = [
                    'console' => $console,
                    'listings' => $topListings
                ];
            }
        }

        // Featured guides (random 3 guides)
        $allGuides = [
            [
                'slug' => 'guide-achat-game-boy-color',
                'title' => 'Guide d\'achat Game Boy Color',
                'description' => 'Découvrez les meilleures variantes à acheter en 2026',
            ],
            [
                'slug' => 'ps-vita-occasion-guide',
                'title' => 'PS Vita d\'occasion',
                'description' => 'Pièges à éviter et meilleures affaires',
            ],
            [
                'slug' => 'guide-game-boy-advance',
                'title' => 'Game Boy Advance',
                'description' => 'Quelle édition pour débuter la collection',
            ],
            [
                'slug' => 'reperer-console-retrogaming-contrefaite',
                'title' => 'Repérer une contrefaçon',
                'description' => 'Signes qui ne trompent pas pour identifier les fausses consoles',
            ],
            [
                'slug' => 'meilleures-consoles-retro-2026',
                'title' => 'Meilleures consoles retro 2026',
                'description' => 'Notre sélection entre 50€ et 200€',
            ],
            [
                'slug' => 'authentifier-console-retrogaming',
                'title' => 'Authentifier une console',
                'description' => 'Guide technique avancé pour vérifier l\'authenticité',
            ],
            [
                'slug' => 'nettoyer-console-retro-jaunie',
                'title' => 'Nettoyer le plastique jauni',
                'description' => 'Méthode Retr0bright et prévention du jaunissement',
            ],
            [
                'slug' => 'pourquoi-prix-gba-ont-explose',
                'title' => 'Prix GBA : analyse de la hausse',
                'description' => 'Pourquoi les Game Boy Advance valent 3x plus cher',
            ],
            [
                'slug' => 'investir-consoles-retrogaming',
                'title' => 'Investir dans le retrogaming',
                'description' => 'ROI et perspectives 2026 pour collectionneurs',
            ],
        ];
        $featuredGuides = collect($allGuides)->random(3);

        // Build meta description
        $totalSales = $consoles->sum(fn($c) => $c->variants->sum('listings_count'));
        $metaDescription = "Suivez les prix du marché des consoles retrogaming d'occasion. Historique de " . number_format($totalSales) . " ventes analysées sur eBay pour Game Boy, PlayStation, Nintendo, Sega et plus.";

        return view('home', compact('consoles', 'latestSales', 'priceRecords', 'featuredGuides', 'metaDescription'));
    }

    public function show(Console $console)
    {
        $console->load(['variants' => function ($query) {
            $query->withCount(['listings' => function ($q) {
                $q->where('status', 'approved');
            }]);
        }]);

        // Get ALL approved listings for ALL variants of this console (aggregate data)
        $allListings = Listing::whereHas('variant', function ($query) use ($console) {
            $query->where('console_id', $console->id);
        })
            ->where('status', 'approved')
            ->orderBy('sold_date', 'desc')
            ->get();

        $prices = $allListings->pluck('price')->sort()->values();

        // Calculate aggregate statistics for the entire console
        $statistics = [
            'count' => $allListings->count(),
            'avg_price' => $prices->avg(),
            'min_price' => $prices->min(),
            'max_price' => $prices->max(),
            'median_price' => $this->calculateMedian($prices),
        ];

        // Recent listings (last 10 across all variants)
        $recentListings = $allListings->take(10);

        // Prepare chart data (price over time for all variants)
        $chartData = $this->prepareChartData($allListings);

        // Generate auto description
        $autoDescription = ConsoleDescriptionGenerator::generate($console);

        // Build meta description
        $metaDescription = $statistics['count'] > 0
            ? "Prix moyen {$console->name}: " . number_format($statistics['avg_price'], 0) . "€ ({$statistics['count']} ventes analysées). Historique complet du marché {$console->name} d'occasion avec graphiques et tendances."
            : "{$console->name} - Suivez les prix d'occasion et l'évolution du marché retrogaming.";

        // Get related consoles (same manufacturer or similar name)
        $relatedConsoles = Console::where('is_active', true)
            ->where('id', '!=', $console->id)
            ->where(function($query) use ($console) {
                // Same manufacturer
                if ($console->manufacturer) {
                    $query->where('manufacturer', $console->manufacturer);
                }
                // Or similar name patterns (e.g., "Game Boy" family)
                $baseName = explode(' ', $console->name)[0] . ' ' . (explode(' ', $console->name)[1] ?? '');
                $query->orWhere('name', 'like', $baseName . '%');
            })
            ->withCount(['variants' => function ($q) {
                $q->whereHas('listings', function($listing) {
                    $listing->where('status', 'approved');
                });
            }])
            ->having('variants_count', '>', 0)
            ->limit(4)
            ->get();

        // Map console slugs to guide URLs
        $guideMap = [
            'game-boy-color' => '/guides/guide-achat-game-boy-color',
            'game-boy-advance' => '/guides/guide-achat-game-boy-advance',
            'ps-vita' => '/guides/ps-vita-occasion-guide',
        ];
        $guideUrl = $guideMap[$console->slug] ?? null;

        return view('console.show', compact('console', 'autoDescription', 'statistics', 'recentListings', 'chartData', 'metaDescription', 'relatedConsoles', 'guideUrl'));
    }

    private function calculateMedian($prices)
    {
        $count = $prices->count();

        if ($count === 0) {
            return null;
        }

        $middle = (int) floor($count / 2);

        if ($count % 2 === 0) {
            return ($prices[$middle - 1] + $prices[$middle]) / 2;
        }

        return $prices[$middle];
    }

    private function prepareChartData($listings)
    {
        // Reverse to show oldest to newest on chart
        $sortedListings = $listings->sortBy('sold_date');

        return [
            'labels' => $sortedListings->map(function ($listing) {
                return $listing->sold_date?->format('d M') ?? 'N/A';
            })->values(),
            'prices' => $sortedListings->map(function ($listing) {
                return (float) $listing->price;
            })->values(),
            'urls' => $sortedListings->pluck('url')->values(),
            'titles' => $sortedListings->pluck('title')->values(),
        ];
    }
}
