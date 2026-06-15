<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class SecurityAccessReviewCycle extends Model{protected $fillable=['cycle_number','status','review_type','scope','opened_by_id','completed_by_id','opened_at','due_at','completed_at','notes','metadata'];protected function casts():array{return ['opened_at'=>'datetime','due_at'=>'datetime','completed_at'=>'datetime','metadata'=>'array'];}public function items(){return $this->hasMany(SecurityAccessReviewItem::class);}public function events(){return $this->hasMany(SecurityAccessReviewEvent::class)->latest('created_at');}}
