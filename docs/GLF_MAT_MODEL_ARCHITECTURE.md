# GLF MaT — Model Architecture Plan

_Document only. No migrations created or run by this document._
_Status: MVP runs on existing Order + ServiceScenario tables. Everything below
"LATER WITH APPROVAL" requires explicit owner sign-off before any migration is written._

---

## MVP NOW — no new tables

GLF MaT currently runs entirely on tables that already exist in BiKuBe OS:

| Need | Backed by | Notes |
|---|---|---|
| Delivery request | `orders` + `service_scenarios` (`delivery.meals`, slug `delivery-meals`) | 6 real intake fields configured: `restaurant_name`, `pickup_address`, `dropoff_address`, `order_reference`, `delivery_window`, `contact_phone` |
| Booking request | `orders` + `service_scenarios` (`restaurant.booking`, slug `restaurant-booking`) | 3 real intake fields: `booking_date`, `booking_time` (7 fixed slots), `guest_count` (8 fixed bands) |
| Admin queue | `app/Filament/Pages/GLFMaTPartnerDashboard.php` | Filters `orders.service_scenario_key IN ('delivery.meals','restaurant.booking')` |
| Menu shown to customers | Hardcoded PHP array in `resources/views/public/food.blade.php` | Not a DB table. Explicitly labeled as preview in the admin cockpit. |

**Known MVP limitation:** `delivery.meals` and `restaurant.booking` are generic, platform-wide
scenarios. There is currently only one active restaurant partner (GLF MaT), so filtering admin
views by `service_scenario_key` is an honest proxy for "GLF MaT requests." The moment a second
restaurant partner is onboarded onto the same scenarios, this proxy breaks — there is no
`partner_id` / `restaurant_id` column anywhere in the request path to disambiguate. This is the
single biggest reason the tables below are needed before onboarding partner #2.

---

## LATER WITH APPROVAL — proposed tables

None of these exist yet. None should be created without a separate, explicit "yes, build this
migration" from the owner. Each entry states whether it's needed now or can wait.

### 1. `restaurant_partners` (or generalize as `partners`)
**Purpose:** One row per restaurant/partner business (GLF MaT today, others later).
**Fields:** `id`, `name`, `slug`, `status` (pending/active/suspended), `contact_email`,
`contact_phone`, `owner_user_id` (nullable FK to `users`), `onboarded_at`, `metadata` (jsonb).
**Relationships:** `hasMany` RestaurantProfile (1:1 in practice), `hasMany` RestaurantMenuItem,
`hasMany` RestaurantBooking, `hasMany` PartnerStaff.
**Needed now or later:** Later — only matters once partner #2 exists, or once GLF MaT needs its
own login separate from platform admin.
**Can use existing Order/ServiceScenario now?** Yes, fully, for a single-partner MVP.
**Migration needed?** Yes, new table.
**Admin resource/page needed?** Yes — a `PartnerResource` (Filament) to manage partner accounts.

### 2. `restaurant_profiles`
**Purpose:** Public-facing brand/profile data for a partner (the content currently hardcoded in
`food.blade.php`: tagline, hero copy, address, opening hours summary, social links).
**Fields:** `id`, `partner_id` (FK), `display_name`, `tagline`, `description`, `address`,
`city`, `phone`, `email`, `logo_media_id`, `hero_image_media_id`, `social_links` (jsonb).
**Relationships:** `belongsTo` RestaurantPartner.
**Needed now or later:** Later — today this content is safe as static Blade, since there is one
partner and no self-service editing requirement yet.
**Can use existing Order/ServiceScenario now?** N/A (not order data).
**Migration needed?** Yes.
**Admin resource/page needed?** Yes — would replace the hardcoded hero/about copy in
`food.blade.php` with DB-driven content.

### 3. `restaurant_menu_categories`
**Purpose:** Real menu category structure (Ukrainian cuisine, Azerbaijani cuisine, Grill, Soups,
Bakery, Desserts, Drinks, Combo sets) instead of the hardcoded `$catIcons` array.
**Fields:** `id`, `partner_id` (FK), `name`, `slug`, `icon`, `sort_order`, `is_active`.
**Relationships:** `belongsTo` RestaurantPartner, `hasMany` RestaurantMenuItem.
**Needed now or later:** Later.
**Can use existing models now?** No direct equivalent exists.
**Migration needed?** Yes.
**Admin resource/page needed?** Yes.

### 4. `restaurant_menu_items`
**Purpose:** Real dish records, replacing the hardcoded `$dishes` array in `food.blade.php`
(currently 8 dishes with title/subtitle/ingredients/price/rating/badge/category).
**Fields:** `id`, `partner_id` (FK), `category_id` (FK), `title`, `subtitle`, `description`,
`ingredients`, `price`, `currency`, `rating` (nullable — must stay null until real reviews
exist, never fabricated), `badge` (nullable enum: hit/new), `image_media_id`, `is_available`,
`sort_order`.
**Relationships:** `belongsTo` RestaurantPartner, `belongsTo` RestaurantMenuCategory,
`hasMany` RestaurantMenuItemOption.
**Needed now or later:** Later — this is the highest-value future investment (lets GLF MaT
actually manage their own menu instead of a developer editing Blade).
**Can use existing models now?** No.
**Migration needed?** Yes.
**Admin resource/page needed?** Yes — full CRUD with image upload (see plugin matrix).

