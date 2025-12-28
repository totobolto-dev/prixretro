<?php

namespace App\Filament\Resources\Consoles;

use App\Filament\Resources\Consoles\Pages\CreateConsole;
use App\Filament\Resources\Consoles\Pages\EditConsole;
use App\Filament\Resources\Consoles\Pages\ListConsoles;
use App\Filament\Resources\Consoles\Schemas\ConsoleForm;
use App\Filament\Resources\Consoles\Tables\ConsolesTable;
use App\Models\Console;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ConsoleResource extends Resource
{
    protected static ?string $model = Console::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ConsoleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ConsolesTable::configure($table);
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
            'index' => ListConsoles::route('/'),
            'create' => CreateConsole::route('/create'),
            'edit' => EditConsole::route('/{record}/edit'),
        ];
    }
}
