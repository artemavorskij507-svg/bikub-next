<?php

namespace App\Filament\Pages;

use App\Models\BillingDocument;
use App\Models\Order;
use App\Models\PaymentRecord;
use App\Models\ServiceScenario;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WorkerAvailability;
use App\Models\WorkerDocument;
use App\Models\WorkerProfile;
use App\Services\Dispatch\DispatchEngine;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Route;
use Throwable;

class Dashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.dashboard';

    protected static ?string $title = 'BiKuBe Operations Command Center';

    public static function canAccess(): bool
    {
        if (config('database.default') === 'sqlite' && ! extension_loaded('pdo_sqlite')) {
            return auth()->check();
        }

        return auth()->user()?->can('admin.dashboard.view') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function getBusinessSnapshot(): array
    {
        try {
            $unassigned = app(DispatchEngine::class)->listUnassignedOrders()->count();
            $eligibleWorkers = app(DispatchEngine::class)->eligibleWorkers()->count();
        } catch (Throwable) {
            $unassigned = 0;
            $eligibleWorkers = 0;
        }

        try {
            return [
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'orders_waiting_dispatch' => Order::whereIn('status', ['submitted', 'accepted'])->count(),
                'unassigned_orders' => $unassigned,
                'assigned_jobs' => Order::whereHas('dispatchAssignments', fn ($query) => $query->whereIn('status', ['assigned', 'accepted']))->count(),
                'active_workers' => WorkerAvailability::whereIn('status', ['online', 'available'])->count(),
                'eligible_workers' => $eligibleWorkers,
                'approved_workers' => WorkerProfile::where('status', 'approved')->count(),
                'open_support_tickets' => SupportTicket::whereNotIn('status', ['resolved', 'closed'])->count(),
                'unpaid_invoices' => BillingDocument::whereNull('paid_at')->whereIn('status', ['draft', 'issued', 'sent', 'overdue'])->count(),
                'blocked_payments' => PaymentRecord::whereIn('status', ['failed', 'blocked', 'requires_action'])->count(),
                'active_services' => ServiceScenario::active()->count(),
                'customers' => User::whereHas('customerOrders')->count(),
                'worker_documents_pending' => WorkerDocument::whereIn('status', ['pending', 'submitted'])->count(),
            ];
        } catch (Throwable) {
            return [
                'orders_today' => 0,
                'orders_waiting_dispatch' => 0,
                'unassigned_orders' => 0,
                'assigned_jobs' => 0,
                'active_workers' => 0,
                'eligible_workers' => 0,
                'approved_workers' => 0,
                'open_support_tickets' => 0,
                'unpaid_invoices' => 0,
                'blocked_payments' => 0,
                'active_services' => 0,
                'customers' => 0,
                'worker_documents_pending' => 0,
            ];
        }
    }

    /**
     * @return array<string, int>
     */
    public function getOrderPipeline(): array
    {
        try {
            return [
                'created' => Order::count(),
                'waiting_dispatch' => Order::whereIn('status', ['submitted', 'accepted'])->count(),
                'assigned' => Order::whereHas('dispatchAssignments', fn ($query) => $query->whereIn('status', ['assigned', 'accepted']))->count(),
                'in_progress' => Order::where('status', 'in_progress')->count(),
                'completed' => Order::whereNotNull('completed_at')->count(),
                'blocked' => Order::whereHas('supportTickets', fn ($query) => $query->whereNotIn('status', ['resolved', 'closed']))
                    ->orWhereIn('payment_status', ['failed', 'blocked', 'requires_action'])
                    ->count(),
            ];
        } catch (Throwable) {
            return array_fill_keys(['created', 'waiting_dispatch', 'assigned', 'in_progress', 'completed', 'blocked'], 0);
        }
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getDeliveryCorridor(): array
    {
        $deliveryScenario = rescue(fn () => ServiceScenario::query()
            ->where('slug', 'delivery')
            ->orWhere('scenario_key', 'delivery')
            ->first(), null, report: false);
        $latestOrder = rescue(fn () => Order::query()->latest('updated_at')->first(), null, report: false);

        return [
            $this->corridorStep('Customer request', $deliveryScenario ? 'ready' : 'blocked', $deliveryScenario ? 'Public request route is available for the delivery scenario.' : 'No active delivery scenario/slug is configured.', 'Open checkout', 'public.orders.request', ['serviceSlug' => $deliveryScenario?->slug ?? 'delivery']),
            $this->corridorStep('Quote / invoice', $latestOrder?->latestPriceQuote() ? 'review' : 'setup', $latestOrder ? 'Finance can calculate a real quote; payment provider remains disabled.' : 'Create a real order before quote/invoice work.', 'Open Finance', 'filament.admin.pages.finance-control'),
            $this->corridorStep('Dispatch', $latestOrder ? 'ready' : 'setup', $latestOrder ? 'Dispatch Center can review assignment readiness for the latest order.' : 'No real order exists for dispatch review.', 'Open Dispatch', 'filament.admin.pages.dispatch-center'),
            $this->corridorStep('Worker accepts', Route::has('worker.dashboard') ? 'review' : 'blocked', 'Worker cockpit exists; active delivery acceptance must come from the worker flow.', 'Open Worker Cockpit', 'worker.dashboard'),
            $this->corridorStep('Tracking', 'blocked', 'Live map accepts real GPS only. Mobile HTTPS/GPS proof is still required.', 'Open Live Map', 'filament.admin.pages.live-operations-map'),
            $this->corridorStep('Completion proof', $latestOrder ? 'review' : 'setup', 'Completion proof remains workflow-controlled; no fake delivered state is shown.', 'Open Orders Hub', 'filament.admin.pages.orders-hub'),
            $this->corridorStep('Finance / settlement', 'blocked', 'Payment capture and payout provider are still blocked, so no paid state is claimed.', 'Open Finance', 'filament.admin.pages.finance-control'),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getBusinessCorridorActions(): array
    {
        return array_values(array_filter([
            $this->action('Open public delivery request', 'Start a real customer intake flow from the public service route.', 'public.orders.request', ['serviceSlug' => 'delivery']),
            $this->action('Open Orders Hub', 'Review order lifecycle, blockers and settlement state.', 'filament.admin.pages.orders-hub'),
            $this->action('Open Dispatch Center', 'Assign real submitted orders to eligible workers.', 'filament.admin.pages.dispatch-center'),
            $this->action('Open Live Map', 'See real worker GPS only; no fake markers.', 'filament.admin.pages.live-operations-map'),
            $this->action('Open Worker Cockpit', 'Use the real worker dashboard route.', 'worker.dashboard'),
            $this->action('Open Customer Account', 'Use the authenticated customer account route.', 'account.dashboard'),
            $this->action('Open Finance Control', 'Review invoices, payment blockers and settlement readiness.', 'filament.admin.pages.finance-control'),
            $this->action('Open Support Center', 'Handle customer and worker support tickets.', 'filament.admin.pages.support-center'),
        ]));
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getLaunchReadiness(): array
    {
        return [
            $this->readiness('Orders', Route::has('filament.admin.pages.orders-hub') ? 'Ready' : 'Blocked', 'Real order models and admin order routes exist.', 'Open Orders Hub', 'filament.admin.pages.orders-hub'),
            $this->readiness('Dispatch', Route::has('filament.admin.pages.dispatch-center') ? 'Ready' : 'Blocked', 'Dispatch queue and assignment audit are visible.', 'Open Dispatch Center', 'filament.admin.pages.dispatch-center'),
            $this->readiness('Workers', Route::has('worker.dashboard') ? 'Ready' : 'Blocked', 'Worker cockpit and online/presence routes exist.', 'Open Worker Cockpit', 'worker.dashboard'),
            $this->readiness('Finance', Route::has('filament.admin.pages.finance-control') ? 'Blocked by provider' : 'Blocked', 'Billing is local; external payment provider is not connected.', 'Open Finance Control', 'filament.admin.pages.finance-control'),
            $this->readiness('Support', Route::has('filament.admin.pages.support-center') ? 'Ready' : 'Blocked', 'Support center and ticket resources exist.', 'Open Support Center', 'filament.admin.pages.support-center'),
            $this->readiness('System readiness', Route::has('filament.admin.pages.system-security') ? 'Needs production setup' : 'Blocked', 'Payment, payout, GPS, email/SMS and backups still need production configuration.', 'Open System Readiness', 'filament.admin.pages.system-security'),
        ];
    }

    private function action(string $label, string $description, string $route, array $parameters = []): ?array
    {
        if (! Route::has($route)) {
            return null;
        }

        try {
            return ['label' => $label, 'description' => $description, 'url' => route($route, $parameters, absolute: false)];
        } catch (Throwable) {
            return null;
        }
    }

    private function readiness(string $area, string $status, string $meaning, string $action, string $route): array
    {
        return [
            'area' => $area,
            'status' => $status,
            'meaning' => $meaning,
            'action' => $action,
            'url' => Route::has($route) ? route($route, absolute: false) : '',
        ];
    }

    private function corridorStep(string $step, string $tone, string $blocker, string $action, string $route, array $parameters = []): array
    {
        $url = '';

        if (Route::has($route)) {
            try {
                $url = route($route, $parameters, absolute: false);
            } catch (Throwable) {
                $url = '';
            }
        }

        return compact('step', 'tone', 'blocker', 'action', 'url');
    }
}
