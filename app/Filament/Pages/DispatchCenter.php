<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\DispatchAssignment;
use App\Models\Order;
use App\Models\SupportTicket;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\WorkerLocationPing;
use App\Services\Dispatch\DispatchEngine;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;

class DispatchCenter extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Dispatch Center';

    protected static string|\UnitEnum|null $navigationGroup = 'Dispatch';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Dispatch Center';

    protected string $view = 'filament.pages.dispatch-center';

    public function getModuleKey(): string
    {
        return 'dispatch';
    }

    public function markReady(int $orderId): void
    {
        try {
            app(DispatchEngine::class)->markReadyForDispatch(Order::findOrFail($orderId), 'Marked ready from Dispatch Center.');
            Notification::make()->title('Order marked dispatch-ready')->success()->send();
        } catch (ValidationException $exception) {
            Notification::make()->title(collect($exception->errors())->flatten()->first())->warning()->send();
        }
    }
    public function assignWorker(int $orderId, int $userId): void
    {
        try { app(DispatchEngine::class)->assign(Order::findOrFail($orderId), \App\Models\User::findOrFail($userId), auth()->user(), 'Assigned from Dispatch Center.'); Notification::make()->title('Order assigned')->success()->send(); }
        catch (ValidationException $e) { Notification::make()->title(collect($e->errors())->flatten()->first())->warning()->send(); }
    }

    public function createSupportTicket(int $orderId): void
    {
        $order = Order::findOrFail($orderId);
        if (SupportTicket::where('order_id', $order->id)->whereNotIn('status', ['resolved', 'closed'])->exists()) {
            Notification::make()->title('An open support ticket already exists for this order')->warning()->send();
            return;
        }
        $assignment = $order->activeDispatchAssignment();
        app(\App\Services\Support\SupportTicketService::class)->createTicket([
            'subject' => 'Dispatch issue: '.$order->order_number, 'category' => 'delivery_issue', 'priority' => 'normal',
            'source' => 'admin', 'visibility' => 'internal', 'order_id' => $order->id,
            'dispatch_assignment_id' => $assignment?->id, 'worker_profile_id' => $assignment?->assignedUser?->workerProfile?->id,
        ], auth()->user());
        Notification::make()->title('Support ticket created')->success()->send();
    }

    public function getDispatchData(): array    {
        $engine = app(DispatchEngine::class);
        $map = fn (Order $order) => [
            'id' => $order->id, 'number' => $order->order_number, 'scenario' => $order->scenario?->title ?? $order->service_scenario_key,
            'contact' => implode(' · ', array_filter([$order->customer_name, $order->customer_email, $order->customer_phone])),
            'submitted_at' => $order->submitted_at?->format('Y-m-d H:i'), 'estimated' => $order->estimated_total,
            'quote' => $order->latestPriceQuote()?->status ?? 'No quote', 'payment' => $order->payment_status->value,
            'ready' => $order->isDispatchReady(), 'latest_event' => $order->dispatchEvents->first()?->event_type ?? 'No dispatch event',
            'url' => OrderResource::getUrl('edit', ['record' => $order]),
            'eligible' => app(\App\Services\Workers\WorkerEligibilityService::class)->eligibleForOrder($order),
            'support_open' => SupportTicket::where('order_id',$order->id)->whereNotIn('status',['resolved','closed'])->count(),
            'support_urgent' => SupportTicket::where('order_id',$order->id)->whereNotIn('status',['resolved','closed'])->where(fn($q)=>$q->where('priority','urgent')->orWhere('status','escalated'))->count(),
            'support_latest' => SupportTicket::where('order_id',$order->id)->latest()->first(),
        ];

        try {
            return [
                'unassigned' => $engine->listUnassignedOrders()->map($map)->all(),
                'assigned' => DispatchAssignment::with(['order.scenario', 'order.dispatchEvents', 'assignedUser.workerAvailability'])->whereIn('status', ['assigned', 'accepted'])->latest()->get(),
                'eligible_workers' => $engine->eligibleWorkers(),
                'events' => \App\Models\DispatchEvent::latest()->limit(12)->get(),
                'location_ping_count' => WorkerLocationPing::count(),
                'latest_location_ping_at' => WorkerLocationPing::latest('captured_at')->value('captured_at'),
            ];
        } catch (\Throwable) {
            return [
                'unassigned' => [],
                'assigned' => collect(),
                'eligible_workers' => collect(),
                'events' => collect(),
                'location_ping_count' => 0,
                'latest_location_ping_at' => null,
            ];
        }
    }
}
