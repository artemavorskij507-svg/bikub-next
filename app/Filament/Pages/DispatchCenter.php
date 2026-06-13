<?php

namespace App\Filament\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Models\DispatchAssignment;
use App\Models\DispatchEvent;
use App\Models\Order;
use App\Models\OperationZone;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WorkerLocationPing;
use App\Models\WorkerProfile;
use App\Services\Dispatch\DispatchEngine;
use App\Services\Support\SupportTicketService;
use App\Services\Workers\WorkerEligibilityService;
use App\Settings\OperationsSettings;
use App\Settings\MapSettings;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class DispatchCenter extends AdminOsModulePage
{
    public string $queueFilter = 'active';

    public ?int $selectedOrderId = null;

    public string $dispatchNote = '';

    public string $unassignReason = '';

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

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function mount(): void
    {
        $this->selectedOrderId = DispatchAssignment::query()
            ->whereIn('status', ['assigned', 'accepted'])
            ->latest('assigned_at')
            ->value('order_id')
            ?? Order::query()->latest('submitted_at')->value('id');
    }

    public function setQueueFilter(string $filter): void
    {
        abort_unless(in_array($filter, ['waiting', 'unassigned', 'assigned', 'active', 'risk', 'payment', 'support', 'completed'], true), 422);

        $this->queueFilter = $filter;
        $this->selectedOrderId = $this->dispatchQueue()->value('id');
    }

    public function selectOrder(int $orderId): void
    {
        abort_unless(auth()->user()?->can('admin.dispatch.view'), 403);
        $this->selectedOrderId = Order::findOrFail($orderId)->id;
        $this->dispatchNote = '';
        $this->unassignReason = '';
    }

    public function markReady(int $orderId): void
    {
        abort_unless(auth()->user()?->can('admin.dispatch.view'), 403);

        try {
            app(DispatchEngine::class)->markReadyForDispatch(Order::findOrFail($orderId), 'Marked ready from Dispatch Center.');
            Notification::make()->title('Order marked dispatch-ready')->success()->send();
        } catch (ValidationException $exception) {
            $this->validationWarning($exception);
        }
    }

    public function assignWorker(int $orderId, int $userId): void
    {
        abort_unless(auth()->user()?->can('admin.dispatch.view'), 403);

        try {
            app(DispatchEngine::class)->assign(
                Order::findOrFail($orderId),
                User::findOrFail($userId),
                auth()->user(),
                'Assigned from Dispatch Center.',
            );
            Notification::make()->title('Worker assigned')->success()->send();
        } catch (ValidationException $exception) {
            $this->validationWarning($exception);
        }
    }

    public function unassignWorker(int $orderId): void
    {
        abort_unless(auth()->user()?->can('admin.dispatch.view'), 403);
        $this->validate(['unassignReason' => ['required', 'string', 'max:2000']]);

        try {
            app(DispatchEngine::class)->unassign(Order::findOrFail($orderId), auth()->user(), $this->unassignReason);
            $this->unassignReason = '';
            Notification::make()->title('Worker unassigned')->success()->send();
        } catch (ValidationException $exception) {
            $this->validationWarning($exception);
        }
    }

    public function addDispatchNote(int $orderId): void
    {
        abort_unless(auth()->user()?->can('admin.dispatch.view'), 403);
        $this->validate(['dispatchNote' => ['required', 'string', 'max:3000']]);

        app(DispatchEngine::class)->recordDispatchEvent(
            Order::findOrFail($orderId),
            'dispatch.note',
            [],
            $this->dispatchNote,
        );

        $this->dispatchNote = '';
        Notification::make()->title('Dispatch note recorded')->success()->send();
    }

    public function createSupportTicket(int $orderId, bool $confirmedDuplicate = false): void
    {
        abort_unless(auth()->user()?->can('admin.support.manage'), 403);
        $order = Order::findOrFail($orderId);
        $openTicket = $order->supportTickets()->whereNotIn('status', ['resolved', 'closed'])->latest('updated_at')->first();

        if (! $confirmedDuplicate && $openTicket) {
            Notification::make()
                ->title('Open support ticket already exists')
                ->body($openTicket->ticket_number.' must be reviewed before another ticket is created.')
                ->warning()
                ->send();

            return;
        }

        $assignment = $order->activeDispatchAssignment();
        $ticket = app(SupportTicketService::class)->createTicket([
            'subject' => 'Dispatch issue: '.$order->order_number,
            'category' => 'delivery_issue',
            'priority' => 'normal',
            'source' => 'admin',
            'visibility' => 'internal',
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'dispatch_assignment_id' => $assignment?->id,
            'worker_profile_id' => $assignment?->assignedUser?->workerProfile?->id,
        ], auth()->user());

        Notification::make()->title('Support ticket '.$ticket->ticket_number.' created')->success()->send();
    }

    public function getViewData(): array
    {
        $selectedOrder = $this->selectedOrder();
        $operations = rescue(fn () => app(OperationsSettings::class), null, report: false);
        $activeStatuses = [OrderStatus::Submitted->value, OrderStatus::Accepted->value, OrderStatus::InProgress->value];
        $openSupport = fn (Builder $query) => $query->whereNotIn('status', ['resolved', 'closed']);
        $activeAssignments = DispatchAssignment::query()->whereIn('status', ['assigned', 'accepted']);
        $latestPing = $selectedOrder?->workerLocationPings->first()
            ?? $selectedOrder?->activeDispatchAssignment()?->assignedUser?->locationPings()->latest('captured_at')->first();
        $mapSettings = rescue(fn () => app(MapSettings::class), null, report: false);

        return [
            'metrics' => [
                'waiting' => app(DispatchEngine::class)->listUnassignedOrders()->filter(fn (Order $order) => $order->isDispatchReady())->count(),
                'unassigned' => app(DispatchEngine::class)->listUnassignedOrders()->count(),
                'active_assignments' => (clone $activeAssignments)->count(),
                'eligible_workers' => app(DispatchEngine::class)->eligibleWorkers()->count(),
                'support_issues' => Order::query()->whereIn('status', $activeStatuses)->whereHas('supportTickets', $openSupport)->count(),
                'payment_not_ready' => Order::query()->whereIn('status', $activeStatuses)->whereIn('payment_status', ['pending', 'failed'])->count(),
                'orders_with_ping' => WorkerLocationPing::query()->whereNotNull('order_id')->distinct('order_id')->count('order_id'),
                'completed_today' => Order::query()->whereDate('completed_at', today())->count(),
                'active_zones' => OperationZone::query()->where('status', 'active')->count(),
                'stale_gps' => WorkerLocationPing::query()
                    ->where('captured_at', '<', now()->subSeconds((int) ($mapSettings?->stale_gps_seconds ?? 120)))
                    ->count(),
            ],
            'queue' => $this->dispatchQueue()->get(),
            'selectedOrder' => $selectedOrder,
            'assignment' => $selectedOrder?->activeDispatchAssignment(),
            'workerCandidates' => $selectedOrder ? $this->workerCandidates($selectedOrder) : collect(),
            'latestSupportTicket' => $selectedOrder?->supportTickets->first(),
            'openSupportTickets' => $selectedOrder?->supportTickets->whereNotIn('status', ['resolved', 'closed']) ?? collect(),
            'latestPing' => $latestPing,
            'dispatchEvents' => $selectedOrder?->dispatchEvents->take(12) ?? collect(),
            'paymentProviderEnabled' => (bool) ($operations?->payment_provider_enabled ?? false),
            'customerTrackingEnabled' => (bool) ($operations?->customer_tracking_enabled ?? false),
            'gpsTrackingEnabled' => (bool) ($operations?->gps_tracking_enabled ?? true),
            'defaultMapLayer' => $mapSettings?->default_map_layer ?? 'standard',
        ];
    }

    private function dispatchQueue(): Builder
    {
        return Order::query()
            ->with([
                'scenario',
                'customer',
                'priceQuotes',
                'dispatchAssignments.assignedUser.workerProfile',
                'dispatchAssignments.assignedUser.workerAvailability',
                'dispatchEvents',
                'supportTickets.assignee',
                'workerLocationPings',
            ])
            ->when($this->queueFilter === 'waiting', fn (Builder $query) => $query
                ->whereIn('status', [OrderStatus::Submitted->value, OrderStatus::Accepted->value])
                ->whereDoesntHave('dispatchAssignments', fn (Builder $assignments) => $assignments->whereIn('status', ['assigned', 'accepted']))
                ->whereHas('dispatchEvents', fn (Builder $events) => $events->where('event_type', 'dispatch.ready')))
            ->when($this->queueFilter === 'unassigned', fn (Builder $query) => $query
                ->whereIn('status', [OrderStatus::Submitted->value, OrderStatus::Accepted->value])
                ->whereDoesntHave('dispatchAssignments', fn (Builder $assignments) => $assignments->whereIn('status', ['assigned', 'accepted'])))
            ->when($this->queueFilter === 'assigned', fn (Builder $query) => $query
                ->whereHas('dispatchAssignments', fn (Builder $assignments) => $assignments->whereIn('status', ['assigned', 'accepted'])))
            ->when($this->queueFilter === 'active', fn (Builder $query) => $query->where('status', OrderStatus::InProgress->value))
            ->when($this->queueFilter === 'risk', fn (Builder $query) => $query
                ->whereHas('supportTickets', fn (Builder $tickets) => $tickets
                    ->whereNotIn('status', ['resolved', 'closed'])
                    ->where(fn (Builder $risk) => $risk->where('priority', 'urgent')->orWhere('status', 'escalated'))))
            ->when($this->queueFilter === 'payment', fn (Builder $query) => $query->whereIn('payment_status', ['pending', 'failed']))
            ->when($this->queueFilter === 'support', fn (Builder $query) => $query
                ->whereHas('supportTickets', fn (Builder $tickets) => $tickets->whereNotIn('status', ['resolved', 'closed'])))
            ->when($this->queueFilter === 'completed', fn (Builder $query) => $query->whereDate('completed_at', today()))
            ->latest('updated_at')
            ->limit(40);
    }

    private function selectedOrder(): ?Order
    {
        return Order::query()
            ->with([
                'scenario',
                'customer',
                'priceQuotes',
                'dispatchAssignments.assignedUser.workerProfile',
                'dispatchAssignments.assignedUser.workerAvailability',
                'dispatchEvents',
                'supportTickets.assignee',
                'workerLocationPings',
                'events',
            ])
            ->find($this->selectedOrderId ?? $this->dispatchQueue()->value('id'));
    }

    private function workerCandidates(Order $order): Collection
    {
        $eligibility = app(WorkerEligibilityService::class);

        return WorkerProfile::query()
            ->with(['user.workerAvailability', 'user.locationPings'])
            ->get()
            ->map(function (WorkerProfile $profile) use ($order, $eligibility): array {
                $user = $profile->user;
                $eligible = $user ? $eligibility->userIsEligible($user, $order) : false;
                $reason = match (true) {
                    ! $user => 'No linked user account.',
                    $profile->status !== 'approved' => 'Worker profile is not approved.',
                    ! in_array($user->workerAvailability?->status, ['online', 'available'], true) => 'Worker is not online or available.',
                    ! $order->scenario => 'Order has no service scenario for capability matching.',
                    ! $eligible => 'Worker capability does not match this service scenario.',
                    default => 'Approved, online and capability-matched.',
                };

                return [
                    'user' => $user,
                    'profile' => $profile,
                    'eligible' => $eligible,
                    'reason' => $reason,
                    'availability' => $user?->workerAvailability?->status ?? 'offline',
                    'active_assignments' => $user ? DispatchAssignment::query()->where('assigned_user_id', $user->id)->whereIn('status', ['assigned', 'accepted'])->count() : 0,
                    'latest_ping' => $user?->locationPings->sortByDesc('captured_at')->first(),
                ];
            })
            ->sortByDesc('eligible')
            ->values();
    }

    private function validationWarning(ValidationException $exception): void
    {
        Notification::make()
            ->title((string) collect($exception->errors())->flatten()->first())
            ->warning()
            ->send();
    }
}
