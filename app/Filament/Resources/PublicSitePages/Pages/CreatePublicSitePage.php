<?php

namespace App\Filament\Resources\PublicSitePages\Pages;

use App\Filament\Resources\PublicSitePages\PublicSitePageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePublicSitePage extends CreateRecord
{
    protected static string $resource = PublicSitePageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['updated_by'] = auth()->id();

        return $data;
    }
}
