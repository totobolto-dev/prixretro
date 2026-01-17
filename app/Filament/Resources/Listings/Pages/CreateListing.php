<?php

namespace App\Filament\Resources\Listings\Pages;

use App\Filament\Resources\Listings\ListingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateListing extends CreateRecord
{
    protected static string $resource = ListingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
}
