<?php

namespace App\Filament\Resources\ClassifiedCategories\Pages;

use App\Filament\Resources\ClassifiedCategories\ClassifiedCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassifiedCategories extends ListRecords
{
    protected static string $resource = ClassifiedCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
