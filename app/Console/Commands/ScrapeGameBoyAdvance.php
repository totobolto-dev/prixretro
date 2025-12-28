<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class ScrapeGameBoyAdvance extends Command
{
    protected $signature = 'scrape:gba {--max-pages=50 : Maximum pages to scrape}';
    protected $description = 'Scrape Game Boy Advance sold listings from eBay.fr';

    public function handle()
    {
        $this->info('🎮 Scraping Game Boy Advance listings from eBay.fr...');

        $maxPages = $this->option('max-pages');
        $scriptPath = base_path('scrapers/ebay_scraper.py');

        if (!file_exists($scriptPath)) {
            $this->error('Scraper script not found: ' . $scriptPath);
            return 1;
        }

        $this->info("Max pages: {$maxPages}");
        $this->newLine();

        try {
            $result = Process::path(base_path('scrapers'))
                ->timeout(600)
                ->run([
                    'python3',
                    'ebay_scraper.py',
                    'game boy advance',
                    'gba',
                    '--max-pages',
                    $maxPages
                ]);

            if ($result->successful()) {
                $this->info('✅ Scraping completed successfully');
                $this->info("Output saved to: storage/app/scraped_data_gba.json");
                $this->newLine();
                $this->info('Next steps:');
                $this->info('  1. Run: php artisan import:scraped storage/app/scraped_data_gba.json');
                $this->info('  2. Review listings in admin panel');
                $this->info('  3. Approve/reject items');
                $this->info('  4. Sync to production');

                return 0;
            } else {
                $this->error('Scraping failed:');
                $this->error($result->errorOutput());
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Error running scraper: ' . $e->getMessage());
            return 1;
        }
    }
}
