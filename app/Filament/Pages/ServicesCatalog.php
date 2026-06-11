<?php

namespace App\Filament\Pages;

use App\Filament\Resources\ServiceCategories\ServiceCategoryResource;
use App\Filament\Resources\ServiceScenarios\ServiceScenarioResource;
use App\Models\ServiceCategory;
use App\Models\ServiceScenario;
use App\Models\ServiceScenarioField;
use Throwable;

class ServicesCatalog extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Service Catalog';

    protected static string|\UnitEnum|null $navigationGroup = 'Services';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Service Catalog';

    protected string $view = 'filament.pages.services-catalog';

    public function getModuleKey(): string
    {
        return 'services';
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getScenarioModules(): array
    {
        try {
            return ServiceScenario::with(['category', 'fields'])->withCount('fields')->orderBy('sort_order')->get()->map(fn (ServiceScenario $scenario) => [
                'code' => $scenario->scenario_key,
                'label' => $scenario->title,
                'scope' => $scenario->category?->title ?? $scenario->service_type,
                'status' => $scenario->status,
                'fields' => (string) $scenario->fields_count,
                'payment' => $scenario->requires_payment ? 'Required' : 'Not required',
                'tracking' => $scenario->supports_live_tracking ? 'Supported by contract; not connected' : 'Not supported',
                'pricing' => $scenario->pricingRules()->active()->exists() ? 'Rule configured' : 'No active rule',
                'edit_url' => ServiceScenarioResource::getUrl('edit', ['record' => $scenario]),
                'url' => route('public.cms.service-page', ['serviceSlug' => $scenario->slug]),
                'request_url' => route('public.orders.request', ['serviceSlug' => $scenario->slug]),
            ])->all();
        } catch (Throwable) {
            return [];
        }
    }

    public function getCatalogCounts(): array
    {
        try {
            return [
                'categories' => ServiceCategory::count(), 'active' => ServiceScenario::active()->count(),
                'draft' => ServiceScenario::where('status', 'draft')->count(), 'paused' => ServiceScenario::where('status', 'paused')->count(),
                'archived' => ServiceScenario::where('status', 'archived')->count(), 'fields' => ServiceScenarioField::count(),
                'configured' => ServiceScenario::whereHas('fields', fn ($query) => $query->active())->count(),
                'missing_fields' => ServiceScenario::active()->whereDoesntHave('fields', fn ($query) => $query->active())->count(),
            ];
        } catch (Throwable) {
            return array_fill_keys(['categories', 'active', 'draft', 'paused', 'archived', 'fields', 'configured', 'missing_fields'], null);
        }
    }

    public function getResourceLinks(): array
    {
        return ['categories' => ServiceCategoryResource::getUrl(), 'scenarios' => ServiceScenarioResource::getUrl()];
    }
}
