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
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable()
                    ->width('60px'),
                TextColumn::make('console.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->width('150px')
                    ->badge(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->description(fn ($record) => $record->full_slug),
                TextColumn::make('search_term')
                    ->label('Search Term')
                    ->formatStateUsing(fn ($state) => $state ? '🔍 ' . $state : '-')
                    ->wrap()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('blacklist_terms')
                    ->label('Blacklist')
                    ->formatStateUsing(fn ($state) => $state ? '🚫 ' . $state : '-')
                    ->wrap()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('image_url')
                    ->label('Image Path')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('rarity_level')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->width('100px')
                    ->badge()
                    ->description(fn ($record) =>
                        ($record->region ? $record->region : '')
                    ),
                IconColumn::make('is_special_edition')
                    ->boolean()
                    ->sortable()
                    ->toggleable()
                    ->width('60px')
                    ->label('Special'),
                IconColumn::make('is_default')
                    ->boolean()
                    ->sortable()
                    ->toggleable()
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
            ->persistSortInSession()
            ->persistColumnSearchesInSession()
            ->persistSearchInSession()
            ->columnToggleFormColumns(2)
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
