<?php

namespace App\Filament\Resources\Listings\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('variant.console.name')
                    ->label('Console')
                    ->searchable()
                    ->width('120px')
                    ->badge()
                    ->color('info'),
                TextColumn::make('variant.name')
                    ->label('Variant')
                    ->searchable()
                    ->width('120px')
                    ->badge()
                    ->color('success'),
                TextColumn::make('console_slug')
                    ->label('Console Slug')
                    ->searchable()
                    ->width('120px')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->description(fn ($record) => 'variant_id: ' . ($record->variant_id ?? 'NULL')),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->description(fn ($record) =>
                        ($record->price ? number_format($record->price, 2) . '€' : '') .
                        ($record->sold_date ? ' • ' . $record->sold_date : '') .
                        ($record->condition ? ' • ' . $record->condition : '')
                    ),
                TextColumn::make('source')
                    ->searchable()
                    ->width('80px')
                    ->badge(),
                IconColumn::make('is_outlier')
                    ->boolean()
                    ->width('60px')
                    ->label('Outlier'),
                TextColumn::make('status')
                    ->badge()
                    ->width('100px'),
                TextColumn::make('reviewed_at')
                    ->dateTime()
                    ->sortable()
                    ->width('150px')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),
                SelectFilter::make('variant')
                    ->relationship('variant', 'name')
                    ->searchable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'approved',
                                    'reviewed_at' => now(),
                                ]);
                            });

                            Notification::make()
                                ->title('Listings Approved')
                                ->body("Approved {$records->count()} listings")
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'status' => 'rejected',
                                    'reviewed_at' => now(),
                                ]);
                            });

                            Notification::make()
                                ->title('Listings Rejected')
                                ->body("Rejected {$records->count()} listings")
                                ->danger()
                                ->send();
                        }),
                ]),
            ]);

        return $table;
    }
}
