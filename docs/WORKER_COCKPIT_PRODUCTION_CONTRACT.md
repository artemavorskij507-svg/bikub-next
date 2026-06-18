# Worker Cockpit — Production Contract

_BiKuBe OS · Generated 2026-06-18 · Production baseline audit_

---

## 1. Worker roles and auth assumptions

| Assumption | Value |
|---|---|
| Route group | `GET|POST /worker/*` |
| Middleware | `auth` + `approved.worker` (EnsureApprovedWorker) |
| Approval check | `workerProfile->status === 'approved'` |
| Blocked state | Returns `worker.blocked` (HTTP 403) |
| Admin access | Admin panel at `/admin` uses Filament, separate auth |

A user becomes a worker via `/become-worker` public application flow, reviewed in admin, status set to `approved`.

---

## 2. Worker lifecycle

```
not_onboarded → (applies) → pending_verification → (admin approves) → active → (admin suspends) → suspended
```

| State | `worker_profiles.status` | Cockpit access |
|---|---|---|
| Not onboarded | no profile row | Blocked view |
| Pending | `pending` | Blocked view |
| Active | `approved` | Full cockpit |
| Suspended | `suspended` | Blocked view |

---

## 3. Availability states

Stored in `worker_availabilities` table, managed by `WorkerAvailabilityService`.

| State | Description |
|---|---|
| `offline` | Default, no location tracking, not dispatched |
| `online` | Available for dispatch, location tracking allowed |
| `busy` | Implicit: has active accepted/in_progress assignment |

Transitions: `offline ↔ online` via POST `/worker/presence/online|offline`.

GPS consent and tracking are separate from availability. Going online does NOT auto-start GPS.

---

## 4. Assignment (order) lifecycle

Managed by `WorkerOrderWorkflowService`. Enforced by `OrderStatus` enum with `canTransitionTo()`.

```
submitted → accepted → in_progress → completed
              ↓ (reject not yet implemented — cancellation via admin)
           cancelled
```

| Worker action | Route | Status transition | Order event |
|---|---|---|---|
| Accept | `POST /worker/orders/{id}/accept` | `submitted → accepted` | `worker.accepted` |
| Start | `POST /worker/orders/{id}/start` | `accepted → in_progress` | `worker.started` |
| Arrived pickup | `POST /worker/orders/{id}/arrived-pickup` | milestone (in_progress) | `worker.arrived_pickup` |
| Confirm pickup | `POST /worker/orders/{id}/picked-up` | milestone (in_progress) | `worker.picked_up` |
| Arrived dropoff | `POST /worker/orders/{id}/arrived-dropoff` | milestone (in_progress) | `worker.arrived_dropoff` |
| Submit proof | `POST /worker/orders/{id}/completion-proof` | stays in_progress | Creates `order_completion_proofs` row |
| Complete | `POST /worker/orders/{id}/complete` | guarded — requires `arrived_dropoff` event | `worker.completed` |

Illegal transitions throw `ValidationException`. Each action creates both `order_events` and `dispatch_events` records.

---

## 5. GPS policy

- **No tracking before explicit worker action.** Going online does NOT start GPS.
- **No fake coordinates.** `WorkerLocationService` rejects 0,0 and bounds violations.
- **Consent required per ping.** `consent: true` must be in every POST payload.
- **Accuracy enforced.** Max GPS accuracy from `settings.map.max_gps_accuracy_meters` (current: 5000m).
- **Worker must be online.** Pings rejected if `workerAvailability.status` not `online|available`.
- **Target ping interval: 10 seconds** while active assignment is `accepted` or `in_progress`.
- **Stop tracking when:** worker goes offline, page is hidden (visibilitychange), no active assignment, logout.
- **No customer exposure of location before pickup.** The cockpit shows only the worker's own position.
- **Storage:** `worker_location_pings` table. Fields: lat, lng, accuracy_meters, heading, speed_mps, captured_at, order_id (nullable), dispatch_assignment_id (nullable).

---

## 6. Map policy

| Setting | Value |
|---|---|
| Provider | `osm` (OpenStreetMap via Leaflet CDN) |
| Center | Narvik 68.4385, 17.4272 |
| Default zoom | 10 |
| Max accuracy | 5000 m |
| Stale GPS threshold | 120 s |
| Tile CDN | `https://{s}.basemaps.cartocdn.com/dark_all/` (dark theme) or OSM standard |

- Real browser geolocation only. No fake markers.
- If provider or CDN unavailable, show honest "Map provider not configured" panel.
- Courier marker updates on `watchPosition` callback, never interpolated.
- Customer never sees courier position until `worker.arrived_pickup` milestone.

---

## 7. Completion proof

| Type | Status |
|---|---|
| Text note (`worker_note`) | ✅ Implemented — `POST /worker/orders/{id}/completion-proof` |
| Photo upload | ⛔ Not implemented — `OrderCompletionProof` model has `proof_type` field but no media upload route exists yet |
| Customer confirmation | ✅ Via `POST /account/completion-proofs/{id}/accept` (customer portal) |
| Customer dispute | ✅ Via `POST /account/completion-proofs/{id}/dispute` — auto-creates support ticket |

Photo upload is **honest-disabled** with explanation in the UI until a secure media policy and upload route are configured.

---

## 8. Support / escalation

| Item | Status |
|---|---|
| Worker support ticket list | ✅ `GET /worker/support` → WorkerSupportController@index |
| Ticket detail + reply | ✅ `GET /worker/support/{ticket}` + `POST /worker/support/{ticket}/reply` |
| SupportTicketService | ✅ exists — `addMessage()` used for worker replies |
| New ticket creation | ⛔ Worker cannot open tickets directly — must be opened by admin or auto-triggered by dispute |
| Escalation | ✅ Dispute flow auto-creates support ticket |

---

## 9. Production honesty rules

- No fake ratings, no fake ETA, no fake earnings, no fake live GPS.
- No fake order success or fake completion.
- Map shows real OSM tiles or honest disabled state.
- All action buttons either have a real route or a visible disabled reason.
- Empty states explicitly state "no real data" rather than showing fake content.
- KPIs show `0` or `—` rather than made-up numbers.
- Completion proof waits for real customer confirmation — no auto-approve.
- Worker location is shared only with explicit consent and only while online with active assignment.

---

## 10. Remaining blockers and next steps

| Blocker | Severity | Next step |
|---|---|---|
| 0 orders in production DB | Medium | Owner approval required to create controlled smoke order/assignment |
| Photo proof upload not implemented | Low | Requires media policy approval + upload controller |
| Worker cannot open new support ticket | Low | Add `POST /worker/support` route + form |
| No reject-assignment flow | Low | Add POST route + ValidationException guard in service |
| Map CDN requires external request | Info | Leaflet CDN (jsdelivr) + CartoDB tiles — no API key needed, CDN already approved |
