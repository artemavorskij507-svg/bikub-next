<?php

namespace App\Services\Operations;

use App\Models\OperationZone;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OperationZoneService
{
    private const TYPES = ['service_area', 'priority_area', 'no_go_area', 'temporary_busy_area', 'pickup_hotspot', 'support_incident'];
    private const GEOMETRIES = ['point', 'circle', 'polygon'];

    public function createZone(array $data, User $actor): OperationZone
    {
        $this->validate($data);

        return DB::transaction(function () use ($data, $actor) {
            $zone = OperationZone::create([...$data, 'status' => 'active', 'created_by_id' => $actor->id, 'updated_by_id' => $actor->id]);
            $this->event($zone, $actor, 'created', $data['note'] ?? 'Operation zone created.');
            activity()->performedOn($zone)->causedBy($actor)->withProperties(['type' => $zone->type, 'geometry_type' => $zone->geometry_type])->log('operation_zone.created');
            return $zone;
        });
    }

    public function updateZone(OperationZone $zone, array $data, User $actor): OperationZone
    {
        $this->validate([...$zone->toArray(), ...$data]);
        $zone->update([...$data, 'updated_by_id' => $actor->id]);
        $this->event($zone, $actor, 'updated', $data['note'] ?? 'Operation zone updated.');
        return $zone->refresh();
    }

    public function deactivateZone(OperationZone $zone, User $actor, string $reason): OperationZone
    {
        if (blank($reason)) throw ValidationException::withMessages(['reason' => 'Deactivation reason is required.']);
        $zone->update(['status' => 'inactive', 'updated_by_id' => $actor->id]);
        $this->event($zone, $actor, 'deactivated', $reason);
        activity()->performedOn($zone)->causedBy($actor)->withProperties(['reason' => $reason])->log('operation_zone.deactivated');
        return $zone->refresh();
    }

    public function addZoneNote(OperationZone $zone, User $actor, string $note): OperationZone
    {
        if (blank($note)) throw ValidationException::withMessages(['note' => 'Zone note is required.']);
        $this->event($zone, $actor, 'note_added', $note);
        return $zone->refresh();
    }

    private function validate(array $data): void
    {
        if (! in_array($data['type'] ?? null, self::TYPES, true)) throw ValidationException::withMessages(['type' => 'Unsupported operation zone type.']);
        if (! in_array($data['geometry_type'] ?? null, self::GEOMETRIES, true)) throw ValidationException::withMessages(['geometry_type' => 'Unsupported zone geometry.']);
        $coordinates = $data['coordinates'] ?? [];
        if (($data['geometry_type'] ?? null) !== 'polygon') {
            $lat = $coordinates['lat'] ?? null;
            $lng = $coordinates['lng'] ?? null;
            if (! is_numeric($lat) || ! is_numeric($lng) || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180 || ((float)$lat === 0.0 && (float)$lng === 0.0)) {
                throw ValidationException::withMessages(['coordinates' => 'Valid non-zero latitude and longitude are required.']);
            }
        }
        if (($data['geometry_type'] ?? null) === 'circle' && (! is_numeric($data['radius_meters'] ?? null) || $data['radius_meters'] < 25 || $data['radius_meters'] > 50000)) {
            throw ValidationException::withMessages(['radius_meters' => 'Circle radius must be between 25 and 50,000 meters.']);
        }
    }

    private function event(OperationZone $zone, User $actor, string $type, ?string $description): void
    {
        $zone->events()->create(['actor_id' => $actor->id, 'event_type' => $type, 'description' => $description, 'created_at' => now()]);
    }
}
