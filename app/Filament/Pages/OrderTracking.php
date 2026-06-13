<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Settings\OperationsSettings;
use Illuminate\Contracts\Support\Htmlable;

class OrderTracking extends AdminOsModulePage
{
    public Order $order;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $slug = 'order-tracking/{order}';
    protected string $view = 'filament.pages.order-tracking';
    public function getModuleKey(): string { return 'dispatch'; }
    public function getHeading(): string|Htmlable { return ''; }
    public function mount(Order $order): void { $this->order=$order->load(['scenario','customer','dispatchAssignments.assignedUser.workerProfile','dispatchEvents','events','supportTickets','workerLocationPings','priceQuotes']); }
    public function getViewData(): array { return ['assignment'=>$this->order->activeDispatchAssignment(),'latestPing'=>$this->order->workerLocationPings->first(),'latestSupport'=>$this->order->supportTickets->first(),'operations'=>rescue(fn()=>app(OperationsSettings::class),null,report:false)]; }
}
