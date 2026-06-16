# BiKuBe OS — Project Context for Claude

Generated: 2026-06-16. Based on live file inspection of both projects.

---

## Business Overview

BiKuBe is a **Narvik-first local services platform** (Norway). It connects customers
who need on-demand services (delivery, moving, eco-disposal, handyman, roadside) with
vetted local workers (couriers, executors, handymen). The core operational flow is:

1. Customer submits a service request via the public website (scenario-driven intake form)
2. Admin reviews and marks the order dispatch-ready
3. Dispatch Center assigns an eligible worker
4. Worker accepts, progresses through lifecycle steps, and submits completion proof
5. Customer confirms (or disputes) proof
6. Finance: quote → invoice → settlement → payout to worker (partially implemented)

**Key business constraints:**
- Payment provider (Vipps MobilePay) is NOT yet connected — billing is local/draft only
- GPS live tracking requires HTTPS + mobile UAT — not yet done in production
- No fake data is shown anywhere — all blockers are visible to operators
- Launch target: Narvik first, then expand
- Four languages: nb (Norsk Bokmål), en (English), uk (Ukrainian), ru (Russian)

---

## Project Structure

| Project | Path | Role |
|---|---|---|
| **bikube-next** | `/home/keks/bikube-next` | Active production-track rewrite. Laravel 13, Filament v5, Livewire v4, PostgreSQL. Do all new work here. |
| **bikube** (old) | `/home/keks/bikube` | Legacy reference. Much richer in views/routes/modules but on older stack. Read-only for porting patterns. Never modify. |

---

## bikube-next: Current State

### Routes (grouped by zone)

**Public (unauthenticated)**
- `GET /` — home page (public.home) — renders DB categories + scenarios
- `GET /category/delivery` — delivery category landing (PublicOrderRequestController)
- `GET /p/{slug}` — CMS static pages (PublicCmsController)
- `GET /services/{serviceSlug}` — service page (PublicCmsController)
- `GET /services/{serviceSlug}/request` + `POST` — order intake form (PublicOrderRequestController)
- `GET /order-requests/{orderNumber}/received` — confirmation page
- `GET /become-worker` + `POST` — worker application
- `GET /become-worker/received` — application received confirmation
- `GET /worker-invitations/{token}` + `POST` — invitation acceptance
- Auth routes: login, register, forgot-password, reset-password, 2FA, passkeys (Fortify/Laravel)

**Worker (auth + approved.worker middleware)**
- `GET /worker/dashboard` — WorkerCockpitController@dashboard
- `GET /worker/orders` — assignment list
- `GET /worker/orders/{order}` — assignment detail (GPS ping, navigation, completion proof)
- `POST /worker/orders/{order}/accept|start|arrived-pickup|picked-up|arrived-dropoff|complete` — lifecycle transitions
- `POST /worker/orders/{order}/completion-proof` — submit proof
- `POST /worker/presence/online|offline` — presence toggle
- `POST /worker/location-pings` — real GPS ping (secure context only)
- `GET /worker/payout-profile` + `POST` + `POST /submit` — payout profile
- `GET /worker/payout-reviews` + `POST /{type}/submit` + `POST /{review}/evidence` — identity/tax/payout reviews
- `GET /worker/payout-reviews/evidence/{evidence}/download`
- `GET /worker/support` + `GET /{ticket}` + `POST /{ticket}/reply`

**Customer Account (auth middleware)**
- `GET /account` — dashboard (AccountDashboardController)
- `GET /account/orders` + `/{order}` — order list and detail
- `GET /account/billing` + `/documents/{billingDocument}` — invoices
- `GET /account/support` + `/create` + `POST` + `/{ticket}` + `/{ticket}/reply`
- `POST /account/completion-proofs/{proof}/accept|dispute` — confirm/dispute worker proof

