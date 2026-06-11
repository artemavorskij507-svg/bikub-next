<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerLocationPing extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'worker_profile_id', 'order_id', 'dispatch_assignment_id', 'latitude', 'longitude', 'accuracy_meters', 'heading', 'speed_mps', 'source', 'captured_at', 'metadata', 'created_at'];

    protected function casts(): array
    {
        return ['latitude' => 'decimal:7', 'longitude' => 'decimal:7', 'accuracy_meters' => 'decimal:2', 'heading' => 'decimal:2', 'speed_mps' => 'decimal:3', 'captured_at' => 'datetime', 'metadata' => 'array', 'created_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function order() { return $this->belongsTo(Order::class); }
    public function assignment() { return $this->belongsTo(DispatchAssignment::class, 'dispatch_assignment_id'); }
}
