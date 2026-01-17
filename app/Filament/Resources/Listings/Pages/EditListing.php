<?php

namespace App\Filament\Resources\Listings\Pages;

use App\Filament\Resources\Listings\ListingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditListing extends EditRecord
{
    protected static string $resource = ListingResource::class;

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
        // Ensure console_slug is saved from the form
        // If variant is selected, also set console_slug from variant
        if (isset($data['variant_id']) && $data['variant_id']) {
            $variant = \App\Models\Variant::find($data['variant_id']);
            if ($variant && $variant->console) {
                $data['console_slug'] = $variant->console->slug;
            }
        }
        // If no variant selected, console_slug should be set from form dropdown
        // It's already in $data['console_slug'] from the form

        return $data;
    }
}
