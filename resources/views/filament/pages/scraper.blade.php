<x-filament-panels::page>
    <x-filament::section>
        <x-slot name="heading">
            Run eBay Scraper
        </x-slot>

        <x-slot name="description">
            Scrape eBay sold listings for retro consoles. Data will be saved locally for review before syncing to production.
        </x-slot>

        <form wire:submit="runScraper">
            {{ $this->form }}

            <x-filament::button type="submit" class="mt-4">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                Start Scraping
            </x-filament::button>
        </form>
    </x-filament::section>

    <x-filament::section class="mt-6">
        <x-slot name="heading">
            Workflow
        </x-slot>

        <div class="prose dark:prose-invert max-w-none">
            <ol>
                <li><strong>Scrape:</strong> Run scraper to collect eBay sold listings</li>
                <li><strong>Import:</strong> Import scraped data to local database</li>
                <li><strong>Review:</strong> Go to Listings → Filter by "Pending" → Review and approve/reject</li>
                <li><strong>Sync:</strong> Use "Sync to Production" button to push approved listings live</li>
            </ol>
        </div>
    </x-filament::section>
</x-filament-panels::page>
