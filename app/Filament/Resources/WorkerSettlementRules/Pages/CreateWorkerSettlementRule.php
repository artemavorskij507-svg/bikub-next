<?php
namespace App\Filament\Resources\WorkerSettlementRules\Pages;
use App\Filament\Resources\WorkerSettlementRules\WorkerSettlementRuleResource;
use App\Services\Finance\WorkerSettlementRuleService;
use Filament\Resources\Pages\CreateRecord;
class CreateWorkerSettlementRule extends CreateRecord { protected static string $resource = WorkerSettlementRuleResource::class; protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model { return app(WorkerSettlementRuleService::class)->createDraft($data, auth()->user()); } }
