<?php

namespace App\Services\Workers;

use App\Models\User;
use App\Models\WorkerAvailability;
use App\Models\WorkerStatusEvent;
use Illuminate\Validation\ValidationException;

class WorkerAvailabilityService
{
    public function setOnline(User $user, ?string $note = null): WorkerAvailability
    {
        if ($user->workerProfile?->status !== 'approved') throw ValidationException::withMessages(['worker' => 'Only approved workers can go online.']);
        return $this->setStatus($user, 'online', $note);
    }
    public function setOffline(User $user, ?string $note = null): WorkerAvailability { return $this->setStatus($user, 'offline', $note); }
    private function setStatus(User $user, string $status, ?string $note): WorkerAvailability
    {
        $profile = $user->workerProfile;
        if (! $profile) throw ValidationException::withMessages(['worker' => 'Worker profile is required.']);
        $availability = WorkerAvailability::firstOrNew(['user_id' => $user->id]);
        $from = $availability->exists ? $availability->status : null;
        $availability->fill(['worker_profile_id'=>$profile->id,'status'=>$status,'source'=>auth()->id()===$user->id?'worker':'admin','last_seen_at'=>now(),'notes'=>$note])->save();
        $this->recordStatusEvent($user, 'worker.availability.changed', $from, $status, $note);
        return $availability;
    }
    public function recordStatusEvent(User $user, string $eventType, ?string $from, ?string $to, ?string $note = null): WorkerStatusEvent
    {
        return WorkerStatusEvent::create(['user_id'=>$user->id,'worker_profile_id'=>$user->workerProfile?->id,'actor_type'=>auth()->check()?get_class(auth()->user()):null,'actor_id'=>auth()->id(),'event_type'=>$eventType,'from_status'=>$from,'to_status'=>$to,'note'=>$note,'created_at'=>now()]);
    }
}
