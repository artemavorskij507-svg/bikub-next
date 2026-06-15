<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\SoftDeletes;
class SecurityIncidentPlaybook extends Model{use SoftDeletes;protected $fillable=['playbook_key','name','description','incident_type','severity','is_active','steps','created_by_id','updated_by_id'];protected function casts():array{return ['is_active'=>'boolean','steps'=>'array'];}}
