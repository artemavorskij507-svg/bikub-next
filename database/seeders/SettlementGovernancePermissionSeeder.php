<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission,Role};
use Spatie\Permission\PermissionRegistrar;
class SettlementGovernancePermissionSeeder extends Seeder {
 public function run():void {
  $map=[
   'owner'=>['admin.settlement_reviews.view','admin.settlement_reviews.request','admin.settlement_reviews.legal.approve','admin.settlement_reviews.tax.approve','admin.settlement_reviews.finance.approve','admin.settlement_reviews.compliance.approve','admin.settlement_reviews.reject','admin.settlement_reviews.cancel','admin.settlement_reviews.override_separation','admin.settlement_rules.view','admin.settlement_rules.manage','admin.settlement_rules.activate','admin.settlement_rules.archive','admin.payouts.view','admin.payouts.manage','admin.payouts.provider_settings','admin.payouts.prepare_instruction','admin.payouts.mark_paid','admin.payouts.cancel'],
   'admin'=>['admin.settlement_reviews.view','admin.settlement_reviews.request','admin.settlement_reviews.reject','admin.settlement_reviews.cancel','admin.settlement_rules.view','admin.settlement_rules.manage','admin.settlement_rules.activate','admin.settlement_rules.archive','admin.payouts.view','admin.payouts.manage','admin.payouts.provider_settings'],
   'finance'=>['admin.settlement_reviews.view','admin.settlement_reviews.request','admin.settlement_reviews.finance.approve','admin.settlement_reviews.reject','admin.settlement_rules.view','admin.settlement_rules.manage','admin.payouts.view','admin.payouts.manage'],
   'legal_reviewer'=>['admin.settlement_reviews.view','admin.settlement_reviews.legal.approve'],
   'tax_reviewer'=>['admin.settlement_reviews.view','admin.settlement_reviews.tax.approve'],
   'compliance_reviewer'=>['admin.settlement_reviews.view','admin.settlement_reviews.compliance.approve'],
  ];
  foreach(array_unique(array_merge(...array_values($map))) as $permission)Permission::findOrCreate($permission,'web');
  foreach($map as $role=>$permissions)Role::findOrCreate($role,'web')->givePermissionTo($permissions);
  app(PermissionRegistrar::class)->forgetCachedPermissions();
 }
}
