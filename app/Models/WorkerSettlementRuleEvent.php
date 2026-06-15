<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerSettlementRuleEvent extends Model
{
    public $timestamps = false;
    protected $fillable = ['worker_settlement_rule_id', 'actor_id', 'event_type', 'from_value', 'to_value', 'description', 'metadata', 'created_at'];
    protected function casts(): array { return ['metadata' => 'array', 'created_at' => 'datetime']; }
    public function rule() { return $this->belongsTo(WorkerSettlementRule::class, 'worker_settlement_rule_id'); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
