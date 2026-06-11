<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchAssignment extends Model
{
    protected $fillable = ['order_id', 'assigned_user_id', 'assigned_by_user_id', 'status', 'assignment_type', 'assigned_at', 'accepted_at', 'rejected_at', 'cancelled_at', 'cancellation_reason', 'notes', 'metadata'];
    protected function casts(): array { return ['assigned_at' => 'datetime', 'accepted_at' => 'datetime', 'rejected_at' => 'datetime', 'cancelled_at' => 'datetime', 'metadata' => 'array']; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function assignedUser(): BelongsTo { return $this->belongsTo(User::class, 'assigned_user_id'); }
    public function assignedBy(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by_user_id'); }
}
