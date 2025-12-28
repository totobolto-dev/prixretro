<?php

namespace App\Filament\Resources\CurrentListings;

use App\Filament\Resources\CurrentListings\Pages\CreateCurrentListing;
use App\Filament\Resources\CurrentListings\Pages\EditCurrentListing;
use App\Filament\Resources\CurrentListings\Pages\ListCurrentListings;
use App\Filament\Resources\CurrentListings\Schemas\CurrentListingForm;
use App\Filament\Resources\CurrentListings\Tables\CurrentListingsTable;
use App\Models\CurrentListing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CurrentListingResource extends Resource
{
    protected static ?string $model = CurrentListing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CurrentListingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CurrentListingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCurrentListings::route('/'),
            'create' => CreateCurrentListing::route('/create'),
            'edit' => EditCurrentListing::route('/{record}/edit'),
        ];
    }
}
