---
Title: CMPAAA-141 GLF MaT Partner Module — Product Contract v1.0
Date: 2026-06-18
Status: Final
Audience: Owner, Partner (GLF MaT), Admin, Workers
---

# GLF MaT Partner Module — Production Contract

## 1. Business Purpose

Create a **pilot-ready partner module** for GLF MaT (Ukrainian + Azerbaijani restaurant) within BiKuBe OS to demonstrate that BiKuBe can:

- Deliver restaurant orders through worker network
- Manage table reservations online
- Provide admin tools for restaurant staff
- Connect customers → orders → workers → delivery

**Not a white-label solution.** This is a working prototype that GLF MaT owner can see, test, and propose to their business partners. Success = "We can show this to a restaurant and say it's ready."

---

## 2. Target Partner Profile

**Restaurant:** GLF MaT (Narvik, Ballangen region)
- Ukrainian & Azerbaijani cuisine
- Table service + delivery operations
- Manual order confirmation (not automated payment yet)
- Workers assigned via BiKuBe dispatch center

**Admin Role:** Partner/restaurant staff can:
- View incoming orders (delivery + table bookings)
- Confirm/reject orders
- See delivery progress
- Manage menu (basic)

**Worker Role:** BiKuBe delivery workers can:
- See assigned food delivery orders
- Navigate to pickup → customer
- Confirm delivery with photo proof

---

## 3. Customer Flows

### 3.1 View Menu (Public)
**Actor:** Customer (anonymous)  
**Flow:**
1. Visit `/services/food`
2. Scroll menu categories (Ukrainian, Azerbaijani, Grill, Soups, Bakery, Desserts, Drinks, Combo)
3. See dish cards with:
   - Dish name & weight
   - Ingredients
   - Price (NOK)
   - Rating (if available) or placeholder
   - Availability (always available for now)
4. No login required

**Success:** Customer sees attractive menu, can filter by category

---

### 3.2 Request Delivery (Public → Order)
**Actor:** Customer (anonymous or registered)  
**Precondition:** Customer on `/services/food`  
**Flow:**
1. Customer clicks "Request Delivery" or scrolls to delivery form
2. Fills form:
   - ✅ Delivery address (required)
   - ✅ Phone number (required)
   - ✅ Items description or selections (required)
   - ✅ Special instructions (optional)
   - ✅ Preferred delivery time (optional)
3. Submits form
4. **Backend validation:**
   - Address not empty
   - Phone matches pattern (Norwegian +47 or local)
   - Items not empty
5. **Order created:**
   - Service scenario: `delivery.meals`
   - Partner: GLF MaT (stored in metadata)
   - Status: `pending` (requires manual confirmation)
   - Customer phone & address stored
6. **Confirmation page shows:**
   - Order number (e.g., `ORD-2026-06-18-001`)
   - Expected delivery time (estimated 40-60 min)
   - Message: "Your order received. Restaurant will confirm in 5-10 minutes."
   - **NOT:** Fake "Order confirmed" — only if actually confirmed
   - Link to order tracking (show when assigned to worker)

**Success Criteria:**
- ✅ Form validation works
- ✅ Order stored in database
- ✅ Confirmation page shows real order number
- ✅ No fake success states
- ✅ Manual confirmation message displayed

---

### 3.3 Reserve Table (Public → Booking Request)
**Actor:** Customer (anonymous or registered)  
**Precondition:** Customer on `/services/food`, section #booking  
**Flow:**
1. Customer clicks "Reserve table"
2. Fills form:
   - ✅ Date (required)
   - ✅ Time (required)
   - ✅ Number of guests (required)
   - ✅ Guest name (required)
   - ✅ Phone (required)
   - ✅ Special requests (optional)
