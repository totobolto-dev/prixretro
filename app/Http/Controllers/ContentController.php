<?php

namespace App\Http\Controllers;

use App\Models\Console;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;

class ContentController extends Controller
{
    /**
     * Show ranking page for a console (Top 5 Variants by sales volume)
     */
    public function showRanking(Console $console)
    {
        // Get variants with approved listings count and avg price
        $variants = $console->variants()
            ->withCount(['approvedListings'])
            ->with(['approvedListings' => function ($query) {
                $query->select('variant_id', 'price');
            }])
            ->having('approved_listings_count', '>', 0)
            ->orderBy('approved_listings_count', 'desc')
            ->limit(5)
            ->get();

        // Calculate statistics for each variant
        $rankedVariants = $variants->map(function ($variant, $index) {
            $prices = $variant->approvedListings->pluck('price');

            return [
                'rank' => $index + 1,
                'variant' => $variant,
                'sales_count' => $variant->approved_listings_count,
                'avg_price' => $prices->avg(),
                'min_price' => $prices->min(),
                'max_price' => $prices->max(),
            ];
        });

        // Overall console statistics
        $totalSales = $rankedVariants->sum('sales_count');
        $avgConsolePrice = $rankedVariants->avg('avg_price');

        // Build meta description
        $metaDescription = "Classement " . date('Y') . " des variantes {$console->name} les plus vendues en France. Analyse de " . number_format($totalSales) . " ventes eBay réelles avec prix moyens et tendances du marché.";

        return view('content.ranking', compact(
            'console',
            'rankedVariants',
            'totalSales',
            'avgConsolePrice',
            'metaDescription'
        ));
    }
}
