# BiKuBe Next Module Map

Status: clean architecture seed for the new project.

## Platform Shape

BiKuBe Next is a modular Laravel platform, not a Filament-only app.

```
Laravel Core
├── Public Website / Marketplace
├── Customer Account
├── Worker PWA / LK
├── Partner Portal
├── Admin Operations OS via Filament
├── API for mobile/PWA
└── Realtime / GPS / Payments / Notifications
```

## Core Modules

1. Identity & RBAC
2. Service Scenario Engine
3. Order Engine
4. Dispatch Engine
5. Worker GPS / Presence
6. Live Operations Map
7. Customer App
8. Worker PWA / LK
9. Partner Portal
10. Payment Engine
11. Wallet / Payouts / Finance
12. Support / Tickets / Chat
13. Notifications
14. CMS / SEO
15. Catalog / Partners
16. Analytics / Reporting
17. Security / Audit / Compliance
18. DevOps / Observability

## Admin OS Skeleton Pages

- Operations Command Center
- Dispatch Center
- Orders Hub
- People & Workforce
- Service Catalog
- Finance Control
- Support Center
- CMS & SEO
- System & Security

Each skeleton page currently has honest placeholder state only: no fake KPIs, no fake GPS, no fake orders, no fake payment status, and no fake chat.

## Service Scenario Modules

| Scenario | Launch posture | Notes |
| --- | --- | --- |
| `delivery.groceries` | Narvik pilot candidate | Requires checkout, pricing, dispatch, GPS, support and payment readiness. |
| `delivery.meals` | Evaluate | Requires partner menu/catalog and kitchen readiness. |
| `delivery.bulky` | Evaluate | Requires size/weight/stairs/helper constraints. |
| `moving.home` | Evaluate | Requires inventory/photos/estimate workflow. |
| `moving.business` | Evaluate | Requires commercial requirements and larger estimates. |
| `eco.disposal` | Evaluate | Requires item classification and recycling proof. |
| `eco.furniture` | Evaluate | Requires proof/certificate policy. |
| `eco.appliances` | Evaluate | Requires disposal/legal handling rules. |
| `handyman.hourly` | Evaluate | Requires service scope and materials policy. |
| `handyman.assembly` | Evaluate | Requires proof and warranty boundary. |
| `handyman.repair` | Evaluate | Requires consumer rights and warranty boundaries. |
| `tow.emergency` | Evaluate | Requires roadside legal/safety review. |
| `roadside.assistance` | Evaluate | Requires safety and partner readiness. |
| `personal-task.errand` | Legal review required | No medical/social-care/NAV/municipality-care claims. |
| `personal-task.concierge` | Legal review required | Keep scope practical and non-regulated. |
| `classifieds.delivery` | Evaluate | Requires seller/buyer pickup proof. |

## Technology Direction

- PostgreSQL + PostGIS-ready architecture later.
- Redis for cache/queue/realtime.
- Reverb + Echo for realtime.
- MapLibre GL JS / Leaflet for maps.
- OSRM or Valhalla for routing/ETA once coordinates and route snapshots exist.
- Vipps MobilePay adapter first for Norway readiness, with Stripe/Adyen evaluated for marketplace expansion.
- Signicat BankID evaluated later for Norwegian eID.
