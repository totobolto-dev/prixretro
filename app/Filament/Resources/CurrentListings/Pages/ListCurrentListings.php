<?php

namespace App\Filament\Resources\CurrentListings\Pages;

use App\Filament\Resources\CurrentListings\CurrentListingResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListCurrentListings extends ListRecords
{
    protected static string $resource = CurrentListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_scraped')
                ->label('Import Scraped Data')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('file')
                        ->label('JSON File')
                        ->acceptedFileTypes(['application/json'])
                        ->required()
                        ->helperText('Upload current_listings_*.json from GitHub Actions'),
                ])
                ->action(function (array $data): void {
                    $filePath = $data['file'];

                    if (!$filePath) {
                        Notification::make()
                            ->title('Error')
                            ->body('No file uploaded')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Filament stores uploads in storage/app/public by default
                    // We need to use the public disk
                    $fullPath = Storage::disk('public')->path($filePath);

                    if (!file_exists($fullPath)) {
                        Notification::make()
                            ->title('Error')
                            ->body("File not found: {$filePath}")
                            ->danger()
                            ->send();
                        return;
                    }

                    // Read and parse JSON directly
                    $jsonContent = file_get_contents($fullPath);
                    $data = json_decode($jsonContent, true);

                    if (!$data) {
                        Notification::make()
                            ->title('Error')
                            ->body('Invalid JSON file')
                            ->danger()
                            ->send();
                        return;
                    }

                    // Import directly without using command
                    $imported = 0;
                    $skipped = 0;

                    foreach ($data as $consoleSlug => $consoleData) {
                        foreach ($consoleData['listings'] as $item) {
                            if (\App\Models\CurrentListing::where('item_id', $item['item_id'])->exists()) {
                                $skipped++;
                                continue;
                            }

                            \App\Models\CurrentListing::create([
                                'variant_id' => null,
                                'item_id' => $item['item_id'],
                                'title' => $item['title'],
                                'price' => $item['price'],
                                'url' => $item['url'],
                                'status' => 'pending',
                                'is_sold' => false,
                                'last_seen_at' => now(),
                            ]);

                            $imported++;
                        }
                    }

                    // Clean up uploaded file
                    Storage::disk('public')->delete($filePath);

                    Notification::make()
                        ->title('Import Complete')
                        ->body("✅ Imported: {$imported} | Skipped: {$skipped}")
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
