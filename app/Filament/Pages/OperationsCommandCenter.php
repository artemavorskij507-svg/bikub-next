<?php

namespace App\Filament\Pages;

use Illuminate\Support\Facades\Route;

class OperationsCommandCenter extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-command-line';

    protected static ?string $navigationLabel = 'Operations Command Center';

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Operations Command Center';

    protected string $view = 'filament.pages.operations-command-center';

    public function getModuleKey(): string
    {
        return 'operations';
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getRuntimeChecks(): array
    {
        return [
            [
                'label' => 'Laravel core',
                'value' => app()->version(),
                'status' => 'ready',
                'detail' => 'Fresh BiKuBe Next runtime is available.',
            ],
            [
                'label' => 'Filament Admin OS',
                'value' => $this->packageVersion('filament/filament'),
                'status' => 'ready',
                'detail' => 'Panel Builder is installed for the Admin OS shell.',
            ],
            [
                'label' => 'Database connection',
                'value' => config('database.default', 'unknown'),
                'status' => config('database.default') === 'pgsql' ? 'ready' : 'review',
                'detail' => 'PostgreSQL is expected. PostGIS schema is not introduced yet.',
            ],
            [
                'label' => 'Queue driver',
                'value' => config('queue.default', 'unknown'),
                'status' => in_array(config('queue.default'), ['database', 'redis'], true) ? 'ready' : 'review',
                'detail' => 'Operational jobs exist only after domain modules are added.',
            ],
            [
                'label' => 'Auth routes',
                'value' => Route::has('filament.admin.auth.login') ? 'real route' : 'missing',
                'status' => Route::has('filament.admin.auth.login') ? 'ready' : 'blocked',
                'detail' => 'Admin login route exists; domain roles are not configured here.',
            ],
            [
                'label' => 'Legacy port',
                'value' => 'not connected',
                'status' => 'safe',
                'detail' => 'Old BiKuBe data/resources are intentionally not connected.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getReadinessGates(): array
    {
        return [
            [
                'name' => 'Admin OS shell',
                'state' => 'Ready foundation',
                'status' => 'ready',
                'evidence' => [
                    'Dedicated Filament admin panel exists.',
                    'Premium dark BiKuBe theme is loaded through a render hook.',
                    'Dashboard and module routes are present.',
                ],
                'next' => 'Keep shell stable while adding real bounded modules.',
            ],
            [
                'name' => 'Operations data model',
                'state' => 'Not implemented',
                'status' => 'blocked',
                'evidence' => [
                    'No operations projection tables are introduced.',
                    'No fake operational counters are shown.',
                    'No migrations were run for this pass.',
                ],
                'next' => 'Define read-only launch readiness projections before any live KPI.',
            ],
            [
                'name' => 'Order and dispatch wiring',
                'state' => 'Order Engine works; dispatch blocked',
                'status' => 'review',
                'evidence' => [
                    'Order request model, lifecycle events and Orders resource exist.',
                    'Public request form creates submitted requests from active scenarios.',
                    'Payment, worker assignment and dispatch remain intentionally disconnected.',
                ],
                'next' => 'Implement customer identity boundary and audited Dispatch queues.',
            ],
            [
                'name' => 'GPS, maps and live operations',
                'state' => 'Requires domain pass',
                'status' => 'blocked',
                'evidence' => [
                    'No fake GPS markers are generated.',
                    'No fake map or route preview is embedded.',
                    'Worker presence storage is not implemented in BiKuBe Next yet.',
                ],
                'next' => 'Create worker presence and real GPS ping model after schema approval.',
            ],
            [
                'name' => 'Finance and payments',
                'state' => 'Provider not wired',
                'status' => 'blocked',
                'evidence' => [
                    'Finance Control route exists.',
                    'No fake payment state, payout state or income metrics are displayed.',
                    'No provider credentials or .env values were changed.',
                ],
                'next' => 'Design Payment Engine and Vipps/MobilePay adapter boundary.',
            ],
            [
                'name' => 'Observability foundation',
                'state' => 'Installed foundation',
                'status' => 'ready',
                'evidence' => [
                    'Health, backup, schedule monitor, log viewer and WebPush packages are installed.',
                    'Horizon and Pulse routes are present in the runtime.',
                    'No fake incident stream or operational KPI is shown.',
                ],
                'next' => 'Configure production checks, backup destination, scheduler sync and push VAPID keys.',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getOperationalFoundationModules(): array
    {
        $pulseWorks = $this->routeAvailable('pulse') && $this->pulseTablesReady();
        $horizonState = $this->horizonWorkerState();
        $logViewerProtected = $this->logViewerUsesAuthMiddleware();

        return [
            $this->status('Health checks', $this->fileExists('config/health.php') ? 'CONFIGURED' : 'INSTALLED', $this->packageVersion('spatie/laravel-health').' - '.($this->fileExists('config/health.php') ? 'Config exists; concrete checks still need production policy.' : 'Package installed, config missing.'), $this->fileExists('config/health.php') ? 'configured' : 'installed'),
            $this->status('Backups', $this->fileExists('config/backup.php') ? 'CONFIGURED' : 'INSTALLED', $this->packageVersion('spatie/laravel-backup').' - '.($this->fileExists('config/backup.php') ? 'Config exists; destination and schedule still need setup.' : 'Package installed, config missing.'), $this->fileExists('config/backup.php') ? 'configured' : 'installed'),
            $this->status('Schedule monitor', $this->fileExists('config/schedule-monitor.php') ? 'CONFIGURED' : 'INSTALLED', $this->packageVersion('spatie/laravel-schedule-monitor').' - Tables/config exist; no real scheduled jobs are synced yet.', $this->fileExists('config/schedule-monitor.php') ? 'configured' : 'installed'),
            $this->status('Log Viewer', $this->routeAvailable('log-viewer.index') && $logViewerProtected ? 'WORKS' : 'NEEDS SETUP', $this->packageVersion('opcodesio/log-viewer').' - '.($this->routeAvailable('log-viewer.index') ? 'Route exists; external package UI; '.($logViewerProtected ? 'auth-protected by config.' : 'auth middleware missing.') : 'Route is not available.'), $this->routeAvailable('log-viewer.index') && $logViewerProtected ? 'works' : 'setup', $this->routeAvailable('log-viewer.index') ? url('/log-viewer') : ''),
            $this->status('Horizon', $this->routeAvailable('horizon.index') ? 'ROUTE EXISTS' : 'NEEDS SETUP', $this->packageVersion('laravel/horizon').' - Route exists; '.$horizonState.'. Start a Horizon worker before claiming queue processing is active.', $this->routeAvailable('horizon.index') ? 'review' : 'blocked', $this->routeAvailable('horizon.index') ? url('/horizon') : ''),
            $this->status('Pulse', $pulseWorks ? 'WORKS' : 'NEEDS SETUP', $this->packageVersion('laravel/pulse').' - '.($pulseWorks ? 'Route exists and required Pulse tables exist; /pulse returned HTTP 200 in validation.' : 'Route exists but required Pulse tables/config are missing or not verified.'), $pulseWorks ? 'works' : 'setup', $this->routeAvailable('pulse') ? url('/pulse') : ''),
            $this->status('WebPush', config('webpush.vapid.public_key') ? 'CONFIGURED' : 'NEEDS SETUP', $this->packageVersion('laravel-notification-channels/webpush').' - Subscription table exists; VAPID keys are not configured in this pass.', config('webpush.vapid.public_key') ? 'configured' : 'setup'),
            $this->status('Environment indicator', $this->packageInstalled('pxlrbt/filament-environment-indicator') ? 'INSTALLED' : 'NEEDS SETUP', $this->packageVersion('pxlrbt/filament-environment-indicator').' - Assets/package exist; panel placement can be refined later.', $this->packageInstalled('pxlrbt/filament-environment-indicator') ? 'installed' : 'setup'),
        ];
    }
    /**
     * @return array<int, array<string, string>>
     */
    public function getModuleRoutes(): array
    {
        return collect(config('bikube-next.admin_modules', []))
            ->map(function (array $module, string $key): array {
                $routeName = match ($key) {
                    'operations' => 'filament.admin.pages.operations-command-center',
                    'dispatch' => 'filament.admin.pages.dispatch-center',
                    'orders' => 'filament.admin.pages.orders-hub',
                    'people' => 'filament.admin.pages.people-workforce',
                    'services' => 'filament.admin.pages.services-catalog',
                    'finance' => 'filament.admin.pages.finance-control',
                    'support' => 'filament.admin.pages.support-center',
                    'content' => 'filament.admin.pages.content-cms',
                    'system' => 'filament.admin.pages.system-security',
                    default => null,
                };

                return [
                    'label' => $module['label'] ?? ucfirst($key),
                    'status' => $module['status'] ?? 'skeleton only',
                    'url' => ($routeName && Route::has($routeName)) ? route($routeName) : '',
                    'route' => $routeName ?? 'not mapped',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getActionClassification(): array
    {
        return [
            [
                'label' => 'Open Admin OS dashboard',
                'classification' => 'WORKS',
                'detail' => 'Links to the real Filament dashboard route.',
                'url' => route('filament.admin.pages.dashboard'),
            ],
            [
                'label' => 'Review module route map',
                'classification' => 'WORKS',
                'detail' => 'Uses existing skeleton routes only.',
                'url' => route('filament.admin.pages.dispatch-center'),
            ],
            [
                'label' => 'Start dispatching orders',
                'classification' => 'DISABLED',
                'detail' => 'Requires Order Engine, worker availability and audit-backed assignment service.',
                'url' => '',
            ],
            [
                'label' => 'Show live map markers',
                'classification' => 'DISABLED',
                'detail' => 'Requires real worker GPS presence. No fake GPS or fake markers.',
                'url' => '',
            ],
            [
                'label' => 'Process payouts',
                'classification' => 'DISABLED',
                'detail' => 'Requires Payment Engine, ledger and provider-backed payout contract.',
                'url' => '',
            ],
        ];
    }

}
