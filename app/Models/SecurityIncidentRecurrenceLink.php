<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class SecurityIncidentRecurrenceLink extends Model{protected $fillable=['security_incident_id','related_incident_id','relation_type','confidence','reason','created_by_id','confirmed_by_id','confirmed_at','metadata'];protected function casts():array{return ['confirmed_at'=>'datetime','metadata'=>'array'];}}
