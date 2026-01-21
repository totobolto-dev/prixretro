<?php

namespace App\Filament\Resources\Listings\Tables;

use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
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
                        ($record->item_condition ? ' • ' . $record->item_condition : '')
                    ),
                TextColumn::make('completeness')
                    ->label('État')
                    ->badge()
                    ->width('90px')
                    ->color(fn (string $state = null): string => match ($state) {
                        'loose' => 'gray',
                        'cib' => 'info',
                        'sealed' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state = null): string => match ($state) {
                        'loose' => 'Loose',
                        'cib' => 'CIB',
                        'sealed' => 'Sealed',
                        default => '-',
                    }),
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
                    ->default('approved'),
                SelectFilter::make('completeness')
                    ->label('Complétude')
                    ->options([
                        'loose' => 'Loose',
                        'cib' => 'CIB',
                        'sealed' => 'Sealed',
                    ]),
                SelectFilter::make('variant')
                    ->relationship('variant', 'name')
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn ($record) =>
                        $record->console->name . ' - ' . $record->name
                    ),
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
                    BulkAction::make('change_variant')
                        ->label('Change Variant')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Select::make('console_slug')
                                ->label('Console')
                                ->options(\App\Models\Console::orderBy('display_order')->pluck('name', 'slug')->toArray())
                                ->required()
                                ->live()
                                ->afterStateUpdated(fn($state, callable $set) => $set('variant_id', null)),
                            Select::make('variant_id')
                                ->label('Variant')
                                ->options(function (callable $get) {
                                    if (!$get('console_slug')) {
                                        return [];
                                    }
                                    return \App\Models\Variant::query()
                                        ->whereHas('console', fn($q) => $q->where('slug', $get('console_slug')))
                                        ->get()
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->required()
                                ->disabled(fn(callable $get) => !$get('console_slug'))
                                ->helperText(fn(callable $get) => !$get('console_slug') ? 'Select a console first' : null),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $variant = \App\Models\Variant::find($data['variant_id']);

                            if (!$variant) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Variant not found')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            $records->each(function ($record) use ($variant) {
                                $record->update([
                                    'variant_id' => $variant->id,
                                    'console_slug' => $variant->console->slug,
                                ]);
                            });

                            Notification::make()
                                ->title('Variants Updated')
                                ->body("Updated {$records->count()} listings to {$variant->console->name} - {$variant->name}")
                                ->success()
                                ->send();
                        }),
                ]),
            ]);

        return $table;
    }
}
