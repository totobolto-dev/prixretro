<?php

namespace App\Filament\Resources\Consoles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ConsolesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn ($record) =>
                        $record->slug .
                        ($record->short_name ? ' • ' . $record->short_name : '')
                    ),
                TextColumn::make('search_term')
                    ->searchable()
                    ->width('200px')
                    ->wrap()
                    ->description(fn ($record) =>
                        ($record->ebay_category_id ? 'eBay: ' . $record->ebay_category_id : '')
                    ),
                TextColumn::make('release_year')
                    ->numeric()
                    ->sortable()
                    ->width('80px'),
                TextColumn::make('manufacturer')
                    ->searchable()
                    ->width('120px'),
                IconColumn::make('is_active')
                    ->boolean()
                    ->width('60px'),
                TextColumn::make('display_order')
                    ->numeric()
                    ->sortable()
                    ->width('80px'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
