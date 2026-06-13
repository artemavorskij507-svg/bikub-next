<?php

namespace App\Http\Controllers;

use App\Models\WorkerLocationPing;
use App\Models\OperationZone;
use App\Settings\MapSettings;
use Illuminate\Http\JsonResponse;

class AdminLiveOperationsMapDataController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $latestIds = WorkerLocationPing::query()
            ->selectRaw('MAX(id) AS id')
            ->groupBy('user_id', 'order_id')
            ->pluck('id');

        $staleSeconds = rescue(fn () => app(MapSettings::class)->stale_gps_seconds, 120, report: false);
        $workers = WorkerLocationPing::with(['user.workerAvailability', 'user.workerProfile', 'order', 'assignment'])
            ->whereIn('id', $latestIds)
            ->latest('captured_at')
            ->get()
            ->map(fn (WorkerLocationPing $ping) => [
                'id' => $ping->id,
                'worker' => ['id' => $ping->user_id, 'name' => $ping->user?->name, 'email' => $ping->user?->email],
                'order_number' => $ping->order?->order_number,
                'order_status' => $ping->order?->status?->value,
                'assignment_id' => $ping->dispatch_assignment_id,
                'presence_status' => $ping->user?->workerAvailability?->status ?? 'offline',
                'latitude' => (float) $ping->latitude,
                'longitude' => (float) $ping->longitude,
                'accuracy_meters' => $ping->accuracy_meters !== null ? (float) $ping->accuracy_meters : null,
                'captured_at' => $ping->captured_at?->toIso8601String(),
                'created_at' => $ping->created_at?->toIso8601String(),
                'stale' => ($ping->captured_at ?? $ping->created_at)?->lt(now()->subSeconds($staleSeconds)) ?? true,
                'entity_type' => 'worker',
            ]);

        $zones = OperationZone::with('creator')
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->get()
            ->map(fn (OperationZone $zone) => [
                'id' => $zone->id,
                'name' => $zone->name,
                'type' => $zone->type,
                'status' => $zone->status,
                'geometry_type' => $zone->geometry_type,
                'coordinates' => $zone->coordinates,
                'radius_meters' => $zone->radius_meters,
                'color' => $zone->color,
                'creator' => $zone->creator?->name,
                'starts_at' => $zone->starts_at?->toIso8601String(),
                'ends_at' => $zone->ends_at?->toIso8601String(),
            ]);

        return response()->json([
            'markers' => $workers,
            'zones' => $zones,
            'counts' => [
                'workers' => $workers->count(),
                'stale_gps' => $workers->where('stale', true)->count(),
                'orders' => 0,
                'customers' => 0,
                'support' => 0,
                'payment_issues' => 0,
                'zones' => $zones->count(),
            ],
            'count' => $workers->count(),
            'refreshed_at' => now()->toIso8601String(),
        ]);
    }
}
