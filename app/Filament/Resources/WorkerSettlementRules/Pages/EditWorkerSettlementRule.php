<?php
namespace App\Filament\Resources\WorkerSettlementRules\Pages;
use App\Filament\Resources\WorkerSettlementRules\WorkerSettlementRuleResource;
use App\Services\Finance\WorkerSettlementRuleService;
use Filament\Resources\Pages\EditRecord;
class EditWorkerSettlementRule extends EditRecord { protected static string $resource = WorkerSettlementRuleResource::class; protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model { return app(WorkerSettlementRuleService::class)->updateDraft($record, $data, auth()->user()); } }
