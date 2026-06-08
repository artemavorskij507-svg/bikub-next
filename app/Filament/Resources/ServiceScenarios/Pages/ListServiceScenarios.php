<?php

namespace App\Filament\Resources\ServiceScenarios\Pages;

use App\Filament\Resources\ServiceScenarios\ServiceScenarioResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceScenarios extends ListRecords
{
    protected static string $resource = ServiceScenarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
