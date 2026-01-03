<?php

namespace App\Filament\Resources\CurrentListings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CurrentListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('variant.name')
                    ->searchable()
                    ->width('150px')
                    ->badge()
                    ->color('success'),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->description(fn ($record) =>
                        ($record->price ? number_format($record->price, 2) . '€' : '') .
                        ' • ID: ' . $record->item_id
                    ),
                IconColumn::make('is_sold')
                    ->boolean()
                    ->width('60px'),
                TextColumn::make('last_seen_at')
                    ->dateTime()
                    ->sortable()
                    ->width('150px'),
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
