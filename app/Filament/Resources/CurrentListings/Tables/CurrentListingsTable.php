<?php

namespace App\Filament\Resources\CurrentListings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use App\Models\Variant;
use Illuminate\Database\Eloquent\Collection;

class CurrentListingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->sortable()
                    ->width('100px'),
                TextColumn::make('variant.name')
                    ->searchable()
                    ->width('150px')
                    ->badge()
                    ->color('info')
                    ->default('Not assigned'),
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
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('assign_variant')
                        ->label('Assign Variant')
                        ->icon('heroicon-o-tag')
                        ->color('info')
                        ->form([
                            Select::make('variant_id')
                                ->label('Variant')
                                ->options(Variant::all()->pluck('name', 'id'))
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each(function ($record) use ($data) {
                                $record->update(['variant_id' => $data['variant_id']]);
                            });

                            Notification::make()
                                ->title('Variants Assigned')
                                ->body("Assigned variant to {$records->count()} listings")
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(function ($record) {
                                $record->update(['status' => 'approved']);
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
                        ->action(function (Collection $records): void {
                            $records->each(function ($record) {
                                $record->update(['status' => 'rejected']);
                            });

                            Notification::make()
                                ->title('Listings Rejected')
                                ->body("Rejected {$records->count()} listings")
                                ->danger()
                                ->send();
                        }),
                ]),
            ]);
    }
}
