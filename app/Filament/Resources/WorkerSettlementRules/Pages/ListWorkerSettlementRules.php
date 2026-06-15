<?php
namespace App\Filament\Resources\WorkerSettlementRules\Pages;
use App\Filament\Resources\WorkerSettlementRules\WorkerSettlementRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
class ListWorkerSettlementRules extends ListRecords { protected static string $resource = WorkerSettlementRuleResource::class; protected function getHeaderActions(): array { return [CreateAction::make()]; } }
