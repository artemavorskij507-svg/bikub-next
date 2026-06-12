<?php

namespace App\Filament\Pages;

class SystemSecurity extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'System & Security';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'System & Security';

    protected string $view = 'filament.pages.system-security';

    public function getModuleKey(): string
    {
        return 'system';
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getRuntimeStatus(): array
    {
        return [
            $this->status('Application environment', (string) config('app.env'), 'Reported from config(app.env).', config('app.env') === 'production' ? 'review' : 'safe'),
            $this->status('Debug mode', config('app.debug') ? 'enabled' : 'disabled', 'Production must keep APP_DEBUG disabled.', config('app.debug') ? 'blocked' : 'ready'),
            $this->status('Database driver', (string) config('database.default'), 'PostgreSQL is expected for BiKuBe Next.', config('database.default') === 'pgsql' ? 'ready' : 'review'),
            $this->status('Cache driver', (string) config('cache.default'), 'Operational cache is configured through Laravel config.', 'ready'),
            $this->status('Queue driver', (string) config('queue.default'), 'Horizon/queue monitoring is available when workers run.', 'ready'),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getOperationalModules(): array
    {
        $pulseWorks = $this->routeAvailable('pulse') && $this->pulseTablesReady();
        $horizonState = $this->horizonWorkerState();
        $logViewerProtected = $this->logViewerUsesAuthMiddleware();

        return [
            $this->status('Health checks', $this->packageVersion('spatie/laravel-health'), $this->fileExists('config/health.php') ? 'Config exists; checks policy still needs production setup.' : 'Installed package config is not published.', $this->fileExists('config/health.php') ? 'configured' : 'review'),
            $this->status('Backups', $this->packageVersion('spatie/laravel-backup'), $this->fileExists('config/backup.php') ? 'Config exists; backup destination and schedule still need setup.' : 'Backup config is not published.', $this->fileExists('config/backup.php') ? 'configured' : 'review'),
            $this->status('Schedule monitor', $this->packageVersion('spatie/laravel-schedule-monitor'), $this->fileExists('config/schedule-monitor.php') ? 'Config and DB tables exist; tasks must be synced after real schedules are added.' : 'Schedule monitor config is not published.', $this->fileExists('config/schedule-monitor.php') ? 'configured' : 'review'),
            $this->status('Log Viewer', $this->routeAvailable('log-viewer.index') ? 'WORKS' : 'NEEDS SETUP', $this->routeAvailable('log-viewer.index') ? 'Route exists; external package UI; '.($logViewerProtected ? 'auth-protected by config.' : 'auth middleware missing.') : 'Route not registered.', $this->routeAvailable('log-viewer.index') && $logViewerProtected ? 'works' : 'setup', $this->routeAvailable('log-viewer.index') ? url('/log-viewer') : ''),
            $this->status('Web Push', config('webpush.vapid.public_key') ? 'CONFIGURED' : 'NEEDS SETUP', $this->packageVersion('laravel-notification-channels/webpush').' installed; config/subscription table exist; VAPID keys are not configured here.', config('webpush.vapid.public_key') ? 'configured' : 'setup'),
            $this->status('Horizon', $this->routeAvailable('horizon.index') ? 'ROUTE EXISTS' : 'NEEDS SETUP', 'Route exists; '.$horizonState.'. Start a Horizon worker before claiming queue processing is active.', $this->routeAvailable('horizon.index') ? 'review' : 'blocked', $this->routeAvailable('horizon.index') ? url('/horizon') : ''),
            $this->status('Pulse', $pulseWorks ? 'WORKS' : 'NEEDS SETUP', $pulseWorks ? 'Route exists and required Pulse tables exist; /pulse returned HTTP 200 in validation.' : 'Route exists but required Pulse tables/config are missing or not verified.', $pulseWorks ? 'works' : 'setup', $this->routeAvailable('pulse') ? url('/pulse') : ''),
            $this->status('Reverb', $this->packageVersion('laravel/reverb'), 'Realtime package is installed; production broadcasting config is still a separate setup task.', config('broadcasting.default') === 'reverb' ? 'configured' : 'setup'),
            $this->status('Environment indicator', $this->packageVersion('pxlrbt/filament-environment-indicator'), 'Package assets are present; panel plugin wiring can be refined in a later shell pass.', $this->packageInstalled('pxlrbt/filament-environment-indicator') ? 'installed' : 'blocked'),
            $this->status('Typed settings', $this->packageVersion('spatie/laravel-settings'), 'Platform, operations and map settings are persisted and exposed through permission-protected pages.', $this->tableExists('settings') ? 'configured' : 'blocked', route('filament.admin.pages.platform-settings')),
            $this->status('Media Library', $this->packageVersion('spatie/laravel-medialibrary'), 'Private worker-document uploads and optional CMS hero uploads are wired. Upload never auto-approves a document.', $this->tableExists('media') ? 'configured' : 'blocked', route('filament.admin.resources.worker-documents.index')),
            $this->status('Support Center', \App\Models\SupportTicket::count().' ticket(s)', 'Admin dashboard/resource, portals, protected attachments, sound polling and database notifications are configured. Internal notes remain Admin OS-only. Email, SMS, WebPush, Reverb and live chat are deferred.', $this->tableExists('notifications') && $this->routeAvailable('filament.admin.pages.support-center') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.support-center') ? route('filament.admin.pages.support-center') : ''),
            $this->status('Private worker documents', $this->routeAvailable('admin.worker-documents.download') ? 'ACTIVE' : 'NEEDS SETUP', 'Private local storage and role-authorized audited download route. Retention dates are operator-managed.', $this->routeAvailable('admin.worker-documents.download') ? 'configured' : 'blocked', route('filament.admin.resources.worker-documents.index')),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getSecurityFoundation(): array
    {
        return [
            $this->status('Fortify auth', $this->packageVersion('laravel/fortify'), 'Login, 2FA endpoints and passkey routes are provided by Fortify/Laravel.', 'installed'),
            $this->status('Sanctum API auth', $this->packageVersion('laravel/sanctum'), 'Token/session auth foundation exists; API scopes are not designed yet.', 'installed'),
            $this->status('Filament Shield', $this->packageVersion('bezhansalleh/filament-shield'), 'Shield-compatible Spatie roles are active; Shield management UI is intentionally not published yet.', 'configured'),
            $this->status('Spatie Permission', $this->packageVersion('spatie/laravel-permission'), 'Admin panel, module pages and sensitive resources enforce real permissions.', 'works'),
            $this->status('Activitylog', $this->packageVersion('spatie/laravel-activitylog'), 'Audit table and model logging are active for orders, worker applications and pricing rules.', 'works', route('filament.admin.pages.audit-log')),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getSecurityChecklist(): array
    {
        return [
            $this->status('2FA policy', 'needs setup', 'Fortify is installed, but admin role enforcement and recovery policy still need configuration.', 'setup'),
            $this->status('RBAC', 'active', 'Owner and worker assignments are real; module boundaries use named permissions.', 'ready'),
            $this->status('Audit logging', 'active', 'Audit visibility is available in the Admin Audit Log; additional models can be enrolled incrementally.', 'ready'),
            $this->status('Backups', 'needs setup', 'Backup package/config exists; destination, schedule and restore test are not configured.', 'setup'),
            $this->status('Webhook signatures', 'not implemented', 'Payment/provider webhooks require signed adapters in domain work.', 'blocked'),
            $this->status('Secrets', 'not inspected', 'No secrets are printed or changed here; production secret policy remains required.', 'review'),
            $this->status('HTTPS', 'not configured here', 'TLS/nginx/systemd are outside this approved scope.', 'blocked'),
            $this->status('Production debug off', config('app.debug') ? 'blocked' : 'ready', 'Uses config(app.debug); production must remain false.', config('app.debug') ? 'blocked' : 'ready'),
        ];
    }
}
