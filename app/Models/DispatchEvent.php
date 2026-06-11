<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchEvent extends Model
{
    public const UPDATED_AT = null;
    protected $fillable = ['order_id', 'dispatch_assignment_id', 'actor_type', 'actor_id', 'event_type', 'from_status', 'to_status', 'payload', 'note', 'created_at'];
    protected function casts(): array { return ['payload' => 'array', 'created_at' => 'datetime']; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function assignment(): BelongsTo { return $this->belongsTo(DispatchAssignment::class, 'dispatch_assignment_id'); }
}
