<?php

namespace App\Filament\Pages;

use App\Models\Variant;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;

class ManageCurrentListings extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public string $view = 'filament.pages.manage-current-listings';

    protected static ?string $navigationLabel = 'Manage Current Listings';

    protected static ?int $navigationSort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Variant::query()
                    ->whereHas('listings', function($q) {
                        $q->where('status', 'approved');
                    })
                    ->withCount(['currentListings' => function($q) {
                        $q->where('status', 'approved')
                          ->where('is_sold', false);
                    }])
                    ->orderByRaw('current_listings_fetched_at IS NULL DESC')
                    ->orderBy('current_listings_fetched_at', 'asc')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->width('60px'),
                TextColumn::make('console.name')
                    ->label('Console')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Variant')
                    ->searchable()
                    ->url(fn ($record) => url('/' . $record->full_slug))
                    ->openUrlInNewTab()
                    ->description(fn ($record) =>
                        is_array($record->search_terms) && count($record->search_terms) > 0
                            ? '🔍 ' . implode(', ', $record->search_terms)
                            : null
                    ),
                TextColumn::make('current_listings_count')
                    ->label('Current Listings')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                TextColumn::make('current_listings_fetched_at')
                    ->label('Last Fetched')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Never')
                    ->description(fn ($record) => $record->current_listings_fetched_at
                        ? $record->current_listings_fetched_at->diffForHumans()
                        : 'Never fetched'
                    ),
            ])
            ->recordActions([
                Action::make('fetch')
                    ->label('Fetch')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function ($record) {
                        // Run artisan command synchronously to get output
                        $exitCode = Artisan::call('fetch:current-listings', [
                            '--variant' => $record->id,
                            '--limit' => 5,
                        ]);

                        // Get command output
                        $output = Artisan::output();

                        // Parse output to check for rate limiting
                        if (str_contains($output, 'Rate limit exceeded') || str_contains($output, 'rate limit')) {
                            Notification::make()
                                ->title('Rate limit exceeded')
                                ->body('eBay API rate limit hit. Wait a few minutes and try again.')
                                ->warning()
                                ->duration(8000)
                                ->send();
                            return;
                        }

                        if ($exitCode === 0) {
                            // Update variant timestamp manually to reflect in table
                            $record->refresh();
                            $record->update(['current_listings_fetched_at' => now()]);

                            // Extract stats from output
                            preg_match('/New: (\d+)/', $output, $newMatches);
                            preg_match('/Updated: (\d+)/', $output, $updatedMatches);
                            preg_match('/Total now: (\d+)/', $output, $totalMatches);
                            preg_match('/Skipped: (\d+) rejected, (\d+) blacklisted/', $output, $skippedMatches);

                            $newCount = $newMatches[1] ?? 0;
                            $updatedCount = $updatedMatches[1] ?? 0;
                            $totalCount = $totalMatches[1] ?? '?';
                            $skippedRejected = $skippedMatches[1] ?? 0;
                            $skippedBlacklist = $skippedMatches[2] ?? 0;

                            $bodyText = "New: {$newCount} | Updated: {$updatedCount} | Total: {$totalCount}";

                            if ($totalCount < 5) {
                                $bodyText .= "\n⚠️ Only {$totalCount}/5 listings";
                                if ($skippedRejected > 0 || $skippedBlacklist > 0) {
                                    $bodyText .= " (Skipped: {$skippedRejected} rejected, {$skippedBlacklist} blacklisted)";
                                }
                            }

                            Notification::make()
                                ->title('Fetch completed')
                                ->body($bodyText)
                                ->success()
                                ->duration(8000)
                                ->send();

                            // Trigger table refresh
                            $this->dispatch('refreshTable');
                        } else {
                            Notification::make()
                                ->title('Fetch failed')
                                ->body("Error fetching listings for {$record->name}")
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->poll('30s')
            ->filters([
                //
            ]);
    }
}
