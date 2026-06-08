# BiKuBe Next Plugin Decision Matrix

Status: first clean-project decision log.

## Sources Inspected

- Filament installation documentation: Filament 5 requires PHP 8.2+, Laravel 11.28+, Tailwind CSS 4.1+.
- Filament plugin catalog: community plugin list; most plugins are third-party/community built and not security-reviewed by the Filament team.
- TomatoPHP catalog: useful as an ecosystem reference, not as a bulk install target.
- MadeWithLaravel plugin catalog: useful as a package discovery catalog, not an architecture source.
- Elfsight Laravel modules: marketing widgets only, not operational core.

## Install Now

Installed in the first wave:

- `filament/filament` for Admin OS panel builder.
- `laravel/sanctum` for API token/session auth foundation.
- `laravel/fortify` for auth hardening and 2FA foundation.
- `laravel/reverb` for realtime events.
- `laravel/horizon` for queue visibility.
- `laravel/pennant` for feature flags.
- `laravel/pulse` for application telemetry.
- `laravel/telescope` as dev-only diagnostics.
- `spatie/laravel-permission` for RBAC.
- `spatie/laravel-activitylog` for audit trail.
- `spatie/laravel-medialibrary` for media/document/proof files.
- `spatie/laravel-settings` for typed settings.
- `spatie/laravel-translatable` for multilingual content models.
- `spatie/laravel-sitemap` for SEO sitemap generation.
- `bezhansalleh/filament-shield` for Filament RBAC UI.

## Evaluate Later

Do not install until compatibility, maintenance, security posture and business fit are verified:

- Filament Spatie Media Library UI plugin.
- Filament Spatie Settings UI plugin.
- Filament Spatie Translatable plugin. Composer resolution showed available versions are not compatible with Laravel 13 / Filament 5 in this environment.
- Activitylog viewer plugin for Filament.
- FullCalendar-compatible Filament widget.
- Apex/chart widgets.
- Pennant Manager UI.
- Queue monitor UI beyond Horizon.
- Scheduler monitor UI.
- Mature map field plugin.
- Email template editor.
- Browser notifications plugin.
- Backup/log viewer UI.
- Advanced tables/reporting plugins.
- Address autocomplete, country selector, QR, short URL and impersonation plugins.

## Avoid

- Full TomatoPHP ecosystem install.
- Random CRM/shop/booking/chat/payment plugins that bypass BiKuBe domain modules.
- Elfsight for orders, checkout, dispatch, GPS, worker app, payments, payouts, partner catalog, support tickets or accounting.
- Any plugin that creates duplicate Admin OS navigation or cannot explain its BiKuBe business purpose.

## Build Custom

- Order Engine.
- Service Scenario Engine.
- Dispatch Engine.
- GPS / worker presence.
- Live Operations Map.
- Payment Engine adapters and webhook idempotency.
- Wallet ledger and payouts.
- Support/chat tied to orders/payments/workers.
- Partner portal workflows.
- Customer account and tracker.
- Worker PWA / LK.
- Compliance/legal boundary controls.

## First Wave Reality

The first wave intentionally installs infrastructure and platform packages only. It does not install calendar/chart/map/email template plugins yet because those need compatibility checks against Laravel 13 and Filament 5.
