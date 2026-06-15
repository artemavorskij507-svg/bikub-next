<?php

namespace App\Http\Controllers;

use App\Models\{Order, WorkerLocationPing};
use App\Services\Workers\{WorkerAvailabilityService, WorkerLocationService, WorkerOrderWorkflowService};
use App\Services\Finance\WorkerSettlementService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WorkerCockpitController extends Controller
{
    public function dashboard(Request $request) { return view('worker.dashboard', ['orders' => $this->orders($request)->take(3), 'availability' => $request->user()->workerAvailability, 'earnings' => app(WorkerSettlementService::class)->getWorkerEarningsSummary($request->user()), 'payoutProfile' => app(\App\Services\Finance\WorkerPayoutProfileService::class)->getReadiness($request->user())]); }
    public function index(Request $request) { return view('worker.orders.index', ['orders' => $this->orders($request)]); }
    public function show(Request $request, Order $order, WorkerOrderWorkflowService $workflow)
    {
        $workflow->assertOwnership($request->user(), $order);
        return view('worker.orders.show', ['order' => $order->load(['scenario', 'events', 'dispatchEvents', 'priceQuotes', 'completionProofs.events']), 'lastPing' => WorkerLocationPing::where('user_id', $request->user()->id)->where('order_id', $order->id)->latest()->first(), 'nextAction' => $workflow->nextAction($order)]);
    }
    public function online(Request $request, WorkerAvailabilityService $service) { $service->setOnline($request->user(), 'Worker enabled presence from cockpit. GPS is separate.'); return back()->with('status', 'You are online. Location is not shared until you explicitly enable it.'); }
    public function offline(Request $request, WorkerAvailabilityService $service) { $service->setOffline($request->user(), 'Worker disabled presence from cockpit.'); return back()->with('status', 'You are offline.'); }
    public function location(Request $request, WorkerLocationService $service)
    {
        $data = $request->validate(['order_id' => 'nullable|integer|exists:orders,id', 'latitude' => 'required|numeric', 'longitude' => 'required|numeric', 'accuracy_meters' => 'required|numeric', 'heading' => 'nullable|numeric', 'speed_mps' => 'nullable|numeric', 'captured_at' => 'nullable|date', 'consent' => 'accepted']);
        $ping = $service->recordPing($request->user(), $data, isset($data['order_id']) ? Order::findOrFail($data['order_id']) : null);
        return response()->json(['message' => 'Real browser location recorded.', 'accuracy_meters' => $ping->accuracy_meters, 'captured_at' => $ping->captured_at?->toIso8601String()]);
    }
    public function action(Request $request, Order $order, string $action, WorkerOrderWorkflowService $service)
    {
        $method = match ($action) { 'accept' => 'acceptAssignment', 'start' => 'startOrder', 'arrived-pickup' => 'markArrivedPickup', 'picked-up' => 'markPickedUp', 'arrived-dropoff' => 'markArrivedDropoff', 'complete' => 'completeOrder', default => null };
        abort_unless($method, 404);
        try { $service->{$method}($request->user(), $order); return back()->with('status', 'Worker action recorded.'); }
        catch (ValidationException $e) { return back()->withErrors($e->errors()); }
    }
    public function accept(Request $r, Order $order, WorkerOrderWorkflowService $s) { return $this->run($r, $order, $s, 'acceptAssignment'); }
    public function start(Request $r, Order $order, WorkerOrderWorkflowService $s) { return $this->run($r, $order, $s, 'startOrder'); }
    public function arrivedPickup(Request $r, Order $order, WorkerOrderWorkflowService $s) { return $this->run($r, $order, $s, 'markArrivedPickup'); }
    public function pickedUp(Request $r, Order $order, WorkerOrderWorkflowService $s) { return $this->run($r, $order, $s, 'markPickedUp'); }
    public function arrivedDropoff(Request $r, Order $order, WorkerOrderWorkflowService $s) { return $this->run($r, $order, $s, 'markArrivedDropoff'); }
    public function complete(Request $r, Order $order, WorkerOrderWorkflowService $s) { return back()->withErrors(['completion' => 'Submit completion proof and wait for customer confirmation before lifecycle completion.']); }
    private function run(Request $request, Order $order, WorkerOrderWorkflowService $service, string $method)
    {
        try { $service->{$method}($request->user(), $order); return back()->with('status', 'Worker action recorded.'); }
        catch (ValidationException $e) { return back()->withErrors($e->errors()); }
    }
    private function orders(Request $request)
    {
        return Order::whereHas('dispatchAssignments', fn ($q) => $q->where('assigned_user_id', $request->user()->id)->whereIn('status', ['assigned', 'accepted']))
            ->with(['scenario', 'priceQuotes', 'dispatchEvents', 'dispatchAssignments'])->latest('submitted_at')->get();
    }
}
