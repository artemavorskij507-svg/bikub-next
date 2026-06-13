<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationZoneEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['operation_zone_id', 'actor_id', 'event_type', 'description', 'metadata', 'created_at'];

    protected function casts(): array { return ['metadata' => 'array', 'created_at' => 'datetime']; }

    public function zone() { return $this->belongsTo(OperationZone::class, 'operation_zone_id'); }
    public function actor() { return $this->belongsTo(User::class, 'actor_id'); }
}
