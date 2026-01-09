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

        $recentListings = $listings->take(10);

        $chartData = $this->prepareChartData($listings);

        // Generate auto description
        $autoDescription = VariantDescriptionGenerator::generate($variant, $statistics);

        return view('variant.show', compact(
            'variant',
            'statistics',
            'recentListings',
            'chartData',
            'autoDescription'
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
