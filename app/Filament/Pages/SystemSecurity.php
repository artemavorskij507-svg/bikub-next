<?php

namespace App\Filament\Pages;

class SystemSecurity extends AdminOsModulePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'System & Security';

    protected static string|\UnitEnum|null $navigationGroup = 'System';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'System & Security';

    protected string $view = 'filament.pages.system-security';

    public function getModuleKey(): string
    {
        return 'system';
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getRuntimeStatus(): array
    {
        return [
            $this->status('Application environment', (string) config('app.env'), 'Reported from config(app.env).', config('app.env') === 'production' ? 'review' : 'safe'),
            $this->status('Debug mode', config('app.debug') ? 'enabled' : 'disabled', 'Production must keep APP_DEBUG disabled.', config('app.debug') ? 'blocked' : 'ready'),
            $this->status('Database driver', (string) config('database.default'), 'PostgreSQL is expected for BiKuBe Next.', config('database.default') === 'pgsql' ? 'ready' : 'review'),
            $this->status('Cache driver', (string) config('cache.default'), 'Operational cache is configured through Laravel config.', 'ready'),
            $this->status('Queue driver', (string) config('queue.default'), 'Horizon/queue monitoring is available when workers run.', 'ready'),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getOperationalModules(): array
    {
        $pulseWorks = $this->routeAvailable('pulse') && $this->pulseTablesReady();
        $horizonState = $this->horizonWorkerState();
        $logViewerProtected = $this->logViewerUsesAuthMiddleware();

        return [
            $this->status('Health checks', $this->packageVersion('spatie/laravel-health'), $this->fileExists('config/health.php') ? 'Config exists; checks policy still needs production setup.' : 'Installed package config is not published.', $this->fileExists('config/health.php') ? 'configured' : 'review'),
            $this->status('Backups', $this->packageVersion('spatie/laravel-backup'), $this->fileExists('config/backup.php') ? 'Config exists; backup destination and schedule still need setup.' : 'Backup config is not published.', $this->fileExists('config/backup.php') ? 'configured' : 'review'),
            $this->status('Schedule monitor', $this->packageVersion('spatie/laravel-schedule-monitor'), $this->fileExists('config/schedule-monitor.php') ? 'Config and DB tables exist; tasks must be synced after real schedules are added.' : 'Schedule monitor config is not published.', $this->fileExists('config/schedule-monitor.php') ? 'configured' : 'review'),
            $this->status('Log Viewer', $this->routeAvailable('log-viewer.index') ? 'WORKS' : 'NEEDS SETUP', $this->routeAvailable('log-viewer.index') ? 'Route exists; external package UI; '.($logViewerProtected ? 'auth-protected by config.' : 'auth middleware missing.') : 'Route not registered.', $this->routeAvailable('log-viewer.index') && $logViewerProtected ? 'works' : 'setup', $this->routeAvailable('log-viewer.index') ? url('/log-viewer') : ''),
            $this->status('Web Push', config('webpush.vapid.public_key') ? 'CONFIGURED' : 'NEEDS SETUP', $this->packageVersion('laravel-notification-channels/webpush').' installed; config/subscription table exist; VAPID keys are not configured here.', config('webpush.vapid.public_key') ? 'configured' : 'setup'),
            $this->status('Horizon', $this->routeAvailable('horizon.index') ? 'ROUTE EXISTS' : 'NEEDS SETUP', 'Route exists; '.$horizonState.'. Start a Horizon worker before claiming queue processing is active.', $this->routeAvailable('horizon.index') ? 'review' : 'blocked', $this->routeAvailable('horizon.index') ? url('/horizon') : ''),
            $this->status('Pulse', $pulseWorks ? 'WORKS' : 'NEEDS SETUP', $pulseWorks ? 'Route exists and required Pulse tables exist; /pulse returned HTTP 200 in validation.' : 'Route exists but required Pulse tables/config are missing or not verified.', $pulseWorks ? 'works' : 'setup', $this->routeAvailable('pulse') ? url('/pulse') : ''),
            $this->status('Reverb', $this->packageVersion('laravel/reverb'), 'Realtime package is installed; production broadcasting config is still a separate setup task.', config('broadcasting.default') === 'reverb' ? 'configured' : 'setup'),
            $this->status('Environment indicator', $this->packageVersion('pxlrbt/filament-environment-indicator'), 'Package assets are present; panel plugin wiring can be refined in a later shell pass.', $this->packageInstalled('pxlrbt/filament-environment-indicator') ? 'installed' : 'blocked'),
            $this->status('Typed settings', $this->packageVersion('spatie/laravel-settings'), 'Platform, operations and map settings are persisted and exposed through permission-protected pages.', $this->tableExists('settings') ? 'configured' : 'blocked', route('filament.admin.pages.platform-settings')),
            $this->status('Media Library', $this->packageVersion('spatie/laravel-medialibrary'), 'Private worker-document uploads and optional CMS hero uploads are wired. Upload never auto-approves a document.', $this->tableExists('media') ? 'configured' : 'blocked', route('filament.admin.resources.worker-documents.index')),
            $this->status('Support Center', \App\Models\SupportTicket::count().' ticket(s)', 'Admin dashboard/resource, portals, protected attachments, sound polling and database notifications are configured. Internal notes remain Admin OS-only. Email, SMS, WebPush, Reverb and live chat are deferred.', $this->tableExists('notifications') && $this->routeAvailable('filament.admin.pages.support-center') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.support-center') ? route('filament.admin.pages.support-center') : ''),
            $this->status('Customer account ownership', \App\Models\Order::whereNotNull('customer_id')->count().' linked order(s)', 'Explicit nullable orders.customer_id controls account order and linked support visibility. Unsafe automatic ownership inference is blocked; payments and customer GPS tracking remain deferred.', $this->routeAvailable('account.orders.index') && \Illuminate\Support\Facades\Schema::hasColumn('orders', 'customer_id') ? 'configured' : 'blocked', $this->routeAvailable('account.orders.index') ? route('account.orders.index') : ''),
            $this->status('Finance and payments', $this->routeAvailable('filament.admin.pages.finance-control') ? 'WORKFLOW CONFIGURED' : 'NEEDS SETUP', 'Real pricing rules can create audited quotes and draft/issued invoices. Payment intent, capture, refund and receipts remain blocked because no external provider adapter is connected.', $this->routeAvailable('filament.admin.pages.finance-control') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.finance-control') ? route('filament.admin.pages.finance-control') : ''),
            $this->status('Billing/provider contract', $this->tableExists('billing_documents') && $this->tableExists('payment_records') ? 'CONFIGURED' : 'NEEDS SETUP', 'Billing documents, payment records, idempotency and webhook event contracts are local and audited. Disabled provider is active; real external provider and receipt delivery are deferred.', $this->tableExists('billing_documents') && $this->tableExists('payment_records') ? 'configured' : 'blocked'),
            $this->status('Worker settlement ledger', $this->tableExists('worker_settlement_entries') && $this->tableExists('worker_settlement_events') ? 'CONFIGURED' : 'NEEDS SETUP', 'Settlement readiness requires an accepted completion proof, issued invoice, captured payment, and explicit worker settlement rule. Paid payout remains disabled until a real payout workflow exists.', $this->tableExists('worker_settlement_entries') && $this->tableExists('worker_settlement_events') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.resources.worker-settlement-entries.index') ? route('filament.admin.resources.worker-settlement-entries.index') : ''),
            $this->status('Worker settlement rule governance', $this->tableExists('worker_settlement_rules') ? \App\Models\WorkerSettlementRule::where('status', 'active')->where('legal_review_status', 'approved')->where('tax_review_status', 'approved')->count().' approved active rule(s)' : 'NEEDS SETUP', 'Only active rules with explicit legal and tax approval can calculate worker amounts. Draft or pending review rules never imply production approval.', $this->tableExists('worker_settlement_rules') && \App\Models\WorkerSettlementRule::where('status', 'active')->where('legal_review_status', 'approved')->where('tax_review_status', 'approved')->exists() ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.resources.worker-settlement-rules.index') ? route('filament.admin.resources.worker-settlement-rules.index') : ''),
            $this->status('Settlement review and payout provider gate', $this->tableExists('worker_settlement_rule_reviews') ? 'FAIL-CLOSED' : 'NEEDS SETUP', 'Legal, tax and finance reviews require reviewer decisions and evidence summaries. Disabled payout provider is active; payout instruction and mark-paid actions remain blocked.', $this->tableExists('worker_settlement_rule_reviews') ? 'configured' : 'blocked'),
            $this->status('Reviewer separation and payout onboarding', $this->routeAvailable('filament.admin.pages.payout-provider-settings') ? 'CONFIGURED' : 'NEEDS SETUP', 'Legal, tax, finance and compliance review decisions use separate permissions. Creator override requires an audited permission. Payout onboarding remains outbound-disabled and bank-provider deferred.', $this->routeAvailable('filament.admin.pages.payout-provider-settings') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.payout-provider-settings') ? route('filament.admin.pages.payout-provider-settings') : ''),
            $this->status('Worker payout profile workflow', $this->tableExists('worker_payout_profiles') ? 'ENCRYPTED / REVIEW REQUIRED' : 'NEEDS SETUP', 'Workers can submit encrypted payout identifiers; Admin OS exposes masked presence only. Tax and identity review approval remains required before payout readiness.', $this->tableExists('worker_payout_profiles') ? 'configured' : 'blocked'),
            $this->status('Worker tax and identity payout review', $this->tableExists('worker_payout_reviews') ? 'TEXT ATTESTATION / REVIEW REQUIRED' : 'NEEDS SETUP', 'Identity and tax approvals require type-specific reviewer permissions and explicit evidence summaries. Private payout document upload remains deferred pending an approved media security policy.', $this->tableExists('worker_payout_reviews') ? 'configured' : 'blocked'),
            $this->status('Private worker payout evidence', $this->tableExists('worker_payout_review_evidence') ? 'PRIVATE PDF / PROTECTED DOWNLOAD' : 'NEEDS SETUP', 'Evidence uses private local storage, protected streaming, SHA-256 hashes and audit events. Images are disabled without EXIF stripping. Virus scanning is not configured; upload never auto-approves review.', $this->tableExists('worker_payout_review_evidence') ? 'configured' : 'blocked'),
            $this->status('Private evidence malware scan gate', app(\App\Services\Security\FileScannerManager::class)->status()['available'] ? 'SCANNER AVAILABLE' : 'SCANNER UNAVAILABLE / DOWNLOAD BLOCKED', 'New evidence is fail-closed. Reviewer download requires a clean scan or an audited owner/admin override. Quarantine and retention fields are configured; physical retention deletion remains deferred.', app(\App\Services\Security\FileScannerManager::class)->status()['available'] ? 'configured' : 'blocked'),
            $this->status('Scanner and retention operations', $this->routeAvailable('filament.admin.pages.security-file-scanner') ? 'DRY-RUN WORKFLOW CONFIGURED' : 'NEEDS SETUP', 'Scanner selection and real binary availability are persisted and reported fail-closed. Retention jobs are audited; physical deletion remains disabled and dry-run only.', $this->routeAvailable('filament.admin.pages.security-file-scanner') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.security-file-scanner') ? route('filament.admin.pages.security-file-scanner') : ''),
            $this->status('Retention approval and evidence rescan', class_exists(\App\Console\Commands\EvidenceRescanCommand::class) ? 'MANUAL / FAIL-CLOSED' : 'NEEDS SETUP', 'Retention requester, approver and executor permissions are separated. Evidence rescan is manual-only and cannot produce a clean verdict while the scanner is unavailable.', class_exists(\App\Console\Commands\EvidenceRescanCommand::class) ? 'configured' : 'blocked'),
            $this->status('Controlled security reviewer provisioning', \Spatie\Permission\Models\Role::where('name', 'security_reviewer')->exists() ? \App\Models\User::role('security_reviewer')->count().' local reviewer(s)' : 'ROLE MISSING', 'Local-only provisioning never displays credentials. Security reviewers may approve retention jobs but cannot execute deletion.', \Spatie\Permission\Models\Role::where('name', 'security_reviewer')->exists() ? 'configured' : 'blocked'),
            $this->status('Reviewer lifecycle and governance audit', $this->tableExists('security_reviewer_accesses') ? \App\Models\SecurityReviewerAccess::where('status', 'active')->count().' active delegated access(es)' : 'NEEDS SETUP', 'Delegated reviewer actions require active, non-expired lifecycle access. Revocation leaves the user intact and blocks sensitive approval.', $this->tableExists('security_reviewer_accesses') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.security-governance') ? route('filament.admin.pages.security-governance') : ''),
            $this->status('Reviewer expiry and access reviews', $this->tableExists('security_access_review_cycles') ? \App\Models\SecurityAccessReviewCycle::where('status', 'open')->count().' open review cycle(s)' : 'NEEDS SETUP', 'Laravel scheduler registers expiry and expiring-soon commands. Access review decisions are audited; server scheduler activation remains an operations requirement.', $this->tableExists('security_access_review_cycles') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.security-governance') ? route('filament.admin.pages.security-governance') : ''),
            $this->status('Governance notifications and audit exports', $this->tableExists('security_governance_notifications') ? \App\Models\SecurityGovernanceNotification::where('status', 'open')->where('severity', 'critical')->count().' open critical notification(s)' : 'NEEDS SETUP', 'Governance escalation is database/in-app only. Private JSON exports are SHA-256 stamped and exclude evidence paths, secrets, and decrypted payout data. External email/SMS is not configured.', $this->tableExists('security_governance_notifications') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.security-governance') ? route('filament.admin.pages.security-governance') : ''),
            $this->status('Protected audit exports and notification ownership', $this->routeAvailable('admin.security-audit-exports.download') ? 'PROTECTED / PRIVATE' : 'NEEDS SETUP', 'Audit export downloads require permission and hash verification. Archive and retention scheduling are non-destructive. Governance notifications support database-only ownership and escalation.', $this->routeAvailable('admin.security-audit-exports.download') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.security-governance') ? route('filament.admin.pages.security-governance') : ''),
            $this->status('Security governance operations', $this->tableExists('security_incidents') ? \App\Models\SecurityIncident::whereNotIn('status', ['resolved','cancelled'])->count().' open incident(s)' : 'NEEDS SETUP', 'Export retention jobs are dry-run and deletion-blocked by default. Notification SLA checks and security incidents are database-audited; external email/SMS remains unconfigured.', $this->tableExists('security_incidents') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.security-governance') ? route('filament.admin.pages.security-governance') : ''),
            $this->status('Incident response and deletion policy gate', $this->tableExists('security_incident_playbooks') ? \App\Models\SecurityIncidentPlaybook::where('is_active', true)->count().' active playbook(s)' : 'NEEDS SETUP', 'Incidents support masked evidence links, response tasks, timelines, and playbooks. Physical export deletion remains blocked by explicit governance readiness gates.', $this->tableExists('security_incident_playbooks') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.security-governance') ? route('filament.admin.pages.security-governance') : ''),
            $this->status('Incident closure governance', $this->tableExists('security_incident_postmortems') ? \App\Models\SecurityIncident::whereIn('severity', ['high','critical'])->whereNotIn('status', ['resolved','cancelled'])->count().' unresolved high/critical incident(s)' : 'NEEDS SETUP', 'Governed closure requires owners, completed response tasks, approved RCA when required, and no open critical corrective actions.', $this->tableExists('security_incident_postmortems') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.security-governance') ? route('filament.admin.pages.security-governance') : ''),
            $this->status('Incident continuous improvement', $this->tableExists('security_postmortem_review_cycles') ? \App\Models\SecurityIncidentCorrectiveAction::where('verification_required', true)->where('verification_status', '!=', 'verified')->count().' unverified action(s)' : 'NEEDS SETUP', 'Recurrence matching suggests patterns without auto-confirming. Corrective action completion and verification are separate, and postmortem review cycles are audited.', $this->tableExists('security_postmortem_review_cycles') ? 'configured' : 'blocked', $this->routeAvailable('filament.admin.pages.security-governance') ? route('filament.admin.pages.security-governance') : ''),
            $this->status('Private worker documents', $this->routeAvailable('admin.worker-documents.download') ? 'ACTIVE' : 'NEEDS SETUP', 'Private local storage and role-authorized audited download route. Retention dates are operator-managed.', $this->routeAvailable('admin.worker-documents.download') ? 'configured' : 'blocked', route('filament.admin.resources.worker-documents.index')),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getSecurityFoundation(): array
    {
        return [
            $this->status('Fortify auth', $this->packageVersion('laravel/fortify'), 'Login, 2FA endpoints and passkey routes are provided by Fortify/Laravel.', 'installed'),
            $this->status('Sanctum API auth', $this->packageVersion('laravel/sanctum'), 'Token/session auth foundation exists; API scopes are not designed yet.', 'installed'),
            $this->status('Filament Shield', $this->packageVersion('bezhansalleh/filament-shield'), 'Shield-compatible Spatie roles are active; Shield management UI is intentionally not published yet.', 'configured'),
            $this->status('Spatie Permission', $this->packageVersion('spatie/laravel-permission'), 'Admin panel, module pages and sensitive resources enforce real permissions.', 'works'),
            $this->status('Activitylog', $this->packageVersion('spatie/laravel-activitylog'), 'Audit table and model logging are active for orders, worker applications and pricing rules.', 'works', route('filament.admin.pages.audit-log')),
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function getSecurityChecklist(): array
    {
        return [
            $this->status('2FA policy', 'needs setup', 'Fortify is installed, but admin role enforcement and recovery policy still need configuration.', 'setup'),
            $this->status('RBAC', 'active', 'Owner and worker assignments are real; module boundaries use named permissions.', 'ready'),
            $this->status('Audit logging', 'active', 'Audit visibility is available in the Admin Audit Log; additional models can be enrolled incrementally.', 'ready'),
            $this->status('Backups', 'needs setup', 'Backup package/config exists; destination, schedule and restore test are not configured.', 'setup'),
            $this->status('Webhook signatures', 'not implemented', 'Payment/provider webhooks require signed adapters in domain work.', 'blocked'),
            $this->status('Secrets', 'not inspected', 'No secrets are printed or changed here; production secret policy remains required.', 'review'),
            $this->status('HTTPS', 'not configured here', 'TLS/nginx/systemd are outside this approved scope.', 'blocked'),
            $this->status('Production debug off', config('app.debug') ? 'blocked' : 'ready', 'Uses config(app.debug); production must remain false.', config('app.debug') ? 'blocked' : 'ready'),
        ];
    }
}
