<?php

namespace App\Console\Commands;

use App\Models\Console;
use Illuminate\Console\Command;

class AddNintendo3DSFamily extends Command
{
    protected $signature = 'console:add-3ds-family';
    protected $description = 'Add Nintendo 2DS and 3DS family consoles';

    public function handle()
    {
        $this->info('Adding Nintendo 2DS and 3DS family consoles...');

        $consoles = [
            [
                'name' => 'Nintendo 2DS',
                'slug' => 'nintendo-2ds',
                'description' => 'Nintendo 2DS',
                'search_term' => 'nintendo 2ds',
            ],
            [
                'name' => 'Nintendo 3DS',
                'slug' => 'nintendo-3ds',
                'description' => 'Nintendo 3DS',
                'search_term' => 'nintendo 3ds',
            ],
            [
                'name' => 'Nintendo 3DS XL',
                'slug' => 'nintendo-3ds-xl',
                'description' => 'Nintendo 3DS XL',
                'search_term' => 'nintendo 3ds xl',
            ],
            [
                'name' => 'New Nintendo 3DS',
                'slug' => 'new-nintendo-3ds',
                'description' => 'New Nintendo 3DS',
                'search_term' => 'new nintendo 3ds',
            ],
            [
                'name' => 'New Nintendo 3DS XL',
                'slug' => 'new-nintendo-3ds-xl',
                'description' => 'New Nintendo 3DS XL',
                'search_term' => 'new nintendo 3ds xl',
            ],
            [
                'name' => 'New Nintendo 2DS XL',
                'slug' => 'new-nintendo-2ds-xl',
                'description' => 'New Nintendo 2DS XL',
                'search_term' => 'new nintendo 2ds xl',
            ],
        ];

        foreach ($consoles as $consoleData) {
            $existing = Console::where('slug', $consoleData['slug'])->first();

            if ($existing) {
                $this->warn("  ⏭️  {$consoleData['name']} already exists");
                continue;
            }

            Console::create($consoleData);
            $this->info("  ✅ Created: {$consoleData['name']}");
        }

        $this->newLine();
        $this->info('✅ Console addition complete!');

        $this->newLine();
        $this->info('Summary of all consoles:');
        Console::orderBy('name')->get()->each(function($c) {
            $this->line("  - {$c->name}: {$c->variants->count()} variants");
        });

        return 0;
    }
}
