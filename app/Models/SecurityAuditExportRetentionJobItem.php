<?php
namespace App\Models;use Illuminate\Database\Eloquent\Model;
class SecurityAuditExportRetentionJobItem extends Model{protected $fillable=['security_audit_export_retention_job_id','security_audit_export_id','status','reason','metadata'];protected function casts():array{return ['metadata'=>'array'];}}
