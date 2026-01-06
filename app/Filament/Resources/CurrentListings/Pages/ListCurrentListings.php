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

                    // Try multiple possible storage locations
                    $possiblePaths = [
                        Storage::disk('public')->path($filePath),
                        Storage::disk('local')->path($filePath),
                        storage_path('app/livewire-tmp/' . $filePath),
                        storage_path('app/public/' . $filePath),
                    ];

                    $fullPath = null;
                    foreach ($possiblePaths as $path) {
                        if (file_exists($path)) {
                            $fullPath = $path;
                            break;
                        }
                    }

                    if (!$fullPath) {
                        Notification::make()
                            ->title('Error')
                            ->body("File not found. Tried: " . basename($filePath))
                            ->danger()
                            ->send();
                        return;
                    }

                    // Read and parse JSON directly
                    $jsonContent = file_get_contents($fullPath);
                    $jsonData = json_decode($jsonContent, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Notification::make()
                            ->title('Error')
                            ->body('Invalid JSON: ' . json_last_error_msg())
                            ->danger()
                            ->send();
                        @unlink($fullPath);
                        return;
                    }

                    if (!$jsonData || !is_array($jsonData)) {
                        Notification::make()
                            ->title('Error')
                            ->body('JSON file is empty or not an array')
                            ->danger()
                            ->send();
                        @unlink($fullPath);
                        return;
                    }

                    // Import directly without using command
                    $imported = 0;
                    $skipped = 0;

                    foreach ($jsonData as $consoleSlug => $consoleData) {
                        if (!isset($consoleData['listings']) || !is_array($consoleData['listings'])) {
                            continue;
                        }

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
                    @unlink($fullPath);

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
