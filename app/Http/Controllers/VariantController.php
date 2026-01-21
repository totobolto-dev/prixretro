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

        return view('variant.show', compact(
            'variant',
            'statistics',
            'statsByCompleteness',
            'recentListings',
            'chartData',
            'autoDescription',
            'metaDescription',
            'schemaData'
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
