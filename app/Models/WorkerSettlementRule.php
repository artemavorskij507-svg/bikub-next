<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkerSettlementRule extends Model
{
    use SoftDeletes;

    protected $fillable = ['rule_number', 'name', 'status', 'service_scenario_key', 'worker_role', 'calculation_type', 'worker_share_percent', 'platform_fee_percent', 'fixed_worker_amount', 'currency', 'min_order_amount', 'max_order_amount', 'legal_review_status', 'tax_review_status', 'effective_from', 'effective_until', 'approved_by_id', 'approved_at', 'rejected_by_id', 'rejected_at', 'approval_note', 'metadata', 'created_by_id', 'updated_by_id'];

    protected function casts(): array
    {
        return ['worker_share_percent' => 'decimal:2', 'platform_fee_percent' => 'decimal:2', 'fixed_worker_amount' => 'decimal:2', 'min_order_amount' => 'decimal:2', 'max_order_amount' => 'decimal:2', 'effective_from' => 'date', 'effective_until' => 'date', 'approved_at' => 'datetime', 'rejected_at' => 'datetime', 'metadata' => 'array'];
    }

    public function events() { return $this->hasMany(WorkerSettlementRuleEvent::class)->latest('created_at'); }
    public function reviews() { return $this->hasMany(WorkerSettlementRuleReview::class)->latest('created_at'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by_id'); }
}
