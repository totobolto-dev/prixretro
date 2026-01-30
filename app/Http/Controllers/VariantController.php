<?php

namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\Variant;
use App\Services\VariantDescriptionGenerator;
use Illuminate\Support\Facades\DB;

class VariantController extends Controller
{
    public function show(Console $console, string $variant)
    {
        $variant = $console->variants()->where('slug', $variant)->firstOrFail();
        $variant->load('console');

        // Social proof: Count collectors tracking this variant
        $collectorsCount = \App\Models\UserCollection::where('variant_id', $variant->id)->count();

        // Social proof: Recently added (last 7 days)
        $recentlyAddedCount = \App\Models\UserCollection::where('variant_id', $variant->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $listings = $variant->approvedListings()
            ->orderBy('sold_date', 'desc')
            ->get();

        $prices = $listings->pluck('price')->sort()->values();

        $statistics = [
            'count' => $listings->count(),
            'avg_price' => $prices->avg(),
            'min_price' => $prices->min(),
            'max_price' => $prices->max(),
            'median_price' => $this->calculateMedian($prices),
        ];

        // Calculate statistics by completeness type
        $statsByCompleteness = [];
        foreach (['loose', 'cib', 'sealed'] as $completeness) {
            $filtered = $listings->where('completeness', $completeness);
            if ($filtered->count() >= 5) {
                $filteredPrices = $filtered->pluck('price')->sort()->values();
                $statsByCompleteness[$completeness] = [
                    'count' => $filtered->count(),
                    'avg_price' => $filteredPrices->avg(),
                    'min_price' => $filteredPrices->min(),
                    'max_price' => $filteredPrices->max(),
                ];
            }
        }

        $recentListings = $listings->take(10);

        $chartData = $this->prepareChartData($listings);

        // Calculate price trend (last 30 days vs previous 30 days)
        $priceTrend = null;
        if ($listings->count() >= 10) {
            $recentDate = now()->subDays(30);
            $olderDate = now()->subDays(60);

            $recentAvg = $listings->where('sold_date', '>=', $recentDate)->avg('price');
            $olderAvg = $listings->whereBetween('sold_date', [$olderDate, $recentDate])->avg('price');

            if ($recentAvg && $olderAvg && $olderAvg > 0) {
                $priceTrend = [
                    'percentage' => round((($recentAvg - $olderAvg) / $olderAvg) * 100, 1),
                    'direction' => $recentAvg > $olderAvg ? 'up' : 'down',
                    'recentAvg' => round($recentAvg),
                    'olderAvg' => round($olderAvg),
                ];
            }
        }

        // Best time to buy insight
        $buyingInsight = null;
        if ($statistics['count'] >= 20) {
            $avgPrice = $statistics['avg_price'];
            $currentAvg = $listings->take(5)->avg('price');

            if ($currentAvg < $avgPrice * 0.9) {
                $buyingInsight = "Les prix sont actuellement en dessous de la moyenne. C'est un bon moment pour acheter.";
            } elseif ($currentAvg > $avgPrice * 1.1) {
                $buyingInsight = "Les prix sont actuellement au-dessus de la moyenne. Attendez une meilleure opportunité.";
            } else {
                $buyingInsight = "Les prix sont stables autour de la moyenne du marché.";
            }
        }

        // Generate auto description
        $autoDescription = VariantDescriptionGenerator::generate($variant, $statistics);

        // Build meta description
        $metaDescription = $statistics['count'] > 0
            ? "Prix moyen {$variant->display_name}: " . number_format($statistics['avg_price'], 0) . "€ ({$statistics['count']} ventes). Historique et meilleures offres eBay."
            : "{$variant->display_name} - Suivez les prix d'occasion sur eBay.";

        // Build Schema.org structured data
        $schemaData = null;
        if ($statistics['count'] > 0) {
            $schemaData = [
                'product' => [
                    '@context' => 'https://schema.org/',
                    '@type' => 'Product',
                    'name' => $variant->display_name,
                    'description' => $autoDescription,
                    'brand' => [
                        '@type' => 'Brand',
                        'name' => explode(' ', $variant->console->name)[0]
                    ],
                    'category' => 'Consoles de jeux vidéo',
                    'offers' => [
                        '@type' => 'AggregateOffer',
                        'availability' => 'https://schema.org/PreOrder',
                        'priceCurrency' => 'EUR',
                        'lowPrice' => number_format($statistics['min_price'], 2, '.', ''),
                        'highPrice' => number_format($statistics['max_price'], 2, '.', ''),
                        'offerCount' => $statistics['count']
                    ]
                ],
                'breadcrumb' => [
                    '@context' => 'https://schema.org/',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => 'Accueil',
                            'item' => url('/')
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => $variant->console->name,
                            'item' => url('/' . $variant->console->slug)
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $variant->name,
                            'item' => url('/' . $variant->console->slug . '/' . $variant->slug)
                        ]
                    ]
                ]
            ];
        }

