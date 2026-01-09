<?php

namespace App\Http\Controllers;

use App\Models\Console;
use App\Services\ConsoleDescriptionGenerator;

class ConsoleController extends Controller
{
    public function index()
    {
        $consoles = Console::with(['variants' => function ($query) {
            $query->withCount('listings');
        }])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->get();

        return view('home', compact('consoles'));
    }

    public function show(Console $console)
    {
        $console->load(['variants' => function ($query) {
            $query->withCount('listings');
        }]);

        // Generate auto description
        $autoDescription = ConsoleDescriptionGenerator::generate($console);

        return view('console.show', compact('console', 'autoDescription'));
    }
}