**Admin (Filament panel)**
- `GET /admin` — dashboard (real queues, blockers — no fake data)
- All admin/* routes are Filament-managed — see Pages & Resources below

**API**
- `POST /api/payments/vipps-mobilepay/webhook` — Vipps MobilePay webhook endpoint

**System / Utility**
- `GET /admin/live-operations-map/data` — JSON data for LiveOps map
- `GET /admin/worker-documents/{id}/download` — protected doc download
- `GET /admin/support-attachments/{media}/download`
- `GET /admin/security-audit-exports/{export}/download`
- `POST /locale` — locale switch (LocaleController)
- `GET /theme-palette/config` + `POST /save` + `POST /reset`
- `/horizon/*`, `/pulse`, `/log-viewer/*` — observability

**Total routes: 215**

### Filament Pages

| Page | Route |
|---|---|
| Dashboard | `/admin` |
| AuditLog | `/admin/audit-log` |
| ContentCms | `/admin/content-cms` |
| DispatchCenter | `/admin/dispatch-center` |
| FinanceControl | `/admin/finance-control` |
| LiveOperationsMap | `/admin/live-operations-map` |
| MapSettings | `/admin/map-settings` |
| OperationZones | `/admin/operation-zones` |
| OperationsCommandCenter | `/admin/operations-command-center` |
| OperationsSettings | `/admin/operations-settings` |
| OrderBoard | `/admin/order-board` |
| OrderConfig | `/admin/order-config` |
| OrderTracking | `/admin/order-tracking/{order}` |
| OrdersHub | `/admin/orders-hub` |
| PaymentProviderSettings | `/admin/payment-provider-settings` |
| PayoutProviderSettings | `/admin/payout-provider-settings` |
| PeopleWorkforce | `/admin/people-workforce` |
| PlatformSettings | `/admin/platform-settings` |
| SecurityFileScanner | `/admin/security-file-scanner` |
| SecurityGovernance | `/admin/security-governance` |
| ServicesCatalog | `/admin/services-catalog` |
| SupportCenter | `/admin/support-center` |
| SystemSecurity | `/admin/system-security` |
| ThemePaletteSettings | `/admin/theme-palette-settings` |
| TranslationManager | `/admin/translation-manager` |

### Filament Resources

| Resource | Routes |
|---|---|
| BillingDocuments | `/admin/billing-documents` |
| CmsPages | `/admin/cms-pages` (list/create/edit) |
| Orders | `/admin/orders` (list/view/edit) |
| PaymentRecords | `/admin/payment-records` |
| PaymentWebhookEvents | `/admin/payment-webhook-events` |
| PricingRules | `/admin/pricing-rules` (list/create/edit) |
| SeoMetadata | `/admin/seo-metadata` (list/create/edit) |
| ServiceCategories | `/admin/service-categories` (list/create/edit) |
| ServicePages | `/admin/service-pages` (list/create/edit) |
| ServiceScenarios | `/admin/service-scenarios` (list/create/edit) |
| SupportTickets | `/admin/support-tickets` (list/create/view/edit) |
| WorkerApplications | `/admin/worker-applications` (list/edit) |
| WorkerDocuments | `/admin/worker-documents` (list/edit) |
| WorkerPayoutProfiles | `/admin/worker-payout-profiles` |
| WorkerPayoutReviews | `/admin/worker-payout-reviews` |
| WorkerProfiles | `/admin/worker-profiles` (list/create/view/edit) |
| WorkerSettlementEntries | `/admin/worker-settlement-entries` |
| WorkerSettlementRules | `/admin/worker-settlement-rules` (list/create/edit) |

### Views (by section)

**public/**
- `public/home.blade.php` — full dark-theme homepage, loads DB categories + scenarios, slider, process steps, readiness section
- `public/cms/page.blade.php` — CMS static page renderer
- `public/cms/service-page.blade.php` — service page with scenario detail
- `public/services/scenario.blade.php` — scenario detail
- `public/orders/request.blade.php` — intake form (scenario-driven dynamic fields)
- `public/orders/confirmation.blade.php` — post-submit confirmation
- `public/workers/apply.blade.php` — worker application form
- `public/workers/received.blade.php` — application received
- `public/workers/invitation.blade.php` — invitation accept form
- `public/workers/invitation-error.blade.php`
- `public/workers/account-created.blade.php`
- `public/categories/delivery.blade.php` — delivery category landing
- `public/layouts/app.blade.php` — public layout shell

**account/**
- `account/dashboard.blade.php` — customer dashboard (orders, billing, support summary)
- `account/orders/index.blade.php` — order list
- `account/orders/show.blade.php` — order detail + completion proof action
- `account/billing/index.blade.php` — billing documents list
- `account/billing/show.blade.php` — billing document detail

**worker/**
- `worker/layout.blade.php` — worker cockpit layout
- `worker/dashboard.blade.php` — cockpit dashboard (KPIs, active assignment, settlement)
- `worker/orders/index.blade.php` — assignment list
- `worker/orders/show.blade.php` — assignment detail (lifecycle actions, GPS, navigation, proof)
- `worker/payout-profile.blade.php` — payout profile form
- `worker/payout-reviews.blade.php` — identity/tax/payout review list + upload
- `worker/blocked.blade.php` — blocked worker notice

**support/** (shared between account and worker paths)
- `support/index.blade.php`
- `support/create.blade.php`
- `support/show.blade.php`

**layouts/**
- `layouts/account-shell.blade.php` — account area layout
- `layouts/public-shell.blade.php` — public shell

**auth/**
- `auth/login.blade.php`

**filament/pages/** — Filament-specific blade views for each admin page
**components/admin-os/** — Admin OS component library:
  kpi-card, status-badge, empty-state, context-panel, action-button, timeline-item,
  module-shell, queue-card, liveops-nav, page-shell, readiness-card, action-matrix

### Controllers

| Controller | Responsibility |
|---|---|
| `PublicOrderRequestController` | Service intake form + order creation + confirmation |
| `PublicCmsController` | CMS page + service page rendering |
| `PublicWorkerApplicationController` | Worker application (create/store/received) |
| `PublicWorkerInvitationController` | Worker invitation token (show/store/received) |
| `WorkerCockpitController` | Worker dashboard, order list/detail, lifecycle actions, presence, GPS |
| `OrderCompletionController` | Completion proof submit + customer accept/dispute |
| `WorkerPayoutProfileController` | Payout profile show/update/submit |
| `WorkerPayoutReviewController` | Identity/tax/payout reviews index + submit |
| `WorkerPayoutEvidenceController` | Evidence upload + download |
| `WorkerSupportController` | Worker support ticket list/detail/reply |
| `AccountDashboardController` | Customer account dashboard |
| `AccountOrderController` | Customer order list + detail |
| `AccountBillingController` | Billing documents list + detail |
| `AccountSupportController` | Customer support ticket CRUD + reply |
| `AdminLiveOperationsMapDataController` | JSON worker GPS data for LiveOps map |
| `AdminWorkerDocumentDownloadController` | Protected worker document download |
| `AdminSupportActivityController` | Admin support activity feed |
| `AdminSupportAttachmentDownloadController` | Protected support attachment download |
| `AdminSecurityAuditExportDownloadController` | Protected audit export download |
| `ThemePaletteController` | Admin theme palette config/save/reset |
| `LocaleController` | Locale switch (nb/en/uk/ru) |
| `Api/VippsMobilePayWebhookController` | Vipps MobilePay webhook handler |

### Language Coverage

Four languages: `nb`, `en`, `uk`, `ru`.
Files: `lang/{locale}/bikube.php` — domain-specific strings.
Admin UI strings: vast flat key dictionary in `lang/nb/bikube.php` under `admin_ui` key
(covering nearly all Filament admin panel labels, statuses, blockers).
Vendor: `lang/vendor/benriadh-filament-translation-manager/` (en + fr only — fr appears unused).

**Coverage gaps:**
- `admin_ui` flat key dictionary is very large in `nb` but may have gaps in `en`/`uk`/`ru`
- Translation Manager page (`/admin/translation-manager`) exists for managing this
- Mixed language risk: `public/home.blade.php` is mostly in **Russian** (nav labels, hero text, category descriptions, footer), not Norwegian — this is a mismatch for a Narvik-first platform

### Recent Git Commits (top 30)

```
6865b3f Port worker cockpit layout from legacy LK
17054ec Polish BiKuBe OS admin shell and delivery corridor
502ab94 Harden Admin OS runtime localization
211760b Transform
95521a2 Reshape admin into BiKuBe business operations shell
e11bba3 Complete
0f7f955 Fix
c067d4c Harden
fb0656c Complete
fdc1678 Add translation manager and four-language localization
d2ac202 Add incident recurrence and corrective action verification
8a846de Add incident governance and postmortem workflow
638da86 Add security incident response playbooks
2b6dd98 Add export retention jobs and security incidents
0678ac6 Add protected audit export downloads and notification ownership
bdac709 Add governance notifications and security audit exports
5e27fef Add reviewer access expiry and access review workflow
ccd97f2 Add reviewer lifecycle and security governance cockpit
4e04f07 Add controlled security reviewer provisioning
304dd10 Add scanner configuration cockpit and retention jobs
07a3bc8 Add private evidence malware scan gate
382a49c Add private worker payout evidence upload
8e2a534 Add worker tax and identity payout review workflow
10034e6 Add worker payout profile readiness workflow
8d98e62 Add reviewer role separation and payout provider cockpit
b6ccb75 Add settlement policy review and payout provider contract gate
81c6ef9 Add worker settlement rule governance
5129c4b Complete worker settlement ledger readiness acceptance
3aa61fb Add worker settlement ledger and payout readiness
cd6a818 Complete order completion proof acceptance gaps
```

---

## bikube (old): Useful Assets for Reference

### Views worth porting

| Old path | Value |
|---|---|
| `resources/views/lk/executor/job-show.blade.php` | Rich Tailwind-styled job detail with status badges, timeline, action buttons — much more polished than current worker/orders/show |
| `resources/views/executor/dashboard.blade.php` | Job table with status badges, assignment accept/decline links |
| `resources/views/executor/layout.blade.php` | Clean executor/worker shell layout with Tailwind |
| `resources/views/public/order-form.blade.php` | Public order form with real field rendering logic |
| `resources/views/public/category-services.blade.php` | Category-scoped services grid |
| `resources/views/filament/pages/tasks-kanban.blade.php` | Kanban board view — useful reference for OrderBoard |
| `resources/views/filament/pages/dispatch.blade.php` | Old dispatch page — reference for DispatchCenter |
| `resources/views/filament/resources/support-ticket-resource/relation-managers/messages-chat.blade.php` | Real-time chat relation manager for support tickets |
| `resources/views/livewire/support-ticket-chat.blade.php` | Livewire chat component |
| `resources/views/components/account-layout.blade.php` | Account section layout |
| `resources/views/lk/partials/assistant.blade.php` | Worker/LK assistant partial |

### Routes/logic worth referencing

Old `routes/web.php` has:
- `lk.*` prefix group — complete worker LK with wallet, schedule, notifications, settings, profile, roadside jobs, support, assistant
- `account.*` group — orders+track, deliveries, repairs, claims with messages, notifications feed, security+2FA, errands, billing transactions, social care visits
- `executor.*` — executor jobs with accept/decline/status
- `handyman.*` — catalog + booking
- `repair.*` — intake + project
- `checkout/{scenario}` — Checkout controller (session basket + scenario-driven checkout)

Old files `routes/logistics-api.php`, `routes/agency-agents.php`, `routes/api-virtual-office.php` — reference for future API design.

### Components/layouts useful to copy

- `resources/views/components/account-layout.blade.php` — account shell with sidebar nav
- `resources/views/components/service-cards-grid.blade.php` — services grid component
- `resources/views/components/fast-order-form.blade.php` — quick order form

---

## Gap Analysis

### Pages that look weak or unfinished

| Area | Issue |
|---|---|
| `worker/orders/show.blade.php` | All content is on ONE LINE (minified). Hard to read/edit. Needs formatting. |
| `account/dashboard.blade.php` | All content is on ONE LINE (minified). Same issue. |
| Public homepage (`public/home.blade.php`) | UI/copy is in **Russian** — nav anchors, hero text, category descriptions, footer. Should be Norwegian (nb) or use translation keys. This is the most critical language mismatch. |
| Worker payout reviews (`worker/payout-reviews.blade.php`) | File exists but depth unknown — needs review |
| Account support views | `support/` views are shared between account and worker — verify they are routed correctly per context |
| No mobile menu on homepage | `nav-links` is `display:none` on mobile — no hamburger implemented |
| `account/orders/show.blade.php` | Completion proof UI — needs review to confirm accept/dispute is properly wired |

### Routes that are 404 or missing

- Worker has no `/worker/profile` route (no settings, no name/contact update)
- Worker has no `/worker/notifications` route
- No public `/workers` page (just `/become-worker`) — no worker recruitment landing
- No `/account/profile` route in bikube-next (exists in old bikube)
- No `/account/notifications` route in bikube-next
- No customer order tracking route (`/orders/{id}/track` exists only in old bikube)
- `/admin/support-tickets/create` exists in Filament but account/worker cannot escalate to admin directly
- `filament/pages/content-cms.blade.php` and `filament/admin-ui-translator.blade.php` — verify they are functional stubs or real pages

### Fake/placeholder risks

- Hero stats on homepage show hardcoded `"10+"`, `"Request"`, `"Narvik"`, `"Честно"` — not real DB counts (intentional per policy, but caller must not claim these are live KPIs)
- `Popular Scenarios` slider in homepage has **hardcoded slug array** (`delivery-groceries`, `delivery-meals`, etc.) — these slugs may or may not exist in the DB; broken links if scenario is not seeded
- `"Partner portal not connected yet"` button in homepage readiness section — fine as placeholder
- Admin Dashboard shows real queue counts but is susceptible to showing zeros when DB is empty — verify copy says "no data" not "nothing to do"

### Mixed language risks

| File | Problem |
|---|---|
| `resources/views/public/home.blade.php` | Russian UI copy. Target market is Norway. Should be nb or en. |
| `resources/views/worker/dashboard.blade.php` | English (acceptable for operator-facing) |
| `resources/views/account/dashboard.blade.php` | English (acceptable, but Norwegian customers may expect nb) |
| `worker/orders/show.blade.php` | English — acceptable |
| Support views | Likely English — check with nb locale |
| `lang/nb/bikube.php` admin_ui keys | Very complete in nb, but runtime rendering depends on correct `__()` calls in blade |

---

## Next 5 Product Tasks (Priority Order)

### 1. Fix public homepage language mismatch (HIGH PRIORITY)
**Why:** The homepage hero, nav, category descriptions, and footer are in Russian. This is wrong for a Norwegian-market platform. Customers who arrive at bikube-next will see Russian.
**Files to change:**
- `resources/views/public/home.blade.php` — replace all hardcoded Russian strings with `__('bikube.xxx')` calls or Norwegian text
- `lang/nb/bikube.php` — add `public.*` keys for homepage strings
- `lang/en/bikube.php`, `lang/uk/bikube.php`, `lang/ru/bikube.php` — mirror those keys

### 2. Format/refactor minified blade views (MEDIUM)
**Why:** `worker/orders/show.blade.php` and `account/dashboard.blade.php` are single-line files. This makes them impossible to diff, review or extend.
**Files to change:**
- `resources/views/worker/orders/show.blade.php` — expand from 1-line minified to proper indented Blade
- `resources/views/account/dashboard.blade.php` — same
- Consider doing `worker/orders/index.blade.php` and `support/*.blade.php` too

### 3. Add worker profile / settings route (MEDIUM)
**Why:** Workers can toggle presence and submit GPS but cannot update their own profile (name, phone, vehicle). Old bikube has `lk.profile`, `lk.settings`. bikube-next has no `/worker/profile`.
**Files to create/change:**
- `routes/web.php` — add `GET|POST /worker/profile` inside the worker middleware group
- `app/Http/Controllers/WorkerProfileController.php` — new controller
- `resources/views/worker/profile.blade.php` — new view (port from `bikube/resources/views/executor/layout.blade.php` pattern)

### 4. Fix popular scenarios slider hardcoded slugs (MEDIUM)
**Why:** The homepage slider has 8 hardcoded scenario slugs. If those scenarios don't exist in the DB (or have different slugs), users click through to 404 service pages.
**File to change:**
- `resources/views/public/home.blade.php` — replace hardcoded `$featuredScenarios` array with a DB query:
  ```php
  $featuredScenarios = \App\Models\ServiceScenario::active()
      ->whereNotNull('cover_image')
      ->orderBy('sort_order')
      ->take(8)
      ->get();
  ```
  Or add a `is_featured` flag to `service_scenarios` table.

### 5. Add account profile route (MEDIUM)
**Why:** Logged-in customers have no way to update their name, email or password via the account portal. This is a basic account hygiene gap before any real launch.
**Files to create/change:**
- `routes/web.php` — add `GET|POST /account/profile` inside the account middleware group
- `app/Http/Controllers/AccountProfileController.php` — new controller (port from old `bikube` Account\ProfileController pattern)
- `resources/views/account/profile.blade.php` — new view

---

## Rules & Constraints

- **NEVER modify `/home/keks/bikube` (old project)** — read-only reference only
- **NEVER run `migrate:fresh`** — only `migrate` for additive changes
- **NEVER run `composer install` or `npm install`** without explicit user request
- **NEVER change `.env`**
- **No fake data** — all admin pages and worker/customer pages must show real DB state or explicit "no data" messages. Never invent KPIs.
- **No GPS faking** — GPS pings must come from real browser geolocation with permission. No fallback coordinates.
- **No fake payment state** — Vipps MobilePay is not connected. Show "not connected" blockers, never a fake "payment captured" state.
- **Filament Shield** is active (v4.2.0) — all new admin actions must be permission-gated
- **Audit trail** — sensitive actions (document downloads, evidence access, settlement rules, payout approvals) must leave audit log entries
- **Private evidence** — worker payout evidence files use private local storage with SHA-256 hashes. Never serve via public URL.
- **Language: Narvik-first** means Norwegian (nb) is the primary customer-facing locale. English is acceptable for admin/operator UI. Russian/Ukrainian are secondary.
- **Storage not linked** (`php artisan about` confirms `public/storage: NOT LINKED`) — do not rely on `storage:link` in development without explicit confirmation

---

## Validation Commands

```bash
# Check all routes
wsl -d Ubuntu -u keks -- bash -c "cd /home/keks/bikube-next && php artisan route:list 2>&1 | head -250"

# Application status
wsl -d Ubuntu -u keks -- bash -c "cd /home/keks/bikube-next && php artisan about 2>&1"

# Recent commits
wsl -d Ubuntu -u keks -- bash -c "cd /home/keks/bikube-next && git log --oneline -20"

# Uncommitted changes
wsl -d Ubuntu -u keks -- bash -c "cd /home/keks/bikube-next && git status --short"

# Run migrations (additive only)
wsl -d Ubuntu -u keks -- bash -c "cd /home/keks/bikube-next && php artisan migrate --force 2>&1"

# Check views are cached
wsl -d Ubuntu -u keks -- bash -c "cd /home/keks/bikube-next && php artisan view:cache 2>&1"

# Check for any syntax errors in PHP
wsl -d Ubuntu -u keks -- bash -c "cd /home/keks/bikube-next && php artisan cache:clear 2>&1"

# List Filament resources
wsl -d Ubuntu -u keks -- bash -c "ls /home/keks/bikube-next/app/Filament/Resources/"

# List all views by section
wsl -d Ubuntu -u keks -- bash -c "find /home/keks/bikube-next/resources/views -name '*.blade.php' | sort"
```

---

## Stack Reference

| Item | Value |
|---|---|
| Laravel | 13.14.0 |
| PHP | 8.4.21 |
| Filament | v5.6.6 |
| Livewire | v4.3.1 |
| Filament Shield | v4.2.0 |
| Spatie Permissions | v7.4.2 |
| Laravel Pulse | v1.7.4 |
| Database | PostgreSQL |
| Queue/Cache | Database driver (local) |
| Session | Database |
| Broadcasting | Log (Reverb not connected) |
| Mail | Log (no real mailer) |
| Storage | Local (public/storage NOT LINKED) |
| Locale | en (default app locale) |
