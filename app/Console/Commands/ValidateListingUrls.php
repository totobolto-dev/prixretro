<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Services\UrlValidationService;
use Illuminate\Console\Command;

class ValidateListingUrls extends Command
{
    protected $signature = 'listings:validate-urls
                            {--limit= : Maximum number of listings to validate}
                            {--delay=2000 : Delay between requests in milliseconds (default: 2000)}
                            {--only-pending : Only validate listings without validation status}
                            {--auto-reject : Automatically reject listings with invalid URLs}';

    protected $description = 'Validate eBay listing URLs to detect redirects and dead links';

    private UrlValidationService $validationService;

    public function __construct(UrlValidationService $validationService)
    {
        parent::__construct();
        $this->validationService = $validationService;
    }

    public function handle(): int
    {
        $this->info('Starting URL validation...');

        // Build query
        $query = Listing::query();

        if ($this->option('only-pending')) {
            $query->urlNotValidated();
        }

        if ($limit = $this->option('limit')) {
            $query->limit((int) $limit);
        }

        $listings = $query->get();

        if ($listings->isEmpty()) {
            $this->info('No listings to validate.');
            return Command::SUCCESS;
        }

        $this->info("Found {$listings->count()} listings to validate.");

        $delay = (int) $this->option('delay');
        $autoReject = $this->option('auto-reject');

        $stats = [
            'valid' => 0,
            'invalid' => 0,
            'captcha' => 0,
            'error' => 0,
        ];

        $progressBar = $this->output->createProgressBar($listings->count());
        $progressBar->start();

        foreach ($listings as $listing) {
            $result = $this->validationService->validateUrl($listing->url, $listing->item_id);

            // Update listing with validation result
            $listing->update([
                'url_validation_status' => $result['status'],
                'url_redirect_target' => $result['redirect_target'],
                'url_validation_error' => $result['error'],
                'url_validated_at' => now(),
            ]);

            // Auto-reject if requested and URL is invalid
            if ($autoReject && $result['status'] === 'invalid' && $listing->status !== 'rejected') {
                $listing->update([
                    'status' => 'rejected',
                    'reviewed_at' => now(),
                ]);
                $this->newLine();
                $this->warn("Auto-rejected listing #{$listing->id}: {$result['error']}");
            }

            $stats[$result['status']]++;

            $progressBar->advance();

            // Throttle requests to avoid bot detection
            if (!$listings->last()->is($listing)) {
                $this->validationService->throttle($delay);
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->info('Validation completed!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Valid', $stats['valid']],
                ['Invalid (redirects)', $stats['invalid']],
                ['CAPTCHA challenges', $stats['captcha']],
                ['Errors', $stats['error']],
            ]
        );

        if ($stats['invalid'] > 0) {
            $this->warn("\n{$stats['invalid']} listings have invalid URLs (redirects to different items).");

            if (!$autoReject) {
                $this->info('Run with --auto-reject to automatically reject these listings.');
            }
        }

        if ($stats['captcha'] > 0) {
            $this->warn("\n{$stats['captcha']} listings triggered CAPTCHA challenges.");
            $this->info('These listings were not validated. Try again later or increase --delay.');
        }

        return Command::SUCCESS;
    }
}
