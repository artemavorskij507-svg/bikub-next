<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class SecurityReviewerAccessEvent extends Model{public $timestamps=false;protected $fillable=['security_reviewer_access_id','user_id','actor_id','event_type','description','metadata','created_at'];protected function casts():array{return ['metadata'=>'array','created_at'=>'datetime'];}}
