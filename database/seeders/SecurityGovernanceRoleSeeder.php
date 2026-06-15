<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;use Spatie\Permission\Models\{Permission,Role};use Spatie\Permission\PermissionRegistrar;
class SecurityGovernanceRoleSeeder extends Seeder {
 public function run():void{$map=['security_reviewer'=>['admin.security.retention.view','admin.security.retention.dry_run','admin.security.retention.approve','admin.security.file_scanner.test','admin.security.evidence_rescan.run'],'compliance_reviewer'=>['admin.security.retention.view','admin.security.retention.approve','admin.worker_payout_evidence.download']];foreach(array_unique(array_merge(...array_values($map))) as $p)Permission::findOrCreate($p,'web');foreach($map as $role=>$permissions)Role::findOrCreate($role,'web')->givePermissionTo($permissions);app(PermissionRegistrar::class)->forgetCachedPermissions();}
}
