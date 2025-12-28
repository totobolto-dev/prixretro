<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Process;

class Scraper extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationGroup = 'Data Management';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.scraper';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'console' => 'gba',
            'max_items' => 100,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('console')
                    ->label('Console')
                    ->options([
                        'gbc' => 'Game Boy Color',
                        'gba' => 'Game Boy Advance',
                        'ds' => 'Nintendo DS',
                    ])
                    ->required(),
                TextInput::make('max_items')
                    ->label('Max Items to Scrape')
                    ->numeric()
                    ->default(100)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function runScraper()
    {
        $data = $this->form->getState();
        $console = $data['console'];
        $maxItems = $data['max_items'];

        Notification::make()
            ->title('Scraper Started')
            ->body("Scraping {$console} with max {$maxItems} items...")
            ->info()
            ->send();

        try {
            // Run the appropriate scraper script
            $scriptPath = base_path("legacy-python/scraper_{$console}.py");

            if (!file_exists($scriptPath)) {
                throw new \Exception("Scraper script not found: {$scriptPath}");
            }

            $result = Process::path(base_path('legacy-python'))
                ->timeout(600)
                ->run("python3 scraper_{$console}.py --max-items={$maxItems}");

            if ($result->successful()) {
                Notification::make()
                    ->title('Scraper Completed')
                    ->body('Scraped data saved. Import it to review in Listings.')
                    ->success()
                    ->send();
            } else {
                throw new \Exception($result->errorOutput());
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Scraper Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
