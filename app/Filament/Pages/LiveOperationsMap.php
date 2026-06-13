<?php

namespace App\Filament\Pages;

use App\Models\DispatchAssignment;
use App\Models\WorkerLocationPing;
use App\Models\OperationZone;
use App\Models\Order;
use App\Models\WorkerProfile;
use App\Services\Dispatch\DispatchEngine;
use App\Services\Operations\OperationZoneService;
use App\Services\Support\SupportTicketService;
use Filament\Notifications\Notification;
use App\Settings\MapSettings;
use App\Settings\OperationsSettings;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Validation\ValidationException;

class LiveOperationsMap extends AdminOsModulePage
{
    public float $contextLat = 68.4385;
    public float $contextLng = 17.4272;
    public string $zoneName = '';
    public string $zoneType = 'priority_area';
    public int $zoneRadius = 500;
    public string $zoneNote = '';
    public string $zoneDeactivateReason = '';
    public string $dispatchLocationNote = '';
    public string $supportSubject = '';
    public string $supportPriority = 'normal';
    public string $supportCategory = 'delivery_issue';
    public string $supportInternalNote = '';
    public ?string $activeContextEditor = null;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationLabel = 'Live Operations Map';

    protected static string|\UnitEnum|null $navigationGroup = 'Dispatch';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Live Operations Map';

    protected string $view = 'filament.pages.live-operations-map';

    public static function canAccess(): bool
    {
        if (config('database.default') === 'sqlite' && ! extension_loaded('pdo_sqlite')) {
            return auth()->check();
        }

        return auth()->user()?->can('admin.dispatch.view') ?? false;
    }

    public function getModuleKey(): string
    {
        return 'dispatch';
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getViewData(): array
    {
        $assignment = DispatchAssignment::query()
            ->with([
                'order.supportTickets.assignee',
                'order.workerLocationPings',
                'assignedUser.workerAvailability',
                'assignedUser.workerProfile',
                'assignedUser.locationPings',
            ])
            ->whereIn('status', ['assigned', 'accepted'])
            ->latest('assigned_at')
            ->first();
        $latestPing = WorkerLocationPing::query()->with(['user', 'order'])->latest('captured_at')->first();
        $operations = rescue(fn () => app(OperationsSettings::class), null, report: false);

        return [
            'assignment' => $assignment,
            'latestPing' => $latestPing,
            'latestSupportTicket' => $assignment?->order?->supportTickets->first(),
            'mapDefaults' => $this->getMapDefaults(),
            'metrics' => [
                'pings' => WorkerLocationPing::query()->count(),
                'active_assignments' => DispatchAssignment::query()->whereIn('status', ['assigned', 'accepted'])->count(),
                'workers_with_ping' => WorkerLocationPing::query()->distinct('user_id')->count('user_id'),
                'orders_with_ping' => WorkerLocationPing::query()->whereNotNull('order_id')->distinct('order_id')->count('order_id'),
                'stale_pings' => WorkerLocationPing::query()->where('captured_at', '<', now()->subMinutes(2))->count(),
                'zones' => OperationZone::query()->where('status', 'active')->count(),
                'customer_tracking' => (bool) ($operations?->customer_tracking_enabled ?? false),
            ],
            'gpsTrackingEnabled' => (bool) ($operations?->gps_tracking_enabled ?? true),
            'activeZones' => OperationZone::with(['creator', 'events'])->where('status', 'active')->latest()->limit(20)->get(),
            'activeOrders' => Order::with(['scenario', 'dispatchAssignments.assignedUser', 'supportTickets', 'workerLocationPings'])
                ->whereIn('status', ['submitted', 'accepted', 'in_progress'])->latest('updated_at')->limit(20)->get(),
            'fleetWorkers' => WorkerProfile::with(['user.workerAvailability', 'user.locationPings'])->where('status', 'approved')->get(),
        ];
    }

    public function getMapDefaults(): array
    {
        return rescue(fn () => [
            'lat' => app(MapSettings::class)->map_center_lat,
            'lng' => app(MapSettings::class)->map_center_lng,
            'zoom' => app(MapSettings::class)->map_default_zoom,
            'max_accuracy' => app(MapSettings::class)->max_gps_accuracy_meters,
            'default_layer' => app(MapSettings::class)->default_map_layer,
            'enabled_layers' => app(MapSettings::class)->enabled_map_layers,
            'refresh_seconds' => app(MapSettings::class)->map_refresh_seconds,
            'stale_seconds' => app(MapSettings::class)->stale_gps_seconds,
        ], [
            'lat' => 68.4385, 'lng' => 17.4272, 'zoom' => 10, 'max_accuracy' => 5000,
            'default_layer' => 'standard', 'enabled_layers' => ['standard', 'satellite', 'hybrid', 'terrain'],
            'refresh_seconds' => 12, 'stale_seconds' => 120,
        ], report: false);
    }

    public function setMapContext(float $lat, float $lng): void
    {
        abort_unless(auth()->user()?->can('admin.dispatch.view'), 403);
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || ($lat === 0.0 && $lng === 0.0)) abort(422, 'Invalid map coordinates.');
        $this->contextLat = $lat;
        $this->contextLng = $lng;
    }

