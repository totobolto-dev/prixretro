<?php

namespace App\Filament\Resources\Consoles\Pages;

use App\Filament\Resources\Consoles\ConsoleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsoles extends ListRecords
{
    protected static string $resource = ConsoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
