<?php

namespace App\Filament\Pages;

use App\Models\Console;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class ManageGuides extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    public string $view = 'filament.pages.manage-guides';

    protected static ?string $navigationLabel = 'Manage Guides';

    protected static ?int $navigationSort = 5;

    // Central guide map - sync this with controllers
    public static function getGuideMap(): array
    {
        return [
            'game-boy-color' => '/guides/guide-achat-game-boy-color',
            'game-boy-advance' => '/guides/guide-achat-game-boy-advance',
            'ps-vita' => '/guides/ps-vita-occasion-guide',
        ];
    }

    public function table(Table $table): Table
    {
        $guideMap = self::getGuideMap();

        return $table
            ->query(
                Console::query()
                    ->withCount(['variants' => function($q) {
                        $q->whereHas('listings', function($listing) {
                            $listing->where('status', 'approved');
                        });
                    }])
                    ->orderBy('display_order')
            )
            ->columns([
                IconColumn::make('has_guide')
                    ->label('Guide')
                    ->boolean()
                    ->getStateUsing(fn ($record) => isset($guideMap[$record->slug]))
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->width('80px'),
                TextColumn::make('name')
                    ->label('Console')
                    ->searchable()
                    ->sortable()
                    ->url(fn ($record) => isset($guideMap[$record->slug]) ? $guideMap[$record->slug] : null)
                    ->openUrlInNewTab(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('variants_count')
                    ->label('Variants with Data')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'warning'),
                TextColumn::make('guide_url')
                    ->label('Guide URL')
                    ->getStateUsing(fn ($record) => $guideMap[$record->slug] ?? 'No guide')
                    ->color(fn ($record) => isset($guideMap[$record->slug]) ? 'success' : 'danger'),
            ])
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('has_guide')
                    ->label('Has Guide')
                    ->placeholder('All consoles')
                    ->trueLabel('With guide')
                    ->falseLabel('Without guide')
                    ->query(function ($query, $state) use ($guideMap) {
                        if ($state === true) {
                            $slugsWithGuides = array_keys($guideMap);
                            $query->whereIn('slug', $slugsWithGuides);
                        } elseif ($state === false) {
                            $slugsWithGuides = array_keys($guideMap);
                            $query->whereNotIn('slug', $slugsWithGuides);
                        }
                    }),
            ]);
    }
}