    public function openContextEditor(string $editor, float $lat, float $lng, ?string $zoneType = null): void
    {
        abort_unless(auth()->user()?->can('admin.dispatch.view'), 403);
        $this->setMapContext($lat, $lng);
        abort_unless(in_array($editor, ['zone', 'dispatch', 'support'], true), 422, 'Unsupported map action.');
        if ($editor === 'zone') {
            abort_unless(in_array($zoneType, ['service_area', 'priority_area', 'no_go_area', 'support_incident'], true), 422, 'Unsupported zone type.');
            $this->zoneType = $zoneType;
        }
        $this->activeContextEditor = $editor;
    }

    public function closeContextEditor(): void
    {
        $this->activeContextEditor = null;
    }

    public function createZone(?string $type = null): void
    {
        abort_unless(auth()->user()?->can('admin.dispatch.view'), 403);
        $type ??= $this->zoneType;
        $this->zoneType = $type;
        $this->validate([
            'zoneName' => ['required', 'string', 'max:150'],
            'zoneType' => ['required', 'in:service_area,priority_area,no_go_area,temporary_busy_area,pickup_hotspot,support_incident'],
            'zoneRadius' => ['required', 'integer', 'min:25', 'max:50000'],
        ]);
        try {
            app(OperationZoneService::class)->createZone([
                'name' => $this->zoneName,
                'type' => $this->zoneType,
                'geometry_type' => $type === 'support_incident' ? 'point' : 'circle',
                'coordinates' => ['lat' => $this->contextLat, 'lng' => $this->contextLng],
                'radius_meters' => $type === 'support_incident' ? null : $this->zoneRadius,
                'color' => $this->zoneColor($type),
                'note' => $this->zoneNote ?: null,
            ], auth()->user());
            $this->reset(['zoneName', 'zoneNote', 'activeContextEditor']);
            Notification::make()->title('Operation zone created')->success()->send();
            $this->dispatch('liveops-action-completed', message: 'Operation zone created and map refreshed.');
        } catch (ValidationException $exception) {
            Notification::make()->title((string) collect($exception->errors())->flatten()->first())->warning()->send();
        }
    }

    public function deactivateZone(int $zoneId): void
    {
        $this->validate(['zoneDeactivateReason' => ['required', 'string', 'max:2000']]);
        app(OperationZoneService::class)->deactivateZone(OperationZone::findOrFail($zoneId), auth()->user(), $this->zoneDeactivateReason);
        $this->zoneDeactivateReason = '';
        Notification::make()->title('Operation zone deactivated')->success()->send();
    }

    public function addDispatchNoteAtLocation(): void
    {
        $assignment = $this->currentAssignment();
        if (! $assignment?->order) {
            Notification::make()->title('Select an active order first')->warning()->send();
            return;
        }
        $this->validate(['dispatchLocationNote' => ['required', 'string', 'max:2000']]);
        app(DispatchEngine::class)->recordDispatchEvent($assignment->order, 'dispatch.location_note', ['latitude' => $this->contextLat, 'longitude' => $this->contextLng], $this->dispatchLocationNote, $assignment);
        $this->dispatchLocationNote = '';
        $this->activeContextEditor = null;
        Notification::make()->title('Dispatch location note recorded')->success()->send();
        $this->dispatch('liveops-action-completed', message: 'Dispatch location note recorded.');
    }

    public function createSupportAtLocation(): void
    {
        $assignment = $this->currentAssignment();
        if (! $assignment?->order) {
            Notification::make()->title('Select an active order first')->warning()->send();
            return;
        }
        $this->validate([
            'supportSubject' => ['required', 'string', 'max:255'],
            'supportPriority' => ['required', 'in:low,normal,high,urgent'],
            'supportCategory' => ['required', 'in:order_issue,delivery_issue,worker_issue,payment_issue,document_issue,customer_question,system_issue,other'],
            'supportInternalNote' => ['nullable', 'string', 'max:5000'],
        ]);
        $service = app(SupportTicketService::class);
        $ticket = $service->createTicket([
            'subject' => $this->supportSubject,
            'category' => $this->supportCategory, 'priority' => $this->supportPriority, 'source' => 'admin', 'visibility' => 'internal',
            'order_id' => $assignment->order_id, 'dispatch_assignment_id' => $assignment->id,
            'worker_profile_id' => $assignment->assignedUser?->workerProfile?->id, 'customer_id' => $assignment->order->customer_id,
            'metadata' => ['location' => ['latitude' => $this->contextLat, 'longitude' => $this->contextLng]],
        ], auth()->user());
        if ($this->supportInternalNote !== '') {
            $service->addMessage($ticket, ['body' => $this->supportInternalNote, 'author_type' => 'admin', 'message_type' => 'internal_note', 'visibility' => 'internal'], auth()->user());
        }
        $this->reset(['supportSubject', 'supportInternalNote']);
        $this->supportPriority = 'normal';
        $this->supportCategory = 'delivery_issue';
        $this->activeContextEditor = null;
        Notification::make()->title('Location support ticket created')->success()->send();
        $this->dispatch('liveops-action-completed', message: "Support ticket {$ticket->ticket_number} created.");
    }

    private function currentAssignment(): ?DispatchAssignment
    {
        return DispatchAssignment::with(['order', 'assignedUser.workerProfile'])->whereIn('status', ['assigned', 'accepted'])->latest('assigned_at')->first();
    }

    private function zoneColor(string $type): string
    {
        return match ($type) {
            'no_go_area' => '#ef4444', 'priority_area' => '#f59e0b', 'service_area' => '#22d3ee',
            'temporary_busy_area' => '#a855f7', 'pickup_hotspot' => '#10b981', default => '#f97316',
        };
    }
}
