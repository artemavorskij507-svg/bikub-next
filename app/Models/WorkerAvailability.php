<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerAvailability extends Model
{
    protected $fillable = ['user_id','worker_profile_id','status','source','available_from','available_until','last_seen_at','notes','metadata'];
    protected function casts(): array { return ['available_from'=>'datetime','available_until'=>'datetime','last_seen_at'=>'datetime','metadata'=>'array']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function workerProfile(): BelongsTo { return $this->belongsTo(WorkerProfile::class); }
}
