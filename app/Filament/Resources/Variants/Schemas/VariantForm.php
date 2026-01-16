<?php

namespace App\Filament\Resources\Variants\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class VariantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('console_id')
                    ->relationship('console', 'name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('full_slug')
                    ->required(),
                TextInput::make('search_terms'),
                FileUpload::make('image_filename')
                    ->image(),
                TextInput::make('rarity_level'),
                TextInput::make('region'),
                Toggle::make('is_special_edition')
                    ->required(),
                Toggle::make('is_default')
                    ->label('Default Variant')
                    ->helperText('Default variants display as the console name (e.g., "Nintendo 64" instead of "Nintendo 64 Black")')
                    ->default(false),
            ]);
    }
}
