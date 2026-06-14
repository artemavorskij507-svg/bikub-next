<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class WorkerSettlementEntry extends Model {
    use SoftDeletes;
    protected $fillable=['entry_number','worker_id','worker_profile_id','order_id','dispatch_assignment_id','billing_document_id','payment_record_id','completion_proof_id','status','currency','gross_amount','platform_fee_amount','worker_amount','tax_amount','calculation_basis','blocker_reason','ready_at','approved_at','paid_at','metadata','created_by_id','updated_by_id'];
    protected function casts():array{return ['gross_amount'=>'decimal:2','platform_fee_amount'=>'decimal:2','worker_amount'=>'decimal:2','tax_amount'=>'decimal:2','ready_at'=>'datetime','approved_at'=>'datetime','paid_at'=>'datetime','metadata'=>'array'];}
    public function worker(){return $this->belongsTo(User::class,'worker_id');} public function workerProfile(){return $this->belongsTo(WorkerProfile::class);} public function order(){return $this->belongsTo(Order::class);} public function assignment(){return $this->belongsTo(DispatchAssignment::class,'dispatch_assignment_id');} public function billingDocument(){return $this->belongsTo(BillingDocument::class);} public function paymentRecord(){return $this->belongsTo(PaymentRecord::class);} public function completionProof(){return $this->belongsTo(OrderCompletionProof::class);} public function events(){return $this->hasMany(WorkerSettlementEvent::class,'settlement_entry_id')->latest('created_at');}
}
