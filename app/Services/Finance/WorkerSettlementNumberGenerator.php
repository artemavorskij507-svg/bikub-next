<?php
namespace App\Services\Finance;
use App\Models\WorkerSettlementEntry;
class WorkerSettlementNumberGenerator { public function generate():string{do{$number='SET-'.now()->format('Ymd').'-'.strtoupper(str()->random(6));}while(WorkerSettlementEntry::where('entry_number',$number)->exists());return $number;} }
