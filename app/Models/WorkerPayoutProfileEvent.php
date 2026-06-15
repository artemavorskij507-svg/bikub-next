<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WorkerPayoutProfileEvent extends Model {public $timestamps=false;protected $fillable=['worker_payout_profile_id','worker_id','actor_id','event_type','description','metadata','created_at'];protected function casts():array{return ['metadata'=>'array','created_at'=>'datetime'];}}
