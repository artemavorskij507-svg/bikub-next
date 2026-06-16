<?php

namespace App\Filament\Resources\ClassifiedListings\Pages;

use App\Filament\Resources\ClassifiedListings\ClassifiedListingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassifiedListing extends EditRecord
{
    protected static string $resource = ClassifiedListingResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
