<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="BiKuBe Levering — dagligvarer, mat og tunge varer levert i Narvik og Ballangen.">
  <link rel="canonical" href="{{ url()->current() }}">
  <title>Levering — BiKuBe</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('storage/delivery-template/style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/theme-palette.css') }}">
  <script>window.BKB_THEME_SURFACE='public'</script>
  <script src="{{ asset('js/theme-palette.js') }}" defer></script>
</head>
<body>
<div class="delivery-page">

  {{-- ─── TOP NAV ──────────────────────────────────────────────── --}}
  <header class="top-nav">
    <div class="container nav-inner">
      <a href="{{ url('/') }}" class="brand">
        <i class="fa-solid fa-bicycle"></i>
        <span>BiKu<span>Be</span><small>Levering i Narvik</small></span>
      </a>
      <nav class="nav-links">
        <a class="active" href="{{ route('public.categories.delivery') }}">Levering</a>
        <a href="{{ route('public.orders.request', 'delivery.groceries') }}">Dagligvarer</a>
        <a href="{{ route('public.orders.request', 'delivery.meals') }}">Mat</a>
        <a href="{{ route('public.orders.request', 'delivery.bulky') }}">Tunge varer</a>
        <a href="{{ route('public.workers.apply') }}">Bli sjåfør</a>
      </nav>
      <div class="nav-actions">
        @auth
          <a href="{{ route('account.dashboard') }}" class="btn login">Min konto</a>
        @else
          <a href="{{ route('login') }}" class="btn login">Logg inn</a>
        @endauth
        <a href="{{ route('public.orders.request', 'delivery.groceries') }}" class="btn signup">Bestill nå</a>
      </div>
    </div>
  </header>

  {{-- ─── HERO ────────────────────────────────────────────────── --}}
  <section class="hero">
    <div class="container">
      <div class="hero-shell">
        <div class="hero-left">
          <h1>Levering av alt,<br><span>du trenger</span></h1>
          <p class="hero-sub">Dagligvarer, ferdigmat og store gjenstander levert trygt og raskt til hjemmet ditt i Narvik og Ballangen.</p>
          <ul class="hero-features">
            <li><i class="fa-solid fa-check"></i> Lokal levering — ekte sjåfører</li>
            <li><i class="fa-solid fa-check"></i> Bestill enkelt — ingen app nødvendig</li>
            <li><i class="fa-solid fa-check"></i> Sporbar via kontokabinett</li>
          </ul>
          <div class="card-strip">
            <a class="strip-card strip-g" href="{{ route('public.orders.request', 'delivery.groceries') }}">
              <img src="{{ asset('storage/delivery-template/images/bananas.jpg') }}" alt="Dagligvarer">
              <div><h3>Dagligvarer</h3><p>fra butikker</p></div>
            </a>
            <a class="strip-card strip-o" href="{{ route('public.orders.request', 'delivery.meals') }}">
              <img src="{{ asset('storage/delivery-template/images/milk.jpg') }}" alt="Ferdigmat">
              <div><h3>Ferdigmat</h3><p>fra restauranter</p></div>
            </a>
            <a class="strip-card strip-p" href="{{ route('public.orders.request', 'delivery.bulky') }}">
              <img src="{{ asset('storage/delivery-template/images/hero.jpg') }}" alt="Tunge varer">
              <div><h3>Tunge varer</h3><p>stor leveranse</p></div>
            </a>
          </div>
        </div>
        <div class="hero-right">
          <div class="hero-badge">Narvik<small>Pilotlansering</small></div>
        </div>
      </div>

      {{-- Metrics --}}
      <div class="metrics">
        <div class="metric">
          <i class="fa-solid fa-location-dot"></i>
          <div><b>2 byer</b><span>Narvik og Ballangen</span></div>
        </div>
        <div class="metric">
          <i class="fa-solid fa-truck-fast"></i>
          <div><b>3 typer</b><span>leveringstjenester</span></div>
        </div>
        <div class="metric">
          <i class="fa-solid fa-shield-halved"></i>
          <div><b>Ekte</b><span>sjåfører og lokal drift</span></div>
        </div>
        <div class="metric">
          <i class="fa-solid fa-star"></i>
          <div><b>Pilot</b><span>ærlig lansering 2026</span></div>
        </div>
      </div>
    </div>
  </section>

  {{-- ─── HOW IT WORKS ────────────────────────────────────────── --}}
  <section class="section">
    <div class="container">
      <div class="section-title">
        <h2>Slik fungerer det</h2>
      </div>
      <div class="promo-grid">
        <div class="promo pg" style="flex-direction:column;align-items:flex-start;gap:.5rem;min-height:130px">
          <div style="display:flex;align-items:center;gap:.75rem">
            <span style="display:grid;place-items:center;width:2.4rem;height:2.4rem;border-radius:50%;background:rgba(139,227,58,.18);color:var(--green);font-weight:900;font-size:1.1rem;flex-shrink:0">1</span>
            <h3 style="margin:0;font-size:1.25rem">Velg leveringstype</h3>
          </div>
          <p style="margin:0 0 0 3.15rem;color:#c4d3e8;font-size:.95rem">Dagligvarer, mat eller store gjenstander — klikk på ønsket tjeneste.</p>
        </div>
        <div class="promo po" style="flex-direction:column;align-items:flex-start;gap:.5rem;min-height:130px">
          <div style="display:flex;align-items:center;gap:.75rem">
            <span style="display:grid;place-items:center;width:2.4rem;height:2.4rem;border-radius:50%;background:rgba(245,189,84,.18);color:#f5bd54;font-weight:900;font-size:1.1rem;flex-shrink:0">2</span>
            <h3 style="margin:0;font-size:1.25rem">Fyll ut bestillingen</h3>
          </div>
          <p style="margin:0 0 0 3.15rem;color:#c4d3e8;font-size:.95rem">Hentested, leveringsadresse, ønsket tidspunkt og kontaktinfo.</p>
        </div>
        <div class="promo pp" style="flex-direction:column;align-items:flex-start;gap:.5rem;min-height:130px">
          <div style="display:flex;align-items:center;gap:.75rem">
            <span style="display:grid;place-items:center;width:2.4rem;height:2.4rem;border-radius:50%;background:rgba(139,58,227,.18);color:#bf7bff;font-weight:900;font-size:1.1rem;flex-shrink:0">3</span>
            <h3 style="margin:0;font-size:1.25rem">Dispatcher bekrefter</h3>
          </div>
          <p style="margin:0 0 0 3.15rem;color:#c4d3e8;font-size:.95rem">Operasjonsteamet gjennomgår og tildeler en lokal sjåfør.</p>
        </div>
      </div>
      <div style="margin-top:12px;border-radius:14px;border:1px solid #2a3b56;padding:14px 18px;background:linear-gradient(140deg,#0f3826,#09271b);display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
        <span style="display:grid;place-items:center;width:2.4rem;height:2.4rem;flex-shrink:0;border-radius:50%;background:rgba(139,227,58,.18);color:var(--green);font-weight:900;font-size:1.1rem">4</span>
        <div style="flex:1;min-width:200px">
          <h3 style="margin:0 0 .2rem;font-size:1.15rem">Sjåfør leverer — du bekrefter</h3>
          <p style="margin:0;color:#c4d3e8;font-size:.9rem">Sjåføren laster opp leveringsbevis. Du bekrefter mottak i kontokabinett.</p>
        </div>
        <a href="{{ route('public.orders.request', 'delivery.groceries') }}"
           style="flex-shrink:0;border-radius:999px;padding:12px 22px;background:var(--green);color:#172a0a;font-weight:700;text-decoration:none;white-space:nowrap">
          Bestill nå
        </a>
      </div>
    </div>
  </section>

  {{-- ─── SERVICE CARDS ───────────────────────────────────────── --}}
  <section class="section">
    <div class="container">
      <div class="section-title">
        <h2>Velg leveringstjeneste</h2>
      </div>
      <div class="products-row" style="grid-template-columns:repeat(3,1fr);gap:14px">

        <article class="product-card" style="padding:0;overflow:hidden;display:flex;flex-direction:column">
          <img src="{{ asset('storage/delivery-template/images/bananas.jpg') }}" alt="Dagligvarer" style="height:180px;border-radius:0">
          <div style="padding:16px;flex:1;display:flex;flex-direction:column">
            <div class="product-name" style="font-size:1.25rem;min-height:auto">Dagligvarer</div>
            <div class="product-meta" style="margin-top:.35rem">Hent fra Rema, Coop, SPAR og lokale butikker i Narvik</div>
            <div style="margin-top:auto;padding-top:1rem">
              <div class="price-row" style="margin-bottom:.75rem">
                <span class="price" style="font-size:1.3rem">fra 149 NOK</span>
              </div>
              <a href="{{ route('public.orders.request', 'delivery.groceries') }}"
                 class="buy" style="display:flex;align-items:center;justify-content:center;gap:.5rem;text-decoration:none">
                <i class="fa-solid fa-basket-shopping"></i> Bestill dagligvarer
              </a>
            </div>
          </div>
        </article>

        <article class="product-card" style="padding:0;overflow:hidden;display:flex;flex-direction:column">
          <img src="{{ asset('storage/delivery-template/images/milk.jpg') }}" alt="Ferdigmat" style="height:180px;border-radius:0">
          <div style="padding:16px;flex:1;display:flex;flex-direction:column">
            <div class="product-name" style="font-size:1.25rem;min-height:auto">Ferdigmat</div>
            <div class="product-meta" style="margin-top:.35rem">Hent fra restauranter og take-away-steder i Narvik</div>
            <div style="margin-top:auto;padding-top:1rem">
              <div class="price-row" style="margin-bottom:.75rem">
                <span class="price" style="font-size:1.3rem">fra 99 NOK</span>
              </div>
              <a href="{{ route('public.orders.request', 'delivery.meals') }}"
                 class="buy" style="display:flex;align-items:center;justify-content:center;gap:.5rem;text-decoration:none">
                <i class="fa-solid fa-utensils"></i> Bestill mat
              </a>
            </div>
          </div>
        </article>

        <article class="product-card" style="padding:0;overflow:hidden;display:flex;flex-direction:column">
          <img src="{{ asset('storage/delivery-template/images/hero.jpg') }}" alt="Tunge varer" style="height:180px;border-radius:0">
          <div style="padding:16px;flex:1;display:flex;flex-direction:column">
            <div class="product-name" style="font-size:1.25rem;min-height:auto">Tunge varer</div>
            <div class="product-meta" style="margin-top:.35rem">Møbler, hvitevarer og store gjenstander hentet og levert</div>
            <div style="margin-top:auto;padding-top:1rem">
              <div class="price-row" style="margin-bottom:.75rem">
                <span class="price" style="font-size:1.3rem">fra 599 NOK</span>
              </div>
              <a href="{{ route('public.orders.request', 'delivery.bulky') }}"
                 class="buy" style="display:flex;align-items:center;justify-content:center;gap:.5rem;text-decoration:none">
                <i class="fa-solid fa-truck"></i> Bestill stor leveranse
              </a>
            </div>
          </div>
        </article>

      </div>

      <div class="promo-grid" style="margin-top:14px">
        <div class="promo pg">
          <div><h3>Narvik</h3><p>Alle bydeler — sentrum, Ankenes, Håkvik</p></div>
          <img src="{{ asset('storage/delivery-template/images/avocado.jpg') }}" alt="">
        </div>
        <div class="promo po">
          <div><h3>Ballangen</h3><p>Lokale leveranser — hele kommunen</p></div>
          <img src="{{ asset('storage/delivery-template/images/chips.jpg') }}" alt="">
        </div>
        <div class="promo pp">
          <div><h3>Betaling</h3><p>Vipps MobilePay — kommer snart</p></div>
          <img src="{{ asset('storage/delivery-template/images/bananas.jpg') }}" alt="">
        </div>
      </div>
    </div>
  </section>

  {{-- ─── LOCAL STORES ────────────────────────────────────────── --}}
  <section class="section">
    <div class="container">
      <div class="section-title"><h2>Lokale hentesteder</h2></div>
      <div class="store-grid">
        <div class="store"><b>Rema 1000</b><span>Narvik</span></div>
        <div class="store"><b>Coop Extra</b><span>Narvik</span></div>
        <div class="store"><b>SPAR</b><span>Narvik</span></div>
        <div class="store"><b>Kiwi</b><span>Narvik</span></div>
        <div class="store"><b>Coop Prix</b><span>Ballangen</span></div>
        <div class="store"><b>Rema 1000</b><span>Ballangen</span></div>
        <div class="store" style="border:1px dashed #3a5070;background:transparent"><b style="color:#7a93b0">Din butikk?</b><span>Ta kontakt</span></div>
        <div class="store" style="border:1px dashed #3a5070;background:transparent"><b style="color:#7a93b0">Restaurant?</b><span>Bli partner</span></div>
      </div>
      <p style="margin-top:10px;color:#7a93b0;font-size:13px">
        ⚠️ Butikkpartnere er under oppsett for pilotlansering. Hentested beskrives i bestillingsskjemaet.
      </p>
    </div>
  </section>

  {{-- ─── COLLECTIONS / USE CASES ────────────────────────────── --}}
  <section class="section">
    <div class="container">
      <div class="section-title"><h2>Eksempler på bestillinger</h2></div>
      <div class="collect-grid">
        <div class="collect">
          <img src="{{ asset('storage/delivery-template/images/bananas.jpg') }}" alt="Dagligvarer">
          <div class="txt"><b>Ukeshandel</b><p>Alt fra én butikk</p></div>
        </div>
        <div class="collect">
          <img src="{{ asset('storage/delivery-template/images/milk.jpg') }}" alt="Mat">
          <div class="txt"><b>Fredagsmiddag</b><p>Fra favorittrestauranten</p></div>
        </div>
        <div class="collect">
          <img src="{{ asset('storage/delivery-template/images/hero.jpg') }}" alt="Tunge varer">
          <div class="txt"><b>Nytt TV-møbel</b><p>Hentet og levert</p></div>
        </div>
        <div class="collect">
          <img src="{{ asset('storage/delivery-template/images/avocado.jpg') }}" alt="Helse">
          <div class="txt"><b>Sunn lunsj</b><p>Rask levering på dagtid</p></div>
        </div>
        <div class="collect">
          <img src="{{ asset('storage/delivery-template/images/chips.jpg') }}" alt="Snacks">
          <div class="txt"><b>Filmkveld</b><p>Snacks og drikke</p></div>
        </div>
      </div>

      <div class="feature-grid">
        <div class="feature"><i class="fa-solid fa-lock"></i><span>Sikker betaling — Vipps snart</span></div>
        <div class="feature"><i class="fa-solid fa-box"></i><span>Forsiktig håndtering</span></div>
        <div class="feature"><i class="fa-solid fa-headset"></i><span>Lokal support</span></div>
        <div class="feature"><i class="fa-solid fa-map-location-dot"></i><span>GPS-sporing etter henting</span></div>
      </div>
    </div>
  </section>

  {{-- ─── READINESS ───────────────────────────────────────────── --}}
  <section class="section">
    <div class="container">
      <div style="border:1px solid #2e4a6a;border-radius:16px;background:linear-gradient(145deg,#07192e,#040f1e);padding:24px">
        <h2 style="margin:0 0 6px;font-size:1.6rem">Hva er klart nå?</h2>
        <p style="margin:0 0 18px;color:#9db3cb;font-size:.95rem">BiKuBe er i pilotfase. Vi viser kun ærlig status.</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px">
          <div style="border:1px solid rgba(139,227,58,.3);border-radius:12px;background:rgba(20,60,25,.55);padding:14px 16px">
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem">
              <i class="fa-solid fa-circle-check" style="color:var(--green)"></i>
              <b style="font-size:.9rem">Bestilling</b>
            </div>
            <p style="margin:0;color:#b5d4b0;font-size:.82rem">Skjema fungerer — ordre opprettes i systemet umiddelbart.</p>
          </div>
          <div style="border:1px solid rgba(245,189,84,.3);border-radius:12px;background:rgba(60,42,10,.55);padding:14px 16px">
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem">
              <i class="fa-solid fa-clock" style="color:#f5bd54"></i>
              <b style="font-size:.9rem">Betaling</b>
            </div>
            <p style="margin:0;color:#d4c890;font-size:.82rem">Vipps MobilePay kobles snart. Nå: manuell fakturering etter levering.</p>
          </div>
          <div style="border:1px solid rgba(245,189,84,.3);border-radius:12px;background:rgba(60,42,10,.55);padding:14px 16px">
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem">
              <i class="fa-solid fa-location-dot" style="color:#f5bd54"></i>
              <b style="font-size:.9rem">GPS-sporing</b>
            </div>
            <p style="margin:0;color:#d4c890;font-size:.82rem">Tilgjengelig etter at sjåføren aksepterer og gir nettleser-tillatelse.</p>
          </div>
          <div style="border:1px solid rgba(139,227,58,.3);border-radius:12px;background:rgba(20,60,25,.55);padding:14px 16px">
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem">
              <i class="fa-solid fa-circle-check" style="color:var(--green)"></i>
              <b style="font-size:.9rem">Leveringsbevis</b>
            </div>
            <p style="margin:0;color:#b5d4b0;font-size:.82rem">Sjåfør laster opp bevis. Du bekrefter mottak direkte.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- ─── BOTTOM CTA ──────────────────────────────────────────── --}}
  <section class="section">
    <div class="container">
      <div style="text-align:center;border:1px solid #243a59;border-radius:20px;background:linear-gradient(145deg,#0b2035,#060e1c);padding:48px 24px">
        <p style="margin:0 0 8px;color:var(--green);font-size:.75rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase">Klar til å bestille?</p>
        <h2 style="margin:0 0 12px;font-size:clamp(1.6rem,4vw,2.8rem);font-weight:900">Start din leveringsbestilling</h2>
        <p style="margin:0 0 28px;color:#9db3cb;font-size:1rem;max-width:40rem;margin-left:auto;margin-right:auto">
          Lokal levering i Narvik og Ballangen. Ekte sjåfører, ærlig pris, ingen skjulte kostnader.
        </p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
          <a href="{{ route('public.orders.request', 'delivery.groceries') }}"
             style="border-radius:999px;padding:14px 32px;background:var(--green);color:#172a0a;font-weight:700;font-size:1.05rem;text-decoration:none">
            <i class="fa-solid fa-basket-shopping" style="margin-right:.5rem"></i>Bestill levering
          </a>
          @auth
            <a href="{{ route('account.orders.index') }}"
               style="border-radius:999px;padding:14px 32px;border:1px solid #3a5a7a;color:#d0e4f6;font-weight:600;text-decoration:none">
              <i class="fa-solid fa-box-open" style="margin-right:.5rem"></i>Mine bestillinger
            </a>
          @else
            <a href="{{ route('login') }}"
               style="border-radius:999px;padding:14px 32px;border:1px solid #3a5a7a;color:#d0e4f6;font-weight:600;text-decoration:none">
              <i class="fa-solid fa-user" style="margin-right:.5rem"></i>Spor eksisterende ordre
            </a>
          @endauth
        </div>
        <p style="margin:18px 0 0;color:#5a7a9a;font-size:.82rem">
          <i class="fa-solid fa-triangle-exclamation" style="margin-right:.35rem;color:#f5bd54"></i>
          Nettbetaling er ikke tilkoblet ennå. Ordre bekreftes manuelt av operasjonsteamet.
        </p>
      </div>
    </div>
  </section>

  {{-- ─── FOOTER ──────────────────────────────────────────────── --}}
  <footer class="footer">
    <div class="container footer-grid">
      <div>
        <div class="brand" style="font-size:28px;margin-bottom:10px">
          <i class="fa-solid fa-bicycle"></i>
          <span>BiKu<span>Be</span></span>
        </div>
        <p>Lokal leveringstjeneste for Narvik og Ballangen. Ekte sjåfører, ærlige priser.</p>
      </div>
      <div>
        <h4>Tjenester</h4>
        <ul>
          <li><a href="{{ route('public.orders.request', 'delivery.groceries') }}">Dagligvarer</a></li>
          <li><a href="{{ route('public.orders.request', 'delivery.meals') }}">Ferdigmat</a></li>
          <li><a href="{{ route('public.orders.request', 'delivery.bulky') }}">Tunge varer</a></li>
        </ul>
      </div>
      <div>
        <h4>Kunder</h4>
        <ul>
          <li><a href="{{ route('public.orders.request', 'delivery.groceries') }}">Slik bestiller du</a></li>
          @auth
            <li><a href="{{ route('account.orders.index') }}">Mine bestillinger</a></li>
            <li><a href="{{ route('account.support.create') }}">Kontakt support</a></li>
          @else
            <li><a href="{{ route('login') }}">Logg inn</a></li>
            <li><a href="{{ route('register') }}">Opprett konto</a></li>
          @endauth
        </ul>
      </div>
      <div>
        <h4>Partnere</h4>
        <ul>
          <li><a href="{{ route('public.workers.apply') }}">Bli sjåfør</a></li>
          <li><a href="{{ url('/') }}">Om BiKuBe</a></li>
        </ul>
      </div>
      <div>
        <h4>Dekning</h4>
        <ul>
          <li>Narvik</li>
          <li>Ballangen</li>
          <li><a href="{{ route('account.support.create') }}">support@bikube.no</a></li>
        </ul>
      </div>
    </div>
    <div class="container footer-bottom">
      <span>© {{ date('Y') }} BiKuBe. Alle rettigheter forbeholdt.</span>
      <span>Pilotlansering Narvik / Ballangen</span>
    </div>
  </footer>

</div>

<script src="{{ asset('storage/delivery-template/script.js') }}"></script>
<script>
(function() {
  var safe = function(id, fn) { var el = document.getElementById(id); if (el) fn(el); };
  safe('prodPrev', function(){});
  safe('prodNext', function(){});
  safe('colPrev', function(){});
  safe('colNext', function(){});
  document.querySelectorAll('#productTabs .tab').forEach(function(t) {
    t.addEventListener('click', function() {
      document.querySelectorAll('#productTabs .tab').forEach(function(x) { x.classList.remove('active'); });
      t.classList.add('active');
    });
  });
})();
</script>
</body>
</html>
