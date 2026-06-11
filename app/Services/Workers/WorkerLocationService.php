<?php

namespace App\Services\Workers;

use App\Models\{Order, User, WorkerLocationPing};
use Illuminate\Validation\ValidationException;

class WorkerLocationService
{
    public const MAX_ACCURACY_METERS = 5000;

    public function recordPing(User $user, array $payload, ?Order $order = null): WorkerLocationPing
    {
        if (! filter_var($payload['consent'] ?? false, FILTER_VALIDATE_BOOL)) {
            throw ValidationException::withMessages(['consent' => 'Location consent is required for every browser ping.']);
        }
        $latitude = (float) ($payload['latitude'] ?? 999);
        $longitude = (float) ($payload['longitude'] ?? 999);
        $accuracy = isset($payload['accuracy_meters']) ? (float) $payload['accuracy_meters'] : null;
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            throw ValidationException::withMessages(['location' => 'Coordinates are outside valid geographic bounds.']);
        }
        if ($latitude === 0.0 && $longitude === 0.0) {
            throw ValidationException::withMessages(['location' => 'Empty 0,0 coordinates are not accepted as a real location.']);
        }
        if ($accuracy === null || $accuracy <= 0 || $accuracy > self::MAX_ACCURACY_METERS) {
            throw ValidationException::withMessages(['accuracy_meters' => 'Location accuracy is too low. Enable precise location and try again.']);
        }
        if (! in_array($user->workerAvailability?->status, ['online', 'available'], true)) {
            throw ValidationException::withMessages(['presence' => 'Go online before sharing location.']);
        }
        $assignment = $order?->activeDispatchAssignment();
        if ($order && $assignment?->assigned_user_id !== $user->id) {
            throw ValidationException::withMessages(['order' => 'Location can only be linked to your assigned order.']);
        }

        $ping = WorkerLocationPing::create([
            'user_id' => $user->id, 'worker_profile_id' => $user->workerProfile?->id,
            'order_id' => $order?->id, 'dispatch_assignment_id' => $assignment?->id,
            'latitude' => $latitude, 'longitude' => $longitude, 'accuracy_meters' => $accuracy,
            'heading' => $payload['heading'] ?? null, 'speed_mps' => $payload['speed_mps'] ?? null,
            'source' => 'browser', 'captured_at' => $payload['captured_at'] ?? now(), 'metadata' => ['consent' => true],
        ]);
        $user->workerAvailability?->update(['last_seen_at' => now()]);
        return $ping;
    }
}
