<?php

namespace App\Filament\Resources\ClassifiedListings\Pages;

use App\Filament\Resources\ClassifiedListings\ClassifiedListingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassifiedListings extends ListRecords
{
    protected static string $resource = ClassifiedListingResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
