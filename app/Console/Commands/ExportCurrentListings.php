<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ExportCurrentListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:current-listings {--status=pending : Export only listings with this status}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export current listings to JSON for uploading to production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->option('status');

        $this->info('📤 Exporting Current Listings');
        $this->line(str_repeat('=', 60));

        // Get listings grouped by console
        $listings = \App\Models\CurrentListing::with('variant.console')
            ->when($status, fn($q) => $q->where('status', $status))
            ->get();

        if ($listings->isEmpty()) {
            $this->warn('No listings found to export');
            return Command::FAILURE;
        }

        // Group by console slug
        $grouped = [];

        foreach ($listings as $listing) {
            if (!$listing->variant || !$listing->variant->console) {
                continue; // Skip listings without variant
            }

            $consoleSlug = $listing->variant->console->slug;
            $consoleName = $listing->variant->console->name;

            if (!isset($grouped[$consoleSlug])) {
                $grouped[$consoleSlug] = [
                    'console_name' => $consoleName,
                    'total_listings' => 0,
                    'listings' => [],
                    'scraped_at' => now()->toIso8601String(),
                ];
            }

            $grouped[$consoleSlug]['listings'][] = [
                'item_id' => $listing->item_id,
                'title' => $listing->title,
                'price' => (float) $listing->price,
                'url' => $listing->url,
                'image_url' => '',
                'scraped_at' => $listing->created_at->toIso8601String(),
            ];

            $grouped[$consoleSlug]['total_listings']++;
        }

        // Save to file
        $filename = 'current_listings_export_' . now()->format('Ymd_His') . '.json';
        $filePath = 'exports/' . $filename;

        \Illuminate\Support\Facades\Storage::put($filePath, json_encode($grouped, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $fullPath = \Illuminate\Support\Facades\Storage::path($filePath);

        $this->newLine();
        $this->info("✅ Export complete!");
        $this->info("📁 File: {$fullPath}");
        $this->info("📊 Total listings: {$listings->count()}");
        $this->info("🎮 Consoles: " . count($grouped));

        $this->newLine();
        $this->line('Upload this file via /admin/current-listings → Import Scraped Data');

        return Command::SUCCESS;
    }
}
