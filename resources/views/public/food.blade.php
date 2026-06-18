<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BiKuBe Їжа — українська та азербайджанська кухня з доставкою в Києві. Автентичні страви, свіжі інгредієнти, доставка за 30–60 хвилин.">
    <meta name="keywords" content="українська кухня, азербайджанська кухня, доставка їжі Київ, ресторан Київ, BiKuBe">
    <meta property="og:title" content="BiKuBe Їжа — Дві кухні, один смачний світ">
    <meta property="og:description" content="Замовляйте автентичні українські та азербайджанські страви з доставкою або бронюйте стіл у ресторані.">
    <meta property="og:image" content="/images/bikube/home/v2/category-food.png">
    <title>BiKuBe Їжа — Українська &amp; Азербайджанська кухня</title>
    <link rel="icon" href="/images/bikube/home/v2/category-food.png" type="image/png">
    <style>
        /* ===== BASE ===== */
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--gold:#c4a35a;--gold2:#e8b84b;--gold-dim:rgba(196,163,90,.13);--gold-glow:rgba(196,163,90,.35);--bg:#0c0a06;--bg2:#14100a;--bg3:#1e1710;--card:#1a1208;--card2:#231810;--line:rgba(196,163,90,.14);--text:#f5e6c8;--muted:#9a8a72;--red:#c53030;--red2:#a82020;--green:#2a8a50;--shadow:0 24px 80px rgba(0,0,0,.6)}
        .gf-body{background:var(--bg);color:var(--text);font-family:'Georgia','Times New Roman',serif;font-size:16px;line-height:1.6;overflow-x:hidden;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}
        .gf-body *:focus-visible{outline:2px solid var(--gold);outline-offset:3px}
        /* ===== KEYFRAMES ===== */
        @keyframes goldShimmer{0%{background-position:200% center}100%{background-position:-200% center}}
        @keyframes fadeUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
        @keyframes floatCard{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
        @keyframes pulse{0%,100%{opacity:.7;transform:scale(1)}50%{opacity:.2;transform:scale(1.5)}}
        @keyframes steamRise{0%{opacity:.5;transform:translateX(-50%) translateY(0) scale(1)}100%{opacity:0;transform:translateX(-50%) translateY(-50px) scale(1.6)}}
        @keyframes spinText{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
        @keyframes revealLeft{from{opacity:0;transform:translateX(-32px)}to{opacity:1;transform:translateX(0)}}
        @keyframes revealRight{from{opacity:0;transform:translateX(32px)}to{opacity:1;transform:translateX(0)}}
        /* ===== SHELL ===== */
        .gf-shell{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px)}
        .gf-section-header{margin-bottom:40px}
        .gf-section-title{font-size:clamp(26px,3vw,44px);font-weight:400;line-height:1.2;letter-spacing:-.01em}
        .gf-section-sub{font-family:sans-serif;font-size:15px;color:var(--muted);margin-top:10px;line-height:1.7}
        .gf-eyebrow-tag{display:inline-flex;align-items:center;gap:10px;font-family:sans-serif;font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:var(--gold);margin-bottom:12px}
        .gf-eyebrow-tag::before{content:'';width:20px;height:1px;background:var(--gold)}
        /* ===== BUTTONS ===== */
        .gf-btn{display:inline-flex;align-items:center;gap:8px;border-radius:10px;padding:13px 26px;font-family:sans-serif;font-size:14px;font-weight:600;letter-spacing:.04em;cursor:pointer;text-decoration:none;border:none;transition:background .3s,color .3s,transform .2s,box-shadow .3s}
        .gf-btn-primary{background:var(--red);color:#fff;box-shadow:0 8px 28px rgba(197,48,48,.38)}
        .gf-btn-primary:hover{background:var(--red2);transform:translateY(-2px);box-shadow:0 14px 36px rgba(197,48,48,.5)}
        .gf-btn-secondary{background:var(--gold-dim);color:var(--gold);border:1.5px solid rgba(196,163,90,.4)}
        .gf-btn-secondary:hover{background:var(--gold);color:var(--bg);transform:translateY(-2px)}
        .gf-btn-outline{background:transparent;color:var(--gold);border:1.5px solid rgba(196,163,90,.5)}
        .gf-btn-outline:hover{background:var(--gold);color:var(--bg);transform:translateY(-2px)}
        .gf-btn-ghost{background:transparent;color:var(--muted);border:none;padding:13px 4px}
        .gf-btn-ghost:hover{color:var(--text)}
        .gf-add-btn{display:inline-flex;align-items:center;gap:5px;background:var(--bg3);border:1px solid var(--line);color:var(--text);border-radius:8px;padding:8px 14px;font-family:sans-serif;font-size:12px;font-weight:600;cursor:pointer;transition:border-color .3s,background .3s,box-shadow .3s,transform .2s;white-space:nowrap;position:relative}
        .gf-add-btn:hover{border-color:var(--gold);background:var(--gold-dim);box-shadow:0 0 18px var(--gold-glow);transform:scale(1.05)}
        /* ===== HEADER ===== */
        .gf-header{position:fixed;top:0;left:0;right:0;z-index:1000;height:72px;display:flex;align-items:center;padding:0 clamp(16px,2.5vw,36px);backdrop-filter:blur(22px) saturate(1.5);-webkit-backdrop-filter:blur(22px) saturate(1.5);background:rgba(12,10,6,0);border-bottom:1px solid transparent;transition:background .4s,border-color .4s,box-shadow .4s}
        .gf-header.scrolled{background:rgba(12,10,6,.96);border-bottom-color:var(--line);box-shadow:0 4px 36px rgba(0,0,0,.55)}
        .gf-header-inner{width:100%;max-width:1440px;margin:0 auto;display:flex;align-items:center;gap:24px}
        .gf-logo{display:flex;align-items:center;gap:11px;text-decoration:none;color:var(--gold);white-space:nowrap}
        .gf-logo-crest{display:flex;align-items:center;justify-content:center;flex-shrink:0;color:var(--gold)}
        .gf-logo-textblock{display:flex;flex-direction:column;line-height:1.15}
        .gf-logo-main{font-size:18px;font-weight:700;letter-spacing:.04em;color:var(--gold)}
        .gf-logo-sub{font-family:sans-serif;font-size:10px;letter-spacing:.12em;color:var(--muted);text-transform:uppercase}
        .gf-nav{flex:1;display:flex;align-items:center;justify-content:center;min-width:0}
        .gf-nav-list{display:flex;gap:18px;list-style:none;flex-wrap:nowrap;white-space:nowrap}
        .gf-nav-link{color:var(--text);text-decoration:none;font-family:sans-serif;font-size:12.5px;letter-spacing:.03em;position:relative;padding-bottom:3px;transition:color .3s;white-space:nowrap}
        .gf-nav-link::after{content:'';position:absolute;bottom:0;left:0;width:0;height:1.5px;background:var(--gold);transition:width .3s}
        .gf-nav-link:hover,.gf-nav-link.active{color:var(--gold)}
        .gf-nav-link:hover::after,.gf-nav-link.active::after{width:100%}
        .gf-header-actions{display:flex;align-items:center;gap:10px;flex-shrink:0}
        .gf-header-login,.gf-header-register{font-family:sans-serif;font-size:13px;text-decoration:none;padding:8px 16px;border-radius:8px;border:1px solid var(--line);transition:border-color .3s,color .3s,background .3s}
        .gf-header-login{color:var(--muted)}
        .gf-header-login:hover{color:var(--text);border-color:rgba(196,163,90,.3)}
        .gf-header-register{color:var(--gold);border-color:rgba(196,163,90,.4);background:var(--gold-dim)}
        .gf-header-register:hover{background:var(--gold);color:var(--bg)}
        .gf-icon-btn{background:transparent;border:1px solid var(--line);border-radius:8px;padding:8px 10px;color:var(--muted);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:border-color .3s,color .3s}
        .gf-icon-btn:hover{border-color:rgba(196,163,90,.4);color:var(--gold)}
        .gf-cart-btn{position:relative;display:flex;align-items:center;gap:6px}
        .gf-cart-count{font-family:sans-serif;font-size:12px;font-weight:700;color:var(--gold)}
        .gf-lang-toggle{display:flex;gap:4px;align-items:center}
        .gf-lang-btn{font-family:sans-serif;font-size:11px;font-weight:600;letter-spacing:.06em;padding:5px 9px;border-radius:6px;border:1px solid var(--line);background:transparent;color:var(--muted);cursor:pointer;transition:all .25s}
        .gf-lang-btn.active{background:var(--gold-dim);border-color:rgba(196,163,90,.5);color:var(--gold)}
        .gf-lang-btn:hover:not(.active){border-color:rgba(196,163,90,.3);color:var(--text)}
        .gf-burger{display:none;flex-direction:column;gap:5px;background:none;border:none;cursor:pointer;padding:4px}
        .gf-burger span{display:block;width:24px;height:2px;background:var(--text);border-radius:2px;transition:transform .3s,opacity .3s}
        /* ===== HERO ===== */
        .gf-hero{min-height:100vh;position:relative;display:flex;align-items:center;overflow:hidden;padding-top:70px}
        .gf-hero-bg{position:absolute;inset:0;z-index:0}
        .gf-hero-bg-overlay{position:absolute;inset:0;z-index:2;background:linear-gradient(135deg,rgba(12,10,6,.9) 0%,rgba(20,16,10,.6) 50%,rgba(12,10,6,.8) 100%)}
        .gf-hero-bg-img{position:absolute;inset:0;z-index:1;transform:scale(1.06);transition:transform 8s ease;background:
            radial-gradient(ellipse 90% 80% at 68% 45%, rgba(210,120,20,.78), transparent 48%),
            radial-gradient(ellipse 50% 60% at 78% 65%, rgba(160,70,10,.55), transparent 45%),
            radial-gradient(ellipse 40% 40% at 85% 20%, rgba(240,170,30,.38), transparent 40%),
            radial-gradient(ellipse 35% 35% at 52% 52%, rgba(180,100,15,.32), transparent 38%),
            radial-gradient(ellipse 50% 40% at 20% 80%, rgba(120,45,8,.45), transparent 48%),
            radial-gradient(ellipse 30% 30% at 40% 20%, rgba(200,130,20,.22), transparent 35%),
            radial-gradient(circle at 50% 50%, #221008 0%, #140a04 55%, #080402 100%)}
        .gf-hero-particles{position:absolute;inset:0;z-index:3;background-image:
            radial-gradient(ellipse 70% 55% at 68% 40%,rgba(196,163,90,.25),transparent 52%),
            radial-gradient(ellipse 40% 50% at 10% 80%,rgba(197,48,48,.12),transparent 52%),
            radial-gradient(ellipse 25% 25% at 88% 18%,rgba(240,200,60,.2),transparent 40%),
            radial-gradient(ellipse 18% 18% at 46% 55%,rgba(255,210,90,.08),transparent 35%),
            linear-gradient(180deg,rgba(12,10,6,.45) 0%,transparent 40%,rgba(12,10,6,.7) 100%)}
        .gf-hero-content{position:relative;z-index:4;width:100%;max-width:1440px;margin:0 auto;padding:100px clamp(16px,2.5vw,36px) 120px;display:grid;grid-template-columns:1fr 370px;gap:48px;align-items:center}
        .gf-hero-eyebrow{display:inline-flex;align-items:center;gap:12px;font-family:sans-serif;font-size:11px;letter-spacing:.24em;text-transform:uppercase;color:var(--gold);margin-bottom:22px}
        .gf-eyebrow-dot{display:inline-block;width:6px;height:6px;border-radius:50%;background:var(--gold);animation:pulse 2s infinite}
        .gf-hero-h1{font-size:clamp(56px,7.5vw,110px);font-weight:400;line-height:1.02;letter-spacing:-.025em;margin-bottom:24px}
        .gf-hero-accent{display:inline;background:linear-gradient(90deg,#c4a35a 0%,#e8b84b 40%,#f5cc60 60%,#c4a35a 100%);background-size:200% auto;-webkit-background-clip:text;background-clip:text;-webkit-text-fill-color:transparent;animation:goldShimmer 3.5s linear infinite;font-style:italic}
        .gf-hero-sub{font-family:sans-serif;font-size:16px;color:var(--muted);max-width:520px;line-height:1.85;margin-bottom:36px}
        .gf-hero-ctas{display:flex;flex-wrap:wrap;gap:12px;align-items:center;margin-bottom:36px}
        .gf-hero-dots{display:flex;gap:8px}
        .gf-dot{width:8px;height:8px;border-radius:50%;background:rgba(196,163,90,.28);border:none;cursor:pointer;transition:background .3s,transform .3s;padding:0}
        .gf-dot--active,.gf-dot:hover{background:var(--gold);transform:scale(1.35)}
        .gf-hero-card{background:rgba(18,12,6,.88);backdrop-filter:blur(28px);-webkit-backdrop-filter:blur(28px);border:1px solid rgba(196,163,90,.3);border-radius:22px;padding:24px;box-shadow:0 32px 90px rgba(0,0,0,.7),inset 0 0 60px rgba(196,163,90,.05),0 0 0 1px rgba(196,163,90,.08);animation:floatCard 4.5s ease-in-out infinite}
        .gf-hero-card-label{font-family:sans-serif;font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:var(--gold);margin-bottom:18px}
        .gf-hero-card-art{height:130px;border-radius:12px;margin-bottom:18px;overflow:hidden}
        .gf-hero-card-body{}
        .gf-hero-card-badge{display:inline-block;padding:3px 10px;border-radius:50px;background:rgba(196,163,90,.85);color:var(--bg);font-family:sans-serif;font-size:11px;font-weight:700;margin-bottom:8px}
        .gf-hero-card-title{font-size:19px;margin-bottom:3px}
        .gf-hero-card-weight{font-family:sans-serif;font-size:12px;color:var(--muted);margin-bottom:5px}
        .gf-hero-card-ing{font-family:sans-serif;font-size:12px;color:var(--muted);opacity:.7;margin-bottom:16px;line-height:1.5}
        .gf-hero-card-footer{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;padding-top:14px;border-top:1px solid var(--line)}
        .gf-hero-card-rating{font-family:sans-serif;font-size:12px;color:var(--gold)}
        .gf-hero-card-price{font-size:26px;color:var(--gold)}
        .gf-hero-scroll-hint{position:absolute;bottom:32px;left:50%;transform:translateX(-50%);z-index:5;display:flex;flex-direction:column;align-items:center;gap:8px;font-family:sans-serif;font-size:11px;letter-spacing:.18em;text-transform:uppercase;color:var(--muted);animation:fadeUp 1s ease .8s both}
        /* ===== CSS ART — DISHES ===== */
        .gf-dish-art,.gf-hero-card-art{background:#1a1208}
        .gf-dish-art-1,.gf-hero-card-art{background:radial-gradient(circle at 50% 55%,rgba(245,240,220,.65) 0%,transparent 25%),radial-gradient(circle at 50% 50%,#8b1a1a 0%,#c53030 38%,#6b1010 65%,#2a0808 100%)}
        .gf-dish-art-2{background:radial-gradient(circle at 55% 38%,rgba(255,220,100,.45) 0%,transparent 28%),radial-gradient(ellipse at 50% 50%,#1e1200 0%,#5a3e08 40%,#8b6914 65%,#c4a35a 100%)}
        .gf-dish-art-3{background:radial-gradient(circle at 40% 35%,rgba(255,255,200,.5) 0%,transparent 22%),radial-gradient(circle at 60% 65%,rgba(200,180,120,.4) 0%,transparent 18%),radial-gradient(circle at 50% 50%,#201408 0%,#6a5030 45%,#d4c89a 90%)}
        .gf-dish-art-4{background:radial-gradient(circle at 45% 40%,rgba(200,160,80,.45) 0%,transparent 28%),radial-gradient(circle at 50% 50%,#2a1a08 0%,#6a3a10 50%,#a06020 80%,#c4a35a 100%)}
        .gf-dish-art-5{background:radial-gradient(circle at 35% 40%,rgba(200,180,120,.4) 0%,transparent 22%),radial-gradient(circle at 70% 60%,rgba(160,80,40,.4) 0%,transparent 20%),radial-gradient(circle at 50% 50%,#1a1208 0%,#5a3a18 50%,#8a6a30 80%)}
        .gf-dish-art-6{background:radial-gradient(circle at 40% 35%,rgba(180,140,80,.5) 0%,transparent 25%),radial-gradient(circle at 60% 55%,rgba(80,140,60,.3) 0%,transparent 18%),radial-gradient(circle at 50% 50%,#100a02 0%,#3a2a08 50%,#b89448 85%)}
        .gf-dish-art-7{background:radial-gradient(circle at 30% 40%,rgba(200,140,40,.5) 0%,transparent 22%),radial-gradient(circle at 65% 60%,rgba(80,40,10,.4) 0%,transparent 18%),radial-gradient(circle at 50% 50%,#0a0602 0%,#3a2008 45%,#8a5010 75%,#c4903a 100%)}
        .gf-dish-art-8{background:radial-gradient(circle at 50% 35%,rgba(200,180,120,.35) 0%,transparent 22%),radial-gradient(circle at 50% 50%,#0e0804 0%,#201208 55%,#5a3010 80%,#8a5820 100%)}
        /* ===== STEPS ===== */
        .gf-steps{padding:72px 0;background:var(--bg2);border-bottom:1px solid var(--line)}
        .gf-steps-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px)}
        .gf-steps .gf-section-header{margin-bottom:36px}
        .gf-steps-track{display:flex;gap:16px;overflow-x:auto;scroll-snap-type:x mandatory;padding-bottom:24px;-ms-overflow-style:none;scrollbar-width:none}
        .gf-steps-track::-webkit-scrollbar{display:none}
        .gf-step-card{min-width:230px;max-width:260px;flex-shrink:0;scroll-snap-align:start;background:var(--card);border:1px solid var(--line);border-radius:18px;padding:28px 22px;position:relative;overflow:hidden;transition:border-color .35s,box-shadow .35s,transform .35s}
        .gf-step-card:hover{border-color:rgba(196,163,90,.55);box-shadow:0 16px 44px rgba(0,0,0,.55);transform:translateY(-7px)}
        .gf-step-img{height:150px;border-radius:12px;margin-bottom:16px;position:relative;overflow:hidden;transition:transform .4s}
        .gf-step-card:hover .gf-step-img{transform:scale(1.03)}
        .gf-step-num-overlay{position:absolute;top:10px;left:12px;font-family:sans-serif;font-size:13px;font-weight:700;letter-spacing:.12em;color:rgba(255,255,255,.6);text-shadow:0 1px 4px rgba(0,0,0,.5)}
        .gf-step-body{}
        .gf-step-title{font-size:17px;margin-bottom:6px;line-height:1.3}
        .gf-step-sub{font-family:sans-serif;font-size:12px;color:var(--muted);line-height:1.6}
        /* ===== CATICONS ===== */
        .gf-caticons{padding:56px 0;border-bottom:1px solid var(--line)}
        .gf-caticons-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px)}
        .gf-caticons .gf-section-title{margin-bottom:28px}
        .gf-caticons-grid{display:grid;grid-template-columns:repeat(8,1fr);gap:12px}
        .gf-caticon-tile{display:flex;flex-direction:column;align-items:center;gap:9px;padding:18px 8px;background:var(--card);border:1px solid var(--line);border-radius:14px;text-decoration:none;color:var(--text);transition:border-color .3s,transform .3s,box-shadow .3s;cursor:pointer}
        .gf-caticon-tile:hover{border-color:rgba(196,163,90,.55);transform:translateY(-6px) scale(1.07);box-shadow:0 8px 28px rgba(196,163,90,.18)}
        .gf-caticon-svg{width:36px;height:36px;color:var(--gold);opacity:.8;transition:opacity .3s,transform .3s;display:flex;align-items:center;justify-content:center}
        .gf-caticon-svg svg{width:100%;height:100%}
        .gf-caticon-tile:hover .gf-caticon-svg{opacity:1;transform:scale(1.1)}
        .gf-caticon-label{font-family:sans-serif;font-size:10.5px;color:var(--muted);text-align:center;letter-spacing:.03em;line-height:1.3;transition:color .3s}
        .gf-caticon-tile:hover .gf-caticon-label{color:var(--gold)}
        /* ===== MENU ===== */
        .gf-menu{padding:80px 0;border-bottom:1px solid var(--line)}
        .gf-menu-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px)}
        .gf-menu-filters{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:36px}
        .gf-filter-tab{padding:9px 20px;border-radius:50px;background:var(--card);border:1px solid var(--line);color:var(--muted);font-family:sans-serif;font-size:12px;letter-spacing:.04em;cursor:pointer;transition:all .3s;white-space:nowrap}
        .gf-filter-tab:hover{border-color:rgba(196,163,90,.4);color:var(--text)}
        .gf-filter-tab--active{background:var(--gold);border-color:var(--gold);color:var(--bg);font-weight:600}
        .gf-filter-tab--ghost{background:transparent;border-color:transparent;color:var(--gold);text-decoration:none;display:inline-flex;align-items:center}
        .gf-dishes-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:22px;margin-bottom:40px}
        .gf-dish-card{background:var(--card);border-radius:18px;overflow:hidden;border:1px solid var(--line);position:relative;will-change:transform;transition:border-color .35s,box-shadow .35s,opacity .3s}
        .gf-dish-card:hover{border-color:rgba(196,163,90,.6);box-shadow:0 20px 56px rgba(0,0,0,.6),0 0 40px rgba(196,163,90,.1)}
        .gf-dish-art{height:190px;width:100%;position:relative;overflow:hidden;transition:transform .4s}
        .gf-dish-art::after{content:'';position:absolute;bottom:-20px;left:50%;width:60px;height:60px;border-radius:50%;background:rgba(196,163,90,.06);transform:translateX(-50%);filter:blur(18px);animation:steamRise 3s ease-in-out infinite;z-index:1}
        .gf-dish-badge{position:absolute;top:14px;left:14px;z-index:3;padding:4px 11px;border-radius:50px;font-family:sans-serif;font-size:11px;font-weight:700;letter-spacing:.06em;pointer-events:none}
        .gf-dish-badge--hit{background:rgba(196,163,90,.92);color:var(--bg)}
        .gf-dish-badge--new{background:rgba(42,138,80,.92);color:#fff}
        .gf-dish-fav{position:absolute;top:14px;right:14px;z-index:3;background:rgba(12,10,6,.6);border:1px solid var(--line);border-radius:50%;width:34px;height:34px;display:flex;align-items:center;justify-content:center;color:var(--muted);cursor:pointer;transition:border-color .3s,color .3s}
        .gf-dish-fav:hover,.gf-dish-fav.active{border-color:var(--red);color:var(--red)}
        .gf-dish-body{padding:18px}
        .gf-dish-cat-tag{font-family:sans-serif;font-size:11px;color:var(--muted);margin-bottom:6px;letter-spacing:.03em}
        .gf-dish-title{font-size:18px;margin-bottom:3px;line-height:1.3}
        .gf-dish-sub{font-family:sans-serif;font-size:12px;color:var(--muted);margin-bottom:4px}
        .gf-dish-ing{font-family:sans-serif;font-size:12px;color:var(--muted);opacity:.75;line-height:1.5;margin-bottom:16px}
        .gf-dish-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;flex-wrap:wrap}
        .gf-dish-rating{font-family:sans-serif;font-size:11px;color:var(--gold);white-space:nowrap}
        .gf-dish-price{font-size:22px;color:var(--gold)}
        .gf-dish-price-cur{font-family:sans-serif;font-size:14px}
        .gf-menu-more{text-align:center;padding-top:8px}
        /* ===== ABOUT BANNER ===== */
        .gf-about-banner{padding:80px 0;background:var(--bg2);border-top:1px solid var(--line);border-bottom:1px solid var(--line)}
        .gf-about-banner-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px);display:grid;grid-template-columns:1fr 1fr;gap:72px;align-items:center}
        .gf-about-banner-media{position:relative}
        .gf-about-banner-img{border-radius:20px;overflow:hidden;border:1px solid var(--line)}
        .gf-about-banner-img img{width:100%;height:400px;object-fit:cover;display:block;filter:brightness(.75) saturate(.8)}
        .gf-play-btn{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:68px;height:68px;border-radius:50%;border:2px solid rgba(255,255,255,.7);background:rgba(12,10,6,.5);backdrop-filter:blur(8px);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .3s,box-shadow .3s;padding-left:4px}
        .gf-play-btn::after{content:'';position:absolute;inset:-8px;border-radius:50%;border:1px solid rgba(255,255,255,.2);animation:pulse 2.5s ease-in-out infinite}
        .gf-play-btn:hover{background:rgba(196,163,90,.3);box-shadow:0 0 36px rgba(196,163,90,.5)}
        .gf-about-banner-badge{position:absolute;bottom:-20px;right:-20px;width:100px;height:100px}
        .gf-rotating-text{display:block;width:100%;height:100%;border-radius:50%;border:1px solid rgba(196,163,90,.3);font-family:sans-serif;font-size:9.5px;letter-spacing:.12em;color:var(--gold);text-transform:uppercase;animation:spinText 14s linear infinite;line-height:100px;text-align:center;overflow:hidden;white-space:nowrap}
        .gf-about-banner-copy{}
        .gf-about-banner-text{font-family:sans-serif;font-size:15px;color:var(--muted);line-height:1.85;margin-bottom:20px}
        .gf-about-features{list-style:none;margin-top:28px;display:flex;flex-direction:column;gap:14px}
        .gf-about-feature{display:flex;align-items:flex-start;gap:14px;font-family:sans-serif;font-size:14px;color:var(--muted)}
        .gf-about-feature strong{display:block;color:var(--text);font-weight:600;margin-bottom:1px}
        .gf-about-feature-icon{font-size:20px;flex-shrink:0;margin-top:-1px}
        /* ===== PROMOS ===== */
        .gf-promos{padding:80px 0;border-bottom:1px solid var(--line)}
        .gf-promos-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px)}
        .gf-promos-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
        .gf-promo-card{background:var(--card2);border:1px solid var(--line);border-radius:18px;overflow:hidden;position:relative;transition:transform .35s,box-shadow .35s;cursor:pointer}
        .gf-promo-card:hover{transform:translateY(-6px) scale(1.015);box-shadow:var(--shadow)}
        .gf-promo-discount{position:absolute;top:14px;right:14px;z-index:2;background:var(--red);color:#fff;font-family:sans-serif;font-size:12px;font-weight:700;padding:4px 10px;border-radius:50px}
        .gf-promo-art{height:160px;width:100%}
        .gf-promo-art--combo-lunch{background:radial-gradient(circle at 45% 45%,rgba(220,200,120,.55),rgba(100,60,10,.7) 55%,rgba(10,8,2,.95))}
        .gf-promo-art--combo-azerbaijan{background:radial-gradient(circle at 55% 40%,rgba(180,80,20,.5),rgba(60,20,8,.8) 55%,rgba(8,4,2,.95))}
        .gf-promo-art--combo-family{background:radial-gradient(circle at 40% 55%,rgba(160,120,40,.5),rgba(60,40,8,.8) 55%,rgba(10,6,2,.95))}
        .gf-promo-art--combo-grill{background:radial-gradient(circle at 50% 40%,rgba(200,80,20,.6),rgba(80,20,4,.8) 55%,rgba(8,4,2,.95))}
        .gf-promo-body{padding:18px 20px 22px}
        .gf-promo-title{font-size:18px;margin-bottom:6px}
        .gf-promo-sub{font-family:sans-serif;font-size:12px;color:var(--muted);line-height:1.55;margin-bottom:14px}
        .gf-promo-pricing{display:flex;align-items:baseline;gap:10px;margin-bottom:16px}
        .gf-promo-old-price{font-family:sans-serif;font-size:14px;color:var(--muted);text-decoration:line-through}
        .gf-promo-price{font-size:26px;color:var(--gold)}
        .gf-promo-cta{width:100%;justify-content:center;border-radius:9px;padding:11px 20px}
        /* ===== LOWER ===== */
        .gf-lower{padding:80px 0;border-bottom:1px solid var(--line);background:var(--bg2)}
        .gf-lower-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px);display:grid;grid-template-columns:1fr 1fr 1fr;gap:36px;align-items:start}
        .gf-booking,.gf-delivery{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:32px 28px}
        .gf-booking-header,.gf-delivery-header{margin-bottom:26px}
        .gf-booking-icon,.gf-delivery-icon img{display:block;margin-bottom:10px}
        .gf-booking-header .gf-section-title,.gf-delivery-header .gf-section-title{font-size:22px;margin-bottom:6px}
        .gf-booking-form .gf-form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
        .gf-form-group{margin-bottom:14px}
        .gf-form-label{display:block;font-family:sans-serif;font-size:11px;letter-spacing:.1em;color:var(--muted);text-transform:uppercase;margin-bottom:6px}
        .gf-form-label span{color:var(--red)}
        .gf-form-input{width:100%;padding:11px 15px;background:var(--bg3);border:1px solid var(--line);border-radius:9px;color:var(--text);font-family:sans-serif;font-size:14px;appearance:none;outline:none;transition:border-color .3s,box-shadow .3s}
        .gf-form-input:focus{border-color:rgba(196,163,90,.55);box-shadow:0 0 0 3px rgba(196,163,90,.1)}
        .gf-form-input::placeholder{color:var(--muted);opacity:.55}
        .gf-form-input.gf-invalid{border-color:var(--red)}
        .gf-form-textarea{resize:vertical;min-height:80px;font-family:inherit}
        .gf-form-input option{background:var(--card)}
        .gf-booking-submit{width:100%;justify-content:center;margin-top:4px;border-radius:9px;padding:13px 20px}
        .gf-booking-note{font-family:sans-serif;font-size:12px;color:var(--muted);display:flex;align-items:center;gap:6px;margin-top:12px;line-height:1.4}
        .gf-booking-success{text-align:center;padding:40px 20px;font-size:22px;color:var(--gold);animation:fadeUp .5s ease}
        .gf-atmo{background:var(--card);border:1px solid var(--line);border-radius:18px;padding:28px;display:flex;flex-direction:column;gap:16px}
        .gf-atmo .gf-section-title{font-size:22px;margin-bottom:4px}
        .gf-atmo-grid{display:grid;grid-template-rows:180px 120px 120px;gap:10px}
        .gf-atmo-img{border-radius:12px;overflow:hidden;position:relative;transition:transform .4s}
        .gf-atmo-img:hover{transform:scale(1.03)}
        .gf-atmo-img--large{grid-row:span 1}
        .gf-atmo-img--1{background:radial-gradient(ellipse 80% 60% at 35% 45%,rgba(180,100,20,.55),rgba(10,8,4,.95) 70%)}
        .gf-atmo-img--2{background:radial-gradient(ellipse 60% 80% at 70% 30%,rgba(160,80,10,.45),rgba(8,6,2,.95) 70%)}
        .gf-atmo-img--3{background:radial-gradient(ellipse 100% 60% at 50% 60%,rgba(140,70,10,.5),rgba(6,4,2,.95) 75%)}
        .gf-atmo-info{list-style:none;display:flex;flex-direction:column;gap:8px;margin-top:4px}
        .gf-atmo-info-item{display:flex;align-items:center;gap:10px;font-family:sans-serif;font-size:13px;color:var(--muted)}
        .gf-atmo-info-icon{font-size:18px;flex-shrink:0}
        .gf-delivery-info{list-style:none;margin-bottom:24px;display:flex;flex-direction:column;gap:2px}
        .gf-delivery-info-item{display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid var(--line);font-family:sans-serif;font-size:13px;color:var(--muted)}
        .gf-delivery-info-item:last-child{border-bottom:none}
        .gf-delivery-info-icon{font-size:18px;flex-shrink:0;margin-top:0}
        .gf-delivery-info-item strong{display:block;color:var(--text);font-weight:600;margin-bottom:1px;font-size:13px}
        .gf-delivery-map{height:110px;border-radius:12px;background:linear-gradient(135deg,var(--bg3),var(--card2));border:1px solid var(--line);position:relative;overflow:hidden;margin-bottom:20px;display:flex;align-items:center;justify-content:center}
        .gf-delivery-map::before{content:'';position:absolute;inset:0;background-image:repeating-linear-gradient(0deg,transparent,transparent 22px,rgba(196,163,90,.05) 22px,rgba(196,163,90,.05) 23px),repeating-linear-gradient(90deg,transparent,transparent 22px,rgba(196,163,90,.05) 22px,rgba(196,163,90,.05) 23px)}
        .gf-delivery-map-placeholder{display:flex;flex-direction:column;align-items:center;gap:6px;font-family:sans-serif;font-size:13px;color:var(--muted);position:relative;z-index:1}
        .gf-delivery-map-placeholder span:first-child{font-size:28px}
        /* ===== REVIEWS ===== */
        .gf-reviews{padding:80px 0;border-bottom:1px solid var(--line);overflow:hidden}
        .gf-reviews-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px)}
        .gf-reviews-controls{display:flex;gap:10px;margin-top:10px;margin-bottom:32px}
        .gf-reviews-arrow{width:48px;height:48px;border-radius:50%;border:1.5px solid rgba(196,163,90,.35);background:transparent;color:var(--gold);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .3s,border-color .3s,box-shadow .3s}
        .gf-reviews-arrow:hover{background:var(--gold-dim);border-color:var(--gold);box-shadow:0 0 20px rgba(196,163,90,.25)}
        .gf-reviews-track{display:flex;gap:18px;overflow-x:auto;scroll-snap-type:x mandatory;padding-bottom:20px;-ms-overflow-style:none;scrollbar-width:none}
        .gf-reviews-track::-webkit-scrollbar{display:none}
        .gf-review-card{min-width:290px;max-width:330px;flex-shrink:0;scroll-snap-align:start;background:rgba(26,18,8,.75);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px);border:1px solid rgba(196,163,90,.14);border-radius:18px;padding:26px;transition:border-color .3s,transform .3s}
        .gf-review-card:hover{border-color:rgba(196,163,90,.38);transform:translateY(-4px)}
        .gf-review-stars{color:var(--gold);font-size:15px;letter-spacing:2px;margin-bottom:14px}
        .gf-review-text{font-family:sans-serif;font-size:14px;color:var(--muted);line-height:1.8;margin-bottom:20px;font-style:italic}
        .gf-review-text p{margin:0}
        .gf-review-author{display:flex;align-items:center;gap:12px}
        .gf-review-avatar{width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg,var(--card2),var(--bg3));border:1.5px solid rgba(196,163,90,.28);display:flex;align-items:center;justify-content:center;font-family:sans-serif;font-size:13px;font-weight:600;color:var(--gold);flex-shrink:0}
        .gf-review-name{font-size:15px}
        .gf-reviews-write{margin-top:30px;text-align:center}
        /* ===== GALLERY ===== */
        .gf-gallery{padding:80px 0;border-bottom:1px solid var(--line)}
        .gf-gallery-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px)}
        .gf-gallery-grid{display:grid;grid-template-columns:2fr 1fr 1fr;grid-template-rows:280px 280px;gap:12px;margin-top:32px}
        .gf-gallery-item{border-radius:14px;overflow:hidden;position:relative;cursor:pointer}
        .gf-gallery-item--1{grid-row:span 2}
        .gf-gallery-art{width:100%;height:100%;transition:transform .5s;background:var(--card2)}
        .gf-gallery-item:hover .gf-gallery-art{transform:scale(1.06)}
        .gf-gallery-art--1{background:radial-gradient(ellipse 80% 60% at 35% 45%,rgba(180,100,20,.6),rgba(10,8,4,.95))}
        .gf-gallery-art--2{background:radial-gradient(circle at 60% 40%,rgba(140,70,10,.5),rgba(8,5,2,.95))}
        .gf-gallery-art--3{background:radial-gradient(ellipse 80% 80% at 40% 60%,rgba(100,50,8,.6),rgba(6,4,2,.95))}
        .gf-gallery-art--4{background:radial-gradient(circle at 55% 35%,rgba(160,100,20,.5),rgba(10,8,4,.95))}
        .gf-gallery-art--5{background:radial-gradient(ellipse 60% 80% at 30% 50%,rgba(120,60,8,.6),rgba(8,6,2,.95))}
        .gf-gallery-art--6{background:radial-gradient(circle at 50% 45%,rgba(200,130,20,.4),rgba(10,8,4,.95))}
        .gf-gallery-item--center{display:flex;align-items:center;justify-content:center}
        .gf-gallery-play{position:absolute;width:58px;height:58px;border-radius:50%;border:2px solid rgba(255,255,255,.7);background:rgba(12,10,6,.45);backdrop-filter:blur(8px);color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .3s,box-shadow .3s;z-index:2;opacity:0;transform:scale(.8);transition:opacity .35s,transform .35s,background .3s}
        .gf-gallery-item:hover .gf-gallery-play{opacity:1;transform:scale(1)}
        .gf-gallery-play:hover{background:rgba(196,163,90,.3);box-shadow:0 0 28px rgba(196,163,90,.5)}
        .gf-gallery-social{display:flex;align-items:center;gap:14px;margin-top:24px;justify-content:center;font-family:sans-serif;font-size:14px;color:var(--muted)}
        .gf-social-link{color:var(--gold);text-decoration:none;font-weight:600}
        .gf-social-link:hover{text-decoration:underline}
        /* ===== ABOUT US ===== */
        .gf-about-us{padding:80px 0;background:var(--bg2);border-bottom:1px solid var(--line)}
        .gf-about-us-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px);display:grid;grid-template-columns:1fr 1fr 1fr;gap:52px}
        .gf-about-us-p{font-family:sans-serif;font-size:15px;color:var(--muted);line-height:1.85;margin-bottom:18px}
        .gf-stats-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-top:28px}
        .gf-stat{padding:18px;background:var(--card);border:1px solid var(--line);border-radius:14px}
        .gf-stat-val{display:block;font-size:38px;color:var(--gold);line-height:1;margin-bottom:5px}
        .gf-stat-label{font-family:sans-serif;font-size:12px;color:var(--muted)}
        .gf-chefs-heading,.gf-quality-heading{font-size:20px;margin-bottom:20px;margin-top:8px}
        .gf-chefs-list{display:flex;flex-direction:column;gap:18px}
        .gf-chef-card{background:var(--card);border:1px solid var(--line);border-radius:14px;padding:20px;transition:border-color .3s}
        .gf-chef-card:hover{border-color:rgba(196,163,90,.35)}
        .gf-chef-avatar{width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,var(--card2),var(--bg3));border:2px solid rgba(196,163,90,.28);display:flex;align-items:center;justify-content:center;font-family:sans-serif;font-size:18px;font-weight:700;color:var(--gold);margin-bottom:14px}
        .gf-chef-name{font-size:17px;margin-bottom:2px}
        .gf-chef-role{font-family:sans-serif;font-size:12px;color:var(--gold);font-weight:600;margin-bottom:2px}
        .gf-chef-cuisine{font-family:sans-serif;font-size:12px;color:var(--muted);margin-bottom:10px}
        .gf-chef-quote{font-style:italic;font-family:sans-serif;font-size:13px;color:var(--muted);line-height:1.6;border-left:2px solid rgba(196,163,90,.3);padding-left:12px}
        .gf-quality-list{list-style:none;display:flex;flex-direction:column;gap:10px;margin-bottom:24px}
        .gf-quality-item{display:flex;align-items:flex-start;gap:10px;font-family:sans-serif;font-size:14px;color:var(--muted);line-height:1.5}
        .gf-quality-check{color:var(--gold);font-size:14px;flex-shrink:0;margin-top:1px;font-weight:700}
        .gf-certifications{margin-bottom:24px;padding:18px;background:var(--card);border:1px solid var(--line);border-radius:12px}
        .gf-cert-heading{font-family:sans-serif;font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:var(--gold);margin-bottom:12px}
        .gf-cert-list{list-style:none;display:flex;flex-direction:column;gap:8px}
        .gf-cert-item{font-family:sans-serif;font-size:13px;color:var(--muted);display:flex;align-items:center;gap:8px}
        .gf-about-cta-group{display:flex;gap:10px;flex-wrap:wrap}
        /* ===== FOOTER ===== */
        .gf-footer{background:var(--card);border-top:1px solid var(--line)}
        .gf-footer-top{padding:60px 0 48px}
        .gf-footer-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px);display:grid;grid-template-columns:2fr 1fr 1fr 1.4fr;gap:48px}
        .gf-footer-brand .gf-logo{margin-bottom:14px;display:inline-flex}
        .gf-footer-tagline{font-family:sans-serif;font-size:14px;color:var(--muted);line-height:1.8;margin-bottom:22px}
        .gf-social-row{display:flex;gap:10px}
        .gf-social-icon{width:38px;height:38px;border-radius:9px;background:var(--bg3);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;color:var(--muted);text-decoration:none;transition:border-color .3s,color .3s,background .3s}
        .gf-social-icon:hover{border-color:var(--gold);color:var(--gold);background:var(--gold-dim)}
        .gf-footer-nav-heading{font-family:sans-serif;font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);margin-bottom:16px}
        .gf-footer-nav-list,.gf-footer-contact-list{list-style:none;display:flex;flex-direction:column;gap:9px}
        .gf-footer-nav-link{color:var(--muted);text-decoration:none;font-family:sans-serif;font-size:14px;transition:color .3s}
        .gf-footer-nav-link:hover{color:var(--text)}
        .gf-footer-contact-item{display:flex;align-items:flex-start;gap:10px;font-family:sans-serif;font-size:13px;color:var(--muted);line-height:1.55}
        .gf-footer-contact-item svg{flex-shrink:0;margin-top:2px;color:var(--gold)}
        .gf-footer-tel,.gf-footer-email{color:var(--text);text-decoration:none;transition:color .3s}
        .gf-footer-tel:hover,.gf-footer-email:hover{color:var(--gold)}
        .gf-footer-bottom{border-top:1px solid var(--line);padding:20px 0}
        .gf-footer-bottom-inner{max-width:1360px;margin:0 auto;padding:0 clamp(16px,3vw,44px);display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px}
        .gf-copyright,.gf-footer-bottom-link,.gf-footer-bottom-links span{font-family:sans-serif;font-size:13px;color:var(--muted)}
        .gf-footer-bottom-links{display:flex;gap:16px;align-items:center}
        .gf-footer-bottom-link{text-decoration:none;transition:color .3s}
        .gf-footer-bottom-link:hover{color:var(--text)}
        .gf-footer-payments{display:flex;gap:8px;align-items:center}
        .gf-payment-badge{font-family:sans-serif;font-size:11px;color:var(--muted);border:1px solid var(--line);padding:3px 8px;border-radius:4px;letter-spacing:.03em}
        /* ===== RESPONSIVE ===== */
        @media(max-width:1100px){
            .gf-hero-content{grid-template-columns:1fr;gap:40px}
            .gf-hero-card{animation:none;max-width:480px}
            .gf-caticons-grid{grid-template-columns:repeat(4,1fr)}
            .gf-promos-grid{grid-template-columns:repeat(2,1fr)}
            .gf-about-banner-inner{grid-template-columns:1fr;gap:44px}
            .gf-about-banner-media{max-width:560px}
            .gf-lower-inner{grid-template-columns:1fr 1fr}
            .gf-lower-inner .gf-delivery{grid-column:span 2}
            .gf-gallery-grid{grid-template-columns:1fr 1fr;grid-template-rows:auto}
            .gf-gallery-item--1{grid-row:span 1;grid-column:span 2;height:280px}
            .gf-gallery-item{height:220px}
            .gf-about-us-inner{grid-template-columns:1fr 1fr;gap:36px}
            .gf-footer-inner{grid-template-columns:1fr 1fr;gap:32px}
        }
        @media(max-width:720px){
            .gf-nav{display:none}
            .gf-burger{display:flex}
            .gf-header-login,.gf-header-register{display:none}
            .gf-hero-content{padding:40px clamp(16px,3vw,44px) 80px}
            .gf-hero-h1{font-size:clamp(36px,9vw,56px)}
            .gf-hero-ctas{flex-direction:column;align-items:flex-start}
            .gf-caticons-grid{grid-template-columns:repeat(4,1fr);gap:8px}
            .gf-caticon-tile{padding:12px 6px}
            .gf-caticon-emoji{font-size:22px}
            .gf-dishes-grid{grid-template-columns:1fr}
            .gf-promos-grid{grid-template-columns:1fr}
            .gf-lower-inner{grid-template-columns:1fr}
            .gf-lower-inner .gf-delivery{grid-column:span 1}
            .gf-gallery-grid{grid-template-columns:1fr;grid-template-rows:auto}
            .gf-gallery-item--1{grid-column:span 1;height:240px}
            .gf-gallery-item{height:200px}
            .gf-about-us-inner{grid-template-columns:1fr}
            .gf-stats-grid{grid-template-columns:1fr 1fr}
            .gf-footer-inner{grid-template-columns:1fr;gap:28px}
            .gf-footer-bottom-inner{flex-direction:column;align-items:flex-start;gap:10px}
            .gf-about-banner-inner{grid-template-columns:1fr}
        }
    </style>
