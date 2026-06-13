<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class OperationZone extends Model
{
    use LogsActivity, SoftDeletes;

    protected $fillable = ['name', 'type', 'status', 'geometry_type', 'coordinates', 'radius_meters', 'color', 'created_by_id', 'updated_by_id', 'starts_at', 'ends_at', 'metadata'];

    protected function casts(): array
    {
        return ['coordinates' => 'array', 'starts_at' => 'datetime', 'ends_at' => 'datetime', 'metadata' => 'array'];
    }

    public function creator() { return $this->belongsTo(User::class, 'created_by_id'); }
    public function updatedBy() { return $this->belongsTo(User::class, 'updated_by_id'); }
    public function events() { return $this->hasMany(OperationZoneEvent::class)->latest('created_at'); }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnly(['name', 'type', 'status', 'geometry_type', 'coordinates', 'radius_meters', 'starts_at', 'ends_at'])->logOnlyDirty();
    }
}
