<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WorkerProfile extends Model
{
    protected $fillable = ['user_id', 'display_name', 'worker_type', 'status', 'phone', 'vehicle_type', 'service_area', 'can_deliver', 'can_move', 'can_handle_eco', 'can_do_handyman', 'can_tow', 'can_run_errands', 'approved_at', 'approved_by_user_id', 'rejected_at', 'rejection_reason', 'metadata'];
    protected function casts(): array { return ['can_deliver'=>'boolean','can_move'=>'boolean','can_handle_eco'=>'boolean','can_do_handyman'=>'boolean','can_tow'=>'boolean','can_run_errands'=>'boolean','approved_at'=>'datetime','rejected_at'=>'datetime','metadata'=>'array']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approvedBy(): BelongsTo { return $this->belongsTo(User::class, 'approved_by_user_id'); }
    public function availability(): HasOne { return $this->hasOne(WorkerAvailability::class); }
}
