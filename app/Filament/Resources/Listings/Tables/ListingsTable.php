<?php

namespace App\Filament\Resources\Listings\Tables;

use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;

class ListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('variant.name')
                    ->searchable(),
                TextColumn::make('item_id')
                    ->searchable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('price')
                    ->money()
                    ->sortable(),
                TextColumn::make('sold_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('condition')
                    ->searchable(),
                TextColumn::make('source')
                    ->searchable(),
                IconColumn::make('is_outlier')
                    ->boolean(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),
                SelectFilter::make('variant')
                    ->relationship('variant', 'name')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
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
                            $output = Artisan::output();

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

                            // Parse results
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
                    ->label('Import Raw Data (for sorting)')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->color('warning')
                    ->form([
                        Select::make('file')
                            ->label('File')
                            ->options([
                                'scraped_data_ds.json' => 'Nintendo DS (raw)',
                                'scraped_data_gbc.json' => 'Game Boy Color (raw)',
                                'scraped_data_gba.json' => 'Game Boy Advance (raw)',
                            ])
                            ->required()
                            ->helperText('Import raw unsorted data. You will need to classify items in Sort Listings page.'),
                    ])
                    ->action(function (array $data) {
                        $file = $data['file'];
                        $filePath = storage_path("app/{$file}");

                        if (!file_exists($filePath)) {
                            Notification::make()
                                ->title('Import Failed')
                                ->body("File not found: {$file}")
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            Artisan::call('import:raw', ['file' => $filePath]);
                            $output = Artisan::output();

                            // Parse results
                            preg_match('/Would import: (\d+)/', $output, $imported);
                            preg_match('/Skipped: (\d+)/', $output, $skipped);

                            $importedCount = $imported[1] ?? 0;
                            $skippedCount = $skipped[1] ?? 0;

                            Notification::make()
                                ->title('Import Completed')
                                ->body("Imported {$importedCount} unclassified listings. Visit Sort Listings page to classify them.")
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
                Action::make('sync_to_production')
                    ->label('Sync to Production')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Sync Approved Listings to Production')
                    ->modalDescription('This will sync all approved listings from the last 30 days to the production CloudDB.')
                    ->modalSubmitActionLabel('Sync Now')
                    ->action(function () {
                        try {
                            Artisan::call('sync:production');
                            $output = Artisan::output();

                            // Parse output to show results
                            preg_match('/Synced (\d+) new listings/', $output, $synced);
                            preg_match('/Skipped (\d+) existing/', $output, $skipped);

                            $syncedCount = $synced[1] ?? 0;
                            $skippedCount = $skipped[1] ?? 0;

                            Notification::make()
                                ->title('Sync Completed')
                                ->body("Synced {$syncedCount} new listings, skipped {$skippedCount} existing.")
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
            ])
            ->bulkActions([
                BulkAction::make('approve')
                    ->label('Approve Selected')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $record->update([
                                'status' => 'approved',
                                'reviewed_at' => now(),
                            ]);
                        });

                        Notification::make()
                            ->title('Listings Approved')
                            ->body("Approved {$records->count()} listings")
                            ->success()
                            ->send();
                    }),
                BulkAction::make('reject')
                    ->label('Reject Selected')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            $record->update([
                                'status' => 'rejected',
                                'reviewed_at' => now(),
                            ]);
                        });

                        Notification::make()
                            ->title('Listings Rejected')
                            ->body("Rejected {$records->count()} listings")
                            ->danger()
                            ->send();
                    }),
            ]);

        return $table;
    }
}
