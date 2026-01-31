<?php

namespace App\Filament\Pages;

use App\Models\CurrentListing;
use App\Models\Variant;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;

class CurrentListingsManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.pages.current-listings-manager';

    protected static ?string $navigationLabel = 'Current Listings Manager';

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?int $navigationSort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CurrentListing::query()
                    ->with(['variant.console'])
                    ->orderBy('last_seen_at', 'desc')
            )
            ->columns([
                ImageColumn::make('thumbnail_url')
                    ->label('Image')
                    ->width(80)
                    ->height(80)
                    ->defaultImageUrl(asset('images/no-image.png')),
                TextColumn::make('variant.console.name')
                    ->label('Console')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->default('Not assigned'),
                TextColumn::make('variant.name')
                    ->label('Variant')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->default('Not assigned')
                    ->url(fn ($record) => $record->variant ? url('/' . $record->variant->full_slug) : null)
                    ->openUrlInNewTab(),
                TextColumn::make('title')
                    ->searchable()
                    ->limit(60)
                    ->tooltip(fn ($record) => $record->title)
                    ->description(fn ($record) =>
                        ($record->price ? number_format($record->price, 2) . '€' : 'N/A')
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
                    ->sortable(),
                IconColumn::make('is_sold')
                    ->boolean()
                    ->label('Sold'),
                TextColumn::make('last_seen_at')
                    ->label('Last Seen')
                    ->dateTime('d/m H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->last_seen_at ? $record->last_seen_at->diffForHumans() : 'Never'),
            ])
            ->defaultSort('last_seen_at', 'desc')
            ->defaultGroup('variant.console.name')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('approved'),
                SelectFilter::make('console')
                    ->relationship('variant.console', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Console'),
                SelectFilter::make('variant')
                    ->relationship('variant', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Variant'),
                Filter::make('is_sold')
                    ->label('Hide Sold')
                    ->default()
                    ->query(fn ($query) => $query->where('is_sold', false)),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'approved']);
                            Notification::make()
                                ->title('Approved')
                                ->body("Approved {$records->count()} listings")
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(function (Collection $records): void {
                            $records->each->update(['status' => 'rejected']);
                            Notification::make()
                                ->title('Rejected')
                                ->body("Rejected {$records->count()} listings")
                                ->danger()
                                ->send();
                        }),
                    BulkAction::make('assign_variant')
                        ->label('Assign Variant')
                        ->icon('heroicon-o-tag')
                        ->color('info')
                        ->form([
                            Select::make('variant_id')
                                ->label('Variant')
                                ->options(Variant::with('console')->get()->mapWithKeys(fn ($v) => [
                                    $v->id => "{$v->console->name} - {$v->name}"
                                ])->toArray())
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each->update(['variant_id' => $data['variant_id']]);
                            Notification::make()
                                ->title('Assigned')
                                ->body("Assigned variant to {$records->count()} listings")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->poll('30s');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetch_all')
                ->label('Fetch All Variants')
                ->icon('heroicon-o-arrow-path')
                ->color('success')
                ->action(function () {
                    Artisan::call('fetch:current-listings', ['--limit' => 5]);
                    $output = Artisan::output();

                    if (str_contains($output, 'Rate limit exceeded')) {
                        Notification::make()
                            ->title('Rate limit exceeded')
                            ->body('eBay API rate limit hit. Wait a few minutes.')
                            ->warning()
                            ->duration(8000)
                            ->send();
                        return;
                    }

                    preg_match('/New listings: (\d+)/', $output, $newMatches);
                    preg_match('/Updated: (\d+)/', $output, $updatedMatches);
                    $newCount = $newMatches[1] ?? 0;
                    $updatedCount = $updatedMatches[1] ?? 0;

                    Notification::make()
                        ->title('Bulk fetch completed')
                        ->body("New: {$newCount} | Updated: {$updatedCount}")
                        ->success()
                        ->send();
                }),
        ];
    }
}
