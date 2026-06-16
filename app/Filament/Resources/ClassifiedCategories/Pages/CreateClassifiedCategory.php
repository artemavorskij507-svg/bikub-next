<?php

namespace App\Filament\Resources\ClassifiedCategories\Pages;

use App\Filament\Resources\ClassifiedCategories\ClassifiedCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassifiedCategory extends CreateRecord
{
    protected static string $resource = ClassifiedCategoryResource::class;
}
