<?php

namespace App\Console\Commands;

use App\Models\Console;
use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ReorganizeConsoleStructure extends Command
{
    protected $signature = 'console:reorganize';
    protected $description = 'Reorganize console structure - split GBA SP/Micro and DS models into separate consoles';

    public function handle()
    {
        $this->info('Starting console reorganization...');

        // 1. Create Game Boy Advance SP
        $gbaSp = Console::create([
            'name' => 'Game Boy Advance SP',
            'slug' => 'game-boy-advance-sp',
            'description' => 'Nintendo Game Boy Advance SP',
            'search_term' => 'game boy advance sp gba sp',
        ]);
        $this->info("Created console: {$gbaSp->name}");

        // Move SP variants to new console
        $spVariants = Variant::where('console_id', 2) // GBA console ID
            ->where('name', 'like', 'SP %')
            ->get();

        foreach ($spVariants as $variant) {
            $newName = str_replace('SP ', '', $variant->name);
            $variant->update([
                'console_id' => $gbaSp->id,
                'name' => $newName,
                'slug' => Str::slug($newName),
                'full_slug' => 'game-boy-advance-sp/' . Str::slug($newName),
            ]);
            $this->info("  Moved: {$variant->name}");
        }

        // 2. Create Game Boy Advance Micro
        $gbaMicro = Console::create([
            'name' => 'Game Boy Advance Micro',
            'slug' => 'game-boy-advance-micro',
            'description' => 'Nintendo Game Boy Advance Micro',
            'search_term' => 'game boy advance micro gba micro',
        ]);
        $this->info("Created console: {$gbaMicro->name}");

        // Move Micro variants to new console
        $microVariants = Variant::where('console_id', 2) // GBA console ID
            ->where('name', 'like', 'Micro %')
            ->get();

        foreach ($microVariants as $variant) {
            $newName = str_replace('Micro ', '', $variant->name);
            $variant->update([
                'console_id' => $gbaMicro->id,
                'name' => $newName,
                'slug' => Str::slug($newName),
                'full_slug' => 'game-boy-advance-micro/' . Str::slug($newName),
            ]);
            $this->info("  Moved: {$variant->name}");
        }

        // 3. Create Nintendo DS Lite
        $dsLite = Console::create([
            'name' => 'Nintendo DS Lite',
            'slug' => 'nintendo-ds-lite',
            'description' => 'Nintendo DS Lite',
            'search_term' => 'nintendo ds lite nds lite',
        ]);
        $this->info("Created console: {$dsLite->name}");

        // Move DS Lite variants
        $dsLiteVariants = Variant::where('console_id', 3) // DS console ID
            ->where('name', 'like', 'DS Lite %')
            ->get();

        foreach ($dsLiteVariants as $variant) {
            $newName = str_replace('DS Lite ', '', $variant->name);
            $variant->update([
                'console_id' => $dsLite->id,
                'name' => $newName,
                'slug' => Str::slug($newName),
                'full_slug' => 'nintendo-ds-lite/' . Str::slug($newName),
            ]);
            $this->info("  Moved: {$variant->name}");
        }

        // 4. Create Nintendo DSi
        $dsi = Console::create([
            'name' => 'Nintendo DSi',
            'slug' => 'nintendo-dsi',
            'description' => 'Nintendo DSi',
            'search_term' => 'nintendo dsi',
        ]);
        $this->info("Created console: {$dsi->name}");

        // Move DSi variants (not XL)
        $dsiVariants = Variant::where('console_id', 3) // DS console ID
            ->where('name', 'like', 'DSi %')
            ->where('name', 'not like', 'DSi XL %')
            ->get();

        foreach ($dsiVariants as $variant) {
            $newName = str_replace('DSi ', '', $variant->name);
            $variant->update([
                'console_id' => $dsi->id,
                'name' => $newName,
                'slug' => Str::slug($newName),
                'full_slug' => 'nintendo-dsi/' . Str::slug($newName),
            ]);
            $this->info("  Moved: {$variant->name}");
        }

        // 5. Create Nintendo DSi XL
        $dsiXl = Console::create([
            'name' => 'Nintendo DSi XL',
            'slug' => 'nintendo-dsi-xl',
            'description' => 'Nintendo DSi XL',
            'search_term' => 'nintendo dsi xl',
        ]);
        $this->info("Created console: {$dsiXl->name}");

        // Move DSi XL variants
        $dsiXlVariants = Variant::where('console_id', 3) // DS console ID
            ->where('name', 'like', 'DSi XL %')
            ->get();

        foreach ($dsiXlVariants as $variant) {
            $newName = str_replace('DSi XL ', '', $variant->name);
            $variant->update([
                'console_id' => $dsiXl->id,
                'name' => $newName,
                'slug' => Str::slug($newName),
                'full_slug' => 'nintendo-dsi-xl/' . Str::slug($newName),
            ]);
            $this->info("  Moved: {$variant->name}");
        }

        $this->newLine();
        $this->info('✅ Console reorganization complete!');

        // Show summary
        $this->newLine();
        $this->info('Summary:');
        Console::with('variants')->get()->each(function($c) {
            $this->line("- {$c->name}: {$c->variants->count()} variants");
        });

        return 0;
    }
}
