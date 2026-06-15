<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class SecurityAccessReviewEvent extends Model{public $timestamps=false;protected $fillable=['security_access_review_cycle_id','security_access_review_item_id','actor_id','event_type','description','metadata','created_at'];protected function casts():array{return ['metadata'=>'array','created_at'=>'datetime'];}}
