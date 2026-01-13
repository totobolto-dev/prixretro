<?php

namespace App\Filament\Resources\Listings\Pages;

use App\Filament\Resources\Listings\ListingResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class ListListings extends ListRecords
{
    protected static string $resource = ListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('scrape_console')
                ->label('Scrape eBay')
                ->icon('heroicon-o-magnifying-glass')
                ->color('primary')
                ->form([
                    Select::make('console')
                        ->label('Console')
                        ->options([
                            'gbc' => 'Game Boy Color',
                            'gba' => 'Game Boy Advance',
                            'ds' => 'Nintendo DS',
                        ])
                        ->required()
                        ->helperText('Scrape eBay sold listings for this console'),
                ])
                ->action(function (array $data) {
                    $console = $data['console'];

                    try {
                        Artisan::call("scrape:{$console}");

                        Notification::make()
                            ->title('Scraping Started')
                            ->body("eBay scraping for {$console} completed. Check storage/app/scraped_data_{$console}.json")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Scraping Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('import_scraped')
                ->label('Import Scraped Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->form([
                    Select::make('console')
                        ->label('Console')
                        ->options([
                            'gbc' => 'Game Boy Color',
                            'gba' => 'Game Boy Advance',
                            'ds' => 'Nintendo DS (processed)',
                        ])
                        ->required()
                        ->helperText('Import pre-sorted data from storage/app/scraped_data_[console].json'),
                ])
                ->action(function (array $data) {
                    $console = $data['console'];
                    $filePath = storage_path("app/scraped_data_{$console}.json");

                    if (!file_exists($filePath)) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body("File not found: scraped_data_{$console}.json")
                            ->danger()
                            ->send();
                        return;
                    }

                    try {
                        Artisan::call('import:scraped', ['file' => $filePath]);
                        $output = Artisan::output();

                        preg_match('/Would import: (\d+)/', $output, $imported);
                        preg_match('/Skipped: (\d+)/', $output, $skipped);

                        $importedCount = $imported[1] ?? 0;
                        $skippedCount = $skipped[1] ?? 0;

                        Notification::make()
                            ->title('Import Completed')
                            ->body("Imported {$importedCount} new listings, skipped {$skippedCount} existing.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('import_raw')
                ->label('Import Raw Data')
                ->icon('heroicon-o-inbox-arrow-down')
                ->color('warning')
                ->form([
                    Select::make('files')
                        ->label('JSON Files')
                        ->options(function () {
                            $storageDir = storage_path('app');
                            $jsonFiles = File::glob($storageDir . '/*.json');

                            $options = [];
                            foreach ($jsonFiles as $file) {
                                $filename = basename($file);
                                $size = round(filesize($file) / 1024, 2);
                                $options[$filename] = "{$filename} ({$size} KB)";
                            }

                            return $options;
                        })
                        ->multiple()
                        ->required()
                        ->helperText('Select one or more JSON files to bulk import. All files will be imported as unclassified.'),
                ])
                ->action(function (array $data) {
                    $files = $data['files'];
                    $totalImported = 0;
                    $failedFiles = [];

                    foreach ($files as $file) {
                        $filePath = storage_path("app/{$file}");

                        if (!file_exists($filePath)) {
                            $failedFiles[] = $file;
                            continue;
                        }

                        try {
                            Artisan::call('import:raw', ['file' => $filePath]);
                            $output = Artisan::output();

                            preg_match('/Would import: (\d+)/', $output, $imported);
                            $totalImported += (int)($imported[1] ?? 0);
                        } catch (\Exception $e) {
                            $failedFiles[] = "{$file} ({$e->getMessage()})";
                        }
                    }

                    if (count($failedFiles) > 0) {
                        Notification::make()
                            ->title('Import Partially Failed')
                            ->body("Imported {$totalImported} listings. Failed: " . implode(', ', $failedFiles))
                            ->warning()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Bulk Import Completed')
                            ->body("Imported {$totalImported} unclassified listings from " . count($files) . " file(s). Visit Sort Listings to classify.")
                            ->success()
                            ->send();
                    }
                }),
            Action::make('sync_to_production')
                ->label('Sync to Production')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Sync Approved Listings to Production')
                ->modalDescription('Sync all approved listings from last 30 days to production CloudDB.')
                ->modalSubmitActionLabel('Sync Now')
                ->action(function () {
                    try {
                        Artisan::call('sync:production');
                        $output = Artisan::output();

                        preg_match('/Synced (\d+) new listings/', $output, $synced);

                        $syncedCount = $synced[1] ?? 0;

                        Notification::make()
                            ->title('Sync Completed')
                            ->body("Synced {$syncedCount} new listings to production.")
                            ->success()
                            ->send();
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Sync Failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
