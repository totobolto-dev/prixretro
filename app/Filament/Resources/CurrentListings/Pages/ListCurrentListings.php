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
                    $file = $data['file'];

                    if ($file instanceof TemporaryUploadedFile) {
                        $path = $file->store('imports', 'local');

                        Artisan::call('import:current-listings', ['file' => $path]);
                        $output = Artisan::output();

                        Storage::disk('local')->delete($path);

                        Notification::make()
                            ->title('Import Complete')
                            ->body(trim($output))
                            ->success()
                            ->send();
                    }
                }),
            CreateAction::make(),
        ];
    }
}
