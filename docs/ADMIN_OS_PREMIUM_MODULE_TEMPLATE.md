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

## Acceptance

- Browser UAT covers the command center, deep resource, selected record, empty states, and permission boundaries.
- Screenshot-level review confirms hierarchy, density, overflow, and responsive behavior.
- Validation includes PHP lint, routes, tests, Blade compilation, and fresh log review.
