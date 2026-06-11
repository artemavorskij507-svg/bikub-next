<?php
namespace App\Filament\Resources\WorkerProfiles\Pages;
use App\Filament\Resources\WorkerProfiles\WorkerProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListWorkerProfiles extends ListRecords { protected static string $resource = WorkerProfileResource::class; protected function getHeaderActions(): array { return [CreateAction::make()]; } }