</head>
<body class="gf-body">

@php
    $dishes = [
        [
            'title'       => 'Борщ Полтавський',
            'subtitle'    => '500 мл',
            'ingredients' => 'Буряк, капуста, м\'ясо яловичини, картопля, томат, сметана, зелень',
            'price'       => 149,
            'rating'      => '4.9',
            'badge'       => 'Хіт',
            'category'    => 'ukrainian',
        ],
        [
            'title'       => 'Пляцок Закарпатський',
            'subtitle'    => '350 г',
            'ingredients' => 'Картопля, гриби, цибуля, сметана, кріп, домашнє тісто',
            'price'       => 189,
            'rating'      => '4.8',
            'badge'       => null,
            'category'    => 'ukrainian',
        ],
        [
            'title'       => 'Пашалі Кебаб',
            'subtitle'    => '300 г',
            'ingredients' => 'Яловичина, баранина, цибуля, зіра, коріандр, сума́х, лаваш',
            'price'       => 295,
            'rating'      => '4.9',
            'badge'       => 'Хіт',
            'category'    => 'azerbaijani',
        ],
        [
            'title'       => 'Плов по-Бакинськи',
            'subtitle'    => '400 г',
            'ingredients' => 'Рис басматі, баранина, каштани, курага, шафран, топлене масло',
            'price'       => 265,
            'rating'      => '4.7',
            'badge'       => null,
            'category'    => 'azerbaijani',
        ],
        [
            'title'       => 'Котлета по-Київськи',
            'subtitle'    => '250 г + гарнір',
            'ingredients' => 'Куряче філе, вершкове масло, зелень, панірувальні сухарі, картопля фрі',
            'price'       => 225,
            'rating'      => '4.8',
            'badge'       => null,
            'category'    => 'ukrainian',
        ],
        [
            'title'       => 'Долма з виноградних листків',
            'subtitle'    => '6 шт / 300 г',
            'ingredients' => 'Рис, яловичина, виноградні листки, м\'ята, кориця, мацоні',
            'price'       => 215,
            'rating'      => '4.6',
            'badge'       => 'Новинка',
            'category'    => 'azerbaijani',
        ],
        [
            'title'       => 'Шашлик з телятини',
            'subtitle'    => '350 г',
            'ingredients' => 'Телятина маринована, цибуля, лимон, спеції, свіжі овочі на грилі',
            'price'       => 345,
            'rating'      => '4.9',
            'badge'       => 'Хіт',
            'category'    => 'grill',
        ],
        [
            'title'       => 'Медівник з маком',
            'subtitle'    => '200 г',
            'ingredients' => 'Пшеничне борошно, мед, мак, волоський горіх, вершковий крем',
            'price'       => 95,
            'rating'      => '4.7',
            'badge'       => 'Новинка',
            'category'    => 'dessert',
        ],
    ];

    $promos = [
        [
            'title'        => 'Обід для двох',
            'subtitle'     => 'Борщ + Котлета по-Київськи + 2 напої + десерт',
            'price'        => 499,
            'old_price'    => 668,
            'discount_pct' => 25,
            'image_hint'   => 'combo-lunch',
        ],
        [
            'title'        => 'Азербайджанський вечір',
            'subtitle'     => 'Пляцок + Кебаб + Долма + Чай з чабрецем',
            'price'        => 649,
            'old_price'    => 864,
            'discount_pct' => 25,
            'image_hint'   => 'combo-azerbaijan',
        ],
        [
            'title'        => 'Сімейний плов',
            'subtitle'     => 'Плов по-Бакинськи (великий) + Долма + 4 лаваші + Мацоні',
            'price'        => 890,
            'old_price'    => 1180,
            'discount_pct' => 24,
            'image_hint'   => 'combo-family',
        ],
        [
            'title'        => 'Гриль-сет',
            'subtitle'     => 'Шашлик телятина + Кебаб + Овочі гриль + Соуси 3 шт + Хліб',
            'price'        => 720,
            'old_price'    => 940,
            'discount_pct' => 23,
            'image_hint'   => 'combo-grill',
        ],
    ];

    $reviews = [
        [
            'name'     => 'Олена Ковальчук',
            'text'     => 'Замовляю борщ щотижня — він тут як у бабусі, навіть краще! Доставка завжди вчасно, їжа гаряча. Дуже рекомендую пляцок із грибами.',
            'rating'   => 5,
            'initials' => 'ОК',
        ],
        [
            'name'     => 'Рустам Алієв',
            'text'     => 'Плов і долма — просто шедевр. Шафранний аромат, правильне м\'ясо, справжній смак Баку. Приємно знайти таку автентику в Києві.',
            'rating'   => 5,
            'initials' => 'РА',
        ],
        [
            'name'     => 'Марія Дмитренко',
            'text'     => 'Бронювала стіл онлайн — все чудово. Атмосфера затишна, персонал уважний. Котлета по-Київськи з хрусткою скоринкою — просто казка!',
            'rating'   => 5,
            'initials' => 'МД',
        ],
        [
            'name'     => 'Тарас Гриценко',
            'text'     => 'Взяв гриль-сет на компанію — усі в захваті. М\'ясо ніжне, соуси чудові. Медівник з маком — ідеальне завершення вечора. Повернемось!',
            'rating'   => 5,
            'initials' => 'ТГ',
        ],
    ];

    $steps = [
        ['number'=>'01','title'=>'Українська кухня',      'subtitle'=>'Борщ, вареники та інше',       'art'=>'radial-gradient(circle at 50% 55%,rgba(245,230,200,.65) 0%,transparent 22%),radial-gradient(circle at 50% 50%,#8b1a1a 0%,#c53030 38%,#6b1010 65%,#2a0808 100%)'],
        ['number'=>'02','title'=>'Азербайджанська кухня', 'subtitle'=>'Плов, кебаби, садж',            'art'=>'radial-gradient(circle at 45% 38%,rgba(255,220,100,.45) 0%,transparent 26%),radial-gradient(circle at 50% 50%,#1e1200 0%,#5a3e08 40%,#8b6914 65%,#c4a35a 100%)'],
        ['number'=>'03','title'=>'Комбо-набори',           'subtitle'=>'Для компанії та сім\'ї',        'art'=>'radial-gradient(circle at 35% 40%,rgba(220,200,120,.5) 0%,transparent 22%),radial-gradient(circle at 65% 60%,rgba(180,80,20,.4) 0%,transparent 22%),radial-gradient(circle at 50% 50%,#201408 0%,#6a5030 45%,#d4c89a 90%)'],
        ['number'=>'04','title'=>'Доставка',               'subtitle'=>'Швидко та гаряче',              'art'=>'radial-gradient(circle at 60% 40%,rgba(196,163,90,.5) 0%,transparent 28%),radial-gradient(circle at 50% 50%,#0a0602 0%,#2a1a08 50%,#6a4a1a 80%)'],
        ['number'=>'05','title'=>'Атмосфера ресторану',   'subtitle'=>'Затишок і гостинність',         'art'=>'radial-gradient(ellipse 80% 60% at 35% 45%,rgba(200,130,20,.55),rgba(10,8,4,.9) 65%),radial-gradient(ellipse 40% 80% at 80% 60%,rgba(120,60,10,.4),transparent 55%)'],
    ];

    $catIcons = [
        ['label'=>'Українська кухня',     'icon'=>'<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.4"><ellipse cx="16" cy="18" rx="11" ry="7"/><path d="M5 18 Q16 8 27 18"/><path d="M16 11 V6"/><path d="M13 8 Q16 5 19 8"/><line x1="8" y1="22" x2="24" y2="22"/></svg>'],
        ['label'=>'Азербайджанська кухня','icon'=>'<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.4"><circle cx="16" cy="16" r="10"/><path d="M16 6 L18 13 L16 11 L14 13 Z"/><path d="M26 16 L19 18 L21 16 L19 14 Z"/><path d="M16 26 L14 19 L16 21 L18 19 Z"/><path d="M6 16 L13 14 L11 16 L13 18 Z"/><circle cx="16" cy="16" r="4"/></svg>'],
        ['label'=>'Гриль / Мангал',       'icon'=>'<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M8 16 Q10 10 12 16 Q14 22 16 16 Q18 10 20 16 Q22 22 24 16"/><ellipse cx="16" cy="22" rx="9" ry="3"/><line x1="16" y1="25" x2="16" y2="29"/><line x1="11" y1="27" x2="21" y2="27"/></svg>'],
        ['label'=>'Супи',                  'icon'=>'<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M6 14 h20 v2 a10 10 0 01-20 0 Z"/><path d="M10 14 V10 a2 2 0 014 0"/><path d="M16 14 V8 a2 2 0 014 0"/><line x1="9" y1="26" x2="23" y2="26"/><line x1="16" y1="24" x2="16" y2="26"/></svg>'],
        ['label'=>'Випічка',               'icon'=>'<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M8 20 Q6 14 10 10 Q14 6 18 10 Q22 8 24 12 Q28 16 22 20 Z"/><path d="M10 20 Q12 23 16 22 Q20 21 22 20"/></svg>'],
        ['label'=>'Десерти',               'icon'=>'<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="8" y="18" width="16" height="8" rx="2"/><rect x="10" y="12" width="12" height="6" rx="2"/><rect x="12" y="7" width="8" height="5" rx="2"/><line x1="6" y1="26" x2="26" y2="26"/></svg>'],
        ['label'=>'Напої',                 'icon'=>'<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.4"><path d="M10 10 h12 l-2 16 H12 Z"/><path d="M22 14 h3 a3 3 0 010 6 h-3"/><path d="M8 6 Q10 4 12 6 Q14 4 16 6"/></svg>'],
        ['label'=>'Комбо-набори',          'icon'=>'<svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="7" y="13" width="18" height="14" rx="2"/><path d="M7 13 h18 v-3 a2 2 0 00-2-2 H9 a2 2 0 00-2 2 Z"/><line x1="16" y1="8" x2="16" y2="27"/><line x1="7" y1="17" x2="25" y2="17"/><path d="M13 8 Q13 5 16 5 Q19 5 19 8"/></svg>'],
    ];

    $chefs = [
        [
            'name'    => 'Василь Кравченко',
            'role'    => 'Шеф-кухар',
            'cuisine' => 'Українська кухня',
            'initial' => 'В',
        ],
        [
            'name'    => 'Нізамі Гасанов',
            'role'    => 'Шеф-кухар',
            'cuisine' => 'Азербайджанська кухня',
            'initial' => 'Н',
        ],
    ];

    $stats = [
        ['value' => '5+',   'label' => 'Років досвіду'],
        ['value' => '120+', 'label' => 'Страв у меню'],
        ['value' => '4800', 'label' => 'Задоволених гостей'],
        ['value' => '98%',  'label' => 'Позитивних відгуків'],
    ];

    $qualities = [
        'Тільки свіжі продукти від перевірених постачальників',
        'Сімейні рецепти, передані через покоління',
        'Жодних штучних барвників та консервантів',
        'Приготування одразу після замовлення',
    ];
