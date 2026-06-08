<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>BiKuBe — локальная операционная система услуг</title>
        <meta name="description" content="BiKuBe — локальная платформа для Narvik и окрестностей. Доставка, переезды, сервис и партнёры без ненужных обещаний." />
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        <style>
            :root { color-scheme: dark; font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #050807; color: #f8fafc; }
            * { box-sizing: border-box; }
            html, body { margin: 0; min-height: 100%; }
            body { background: radial-gradient(circle at top left, rgba(125,250,146,0.14), transparent 26%), linear-gradient(180deg, #050708 0%, #081118 100%); }
            a { color: inherit; text-decoration: none; }
            .page { display: flex; flex-direction: column; min-height: 100vh; }
            .page__content { flex: 1; }
            .site-header { position: sticky; top: 0; z-index: 50; backdrop-filter: blur(20px); background: rgba(3, 8, 13, 0.92); border-bottom: 1px solid rgba(126,250,146,0.08); box-shadow: 0 4px 16px rgba(0,0,0,0.2); }
            .site-header__inner { max-width: 1200px; margin: 0 auto; padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; gap: 16px; }
            .brand { display: inline-flex; align-items: center; gap: 12px; font-weight: 900; letter-spacing: 0.02em; font-size: 1.1rem; }
            .brand__mark { width: 44px; height: 44px; border-radius: 14px; display: grid; place-items: center; background: linear-gradient(135deg, #7dfa92 0%, #2f9d57 100%); color: #071115; font-size: 1.2rem; font-weight: 900; box-shadow: 0 6px 20px rgba(125,250,146,0.3); }
            .nav-links { display: flex; gap: 24px; flex-wrap: wrap; align-items: center; }
            .nav-links a { color: #cbd5e1; font-size: 0.95rem; font-weight: 500; transition: color .2s ease; letter-spacing: 0.01em; }
            .nav-links a:hover { color: #7dfa92; }
            .header-actions { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
            .button { display: inline-flex; align-items: center; justify-content: center; border-radius: 999px; padding: 12px 22px; font-weight: 700; transition: all .2s ease; font-size: 0.95rem; letter-spacing: 0.01em; }
            .button--primary { background: linear-gradient(135deg, #7dfa92 0%, #34b36b 100%); color: #071115; box-shadow: 0 6px 20px rgba(125,250,146,0.25); }
            .button--primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(125,250,146,0.35); }
            .button--secondary { border: 1.5px solid rgba(126,250,146,0.3); color: #cbd5e1; background: rgba(255,255,255,0.04); }
            .button--secondary:hover { border-color: rgba(126,250,146,0.5); background: rgba(126,250,146,0.08); color: #7dfa92; }
            .hero { position: relative; overflow: hidden; padding: 160px 24px 120px; background: linear-gradient(180deg, rgba(8,18,13,0.3) 0%, transparent 60%); }
            .hero::before { content: ""; position: absolute; inset: 0; background: radial-gradient(circle at 80% 20%, rgba(126,250,146,0.24), transparent 35%), radial-gradient(circle at 20% 80%, rgba(126,250,146,0.1), transparent 45%); pointer-events: none; }
            .hero::after { content: ""; position: absolute; top: 35%; right: -15%; width: 700px; height: 700px; border-radius: 999px; background: radial-gradient(circle, rgba(126,250,146,0.08), transparent); pointer-events: none; }
            .hero__grid { max-width: 1200px; margin: 0 auto; display: grid; gap: 32px; grid-template-columns: 1.05fr 0.95fr; align-items: center; }
            .hero-copy { position: relative; z-index: 1; }
            .badge { display: inline-flex; align-items: center; gap: 10px; padding: 11px 18px; border-radius: 999px; background: linear-gradient(135deg, rgba(126,250,146,0.18), rgba(126,250,146,0.08)); border: 1.5px solid rgba(126,250,146,0.35); color: #9ef5ad; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 20px; font-weight: 600; }
            .hero-copy h1 { margin: 0 0 20px; font-size: clamp(2.8rem, 6vw, 4.2rem); line-height: 1.1; max-width: 16ch; color: #f8fafc; font-weight: 900; letter-spacing: -0.03em; text-shadow: 0 8px 32px rgba(0,0,0,0.4); }
            .hero-copy p { margin: 0 0 32px; max-width: 620px; color: #c8d4e1; font-size: 1.05rem; line-height: 1.8; font-weight: 500; }
            .hero-actions { display: flex; flex-wrap: wrap; gap: 16px; margin-bottom: 40px; }
            .hero-stats { display: grid; gap: 16px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .stat-card { background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); border: 1px solid rgba(126,250,146,0.2); border-radius: 24px; padding: 24px; backdrop-filter: blur(12px); box-shadow: 0 8px 32px rgba(0,0,0,0.2); }
            .stat-card__value { font-size: 1.8rem; font-weight: 900; color: #7dfa92; margin-bottom: 8px; }
            .stat-card__label { color: #a7b6c4; font-size: 0.9rem; line-height: 1.6; font-weight: 500; }
            .hero-panel { position: relative; border-radius: 36px; min-height: 540px; padding: 40px; background: linear-gradient(135deg, rgba(15,35,25,0.8), rgba(5,8,10,0.95)); border: 1px solid rgba(126,250,146,0.15); box-shadow: 0 20px 60px rgba(0,0,0,0.4), inset 0 1px 1px rgba(126,250,146,0.1); backdrop-filter: blur(10px); }
            .hero-panel::before { content: ""; position: absolute; inset: 0; background: radial-gradient(circle at top left, rgba(126,250,146,0.16), transparent 35%), radial-gradient(circle at bottom right, rgba(126,250,146,0.04), transparent 50%); pointer-events: none; border-radius: 36px; }
            .hero-panel::after { content: ""; position: absolute; top: 20px; right: 20px; width: 280px; height: 340px; background: linear-gradient(135deg, rgba(126,250,146,0.15), rgba(126,250,146,0.04)); border: 1px solid rgba(126,250,146,0.2); border-radius: 24px; pointer-events: none; }
            .hero-panel__inner { position: relative; z-index: 2; }
            .hero-panel__title { margin: 0 0 16px; font-size: 1.85rem; line-height: 1.2; color: #f8fafc; font-weight: 800; }
            .hero-panel__text { margin: 0 0 28px; color: #b9c6d3; line-height: 1.8; font-size: 1rem; }
            .hero-panel__list { display: grid; gap: 18px; }
            .hero-panel__item { display: flex; align-items: flex-start; gap: 16px; color: #e2ecf5; background: linear-gradient(135deg, rgba(126,250,146,0.08), transparent); padding: 16px; border-radius: 16px; border: 1px solid rgba(126,250,146,0.1); transition: all .3s ease; }
            .hero-panel__item:hover { background: linear-gradient(135deg, rgba(126,250,146,0.12), rgba(126,250,146,0.02)); border-color: rgba(126,250,146,0.2); }
            .hero-panel__item span { display: inline-flex; align-items: center; justify-content: center; min-width: 40px; min-height: 40px; border-radius: 999px; background: linear-gradient(135deg, #7dfa92, #34b36b); color: #071115; font-weight: 800; font-size: 1.1rem; flex-shrink: 0; }
            .hero-panel__item strong { font-weight: 700; color: #f8fafc; }
            .section { max-width: 1200px; margin: 0 auto; padding: 80px 24px; position: relative; }
            .section + .section { border-top: 1px solid rgba(126,250,146,0.08); }
            .section__head { display: flex; justify-content: space-between; gap: 20px; align-items: flex-end; margin-bottom: 48px; }
            .section__eyebrow { color: #7dfa92; text-transform: uppercase; letter-spacing: 0.2em; font-size: 0.82rem; font-weight: 700; }
            .section__title { margin: 0; font-size: clamp(2.2rem, 2.8vw, 3.2rem); color: #f8fafc; font-weight: 900; letter-spacing: -0.02em; }
            .section__text { margin: 0; color: #c8d4e1; max-width: 700px; line-height: 1.85; font-size: 1rem; }
            .category-grid { display: grid; gap: 18px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .category-card { background: linear-gradient(135deg, rgba(255,255,255,0.06), rgba(255,255,255,0.01)); border: 1px solid rgba(126,250,146,0.15); border-radius: 28px; padding: 28px; min-height: 180px; display: flex; flex-direction: column; justify-content: space-between; transition: all .3s cubic-bezier(0.4,0,0.2,1); position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
            .category-card::before { content: ""; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(126,250,146,0.1), transparent); opacity: 0; transition: opacity .3s ease; pointer-events: none; }
            .category-card::after { content: ""; position: absolute; bottom: -40%; right: -40%; width: 200px; height: 200px; border-radius: 999px; background: radial-gradient(circle, rgba(126,250,146,0.1), transparent); opacity: 0; transition: opacity .3s ease; }
            .category-card:hover { transform: translateY(-8px); border-color: rgba(126,250,146,0.35); background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); box-shadow: 0 20px 50px rgba(126,250,146,0.15); }
            .category-card:hover::before { opacity: 1; }
            .category-card:hover::after { opacity: 1; }
            .category-card__meta { font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.15em; color: #7dfa92; margin-bottom: 12px; opacity: 0.75; font-weight: 600; }
            .category-card__title { margin: 0 0 12px; font-size: 1.45rem; color: #f8fafc; font-weight: 800; letter-spacing: -0.01em; }
            .category-card__text { margin: 0; color: #c8d4e1; line-height: 1.7; font-size: 0.95rem; }
            .category-card__action { margin-top: 24px; color: #7dfa92; font-weight: 700; }
            .category-grid { display: grid; gap: 20px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .grid-3 { display: grid; gap: 24px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .grid-2 { display: grid; gap: 24px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .card { background: linear-gradient(135deg, rgba(255,255,255,0.06), rgba(255,255,255,0.01)); border: 1px solid rgba(126,250,146,0.12); border-radius: 28px; padding: 32px; min-height: 220px; display: flex; flex-direction: column; justify-content: space-between; transition: all .3s cubic-bezier(0.4,0,0.2,1); position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
            .card::before { content: ""; position: absolute; inset: 0; border-radius: 28px; background: linear-gradient(135deg, rgba(126,250,146,0.08), transparent); opacity: 0; transition: opacity .3s ease; pointer-events: none; }
            .card::after { content: ""; position: absolute; bottom: -30%; right: -30%; width: 180px; height: 180px; border-radius: 999px; background: radial-gradient(circle, rgba(126,250,146,0.08), transparent); opacity: 0; transition: opacity .3s ease; }
            .card:hover { transform: translateY(-6px); background: linear-gradient(135deg, rgba(255,255,255,0.08), rgba(255,255,255,0.02)); border-color: rgba(126,250,146,0.25); box-shadow: 0 20px 50px rgba(126,250,146,0.12); }
            .card:hover::before { opacity: 1; }
            .card:hover::after { opacity: 1; }
            .card__title { margin: 0 0 14px; font-size: 1.4rem; color: #f8fafc; font-weight: 800; letter-spacing: -0.01em; }
            .card__text { margin: 0; color: #c8d4e1; line-height: 1.75; font-size: 1rem; }
            .card__link { margin-top: 20px; display: inline-flex; color: #7dfa92; font-weight: 700; }
            .footer { background: linear-gradient(180deg, #030609 0%, #020305 100%); padding: 48px 24px 32px; color: #8b98a8; border-top: 1px solid rgba(126,250,146,0.1); }
            .footer__inner { max-width: 1200px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 24px; align-items: center; }
            .footer__brand { display: flex; align-items: center; gap: 12px; color: #f8fafc; font-weight: 800; font-size: 1.1rem; }
            .footer__links { display: flex; flex-wrap: wrap; gap: 20px; }
            .footer__links a { color: #8b98a8; font-size: 0.95rem; font-weight: 500; transition: color .2s ease; }
            .footer__links a:hover { color: #7dfa92; }
            @media (max-width: 1024px) { .hero__grid, .category-grid, .grid-3, .grid-2, .hero-stats { grid-template-columns: 1fr; } .site-header__inner { flex-wrap: wrap; } .nav-links { justify-content: center; flex-basis: 100%; } .category-card::after, .card::after { display: none; } }
            @media (max-width: 720px) { .site-header__inner { padding: 16px; } .hero { padding: 100px 20px 80px; } .hero-copy h1 { font-size: 2.2rem; } .hero-panel { min-height: auto; padding: 30px; margin-top: 24px; } .hero-panel::after { display: none; } .section { padding: 56px 18px; } .section__head { flex-direction: column; align-items: flex-start; margin-bottom: 32px; } .category-grid { gap: 16px; } .grid-3, .grid-2 { gap: 16px; } .hero-stats { grid-template-columns: 1fr 1fr; } }
        </style>
    </head>
    <body>
        <div class="page">
            <header class="site-header">
                <div class="site-header__inner">
                    <a href="/" class="brand"><span class="brand__mark">B</span>BiKuBe</a>
                    <nav class="nav-links">
                        <a href="#categories">Категории</a>
                        <a href="#how-it-works">Как это работает</a>
                        <a href="#business">Для бизнеса</a>
                        <a href="#partners">Партнёрам</a>
                        <a href="#footer">Контакты</a>
                    </nav>
                    <div class="header-actions">
                        <a href="/login" class="button button--secondary">Войти</a>
                        <a href="#categories" class="button button--primary">Выбрать услугу</a>
                    </div>
                </div>
            </header>
            <main class="page__content">
                <section class="hero">
                    <div class="hero__grid">
                        <div class="hero-copy">
                            <span class="badge">Narvik, Norway</span>
                            <h1>BiKuBe — локальная ОС услуг</h1>
                            <p>Платформа для связи клиентов и исполнителей в Narvik. Доставка, переезды, ремонт, уборка и помощь по дороге — всё на одной платформе.</p>
                            <div class="hero-actions">
                                <a href="#categories" class="button button--primary">Выбрать услугу</a>
                                <a href="#business" class="button button--secondary">Для партнёров</a>
                            </div>
                            <div class="hero-stats">
                                <div class="stat-card"><span class="stat-card__value">5+</span><span class="stat-card__label">направлений</span></div>
                                <div class="stat-card"><span class="stat-card__value">24/7</span><span class="stat-card__label">открыто</span></div>
                                <div class="stat-card"><span class="stat-card__value">Narvik</span><span class="stat-card__label">локальные</span></div>
                                <div class="stat-card"><span class="stat-card__value">Честно</span><span class="stat-card__label">без обещаний</span></div>
                            </div>
                        </div>
                        <aside class="hero-panel">
                            <div class="hero-panel__inner">
                                <h2 class="hero-panel__title">Прямой контакт исполнителей</h2>
                                <p class="hero-panel__text">BiKuBe упрощает общение между клиентом и местным мастером без лишних посредников и бюрократии.</p>
                                <div class="hero-panel__list">
                                    <div class="hero-panel__item"><span>1</span><div><strong>Выберите направление</strong><p>Найдите нужную услугу в нашем каталоге.</p></div></div>
                                    <div class="hero-panel__item"><span>2</span><div><strong>Опишите задачу</strong><p>Укажите детали, адрес и время выполнения.</p></div></div>
                                    <div class="hero-panel__item"><span>3</span><div><strong>Получите результат</strong><p>Исполнитель помогает вам в назначенное время.</p></div></div>
                                </div>
                            </div>
                        </aside>
                    </div>
                </section>
                <section id="categories" class="section">
                    <div class="section__head">
                        <div>
                            <p class="section__eyebrow">Категории</p>
                            <h2 class="section__title">Популярные направления</h2>
                        </div>
                        <p class="section__text">BiKuBe охватывает основные локальные сервисы Narvik. Выберите направление и опишите свою задачу.</p>
                    </div>
                    <div class="category-grid">
                        <a class="category-card" href="#categories"><span class="category-card__meta">Доставка</span><h3 class="category-card__title">🚚 Доставка</h3><p class="category-card__text">Товары, продукты и посылки по городу.</p><span class="category-card__action">Узнать →</span></a>
                        <a class="category-card" href="#categories"><span class="category-card__meta">Переезд</span><h3 class="category-card__title">📦 Переезд</h3><p class="category-card__text">Помощь с переездом и грузоперевозкой.</p><span class="category-card__action">Узнать →</span></a>
                        <a class="category-card" href="#categories"><span class="category-card__meta">Ремонт</span><h3 class="category-card__title">🔧 Ремонт и сборка</h3><p class="category-card__text">Мелкий ремонт, сборка мебели, помощь по дому.</p><span class="category-card__action">Узнать →</span></a>
                        <a class="category-card" href="#categories"><span class="category-card__meta">Уборка</span><h3 class="category-card__title">🧹 Уборка</h3><p class="category-card__text">Генеральная и текущая уборка помещений.</p><span class="category-card__action">Узнать →</span></a>
                        <a class="category-card" href="#categories"><span class="category-card__meta">Помощь</span><h3 class="category-card__title">🚗 Помощь на дороге</h3><p class="category-card__text">Помощь при поломке, эвакуация транспорта.</p><span class="category-card__action">Узнать →</span></a>
                        <a class="category-card" href="#categories"><span class="category-card__meta">Вывоз</span><h3 class="category-card__title">♻️ Утилизация</h3><p class="category-card__text">Вывоз мусора, мебели и техники.</p><span class="category-card__action">Узнать →</span></a>
                    </div>
                </section>
                <section id="how-it-works" class="section">
                    <div class="section__head">
                        <div>
                            <p class="section__eyebrow">Процесс</p>
                            <h2 class="section__title">Как это работает</h2>
                        </div>
                        <p class="section__text">Простой алгоритм: выберите услугу, опишите задачу, получите помощь от местного исполнителя.</p>
                    </div>
                    <div class="grid-3">
                        <div class="card"><h3 class="card__title">1️⃣ Выбор</h3><p class="card__text">Найдите нужную категорию услуги и опишите, что вам нужно.</p></div>
                        <div class="card"><h3 class="card__title">2️⃣ Детали</h3><p class="card__text">Укажите адрес, время и дополнительные требования.</p></div>
                        <div class="card"><h3 class="card__title">3️⃣ Помощь</h3><p class="card__text">Исполнитель приходит и выполняет работу в назначенное время.</p></div>
                    </div>
                </section>
                <section id="business" class="section">
                    <div class="section__head">
                        <div>
                            <p class="section__eyebrow">Возможности</p>
                            <h2 class="section__title">Для бизнеса и исполнителей</h2>
                        </div>
                        <p class="section__text">BiKuBe объединяет местные услуги и дает прямой доступ к клиентам без лишних посредников.</p>
                    </div>
                    <div class="grid-3">
                        <div class="card"><h3 class="card__title">👨‍💼 Клиент</h3><p class="card__text">Используйте BiKuBe для быстрого поиска проверенных исполнителей услуг в Narvik.</p></div>
                        <div class="card"><h3 class="card__title">🛠️ Мастер</h3><p class="card__text">Получайте заказы от местных клиентов и управляйте своим графиком работы.</p></div>
                        <div class="card"><h3 class="card__title">🤝 Партнёр</h3><p class="card__text">Интегрируйте свой сервис в экосистему BiKuBe и расширяйте клиентскую базу.</p></div>
                    </div>
                </section>
                <section id="partners" class="section">
                    <div class="section__head">
                        <div>
                            <p class="section__eyebrow">Партнёры</p>
                            <h2 class="section__title">Присоединяйтесь к сети</h2>
                        </div>
                        <p class="section__text">BiKuBe растёт вместе с местным сообществом мастеров и предпринимателей Narvik.</p>
                    </div>
                    <div class="card"><h3 class="card__title">Станьте частью BiKuBe</h3><p class="card__text">Если вы предоставляете услуги в Narvik, свяжитесь с нами напрямую для интеграции вашего сервиса.</p><span class="card__text" style="margin-top: 12px; font-size: 0.85rem; color: #999;">Контакт: hello@bikube.local</span></div>
                </section>
            </main>
            <footer id="footer" class="footer">
                <div class="footer__inner">
                    <div class="footer__brand"><span class="brand__mark">B</span>BiKuBe</div>
                    <div class="footer__links">
                        <a href="#categories">Категории</a>
                        <a href="#how-it-works">Как это работает</a>
                        <a href="#business">Для бизнеса</a>
                        <a href="#partners">Партнёрам</a>
                        <a href="/login">Войти</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
