<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WorkerSettlementEvent extends Model { public $timestamps=false; protected $fillable=['settlement_entry_id','order_id','worker_id','actor_id','event_type','from_value','to_value','description','metadata','created_at']; protected function casts():array{return ['metadata'=>'array','created_at'=>'datetime'];} public function settlementEntry(){return $this->belongsTo(WorkerSettlementEntry::class);} }
