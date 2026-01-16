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

        return view('home', compact('consoles', 'latestSales', 'priceRecords'));
    }

    public function show(Console $console)
    {
        $console->load(['variants' => function ($query) {
            $query->withCount(['listings' => function ($q) {
                $q->where('status', 'approved');
            }]);
        }]);

        // Generate auto description
        $autoDescription = ConsoleDescriptionGenerator::generate($console);

        return view('console.show', compact('console', 'autoDescription'));
    }
}
