<?php

namespace App\Console\Commands;

use App\Models\Console;
use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportConsoles extends Command
{
    protected $signature = 'import:consoles {--fresh : Wipe existing data before import}';

    protected $description = 'Import consoles and variants from legacy-python/config_multiconsole.json';

    public function handle()
    {
        $configPath = base_path('legacy-python/config_multiconsole.json');

        if (!File::exists($configPath)) {
            $this->error("Config file not found: {$configPath}");
            return 1;
        }

        $config = json_decode(File::get($configPath), true);

        if ($this->option('fresh')) {
            $this->info('Wiping existing consoles and variants...');
            Variant::query()->delete();
            Console::query()->delete();
        }

        $consoleData = $config['consoles'] ?? [];
        $consoleCount = 0;
        $variantCount = 0;

        foreach ($consoleData as $slug => $data) {
            $this->info("Importing console: {$data['name']}");

            $console = Console::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'short_name' => $data['short_name'] ?? null,
                    'search_term' => $data['search_term'],
                    'ebay_category_id' => $data['ebay_category'] ?? null,
                    'description' => $data['description'] ?? null,
                    'release_year' => $data['release_year'] ?? null,
                    'manufacturer' => 'Nintendo',
                    'is_active' => true,
                    'display_order' => $consoleCount,
                ]
            );

            $consoleCount++;

            $variants = $data['variants'] ?? [];
            foreach ($variants as $variantSlug => $variantData) {
                $fullSlug = $slug . '/' . $variantSlug;

                Variant::updateOrCreate(
                    ['full_slug' => $fullSlug],
                    [
                        'console_id' => $console->id,
                        'slug' => $variantSlug,
                        'name' => $variantData['name'],
                        'search_terms' => $variantData['search_terms'] ?? [],
                        'image_filename' => null,
                        'rarity_level' => null,
                        'region' => 'EU',
                        'is_special_edition' => str_contains(strtolower($variantData['name']), 'edition'),
                    ]
                );

                $variantCount++;
                $this->info("  - Imported variant: {$variantData['name']}");
            }
        }

        $this->newLine();
        $this->info("Import complete!");
        $this->info("Consoles imported: {$consoleCount}");
        $this->info("Variants imported: {$variantCount}");

        return 0;
    }
}
