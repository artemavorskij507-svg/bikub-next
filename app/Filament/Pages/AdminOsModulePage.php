<?php

namespace App\Filament\Pages;

use Composer\InstalledVersions;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Throwable;

abstract class AdminOsModulePage extends Page
{
    protected string $view = 'filament.pages.admin-os-module';

    abstract public function getModuleKey(): string;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if (config('database.default') === 'sqlite' && ! extension_loaded('pdo_sqlite')) {
            return true;
        }

        $permission = match ((string) static::$navigationGroup) {
            'Operations' => 'admin.operations.view',
            'Dispatch' => 'admin.dispatch.view',
            'Orders' => 'admin.orders.view',
            'People' => 'admin.people.view',
            'Services' => 'admin.services.view',
            'Finance' => 'admin.finance.view',
            'Support' => 'admin.support.view',
            'Content' => 'admin.content.view',
            'System' => 'admin.system.view',
            default => 'admin.dashboard.view',
        };

        return $user->can($permission);
    }

    /**
     * @return array<string, mixed>
     */
    public function getModuleDescriptor(): array
    {
        $key = $this->getModuleKey();

        return config("bikube-next.admin_modules.{$key}", [
            'label' => static::$title ?? class_basename(static::class),
            'status' => 'skeleton',
            'purpose' => 'Module placeholder awaiting domain implementation.',
            'owning_context' => 'Unassigned',
            'safe_actions' => [],
            'blocked_actions' => ['No production action wired yet.'],
            'next_steps' => ['Define domain model and route/service contract.'],
        ]);
    }

    protected function packageInstalled(string $package): bool
    {
        return InstalledVersions::isInstalled($package);
    }

    protected function packageVersion(string $package): string
    {
        return $this->packageInstalled($package)
            ? InstalledVersions::getPrettyVersion($package) ?? 'installed'
            : 'missing';
    }

    protected function routeAvailable(string $route): bool
    {
        return Route::has($route);
    }

    protected function routeUrlIfAvailable(string $route): string
    {
        return $this->routeAvailable($route) ? route($route) : '';
    }

    protected function fileExists(string $relativePath): bool
    {
        return file_exists(base_path($relativePath));
    }

    protected function tableExists(string $table): bool
    {
        try {
            return Schema::hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }

    protected function pulseTablesReady(): bool
    {
        return $this->tableExists('pulse_values')
            && $this->tableExists('pulse_entries')
            && $this->tableExists('pulse_aggregates');
    }

    protected function logViewerUsesAuthMiddleware(): bool
    {
        $middleware = array_merge(
            (array) config('log-viewer.middleware', []),
            (array) config('log-viewer.api_middleware', []),
        );

        return in_array('auth', $middleware, true)
            || in_array('Filament\\Http\\Middleware\\Authenticate', $middleware, true);
    }

    protected function horizonWorkerState(): string
    {
        if (! $this->routeAvailable('horizon.index')) {
            return 'route missing';
        }

        try {
            $repositoryClass = 'Laravel\\Horizon\\Contracts\\MasterRepository';

            if (! interface_exists($repositoryClass)) {
                return 'worker status unavailable';
            }

            $masters = app($repositoryClass)->all();

            return count($masters) > 0 ? 'worker active' : 'worker inactive';
        } catch (Throwable) {
            return 'worker status unavailable';
        }
    }

    /**
     * @return array<string, string>
     */
    protected function status(string $label, string $state, string $detail, string $tone = 'review', string $url = ''): array
    {
        return compact('label', 'state', 'detail', 'tone', 'url');
    }
}