3. Submits form
4. **Backend handling — TWO OPTIONS** (to be decided):

   **Option A: Use existing Order model**
   - Service scenario: `restaurant.booking` (or reuse `delivery.meals` with flag)
   - Status: `pending_confirmation`
   - Admin sees this in "Reservations" filter
   - ✅ Real database persistence
   
   **Option B: UI-only form with backend disabled**
   - Form shows: "Manual confirmation — restaurant will call you"
   - No database storage (placeholder)
   - ⚠️ Not recommended for MVP

5. **Confirmation page shows:**
   - Booking reference number (if using Order model)
   - Message: "Reservation request received. Restaurant will call to confirm."
   - No fake "Confirmed" status unless actually confirmed

**Success Criteria:**
- ✅ Form has real validation
- ✅ Either stored to database OR clearly marked as manual-only
- ✅ Customer gets confirmation reference or message
- ✅ No fake states

---

### 3.4 View Promotions/Combos
**Actor:** Customer  
**Flow:**
1. Scroll to "Promotions" section on `/services/food`
2. See 4 combo sets:
   - Lunch for two (499 NOK, was 668)
   - Azerbaijan evening (649 NOK, was 864)
   - Family plov (890 NOK, was 1180)
   - Grill set (720 NOK, was 940)
3. See discount percentages (-23% to -25%)
4. Click CTA: "Add to order" → goes to delivery form with preset items

**Status:** Combos are static content for MVP. No combo table yet.

**Success Criteria:**
- ✅ Combos display correctly with pricing
- ✅ Links to delivery form work
- ✅ Not fake promotions (real once partner provides data)

---

### 3.5 Contact Restaurant
**Actor:** Customer  
**Flow:**
1. Scroll to footer or contact section
2. See:
   - Phone number (manual, to be provided)
   - Email (manual, to be provided)
   - Address (manual, to be provided)
   - Social links (Instagram, etc.)

**Status:** Contact info is hardcoded for MVP. No CMS integration yet.

---

## 4. Admin/Partner Flows

### 4.1 Partner Dashboard (Filament)
**Actor:** Restaurant admin (staff user with `partner` role for GLF MaT)  
**URL:** `/admin` (login required)  
**Access:** Filament page showing GLF MaT module

**Content:**
1. **Today's summary:**
   - Delivery orders: count, pending confirmation
   - Reservations: count, pending confirmation
   - Workers on duty: count, available

2. **Quick filters:**
   - Show pending orders (need manual approval)
   - Show confirmed orders (waiting for worker assignment)
   - Show in-progress orders (worker has picked up)
   - Show completed orders (delivered today)

3. **CTA buttons:**
   - "View all delivery orders"
   - "Manage menu categories" (disabled if no menu table yet)
   - "View reservations"
   - "Dispatch settings"

**Success Criteria:**
- ✅ Dashboard loads without errors
- ✅ Shows real order counts
- ✅ Links navigate to correct resources
- ✅ No fake data

---

### 4.2 Delivery Orders Manager
**Actor:** Restaurant admin  
**URL:** `/admin/orders?filter=partner:glf-mat`  
**Content:** Filterable list of orders

**For Each Order:**
- Order number
- Customer phone
- Delivery address
- Items description
- Status (pending confirmation / confirmed / assigned / in progress / completed / cancelled)
- Preferred delivery time
- CTA: "Confirm" or "Reject"

**Order Detail View:**
- Full order information
- Customer notes
- Status timeline
- Assigned worker (name, vehicle, contact)
- Delivery map (if worker location available)
- Action: Mark as ready for pickup (triggers worker notification)

**Success Criteria:**
- ✅ Real orders from delivery form appear here
- ✅ Confirmation action creates OrderEvent
- ✅ Worker assignment flow connected
- ✅ No fake orders pre-seeded

---

### 4.3 Table Reservation Manager (TBD)
**Actor:** Restaurant admin  
**URL:** `/admin/reservations` (if booking uses separate table)  
**or** filter `/admin/orders?filter=type:booking`

**For Each Reservation:**
- Booking reference
- Guest name & phone
- Date, time, number of guests
- Special requests
- Status (pending confirmation / confirmed / cancelled / completed)
- CTA: "Confirm" or "Cancel"

