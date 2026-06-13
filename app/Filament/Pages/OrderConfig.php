<?php

namespace App\Filament\Pages;

use App\Models\ServiceScenario;
use Illuminate\Contracts\Support\Htmlable;

class OrderConfig extends AdminOsModulePage
{
    public ?int $selectedScenarioId=null;
    protected static string|\BackedEnum|null $navigationIcon='heroicon-o-share';
    protected static ?string $navigationLabel='Order Config';
    protected static string|\UnitEnum|null $navigationGroup='Services';
    protected static ?int $navigationSort=30;
    protected static ?string $slug='order-config';
    protected string $view='filament.pages.order-config';
    public function getModuleKey(): string { return 'services'; }
    public function getHeading(): string|Htmlable { return ''; }
    public function mount(): void { $this->selectedScenarioId=ServiceScenario::latest()->value('id'); }
    public function selectScenario(int $id): void { $this->selectedScenarioId=ServiceScenario::findOrFail($id)->id; }
    public function getViewData(): array { $s=ServiceScenario::with(['category','fields','pricingRules'])->orderBy('sort_order')->get(); return ['scenarios'=>$s,'selected'=>$s->firstWhere('id',$this->selectedScenarioId),'metrics'=>['scenarios'=>$s->count(),'active'=>$s->where('status','active')->count(),'fields'=>$s->sum(fn($x)=>$x->fields->count()),'priced'=>$s->filter(fn($x)=>$x->pricingRules->isNotEmpty())->count()]]; }
}
