<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PricingRules\PricingRuleResource;
use App\Models\OrderPriceQuote;
use App\Models\PricingRule;
use Throwable;

class FinanceControl extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Finance Control';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Finance Control';

    protected string $view = 'filament.pages.finance-control';

    public function getModuleKey(): string
    {
        return 'finance';
    }

    public function getFinanceStatus(): array
    {
        try {
            return [
                'rules' => PricingRule::count(),
                'active_rules' => PricingRule::active()->count(),
                'quotes' => OrderPriceQuote::count(),
                'manual_review' => OrderPriceQuote::where('status', 'manual_review_required')->count(),
                'quoted_value' => OrderPriceQuote::where('status', 'estimated')->sum('total'),
            ];
        } catch (Throwable) {
            return array_fill_keys(['rules', 'active_rules', 'quotes', 'manual_review', 'quoted_value'], null);
        }
    }

    public function getPricingRulesUrl(): string { return PricingRuleResource::getUrl(); }
}
