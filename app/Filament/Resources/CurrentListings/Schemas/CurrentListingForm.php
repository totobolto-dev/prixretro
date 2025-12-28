<?php

namespace App\Filament\Resources\CurrentListings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CurrentListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('variant_id')
                    ->relationship('variant', 'name')
                    ->required(),
                TextInput::make('item_id')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Textarea::make('url')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_sold')
                    ->required(),
                DateTimePicker::make('last_seen_at'),
            ]);
    }
}