@endphp

<!-- ===================== HEADER ===================== -->
<header class="gf-header" id="gf-header">
    <div class="gf-header-inner">

        <a href="/services/food" class="gf-logo" aria-label="BiKuBe Їжа — на головну">
            <span class="gf-logo-crest" aria-hidden="true">
                <svg width="40" height="40" viewBox="0 0 40 40" fill="none">
                    <circle cx="20" cy="20" r="19" stroke="currentColor" stroke-width="1"/>
                    <path d="M20 6 L23 13 L31 13 L25 18 L27 26 L20 21 L13 26 L15 18 L9 13 L17 13 Z" fill="currentColor" opacity=".85"/>
                    <circle cx="20" cy="20" r="5" stroke="currentColor" stroke-width="1" fill="none" opacity=".5"/>
                </svg>
            </span>
            <span class="gf-logo-textblock">
                <span class="gf-logo-main">BiKuBe Їжа</span>
                <span class="gf-logo-sub">Кухні світу — битва смаків</span>
            </span>
        </a>

        <nav class="gf-nav" aria-label="Головна навігація">
            <ul class="gf-nav-list" role="list">
                <li><a href="/services/food" class="gf-nav-link">Головна</a></li>
                <li><a href="#menu" class="gf-nav-link">Меню</a></li>
                <li><a href="#delivery" class="gf-nav-link">Доставка</a></li>
                <li><a href="#booking" class="gf-nav-link">Бронювання</a></li>
                <li><a href="#about" class="gf-nav-link">Про нас</a></li>
                <li><a href="#promos" class="gf-nav-link">Акції</a></li>
                <li><a href="#contacts" class="gf-nav-link">Контакти</a></li>
            </ul>
        </nav>

        <div class="gf-header-actions">
            <div class="gf-lang-toggle" aria-label="Мова">
                <button class="gf-lang-btn active" type="button">UA</button>
                <button class="gf-lang-btn" type="button">AZ</button>
            </div>
            <a href="/login" class="gf-header-login">Увійти</a>
            <a href="/register" class="gf-header-register">Реєстрація</a>
            <button class="gf-icon-btn" aria-label="Обране" type="button">
                <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                </svg>
            </button>
            <button class="gf-icon-btn gf-cart-btn" aria-label="Кошик" type="button">
                <svg aria-hidden="true" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="gf-cart-count" aria-live="polite">0</span>
            </button>
            <button class="gf-burger" aria-label="Відкрити меню" aria-expanded="false" type="button">
                <span></span><span></span><span></span>
            </button>
        </div>

    </div>
