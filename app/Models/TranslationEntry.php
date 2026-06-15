<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class TranslationEntry extends Model{protected $fillable=['group','key','locale','value','status','reviewed_by_id','approved_by_id','reviewed_at','approved_at','metadata'];protected function casts():array{return ['reviewed_at'=>'datetime','approved_at'=>'datetime','metadata'=>'array'];}public function events(){return $this->hasMany(TranslationEntryEvent::class);}}
