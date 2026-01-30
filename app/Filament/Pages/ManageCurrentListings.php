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
                TextColumn::make('console.name')
                    ->label('Console')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Variant')
                    ->searchable()
                    ->url(fn ($record) => url('/' . $record->full_slug))
                    ->openUrlInNewTab(),
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
                    ->label('Fetch Now')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        // Run artisan command synchronously to get output
                        $exitCode = Artisan::call('fetch:current-listings', [
                            '--variant' => $record->id,
                            '--limit' => 5,
                        ]);

                        // Get command output
                        $output = Artisan::output();

                        if ($exitCode === 0) {
                            // Update variant timestamp manually to reflect in table
                            $record->update(['current_listings_fetched_at' => now()]);

                            Notification::make()
                                ->title('Listings fetched successfully')
                                ->body("Completed for {$record->name}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Fetch failed')
                                ->body("Error fetching listings for {$record->name}")
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->filters([
                //
            ]);
    }
}
