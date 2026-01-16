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
        // Pre-populate console_slug from variant relationship
        if (isset($data['variant_id']) && $data['variant_id']) {
            $variant = \App\Models\Variant::find($data['variant_id']);
            if ($variant && $variant->console) {
                $data['console_slug'] = $variant->console->slug;
            }
        } elseif (isset($data['console_slug']) && !empty($data['console_slug'])) {
            // console_slug already exists (for listings classified without variant)
            // Keep it as is
        }

        return $data;
    }
}
