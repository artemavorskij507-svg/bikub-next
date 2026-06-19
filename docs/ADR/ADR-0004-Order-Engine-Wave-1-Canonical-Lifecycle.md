# ADR-0004: Order Engine Wave 1 Canonical Lifecycle

**Status:** Accepted for owner review
**Date:** 2026-06-20
**Owner:** BiKuBe Owner
**Related:** `docs/GOVERNANCE/BEOS.md`, `docs/PRODUCT_DESIGN_AUTHORITY/06_ARCHITECTURE_DATA_EVENTS.md`, `docs/BIKUBE_IMPLEMENTATION_ROADMAP.md`

## Context

BiKuBe needs one canonical Order Engine used by delivery, meals, bulky delivery, moving, eco, handyman, tow, personal-task, classifieds delivery, and future services. The existing implementation already has `Order`, `OrderEvent`, `DispatchAssignment`, `DispatchEvent`, `PaymentRecord`, support tickets, worker cockpit pages, customer order pages, and Filament order administration. However, order lifecycle mutation was split across `OrderEngine`, worker workflow services, and admin actions, and an invalid admin action attempted a non-enum `pending -> confirmed` transition.

## Decision

Wave 1 keeps the existing six order statuses for compatibility:

```text
draft -> submitted -> accepted -> in_progress -> completed
cancelled as terminal from active states
```

No new enum statuses, migrations, or payment/payout automation are introduced in this wave.

`OrderStatus` becomes the canonical transition map and exposes labels/options/terminal helpers. `OrderEngine` becomes the canonical executor of order status transitions and records `OrderEvent` entries. Worker workflow services must delegate order status mutation to `OrderEngine` and keep worker/dispatch telemetry in dispatch events. Worker final completion remains blocked until the completion proof/customer/admin review path is authoritative. Payment and payout remain read-only/manual-disabled surfaces.

## Alternatives Considered

1. **Add full production status model now** (`assigned`, `completion_review`, `disputed`, `closed`, etc.). Rejected for Wave 1 because the owner explicitly prohibited migrations and new enum statuses, and production has existing records.
2. **Leave transition logic distributed.** Rejected because duplicated lifecycle mutation creates drift and unsafe admin/worker behavior.
3. **Minimal canonicalization without schema changes.** Accepted because it improves safety, keeps routes stable, avoids DB changes, and prepares Wave 2.

## Consequences

- All order status transitions added in this wave go through `OrderEngine`.
- Invalid non-enum `pending`/`confirmed` admin mutation is removed.
- Worker cannot directly final-complete an order from the next-action card.
- Payment/payout automation remains disabled/manual until provider, legal, accounting, and security gates are approved.
- Future Wave 2 can introduce richer states through a separate ADR and migration/data migration plan.

## Validation

Required validation for this ADR:

- `php -l` for changed PHP files.
- `php artisan optimize:clear`.
- `php artisan route:list` filtered for order/account/worker/admin surfaces.
- Curl checks for public/admin/account/worker routes.
- Browser screenshots where authenticated state is available.
- Laravel log before/after inspection.
- Git diff and relevant-file-only commit.
