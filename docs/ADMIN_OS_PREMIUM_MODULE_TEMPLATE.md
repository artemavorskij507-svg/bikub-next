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

## Acceptance

- Browser UAT covers the command center, deep resource, selected record, empty states, and permission boundaries.
- Screenshot-level review confirms hierarchy, density, overflow, and responsive behavior.
- Validation includes PHP lint, routes, tests, Blade compilation, and fresh log review.