**Status:** To be determined in Phase 3 whether this uses Order model or separate table.

---

### 4.4 Menu Manager (Future)
**Actor:** Restaurant admin  
**URL:** `/admin/glf-mat/menu` (placeholder, needs migration)

**Status:** Not implemented for MVP. Manual menu updates via:
- Email to admin
- Hardcoded in food.blade.php
- Or use existing ServiceScenarioField as temporary storage

---

### 4.5 Partner Profile (Future)
**Actor:** Restaurant owner  
**Content to manage:**
- Restaurant name, description, logo
- Hours of operation
- Delivery zones/areas
- Minimum order value
- Delivery fee
- Payment methods (manual confirmation for now)
- Contact info (phone, email, website)
- Social media links
- Photos/gallery

**Status:** Not implemented for MVP. Hardcoded in food.blade.php.

---

## 5. Worker/Dispatch Flows

### 5.1 Delivery Assignment
**Flow:**
1. Order confirmed by restaurant admin → status `confirmed`
2. Order marked as ready for pickup → status `ready_for_pickup`
3. Dispatch center (manual or automated) assigns worker → OrderAssignment created
4. Worker receives notification (via app, push, SMS)

### 5.2 Delivery Execution (Existing)
**Worker app flow (already implemented in `/worker/orders`):**
1. ✅ See list of assigned orders
2. ✅ Accept order (status → `accepted`)
3. ✅ Navigate to restaurant
4. ✅ Mark "Arrived at pickup" (status → `at_pickup`)
5. ✅ Mark "Picked up" (photo, status → `picked_up`)
6. ✅ Navigate to customer address
7. ✅ Mark "Arrived at delivery" (status → `at_delivery`)
8. ✅ Deliver and photograph (status → `completed`)

**Success:** This flow already works. GLF MaT orders feed into it automatically.

---

## 6. Payment Readiness

### Current State (MVP)
- ✅ Manual order confirmation (no automated payment)
- ✅ Orders tracked in system
- ✅ Manual payment collection (cash at pickup or customer pays restaurant directly)

### Payment Provider Status
- ⚠️ Vipps configured in system
- ⚠️ NOT wired to GLF MaT delivery form
- ⚠️ Manual approval needed before enabling online payment

### Honest Communication to Customer
- Form shows: "Manual confirmation. Payment can be arranged with restaurant or cash at delivery."
- No fake "Payment processed" message
- No fake payment gateway redirect

---

## 7. Operational Honesty Checklist

| Item | Rule | Implementation |
|------|------|-----------------|
| **Fake ratings** | ❌ Not allowed | Only show reviews if real data exists; otherwise "Reviews coming after launch" |
| **Fake reviews** | ❌ Not allowed | Current 4 reviews are **sample data labeled as such**, not stored in DB |
| **Fake delivery status** | ❌ Not allowed | Show actual worker status or "Not yet assigned" |
| **Fake ETA** | ❌ Not allowed | Show estimated range (40-60 min) or "To be confirmed by restaurant" |
| **Fake booking confirmation** | ❌ Not allowed | Show "Pending confirmation" until admin approves |
| **Fake customer count** | ❌ Not allowed | Don't show "4,800 satisfied customers" unless real data in DB |
| **Fake restaurant partnership** | ❌ Not allowed | This is a pilot demo. Proposal doc comes later. |
| **Fake availability** | ✅ OK for now | All menu items always available = OK temporary state |

---

## 8. Acceptance Criteria (Definition of Done)

### Public Page
- [ ] `/services/food` loads correctly (desktop + mobile)
- [ ] All menu sections render
- [ ] Delivery form submits to `/services/delivery.meals/request`
- [ ] Order appears in admin dashboard
- [ ] Confirmation page shows real order number
- [ ] Booking form (Option A: submits and stores OR Option B: labeled as manual-only)
- [ ] No broken images
- [ ] No console errors

