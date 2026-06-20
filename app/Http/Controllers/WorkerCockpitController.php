<?php

namespace App\Http\Controllers;

use App\Models\{Order, WorkerLocationPing};
use App\Services\Workers\{WorkerAvailabilityService, WorkerLocationService, WorkerOrderWorkflowService};
use App\Services\Orders\OrderCompletionService;
use App\Services\Finance\{WorkerSettlementService, WorkerPayoutProfileService};
use App\Settings\MapSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WorkerCockpitController extends Controller
{
    public function dashboard(Request $request)
    {
        $user         = $request->user()->load(['workerAvailability', 'workerProfile']);
        $availability = $user->workerAvailability;
        $isOnline     = in_array($availability?->status, ['online', 'available'], true);
        $orders       = $this->orders($request);
        $activeOrder  = $orders->first();

        $mapConfig = rescue(function () {
            $s = app(MapSettings::class);
            return [
                'provider'    => $s->map_provider,
                'center_lat'  => $s->map_center_lat,
                'center_lng'  => $s->map_center_lng,
                'default_zoom' => $s->map_default_zoom,
                'max_accuracy' => $s->max_gps_accuracy_meters,
                'stale_seconds' => $s->stale_gps_seconds,
                'ping_seconds'  => max(10, $s->map_refresh_seconds),
            ];
        }, [
            'provider'     => 'osm',
            'center_lat'   => 68.4385,
            'center_lng'   => 17.4272,
            'default_zoom' => 10,
            'max_accuracy' => 5000,
            'stale_seconds' => 120,
            'ping_seconds'  => 10,
        ], report: false);

        $earnings     = app(WorkerSettlementService::class)->getWorkerEarningsSummary($user);
        $payoutProfile = app(WorkerPayoutProfileService::class)->getReadiness($user);

        $lastPing = WorkerLocationPing::where('user_id', $user->id)
            ->latest()
            ->first();

        return view('worker.dashboard', compact(
            'user', 'availability', 'isOnline',
            'orders', 'activeOrder',
            'earnings', 'payoutProfile',
            'mapConfig', 'lastPing'
        ));
    }

    public function index(Request $request)
    {
        return view('worker.orders.index', ['orders' => $this->orders($request)]);
    }

    public function show(Request $request, Order $order, WorkerOrderWorkflowService $workflow, OrderCompletionService $completion)
    {
        $workflow->assertOwnership($request->user(), $order);
        $order->load(['scenario', 'events', 'dispatchEvents', 'priceQuotes', 'completionProofs.events', 'dispatchAssignments']);
        $proofEligibility = $completion->canSubmitProof($order, $request->user());

        return view('worker.orders.show', [
            'order'            => $order,
            'activeAssignment' => $order->activeDispatchAssignment(),
            'lastPing'         => WorkerLocationPing::where('user_id', $request->user()->id)->where('order_id', $order->id)->latest()->first(),
            'nextAction'       => $workflow->nextAction($order),
            'executionState'   => $workflow->executionState($request->user(), $order, $proofEligibility),
            'proofEligibility' => $proofEligibility,
        ]);
    }

    public function online(Request $request, WorkerAvailabilityService $service)
    {
        try {
            $service->setOnline($request->user(), 'Worker enabled presence from cockpit. GPS is separate.');
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => collect($e->errors())->flatten()->first()], 422);
            }
            return back()->withErrors($e->errors());
        }

        if ($request->expectsJson()) {
            return response()->json(['status' => 'online', 'message' => 'You are online. Location is not shared until you explicitly enable it.']);
        }

        return back()->with('status', 'You are online.');
    }

    public function offline(Request $request, WorkerAvailabilityService $service)
    {
        $service->setOffline($request->user(), 'Worker disabled presence from cockpit.');

        if ($request->expectsJson()) {
            return response()->json(['status' => 'offline', 'message' => 'You are offline.']);
        }

        return back()->with('status', 'You are offline.');
    }

    public function location(Request $request, WorkerLocationService $service)
    {
        $data = $request->validate([
            'order_id'       => 'nullable|integer|exists:orders,id',
            'latitude'       => 'required|numeric|between:-90,90',
            'longitude'      => 'required|numeric|between:-180,180',
            'accuracy_meters' => 'required|numeric|min:0',
            'heading'        => 'nullable|numeric',
            'speed_mps'      => 'nullable|numeric',
            'captured_at'    => 'nullable|date',
            'consent'        => 'accepted',
        ]);

        $order = isset($data['order_id']) ? Order::findOrFail($data['order_id']) : null;
        $ping  = $service->recordPing($request->user(), $data, $order);

        return response()->json([
            'recorded'        => true,
            'accuracy_meters' => $ping->accuracy_meters,
            'captured_at'     => $ping->captured_at?->toIso8601String(),
        ]);
    }

    public function accept(Request $r, Order $order, WorkerOrderWorkflowService $s)      { return $this->run($r, $order, $s, 'acceptAssignment'); }
    public function start(Request $r, Order $order, WorkerOrderWorkflowService $s)       { return $this->run($r, $order, $s, 'startOrder'); }
    public function arrivedPickup(Request $r, Order $order, WorkerOrderWorkflowService $s) { return $this->run($r, $order, $s, 'markArrivedPickup'); }
    public function pickedUp(Request $r, Order $order, WorkerOrderWorkflowService $s)    { return $this->run($r, $order, $s, 'markPickedUp'); }
    public function arrivedDropoff(Request $r, Order $order, WorkerOrderWorkflowService $s) { return $this->run($r, $order, $s, 'markArrivedDropoff'); }

    public function complete(Request $r, Order $order, WorkerOrderWorkflowService $s)
    {
        return back()->withErrors(['completion' => 'Submit a completion proof note first. Customer must confirm before lifecycle completion.']);
    }

    private function run(Request $request, Order $order, WorkerOrderWorkflowService $service, string $method)
    {
        try {
            $service->{$method}($request->user(), $order);
            return back()->with('status', 'Worker action recorded and audited.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    private function orders(Request $request)
    {
        return Order::whereHas('dispatchAssignments', function ($q) use ($request) {
            $q->where('assigned_user_id', $request->user()->id)
              ->whereIn('status', ['assigned', 'accepted']);
        })
        ->with(['scenario', 'priceQuotes', 'dispatchEvents', 'dispatchAssignments'])
        ->latest('submitted_at')
        ->get();
    }
}
