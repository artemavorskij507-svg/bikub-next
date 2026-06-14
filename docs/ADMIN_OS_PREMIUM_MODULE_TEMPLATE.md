# Admin OS Premium Module Template

Every major Admin OS module ships with an operational command-center page. A CRUD resource remains useful for deep management, but is not the module homepage.

## Required Structure

- Overview: compact real-data KPIs without invented trends.
- Queues: prioritized real records with honest empty states.
- Selected entity: the current operational subject and its latest activity.
- Context: linked domain records, ownership, assignments, attachments, and readiness.
- Actions: every visible action works through a domain service, links to a real page, or is disabled with an exact reason.
- Audit and readiness: real timeline/audit evidence and explicit external-integration status.

## Reusable UI Building Blocks

- `admin-os.module-shell`: semantic boundary for a command-center module.
- `admin-os.kpi-card`: compact real-data metric.
- `admin-os.queue-card`: keyboard-operable queue record with selected state.
- `admin-os.status-badge`: human-readable operational state.
- `admin-os.action-button`: real link or command trigger.
- `admin-os.context-panel`: linked operational context.
- `admin-os.timeline-item`: message/event/audit entry.
- `admin-os.empty-state`: honest zero-data state with useful next action.

## Product Rules

- Never render placeholder, foundation, fake success, or raw translation-key copy.
- Every metric must be reproducible from persisted data.
- Do not claim online presence, SLA, payment, tracking, or external delivery without a real signal.
- Use dense, accessible layouts with semantic headings, keyboard-operable links/actions, visible focus, and responsive fallbacks.
- Keep destructive or irreversible actions behind confirmation and domain validation.
- View-only roles must not receive action controls that their permissions cannot execute.

## Dispatch And Map Pattern

- Dispatch queues separate waiting, unassigned, assigned, active, operational-risk, payment-issue, support-issue, and completed states using persisted order data.
- The selected-entity panel combines lifecycle, assignment, customer ownership, quote/payment readiness, support signals, GPS state, blockers, next action, and the real event timeline.
- Worker eligibility must explain both eligibility and ineligibility from profile approval, real availability, scenario capability, active assignments, and real GPS telemetry.
- Actions call domain services and record events/audit. When an action is unsafe or unavailable, disable it and state the exact reason.
- Integration panels must distinguish configured, disabled, deferred, and missing states for support, payments, GPS, and customer tracking.
- A map cockpit always renders the configured real map center, but creates worker markers only from persisted accepted GPS pings. Never use the map center as a worker marker and never draw an inferred route.
- Map data endpoints remain authenticated and permission-protected. The visible cockpit must give operators actionable context rather than linking them to raw JSON.

## LiveOps Matrix Pattern

- Basemap switching retains a keyless OSM fallback, displays provider attribution, and disables providers that require unavailable credentials.
- Map context menus capture real coordinates and expose only working domain actions or disabled actions with an exact reason.
- Operation zones are persisted domain records with validated geometry, lifecycle state, events, and audit. Never render an invented operational zone.
- Marker taxonomy is semantic: worker telemetry, stale GPS, operational zones, support incidents, payment issues, and order/customer positions render only when real coordinates exist.
- Polling is an honest near-real-time mechanism. Do not claim WebSocket or live streaming when the page polls a protected endpoint.
- Animated active, warning, and role states remain subtle, avoid flashing, and respect `prefers-reduced-motion`.

## Fleetbase-Inspired Operations Patterns

- Service Zones combine a persisted registry, map overlay, selected-zone context, event history, and service-backed actions.
- Fleet Map combines real marker layers with active-order and worker sidebars; entities without coordinates remain visible in sidebars but never become map markers.
- Order Board groups real lifecycle and dispatch state. Drag/drop is prohibited unless it calls validated lifecycle services.
- Order Tracking combines a single order, assignment, support/payment/GPS context, and event timeline without inferred routes.
- Scenario Flow Config visualizes persisted scenarios, intake fields, pricing and capability flags. It remains read-only until a real workflow editor domain exists.
- A major Admin OS module is not accepted as CRUD-only; it requires an operational overview, context and safe actions.

## Acceptance

- Browser UAT covers the command center, deep resource, selected record, empty states, and permission boundaries.
- Screenshot-level review confirms hierarchy, density, overflow, and responsive behavior.
- Validation includes PHP lint, routes, tests, Blade compilation, and fresh log review.

## Orders Hub Pattern

Orders are the central operational object connecting customer ownership, pricing, dispatch, worker execution, support and verified GPS. A production Orders Hub must provide real KPI queues, a selected-order command panel, integration context, an aggregated event timeline and an order-health panel derived only from persisted state. Lifecycle changes must use domain services; unavailable actions remain disabled with an exact reason. Standard CRUD remains available for deep management but is not the primary operations surface.

## Finance Control Pattern

Finance Control is a readiness and exception cockpit until a signed payment-provider adapter exists. Quote, ownership, support issue and order-state evidence must come from persisted data. Payment intent, capture and refund controls remain visibly disabled with an exact blocker until provider contracts and audited services exist. Finance queues prioritize missing quotes, provider blockers, payment support issues, missing ownership and manual review.

Billing and payment adapters preserve a strict separation between document state, internal payment records and external provider evidence. Draft invoices never imply payment. Receipts require captured evidence. Disabled providers record blocked attempts without claiming money movement.
