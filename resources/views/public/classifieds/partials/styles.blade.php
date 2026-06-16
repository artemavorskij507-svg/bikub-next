<style>
    .mk-page{--bg:#050c1a;--card:#0c182d;--line:#23344f;--muted:#9fb2cc;--txt:#f4f7fc;--accent:#d9ff2f;background:radial-gradient(circle at 75% 0%,#15335f 0,#081427 36%,#050c1a 72%);color:var(--txt);margin:-2rem -1rem 0;min-height:100vh}
    .mk-wrap{max-width:1240px;margin:0 auto;padding:0 20px}
    .mk-header{padding:18px 0 26px}
    .mk-topbar{display:flex;align-items:center;gap:24px;flex-wrap:wrap}
    .mk-logo{display:flex;align-items:center;gap:10px;color:#eaf2ff;text-decoration:none}
    .mk-logo-badge{width:42px;height:42px;border:2px solid var(--accent);border-radius:10px;display:grid;place-items:center;font-weight:900;color:var(--accent)}
    .mk-logo strong{display:block;font-size:26px;line-height:1}
    .mk-logo em{display:block;font-style:normal;color:var(--accent);font-size:19px;line-height:1.1}
    .mk-nav{display:flex;gap:20px;flex:1;min-width:240px;flex-wrap:wrap}
    .mk-nav a{color:#c9d7ea;text-decoration:none;font-weight:700;font-size:14px}
    .mk-actions{display:flex;gap:10px;flex-wrap:wrap}
    .mk-btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;border-radius:12px;border:1px solid transparent;text-decoration:none;font-weight:900}
    .mk-btn-ghost{border-color:var(--line);color:#dbe8f8;background:rgba(9,21,39,.65)}
    .mk-btn-primary{background:var(--accent);color:#152507;box-shadow:0 0 0 1px #dfff64 inset,0 8px 24px rgba(167,221,33,.24)}
    .mk-btn-search{min-height:58px}
    .mk-hero{margin-top:18px;border:1px solid var(--line);border-radius:24px;overflow:hidden;position:relative;background-image:linear-gradient(90deg,rgba(4,11,22,.88) 0%,rgba(4,11,22,.70) 42%,rgba(4,11,22,.28) 100%),url('{{ asset('images/bikube/home/scenario-classifieds.png') }}');background-size:cover;background-position:center;min-height:470px}
    .mk-hero-copy{padding:44px 38px 34px;max-width:680px;position:relative;z-index:2}
    .mk-location{display:inline-block;padding:8px 12px;border-radius:999px;border:1px solid #2e4468;color:#d8e6fb;font-size:13px;margin-bottom:18px}
    .mk-hero h1{font-size:clamp(34px,4.5vw,72px);line-height:1.03;margin:0 0 16px;font-weight:950;letter-spacing:-.02em}
    .mk-hero p{margin:0;color:#c4d4ea;max-width:580px;font-size:19px;line-height:1.55}
    .mk-kpis{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:24px}
    .mk-kpis article{border:1px solid #2a3e5f;border-radius:14px;padding:10px 12px;background:rgba(5,14,28,.72)}
    .mk-kpis strong{display:block;font-size:20px;color:var(--accent)}
    .mk-kpis span{display:block;color:#9fb2cc;font-size:12px}
    .mk-search-form{display:grid;grid-template-columns:2.1fr 1.2fr 1fr auto;gap:12px;border:1px solid var(--line);background:rgba(8,20,38,.88);padding:12px;border-radius:18px;margin-top:18px}
    .mk-input span{display:block;font-size:11px;color:#8ea4c2;margin-bottom:5px;text-transform:uppercase;letter-spacing:.05em;font-weight:900}
    .mk-input input,.mk-input select,.mk-form input,.mk-form select,.mk-form textarea{width:100%;border-radius:10px;border:1px solid #23344f;background:#081427;color:#edf4ff;padding:0 12px}
    .mk-input input,.mk-input select{height:40px}
    .mk-main{padding:24px 20px 38px}
    .mk-section{margin-bottom:24px}
    .mk-row-head{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:12px;flex-wrap:wrap}
    .mk-row-head h2{margin:0;font-size:clamp(24px,3vw,34px);font-weight:950}
    .mk-row-head a,.mk-row-head span{color:#d8ec97;text-decoration:none;font-weight:800}
    .mk-categories-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}
    .mk-category-card{display:block;border:1px solid var(--line);border-radius:16px;background:rgba(10,22,41,.92);padding:10px;color:#eef5ff;text-decoration:none}
    .mk-category-image{display:block;width:100%;aspect-ratio:16/10;border-radius:12px;background-size:cover;background-position:center}
    .mk-category-name{display:block;margin-top:8px;font-weight:900}
    .mk-category-count{display:block;color:#90a7c7;font-size:13px}
    .mk-layout{display:grid;grid-template-columns:280px 1fr;gap:16px}
    .mk-filters{border:1px solid var(--line);background:rgba(8,20,38,.92);border-radius:18px;padding:18px;height:max-content}
    .mk-filters h3{margin:0 0 8px;font-size:19px}
    .mk-filters p,.mk-filters li{color:#b9cbe3;line-height:1.55}
    .mk-filters ul{padding-left:18px;margin:12px 0 0}
    .mk-results{min-width:0}
    .mk-ads-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
    .mk-ads-grid-featured{margin-bottom:14px}
    .mk-ad-card{border:1px solid #2d3f5f;border-radius:16px;background:#0b172b;overflow:hidden;position:relative}
    .mk-ad-image{display:block;position:relative;aspect-ratio:4/3;background:#13233d;background-size:cover;background-position:center;text-decoration:none}
    .mk-ad-image span{position:absolute;top:10px;left:10px;border-radius:999px;background:var(--accent);color:#17220a;font-size:11px;font-weight:950;padding:5px 9px;text-transform:uppercase}
    .mk-ad-body{padding:13px}
    .mk-ad-title{display:block;color:#f5fbff;font-weight:900;text-decoration:none;line-height:1.25;margin-bottom:8px}
    .mk-ad-price{display:block;color:var(--accent);font-size:19px;margin-bottom:8px}
    .mk-ad-meta{display:flex;gap:8px;flex-wrap:wrap;color:#9fb2cc;font-size:12px}
    .mk-ad-meta span{border:1px solid #243754;border-radius:999px;padding:4px 8px}
    .mk-empty{grid-column:1/-1;border:1px dashed #385171;border-radius:18px;background:rgba(9,22,40,.78);padding:32px;text-align:center}
    .mk-empty h3{margin:0 0 8px;font-size:26px}
    .mk-empty p{color:#b9cbe3;margin:0 0 18px}
    .mk-pagination{margin-top:18px;color:#dbeafe}
    .mk-detail{display:grid;grid-template-columns:minmax(0,1fr) 360px;gap:18px}
    .mk-detail-card{border:1px solid var(--line);border-radius:20px;background:rgba(8,20,38,.90);padding:22px}
    .mk-detail h1{font-size:clamp(32px,4vw,58px);line-height:1.05;margin:0 0 12px}
    .mk-detail-price{font-size:32px;color:var(--accent);font-weight:950}
    .mk-form{display:grid;gap:14px;max-width:860px}
    .mk-form label span{display:block;margin-bottom:6px;color:#b9cbe3;font-weight:800}
    .mk-form input,.mk-form select{height:44px}
    .mk-form textarea{min-height:160px;padding-top:12px}
    @media (max-width:980px){.mk-search-form,.mk-layout,.mk-detail{grid-template-columns:1fr}.mk-categories-grid,.mk-ads-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (max-width:640px){.mk-page{margin:-1rem}.mk-hero{min-height:420px}.mk-hero-copy{padding:30px 20px}.mk-kpis,.mk-categories-grid,.mk-ads-grid{grid-template-columns:1fr}.mk-actions{width:100%}.mk-btn{width:100%}}
</style>