### Admin Module
- [ ] Filament dashboard loads for partner role
- [ ] Orders list shows real orders filtered by `partner='glf-mat'`
- [ ] Order detail view displays correctly
- [ ] Confirm/Reject actions update order status
- [ ] Worker assignment flow connected (order assigned = worker sees it)

### Worker Flow
- [ ] Worker sees GLF MaT orders in `/worker/orders`
- [ ] Can accept → navigate → pickup → deliver → complete

### Documentation
- [ ] Phase 1 Product Contract ✅
- [ ] Phase 2 Public Page Design specs ✅
- [ ] Phase 3-8 checklist ✅

### Security & Data
- [ ] No secrets exposed in code
- [ ] No .env modifications
- [ ] No fake pre-seeded orders
- [ ] Phone/address encrypted in Order model (verify existing setup)
- [ ] CSRF protection working

### Performance
- [ ] Page load time < 3s (desktop)
- [ ] Mobile responsive
- [ ] No N+1 queries in admin list

---

## 9. Exclusions (Out of Scope for MVP)

- ❌ Multi-language admin panel (English OK)
- ❌ Menu image uploads (CSS art OK for now)
- ❌ Online payment integration (manual only)
- ❌ SMS/Push notifications (UI only)
- ❌ Live tracking map (placeholder OK)
- ❌ Customer reviews submission (hardcoded samples)
- ❌ Loyalty points / rewards program
- ❌ Partner onboarding wizard
- ❌ Kitchen display system (KDS)
- ❌ Real restaurant hours logic

---

## 10. Success Metrics (How We Know It Works)

1. **Delivery Order Flow** ✅
   - Customer submits delivery form
   - Order appears in admin dashboard
   - Admin confirms order
   - Worker receives assignment
   - Worker completes delivery
   - Order status updates end-to-end

2. **Admin Usability** ✅
   - Partner staff can see incoming orders
   - Can confirm/reject with one click
   - Can see worker assignment status

3. **No Fake States** ✅
   - All displayed information matches database
   - No hardcoded "success" messages for unconfirmed actions
   - Manual steps clearly labeled as manual

4. **Brand Integrity** ✅
   - GLF MaT branding consistent (colors, logo, cuisine theme)
   - Premium look & feel (matching reference screenshot)
   - No Wolt/Foodora/competitor branding copied

---

## 11. Partner Pitch Summary

**When showing to GLF MaT owner:**

> "Here's what BiKuBe can do for you:
>
> 1. **Your customers** visit this page, see your menu, order food or book a table
> 2. **Your staff** log into our admin panel, see all orders coming in, confirm them one by one
> 3. **Our delivery workers** get assigned your orders and deliver them — you don't manage couriers
> 4. **You keep running** your restaurant — we handle the platform, the workers, the delivery coordination
> 5. **Manual confirmation** for now — you're in control. As we grow, we can add payment, automations, analytics
>
> This is a working pilot. Your customers are real, orders are real, workers are real. Let's test it together."

---

## 12. Next Phases Roadmap

- **Phase 2:** Wire delivery form, create booking endpoint, improve form UX
- **Phase 3:** Build admin resources, test order flow end-to-end
- **Phase 4:** Research Filament plugins, optional: calendar booking plugin
- **Phase 5:** Validation checklist, screenshots, logs
- **Phase 6:** Partner pitch pack (sales/demo document in Russian)
- **Phase 7:** Browser UAT, verify forms, take screenshots
- **Phase 8:** Commit to git with summary report

---

## Sign-Off

**Prepared by:** Agent Valera (Paperclip Task CMPAAA-141)  
**Date:** 2026-06-18  
**Owner approval required before proceeding to Phase 2**

**Questions:**
- [ ] Booking form: Option A (store in Order) or Option B (UI-only manual)?
- [ ] Menu data: Keep hardcoded in food.blade.php or create temporary storage?
- [ ] Payment: Keep manual confirmation or prepare Vipps integration plan?
