<?php

namespace App\Filament\Resources\ServiceScenarios\Pages;

use App\Filament\Resources\ServiceScenarios\ServiceScenarioResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceScenario extends EditRecord
{
    protected static string $resource = ServiceScenarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
