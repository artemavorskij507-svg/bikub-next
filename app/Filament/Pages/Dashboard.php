<?php

namespace App\Filament\Pages;

use App\Models\CmsPage;
use App\Models\Order;
use App\Models\OrderPriceQuote;
use App\Models\PricingRule;
use App\Models\SeoMetadata;
use App\Models\ServiceCategory;
use App\Models\ServicePage;
use App\Models\ServiceScenario;
use App\Models\ServiceScenarioField;
use Filament\Pages\Dashboard as BaseDashboard;
use Throwable;

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

    public function getProof(): array
    {
        try {
            $latestOrder = Order::latest()->first();
            $latestQuote = OrderPriceQuote::latest()->first();
            return [
                'services' => ['categories' => ServiceCategory::count(), 'scenarios' => ServiceScenario::active()->count(), 'fields' => ServiceScenarioField::active()->count()],
                'orders' => ['total' => Order::count(), 'submitted' => Order::where('status', 'submitted')->count(), 'latest' => $latestOrder?->order_number ?? 'None'],
                'intake' => ['fields' => ServiceScenarioField::active()->count(), 'configured' => ServiceScenario::whereHas('fields', fn ($q) => $q->active())->count(), 'missing' => ServiceScenario::active()->whereDoesntHave('fields', fn ($q) => $q->active())->count()],
                'pricing' => ['rules' => PricingRule::count(), 'active' => PricingRule::active()->count(), 'quotes' => OrderPriceQuote::count(), 'latest' => $latestQuote ? number_format((float) $latestQuote->total, 2).' '.$latestQuote->currency : 'None', 'manual' => OrderPriceQuote::where('status', 'manual_review_required')->count()],
                'cms' => ['pages' => CmsPage::count(), 'services' => ServicePage::count(), 'seo' => SeoMetadata::count()],
            ];
        } catch (Throwable) {
            return [];
        }
    }
}
