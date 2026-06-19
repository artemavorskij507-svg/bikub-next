# GLF MaT — Filament Plugin Decision Matrix

_No plugins were installed to produce this document or the GLF MaT MVP fixes. Filament version
verified on the live server: **filament/filament v5.6.6** (released 2026-05-27 — roughly three
weeks old at the time of writing)._

---

## Why this matters before recommending anything

Filament 5 is very new. The community plugin ecosystem (built mostly for Filament 3/4) needs
time to catch up — plugin authors typically ship Filament-major-version support weeks to months
after a major release. That timing risk is the main reason most categories below land on
"no plugin needed for this phase": installing an unmaintained-for-v5 plugin would trade a small
convenience for real breakage risk on a production admin panel.

---

## Category verdicts

### 1. Media upload
**Need:** Real menu item photos, gallery images, partner logo (once `restaurant_menu_items` /
`restaurant_gallery_images` exist — see `docs/GLF_MAT_MODEL_ARCHITECTURE.md`).
**Verdict: No plugin needed for this phase.**
Filament 5 ships `Filament\Forms\Components\FileUpload` in core — it already covers single/
multi-image upload, image editor cropping, and disk storage out of the box. A dedicated media
library plugin (e.g. Spatie Media Library Filament plugins) only earns its weight once there are
many media-bearing models reused across resources. With zero menu/gallery tables built yet,
core `FileUpload` is sufficient when those tables are eventually approved.

### 2. Calendar / bookings
**Need:** Visual calendar for `restaurant_bookings` (table availability, day/week view).
**Verdict: No plugin needed for this phase.**
There is no `restaurant_bookings` table yet — bookings live in `orders.metadata.intake`. A
calendar plugin has nothing real to render against today. When the booking table exists, revisit
this category specifically (a FullCalendar-based Filament plugin would be the natural fit), but
only after confirming Filament 5 compatibility at that time, since plugin support may have
matured by then.

### 3. Settings
**Need:** Partner-level settings (delivery zones, opening hours, min order amount).
**Verdict: No plugin needed for this phase.**
The platform already uses `spatie/laravel-settings` for global settings (confirmed in use
elsewhere in BiKuBe, e.g. `ThemePaletteSettings`, `MapSettings`). Partner-scoped settings, once
`restaurant_delivery_settings` exists, fit naturally as a normal Eloquent-backed Filament
resource page — no settings plugin needed, just a form on the partner profile page.

### 4. Translatable content
**Need:** GLF MaT page currently mixes Ukrainian copy with Norwegian platform conventions
(`bikube.admin.*` translation keys exist elsewhere in the codebase).
**Verdict: No plugin needed for this phase.**
Laravel's own translation files (`lang/*.json` or `lang/{locale}/*.php`) already power the rest
of BiKuBe's admin panel. Adding a Filament translatable-fields plugin would only matter if
`restaurant_profiles`/`restaurant_menu_items` need *per-record* multi-language editing (e.g. the
same dish description in Ukrainian and Norwegian). That's a real future need once the menu
table exists, but premature today — no menu rows exist to translate.

### 5. Map / location
**Need:** Delivery zone visualization, restaurant location pin.
**Verdict: No plugin needed for this phase.**
BiKuBe already has a working Leaflet-based map implementation (used in the worker cockpit and
`admin/live-operations-map`), loaded via CDN with no Filament plugin dependency. The same
pattern (plain Leaflet + CDN, no plugin) is the lowest-risk way to add a partner location pin
later — it has zero Filament-major-version coupling.

### 6. Import / export
**Need:** Bulk menu item import (CSV/Excel) once GLF MaT wants to upload their full menu at
once instead of one dish at a time.
**Verdict: No plugin needed for this phase.**
No menu table exists yet, so there's nothing to import into. Filament 5 core ships table export
actions; for CSV import specifically, `filament/spatie-laravel-media-library-plugin`-adjacent
import plugins exist but should be evaluated for Filament 5 support only once
`restaurant_menu_items` is real and GLF MaT has more than ~10-15 dishes (below that, manual
entry through a normal resource form is faster than building/vetting an import pipeline).

---

## Overall verdict

**No Filament plugins are needed for the current GLF MaT MVP phase.** Every requirement that
looked plugin-shaped is either already covered by Filament 5 core components, already solved
elsewhere in BiKuBe with a non-plugin pattern (Leaflet maps, Spatie settings, Laravel
translations), or blocked on a table that doesn't exist yet and shouldn't be built speculatively.

**Re-evaluate this matrix when:**
- `restaurant_menu_items` / `restaurant_gallery_images` are approved and built (media library,
  import categories become relevant again).
- `restaurant_bookings` is approved and built (calendar category becomes relevant again).
- A second restaurant partner is onboarded (translatable content becomes more pressing if that
  partner operates in a different primary language).
