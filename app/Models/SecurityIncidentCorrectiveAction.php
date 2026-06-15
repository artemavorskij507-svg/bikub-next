<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\SoftDeletes;
class SecurityIncidentCorrectiveAction extends Model{use SoftDeletes;protected $fillable=['security_incident_id','title','description','status','priority','assigned_to_id','due_at','completed_by_id','completed_at','blocked_reason','completion_note','metadata'];protected function casts():array{return ['due_at'=>'datetime','completed_at'=>'datetime','metadata'=>'array'];}}
