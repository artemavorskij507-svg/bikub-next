<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class BiKuBeAdminRoleSeeder extends Seeder
{
    private const ROLE_PERMISSIONS = [
        'owner' => ['*'],
        'admin' => ['*'],
        'dispatcher' => ['admin.dashboard.view', 'admin.operations.view', 'admin.dispatch.view', 'admin.orders.view', 'admin.orders.manage', 'admin.services.view'],
        'finance' => ['admin.dashboard.view', 'admin.orders.view', 'admin.finance.view', 'admin.finance.manage'],
        'support' => ['admin.dashboard.view', 'admin.orders.view', 'admin.support.view', 'admin.support.manage'],
        'content_manager' => ['admin.dashboard.view', 'admin.content.view', 'admin.content.manage'],
        'workforce_manager' => ['admin.dashboard.view', 'admin.people.view', 'admin.people.manage'],
        'security_manager' => ['admin.dashboard.view', 'admin.system.view', 'admin.system.manage', 'admin.audit.view'],
        'worker' => ['worker.cockpit.view', 'worker.orders.manage', 'worker.location.ping'],
    ];

    private const PERMISSIONS = [
        'admin.dashboard.view', 'admin.operations.view', 'admin.dispatch.view',
        'admin.orders.view', 'admin.orders.manage', 'admin.services.view',
        'admin.services.manage', 'admin.finance.view', 'admin.finance.manage',
        'admin.people.view', 'admin.people.manage', 'admin.content.view',
        'admin.content.manage', 'admin.support.view', 'admin.support.manage',
        'admin.system.view', 'admin.system.manage', 'admin.audit.view',
        'worker.cockpit.view', 'worker.orders.manage', 'worker.location.ping',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function (): void {
            $permissions = collect(self::PERMISSIONS)
                ->mapWithKeys(fn (string $name) => [$name => Permission::findOrCreate($name, 'web')]);

            foreach (self::ROLE_PERMISSIONS as $roleName => $permissionNames) {
                $role = Role::findOrCreate($roleName, 'web');
                $role->syncPermissions($permissionNames === ['*'] ? $permissions->values() : $permissions->only($permissionNames)->values());
            }

            $workers = User::query()->whereHas('workerProfile')->get();
            foreach ($workers as $worker) {
                $worker->syncRoles(['worker']);
            }

            $adminCandidates = User::query()->whereDoesntHave('workerProfile')->get();
            if ($adminCandidates->count() !== 1) {
                throw new RuntimeException('Owner role not assigned: expected exactly one non-worker admin candidate.');
            }

            $adminCandidates->first()->syncRoles(['owner']);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
