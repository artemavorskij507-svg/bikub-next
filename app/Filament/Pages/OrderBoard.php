<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Illuminate\Contracts\Support\Htmlable;

class OrderBoard extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel = 'Order Board';
    protected static string|\UnitEnum|null $navigationGroup = 'Dispatch';
    protected static ?int $navigationSort = 22;
    protected static ?string $slug = 'order-board';
    protected string $view = 'filament.pages.order-board';
    public function getModuleKey(): string { return 'dispatch'; }
    public function getHeading(): string|Htmlable { return ''; }
    public function getViewData(): array
    {
        $orders=Order::with(['scenario','customer','dispatchAssignments.assignedUser','supportTickets','workerLocationPings','priceQuotes'])->latest('updated_at')->get();
        return ['columns'=>[
            'Submitted'=>$orders->whereIn('status',['draft','submitted']),
            'Waiting Dispatch'=>$orders->filter(fn($o)=>in_array($o->status->value,['submitted','accepted'],true)&&!$o->activeDispatchAssignment()),
            'Assigned'=>$orders->filter(fn($o)=>$o->activeDispatchAssignment()&&$o->status->value!=='in_progress'),
            'In Progress'=>$orders->where('status','in_progress'),
            'Completed'=>$orders->where('status','completed'),
            'Cancelled'=>$orders->where('status','cancelled'),
        ],'total'=>$orders->count()];
    }
}
