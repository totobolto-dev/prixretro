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
                TextColumn::make('variant.console.name')
                    ->label('Console')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->width('120px')
                    ->default('Not assigned'),
                TextColumn::make('variant.name')
                    ->label('Variant')
                    ->searchable()
                    ->sortable()
                    ->width('120px')
                    ->badge()
                    ->color('info')
                    ->default('Not assigned')
                    ->url(fn ($record) => $record->variant ? url('/' . $record->variant->full_slug) : null)
                    ->openUrlInNewTab(),
                \Filament\Tables\Columns\ImageColumn::make('thumbnail_url')
                    ->label('Img')
                    ->width('60px')
                    ->height('60px')
                    ->defaultImageUrl(asset('images/no-image.png')),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) =>
                        ($record->price ? number_format($record->price, 2) . '€' : 'N/A') .
                        ' • ' . $record->item_id
                    )
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    })
                    ->sortable()
                    ->width('100px'),
                IconColumn::make('is_sold')
                    ->boolean()
                    ->label('Sold')
                    ->width('50px'),
                TextColumn::make('last_seen_at')
                    ->label('Last Seen')
                    ->dateTime('d/m H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->last_seen_at ? $record->last_seen_at->diffForHumans() : 'Never')
                    ->width('110px'),
            ])
            ->defaultSort('last_seen_at', 'desc')
            ->defaultGroup('variant.console.name')
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('approved'),
                \Filament\Tables\Filters\SelectFilter::make('console')
                    ->relationship('variant.console', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Console'),
                \Filament\Tables\Filters\SelectFilter::make('variant')
                    ->relationship('variant', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Variant'),
                \Filament\Tables\Filters\Filter::make('is_sold')
                    ->label('Hide Sold')
                    ->default()
                    ->query(fn ($query) => $query->where('is_sold', false)),
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
