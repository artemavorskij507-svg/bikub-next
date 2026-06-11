<?php

namespace App\Http\Controllers;

use App\Models\WorkerLocationPing;
use Illuminate\Http\JsonResponse;

class AdminLiveOperationsMapDataController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $latestIds = WorkerLocationPing::query()
            ->selectRaw('MAX(id) AS id')
            ->groupBy('user_id', 'order_id')
            ->pluck('id');

        $markers = WorkerLocationPing::with(['user.workerAvailability', 'order', 'assignment'])
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
            ]);

        return response()->json(['markers' => $markers, 'count' => $markers->count()]);
    }
}
