<?php

namespace App\Filament\Pages;

class PeopleWorkforce extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'People & Workforce';

    protected static string|\UnitEnum|null $navigationGroup = 'People';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'People & Workforce';

    protected string $view = 'filament.pages.people-workforce';

    public function getModuleKey(): string
    {
        return 'people';
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getPeopleFoundation(): array
    {
        return [
            $this->status('Filament Shield', $this->packageVersion('bezhansalleh/filament-shield'), 'RBAC UI package is installed; permissions are not generated/configured in this pass.', 'setup'),
            $this->status('Spatie Permission', $this->packageVersion('spatie/laravel-permission'), 'Role/permission backend foundation is installed.', 'installed'),
            $this->status('Fortify auth', $this->packageVersion('laravel/fortify'), 'Authentication and 2FA foundation exists.', 'installed'),
            $this->status('Sanctum', $this->packageVersion('laravel/sanctum'), 'API/session auth foundation exists for future Worker PWA/API separation.', 'installed'),
            $this->status('Workforce domain', 'not implemented', 'No worker profile, vehicle, compliance document or availability domain is created yet.', 'blocked'),
            $this->status('Fake workers', 'not present', 'This page does not invent workers or staff counts.', 'safe'),
        ];
    }

    public function getWorkerCounts(): array
    {
        try {
            return [
                'users' => \App\Models\User::count(), 'profiles' => \App\Models\WorkerProfile::count(),
                'approved' => \App\Models\WorkerProfile::where('status','approved')->count(),
                'pending' => \App\Models\WorkerProfile::where('status','pending')->count(),
                'online' => \App\Models\WorkerAvailability::whereIn('status',['online','available'])->count(),
                'blocked' => \App\Models\WorkerProfile::whereIn('status',['rejected','suspended'])->count(),
                'applications' => \App\Models\WorkerApplication::count(),
                'submitted' => \App\Models\WorkerApplication::where('status','submitted')->count(),
                'documents' => \App\Models\WorkerDocument::where('status','pending')->count(),
                'eligible' => app(\App\Services\Dispatch\DispatchEngine::class)->eligibleWorkers()->count(),
                'pending_invitations' => \App\Models\WorkerAccountInvitation::where('status','pending')->count(),
                'accepted_invitations' => \App\Models\WorkerAccountInvitation::where('status','accepted')->count(),
                'location_pings' => \App\Models\WorkerLocationPing::count(),
            ];
        } catch (\Throwable) { return array_fill_keys(['users','profiles','approved','pending','online','blocked','applications','submitted','documents','eligible','pending_invitations','accepted_invitations'], 0); }
    }
}