</header>

<!-- ===================== HERO ===================== -->
<section class="gf-hero" aria-label="Головний банер">

    <div class="gf-hero-bg" aria-hidden="true">
        <div class="gf-hero-bg-overlay"></div>
        <div class="gf-hero-bg-img"></div>
        <div class="gf-hero-particles"></div>
    </div>

    <div class="gf-hero-content">

        <div class="gf-hero-copy">
            <div class="gf-hero-eyebrow">
                <span class="gf-eyebrow-dot"></span>
                Українська &amp; Азербайджанська кухня
            </div>
            <h1 class="gf-hero-h1">
                Дві кухні —<br>
                <span class="gf-hero-accent">один смачний світ</span>
            </h1>
            <p class="gf-hero-sub">
                Автентичні рецепти двох великих кулінарних традицій. Готуємо зі свіжих інгредієнтів щодня — від борщу до пашалі кебабу.
            </p>
            <div class="gf-hero-ctas">
                <a href="#menu" class="gf-btn gf-btn-primary">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    Замовити доставку
                </a>
                <a href="#booking" class="gf-btn gf-btn-secondary">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    Забронювати стіл
                </a>
                <a href="#menu" class="gf-btn gf-btn-ghost">Дивитися меню →</a>
            </div>
            <div class="gf-hero-dots" role="tablist" aria-label="Слайди">
                <button class="gf-dot gf-dot--active" role="tab" aria-selected="true"  aria-label="Слайд 1" type="button"></button>
                <button class="gf-dot"               role="tab" aria-selected="false" aria-label="Слайд 2" type="button"></button>
                <button class="gf-dot"               role="tab" aria-selected="false" aria-label="Слайд 3" type="button"></button>
            </div>
        </div>

        <div class="gf-hero-card" aria-label="Рекомендація дня">
            <div class="gf-hero-card-label">
                <span aria-hidden="true">⭐</span> Сьогодні рекомендуємо
            </div>
            <div class="gf-hero-card-art gf-dish-art-1" role="img" aria-label="{{ $dishes[0]['title'] }}"></div>
            <div class="gf-hero-card-body">
                <div class="gf-hero-card-badge">{{ $dishes[0]['badge'] }}</div>
                <h3 class="gf-hero-card-title">{{ $dishes[0]['title'] }}</h3>
                <p class="gf-hero-card-weight">{{ $dishes[0]['subtitle'] }}</p>
                <p class="gf-hero-card-ing">{{ $dishes[0]['ingredients'] }}</p>
                <div class="gf-hero-card-footer">
                    <div class="gf-hero-card-rating" aria-label="Рейтинг {{ $dishes[0]['rating'] }} із 5">
                        <span aria-hidden="true">★★★★★</span> {{ $dishes[0]['rating'] }}
                    </div>
                    <div class="gf-hero-card-price">{{ $dishes[0]['price'] }} ₴</div>
                    <button class="gf-add-btn" type="button">В кошик +</button>
                </div>
            </div>
        </div>

    </div>

    <div class="gf-hero-scroll-hint" aria-hidden="true">
        <span>Гортайте вниз</span>
        <svg width="14" height="20" viewBox="0 0 14 20" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="1" y="1" width="12" height="18" rx="6"/>
            <line x1="7" y1="5" x2="7" y2="9"/>
        </svg>
    </div>

