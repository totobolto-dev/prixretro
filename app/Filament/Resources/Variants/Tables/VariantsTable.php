<?php

namespace App\Filament\Resources\Variants\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VariantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('console.name')
                    ->searchable()
                    ->width('150px')
                    ->badge(),
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn ($record) => $record->full_slug),
                ImageColumn::make('image_filename')
                    ->width('80px'),
                TextColumn::make('rarity_level')
                    ->searchable()
                    ->width('100px')
                    ->badge()
                    ->description(fn ($record) =>
                        ($record->region ? $record->region : '')
                    ),
                IconColumn::make('is_special_edition')
                    ->boolean()
                    ->width('60px')
                    ->label('Special'),
                IconColumn::make('is_default')
                    ->boolean()
                    ->width('60px')
                    ->label('Default'),
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