        // Map console slugs to guide URLs
        $guideMap = [
            'game-boy-color' => '/guides/guide-achat-game-boy-color',
            'game-boy-advance' => '/guides/guide-achat-game-boy-advance',
            'ps-vita' => '/guides/ps-vita-occasion-guide',
        ];
        $guideUrl = $guideMap[$variant->console->slug] ?? null;

        return view('variant.show', compact(
            'variant',
            'statistics',
            'statsByCompleteness',
            'recentListings',
            'chartData',
            'autoDescription',
            'metaDescription',
            'schemaData',
            'guideUrl',
            'priceTrend',
            'buyingInsight',
            'collectorsCount',
            'recentlyAddedCount'
        ));
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

        // Get all unique dates for x-axis
        $allDates = $sortedListings->pluck('sold_date')->unique()->sort()->values();
        $labels = $allDates->map(fn($date) => $date?->format('d M') ?? 'N/A');

        // Prepare datasets for each condition
        $datasets = [];
        $conditions = [
            'loose' => ['label' => 'Loose', 'color' => '#00d9ff'],
            'cib' => ['label' => 'CIB', 'color' => '#3b82f6'],
            'sealed' => ['label' => 'Sealed', 'color' => '#f59e0b'],
        ];

        foreach ($conditions as $conditionKey => $config) {
            $conditionListings = $sortedListings->where('completeness', $conditionKey);

            // Only include if there's at least one listing
            if ($conditionListings->count() > 0) {
                $data = [];
                $titles = [];
                foreach ($allDates as $date) {
                    $listing = $conditionListings->firstWhere('sold_date', $date);
                    $data[] = $listing ? (float) $listing->price : null;
                    $titles[] = $listing ? $listing->title : null;
                }

                // Calculate trend for this condition (if >= 5 data points)
                $label = $config['label'];
                if ($conditionListings->count() >= 5) {
                    $recentDate = now()->subDays(30);
                    $olderDate = now()->subDays(60);

                    $recentItems = $conditionListings->where('sold_date', '>=', $recentDate);
                    $olderItems = $conditionListings->whereBetween('sold_date', [$olderDate, $recentDate]);

                    if ($recentItems->count() > 0 && $olderItems->count() > 0) {
                        $recentAvg = $recentItems->avg('price');
                        $olderAvg = $olderItems->avg('price');

                        if ($recentAvg && $olderAvg && $olderAvg > 0) {
                            $percentage = round((($recentAvg - $olderAvg) / $olderAvg) * 100, 1);
                            $arrow = $percentage > 0 ? '↑' : '↓';
                            $label .= " {$arrow}" . abs($percentage) . '%';
                        }
                    }
                }

                $datasets[] = [
                    'label' => $label,
                    'data' => $data,
                    'titles' => $titles,
                    'borderColor' => $config['color'],
                    'backgroundColor' => $config['color'] . '20',
                    'spanGaps' => true,
                ];
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
}
