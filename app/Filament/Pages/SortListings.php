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
use Illuminate\Support\Facades\DB;

class SortListings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static ?string $navigationLabel = 'Sort Listings';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'sort-listings';

    protected static string $view = 'filament.pages.sort-listings';

    public $listings = [];
    public $currentIndex = 0;
    public $stats = [];
    public $filterStatus = 'unclassified';
    public $filterConsole = '';
    public $searchTerm = '';

    // Form state
    public $selectedConsole = '';
    public $selectedVariant = '';
    public $selectedStatus = 'keep';

    public function mount(): void
    {
        $this->loadListings();
        $this->updateStats();
    }

    public function loadListings(): void
    {
        $query = Listing::query()
            ->whereNull('variant_id')  // Items not yet assigned to a variant
            ->orWhere('status', 'unclassified');

        if ($this->filterStatus && $this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterConsole) {
            $query->where('console_slug', $this->filterConsole);
        }

        if ($this->searchTerm) {
            $query->where('title', 'like', '%' . $this->searchTerm . '%');
        }

        $this->listings = $query->latest()->get()->toArray();
    }

    public function updateStats(): void
    {
        $this->stats = [
            'total' => Listing::whereNull('variant_id')->count(),
            'unclassified' => Listing::where('status', 'unclassified')->count(),
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
}
