<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\SoftDeletes;
class SecurityIncidentEvidenceLink extends Model{use SoftDeletes;protected $fillable=['security_incident_id','linked_type','linked_id','label','visibility','summary','added_by_id','added_at','metadata'];protected $hidden=['metadata'];protected function casts():array{return ['added_at'=>'datetime','metadata'=>'array'];}}
