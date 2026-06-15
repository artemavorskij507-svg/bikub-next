<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class SecurityIncidentPlaybookRun extends Model{protected $fillable=['security_incident_id','security_incident_playbook_id','status','started_by_id','completed_by_id','started_at','completed_at','metadata'];protected function casts():array{return ['started_at'=>'datetime','completed_at'=>'datetime','metadata'=>'array'];}}
