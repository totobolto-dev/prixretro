<?php

namespace App\Filament\Pages;

use App\Models\Console;
use App\Models\Listing;
use App\Models\Variant;
use App\Services\UrlValidationService;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select as FormSelect;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

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
                    ->where('status', '!=', 'rejected')
                    ->where('status', '!=', 'approved')
                    ->where(function ($query) {
                        $query->whereNull('variant_id')
                            ->orWhere('classification_status', 'unclassified');
                    })
            )
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->width('50px')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->description(fn($record) => new HtmlString('
                        <div class="flex gap-2 mt-1" onclick="event.stopPropagation()">
                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400 cursor-pointer hover:underline"
                                  wire:click="mountTableAction(\'classify\', \'' . $record->id . '\')">
                                📝 Classify
                            </span>
                            <span class="text-xs font-medium text-red-600 dark:text-red-400 cursor-pointer hover:underline"
                                  wire:click="mountTableAction(\'reject\', \'' . $record->id . '\')">
                                ❌ Reject
                            </span>
                            <span class="text-xs font-medium text-yellow-600 dark:text-yellow-400 cursor-pointer hover:underline"
                                  wire:click="mountTableAction(\'validate_url\', \'' . $record->id . '\')">
                                🔗 Validate URL
                            </span>
                        </div>
                    '))
                    ->html()
                    ->url(fn($record) => $record->url, shouldOpenInNewTab: true),
                TextColumn::make('price')
                    ->label('Price')
                    ->money('EUR')
                    ->sortable()
                    ->width('100px'),
                TextColumn::make('sold_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->width('100px'),
                TextColumn::make('condition')
                    ->label('Condition')
                    ->badge()
                    ->sortable()
                    ->width('120px'),
                TextColumn::make('url_validation_status')
                    ->label('URL Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string|null $state): string => match ($state) {
                        'valid' => 'success',
                        'invalid' => 'danger',
                        'captcha' => 'warning',
                        'error' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string|null $state): string => match ($state) {
                        'valid' => 'Valid',
                        'invalid' => 'Invalid',
                        'captcha' => 'CAPTCHA',
                        'error' => 'Error',
                        'pending' => 'Pending',
                        default => 'Not Checked',
                    })
                    ->tooltip(fn ($record) => $record->url_validation_error)
                    ->width('120px'),
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
                            ->options(Console::orderBy('display_order')->pluck('name', 'slug')->toArray())
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn($state, callable $set) => $set('variant_id', null)),
                        FormSelect::make('variant_id')
                            ->label('Select Existing Variant')
                            ->options(function (callable $get) {
                                if (!$get('console_slug')) {
                                    return [];
                                }
                                return Variant::query()
                                    ->whereHas('console', fn($q) => $q->where('slug', $get('console_slug')))
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->disabled(fn(callable $get) => !$get('console_slug'))
                            ->helperText(fn(callable $get) => !$get('console_slug') ? 'Select a console first' : 'Or create a new variant below'),
                        TextInput::make('new_variant_name')
                            ->label('Or Create New Variant')
                            ->helperText('Leave empty to use selected variant above')
                            ->disabled(fn(callable $get) => !$get('console_slug')),
                    ])
                    ->fillForm(fn(Listing $record) => [
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
                            'status' => 'approved',
                            'reviewed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Classified successfully')
                            ->success()
                            ->send();
                    }),
                Action::make('validate_url')
                    ->label('Validate URL')
                    ->icon('heroicon-o-link')
                    ->color('warning')
                    ->action(function (Listing $record) {
                        $validator = app(UrlValidationService::class);
                        $result = $validator->validateUrl($record->url, $record->item_id);

                        $record->update([
                            'url_validation_status' => $result['status'],
                            'url_redirect_target' => $result['redirect_target'],
                            'url_validation_error' => $result['error'],
                            'url_validated_at' => now(),
                        ]);

                        // Auto-reject if URL is invalid
                        if ($result['status'] === 'invalid') {
                            $record->update([
                                'status' => 'rejected',
                                'reviewed_at' => now(),
                            ]);

                            Notification::make()
                                ->title('URL Invalid - Auto-Rejected')
                                ->body($result['error'])
                                ->danger()
                                ->send();
                        } elseif ($result['status'] === 'captcha') {
                            Notification::make()
                                ->title('CAPTCHA Challenge')
                                ->body('Cannot validate - eBay requires CAPTCHA. Try again later.')
                                ->warning()
                                ->send();
                        } elseif ($result['status'] === 'error') {
                            Notification::make()
                                ->title('Validation Error')
                                ->body($result['error'])
                                ->danger()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('URL Valid')
                                ->body('This listing URL is valid and accessible.')
                                ->success()
                                ->send();
                        }
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
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_validate_urls')
                        ->label('Validate URLs')
                        ->icon('heroicon-o-link')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Validate URLs')
                        ->modalDescription('This will validate all selected listings and auto-reject invalid ones. Delay of 2 seconds between requests.')
                        ->action(function ($records) {
                            $validator = app(UrlValidationService::class);
                            $stats = [
                                'valid' => 0,
                                'invalid' => 0,
                                'captcha' => 0,
                                'error' => 0,
                            ];

                            foreach ($records as $record) {
                                $result = $validator->validateUrl($record->url, $record->item_id);

                                $record->update([
                                    'url_validation_status' => $result['status'],
                                    'url_redirect_target' => $result['redirect_target'],
                                    'url_validation_error' => $result['error'],
                                    'url_validated_at' => now(),
                                ]);

                                // Auto-reject if URL is invalid
                                if ($result['status'] === 'invalid') {
                                    $record->update([
                                        'status' => 'rejected',
                                        'reviewed_at' => now(),
                                    ]);
                                }

                                $stats[$result['status']]++;

                                // Throttle to avoid bot detection (2 seconds between requests)
                                if (!$records->last()->is($record)) {
                                    $validator->throttle(2000);
                                }
                            }

                            $message = sprintf(
                                'Valid: %d | Invalid: %d | CAPTCHA: %d | Errors: %d',
                                $stats['valid'],
                                $stats['invalid'],
                                $stats['captcha'],
                                $stats['error']
                            );

                            Notification::make()
                                ->title('Bulk URL Validation Complete')
                                ->body($message)
                                ->success()
                                ->send();
                        }),
                    BulkAction::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = $records->count();

                            foreach ($records as $record) {
                                $record->update([
                                    'status' => 'rejected',
                                    'reviewed_at' => now(),
                                ]);
                            }

                            Notification::make()
                                ->title('Bulk Rejection Complete')
                                ->body("Rejected {$count} listings")
                                ->danger()
                                ->send();
                        }),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->actionsColumnLabel('Actions')
            ->selectCurrentPageOnly()
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
        return Console::orderBy('display_order')->pluck('name', 'slug')->toArray();
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
            $updates['status'] = 'approved'; // Ready for review
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
                'status' => 'approved',
                'reviewed_at' => now(),
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
