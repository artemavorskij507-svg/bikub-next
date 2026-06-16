<?php

namespace App\Filament\Pages;

use App\Models\BillingDocument;
use App\Models\Order;
use App\Models\PaymentRecord;
use App\Models\ServiceScenario;
use App\Models\SupportTicket;
use App\Models\WorkerAvailability;
use App\Models\WorkerPayoutProfile;
use App\Models\WorkerProfile;
use Illuminate\Support\Facades\Route;
use Throwable;

class SystemSecurity extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationLabel = 'System Readiness';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'System Readiness';

    protected string $view = 'filament.pages.system-security';

    public static function getNavigationLabel(): string
    {
        return 'System Readiness';
    }

    public function getTitle(): string
    {
        return 'System Readiness';
    }

    public function getModuleKey(): string
    {
        return 'system';
    }

    public function getBusinessReadinessCards(): array
    {
        return [
            $this->businessCard('Orders ready', Order::count(), 'Order intake and admin order views are available.', 'ready', 'filament.admin.pages.orders-hub'),
            $this->businessCard('Dispatch ready', $this->countOrdersWaitingDispatch(), 'Dispatch Center can work with real submitted orders.', 'ready', 'filament.admin.pages.dispatch-center'),
            $this->businessCard('Worker cockpit ready', WorkerProfile::where('status', 'approved')->count(), 'Approved workers can use worker cockpit routes.', 'ready', 'filament.admin.pages.people-workforce'),
            $this->businessCard('Customer account ready', Order::whereNotNull('customer_id')->count(), 'Linked customer orders are visible through account routes.', 'ready', 'account.dashboard'),
            $this->businessCard('Finance billing ready', BillingDocument::count(), 'Invoices and billing documents are local and audited.', 'review', 'filament.admin.pages.finance-control'),
            $this->businessCard('Payment provider blocked', PaymentRecord::whereIn('status', ['captured', 'paid'])->count(), 'No external payment adapter is connected; do not claim capture readiness.', 'blocked', 'filament.admin.pages.payment-provider-settings'),
            $this->businessCard('Payout provider blocked', WorkerPayoutProfile::where('status', 'approved')->count(), 'Payout provider remains disabled and outbound payout is blocked.', 'blocked', 'filament.admin.pages.payout-provider-settings'),
            $this->businessCard('GPS/live tracking blocked', WorkerAvailability::whereIn('status', ['online', 'available'])->count(), 'Live map must use real worker GPS only; HTTPS/mobile UAT is still required.', 'blocked', 'filament.admin.pages.live-operations-map'),
            $this->businessCard('Support ready', SupportTicket::whereNotIn('status', ['resolved', 'closed'])->count(), 'Support tickets and admin support center are available.', 'ready', 'filament.admin.pages.support-center'),
        ];
    }

    public function getLaunchBlockers(): array
    {
        return [
            'Payment provider not connected.',
            'Payout provider disabled.',
            'Live GPS requires HTTPS and real mobile permission testing.',
            'External email/SMS not configured.',
            'Backup destination and restore schedule not configured.',
            'Production HTTPS is outside this local application scope.',
        ];
    }

    public function getModuleReadinessRows(): array
    {
        return [
            $this->moduleRow('Orders', 'Ready', 'Orders exist and can be reviewed by operations.', 'Open Orders Hub', 'filament.admin.pages.orders-hub'),
            $this->moduleRow('Dispatch', 'Ready', 'Dispatch Center exposes the real unassigned queue.', 'Open Dispatch Center', 'filament.admin.pages.dispatch-center'),
            $this->moduleRow('Worker cockpit', 'Ready with GPS blocker', 'Worker cockpit exists; real mobile GPS UAT is still required.', 'Open Worker Cockpit', 'worker.dashboard'),
            $this->moduleRow('Customer account', 'Ready', 'Account, orders, billing and support routes exist.', 'Open Customer Account', 'account.dashboard'),
            $this->moduleRow('Finance', 'Provider blocked', 'Billing is local; real payment provider is deferred.', 'Open Finance Control', 'filament.admin.pages.finance-control'),
            $this->moduleRow('Support', 'Ready', 'Support center and ticket resources are available.', 'Open Support Center', 'filament.admin.pages.support-center'),
            $this->moduleRow('Public delivery', ServiceScenario::active()->exists() ? 'Configured' : 'Needs service content', 'Public request flow uses active service scenarios only.', 'Open Service Catalog', 'filament.admin.pages.services-catalog'),
            $this->moduleRow('Translation', 'Ready', 'Admin UI translation catalog covers four languages.', 'Open Translation Manager', 'filament.admin.pages.translation-manager'),
            $this->moduleRow('Security technical readiness', 'Secondary technical area', 'Scanner, retention, reviewer and incident modules remain available but demoted.', 'Open Security Governance', 'filament.admin.pages.security-governance'),
        ];
    }

    public function getTechnicalReadiness(): array
    {
        return [
            $this->technical('Scanner & private evidence', $this->routeAvailable('filament.admin.pages.security-file-scanner') ? 'Configured / scanner may be unavailable' : 'Needs setup', 'Evidence download remains fail-closed without clean scan.', 'filament.admin.pages.security-file-scanner'),
            $this->technical('Reviewer access', $this->tableExists('security_reviewer_accesses') ? 'Lifecycle configured' : 'Needs setup', 'Delegated technical approval requires active reviewer access.', 'filament.admin.pages.security-governance'),
            $this->technical('Audit export', $this->tableExists('security_audit_exports') ? 'Private export workflow configured' : 'Needs setup', 'Exports are protected and hash-stamped; no public URLs.', 'filament.admin.pages.security-governance'),
            $this->technical('Retention jobs', $this->tableExists('evidence_retention_jobs') ? 'Dry-run only' : 'Needs setup', 'Physical deletion remains disabled by default.', 'filament.admin.pages.security-governance'),
            $this->technical('Incident response', $this->tableExists('security_incidents') ? 'Workflow configured' : 'Needs setup', 'Incidents, playbooks and RCA exist as technical governance tools.', 'filament.admin.pages.security-governance'),
        ];
    }

    private function countOrdersWaitingDispatch(): int
    {
        try {
            return Order::whereIn('status', ['submitted', 'accepted'])->count();
        } catch (Throwable) {
            return 0;
        }
    }

    private function businessCard(string $label, int $count, string $detail, string $tone, string $route): array
    {
        return [
            'label' => $label,
            'count' => $count,
            'detail' => $detail,
            'tone' => $tone,
            'url' => Route::has($route) ? route($route, absolute: false) : '',
        ];
    }

    private function moduleRow(string $area, string $status, string $meaning, string $action, string $route): array
    {
        return [
            'area' => $area,
            'status' => $status,
            'meaning' => $meaning,
            'action' => $action,
            'url' => Route::has($route) ? route($route, absolute: false) : '',
        ];
    }

    private function technical(string $label, string $status, string $detail, string $route): array
    {
        return [
            'label' => $label,
            'status' => $status,
            'detail' => $detail,
            'url' => Route::has($route) ? route($route, absolute: false) : '',
        ];
    }
}
