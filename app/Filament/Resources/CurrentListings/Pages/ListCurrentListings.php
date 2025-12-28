<?php

namespace App\Filament\Resources\CurrentListings\Pages;

use App\Filament\Resources\CurrentListings\CurrentListingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCurrentListings extends ListRecords
{
    protected static string $resource = CurrentListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
