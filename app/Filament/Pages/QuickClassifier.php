<?php

namespace App\Filament\Pages;

use App\Models\Console;
use App\Models\Listing;
use App\Models\Variant;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;

class QuickClassifier extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static ?string $navigationLabel = 'Quick Classifier';

    protected static ?int $navigationSort = 1;

    protected static ?string $slug = 'quick-classifier';

    public string $view = 'filament.pages.quick-classifier';

    public function getLayout(): string
    {
        return 'components.layouts.quick-classifier';
    }

    public ?Listing $currentListing = null;
    public int $remainingCount = 0;
    public bool $isDone = false;

    // Form fields
    public ?string $selectedConsole = null;
    public ?int $selectedVariant = null;
    public ?string $selectedCompleteness = null;
    public ?string $newVariantName = null;

    public function mount(): void
    {
        $this->loadNext();
    }

    public function loadNext(): void
    {
        $this->currentListing = Listing::where('status', 'pending')
            ->orderBy('sold_date', 'asc')
            ->orderBy('id', 'asc')
            ->first();

        if (!$this->currentListing) {
            $this->isDone = true;
            $this->remainingCount = 0;
            return;
        }

        // Total pending count
        $totalPending = Listing::where('status', 'pending')->count();
        $this->remainingCount = $totalPending > 0 ? $totalPending - 1 : 0;

        // Pre-fill console and variant if already set
        $this->selectedConsole = $this->currentListing->console_slug;
        $this->selectedVariant = $this->currentListing->variant_id;
        $this->selectedCompleteness = $this->currentListing->completeness;
        $this->newVariantName = null;
    }

    public function getConsoles(): array
    {
        return Console::orderBy('name')->pluck('name', 'slug')->toArray();
    }

    public function getVariants(): array
    {
        if (!$this->selectedConsole) {
            return [];
        }

        return Variant::query()
            ->whereHas('console', fn($q) => $q->where('slug', $this->selectedConsole))
            ->pluck('name', 'id')
            ->toArray();
    }

    public function updatedSelectedConsole(): void
    {
        // Reset variant when console changes
        $this->selectedVariant = null;
    }

    public function approve(): void
    {
        if (!$this->currentListing) {
            return;
        }

        // Validate required fields
        if (!$this->selectedConsole) {
            Notification::make()
                ->title('Console required')
                ->body('Please select a console before approving')
                ->warning()
                ->send();
            return;
        }

        $variantId = $this->selectedVariant;

        // Create new variant if requested
        if (!empty($this->newVariantName)) {
            $console = Console::where('slug', $this->selectedConsole)->first();

            if (!$console) {
                Notification::make()
                    ->title('Error')
                    ->body('Console not found')
                    ->danger()
                    ->send();
                return;
            }

            $slug = Variant::generateSlugFromName($this->newVariantName, $console->name);
            $variant = Variant::create([
                'console_id' => $console->id,
                'name' => $this->newVariantName,
                'slug' => $slug,
                'full_slug' => $console->slug . '/' . $slug,
            ]);

            $variantId = $variant->id;
        }

        // If no variant selected, create/use default variant
        if (!$variantId) {
            $console = Console::where('slug', $this->selectedConsole)->first();

            if (!$console) {
                Notification::make()
                    ->title('Error')
                    ->body('Console not found')
                    ->danger()
                    ->send();
                return;
            }

            $defaultVariant = Variant::where('console_id', $console->id)
                ->where('is_default', true)
                ->first();

            if (!$defaultVariant) {
                $defaultVariant = Variant::create([
                    'console_id' => $console->id,
                    'name' => $console->name,
                    'slug' => $console->slug,
                    'full_slug' => $console->slug . '/' . $console->slug,
                    'is_default' => true,
                ]);
            }

            $variantId = $defaultVariant->id;
        }

        $this->currentListing->update([
            'console_slug' => $this->selectedConsole,
            'variant_id' => $variantId,
            'completeness' => $this->selectedCompleteness,
            'classification_status' => 'classified',
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        Notification::make()
            ->title('✓ Approved')
            ->success()
            ->send();

        $this->loadNext();
    }

    public function reject(): void
    {
        if (!$this->currentListing) {
            return;
        }

        $this->currentListing->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]);

        Notification::make()
            ->title('✗ Rejected')
            ->danger()
            ->send();

        $this->loadNext();
    }

    public function hold(): void
    {
        if (!$this->currentListing) {
            return;
        }

        $this->currentListing->update([
            'status' => 'on_hold',
            'reviewed_at' => now(),
        ]);

        Notification::make()
            ->title('⏸ On Hold')
            ->warning()
            ->send();

        $this->loadNext();
    }

    public function skip(): void
    {
        $this->loadNext();
    }
}
