<?php

namespace App\Filament\Resources\PublicSitePages\Pages;

use App\Filament\Resources\PublicSitePages\PublicSitePageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicSitePages extends ListRecords
{
    protected static string $resource = PublicSitePageResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
