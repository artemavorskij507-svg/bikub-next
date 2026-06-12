<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission,Role};
use Spatie\Permission\PermissionRegistrar;
class BiKuBeSupportPermissionSeeder extends Seeder {
 public function run():void {
  $map=[
   'owner'=>['admin.support.view','admin.support.manage','admin.support.assign','admin.support.resolve','admin.support.internal_notes','admin.support.attachments'],
   'admin'=>['admin.support.view','admin.support.manage','admin.support.assign','admin.support.resolve','admin.support.internal_notes','admin.support.attachments'],
   'support'=>['admin.support.view','admin.support.manage','admin.support.assign','admin.support.resolve','admin.support.internal_notes','admin.support.attachments'],
   'dispatcher'=>['admin.support.view','admin.support.manage'],
   'workforce_manager'=>['admin.support.view','admin.support.manage'],
   'worker'=>['worker.support.view','worker.support.reply'],
  ];
  foreach(array_unique(array_merge(...array_values($map))) as $name) Permission::findOrCreate($name,'web');
  foreach($map as $role=>$permissions) Role::findOrCreate($role,'web')->givePermissionTo($permissions);
  Permission::findOrCreate('account.support.view','web'); Permission::findOrCreate('account.support.create','web');
  app(PermissionRegistrar::class)->forgetCachedPermissions();
 }
}
