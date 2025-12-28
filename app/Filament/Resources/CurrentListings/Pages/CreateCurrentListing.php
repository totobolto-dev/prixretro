<?php

namespace App\Filament\Resources\CurrentListings\Pages;

use App\Filament\Resources\CurrentListings\CurrentListingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCurrentListing extends CreateRecord
{
    protected static string $resource = CurrentListingResource::class;
}
