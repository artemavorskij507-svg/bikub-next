<?php
namespace App\Console\Commands;
use App\Models\{User,WorkerPayoutReviewEvidence};use App\Services\Finance\WorkerPayoutEvidenceService;use Illuminate\Console\Command;
class EvidenceRescanCommand extends Command {
 protected $signature='security:evidence-rescan {--status=pending_scan,scan_unavailable,scan_failed} {--limit=50} {--dry-run} {--evidence-id=} {--force}';protected $description='Fail-closed rescan of eligible private worker payout evidence';
 public function handle(WorkerPayoutEvidenceService $service):int{$statuses=array_filter(explode(',',(string)$this->option('status')));$q=WorkerPayoutReviewEvidence::query()->whereIn('scan_status',$statuses);if($id=$this->option('evidence-id'))$q->whereKey($id);$items=$q->limit(min(max((int)$this->option('limit'),1),500))->get();$this->info("Evidence rescan candidates: {$items->count()}".($this->option('dry-run')?' (dry run)':''));if($this->option('dry-run'))return self::SUCCESS;$actor=User::whereHas('roles',fn($q)=>$q->whereIn('name',['owner','admin']))->first()??User::find(1);if(!$actor){$this->error('No authorized system actor is available.');return self::FAILURE;}foreach($items as $e){$result=$service->rescanEvidence($e,$actor);$this->line("#{$e->id}: {$result->scan_status}");}return self::SUCCESS;}
}
