<?php

namespace App\Filament\Resources\Consoles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ConsoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('short_name'),
                TextInput::make('search_term')
                    ->required(),
                TextInput::make('ebay_category_id'),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('release_year')
                    ->numeric(),
                TextInput::make('manufacturer'),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('display_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }
}
