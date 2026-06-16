<?php

namespace App\Filament\Resources\ClassifiedCategories\Pages;

use App\Filament\Resources\ClassifiedCategories\ClassifiedCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClassifiedCategory extends EditRecord
{
    protected static string $resource = ClassifiedCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
