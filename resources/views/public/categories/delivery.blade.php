<!DOCTYPE html>
<html lang="nb">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>BiKuBe Delivery — Narvik</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>
[x-cloak]{display:none!important}
*,*::before,*::after{box-sizing:border-box}
html,body{margin:0;padding:0;font-family:'Inter',sans-serif}
.delivery-page{min-height:100vh;width:100%;overflow-x:hidden;background:#020713;color:#f8fafc}
.delivery-page a{text-decoration:none}
/* ── HERO ── */
.delivery-hero{position:relative;isolation:isolate;min-height:clamp(720px,78svh,820px);width:100%;overflow:hidden;background:#03070b}
.delivery-hero::before{content:"";position:absolute;inset:0 auto 0 0;z-index:1;width:min(760px,64vw);background:linear-gradient(90deg,rgba(2,6,23,.98) 0%,rgba(2,6,23,.92) 48%,rgba(2,6,23,.18) 100%);pointer-events:none}
.delivery-slide{position:absolute;inset:0;z-index:0;background-size:cover;background-position:center;transform:scale(1.01)}
.delivery-slide::after{content:"";position:absolute;inset:auto 0 0;height:34%;background:linear-gradient(180deg,transparent,#020713 94%)}
/* ── NAV ── */
.delivery-nav{position:relative;z-index:3;display:flex;align-items:center;justify-content:space-between;gap:24px;width:min(1760px,calc(100vw - 56px));margin:0 auto;padding-top:22px}
.delivery-brand{font-size:24px;font-weight:900;color:#fff;display:flex;align-items:center;gap:8px}
.delivery-nav__links{display:flex;align-items:center;gap:24px;color:#cbd5e1;font-size:13px;font-weight:800}
.delivery-nav__links a{color:#cbd5e1;transition:color .18s}
.delivery-nav__links a:hover,.delivery-nav__links a.active{color:#d9f99d}
.delivery-nav__actions{display:flex;align-items:center;gap:10px}
/* ── BUTTONS ── */
.delivery-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:42px;border-radius:15px;border:1px solid rgba(148,163,184,.35);padding:11px 18px;color:#f8fafc;font-size:13px;font-weight:950;cursor:pointer;transition:transform .18s,border-color .18s,box-shadow .18s,color .18s;text-decoration:none;background:transparent}
.delivery-btn:hover{transform:translateY(-2px)}
.delivery-btn--primary{border-color:rgba(217,249,157,.88);background:linear-gradient(145deg,#e4ff68 0%,#a3e635 48%,#65a30d 100%);color:#07110a;box-shadow:0 14px 34px rgba(132,204,22,.34),inset 0 1px 0 rgba(255,255,255,.52)}
.delivery-btn--ghost{background:rgba(2,6,23,.42);backdrop-filter:blur(12px)}
.delivery-btn--soft{border-color:rgba(163,230,53,.48);background:rgba(132,204,22,.12);color:#d9f99d}
.delivery-btn--large{min-height:50px;padding:14px 22px;font-size:14px}
/* ── CART FAB ── */
.delivery-cart{position:relative;display:grid;width:42px;height:42px;place-items:center;border:1px solid rgba(163,230,53,.46);border-radius:999px;background:rgba(15,23,42,.58);box-shadow:inset 0 1px 0 rgba(255,255,255,.14),0 14px 24px rgba(2,6,23,.32)}
/* ── HERO COPY ── */
.delivery-hero__copy{position:relative;z-index:2;width:min(1760px,calc(100vw - 56px));margin:0 auto;padding-top:clamp(78px,9vw,130px);max-width:1760px}
.delivery-hero__copy > *{max-width:650px}
.delivery-eyebrow{display:inline-flex;width:auto;margin:0 0 17px;border:1px solid rgba(163,230,53,.42);border-radius:999px;background:rgba(132,204,22,.12);padding:7px 12px;color:#d9f99d;font-size:12px;font-weight:950;text-transform:uppercase}
.delivery-hero h1{margin:0;color:#fff;font-size:clamp(46px,5.8vw,88px);line-height:.95;font-weight:1000;letter-spacing:0;text-wrap:balance;text-shadow:0 18px 48px rgba(0,0,0,.46)}
.delivery-lead{margin:22px 0 0;color:#dbeafe;font-size:clamp(16px,1.35vw,20px);line-height:1.6;text-shadow:0 10px 28px rgba(0,0,0,.6)}
.delivery-hero-benefits{display:grid;gap:8px;margin:18px 0 0;padding:0;list-style:none}
.delivery-hero-benefits li{display:flex;align-items:center;gap:8px;color:#e2f8c0;font-size:14px;font-weight:800}
.delivery-hero-benefits li::before{content:"";width:10px;height:10px;border-radius:999px;background:#a3e635;box-shadow:0 0 12px rgba(163,230,53,.8)}
.delivery-cta-row{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px}
/* ── SLIDER CONTROLS ── */
.delivery-slider-control{position:absolute;right:max(28px,calc((100vw - 1760px) / 2));bottom:20px;z-index:3;display:flex;align-items:center;gap:12px;color:#e2e8f0;font-weight:900}
.delivery-slider-control b{color:#bef264}
.delivery-slider-control button{width:44px;height:44px;border:1px solid rgba(163,230,53,.44);border-radius:999px;background:rgba(2,6,23,.48);color:#f8fafc;font-size:25px;line-height:1;backdrop-filter:blur(12px);cursor:pointer;transition:transform .18s,border-color .18s,box-shadow .18s}
.delivery-slider-control button:hover{transform:translateY(-2px);border-color:rgba(217,249,157,.85);box-shadow:0 14px 30px rgba(132,204,22,.22)}
.delivery-slider-dots{position:absolute;left:max(28px,calc((100vw - 1760px) / 2));bottom:28px;z-index:3;display:flex;gap:8px}
.delivery-slider-dots button{width:34px;height:4px;border:0;border-radius:999px;background:rgba(226,232,240,.32);cursor:pointer}
.delivery-slider-dots button.is-active{background:#bef264;box-shadow:0 0 18px rgba(132,204,22,.7)}
/* ── SHELL / LAYOUT ── */
.delivery-shell{width:min(1760px,calc(100vw - 56px));margin:0 auto;padding:0 0 34px}
/* ── SEGMENTS ── */
.delivery-segments{position:relative;z-index:5;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-top:-58px}
.delivery-segment{position:relative;min-height:170px;overflow:hidden;border:1px solid rgba(217,249,157,.24);border-radius:24px;background:#0f172a center/cover no-repeat;color:#fff;cursor:pointer;text-align:left;box-shadow:0 26px 58px rgba(0,0,0,.34);transition:transform .22s,border-color .22s,box-shadow .22s}
.delivery-segment:hover{transform:translateY(-4px)}
.delivery-segment.segment-products.is-active,.delivery-segment.segment-products:hover{border-color:#bef264;box-shadow:0 0 24px rgba(163,230,53,.45)}
.delivery-segment.segment-meals.is-active,.delivery-segment.segment-meals:hover{border-color:#f97316;box-shadow:0 0 24px rgba(249,115,22,.45)}
.delivery-segment.segment-bulky.is-active,.delivery-segment.segment-bulky:hover{border-color:#a855f7;box-shadow:0 0 24px rgba(168,85,247,.45)}
.delivery-segment::before{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(2,6,23,.05),rgba(2,6,23,.70))}
.delivery-segment span{position:absolute;left:22px;right:22px;bottom:20px;z-index:1}
.delivery-segment strong{display:block;font-size:24px;line-height:1.08;font-weight:1000;text-shadow:0 8px 22px rgba(0,0,0,.74)}
.delivery-segment.segment-products strong::after{content:"→";float:right;color:#bef264;font-size:22px;font-weight:900}
.delivery-segment.segment-meals strong::after{content:"→";float:right;color:#fdba74;font-size:22px;font-weight:900}
.delivery-segment.segment-bulky strong::after{content:"→";float:right;color:#c084fc;font-size:22px;font-weight:900}
.delivery-segment small{display:block;margin-top:7px;color:#ecfccb;font-size:13px;font-weight:800;text-shadow:0 6px 18px rgba(0,0,0,.72)}
/* ── SECTIONS ── */
.delivery-section{margin-top:28px}
.delivery-heading{display:flex;align-items:end;justify-content:space-between;gap:16px;margin-bottom:16px}
.delivery-heading h2{margin:0;color:#fff;font-size:clamp(24px,2vw,34px);font-weight:1000}
.delivery-heading p{margin:5px 0 0;color:#a8b6c9;font-size:13px}
/* ── TABS ── */
.delivery-tabs{display:flex;flex-wrap:wrap;gap:8px}
.delivery-tab{border:1px solid rgba(148,163,184,.22);border-radius:999px;background:rgba(15,23,42,.72);color:#cbd5e1;padding:9px 14px;font-size:12px;font-weight:900;cursor:pointer}
.delivery-tab.is-active{border-color:rgba(190,242,100,.86);background:#a3e635;color:#07110a}
/* ── STATS & BENEFIT STRIP ── */
.delivery-stats,.delivery-benefit-strip{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));overflow:hidden;border:1px solid rgba(148,163,184,.20);border-radius:26px;background:linear-gradient(180deg,rgba(4,11,20,.94),rgba(2,6,23,.86));box-shadow:inset 0 1px 0 rgba(255,255,255,.06),0 20px 46px rgba(0,0,0,.20)}
.delivery-stat,.delivery-benefit{display:flex;align-items:center;justify-content:center;gap:15px;min-height:92px;padding:18px 20px;border-right:1px solid rgba(148,163,184,.14)}
.delivery-stat:last-child,.delivery-benefit:last-child{border-right:0}
.delivery-icon{position:relative;display:grid;width:44px;height:44px;flex:0 0 44px;place-items:center;color:#a3e635}
.delivery-icon::before{content:"";position:absolute;inset:0;border:1px solid rgba(132,204,22,.50);border-radius:14px;background:radial-gradient(circle at 50% 34%,rgba(132,204,22,.18),rgba(132,204,22,.05));box-shadow:inset 0 0 18px rgba(132,204,22,.10),0 0 18px rgba(132,204,22,.10)}
.delivery-icon svg{position:relative;z-index:1;width:23px;height:23px;stroke-width:1.9}
.delivery-stat__body,.delivery-benefit__body{min-width:128px;max-width:190px;text-align:left}
.delivery-stat strong{display:block;color:#fff;font-size:24px;line-height:1;font-weight:1000}
.delivery-stat span span,.delivery-benefit span{display:block;margin-top:5px;color:#a5b4c7;font-size:12px;line-height:1.28}
.delivery-benefit strong{display:block;color:#fff;font-size:16px;line-height:1.1;font-weight:1000}
/* ── PRODUCTS ── */
.delivery-products{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:13px}
.delivery-product-card{position:relative;display:flex;min-height:320px;flex-direction:column;border:1px solid rgba(148,163,184,.24);border-radius:20px;background:linear-gradient(180deg,#172437,#0a1421);padding:10px;box-shadow:0 18px 40px rgba(0,0,0,.24);transition:transform .2s,border-color .2s,box-shadow .2s}
.delivery-product-card:hover{transform:translateY(-5px);border-color:rgba(163,230,53,.48);box-shadow:0 24px 48px rgba(0,0,0,.34),0 0 20px rgba(132,204,22,.12)}
.delivery-product-card__badge{position:absolute;top:11px;left:11px;z-index:1;border-radius:999px;background:#a3e635;color:#07110a;padding:4px 8px;font-size:11px;font-weight:950}
.delivery-product-card img{width:100%;aspect-ratio:1.28;object-fit:cover;border-radius:16px;background:#111827}
.delivery-product-card h3{margin:11px 0 0;color:#fff;font-size:16px;font-weight:950}
.delivery-product-card p{min-height:30px;margin:4px 0 0;color:#d0deef;font-size:12px}
.delivery-product-card__price{display:flex;align-items:baseline;gap:8px;margin:8px 0 12px}
.delivery-product-card__price strong{color:#bef264;font-size:17px}
.delivery-product-card__price span{color:#93a4bc;font-size:12px;text-decoration:line-through}
.delivery-product-card button{margin-top:auto;display:flex;justify-content:center;width:100%;border:0;border-radius:12px;background:linear-gradient(140deg,#bef264,#84cc16 56%,#65a30d);color:#07110a;padding:11px 12px;font-size:13px;font-weight:950;cursor:pointer;transition:opacity .18s}
.delivery-product-card button:hover{opacity:.88}
/* ── PROMOS ── */
.delivery-promos{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.delivery-promo{position:relative;min-height:182px;overflow:hidden;border:1px solid rgba(148,163,184,.24);border-radius:24px;background:#111827 center/cover no-repeat;box-shadow:0 18px 42px rgba(0,0,0,.32);cursor:pointer}
.delivery-promo::before{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(2,6,23,.10),rgba(2,6,23,.68))}
.delivery-promo span{position:absolute;left:16px;right:16px;bottom:15px;z-index:1;color:#fff;font-size:18px;font-weight:1000;text-shadow:0 8px 22px rgba(0,0,0,.78)}
.delivery-promo small{display:block;margin-top:5px;color:#d9f99d;font-size:12px;font-weight:850}
/* ── STORES MARQUEE ── */
.delivery-stores-marquee{position:relative;overflow:hidden;border:1px solid rgba(148,163,184,.18);border-radius:24px;background:linear-gradient(180deg,rgba(4,11,20,.90),rgba(2,6,23,.78));padding:14px 0}
.delivery-stores-track{display:flex;width:max-content;gap:12px;animation:deliveryMarquee 34s linear infinite}
.delivery-stores-marquee:hover .delivery-stores-track{animation-play-state:paused}
.delivery-store{display:grid;grid-template-columns:92px 1fr auto;align-items:center;gap:12px;width:330px;margin-left:12px;border:1px solid rgba(255,255,255,.10);border-radius:18px;background:rgba(15,23,42,.72);padding:12px;color:#fff;box-shadow:inset 0 1px 0 rgba(255,255,255,.05)}
.delivery-store__logo{display:grid;height:58px;place-items:center;overflow:hidden;border-radius:12px;background:rgba(255,255,255,.96)}
.delivery-store__logo img{max-width:82px;max-height:48px;object-fit:contain}
.delivery-store h3{margin:0;color:#fff;font-size:14px;font-weight:950}
.delivery-store p{margin:5px 0 0;color:#b3c5dd;font-size:12px}
.delivery-store a,.delivery-store button{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border:0;border-radius:999px;background:#a3e635;color:#07110a;font-weight:1000;cursor:pointer;flex-shrink:0}
/* ── FOOTER ── */
.delivery-footer{display:grid;grid-template-columns:1.45fr repeat(4,minmax(0,1fr));gap:26px;margin-top:34px;border-top:1px solid rgba(148,163,184,.18);padding:28px 0 16px;color:#94a3b8}
.delivery-footer h3,.delivery-footer h4{margin:0 0 10px;color:#fff;font-size:14px;font-weight:950}
.delivery-footer p,.delivery-footer a{display:block;margin:0 0 8px;color:#94a3b8;font-size:12px;line-height:1.55}
.delivery-footer a:hover{color:#d9f99d}
.delivery-footer__brand img{width:136px;margin-bottom:12px;filter:brightness(0) invert(1)}
.delivery-footer__bottom{grid-column:1/-1;display:flex;flex-wrap:wrap;justify-content:space-between;gap:14px;border-top:1px solid rgba(148,163,184,.14);padding-top:16px;color:#64748b;font-size:11px}
/* ── FAB CART ── */
.delivery-fab-cart{position:fixed;right:20px;bottom:20px;z-index:70;display:inline-flex;align-items:center;gap:8px;border:1px solid rgba(190,242,100,.8);border-radius:999px;background:linear-gradient(140deg,#bef264,#84cc16 56%,#65a30d);color:#07110a;padding:11px 14px;font-size:13px;font-weight:950;box-shadow:0 18px 34px rgba(0,0,0,.36);cursor:pointer}
.delivery-fab-cart span:last-child{display:inline-grid;min-width:20px;height:20px;place-items:center;border-radius:999px;background:#07110a;color:#d9f99d;font-size:11px;font-weight:900;padding:0 4px}
/* ── CART DRAWER ── */
.delivery-drawer-backdrop{position:fixed;inset:0;z-index:80;background:rgba(2,6,23,.68);backdrop-filter:blur(2px)}
.delivery-drawer{position:fixed;top:0;right:0;z-index:90;width:min(430px,96vw);height:100vh;display:grid;grid-template-rows:auto 1fr auto;border-left:1px solid rgba(148,163,184,.3);background:linear-gradient(180deg,#081225,#040a16);box-shadow:-24px 0 48px rgba(0,0,0,.48)}
.delivery-drawer__head{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 16px;border-bottom:1px solid rgba(148,163,184,.2)}
.delivery-drawer__head h3{margin:0;font-size:19px;font-weight:950;color:#f8fafc}
.delivery-drawer__close{display:grid;width:36px;height:36px;place-items:center;border:1px solid rgba(148,163,184,.36);border-radius:10px;background:rgba(15,23,42,.72);color:#e2e8f0;font-size:18px;cursor:pointer}
.delivery-drawer__body{overflow:auto;padding:12px 16px}
.delivery-drawer__empty{margin:18px 0;border:1px dashed rgba(148,163,184,.35);border-radius:14px;padding:18px;text-align:center;color:#cbd5e1;font-size:14px}
.delivery-drawer__foot{padding:12px 16px 16px;border-top:1px solid rgba(148,163,184,.2);background:rgba(2,6,23,.62)}
.delivery-cart-list{display:grid;gap:8px}
.delivery-cart-item{display:grid;grid-template-columns:56px 1fr auto;gap:10px;align-items:center;border:1px solid rgba(148,163,184,.2);border-radius:14px;padding:8px;background:rgba(2,6,23,.45)}
.delivery-cart-item img{width:56px;height:56px;object-fit:cover;border-radius:10px;background:#0b1320}
.delivery-cart-item h4{margin:0;font-size:13px;font-weight:900;color:#fff}
.delivery-cart-item p{margin:2px 0 0;font-size:12px;color:#bfd0e6}
.delivery-cart-qty{display:flex;align-items:center;gap:4px;margin-top:6px}
.delivery-cart-qty button{width:28px;height:28px;border:1px solid rgba(148,163,184,.35);border-radius:8px;background:#0f172a;color:#e2e8f0;font-weight:900;cursor:pointer}
.delivery-cart-qty span{min-width:22px;text-align:center;font-size:12px;color:#e2e8f0;font-weight:800}
.delivery-cart-item .remove-btn{border:1px solid rgba(251,113,133,.44);border-radius:8px;background:rgba(159,18,57,.24);color:#fecdd3;padding:5px 8px;font-size:11px;font-weight:800;cursor:pointer}
.delivery-cart-foot{margin-top:12px;border-top:1px solid rgba(148,163,184,.2);padding-top:12px;display:grid;gap:8px}
.delivery-cart-actions{display:flex;flex-wrap:wrap;gap:8px}
.delivery-cart-actions button,.delivery-cart-actions a{display:inline-flex;align-items:center;justify-content:center;min-height:40px;border-radius:10px;padding:8px 14px;font-size:13px;font-weight:900;text-decoration:none;cursor:pointer}
.delivery-cart-actions .clear-btn{border:1px solid rgba(148,163,184,.4);background:transparent;color:#e2e8f0}
.delivery-cart-actions .checkout-btn{border:1px solid rgba(190,242,100,.9);background:linear-gradient(145deg,#e4ff68,#84cc16);color:#07110a}
@keyframes deliveryMarquee{from{transform:translateX(0)}to{transform:translateX(-50%)}}
/* ── RESPONSIVE ── */
@media(max-width:1180px){
  .delivery-products{grid-template-columns:repeat(3,minmax(0,1fr))}
  .delivery-footer{grid-template-columns:repeat(2,minmax(0,1fr))}
}
@media(max-width:820px){
  .delivery-nav,.delivery-hero__copy,.delivery-shell{width:min(100% - 28px,1760px)}
  .delivery-hero{min-height:680px}
  .delivery-hero::before{width:100%;background:linear-gradient(90deg,rgba(2,6,23,.96) 0%,rgba(2,6,23,.74) 56%,rgba(2,6,23,.20) 100%)}
  .delivery-nav__links,.delivery-btn--ghost,.delivery-nav__actions .delivery-btn--primary{display:none}
  .delivery-hero h1{font-size:clamp(36px,10.4vw,46px);line-height:1.02}
  .delivery-lead{font-size:14px;line-height:1.55}
  .delivery-segments{grid-template-columns:1fr;margin-top:-36px}
  .delivery-stats,.delivery-benefit-strip{grid-template-columns:repeat(2,minmax(0,1fr))}
  .delivery-stat:nth-child(2),.delivery-benefit:nth-child(2){border-right:0}
  .delivery-heading{align-items:flex-start;flex-direction:column}
  .delivery-products,.delivery-promos{grid-template-columns:1fr}
  .delivery-footer{grid-template-columns:1fr}
}
@media(max-width:480px){
  .delivery-hero{min-height:640px}
  .delivery-cta-row{flex-direction:column;align-items:stretch}
  .delivery-stats,.delivery-benefit-strip{grid-template-columns:1fr}
  .delivery-stat,.delivery-benefit{justify-content:flex-start;border-right:0;border-bottom:1px solid rgba(148,163,184,.14)}
  .delivery-stat:last-child,.delivery-benefit:last-child{border-bottom:0}
  .delivery-store{width:286px;grid-template-columns:74px 1fr auto}
  .delivery-fab-cart{right:12px;bottom:12px}
}
</style>
</head>
<body>
@php
$orderRouteBase = route('public.orders.request', 'delivery.groceries');
$page = [
    'default_segment' => 'products',
    'segments' => [
        'products' => [
            'key' => 'products',
            'label' => 'Products Delivery',
            'subtitle' => 'Groceries from Norwegian stores',
            'eyebrow' => 'BiKuBe Grocery',
            'description' => 'Dagligvarer, husholdningsprodukter og daglige essensielle varer med nøysom pakking og live ordrestatus.',
            'cta_url' => route('public.orders.request', 'delivery.groceries'),
            'accent' => 'green',
            'category_image' => asset('images/bikube/delivery/category-products.png'),
            'slides' => [
                ['eyebrow'=>'BiKuBe Levering','title'=>'Ferske varer, pakket med omsorg','image'=>asset('images/bikube/delivery/segments/groceries/1.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Levering','title'=>'Alt for uken i én bestilling','image'=>asset('images/bikube/delivery/segments/groceries/2.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Levering','title'=>'Rask lokal levering','image'=>asset('images/bikube/delivery/segments/groceries/3.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Levering','title'=>'Ferskvare alltid synlig','image'=>asset('images/bikube/delivery/segments/groceries/4.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Levering','title'=>'Daglige varer, én kasse','image'=>asset('images/bikube/delivery/segments/groceries/5.png'),'active'=>true],
            ],
            'products' => [
                ['title'=>'Bananer','subtitle'=>'1 kg','price'=>'129 NOK','old_price'=>'152 NOK','badge'=>'-15%','image'=>asset('images/bikube/delivery/products-real/bananas.png')],
                ['title'=>'Avokado Hass','subtitle'=>'2 stk','price'=>'189 NOK','old_price'=>'210 NOK','badge'=>'-10%','image'=>asset('images/bikube/delivery/products-real/avocado.png')],
                ['title'=>'Parmalat Melk','subtitle'=>'1,5 l','price'=>'89 NOK','old_price'=>'97 NOK','badge'=>'Hit','image'=>asset('images/bikube/delivery/products-real/milk.png')],
                ['title'=>'Cherrytomater','subtitle'=>'250 g','price'=>'119 NOK','old_price'=>null,'badge'=>'+6%','image'=>asset('images/bikube/delivery/products-real/tomatoes.png')],
                ['title'=>'Gresk Yoghurt','subtitle'=>'500 g','price'=>'99 NOK','old_price'=>null,'badge'=>'Fersk','image'=>asset('images/bikube/delivery/products-real/milk.png')],
                ['title'=>"Lay's Chips",'subtitle'=>'150 g','price'=>'129 NOK','old_price'=>'147 NOK','badge'=>'-12%','image'=>asset('images/bikube/delivery/products-real/chips.png')],
            ],
            'promos' => [
                ['title'=>'Gratis levering','subtitle'=>'For første dagligvarebestilling','image'=>asset('images/bikube/delivery/promo-baner2.png')],
                ['title'=>'Sunn kurv','subtitle'=>'Grønnsaker, meieri og snacks','image'=>asset('images/bikube/delivery/segments/groceries/2.png')],
                ['title'=>'Familiens ukeshandel','subtitle'=>'Én bestilling for hele uken','image'=>asset('images/bikube/delivery/segments/groceries/5.png')],
            ],
            'stores' => [
                ['name'=>'MENY','logo'=>asset('images/bikube/delivery/stores/meny.png'),'rating'=>'4.9','eta'=>'30 min'],
                ['name'=>'KIWI','logo'=>asset('images/bikube/delivery/stores/kiwi.jpg'),'rating'=>'4.8','eta'=>'30 min'],
                ['name'=>'REMA 1000','logo'=>asset('images/bikube/delivery/stores/rema1000.svg'),'rating'=>'4.8','eta'=>'35 min'],
                ['name'=>'Coop Mega','logo'=>asset('images/bikube/delivery/stores/coopmega.svg'),'rating'=>'4.7','eta'=>'35 min'],
                ['name'=>'SPAR','logo'=>asset('images/bikube/delivery/stores/spar.svg'),'rating'=>'4.9','eta'=>'40 min'],
                ['name'=>'Joker','logo'=>asset('images/bikube/delivery/stores/joker.svg'),'rating'=>'4.7','eta'=>'40 min'],
            ],
        ],
        'meals' => [
            'key' => 'meals',
            'label' => 'Ready Meals',
            'subtitle' => 'Hot food from restaurants',
            'eyebrow' => 'BiKuBe Mat',
            'description' => 'Varm mat, restaurantmåltider og takeaway med klar ETA og support.',
            'cta_url' => route('public.orders.request', 'delivery.meals'),
            'accent' => 'amber',
            'category_image' => asset('images/bikube/delivery/category-meals.png'),
            'slides' => [
                ['eyebrow'=>'BiKuBe Levering','title'=>'Varm restaurantmat, levert','image'=>asset('images/bikube/delivery/segments/meals/1.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Levering','title'=>'Middag uten ventetid','image'=>asset('images/bikube/delivery/segments/meals/2.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Levering','title'=>'Lunsj, sushi, grill og bowls','image'=>asset('images/bikube/delivery/segments/meals/3.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Levering','title'=>'Fersk fra partnerkjøkken','image'=>asset('images/bikube/delivery/segments/meals/4.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Levering','title'=>'Restaurantlevering i én kasse','image'=>asset('images/bikube/delivery/segments/meals/5.png'),'active'=>true],
            ],
            'products' => [
                ['title'=>'Laksebowl','subtitle'=>'Ferdig varm','price'=>'229 NOK','old_price'=>null,'badge'=>'Kokkens valg','image'=>asset('images/bikube/delivery/products-real/salmon-bowl.png')],
                ['title'=>'Sushisett','subtitle'=>'18 biter','price'=>'289 NOK','old_price'=>null,'badge'=>'Populær','image'=>asset('images/bikube/delivery/products-real/sushi-set.png')],
                ['title'=>'Pasta Carbonara','subtitle'=>'Porsjon','price'=>'189 NOK','old_price'=>null,'badge'=>'Varm','image'=>asset('images/bikube/delivery/products-real/pasta.png')],
                ['title'=>'Kyllinggrill','subtitle'=>'Restaurantmåltid','price'=>'249 NOK','old_price'=>null,'badge'=>'Hit','image'=>asset('images/bikube/delivery/products-real/grill.png')],
                ['title'=>'Tom Yum Suppe','subtitle'=>'500 ml','price'=>'169 NOK','old_price'=>null,'badge'=>'Ny','image'=>asset('images/bikube/delivery/products-real/sushi-set.png')],
                ['title'=>'Burger Combo','subtitle'=>'Menyvalg','price'=>'199 NOK','old_price'=>null,'badge'=>'Rask','image'=>asset('images/bikube/delivery/products-real/grill.png')],
            ],
            'promos' => [
                ['title'=>'Middag i kveld','subtitle'=>'Ferdigmat og restaurantmat','image'=>asset('images/bikube/delivery/segments/meals/1.png')],
                ['title'=>'Kokkens utvalg','subtitle'=>'Utvalgte varme retter i nærheten','image'=>asset('images/bikube/delivery/segments/meals/3.png')],
                ['title'=>'Rask lunsj','subtitle'=>'Måltider med klar ETA','image'=>asset('images/bikube/delivery/segments/meals/4.png')],
            ],
            'stores' => [
                ['name'=>'Narvik Kitchen','logo'=>asset('images/bikube/delivery/segments/meals/1.png'),'rating'=>'4.9','eta'=>'25 min'],
                ['name'=>'Sushi Partner','logo'=>asset('images/bikube/delivery/segments/meals/2.png'),'rating'=>'4.8','eta'=>'30 min'],
                ['name'=>'Grill House','logo'=>asset('images/bikube/delivery/segments/meals/3.png'),'rating'=>'4.7','eta'=>'30 min'],
                ['name'=>'Cafe Route','logo'=>asset('images/bikube/delivery/segments/meals/4.png'),'rating'=>'4.8','eta'=>'20 min'],
                ['name'=>'Dinner Hub','logo'=>asset('images/bikube/delivery/segments/meals/5.png'),'rating'=>'4.9','eta'=>'35 min'],
            ],
        ],
        'bulky' => [
            'key' => 'bulky',
            'label' => 'Bulky Delivery',
            'subtitle' => 'Large goods and home items',
            'eyebrow' => 'BiKuBe Cargo',
            'description' => 'Møbler, hvitevarer og store gjenstander med håndtering, planlagte slots og kundebekreftelse.',
            'cta_url' => route('public.orders.request', 'delivery.bulky'),
            'accent' => 'violet',
            'category_image' => asset('images/bikube/delivery/category-bulky.png'),
            'slides' => [
                ['eyebrow'=>'BiKuBe Cargo','title'=>'Storlevering av hjemmeartikler','image'=>asset('images/bikube/delivery/segments/bulky/1.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Cargo','title'=>'To-person innbæringshjelp','image'=>asset('images/bikube/delivery/segments/bulky/2.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Cargo','title'=>'Hvitevarer, bokser og møbler','image'=>asset('images/bikube/delivery/segments/bulky/3.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Cargo','title'=>'Planlagte slots for tunge bestillinger','image'=>asset('images/bikube/delivery/segments/bulky/4.png'),'active'=>true],
                ['eyebrow'=>'BiKuBe Cargo','title'=>'Hjemmelogistikk i én kasse','image'=>asset('images/bikube/delivery/segments/bulky/5.png'),'active'=>true],
            ],
            'products' => [
                ['title'=>'Sofalevering','subtitle'=>'2-persons team','price'=>'690 NOK','old_price'=>null,'badge'=>'Stor','image'=>asset('images/bikube/delivery/products-real/sofa.png')],
                ['title'=>'Vaskemaskin','subtitle'=>'Løft + installasjon','price'=>'790 NOK','old_price'=>null,'badge'=>'Service','image'=>asset('images/bikube/delivery/products-real/washer.png')],
                ['title'=>'Sengeramme','subtitle'=>'Med innbæring','price'=>'620 NOK','old_price'=>null,'badge'=>'Hjem','image'=>asset('images/bikube/delivery/products-real/home-boxes.png')],
                ['title'=>'TV 65"','subtitle'=>'Skjømmsom håndtering','price'=>'540 NOK','old_price'=>null,'badge'=>'Omsorg','image'=>asset('images/bikube/delivery/products-real/washer.png')],
                ['title'=>'Flyttebokser','subtitle'=>'Bulktransport','price'=>'460 NOK','old_price'=>null,'badge'=>'Flytt','image'=>asset('images/bikube/delivery/products-real/home-boxes.png')],
                ['title'=>'Kontorstol','subtitle'=>'Montert henting','price'=>'390 NOK','old_price'=>null,'badge'=>'Express','image'=>asset('images/bikube/delivery/products-real/sofa.png')],
            ],
            'promos' => [
                ['title'=>'Hjemmeomsorg','subtitle'=>'Store varer og bulksupport','image'=>asset('images/bikube/delivery/segments/bulky/1.png')],
                ['title'=>'Hvitvare-rute','subtitle'=>'Innbæring og oppsettbestilling','image'=>asset('images/bikube/delivery/segments/bulky/3.png')],
                ['title'=>'Kontorflytt','subtitle'=>'Bokser, skrivebord og utstyr','image'=>asset('images/bikube/delivery/segments/bulky/5.png')],
            ],
            'stores' => [
                ['name'=>'Home Partner','logo'=>asset('images/bikube/delivery/segments/bulky/1.png'),'rating'=>'4.8','eta'=>'same day'],
                ['name'=>'Appliance Hub','logo'=>asset('images/bikube/delivery/segments/bulky/2.png'),'rating'=>'4.7','eta'=>'slot'],
                ['name'=>'Furniture Route','logo'=>asset('images/bikube/delivery/segments/bulky/3.png'),'rating'=>'4.9','eta'=>'planlagt'],
                ['name'=>'Cargo Team','logo'=>asset('images/bikube/delivery/segments/bulky/4.png'),'rating'=>'4.8','eta'=>'slot'],
                ['name'=>'Office Supply','logo'=>asset('images/bikube/delivery/segments/bulky/6.png'),'rating'=>'4.7','eta'=>'planlagt'],
            ],
        ],
    ],
    'stats' => [
        ['value'=>'10 000+','label'=>'items available','icon'=>'bag'],
        ['value'=>'200+','label'=>'stores and partners','icon'=>'store'],
        ['value'=>'30 min','label'=>'average dispatch','icon'=>'clock'],
        ['value'=>'4.9','label'=>'service rating','icon'=>'star'],
    ],
    'benefits' => [
        ['title'=>'Secure payment','subtitle'=>'Protected checkout','icon'=>'lock'],
        ['title'=>'Careful packing','subtitle'=>'Freshness preserved','icon'=>'gift'],
        ['title'=>'Support 24/7','subtitle'=>'Always online','icon'=>'phone'],
        ['title'=>'Bonuses and offers','subtitle'=>'Useful promotions','icon'=>'spark'],
    ],
];
@endphp

<section class="delivery-page"
    x-data="deliveryCommercePage(@js($page))"
    x-init="init()"
    @keydown.escape.window="closeCartDrawer()">

    {{-- ── HERO SLIDER ── --}}
    <section class="delivery-hero"
        @mouseenter="pauseSlider()"
        @mouseleave="resumeSlider()"
        @touchstart.passive="touchStart = $event.changedTouches[0].clientX"
        @touchend.passive="handleSwipe($event.changedTouches[0].clientX)">

        <template x-for="(slide, i) in currentSlides" :key="`${activeSegment}-${i}`">
            <article
                x-show="activeSlide === i"
                x-transition.opacity.duration.500ms
                class="delivery-slide"
                :style="`background-image: linear-gradient(90deg, rgba(2,6,23,.96) 0%, rgba(2,6,23,.78) 34%, rgba(2,6,23,.16) 63%, rgba(2,6,23,.64) 100%), url('${slide.image}')`"
            ></article>
        </template>

        <header class="delivery-nav">
            <a href="{{ route('public.categories.delivery') }}" class="delivery-brand" aria-label="BiKuBe Delivery">
                <i class="fa-solid fa-bag-shopping" style="color:#84cc16"></i>
                <span>BiKuBe <span style="color:#84cc16">Delivery</span></span>
            </a>
            <nav class="delivery-nav__links" aria-label="Delivery sections">
                <a class="active" href="#" style="display:inline-flex;align-items:center;gap:6px;color:#84cc16">
                    <i class="fa-solid fa-location-dot"></i> Narvik
                </a>
                <a href="#popular-products" @click.prevent="setSegment('products')">Produkter</a>
                <a href="#popular-products" @click.prevent="setSegment('meals')">Ferdigmat</a>
                <a href="#popular-products" @click.prevent="setSegment('bulky')">Stor leveranse</a>
                <a href="#delivery-support">Support</a>
            </nav>
            <div class="delivery-nav__actions">
                @auth
                    <a class="delivery-btn delivery-btn--ghost" href="{{ route('account.dashboard') }}">Konto</a>
                @else
                    <a class="delivery-btn delivery-btn--ghost" href="{{ route('login') }}">Logg inn</a>
                @endauth
                <a class="delivery-btn delivery-btn--primary" href="{{ route('public.workers.apply') }}">Bli sjåfør</a>
            </div>
        </header>

        <div class="delivery-hero__copy">
            <p class="delivery-eyebrow" x-text="currentSlide.eyebrow || activeSegmentData.eyebrow"></p>
            <h1>Alt du trenger,<br>levert raskt</h1>
            <p class="delivery-lead">Dagligvarer, ferdigmat og store leveranser — raskt og trygt levert hjem.</p>
            <ul class="delivery-hero-benefits">
                <li>Rask bestilling</li>
                <li>Skånsom levering</li>
                <li>Sporing i sanntid</li>
            </ul>
            <div class="delivery-cta-row">
                <button type="button" class="delivery-btn delivery-btn--primary delivery-btn--large"
                    @click="goToOrder('delivery.groceries')">Bestill dagligvarer</button>
                <a class="delivery-btn delivery-btn--soft delivery-btn--large"
                    href="#popular-products">Se leveringsvalg</a>
            </div>
        </div>

        <div class="delivery-slider-control">
            <button type="button" @click="prevSlide()" aria-label="Forrige slide">&lsaquo;</button>
            <span><b x-text="String(activeSlide + 1).padStart(2,'0')"></b> / <span x-text="String(currentSlides.length||1).padStart(2,'0')"></span></span>
            <button type="button" @click="nextSlide()" aria-label="Neste slide">&rsaquo;</button>
        </div>
        <div class="delivery-slider-dots" aria-label="Slides">
            <template x-for="(slide, i) in currentSlides" :key="`dot-${activeSegment}-${i}`">
                <button type="button" @click="setSlide(i)"
                    :class="{'is-active': activeSlide === i}"
                    :aria-label="`Slide ${i + 1}`"></button>
            </template>
        </div>
    </section>

    <main class="delivery-shell">

        {{-- ── SEGMENTS ── --}}
        <section class="delivery-segments" aria-label="Delivery segments">
            <template x-for="segment in segmentList" :key="segment.key">
                <button type="button" class="delivery-segment"
                    :class="{
                        'is-active': activeSegment === segment.key,
                        'segment-products': segment.key === 'products',
                        'segment-meals': segment.key === 'meals',
                        'segment-bulky': segment.key === 'bulky'
                    }"
                    :style="`background-image: url('${segment.category_image || currentSlide.image}')`"
                    @click="setSegment(segment.key); document.getElementById('popular-products').scrollIntoView({behavior:'smooth'})">
                    <span>
                        <strong x-text="segment.label"></strong>
                        <small x-text="segment.subtitle"></small>
                    </span>
                </button>
            </template>
        </section>

        {{-- ── STATS ── --}}
        <section class="delivery-section delivery-stats">
            <template x-for="stat in page.stats || []" :key="stat.label">
                <div class="delivery-stat">
                    <span class="delivery-icon" x-html="iconSvg(stat.icon)"></span>
                    <span class="delivery-stat__body">
                        <strong x-text="stat.value"></strong>
                        <span x-text="stat.label"></span>
                    </span>
                </div>
            </template>
        </section>

        {{-- ── PRODUCTS ── --}}
        <section id="popular-products" class="delivery-section">
            <div class="delivery-heading">
                <div>
                    <h2 x-text="activeSegment === 'meals' ? 'Populære måltider' : (activeSegment === 'bulky' ? 'Populære storleveringer' : 'Populære produkter')"></h2>
                    <p x-text="activeSegmentData.description"></p>
                </div>
                <div class="delivery-tabs">
                    <template x-for="segment in segmentList" :key="`tab-${segment.key}`">
                        <button type="button" class="delivery-tab"
                            :class="{'is-active': activeSegment === segment.key}"
                            @click="setSegment(segment.key)"
                            x-text="segment.label"></button>
                    </template>
                </div>
            </div>
            <div class="delivery-products">
                <template x-for="product in activeProducts" :key="product.title">
                    <article class="delivery-product-card">
                        <span class="delivery-product-card__badge" x-text="product.badge"></span>
                        <img :src="product.image" :alt="product.title" loading="lazy">
                        <h3 x-text="product.title"></h3>
                        <p x-text="product.subtitle"></p>
                        <div class="delivery-product-card__price">
                            <strong x-text="product.price"></strong>
                            <span x-show="product.old_price" x-text="product.old_price"></span>
                        </div>
                        <button type="button" @click="addToCart(product)">Legg til</button>
                    </article>
                </template>
            </div>
        </section>

        {{-- ── PROMOS ── --}}
        <section class="delivery-section delivery-promos">
            <template x-for="promo in activePromos" :key="promo.title">
                <div class="delivery-promo"
                    :style="`background-image: url('${promo.image}')`"
                    @click="goToOrder(resolveScenario(activeSegment))">
                    <span>
                        <span x-text="promo.title"></span>
                        <small x-text="promo.subtitle"></small>
                    </span>
                </div>
            </template>
        </section>

        {{-- ── STORES ── --}}
        <section id="stores-and-partners" class="delivery-section">
            <div class="delivery-heading">
                <div>
                    <h2>Butikker og partnere</h2>
                    <p x-text="activeSegment === 'meals' ? 'Kjøkkenpartnere for hurtig måltidslevering.' : (activeSegment === 'bulky' ? 'Partnere for store varer og planlagt levering.' : 'Lokale butikker med rask hjemlevering.')"></p>
                </div>
                <button type="button" class="delivery-btn delivery-btn--soft"
                    @click="goToOrder(resolveScenario(activeSegment))">Åpne valg</button>
            </div>
            <div class="delivery-stores-marquee">
                <div class="delivery-stores-track">
                    <template x-for="(store, idx) in marqueeStores" :key="`${store.name}-${idx}`">
                        <article class="delivery-store">
                            <span class="delivery-store__logo"><img :src="store.logo" :alt="store.name" loading="lazy"></span>
                            <span>
                                <h3 x-text="store.name"></h3>
                                <p><span x-text="store.rating"></span> rating · <span x-text="store.eta"></span></p>
                            </span>
                            <button type="button" @click="goToOrder(resolveScenario(activeSegment))" aria-label="Åpne">→</button>
                        </article>
                    </template>
                </div>
            </div>
        </section>

        {{-- ── BENEFIT STRIP ── --}}
        <section id="delivery-support" class="delivery-section delivery-benefit-strip">
            <template x-for="benefit in page.benefits || []" :key="benefit.title">
                <div class="delivery-benefit">
                    <span class="delivery-icon" x-html="iconSvg(benefit.icon)"></span>
                    <span class="delivery-benefit__body">
                        <strong x-text="benefit.title"></strong>
                        <span x-text="benefit.subtitle"></span>
                    </span>
                </div>
            </template>
        </section>

        {{-- ── FOOTER ── --}}
        <footer class="delivery-footer">
            <div class="delivery-footer__brand">
                <img src="{{ asset('images/bikube/delivery/logo-delivery.svg') }}" alt="BiKuBe Delivery">
                <p>Lokal levering av dagligvarer, ferdigmat og store varer i Narvik og omegn.</p>
            </div>
            <div>
                <h4>Tjenester</h4>
                <a href="#" @click.prevent="setSegment('products')">Products Delivery</a>
                <a href="#" @click.prevent="setSegment('meals')">Ready Meals</a>
                <a href="#" @click.prevent="setSegment('bulky')">Bulky Delivery</a>
            </div>
            <div>
                <h4>Kunder</h4>
                <a href="{{ route('account.dashboard') }}">Min konto</a>
                <a href="{{ route('account.orders.index') }}">Mine bestillinger</a>
                <a href="{{ route('account.support.index') }}">Support</a>
            </div>
            <div>
                <h4>Bli sjåfør</h4>
                <a href="{{ route('public.workers.apply') }}">Søk nå</a>
                <a href="{{ route('worker.dashboard') }}">Worker portal</a>
            </div>
            <div>
                <h4>Support 24/7</h4>
                <p>Bestilling, dispatch og leveringsstatus.</p>
                <a href="mailto:support@bikube.no">support@bikube.no</a>
            </div>
            <div class="delivery-footer__bottom">
                <span>© {{ date('Y') }} BiKuBe Delivery · Narvik</span>
                <span>Vilkår · Personvern · Driftstatus</span>
            </div>
        </footer>
    </main>

    {{-- ── FAB CART ── --}}
    <button type="button" class="delivery-fab-cart" @click="openCartDrawer()" aria-label="Åpne handlekurv">
        <span>Handlekurv</span>
        <span x-text="cartCount"></span>
    </button>

    {{-- ── CART DRAWER ── --}}
    <template x-if="isCartDrawerOpen">
        <div class="delivery-drawer-backdrop" @click="closeCartDrawer()"></div>
    </template>
    <aside class="delivery-drawer" x-show="isCartDrawerOpen" x-transition.opacity.duration.180ms>
        <header class="delivery-drawer__head">
            <h3>Handlekurv <span x-text="`(${cartCount})`"></span></h3>
            <button type="button" class="delivery-drawer__close" @click="closeCartDrawer()">×</button>
        </header>
        <div class="delivery-drawer__body">
            <template x-if="cart.length === 0">
                <div class="delivery-drawer__empty">Kurven er tom</div>
            </template>
            <div class="delivery-cart-list" x-show="cart.length > 0">
                <template x-for="item in cart" :key="item.cartKey">
                    <article class="delivery-cart-item">
                        <img :src="item.image" :alt="item.name">
                        <div>
                            <h4 x-text="item.name"></h4>
                            <p x-text="item.price"></p>
                            <div class="delivery-cart-qty">
                                <button type="button" @click="changeQty(item.cartKey, -1)">-</button>
                                <span x-text="item.qty"></span>
                                <button type="button" @click="changeQty(item.cartKey, 1)">+</button>
                            </div>
                        </div>
                        <button type="button" class="remove-btn" @click="removeItem(item.cartKey)">Fjern</button>
                    </article>
                </template>
            </div>
        </div>
        <footer class="delivery-drawer__foot">
            <div class="delivery-cart-foot">
                <p style="font-size:13px;color:#94a3b8;margin:0 0 8px">
                    Gå til bestillingsskjema for å fullføre leveransen.
                </p>
                <div class="delivery-cart-actions">
                    <button type="button" class="clear-btn" @click="clearCart()">Tøm kurv</button>
                    <a class="checkout-btn" :href="checkoutUrl">Bestill nå →</a>
                </div>
            </div>
        </footer>
    </aside>
</section>

<script>
window.deliveryCommercePage = function(page) {
    return {
        page: page || {},
        activeSegment: page.default_segment || 'products',
        activeSlide: 0,
        timer: null,
        touchStart: null,
        isCartDrawerOpen: false,
        cart: [],

        get checkoutUrl() {
            return '{{ url("services") }}/' + this.resolveScenario(this.activeSegment) + '/request';
        },
        get segmentList() { return Object.values(this.page.segments || {}); },
        get activeSegmentData() { return (this.page.segments || {})[this.activeSegment] || this.segmentList[0] || {}; },
        get currentSlides() { return (this.activeSegmentData.slides || []).filter(s => s.active !== false); },
        get currentSlide() { return this.currentSlides[this.activeSlide] || this.currentSlides[0] || {}; },
        get activeProducts() { return this.activeSegmentData.products || []; },
        get activePromos() { return this.activeSegmentData.promos || []; },
        get activeStores() { return this.activeSegmentData.stores || []; },
        get marqueeStores() { return [...this.activeStores, ...this.activeStores]; },
        get cartCount() { return this.cart.reduce((s, i) => s + i.qty, 0); },

        init() {
            if (!(this.page.segments || {})[this.activeSegment] && this.segmentList[0]) {
                this.activeSegment = this.segmentList[0].key;
            }
            this.resumeSlider();
        },
        setSegment(segment) {
            if (!(this.page.segments || {})[segment]) return;
            this.activeSegment = segment;
            this.activeSlide = 0;
            this.restartSlider();
        },
        setSlide(i) { this.activeSlide = i; this.restartSlider(); },
        nextSlide() { const t = this.currentSlides.length || 1; this.activeSlide = (this.activeSlide + 1) % t; },
        prevSlide() { const t = this.currentSlides.length || 1; this.activeSlide = (this.activeSlide - 1 + t) % t; },
        pauseSlider() { if (this.timer) { clearInterval(this.timer); this.timer = null; } },
        resumeSlider() { this.pauseSlider(); this.timer = setInterval(() => this.nextSlide(), 5600); },
        restartSlider() { this.resumeSlider(); },
        resolveScenario(seg) {
            if (seg === 'meals') return 'delivery.meals';
            if (seg === 'bulky') return 'delivery.bulky';
            return 'delivery.groceries';
        },
        goToOrder(scenario) {
            window.location.href = '{{ url("services") }}/' + scenario + '/request';
        },
        addToCart(product) {
            const key = this.activeSegment + ':' + (product.title || '');
            const existing = this.cart.find(i => i.cartKey === key);
            if (existing) { existing.qty++; } else {
                this.cart.push({ cartKey: key, name: product.title, price: product.price, image: product.image, qty: 1 });
            }
            this.isCartDrawerOpen = true;
            document.body.style.overflow = 'hidden';
        },
        changeQty(cartKey, delta) {
            const item = this.cart.find(i => i.cartKey === cartKey);
            if (!item) return;
            item.qty += delta;
            if (item.qty <= 0) this.removeItem(cartKey);
        },
        removeItem(cartKey) { this.cart = this.cart.filter(i => i.cartKey !== cartKey); },
        clearCart() { this.cart = []; },
        openCartDrawer() { this.isCartDrawerOpen = true; document.body.style.overflow = 'hidden'; },
        closeCartDrawer() { this.isCartDrawerOpen = false; document.body.style.overflow = ''; },
        handleSwipe(endX) {
            if (this.touchStart === null) return;
            const delta = endX - this.touchStart;
            this.touchStart = null;
            if (Math.abs(delta) < 42) return;
            delta < 0 ? this.nextSlide() : this.prevSlide();
            this.restartSlider();
        },
        iconSvg(icon) {
            const icons = {
                bag: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 8h14l-1.3 11.3a2 2 0 0 1-2 1.7H8.3a2 2 0 0 1-2-1.7L5 8Z"/><path d="M9 8a3 3 0 0 1 6 0"/><path d="M9 13h6"/></svg>',
                store: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 21V8l8-5 8 5v13"/><path d="M9 21v-7h6v7"/><path d="M7 10h.01M17 10h.01"/></svg>',
                clock: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 6v6l4 2"/><path d="M19 12a7 7 0 1 1-2.05-4.95"/><path d="M19 5v5h-5"/></svg>',
                star: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-3-5.6 3 1.1-6.2L3 9.6l6.2-.9L12 3Z"/></svg>',
                lock: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 11V8a5 5 0 0 1 10 0v3"/><path d="M6 11h12v10H6z"/><path d="M12 15v2"/></svg>',
                gift: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M20 12v9H4v-9"/><path d="M2 7h20v5H2z"/><path d="M12 7v14"/><path d="M12 7H8.5A2.5 2.5 0 1 1 11 4.5C11 6 12 7 12 7Z"/><path d="M12 7h3.5A2.5 2.5 0 1 0 13 4.5C13 6 12 7 12 7Z"/></svg>',
                phone: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.7.6 2.5a2 2 0 0 1-.4 2.1L8 9.6a16 16 0 0 0 6.4 6.4l1.3-1.3a2 2 0 0 1 2.1-.4c.8.3 1.6.5 2.5.6A2 2 0 0 1 22 16.9Z"/></svg>',
                spark: '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="m12 2 1.7 6.3L20 10l-6.3 1.7L12 18l-1.7-6.3L4 10l6.3-1.7L12 2Z"/><path d="m19 15 .8 2.8L22 19l-2.2.8L19 23l-.8-3.2L15 19l3.2-1.2L19 15Z"/></svg>',
            };
            return icons[icon] || icons.star;
        },
    };
};
</script>
</body>
</html>
