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
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use App\Filament\Resources\Variants\Schemas\VariantForm;

class ManageVariants extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog;

    protected static ?string $navigationLabel = 'Manage Variants';

    protected static ?int $navigationSort = 3;

    public string $view = 'filament.pages.manage-variants';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->model(Variant::class)
                ->form(fn ($form) => VariantForm::configure($form)->getComponents())
                ->mutateFormDataUsing(function (array $data): array {
                    return $data;
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Variant::query()
                    ->with('console')
                    ->withCount(['currentListings' => function($q) {
                        $q->where('status', 'approved')
                          ->where('is_sold', false);
                    }])
            )
            ->defaultSort('id', 'desc')
            ->groups([])
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable()
                    ->width('60px'),
                TextColumn::make('console.name')
                    ->label('Console')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge(),
                TextColumn::make('name')
                    ->label('Variant')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->url(fn ($record) => url('/' . $record->full_slug))
                    ->openUrlInNewTab()
                    ->description(fn ($record) => $record->full_slug)
                    ->wrap(),
                TextColumn::make('search_term')
                    ->label('Search Terms')
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
                TextColumn::make('current_listings_count')
                    ->label('Current Listings')
                    ->badge()
                    ->sortable()
                    ->toggleable()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger'),
                TextColumn::make('current_listings_fetched_at')
                    ->label('Last Fetched')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->placeholder('Never')
                    ->description(fn ($record) => $record->current_listings_fetched_at
                        ? $record->current_listings_fetched_at->diffForHumans()
                        : 'Never fetched'
                    ),
                IconColumn::make('is_special_edition')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Special'),
                IconColumn::make('is_default')
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Default'),
            ])
            ->recordActions([
                \Filament\Tables\Actions\EditAction::make()
                    ->form(fn ($form) => VariantForm::configure($form)->getComponents())
                    ->mutateFormDataUsing(function (array $data): array {
                        return $data;
                    })
                    ->successRedirectUrl(route('filament.admin.pages.manage-variants')),
                \Filament\Tables\Actions\Action::make('fetch')
                    ->label('Fetch')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function ($record) {
                        $exitCode = Artisan::call('fetch:current-listings', [
                            '--variant' => $record->id,
                            '--limit' => 5,
                        ]);

                        $output = Artisan::output();

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
                            $record->refresh();

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
                        } else {
                            Notification::make()
                                ->title('Fetch failed')
                                ->body("Error fetching listings for {$record->name}")
                                ->danger()
                                ->send();
                        }
                    }),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->filters([])
            ->persistSortInSession()
            ->persistColumnSearchesInSession()
            ->persistFiltersInSession()
            ->persistSearchInSession()
            ->columnToggleFormColumns(2)
            ->reorderable(false);
    }
}