</section>

<!-- ===================== HOW IT WORKS (STEPS) ===================== -->
<section class="gf-steps" aria-labelledby="gf-steps-heading">
    <div class="gf-steps-inner">

        <div class="gf-section-header">
            <h2 class="gf-section-title" id="gf-steps-heading">Як це працює</h2>
            <p class="gf-section-sub">Замовити їжу просто — 5 кроків до смачного обіду</p>
        </div>

        <div class="gf-steps-track" role="list">
            @foreach ($steps as $step)
            <div class="gf-step-card" role="listitem">
                <div class="gf-step-img" style="background:{{ $step['art'] }}" aria-hidden="true">
                    <span class="gf-step-num-overlay">{{ $step['number'] }}</span>
                </div>
                <div class="gf-step-body">
                    <h3 class="gf-step-title">{{ $step['title'] }}</h3>
                    <p class="gf-step-sub">{{ $step['subtitle'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</section>

<!-- ===================== CATEGORY ICONS ===================== -->
<section class="gf-caticons" aria-labelledby="gf-cat-heading">
    <div class="gf-caticons-inner">

        <h2 class="gf-section-title" id="gf-cat-heading">Швидкий вибір</h2>

        <div class="gf-caticons-grid" role="list">
            @foreach ($catIcons as $cat)
            <a href="#menu" class="gf-caticon-tile" role="listitem" aria-label="Категорія: {{ $cat['label'] }}">
                <span class="gf-caticon-svg" aria-hidden="true">{!! $cat['icon'] !!}</span>
                <span class="gf-caticon-label">{{ $cat['label'] }}</span>
            </a>
            @endforeach
        </div>

    </div>
</section>

<!-- ===================== MENU ===================== -->
<section class="gf-menu" id="menu" aria-labelledby="gf-menu-heading">
    <div class="gf-menu-inner">

        <div class="gf-section-header">
            <h2 class="gf-section-title" id="gf-menu-heading">
                <span aria-hidden="true">🔥</span> Популярні страви
            </h2>
            <p class="gf-section-sub">Найулюбленіші страви наших гостей — свіжі, смачні, автентичні</p>
        </div>

        <div class="gf-menu-filters" role="tablist" aria-label="Фільтрація меню">
            <button class="gf-filter-tab gf-filter-tab--active" role="tab" aria-selected="true"  data-filter="all"         type="button">Усі</button>
            <button class="gf-filter-tab"                        role="tab" aria-selected="false" data-filter="hit"         type="button"><span aria-hidden="true">⭐</span> Хіт продажів</button>
            <button class="gf-filter-tab"                        role="tab" aria-selected="false" data-filter="ukrainian"   type="button"><span aria-hidden="true">🇺🇦</span> Українська кухня</button>
            <button class="gf-filter-tab"                        role="tab" aria-selected="false" data-filter="azerbaijani" type="button"><span aria-hidden="true">🇦🇿</span> Азербайджанська кухня</button>
            <button class="gf-filter-tab"                        role="tab" aria-selected="false" data-filter="grill"       type="button"><span aria-hidden="true">🔥</span> Гриль</button>
            <button class="gf-filter-tab"                        role="tab" aria-selected="false" data-filter="soup"        type="button">Супи</button>
            <button class="gf-filter-tab"                        role="tab" aria-selected="false" data-filter="dessert"     type="button">Десерти</button>
            <a href="#menu" class="gf-filter-tab gf-filter-tab--ghost">Переглянути все →</a>
        </div>

        <div class="gf-dishes-grid" role="list" id="gf-dishes-grid">
            @foreach ($dishes as $dish)
            <article
                class="gf-dish-card"
                role="listitem"
                data-cat="{{ $dish['category'] }}"
                data-badge="{{ $dish['badge'] ?? '' }}"
                aria-label="{{ $dish['title'] }}, {{ $dish['price'] }} гривень"
            >
                <div
                    class="gf-dish-art gf-dish-art-{{ $loop->index + 1 }}"
                    role="img"
                    aria-label="Фото страви: {{ $dish['title'] }}"
                ></div>

                @if ($dish['badge'])
                <div class="gf-dish-badge gf-dish-badge--{{ $dish['badge'] === 'Хіт' ? 'hit' : 'new' }}" aria-label="{{ $dish['badge'] }}">
                    {{ $dish['badge'] }}
                </div>
                @endif

                <button class="gf-dish-fav" aria-label="Додати до обраного: {{ $dish['title'] }}" type="button">
                    <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                    </svg>
                </button>

                <div class="gf-dish-body">
                    <div class="gf-dish-cat-tag">
                        @if ($dish['category'] === 'ukrainian') 🇺🇦 Українська
                        @elseif ($dish['category'] === 'azerbaijani') 🇦🇿 Азербайджанська
                        @elseif ($dish['category'] === 'grill') 🔥 Гриль
                        @elseif ($dish['category'] === 'dessert') 🍮 Десерт
                        @else {{ $dish['category'] }}
                        @endif
                    </div>
                    <h3 class="gf-dish-title">{{ $dish['title'] }}</h3>
                    <p class="gf-dish-sub">{{ $dish['subtitle'] }}</p>
                    <p class="gf-dish-ing">{{ $dish['ingredients'] }}</p>
                    <div class="gf-dish-footer">
                        <div class="gf-dish-rating" aria-label="Рейтинг {{ $dish['rating'] }} із 5">
                            <span aria-hidden="true">★★★★★</span> {{ $dish['rating'] }}
                        </div>
                        <div class="gf-dish-price">
                            <span class="gf-dish-price-val">{{ $dish['price'] }}</span>
                            <span class="gf-dish-price-cur">₴</span>
                        </div>
                        <button class="gf-add-btn" type="button" aria-label="Додати {{ $dish['title'] }} до кошика">
                            В кошик +
                        </button>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <div class="gf-menu-more">
            <a href="#menu" class="gf-btn gf-btn-outline">Переглянути все меню (120+ страв)</a>
        </div>

    </div>
</section>

<!-- ===================== ABOUT BANNER ===================== -->
<section class="gf-about-banner" aria-labelledby="gf-about-banner-heading">
    <div class="gf-about-banner-inner">

        <div class="gf-about-banner-media">
            <div class="gf-about-banner-img" role="img" aria-label="Наш ресторан та кухня">
                <img
                    src="/images/bikube/home/v2/category-delivery.png"
                    alt="Доставка BiKuBe — свіжа їжа до вашого порогу"
                    loading="lazy"
                    width="560"
                    height="400"
                >
            </div>
            <button class="gf-play-btn" aria-label="Дивитися відео про ресторан" type="button">
                <svg aria-hidden="true" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="5 3 19 12 5 21 5 3"/>
                </svg>
            </button>
            <div class="gf-about-banner-badge" aria-hidden="true">
                <span class="gf-rotating-text">BIKUBE · ЇЖА · ДОСТАВКА · РЕСТОРАН ·</span>
            </div>
        </div>

        <div class="gf-about-banner-copy">
            <div class="gf-eyebrow-tag">Наша історія</div>
            <h2 class="gf-section-title" id="gf-about-banner-heading">
                Де зустрічаються дві великі кулінарні традиції
            </h2>
            <p class="gf-about-banner-text">
                BiKuBe Їжа народилася з любові до двох культур. Ми поєднали теплоту та щедрість <strong>української кухні</strong> — борщі, вареники, котлети — із багатством і ароматом <strong>азербайджанської кулінарії</strong>: пловами, кебабами, долмою.
            </p>
            <p class="gf-about-banner-text">
                Кожна страва готується з душею. Наші шефи — носії справжніх сімейних рецептів, що передавались через покоління. Ми не йдемо на компроміси в якості — тільки свіжі продукти, тільки справжній смак.
            </p>
            <ul class="gf-about-features" role="list">
                <li class="gf-about-feature">
                    <span class="gf-about-feature-icon" aria-hidden="true">📖</span>
                    <div>
                        <strong>Сімейні рецепти</strong>
                        <span>Автентика, що передається з покоління в покоління</span>
                    </div>
                </li>
                <li class="gf-about-feature">
                    <span class="gf-about-feature-icon" aria-hidden="true">🌿</span>
                    <div>
                        <strong>Натуральні інгредієнти</strong>
                        <span>Без консервантів, барвників та підсилювачів смаку</span>
                    </div>
                </li>
                <li class="gf-about-feature">
                    <span class="gf-about-feature-icon" aria-hidden="true">🤝</span>
                    <div>
                        <strong>Гостинність</strong>
                        <span>Кожен гість — бажаний і особливий</span>
                    </div>
                </li>
                <li class="gf-about-feature">
                    <span class="gf-about-feature-icon" aria-hidden="true">🏆</span>
                    <div>
                        <strong>Якість</strong>
                        <span>Контроль на кожному етапі — від постачальника до тарілки</span>
                    </div>
                </li>
            </ul>
        </div>

    </div>
</section>

<!-- ===================== PROMOS ===================== -->
<section class="gf-promos" id="promos" aria-labelledby="gf-promos-heading">
    <div class="gf-promos-inner">

        <div class="gf-section-header">
            <h2 class="gf-section-title" id="gf-promos-heading">
                <span aria-hidden="true">🎁</span> Акції та комбо-пропозиції
            </h2>
            <p class="gf-section-sub">Вигідні набори для повноцінного обіду або вечері</p>
        </div>

        <div class="gf-promos-grid" role="list">
            @foreach ($promos as $promo)
            <article class="gf-promo-card" role="listitem" aria-label="{{ $promo['title'] }}, знижка {{ $promo['discount_pct'] }}%">
                <div class="gf-promo-discount" aria-label="Знижка {{ $promo['discount_pct'] }} відсотків">
                    -{{ $promo['discount_pct'] }}%
                </div>
                <div class="gf-promo-art gf-promo-art--{{ $promo['image_hint'] }}" role="img" aria-label="{{ $promo['title'] }}"></div>
                <div class="gf-promo-body">
                    <h3 class="gf-promo-title">{{ $promo['title'] }}</h3>
                    <p class="gf-promo-sub">{{ $promo['subtitle'] }}</p>
                    <div class="gf-promo-pricing">
                        <span class="gf-promo-old-price" aria-label="Стара ціна {{ $promo['old_price'] }} гривень">
                            {{ $promo['old_price'] }} ₴
                        </span>
                        <span class="gf-promo-price" aria-label="Нова ціна {{ $promo['price'] }} гривень">
                            {{ $promo['price'] }} ₴
                        </span>
                    </div>
                    <button class="gf-btn gf-btn-primary gf-promo-cta" type="button">
                        Замовити комбо
                    </button>
                </div>
            </article>
            @endforeach
        </div>

    </div>
</section>

<!-- ===================== LOWER 3-COL: BOOKING + ATMOSPHERE + DELIVERY ===================== -->
<section class="gf-lower" aria-label="Бронювання, атмосфера та доставка">
    <div class="gf-lower-inner">

        <!-- BOOKING -->
        <div class="gf-booking" id="booking">
            <div class="gf-booking-header">
                <span class="gf-booking-icon" aria-hidden="true">📅</span>
                <h2 class="gf-section-title">Бронювання столу</h2>
                <p class="gf-section-sub">Оберіть зручний час — ми підготуємо все до вашого приходу</p>
            </div>
            <form class="gf-booking-form" action="/services/food/booking" method="POST" aria-label="Форма бронювання столу" novalidate>
                @csrf
                <div class="gf-form-row">
                    <div class="gf-form-group">
                        <label for="booking-date" class="gf-form-label">Дата <span aria-hidden="true">*</span></label>
                        <input
                            type="date"
                            id="booking-date"
                            name="date"
                            class="gf-form-input"
                            required
                            aria-required="true"
                            min="{{ date('Y-m-d') }}"
                        >
                    </div>
                    <div class="gf-form-group">
                        <label for="booking-time" class="gf-form-label">Час <span aria-hidden="true">*</span></label>
                        <select id="booking-time" name="time" class="gf-form-input" required aria-required="true">
                            <option value="" disabled selected>Оберіть час</option>
                            <option value="12:00">12:00</option>
                            <option value="12:30">12:30</option>
                            <option value="13:00">13:00</option>
                            <option value="13:30">13:30</option>
                            <option value="14:00">14:00</option>
                            <option value="14:30">14:30</option>
                            <option value="15:00">15:00</option>
                            <option value="18:00">18:00</option>
                            <option value="18:30">18:30</option>
                            <option value="19:00">19:00</option>
                            <option value="19:30">19:30</option>
                            <option value="20:00">20:00</option>
                            <option value="20:30">20:30</option>
                            <option value="21:00">21:00</option>
                        </select>
                    </div>
                </div>
                <div class="gf-form-group">
                    <label for="booking-guests" class="gf-form-label">Кількість гостей <span aria-hidden="true">*</span></label>
                    <select id="booking-guests" name="guests" class="gf-form-input" required aria-required="true">
                        <option value="" disabled selected>Оберіть кількість</option>
                        <option value="1">1 особа</option>
                        <option value="2">2 особи</option>
                        <option value="3">3 особи</option>
                        <option value="4">4 особи</option>
                        <option value="5">5 осіб</option>
                        <option value="6">6 осіб</option>
                        <option value="7-10">7–10 осіб</option>
                        <option value="10+">Більше 10 осіб</option>
                    </select>
                </div>
                <div class="gf-form-group">
                    <label for="booking-phone" class="gf-form-label">Телефон <span aria-hidden="true">*</span></label>
                    <input
                        type="tel"
                        id="booking-phone"
                        name="phone"
                        class="gf-form-input"
                        placeholder="+38 (0XX) XXX-XX-XX"
                        required
                        aria-required="true"
                        autocomplete="tel"
                    >
                </div>
                <div class="gf-form-group">
                    <label for="booking-name" class="gf-form-label">Ваше ім'я</label>
                    <input
                        type="text"
                        id="booking-name"
                        name="name"
                        class="gf-form-input"
                        placeholder="Як вас звати?"
                        autocomplete="given-name"
                    >
                </div>
                <div class="gf-form-group">
                    <label for="booking-comment" class="gf-form-label">Коментар / побажання</label>
                    <textarea
                        id="booking-comment"
                        name="comment"
                        class="gf-form-input gf-form-textarea"
                        rows="3"
                        placeholder="Алергії, особливі побажання, привід..."
                    ></textarea>
                </div>
                <button type="submit" class="gf-btn gf-btn-primary gf-booking-submit">
                    <svg aria-hidden="true" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.6 3.42 2 2 0 0 1 3.57 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.54a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/>
                    </svg>
                    Забронювати стіл
                </button>
                <p class="gf-booking-note">
                    <svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    Підтвердження надійде SMS протягом 5 хвилин
                </p>
            </form>
        </div>

        <!-- ATMOSPHERE -->
        <div class="gf-atmo" aria-labelledby="gf-atmo-heading">
            <h2 class="gf-section-title" id="gf-atmo-heading">Атмосфера</h2>
            <p class="gf-section-sub">Затишний простір для зустрічей і свят</p>
            <div class="gf-atmo-grid" role="list">
                <div class="gf-atmo-img gf-atmo-img--1 gf-atmo-img--large" role="listitem" aria-label="Інтер'єр ресторану BiKuBe"></div>
                <div class="gf-atmo-img gf-atmo-img--2"                     role="listitem" aria-label="Відкрита кухня ресторану"></div>
                <div class="gf-atmo-img gf-atmo-img--3"                     role="listitem" aria-label="Банкетний зал"></div>
            </div>
            <ul class="gf-atmo-info" role="list">
                <li class="gf-atmo-info-item">
                    <span class="gf-atmo-info-icon" aria-hidden="true">🪑</span>
                    <span>80 посадкових місць</span>
                </li>
                <li class="gf-atmo-info-item">
                    <span class="gf-atmo-info-icon" aria-hidden="true">🎵</span>
                    <span>Жива музика по п'ятницях</span>
                </li>
                <li class="gf-atmo-info-item">
                    <span class="gf-atmo-info-icon" aria-hidden="true">🌿</span>
                    <span>Літня тераса</span>
                </li>
                <li class="gf-atmo-info-item">
                    <span class="gf-atmo-info-icon" aria-hidden="true">🎂</span>
                    <span>Банкети та святкування</span>
                </li>
            </ul>
        </div>

        <!-- DELIVERY -->
        <div class="gf-delivery" id="delivery" aria-labelledby="gf-delivery-heading">
            <div class="gf-delivery-header">
                <span class="gf-delivery-icon" aria-hidden="true">
                    <img src="/images/bikube/home/v2/category-delivery.png" alt="" width="40" height="40">
                </span>
                <h2 class="gf-section-title" id="gf-delivery-heading">Доставка їжі</h2>
                <p class="gf-section-sub">Швидко, гаряче, надійно — прямо до вашого порогу</p>
            </div>

            <ul class="gf-delivery-info" role="list">
                <li class="gf-delivery-info-item">
                    <span class="gf-delivery-info-icon" aria-hidden="true">⚡</span>
                    <div>
                        <strong>Час доставки</strong>
                        <span>30–60 хвилин залежно від зони</span>
                    </div>
                </li>
                <li class="gf-delivery-info-item">
                    <span class="gf-delivery-info-icon" aria-hidden="true">🛒</span>
                    <div>
                        <strong>Мінімальне замовлення</strong>
                        <span>300 ₴ (доставка від 59 ₴)</span>
                    </div>
                </li>
                <li class="gf-delivery-info-item">
                    <span class="gf-delivery-info-icon" aria-hidden="true">🚚</span>
                    <div>
                        <strong>Безкоштовна доставка</strong>
                        <span>При замовленні від 800 ₴</span>
                    </div>
                </li>
                <li class="gf-delivery-info-item">
                    <span class="gf-delivery-info-icon" aria-hidden="true">💳</span>
                    <div>
                        <strong>Способи оплати</strong>
                        <span>Картка онлайн, готівка, Apple Pay, Google Pay</span>
                    </div>
                </li>
                <li class="gf-delivery-info-item">
                    <span class="gf-delivery-info-icon" aria-hidden="true">📍</span>
                    <div>
                        <strong>Зони доставки</strong>
                        <span>Голосіїво, Печерськ, Шевченківський, Подільський, Оболонь, Дарниця</span>
                    </div>
                </li>
                <li class="gf-delivery-info-item">
                    <span class="gf-delivery-info-icon" aria-hidden="true">📡</span>
                    <div>
                        <strong>Live-трекінг</strong>
                        <span>Відстежуйте статус замовлення в реальному часі в додатку</span>
                    </div>
                </li>
            </ul>

            <div class="gf-delivery-map" role="img" aria-label="Карта зон доставки BiKuBe по Києву">
                <div class="gf-delivery-map-placeholder">
                    <span aria-hidden="true">🗺️</span>
                    <span>Карта зон доставки</span>
                </div>
            </div>

            <a href="#menu" class="gf-btn gf-btn-primary">Замовити зараз</a>
        </div>

    </div>
</section>

<!-- ===================== REVIEWS ===================== -->
<section class="gf-reviews" aria-labelledby="gf-reviews-heading">
    <div class="gf-reviews-inner">

        <div class="gf-section-header">
            <h2 class="gf-section-title" id="gf-reviews-heading">
                <span aria-hidden="true">💬</span> Відгуки наших гостей
            </h2>
            <p class="gf-section-sub">Більше 4800 задоволених гостей — ось що вони кажуть</p>
        </div>

        <div class="gf-reviews-controls">
            <button class="gf-reviews-arrow gf-reviews-arrow--prev" aria-label="Попередній відгук" type="button">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
            <button class="gf-reviews-arrow gf-reviews-arrow--next" aria-label="Наступний відгук" type="button">
                <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </button>
        </div>

        <div class="gf-reviews-track" role="list" aria-live="polite">
            @foreach ($reviews as $review)
            <article class="gf-review-card" role="listitem" aria-label="Відгук від {{ $review['name'] }}">
                <div class="gf-review-stars" aria-label="Оцінка: {{ $review['rating'] }} із 5 зірок">
                    @for ($i = 0; $i < $review['rating']; $i++)
                    <span aria-hidden="true">★</span>
                    @endfor
                </div>
                <blockquote class="gf-review-text">
                    <p>"{{ $review['text'] }}"</p>
                </blockquote>
                <footer class="gf-review-author">
                    <div class="gf-review-avatar" aria-hidden="true">{{ $review['initials'] }}</div>
                    <div class="gf-review-name">{{ $review['name'] }}</div>
                </footer>
            </article>
            @endforeach
        </div>

        <div class="gf-reviews-write">
            <a href="#" class="gf-btn gf-btn-outline">Залишити відгук</a>
        </div>

    </div>
</section>

<!-- ===================== GALLERY ===================== -->
<section class="gf-gallery" aria-labelledby="gf-gallery-heading">
    <div class="gf-gallery-inner">

        <div class="gf-section-header">
            <h2 class="gf-section-title" id="gf-gallery-heading">Атмосфера BiKuBe</h2>
            <p class="gf-section-sub">Зазирніть на нашу кухню та в зал ресторану</p>
        </div>

        <div class="gf-gallery-grid" role="list">
            <div class="gf-gallery-item gf-gallery-item--1" role="listitem">
                <div class="gf-gallery-art gf-gallery-art--1" role="img" aria-label="Страва ресторану BiKuBe"></div>
            </div>
            <div class="gf-gallery-item gf-gallery-item--2" role="listitem">
                <div class="gf-gallery-art gf-gallery-art--2" role="img" aria-label="Кухня ресторану"></div>
            </div>
            <div class="gf-gallery-item gf-gallery-item--3 gf-gallery-item--center" role="listitem">
                <div class="gf-gallery-art gf-gallery-art--3" role="img" aria-label="Шеф-кухар за роботою"></div>
                <button class="gf-gallery-play" aria-label="Дивитися відео про кухню BiKuBe" type="button">
                    <svg aria-hidden="true" width="32" height="32" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="5 3 19 12 5 21 5 3"/>
                    </svg>
                </button>
            </div>
            <div class="gf-gallery-item gf-gallery-item--4" role="listitem">
                <div class="gf-gallery-art gf-gallery-art--4" role="img" aria-label="Інтер'єр ресторану"></div>
            </div>
            <div class="gf-gallery-item gf-gallery-item--5" role="listitem">
                <div class="gf-gallery-art gf-gallery-art--5" role="img" aria-label="Страви азербайджанської кухні"></div>
            </div>
            <div class="gf-gallery-item gf-gallery-item--6" role="listitem">
                <div class="gf-gallery-art gf-gallery-art--6" role="img" aria-label="Десерти та напої"></div>
            </div>
        </div>

        <div class="gf-gallery-social">
            <span>Стежте за нами в Instagram</span>
            <a href="https://instagram.com/bikube_food" class="gf-social-link" target="_blank" rel="noopener noreferrer" aria-label="BiKuBe Їжа в Instagram">
                @bikube_food
            </a>
        </div>

    </div>
</section>

<!-- ===================== ABOUT US ===================== -->
<section class="gf-about-us" id="about" aria-labelledby="gf-about-us-heading">
    <div class="gf-about-us-inner">

        <!-- COL 1: About text + Stats -->
        <div class="gf-about-text-col">
            <div class="gf-eyebrow-tag">Про нас</div>
            <h2 class="gf-section-title" id="gf-about-us-heading">BiKuBe Їжа — більше ніж ресторан</h2>
            <p class="gf-about-us-p">
                Ми — команда однодумців, закоханих у кулінарну культуру України та Азербайджану. З 2019 року ми несемо автентичні смаки до столів наших гостей — у залі ресторану чи вдома.
            </p>
            <p class="gf-about-us-p">
                Наше меню — живий організм: воно оновлюється з сезонами та кухарськими знахідками. Але борщ, плов і кебаб — незмінні. Адже є страви, що стали частиною нашої ідентичності.
            </p>
            <div class="gf-stats-grid" role="list" aria-label="Наші досягнення">
                @foreach ($stats as $stat)
                <div class="gf-stat" role="listitem">
                    <span class="gf-stat-val">{{ $stat['value'] }}</span>
                    <span class="gf-stat-label">{{ $stat['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <!-- COL 2: Chefs -->
        <div class="gf-chefs-col">
            <div class="gf-eyebrow-tag">Наші шефи</div>
            <h3 class="gf-chefs-heading">Майстри двох кухонь</h3>
            <div class="gf-chefs-list" role="list">
                @foreach ($chefs as $chef)
                <div class="gf-chef-card" role="listitem" aria-label="Шеф-кухар {{ $chef['name'] }}">
                    <div class="gf-chef-avatar" aria-hidden="true">{{ $chef['initial'] }}</div>
                    <div class="gf-chef-info">
                        <h4 class="gf-chef-name">{{ $chef['name'] }}</h4>
                        <p class="gf-chef-role">{{ $chef['role'] }}</p>
                        <p class="gf-chef-cuisine">{{ $chef['cuisine'] }}</p>
                    </div>
                    <p class="gf-chef-quote">
                        @if ($loop->index === 0)
                            "Борщ — це не просто суп. Це душа України в тарілці."
                        @else
                            "Плов потребує терпіння та любові. Решта — прийде."
                        @endif
                    </p>
                </div>
                @endforeach
            </div>
        </div>

        <!-- COL 3: Quality checklist -->
        <div class="gf-quality-col">
            <div class="gf-eyebrow-tag">Наші принципи</div>
            <h3 class="gf-quality-heading">Чому обирають нас</h3>
            <ul class="gf-quality-list" role="list">
                @foreach ($qualities as $quality)
                <li class="gf-quality-item" role="listitem">
                    <span class="gf-quality-check" aria-hidden="true">✓</span>
                    <span>{{ $quality }}</span>
                </li>
                @endforeach
            </ul>

            <div class="gf-certifications">
                <h4 class="gf-cert-heading">Сертифікати та нагороди</h4>
                <ul class="gf-cert-list" role="list">
                    <li class="gf-cert-item"><span aria-hidden="true">🏅</span> Найкращий ресторан Подолу 2023</li>
                    <li class="gf-cert-item"><span aria-hidden="true">🌟</span> Top-10 доставки їжі Києва 2024</li>
                    <li class="gf-cert-item"><span aria-hidden="true">✅</span> Сертифікат харчової безпеки ISO 22000</li>
                </ul>
            </div>

            <div class="gf-about-cta-group">
                <a href="#booking" class="gf-btn gf-btn-primary">Забронювати стіл</a>
                <a href="#menu"    class="gf-btn gf-btn-outline">Переглянути меню</a>
            </div>
        </div>

    </div>
</section>

<!-- ===================== FOOTER ===================== -->
<footer class="gf-footer" id="contacts" role="contentinfo">

    <div class="gf-footer-top">
        <div class="gf-footer-inner">

            <!-- Logo + tagline + social -->
            <div class="gf-footer-brand">
                <a href="/services/food" class="gf-logo" aria-label="BiKuBe Їжа — на головну">
                    <span class="gf-logo-icon" aria-hidden="true">
                        <img src="/images/bikube/home/v2/category-food.png" alt="" width="32" height="32">
                    </span>
                    <span class="gf-logo-text">BiKuBe <strong>Їжа</strong></span>
                </a>
                <p class="gf-footer-tagline">
                    Українська &amp; Азербайджанська кухня.<br>
                    Доставка та ресторан у Києві.
                </p>
                <nav class="gf-social-row" aria-label="Соціальні мережі">
                    <a href="https://instagram.com/bikube_food" class="gf-social-icon" target="_blank" rel="noopener noreferrer" aria-label="Instagram BiKuBe Їжа">
                        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                            <circle cx="12" cy="12" r="4"/>
                            <circle cx="17.5" cy="6.5" r="0.5" fill="currentColor"/>
                        </svg>
                    </a>
                    <a href="https://facebook.com/bikubefood" class="gf-social-icon" target="_blank" rel="noopener noreferrer" aria-label="Facebook BiKuBe Їжа">
                        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/>
                        </svg>
                    </a>
                    <a href="https://t.me/bikube_food" class="gf-social-icon" target="_blank" rel="noopener noreferrer" aria-label="Telegram BiKuBe Їжа">
                        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <line x1="22" y1="2" x2="11" y2="13"/>
                            <polygon points="22 2 15 22 11 13 2 9 22 2"/>
                        </svg>
                    </a>
                    <a href="viber://chat?number=380XXXXXXXXX" class="gf-social-icon" aria-label="Viber BiKuBe Їжа">
                        <svg aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </a>
                </nav>
            </div>

            <!-- Nav col 1: Menu -->
            <nav class="gf-footer-nav" aria-label="Меню ресторану">
                <h3 class="gf-footer-nav-heading">Меню</h3>
                <ul class="gf-footer-nav-list" role="list">
                    <li><a href="#menu" class="gf-footer-nav-link">Українська кухня</a></li>
                    <li><a href="#menu" class="gf-footer-nav-link">Азербайджанська кухня</a></li>
                    <li><a href="#menu" class="gf-footer-nav-link">Гриль та м'ясо</a></li>
                    <li><a href="#menu" class="gf-footer-nav-link">Супи та закуски</a></li>
                    <li><a href="#menu" class="gf-footer-nav-link">Десерти</a></li>
                    <li><a href="#menu" class="gf-footer-nav-link">Напої</a></li>
                    <li><a href="#promos" class="gf-footer-nav-link">Комбо-пропозиції</a></li>
                </ul>
            </nav>

            <!-- Nav col 2: Information -->
            <nav class="gf-footer-nav" aria-label="Інформація">
                <h3 class="gf-footer-nav-heading">Інформація</h3>
                <ul class="gf-footer-nav-list" role="list">
                    <li><a href="#about"    class="gf-footer-nav-link">Про нас</a></li>
                    <li><a href="#booking"  class="gf-footer-nav-link">Бронювання</a></li>
                    <li><a href="#delivery" class="gf-footer-nav-link">Умови доставки</a></li>
                    <li><a href="/privacy"  class="gf-footer-nav-link">Політика конфіденційності</a></li>
                    <li><a href="/terms"    class="gf-footer-nav-link">Публічна оферта</a></li>
                    <li><a href="/services/food/franchise" class="gf-footer-nav-link">Франшиза</a></li>
                    <li><a href="/services/food/careers"   class="gf-footer-nav-link">Вакансії</a></li>
                </ul>
            </nav>

            <!-- Nav col 3: Contacts -->
            <address class="gf-footer-contacts" aria-label="Контакти">
                <h3 class="gf-footer-nav-heading">Контакти</h3>
                <ul class="gf-footer-contact-list" role="list">
                    <li class="gf-footer-contact-item">
                        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <span>м. Київ, вул. Хрещатик, 22<br>Метро «Хрещатик»</span>
                    </li>
                    <li class="gf-footer-contact-item">
                        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.6 3.42 2 2 0 0 1 3.57 1h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.54a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/>
                        </svg>
                        <a href="tel:+380441234567" class="gf-footer-tel">+38 (044) 123-45-67</a>
                    </li>
                    <li class="gf-footer-contact-item">
                        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <a href="mailto:food@bikube.ua" class="gf-footer-email">food@bikube.ua</a>
                    </li>
                    <li class="gf-footer-contact-item">
                        <svg aria-hidden="true" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Пн–Пт: 11:00 – 23:00<br>Сб–Нд: 10:00 – 00:00</span>
                    </li>
                </ul>
            </address>

        </div>
    </div>

    <div class="gf-footer-bottom">
        <div class="gf-footer-bottom-inner">
            <p class="gf-copyright">
                &copy; {{ date('Y') }} BiKuBe Platform. Усі права захищені.
            </p>
            <div class="gf-footer-bottom-links">
                <a href="/privacy" class="gf-footer-bottom-link">Конфіденційність</a>
                <span aria-hidden="true">·</span>
                <a href="/terms"   class="gf-footer-bottom-link">Умови використання</a>
                <span aria-hidden="true">·</span>
                <a href="/sitemap" class="gf-footer-bottom-link">Карта сайту</a>
            </div>
            <div class="gf-footer-payments" aria-label="Способи оплати">
                <span class="gf-payment-badge">Visa</span>
                <span class="gf-payment-badge">MC</span>
                <span class="gf-payment-badge">Apple Pay</span>
                <span class="gf-payment-badge">Google Pay</span>
            </div>
        </div>
    </div>

</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {

      // 1. HEADER SCROLL
      var hdr = document.getElementById('gf-header');
      window.addEventListener('scroll', function () {
        if (hdr) hdr.classList.toggle('scrolled', window.scrollY > 50);
      }, { passive: true });

      // 2. SCROLL REVEAL via IntersectionObserver
      var revealEls = document.querySelectorAll('.gf-step-card,.gf-caticon-tile,.gf-dish-card,.gf-promo-card,.gf-review-card,.gf-about-feature,.gf-quality-item,.gf-cert-item,.gf-stat,.gf-chef-card,.gf-gallery-item,.gf-atmo-img,.gf-booking,.gf-delivery,.gf-atmo');
      if ('IntersectionObserver' in window) {
        var ro = new IntersectionObserver(function (entries) {
          entries.forEach(function (e, idx) {
            if (!e.isIntersecting) return;
            e.target.style.animationDelay = (e.target.dataset.di || 0) + 'ms';
            e.target.classList.add('gf-visible');
            ro.unobserve(e.target);
          });
        }, { threshold: 0.1 });
        revealEls.forEach(function (el, i) {
          el.dataset.di = (i % 6) * 70;
          el.style.opacity = '0';
          el.style.transform = 'translateY(22px)';
          el.style.transition = 'opacity .55s ease, transform .55s ease';
          ro.observe(el);
        });
        document.head.insertAdjacentHTML('beforeend', '<style>.gf-visible{opacity:1!important;transform:translateY(0)!important}</style>');
      }

      // 3. PARALLAX HERO BG
      var heroBg = document.querySelector('.gf-hero-bg-img');
      var ticking = false;
      window.addEventListener('scroll', function () {
        if (!ticking) {
          ticking = true;
          requestAnimationFrame(function () {
            if (heroBg && window.scrollY < window.innerHeight * 1.2)
              heroBg.style.transform = 'scale(1.06) translateY(' + (window.scrollY * 0.28) + 'px)';
            ticking = false;
          });
        }
      }, { passive: true });

      // 4. 3D TILT ON DISH CARDS
      document.querySelectorAll('.gf-dish-card').forEach(function (card) {
        card.addEventListener('mousemove', function (e) {
          var r = card.getBoundingClientRect();
          var dx = (e.clientX - r.left - r.width/2) / (r.width/2);
          var dy = (e.clientY - r.top  - r.height/2) / (r.height/2);
          card.style.transform = 'perspective(900px) rotateX(' + (-dy*8).toFixed(1) + 'deg) rotateY(' + (dx*8).toFixed(1) + 'deg) scale(1.03)';
          card.style.transition = 'transform 0.07s linear, border-color .35s, box-shadow .35s';
        });
        card.addEventListener('mouseleave', function () {
          card.style.transition = 'transform 0.5s cubic-bezier(.25,.8,.25,1), border-color .35s, box-shadow .35s, opacity .3s';
          card.style.transform = '';
        });
      });

      // 5. MENU FILTER TABS
      var filterTabs = document.querySelectorAll('.gf-filter-tab[data-filter]');
      var dishCards  = document.querySelectorAll('.gf-dish-card');
      filterTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          filterTabs.forEach(function (t) { t.classList.remove('gf-filter-tab--active'); t.setAttribute('aria-selected','false'); });
          tab.classList.add('gf-filter-tab--active');
          tab.setAttribute('aria-selected','true');
          var f = tab.dataset.filter;
          dishCards.forEach(function (c) {
            c.style.transition = 'opacity .2s, transform .2s';
            c.style.opacity = '0';
            c.style.transform = 'scale(.97)';
          });
          setTimeout(function () {
            dishCards.forEach(function (c) {
              var show = f === 'all'
                || (f === 'hit' && c.dataset.badge === 'Хіт')
                || c.dataset.cat === f;
              c.style.display = show ? '' : 'none';
            });
            requestAnimationFrame(function () {
              dishCards.forEach(function (c) {
                if (c.style.display !== 'none') { c.style.opacity = '1'; c.style.transform = ''; }
              });
            });
          }, 200);
        });
      });

      // 6. ANIMATED COUNTERS on .gf-stat-val
      function easeOut(t){ return 1-Math.pow(1-t,4); }
      var statEls = document.querySelectorAll('.gf-stat-val');
      if ('IntersectionObserver' in window && statEls.length) {
        var so = new IntersectionObserver(function (entries) {
          entries.forEach(function (e) {
            if (!e.isIntersecting) return;
            so.unobserve(e.target);
            var el = e.target;
            var raw = el.textContent.trim();
            var suffix = raw.replace(/[\d.]/g,'');
            var target = parseFloat(raw);
            if (isNaN(target)) return;
            var dur = 1400, t0 = performance.now();
            var isFloat = raw.includes('.');
            (function tick(now){
              var p = Math.min((now-t0)/dur, 1);
              var v = easeOut(p)*target;
              el.textContent = (isFloat ? v.toFixed(1) : Math.round(v)) + suffix;
              if (p < 1) requestAnimationFrame(tick);
            })(t0);
          });
        }, { threshold: 0.5 });
        statEls.forEach(function (el) { so.observe(el); });
      }

      // 7. REVIEWS CAROUSEL + AUTOPLAY
      var track   = document.querySelector('.gf-reviews-track');
      var prevBtn = document.querySelector('.gf-reviews-arrow--prev');
      var nextBtn = document.querySelector('.gf-reviews-arrow--next');
      function cardWidth(){ var c=track&&track.querySelector('.gf-review-card'); return c ? c.offsetWidth+18 : 308; }
      function scrollReviews(dir){ if(track) track.scrollBy({left:dir*cardWidth(),behavior:'smooth'}); }
      if (prevBtn) prevBtn.addEventListener('click', function(){ scrollReviews(-1); });
      if (nextBtn) nextBtn.addEventListener('click', function(){ scrollReviews(1); });
      var reviewAuto = setInterval(function(){
        if(!track) return;
        if(track.scrollLeft >= track.scrollWidth-track.clientWidth-4) track.scrollTo({left:0,behavior:'smooth'});
        else scrollReviews(1);
      }, 5000);
      if(track){
        track.addEventListener('mouseenter', function(){ clearInterval(reviewAuto); });
        track.addEventListener('mouseleave', function(){ reviewAuto=setInterval(function(){ scrollReviews(1); },5000); });
      }

      // 8. BOOKING FORM
      var bform = document.querySelector('.gf-booking-form');
      if (bform) bform.addEventListener('submit', function (e) {
        e.preventDefault();
        var ok = true;
        bform.querySelectorAll('[required]').forEach(function (f) {
          if (!f.value.trim()) {
            ok = false;
            f.classList.add('gf-invalid');
          } else {
            f.classList.remove('gf-invalid');
          }
        });
        if (!ok) return;
        bform.innerHTML = '<div class="gf-booking-success" role="alert">✅ Столик заброньовано!<br><small style="font-family:sans-serif;font-size:14px;color:var(--muted)">Підтвердження надійде SMS протягом 5 хвилин.</small></div>';
      });

      // 9. CART
      var cartCount = document.querySelector('.gf-cart-count');
      var count = 0;
      document.querySelectorAll('.gf-add-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
          count++;
          if (cartCount) { cartCount.textContent = count; cartCount.style.animation='none'; requestAnimationFrame(function(){ cartCount.style.animation=''; }); }
          var orig = btn.textContent;
          btn.textContent = '✓';
          btn.disabled = true;
          setTimeout(function () { btn.textContent = orig; btn.disabled = false; }, 1100);
        });
      });

      // 10. MAGNETIC HOVER on primary buttons
      document.querySelectorAll('.gf-btn-primary').forEach(function (el) {
        el.addEventListener('mousemove', function (e) {
          var r = el.getBoundingClientRect();
          var dx = ((e.clientX-r.left)/r.width-.5)*10;
          var dy = ((e.clientY-r.top)/r.height-.5)*10;
          el.style.transform = 'translate('+dx.toFixed(1)+'px,'+dy.toFixed(1)+'px) translateY(-2px)';
        });
        el.addEventListener('mouseleave', function () {
          el.style.transition = 'transform .45s cubic-bezier(.25,.8,.25,1), background .3s, box-shadow .3s';
          el.style.transform = '';
        });
      });

      // 11. GALLERY PLAY BUTTON — overlay modal
      document.querySelectorAll('.gf-gallery-play,.gf-play-btn').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.preventDefault();
          var ov = document.createElement('div');
          ov.setAttribute('role','dialog');
          ov.setAttribute('aria-modal','true');
          ov.setAttribute('aria-label','Відео BiKuBe');
          ov.style.cssText='position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;display:flex;align-items:center;justify-content:center;cursor:pointer;animation:fadeUp .25s ease';
          ov.innerHTML='<div style="color:#f5e6c8;font-family:Georgia,serif;text-align:center;padding:48px"><span style="font-size:72px">🎬</span><h2 style="font-size:2rem;margin:20px 0 10px;font-weight:400">Відео незабаром</h2><p style="font-family:sans-serif;font-size:15px;color:#9a8a72">Натисніть будь-де або Esc для закриття</p></div>';
          document.body.appendChild(ov);
          document.body.style.overflow='hidden';
          function close(){ ov.remove(); document.body.style.overflow=''; document.removeEventListener('keydown',onEsc); }
          function onEsc(e){ if(e.key==='Escape') close(); }
          ov.addEventListener('click',close);
          document.addEventListener('keydown',onEsc);
          btn.blur();
        });
      });

      // 12. NAV ACTIVE LINK on scroll
      var sections = Array.from(document.querySelectorAll('section[id],div[id]'));
      var navLinks  = document.querySelectorAll('.gf-nav-link');
      if ('IntersectionObserver' in window && navLinks.length) {
        var no = new IntersectionObserver(function (entries) {
          entries.forEach(function (e) {
            if (!e.isIntersecting) return;
            navLinks.forEach(function (l) {
              l.classList.toggle('active', l.getAttribute('href') === '#' + e.target.id);
            });
          });
        }, { rootMargin: '-20% 0px -60% 0px' });
        sections.forEach(function (s) { if(s.id) no.observe(s); });
      }

      // 13. SMOOTH SCROLL for anchor links
      document.querySelectorAll('a[href^="#"]').forEach(function (a) {
        a.addEventListener('click', function (e) {
          var id = a.getAttribute('href');
          if (id.length < 2) return;
          var target = document.querySelector(id);
          if (!target) return;
          e.preventDefault();
          target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
      });

      // 14. FAVOURITE TOGGLE on dish cards
      document.querySelectorAll('.gf-dish-fav').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
          e.stopPropagation();
          btn.classList.toggle('active');
          btn.setAttribute('aria-pressed', btn.classList.contains('active'));
        });
      });

    });
</script>

</body>
</html>
