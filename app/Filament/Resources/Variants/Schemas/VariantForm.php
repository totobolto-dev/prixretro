<?php

namespace App\Filament\Resources\Variants\Schemas;

use App\Models\Console;
use App\Models\Variant;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (callable $set, callable $get, ?int $state) {
                        // Auto-update full_slug when console changes
                        if ($state && $get('slug')) {
                            $console = Console::find($state);
                            if ($console) {
                                $set('full_slug', $console->slug . '/' . $get('slug'));
                            }
                        }
                    }),
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (callable $set, callable $get, ?string $state) {
                        // Auto-generate slug from name, removing console name if present
                        if ($state && $get('console_id')) {
                            $console = Console::find($get('console_id'));
                            if ($console) {
                                $slug = Variant::generateSlugFromName($state, $console->name);
                                $set('slug', $slug);
                                $set('full_slug', $console->slug . '/' . $slug);
                            }
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->helperText('Auto-generated from name. Edit manually if needed.'),
                TextInput::make('full_slug')
                    ->required()
                    ->helperText('Auto-generated. Edit manually if needed.'),
                TextInput::make('search_term')
                    ->label('Custom Search Term')
                    ->helperText('Optional: Custom eBay search term (e.g., "Game Boy Color Violet Transparent" for French color names). Will be used for fetching listings.')
                    ->placeholder('Leave empty to use console + variant name'),
                TagsInput::make('blacklist_terms')
                    ->label('Variant Blacklist')
                    ->helperText('Optional: Strings to filter out from eBay results (e.g., " sp " to exclude "Game Boy Advance SP" when fetching "Game Boy Advance"). Include spaces for exact matching.')
                    ->placeholder('Add blacklist term...')
                    ->separator(','),
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
