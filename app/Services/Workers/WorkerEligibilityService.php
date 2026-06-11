<?php

namespace App\Services\Workers;

use App\Models\Order;
use App\Models\ServiceScenario;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class WorkerEligibilityService
{
    public function eligibleForScenario(ServiceScenario $scenario): Collection
    {
        return User::with(['workerProfile', 'workerAvailability'])->get()
            ->filter(fn (User $user) => $this->userIsEligibleForScenario($user, $scenario))->values();
    }

    public function eligibleForOrder(Order $order): Collection
    {
        return $order->scenario ? $this->eligibleForScenario($order->scenario) : new Collection();
    }

    public function userIsEligible(User $user, Order $order): bool
    {
        return $order->scenario && $this->userIsEligibleForScenario($user, $order->scenario);
    }

    private function userIsEligibleForScenario(User $user, ServiceScenario $scenario): bool
    {
        if (! $user->isEligibleWorker()) return false;
        $key = $scenario->scenario_key;
        $capability = match (true) {
            str_starts_with($key, 'delivery.'), $key === 'classifieds.delivery' => 'can_deliver',
            str_starts_with($key, 'moving.') => 'can_move',
            str_starts_with($key, 'eco.') => 'can_handle_eco',
            str_starts_with($key, 'handyman.') => 'can_do_handyman',
            str_starts_with($key, 'tow.'), str_starts_with($key, 'roadside.') => 'can_tow',
            str_starts_with($key, 'personal-task.') => 'can_run_errands',
            default => null,
        };
        return $capability && (bool) $user->workerProfile->{$capability};
    }
}
