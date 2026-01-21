<?php

namespace App\Filament\Resources\Listings\Schemas;

use App\Models\Console;
use App\Models\Variant;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('console_slug')
                    ->label('Console')
                    ->options(Console::orderBy('display_order')->pluck('name', 'slug')->toArray())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn($state, callable $set) => $set('variant_id', null)),
                Select::make('variant_id')
                    ->label('Variant')
                    ->options(function (callable $get) {
                        if (!$get('console_slug')) {
                            return [];
                        }
                        return Variant::query()
                            ->whereHas('console', fn($q) => $q->where('slug', $get('console_slug')))
                            ->pluck('name', 'id')
                            ->toArray();
                    })
                    ->disabled(fn(callable $get) => !$get('console_slug'))
                    ->helperText(fn(callable $get) => !$get('console_slug') ? 'Select a console first' : 'Optional - leave empty for default/original console'),
                TextInput::make('item_id')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                DatePicker::make('sold_date'),
                TextInput::make('item_condition')
                    ->label('État (occasion/neuf)')
                    ->placeholder('Ex: Occasion, Neuf, Used, New'),
                Select::make('completeness')
                    ->label('Complétude')
                    ->options([
                        'loose' => 'Loose (console seule)',
                        'cib' => 'CIB (complet en boîte)',
                        'sealed' => 'Sealed (neuf scellé)',
                    ])
                    ->placeholder('Non défini')
                    ->helperText('Loose = console seule | CIB = complet avec boîte et accessoires | Sealed = neuf jamais ouvert'),
                Textarea::make('url')
                    ->label('URL eBay')
                    ->required()
                    ->rows(2)
                    ->helperText(fn ($record) => $record?->url
                        ? new HtmlString('<a href="' . e($record->url) . '" target="_blank" rel="noopener noreferrer" style="color: rgb(0, 217, 255); text-decoration: underline; font-weight: 600;">→ Ouvrir dans eBay</a>')
                        : 'Enregistrez pour voir le lien'),
                Textarea::make('thumbnail_url')
                    ->columnSpanFull(),
                TextInput::make('source')
                    ->required()
                    ->default('ebay'),
                Toggle::make('is_outlier')
                    ->required(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('reviewed_at'),
            ]);
    }
}
