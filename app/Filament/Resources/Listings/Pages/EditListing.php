<?php

namespace App\Filament\Resources\Listings\Pages;

use App\Filament\Resources\Listings\ListingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditListing extends EditRecord
{
    protected static string $resource = ListingResource::class;

    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Store the referrer URL to preserve query parameters
        $referrer = request()->headers->get('referer');
        if ($referrer && str_contains($referrer, '/admin/listings')) {
            session(['listings_return_url' => $referrer]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Pre-populate console_slug from either variant relationship or existing value
        if (isset($data['variant_id']) && $data['variant_id']) {
            $variant = \App\Models\Variant::find($data['variant_id']);
            if ($variant && $variant->console) {
                $data['console_slug'] = $variant->console->slug;
            }
        }
        // If variant_id is NULL but console_slug exists, keep it
        // (This handles listings classified without variant)

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If variant is selected, set console_slug from variant
        if (isset($data['variant_id']) && $data['variant_id']) {
            $variant = \App\Models\Variant::find($data['variant_id']);
            if ($variant && $variant->console) {
                $data['console_slug'] = $variant->console->slug;
            }
        }
        // If no variant selected but console is, auto-create default variant
        elseif (isset($data['console_slug']) && !empty($data['console_slug']) && empty($data['variant_id'])) {
            $console = \App\Models\Console::where('slug', $data['console_slug'])->first();

            if ($console) {
                // Check if default variant already exists
                $defaultVariant = \App\Models\Variant::where('console_id', $console->id)
                    ->where('is_default', true)
                    ->first();

                if (!$defaultVariant) {
                    // Create default variant with same name as console
                    $defaultVariant = \App\Models\Variant::create([
                        'console_id' => $console->id,
                        'name' => $console->name,
                        'slug' => $console->slug,
                        'full_slug' => $console->slug . '/' . $console->slug,
                        'is_default' => true,
                    ]);
                }

                $data['variant_id'] = $defaultVariant->id;
            }
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Redirect back to the listings index with preserved query parameters
        $returnUrl = session('listings_return_url');

        // Clear the session
        session()->forget('listings_return_url');

        // If we have a stored return URL, use it, otherwise fall back to index
        return $returnUrl ?? $this->getResource()::getUrl('index');
    }
}