### 5. `restaurant_menu_item_options`
**Purpose:** Variants/add-ons per dish (size, spice level, extra sauce) for real ordering, not
just a static preview card.
**Fields:** `id`, `menu_item_id` (FK), `name`, `price_delta`, `is_required`, `sort_order`.
**Relationships:** `belongsTo` RestaurantMenuItem.
**Needed now or later:** Later — only matters once checkout becomes itemized (today, delivery
requests are a single free-text "what to order" intake field, not a cart).
**Migration needed?** Yes.
**Admin resource/page needed?** Nested in the menu item resource.

### 6. `restaurant_bookings`
**Purpose:** A dedicated booking record with real fields (party size, table preference, special
occasion) instead of stuffing everything into `orders.metadata.intake`. Would also let multiple
partners share `restaurant.booking` safely via a `partner_id` column.
**Fields:** `id`, `order_id` (FK, nullable — keeps backward link to existing flow), `partner_id`
(FK), `booking_date`, `booking_time`, `guest_count`, `table_number` (nullable, manual today),
`confirmed_by_user_id`, `confirmed_at`, `status`.
**Relationships:** `belongsTo` Order, `belongsTo` RestaurantPartner.
**Needed now or later:** Later, with one exception: **needed now if a second restaurant partner
is onboarded before this table exists** — see the MVP limitation above.
**Can use existing Order/ServiceScenario now?** Yes for single-partner MVP (current state).
**Migration needed?** Yes (when triggered).
**Admin resource/page needed?** Could be a tab on the Order resource rather than a separate page.

### 7. `restaurant_opening_hours`
**Purpose:** Real weekly schedule instead of the static "Пн–Пт: 11:00–23:00" text in the footer.
**Fields:** `id`, `partner_id` (FK), `day_of_week` (0-6), `opens_at`, `closes_at`, `is_closed`.
**Relationships:** `belongsTo` RestaurantPartner.
**Needed now or later:** Later — low urgency, footer text is honest as static copy today.
**Migration needed?** Yes.
**Admin resource/page needed?** Small repeater field on the partner profile page; no need for
a dedicated resource.

### 8. `restaurant_promotions`
**Purpose:** Real combo/discount records instead of the hardcoded `$promos` array (currently
labeled honestly — discounts shown are illustrative, not live offers).
**Fields:** `id`, `partner_id` (FK), `title`, `subtitle`, `price`, `old_price`, `discount_pct`,
`starts_at`, `ends_at`, `is_active`, `image_media_id`.
**Relationships:** `belongsTo` RestaurantPartner.
**Needed now or later:** Later.
**Migration needed?** Yes.
**Admin resource/page needed?** Yes, simple CRUD.

### 9. `restaurant_gallery_images`
**Purpose:** Real atmosphere/interior photos instead of the CSS-gradient/stock-photo
placeholders currently used in the "Атмосфера" and gallery sections.
**Fields:** `id`, `partner_id` (FK), `media_id`, `caption`, `sort_order`.
**Relationships:** `belongsTo` RestaurantPartner.
**Needed now or later:** Later — blocked on the partner actually supplying real photos (cannot
be fabricated per the no-fake-asset rule).
**Migration needed?** Yes.
**Admin resource/page needed?** Yes — simple media upload list (see plugin matrix, media
upload category).

### 10. `restaurant_delivery_settings`
**Purpose:** Real delivery zones, minimum order, free-delivery threshold per partner, replacing
the static "Голосіїво, Печерськ..." zone list and "300 ₴ / 800 ₴" thresholds in `food.blade.php`.
**Fields:** `id`, `partner_id` (FK), `min_order_amount`, `free_delivery_threshold`,
`delivery_zones` (jsonb), `avg_delivery_minutes_low`, `avg_delivery_minutes_high`.
**Relationships:** `belongsTo` RestaurantPartner.
**Needed now or later:** Later.
**Migration needed?** Yes.
**Admin resource/page needed?** Section on the partner profile page.

### 11. `partner_staff` (or reuse Spatie roles on `users` with a `partner_id` pivot)
**Purpose:** Let GLF MaT staff log into a scoped partner view (today, only platform `owner`/
`admin`/`super_admin` can see the dashboard — there is no partner-only login).
**Fields (if dedicated table):** `id`, `partner_id` (FK), `user_id` (FK), `role`
(owner/manager/staff), `invited_at`, `accepted_at`.
**Relationships:** `belongsTo` RestaurantPartner, `belongsTo` User.
**Needed now or later:** Later — meaningful once GLF MaT staff (not platform staff) need direct
access. Today the `canAccess()` check on `GLFMaTPartnerDashboard` is
`admin.orders.view OR hasRole('partner')` — the `partner` role exists in the permission check
but is not yet assigned to any real GLF MaT staff account.
**Migration needed?** Yes, or extend the existing Spatie `model_has_roles` pivot with scoping.
**Admin resource/page needed?** Yes — invite/manage partner staff.

---

## Suggested build order (when owner approves "later" work)

1. `restaurant_partners` + `restaurant_profiles` — unlocks real partner identity, makes
   `partner_id` available for every other table below.
2. `restaurant_bookings` with `partner_id` — removes the single-partner assumption flagged
   above; do this **before** onboarding any second restaurant.
3. `restaurant_menu_categories` + `restaurant_menu_items` — replaces the hardcoded menu, the
   highest visible value for GLF MaT directly.
4. `restaurant_gallery_images`, `restaurant_promotions`, `restaurant_opening_hours`,
   `restaurant_delivery_settings` — polish, can be done in any order.
5. `partner_staff` — only once GLF MaT needs to log in themselves rather than platform admin
   managing requests on their behalf.
