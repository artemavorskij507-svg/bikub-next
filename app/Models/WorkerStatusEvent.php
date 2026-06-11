<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerStatusEvent extends Model
{
    public $timestamps = false;
    protected $fillable = ['user_id','worker_profile_id','actor_type','actor_id','event_type','from_status','to_status','payload','note','created_at'];
    protected function casts(): array { return ['payload'=>'array','created_at'=>'datetime']; }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function workerProfile(): BelongsTo { return $this->belongsTo(WorkerProfile::class); }
}
