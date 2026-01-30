<?php

namespace App\Filament\Resources\CurrentListings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CurrentListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('variant_id')
                    ->label('Variant')
                    ->relationship('variant', 'name')
                    ->searchable()
                    ->required()
                    ->getOptionLabelFromRecordUsing(fn ($record) =>
                        $record->console->name . ' - ' . $record->name
                    ),
                TextInput::make('item_id')
                    ->label('eBay Item ID')
                    ->required(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('€'),
                TextInput::make('url')
                    ->url()
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'blacklisted' => 'Blacklisted',
                    ])
                    ->default('pending')
                    ->required(),
                Toggle::make('is_sold')
                    ->label('Is Sold')
                    ->default(false),
            ]);
    }
}
