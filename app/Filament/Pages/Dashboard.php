<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'BiKuBe Admin OS';

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getAdminModules(): array
    {
        return config('bikube-next.admin_modules', []);
    }
}
