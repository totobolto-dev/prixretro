<?php

namespace App\Filament\Pages;

use App\Models\Console;
use App\Models\Listing;
use App\Models\Variant;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class SortListings extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static ?string $navigationLabel = 'Sort Listings';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'sort-listings';

    public string $view = 'filament.pages.sort-listings';

    public $listings = [];
    public $currentIndex = 0;
    public $stats = [];
    public $filterStatus = 'unclassified';
    public $filterConsole = '';
    public $searchTerm = '';
    public $perPage = 25;
    public $page = 1;

    // Form state
    public $selectedConsole = '';
    public $selectedVariant = '';
    public $selectedStatus = 'keep';

    public function mount(): void
    {
        $this->updateStats();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Listing::query()
                    ->whereNull('variant_id')
                    ->orWhere('classification_status', 'unclassified')
            )
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->width('50px'),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->url(fn ($record) => $record->url, shouldOpenInNewTab: true)
                    ->description(fn ($record) =>
                        ($record->price ? number_format($record->price, 2) . '€' : '') .
                        ' • ' .
                        ($record->sold_date ? $record->sold_date : '') .
                        ' • ' .
                        ($record->condition ? $record->condition : '')
                    ),
                TextColumn::make('console_slug')
                    ->label('Console')
                    ->badge()
                    ->width('120px')
                    ->default('Not set'),
                TextColumn::make('variant.name')
                    ->label('Variant')
                    ->badge()
                    ->color('success')
                    ->width('150px')
                    ->default('Not set'),
            ])
            ->filters([
                // Filters
            ])
            ->recordActions([
                Action::make('classify')
                    ->label('Classify')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->form([
                        FormSelect::make('console_slug')
                            ->label('Console')
                            ->options(Console::pluck('name', 'slug')->toArray())
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('variant_id', null)),
                        FormSelect::make('variant_id')
                            ->label('Select Existing Variant')
                            ->options(function (callable $get) {
                                if (!$get('console_slug')) {
                                    return [];
                                }
                                return Variant::query()
                                    ->whereHas('console', fn ($q) => $q->where('slug', $get('console_slug')))
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->disabled(fn (callable $get) => !$get('console_slug'))
                            ->helperText(fn (callable $get) => !$get('console_slug') ? 'Select a console first' : 'Or create a new variant below'),
                        TextInput::make('new_variant_name')
                            ->label('Or Create New Variant')
                            ->helperText('Leave empty to use selected variant above')
                            ->disabled(fn (callable $get) => !$get('console_slug')),
                    ])
                    ->fillForm(fn (Listing $record) => [
                        'console_slug' => $record->console_slug,
                        'variant_id' => $record->variant_id,
                    ])
                    ->action(function (Listing $record, array $data) {
                        $variantId = $data['variant_id'] ?? null;

                        // Check if user wants to create a new variant
                        if (!empty($data['new_variant_name'])) {
                            $console = Console::where('slug', $data['console_slug'])->first();

                            if (!$console) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Console not found')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Create new variant
                            $slug = Str::slug($data['new_variant_name']);
                            $variant = Variant::create([
                                'console_id' => $console->id,
                                'name' => $data['new_variant_name'],
                                'slug' => $slug,
                                'full_slug' => $console->slug . '/' . $slug,
                            ]);

                            $variantId = $variant->id;

                            Notification::make()
                                ->title('Variant created')
                                ->body("Created new variant: {$data['new_variant_name']}")
                                ->success()
                                ->send();
                        }

                        if (!$variantId) {
                            Notification::make()
                                ->title('Error')
                                ->body('Please select a variant or create a new one')
                                ->warning()
                                ->send();
                            return;
                        }

                        $record->update([
                            'console_slug' => $data['console_slug'],
                            'variant_id' => $variantId,
                            'classification_status' => 'classified',
                        ]);

                        Notification::make()
                            ->title('Classified successfully')
                            ->success()
                            ->send();
                    }),
                Action::make('approve')
                    ->label('Keep')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Listing $record) {
                        if (!$record->variant_id) {
                            Notification::make()
                                ->title('Cannot approve')
                                ->body('Please classify this listing first')
                                ->warning()
                                ->send();
                            return;
                        }

                        $record->update([
                            'status' => 'approved',
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Listing approved')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Listing $record) {
                        $record->update([
                            'status' => 'rejected',
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Listing rejected')
                            ->danger()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100]);
    }

    public function loadListings(): void
    {
        $query = Listing::query()
            ->whereNull('variant_id')  // Items not yet assigned to a variant
            ->orWhere('classification_status', 'unclassified');

        if ($this->filterStatus && $this->filterStatus !== 'all') {
            $query->where('classification_status', $this->filterStatus);
        }

        if ($this->filterConsole) {
            $query->where('console_slug', $this->filterConsole);
        }

        if ($this->searchTerm) {
            $query->where('title', 'like', '%' . $this->searchTerm . '%');
        }

        // Paginate results
        $offset = ($this->page - 1) * $this->perPage;
        $this->listings = $query->latest()
            ->skip($offset)
            ->take($this->perPage)
            ->get()
            ->toArray();
    }

    public function nextPage(): void
    {
        $this->page++;
        $this->loadListings();
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->loadListings();
        }
    }

    public function updateStats(): void
    {
        $this->stats = [
            'total' => Listing::whereNull('variant_id')->count(),
            'unclassified' => Listing::where('classification_status', 'unclassified')->count(),
            'classified' => Listing::whereNotNull('console_slug')->whereNull('variant_id')->count(),
        ];
    }

    public function getConsoles(): array
    {
        return Console::pluck('name', 'slug')->toArray();
    }

    public function getVariants(): array
    {
        if (!$this->selectedConsole) {
            return [];
        }

        $console = Console::where('slug', $this->selectedConsole)->first();
        if (!$console) {
            return [];
        }

        return Variant::where('console_id', $console->id)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function classifyItem($listingId): void
    {
        $listing = Listing::find($listingId);

        if (!$listing) {
            Notification::make()
                ->title('Error')
                ->body('Listing not found')
                ->danger()
                ->send();
            return;
        }

        $updates = [];

        // Update console if selected
        if ($this->selectedConsole) {
            $updates['console_slug'] = $this->selectedConsole;
        }

        // Update variant if selected
        if ($this->selectedVariant) {
            $updates['variant_id'] = $this->selectedVariant;
            $updates['status'] = 'pending'; // Ready for review
        }

        // Update status
        if ($this->selectedStatus === 'reject') {
            $updates['status'] = 'rejected';
            $updates['reviewed_at'] = now();
        }

        $listing->update($updates);

        $this->loadListings();
        $this->updateStats();

        // Reset form
        $this->selectedConsole = '';
        $this->selectedVariant = '';
        $this->selectedStatus = 'keep';

        // Move to next item
        if ($this->currentIndex < count($this->listings) - 1) {
            $this->currentIndex++;
        }
    }

    public function skipItem(): void
    {
        if ($this->currentIndex < count($this->listings) - 1) {
            $this->currentIndex++;
        }
    }

    public function previousItem(): void
    {
        if ($this->currentIndex > 0) {
            $this->currentIndex--;
        }
    }

    public function updatedFilterStatus(): void
    {
        $this->loadListings();
        $this->currentIndex = 0;
    }

    public function updatedFilterConsole(): void
    {
        $this->loadListings();
        $this->currentIndex = 0;
    }

    public function updatedSearchTerm(): void
    {
        $this->loadListings();
        $this->currentIndex = 0;
    }

    public function updatedSelectedConsole(): void
    {
        // Reset variant when console changes
        $this->selectedVariant = '';
    }

    public function updateItemConsole($listingId, $consoleSlug): void
    {
        $listing = Listing::find($listingId);

        if (!$listing) {
            return;
        }

        $listing->update([
            'console_slug' => $consoleSlug,
        ]);

        $this->loadListings();
    }

    public function updateItemVariant($listingId, $variantId): void
    {
        $listing = Listing::find($listingId);

        if (!$listing) {
            return;
        }

        if ($variantId) {
            $listing->update([
                'variant_id' => $variantId,
                'classification_status' => 'classified',
            ]);
        }

        $this->loadListings();
        $this->updateStats();
    }

    public function quickApprove($listingId): void
    {
        $listing = Listing::find($listingId);

        if (!$listing) {
            return;
        }

        if (!$listing->variant_id) {
            Notification::make()
                ->title('Cannot approve')
                ->body('Please select a variant first')
                ->warning()
                ->send();
            return;
        }

        $listing->update([
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        $this->loadListings();
        $this->updateStats();

        Notification::make()
            ->title('Approved')
            ->body('Listing approved successfully')
            ->success()
            ->send();
    }

    public function quickReject($listingId): void
    {
        $listing = Listing::find($listingId);

        if (!$listing) {
            return;
        }

        $listing->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]);

        $this->loadListings();
        $this->updateStats();

        Notification::make()
            ->title('Rejected')
            ->body('Listing rejected')
            ->danger()
            ->send();
    }
}
