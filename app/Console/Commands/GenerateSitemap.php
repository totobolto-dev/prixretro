<?php

namespace App\Console\Commands;

use App\Models\Console;
use App\Models\Variant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';
    protected $description = 'Generate sitemap.xml with all variant pages';

    public function handle()
    {
        $this->info('🗺️  Generating sitemap...');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Homepage
        $xml .= $this->addUrl('https://www.prixretro.com/', '1.0', 'daily', now()->toDateString());

        // Guide pages
        $guides = [
            '/guides',
            '/guides/guide-achat-game-boy-color',
            '/guides/ps-vita-occasion-guide',
            '/guides/guide-game-boy-advance',
            '/guides/reperer-console-retrogaming-contrefaite',
            '/guides/meilleures-consoles-retro-2026',
        ];

        foreach ($guides as $guide) {
            $xml .= $this->addUrl(
                "https://www.prixretro.com{$guide}",
                '0.9',
                'monthly',
                now()->toDateString()
            );
        }

        // Get all consoles with their variants
        $consoles = Console::with('variants')->where('is_active', true)->get();

        $variantCount = 0;
        $rankingCount = 0;

        foreach ($consoles as $console) {
            // Console page
            $xml .= $this->addUrl(
                "https://www.prixretro.com/{$console->slug}",
                '0.9',
                'weekly',
                now()->toDateString()
            );

            // Ranking page (if console has 3+ variants with data)
            $variantsWithData = $console->variants()
                ->whereHas('listings', function($q) {
                    $q->where('status', 'approved');
                })
                ->count();

            if ($variantsWithData >= 3) {
                $xml .= $this->addUrl(
                    "https://www.prixretro.com/{$console->slug}/classement",
                    '0.8',
                    'weekly',
                    now()->toDateString()
                );
                $rankingCount++;
            }

            // Variant pages
            foreach ($console->variants as $variant) {
                $xml .= $this->addUrl(
                    "https://www.prixretro.com/{$console->slug}/{$variant->slug}",
                    '0.7',
                    'weekly',
                    now()->toDateString()
                );
                $variantCount++;
            }
        }

        $xml .= '</urlset>';

        // Write to public/sitemap.xml
        $path = public_path('sitemap.xml');
        File::put($path, $xml);

        $guideCount = count($guides);
        $totalUrls = 1 + $guideCount + $consoles->count() + $rankingCount + $variantCount;

        $this->info("✅ Sitemap generated successfully!");
        $this->info("📊 Total URLs: {$totalUrls}");
        $this->info("   - Homepage: 1");
        $this->info("   - Guide pages: {$guideCount}");
        $this->info("   - Console pages: " . $consoles->count());
        $this->info("   - Ranking pages: {$rankingCount}");
        $this->info("   - Variant pages: {$variantCount}");
        $this->info("📍 Location: {$path}");

        return Command::SUCCESS;
    }

    private function addUrl(string $loc, string $priority, string $changefreq, string $lastmod): string
    {
        return "  <url>" . PHP_EOL .
               "    <loc>{$loc}</loc>" . PHP_EOL .
               "    <lastmod>{$lastmod}</lastmod>" . PHP_EOL .
               "    <changefreq>{$changefreq}</changefreq>" . PHP_EOL .
               "    <priority>{$priority}</priority>" . PHP_EOL .
               "  </url>" . PHP_EOL;
    }
}
