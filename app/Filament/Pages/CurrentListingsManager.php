<?php

namespace App\Filament\Pages;

use App\Models\CurrentListing;
use App\Models\Variant;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\Action as HeaderAction;
use Filament\Tables\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;
use Livewire\Attributes\On;

class CurrentListingsManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.current-listings-manager';

    protected static ?string $navigationLabel = 'Current Listings Hub';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?int $navigationSort = 3;

    // Stats - will be updated via Livewire
    public int $activeCount = 0;
    public int $pendingCount = 0;
    public int $variantsCovered = 0;

    public function mount(): void
    {
        $this->updateStats();
    }

    #[On('refresh-stats')]
    public function updateStats(): void
    {
        $this->activeCount = CurrentListing::where('status', 'approved')->where('is_sold', false)->count();
        $this->pendingCount = CurrentListing::where('status', 'pending')->count();
        $this->variantsCovered = Variant::whereHas('currentListings', fn($q) =>
            $q->where('status', 'approved')->where('is_sold', false)
        )->count();
    }

    public function fetchVariant(int $variantId): void
    {
        $variant = Variant::find($variantId);
        if (!$variant) {
            Notification::make()
                ->title('Variant not found')
                ->danger()
                ->send();
            return;
        }

        // Run fetch command
        $exitCode = Artisan::call('fetch:current-listings', [
            '--variant' => $variantId,
            '--limit' => 5,
        ]);

        $output = Artisan::output();

        // Check for rate limiting
        if (str_contains($output, 'Rate limit exceeded')) {
            Notification::make()
                ->title('Rate limit exceeded')
                ->body('eBay API rate limit hit. Wait a few minutes.')
                ->warning()
                ->duration(8000)
                ->send();
            return;
        }

        if ($exitCode === 0) {
            // Extract stats
            preg_match('/New: (\d+)/', $output, $newMatches);
            preg_match('/Updated: (\d+)/', $output, $updatedMatches);
            preg_match('/Total now: (\d+)/', $output, $totalMatches);

            $newCount = $newMatches[1] ?? 0;
            $updatedCount = $updatedMatches[1] ?? 0;
            $totalCount = $totalMatches[1] ?? '?';

            Notification::make()
                ->title("Fetched: {$variant->name}")
                ->body("New: {$newCount} | Updated: {$updatedCount} | Total: {$totalCount}")
                ->success()
                ->send();

            // Refresh table and stats
            $this->dispatch('refresh-stats');
            $this->dispatch('$refresh');
        } else {
            Notification::make()
                ->title('Fetch failed')
                ->body("Error fetching listings for {$variant->name}")
                ->danger()
                ->send();
        }
    }

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
                    ->openUrlInNewTab()
                    ->description(fn ($record) => $record->variant ?
                        CurrentListing::where('variant_id', $record->variant_id)
                            ->where('status', 'approved')
                            ->where('is_sold', false)
                            ->count() . ' active listings'
                        : null
                    ),
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
            ->defaultSort('variant.console.name')
            ->defaultGroup('variant.console.name')
            ->actions([
                Action::make('fetch_variant')
                    ->label('Fetch')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->size('sm')
                    ->visible(fn ($record) => $record->variant_id !== null)
                    ->action(fn ($record) => $this->fetchVariant($record->variant_id)),
            ])
            ->groupedBulkActions([
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
                        $this->dispatch('refresh-stats');
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
                        $this->dispatch('refresh-stats');
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
                        $this->dispatch('refresh-stats');
                    }),
            ])
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
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            HeaderAction::make('fetch_all')
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

                    // Refresh stats and table
                    $this->dispatch('refresh-stats');
                    $this->dispatch('$refresh');
                }),
        ];
    }
}
