<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;

class MarketTrendsController extends Controller
{
    public function index()
    {
        // Get all variants with enough data to calculate trends
        $variants = Variant::with('console')
            ->whereHas('listings', function ($query) {
                $query->where('status', 'approved');
            }, '>=', 10)
            ->get();

        $trends = [];

        foreach ($variants as $variant) {
            $listings = $variant->approvedListings()
                ->orderBy('sold_date', 'desc')
                ->get();

            if ($listings->count() < 10) {
                continue;
            }

            // Calculate 30-day trend
            $recentDate = now()->subDays(30);
            $olderDate = now()->subDays(60);

            $recentListings = $listings->where('sold_date', '>=', $recentDate);
            $olderListings = $listings->whereBetween('sold_date', [$olderDate, $recentDate]);

            if ($recentListings->count() < 3 || $olderListings->count() < 3) {
                continue;
            }

            $recentAvg = $recentListings->avg('price');
            $olderAvg = $olderListings->avg('price');

            if ($recentAvg && $olderAvg && $olderAvg > 0) {
                $changePercentage = (($recentAvg - $olderAvg) / $olderAvg) * 100;

                $trends[] = [
                    'variant' => $variant,
                    'console' => $variant->console,
                    'current_avg' => $recentAvg,
                    'previous_avg' => $olderAvg,
                    'change_percentage' => $changePercentage,
                    'change_amount' => $recentAvg - $olderAvg,
                    'total_sales' => $listings->count(),
                    'recent_sales' => $recentListings->count(),
                ];
            }
        }

        // Sort by absolute percentage change
        usort($trends, function ($a, $b) {
            return abs($b['change_percentage']) <=> abs($a['change_percentage']);
        });

        // Split into gainers and losers
        $gainers = array_filter($trends, fn($t) => $t['change_percentage'] > 0);
        usort($gainers, fn($a, $b) => $b['change_percentage'] <=> $a['change_percentage']);
        $topGainers = array_slice($gainers, 0, 10);

        $losers = array_filter($trends, fn($t) => $t['change_percentage'] < 0);
        usort($losers, fn($a, $b) => $a['change_percentage'] <=> $b['change_percentage']);
        $topLosers = array_slice($losers, 0, 10);

        // Calculate overall market stats
        $totalVariants = count($trends);
        $avgChange = $totalVariants > 0 ? array_sum(array_column($trends, 'change_percentage')) / $totalVariants : 0;
        $gainersCount = count($gainers);
        $losersCount = count($losers);

        $marketStats = [
            'total_variants' => $totalVariants,
            'avg_change' => $avgChange,
            'gainers_count' => $gainersCount,
            'losers_count' => $losersCount,
            'gainers_percentage' => $totalVariants > 0 ? ($gainersCount / $totalVariants) * 100 : 0,
        ];

        $metaDescription = "Tendances du marché retrogaming : découvrez les consoles dont les prix augmentent ou baissent. Analyse de {$totalVariants} variantes avec données sur 60 jours.";

        return view('trends.index', compact('topGainers', 'topLosers', 'marketStats', 'metaDescription'));
    }
}
