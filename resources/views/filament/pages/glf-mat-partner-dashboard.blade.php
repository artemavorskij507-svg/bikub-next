<x-filament-panels::page>

    @php
        $OrderStatus = \App\Enums\OrderStatus::class;
        $scenarioKeys = ['delivery.meals', 'restaurant.booking'];
        $glfOrders = \App\Models\Order::whereIn('service_scenario_key', $scenarioKeys)
            ->with('scenario')
            ->latest('created_at')
            ->get();

        $pending   = $glfOrders->where('status', $OrderStatus::Submitted);
        $active    = $glfOrders->whereIn('status', [$OrderStatus::Accepted, $OrderStatus::InProgress]);
        $completed = $glfOrders->where('status', $OrderStatus::Completed);
        $delivery  = $glfOrders->where('service_scenario_key', 'delivery.meals');
        $booking   = $glfOrders->where('service_scenario_key', 'restaurant.booking');

        $statusLabel = [
            $OrderStatus::Draft->value     => 'Чернетка',
            $OrderStatus::Submitted->value => 'Очікує підтвердження',
            $OrderStatus::Accepted->value  => 'Підтверджено',
            $OrderStatus::InProgress->value => 'В роботі',
            $OrderStatus::Completed->value => 'Виконано',
            $OrderStatus::Cancelled->value => 'Скасовано',
        ];
        $statusColor = [
            $OrderStatus::Draft->value     => 'gray',
            $OrderStatus::Submitted->value => 'warning',
            $OrderStatus::Accepted->value  => 'info',
            $OrderStatus::InProgress->value => 'primary',
            $OrderStatus::Completed->value => 'success',
            $OrderStatus::Cancelled->value => 'danger',
        ];

        $hasDispatch = \Illuminate\Support\Facades\Route::has('filament.admin.pages.dispatch-center');
        $hasWorkerOrders = \Illuminate\Support\Facades\Route::has('worker.orders.index');
    @endphp

    <style>
        /* Scoped, inline-compiled CSS for the GLF MaT cockpit.
           Deliberately NOT using Tailwind utility classes here: this admin panel's
           CSS bundle is pre-built ahead of time and does not get rebuilt when this
           file changes (no node_modules / build pipeline on this server), so any
           Tailwind classes not already present in the compiled bundle render as
           plain unstyled text. Plain CSS below has no such dependency. */
        .glf-wrap{
            --glf-gold:#e8c98a;
            --glf-gold-line:rgba(196,163,90,.22);
            --glf-gold-line-soft:rgba(196,163,90,.18);
            --glf-bg-card:#181410;
            --glf-text:#e7e9ec;
            --glf-muted:#8d8475;
            --glf-muted-soft:#b9b2a6;
            --glf-warning:#f5bd54;
            --glf-info:#7cc4f5;
            --glf-success:#5fd99a;
            --glf-primary:#a5b4fc;
            --glf-danger:#f59999;
            --glf-radius-lg:14px;
            --glf-radius-md:12px;
            --glf-radius-sm:9px;
            --glf-gap:16px;
            --glf-card-gap:22px;
            font-family:inherit;color:#e7e9ec
        }
        .glf-card{border-radius:14px;border:1px solid rgba(196,163,90,.22);background:#181410;margin-bottom:22px;overflow:hidden}
        .glf-header{background:linear-gradient(135deg,rgba(196,163,90,.14),rgba(20,16,12,.4));padding:20px 22px;display:flex;flex-wrap:wrap;justify-content:space-between;gap:16px;align-items:flex-start}
        .glf-header h2{margin:0 0 4px;font-size:1.25rem;font-weight:800;color:#fff;display:flex;align-items:center;gap:8px}
        .glf-header p{margin:0;font-size:.85rem;color:#b9b2a6}
        .glf-badge-mode{display:inline-flex;align-items:center;gap:6px;margin-top:10px;padding:4px 12px;border-radius:999px;background:rgba(196,163,90,.18);color:#e8c98a;font-size:.72rem;font-weight:700}
        .glf-dot{width:6px;height:6px;border-radius:50%;background:#e8c98a;flex-shrink:0}
        .glf-links{display:flex;flex-wrap:wrap;gap:8px}
        .glf-link{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;background:#221c16;border:1px solid rgba(196,163,90,.2);color:#ddd5c7;font-size:.82rem;font-weight:600;text-decoration:none;transition:border-color .2s}
        .glf-link:hover{border-color:rgba(196,163,90,.6)}
        .glf-section-label{font-size:.78rem;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:#8d8475;margin:0 0 12px}
        .glf-board{display:grid;grid-template-columns:repeat(3,1fr);gap:var(--glf-gap)}
        @media(max-width:900px){.glf-board{grid-template-columns:1fr 1fr}}
        @media(max-width:768px){.glf-board{grid-template-columns:1fr}}
        .glf-col{border-radius:12px;border:1px solid rgba(196,163,90,.18);background:#181410;overflow:hidden}
        .glf-col-head{padding:12px 16px;display:flex;justify-content:space-between;align-items:center;font-size:.85rem;font-weight:700}
        .glf-col-head.pending{background:rgba(245,189,84,.1);color:#f5bd54}
        .glf-col-head.active{background:rgba(85,168,217,.1);color:#7cc4f5}
        .glf-col-head.done{background:rgba(52,168,110,.1);color:#5fd99a}
        .glf-count{font-size:.72rem;font-weight:800;padding:2px 9px;border-radius:999px;background:rgba(255,255,255,.08)}
        .glf-col-body{padding:10px;max-height:280px;overflow-y:auto;display:flex;flex-direction:column;gap:8px}
        .glf-row{display:block;padding:9px 11px;border-radius:var(--glf-radius-sm);border:1px solid rgba(255,255,255,.06);background:rgba(255,255,255,.015);text-decoration:none;color:inherit;transition:border-color .2s,background .2s,transform .15s}
        .glf-row:hover{border-color:rgba(196,163,90,.4);background:rgba(196,163,90,.05);transform:translateX(2px)}
        .glf-row-top{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:5px}
        .glf-row-num{font-family:monospace;font-size:.7rem;color:var(--glf-muted)}
        .glf-row-name{font-size:.85rem;font-weight:600;color:#e7e9ec;margin-bottom:3px}
        .glf-row-meta{font-size:.7rem;color:var(--glf-muted)}
        .glf-empty{text-align:center;font-size:.78rem;color:var(--glf-muted);padding:26px 12px;border:1px dashed rgba(255,255,255,.08);border-radius:var(--glf-radius-sm);margin:2px}
        .glf-empty .glf-empty-icon{display:block;font-size:1.3rem;margin-bottom:6px;opacity:.5}
        .glf-empty-banner{margin-top:14px;padding:18px;border-radius:12px;border:1px dashed rgba(196,163,90,.3);text-align:center;font-size:.85rem;color:#b9b2a6}
        .glf-empty-banner a{color:#e8c98a;font-weight:700;text-decoration:none}
        .glf-table-head{padding:14px 18px;border-bottom:1px solid var(--glf-gold-line-soft);display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:10px}
        .glf-tabs{display:flex;gap:6px}
        .glf-tab{padding:5px 12px;border-radius:999px;border:1px solid rgba(255,255,255,.1);background:transparent;color:var(--glf-muted);font-size:.76rem;font-weight:600;cursor:pointer;transition:all .2s}
        .glf-tab:hover{border-color:rgba(196,163,90,.35);color:var(--glf-muted-soft)}
        .glf-tab.is-active{background:rgba(196,163,90,.16);border-color:rgba(196,163,90,.4);color:var(--glf-gold)}
        .glf-table-scroll{overflow-x:auto;position:relative}
        @media(max-width:560px){.glf-hide-mobile{display:none}}
        .glf-table{width:100%;font-size:.85rem;border-collapse:collapse}
        .glf-table thead th{text-align:left;font-size:.68rem;text-transform:uppercase;letter-spacing:.05em;color:#8d8475;padding:10px 16px;border-bottom:1px solid rgba(196,163,90,.18)}
        .glf-table tbody td{padding:11px 16px;border-bottom:1px solid rgba(255,255,255,.05);color:#e7e9ec}
        .glf-table tbody tr:hover{background:rgba(196,163,90,.05)}
        .glf-pill{display:inline-block;padding:2px 9px;border-radius:6px;font-size:.7rem;font-weight:700}
        .glf-pill.delivery{background:rgba(52,168,110,.16);color:#5fd99a}
        .glf-pill.booking{background:rgba(168,110,232,.16);color:#c9a3f5}
        .glf-pill.gray{background:rgba(255,255,255,.08);color:#b9b2a6}
        .glf-pill.warning{background:rgba(245,189,84,.16);color:#f5bd54}
        .glf-pill.info{background:rgba(85,168,217,.16);color:#7cc4f5}
        .glf-pill.primary{background:rgba(129,140,248,.16);color:#a5b4fc}
        .glf-pill.success{background:rgba(52,168,110,.16);color:#5fd99a}
        .glf-pill.danger{background:rgba(244,114,114,.16);color:#f59999}
        .glf-table a.open{color:#e8c98a;font-weight:700;text-decoration:none}
        .glf-grid2{display:grid;grid-template-columns:1fr 1fr;gap:var(--glf-gap)}
        @media(max-width:768px){.glf-grid2{grid-template-columns:1fr}}
        .glf-panel{padding:20px}
        .glf-panel h3{margin:0 0 6px;font-size:1rem;font-weight:800;color:#fff;letter-spacing:.01em}
        .glf-panel .sub{font-size:.76rem;color:var(--glf-muted);margin:0 0 14px;line-height:1.5}
        .glf-panel code{font-family:monospace;background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px;font-size:.78rem}
        .glf-item{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:11px 0;border-bottom:1px solid rgba(255,255,255,.05)}
        .glf-item:last-child{border-bottom:none}
        .glf-item-name{font-size:.86rem;font-weight:600;color:#e7e9ec;margin-bottom:3px}
        .glf-item-sub{font-size:.75rem;color:var(--glf-muted);line-height:1.4;word-break:break-word}
        .glf-note{padding:18px;font-size:.85rem;line-height:1.6;color:#b9b2a6}
        .glf-workflow{padding:18px;display:flex;flex-wrap:wrap;align-items:center;gap:8px}
        @media(max-width:680px){
            .glf-workflow{flex-direction:column;align-items:stretch}
            .glf-workflow .glf-step{width:100%;text-align:center}
            .glf-workflow .glf-arrow{transform:rotate(90deg);text-align:center;padding:2px 0}
        }
        .glf-step{padding:8px 14px;border-radius:9px;font-size:.82rem;font-weight:700;background:rgba(255,255,255,.06);color:#ddd5c7}
        .glf-step.s2{background:rgba(245,189,84,.14);color:#f5bd54}
        .glf-step.s3{background:rgba(85,168,217,.14);color:#7cc4f5}
        .glf-step.s4{background:rgba(129,140,248,.14);color:#a5b4fc}
        .glf-step.s5{background:rgba(52,168,110,.14);color:#5fd99a}
        .glf-arrow{color:#8d8475}
        .glf-reality{border-color:rgba(244,114,114,.3)!important}
        .glf-reality .glf-panel{padding:18px}
        .glf-reality h3{color:var(--glf-danger)}
        .glf-reality ul{margin:0;padding-left:0;list-style:none;display:flex;flex-direction:column;gap:9px}
        .glf-reality li{display:flex;align-items:flex-start;gap:10px;font-size:.85rem;color:#e2b3b3;line-height:1.55}
        .glf-reality-icon{flex-shrink:0;font-size:1rem;opacity:.85;margin-top:1px}
    </style>

    <div class="glf-wrap">

        {{-- ===================== HEADER COCKPIT ===================== --}}
        <div class="glf-card">
            <div class="glf-header">
                <div>
                    <h2><span>🍽️</span> GLF MaT — партнерський модуль</h2>
                    <p>Доставка страв + бронювання столу · Кухні світу, битва смаків</p>
                    <span class="glf-badge-mode"><span class="glf-dot"></span> Режим ручного підтвердження</span>
                </div>
                <div class="glf-links">
                    <a href="/services/food" target="_blank" class="glf-link">🔗 Публічна сторінка</a>
                    <a href="{{ route('filament.admin.resources.orders.index') }}" class="glf-link">📋 Усі замовлення</a>
                    @if($hasDispatch)
                    <a href="{{ route('filament.admin.pages.dispatch-center') }}" class="glf-link">🛰️ Dispatch Center</a>
                    @endif
                    @if($hasWorkerOrders)
                    <a href="{{ route('worker.orders.index') }}" class="glf-link">🧑‍🔧 Замовлення воркерів</a>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===================== KPI WIDGET ===================== --}}
        <div style="margin-bottom:22px">
            @livewire(\App\Filament\Widgets\GLFMaTStatsOverview::class)
        </div>

        {{-- ===================== OPERATIONS BOARD ===================== --}}
        <p class="glf-section-label">📊 Операційна дошка</p>
        <div class="glf-board">

            <div class="glf-col">
                <div class="glf-col-head pending">
                    <span>⏳ Очікують підтвердження</span>
                    <span class="glf-count">{{ $pending->count() }}</span>
                </div>
                <div class="glf-col-body">
                    @forelse($pending->take(8) as $order)
                        <a href="{{ route('filament.admin.resources.orders.view', $order) }}" class="glf-row">
                            <div class="glf-row-top"><span class="glf-row-num">{{ $order->order_number }}</span><span class="glf-pill warning">{{ $statusLabel[$order->status->value] ?? $order->status->value }}</span></div>
                            <div class="glf-row-name">{{ $order->customer_name ?? 'Без імені' }}</div>
                            <div class="glf-row-meta">{{ $order->created_at->diffForHumans() }}</div>
                        </a>
                    @empty
                        <p class="glf-empty"><span class="glf-empty-icon">⏳</span>Немає заявок, що очікують підтвердження.</p>
                    @endforelse
                </div>
            </div>

            <div class="glf-col">
                <div class="glf-col-head active">
                    <span>▶️ Підтверджені / в роботі</span>
                    <span class="glf-count">{{ $active->count() }}</span>
                </div>
                <div class="glf-col-body">
                    @forelse($active->take(8) as $order)
                        @php $colorKey = $statusColor[$order->status->value] ?? 'gray'; @endphp
                        <a href="{{ route('filament.admin.resources.orders.view', $order) }}" class="glf-row">
                            <div class="glf-row-top"><span class="glf-row-num">{{ $order->order_number }}</span><span class="glf-pill {{ $colorKey }}">{{ $statusLabel[$order->status->value] ?? $order->status->value }}</span></div>
                            <div class="glf-row-name">{{ $order->customer_name ?? 'Без імені' }}</div>
                        </a>
                    @empty
                        <p class="glf-empty"><span class="glf-empty-icon">▶️</span>Немає активних замовлень.</p>
                    @endforelse
                </div>
            </div>

            <div class="glf-col">
                <div class="glf-col-head done">
                    <span>✅ Виконано</span>
                    <span class="glf-count">{{ $completed->count() }}</span>
                </div>
                <div class="glf-col-body">
                    @forelse($completed->take(8) as $order)
                        <a href="{{ route('filament.admin.resources.orders.view', $order) }}" class="glf-row">
                            <div class="glf-row-top"><span class="glf-row-num">{{ $order->order_number }}</span><span class="glf-pill success">{{ $statusLabel[$order->status->value] ?? $order->status->value }}</span></div>
                            <div class="glf-row-name">{{ $order->customer_name ?? 'Без імені' }}</div>
                            <div class="glf-row-meta">{{ $order->completed_at?->diffForHumans() }}</div>
                        </a>
                    @empty
                        <p class="glf-empty"><span class="glf-empty-icon">✅</span>Ще немає виконаних замовлень.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @if($glfOrders->isEmpty())
        <div class="glf-empty-banner">
            Ще немає жодної реальної заявки GLF MaT.
            Надішліть форму на <a href="/services/food" target="_blank">/services/food</a>, щоб створити першу заявку.
        </div>
        @endif

        {{-- ===================== LATEST REQUESTS TABLE ===================== --}}
        <div class="glf-card" style="margin-top:22px">
            <div class="glf-table-head">
                <h3 style="margin:0;font-size:.95rem;font-weight:800;color:#fff">Останні заявки (10)</h3>
                <div class="glf-tabs" id="glf-req-tabs">
                    <button type="button" class="glf-tab is-active" data-filter="all">Усі</button>
                    <button type="button" class="glf-tab" data-filter="delivery">Доставка</button>
                    <button type="button" class="glf-tab" data-filter="booking">Бронювання</button>
                </div>
            </div>
            <div class="glf-table-scroll">
                <table class="glf-table">
                    <thead>
                        <tr>
                            <th>№</th><th>Тип</th><th>Клієнт</th><th class="glf-hide-mobile">Телефон</th><th>Статус</th><th class="glf-hide-mobile">Створено</th><th></th>
                        </tr>
                    </thead>
                    <tbody id="glf-req-rows">
                        @forelse($glfOrders->take(10) as $order)
                            @php
                                $isDelivery = $order->service_scenario_key === 'delivery.meals';
                                $statusVal = $order->status->value;
                                $colorKey = $statusColor[$statusVal] ?? 'gray';
                            @endphp
                            <tr data-type="{{ $isDelivery ? 'delivery' : 'booking' }}">
                                <td style="font-family:monospace;font-size:.78rem">{{ $order->order_number }}</td>
                                <td><span class="glf-pill {{ $isDelivery ? 'delivery' : 'booking' }}">{{ $isDelivery ? 'Доставка' : 'Бронювання' }}</span></td>
                                <td>{{ $order->customer_name ?? '—' }}</td>
                                <td class="glf-hide-mobile">{{ $order->customer_phone ?? '—' }}</td>
                                <td><span class="glf-pill {{ $colorKey }}">{{ $statusLabel[$statusVal] ?? $statusVal }}</span></td>
                                <td class="glf-hide-mobile" style="color:#8d8475">{{ $order->created_at->format('d.m H:i') }}</td>
                                <td><a href="{{ route('filament.admin.resources.orders.view', $order) }}" class="open">Відкрити</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" style="text-align:center;padding:28px;color:#8d8475">Жодної заявки GLF MaT поки немає.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ===================== BOOKING + DELIVERY PANELS ===================== --}}
        <div class="glf-grid2" style="margin-top:22px">
            <div class="glf-card glf-panel">
                <h3>🍽️ Бронювання столів</h3>
                <p class="sub">Сценарій <code>restaurant.booking</code> — дата, час, кількість гостей зберігаються в Order.metadata.intake.</p>
                @forelse($booking->take(5) as $order)
                    @php $intake = $order->metadata['intake'] ?? []; $colorKey = $statusColor[$order->status->value] ?? 'gray'; @endphp
                    <div class="glf-item">
                        <div>
                            <div class="glf-item-name">{{ $order->customer_name ?? '—' }} · {{ $intake['guest_count'] ?? '?' }} гост.</div>
                            <div class="glf-item-sub">{{ $intake['booking_date'] ?? '—' }} {{ $intake['booking_time'] ?? '' }}</div>
                        </div>
                        <span class="glf-pill {{ $colorKey }}">{{ $statusLabel[$order->status->value] ?? $order->status->value }}</span>
                    </div>
                @empty
                    <p class="glf-empty" style="text-align:left;padding:12px 0">Запитів на бронювання ще немає. Календар столів не підключено — підтвердження виключно вручну.</p>
                @endforelse
            </div>

            <div class="glf-card glf-panel">
                <h3>🚚 Координація доставки</h3>
                <p class="sub">Сценарій <code>delivery.meals</code> — воркер призначається через Dispatch Center лише після підтвердження.</p>
                @forelse($delivery->take(5) as $order)
                    @php $intake = $order->metadata['intake'] ?? []; $colorKey = $statusColor[$order->status->value] ?? 'gray'; @endphp
                    <div class="glf-item">
                        <div>
                            <div class="glf-item-name">{{ $order->customer_name ?? '—' }}</div>
                            <div class="glf-item-sub">📍 {{ \Illuminate\Support\Str::limit($intake['dropoff_address'] ?? '—', 60) }}</div>
                        </div>
                        <span class="glf-pill {{ $colorKey }}">{{ $statusLabel[$order->status->value] ?? $order->status->value }}</span>
                    </div>
                @empty
                    <p class="glf-empty" style="text-align:left;padding:12px 0">Запитів на доставку ще немає.</p>
                @endforelse
            </div>
        </div>

        {{-- ===================== MENU / PARTNER CONTENT LIMITATION ===================== --}}
        <div class="glf-card" style="margin-top:22px">
            <div class="glf-note">
                <h3 style="margin:0 0 6px;font-size:.9rem;color:#fff">📖 Меню та контент партнера</h3>
                Меню на публічній сторінці — статичний preview (8 страв, захардкоджені дані в Blade), без таблиці БД.
                Управління меню з адмінки наразі недоступне. Реальна модель меню (категорії, страви, опції) описана
                в <code style="background:rgba(255,255,255,.07);padding:1px 5px;border-radius:4px">docs/GLF_MAT_MODEL_ARCHITECTURE.md</code> як майбутній етап, що потребує
                окремого підтвердження власника перед створенням міграцій.
            </div>
        </div>

        {{-- ===================== MANUAL WORKFLOW ===================== --}}
        <div class="glf-card" style="margin-top:22px">
            <div class="glf-workflow">
                <span class="glf-step">1. Клієнт надсилає форму</span>
                <span class="glf-arrow">→</span>
                <span class="glf-step s2">2. Адмін підтверджує заявку</span>
                <span class="glf-arrow">→</span>
                <span class="glf-step s3">3. Dispatch призначає воркера</span>
                <span class="glf-arrow">→</span>
                <span class="glf-step s4">4. Воркер виконує</span>
                <span class="glf-arrow">→</span>
                <span class="glf-step s5">5. Підтвердження виконання</span>
            </div>
        </div>

        {{-- ===================== REALITY BLOCK ===================== --}}
        <div class="glf-card glf-reality" style="margin-top:22px">
            <div class="glf-panel">
                <h3>⚠️ Чесний стан системи (без вигадок)</h3>
                <ul>
                    <li><span class="glf-reality-icon">💳</span><span><strong>Оплата:</strong> ручна / провайдер не підключено. Готівка або домовленість з рестораном.</span></li>
                    <li><span class="glf-reality-icon">📅</span><span><strong>Календар столів:</strong> не підключено. Кожне бронювання підтверджується людиною вручну.</span></li>
                    <li><span class="glf-reality-icon">📡</span><span><strong>ETA / GPS:</strong> з'являється лише після призначення воркера через Dispatch Center.</span></li>
                    <li><span class="glf-reality-icon">🏢</span><span><strong>Партнерський акаунт GLF MaT:</strong> налаштування ще не завершено (немає окремої моделі Partner/RestaurantProfile).</span></li>
                    <li><span class="glf-reality-icon">🚫</span><span>Жодних фейкових замовлень, відгуків, рейтингів чи трекінгу на цій сторінці немає.</span></li>
                </ul>
            </div>
        </div>

    </div>

    <script>
        (function () {
            var tabs = document.querySelectorAll('#glf-req-tabs .glf-tab');
            var rows = document.querySelectorAll('#glf-req-rows tr[data-type]');
            tabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    tabs.forEach(function (t) { t.classList.remove('is-active'); });
                    tab.classList.add('is-active');
                    var f = tab.dataset.filter;
                    rows.forEach(function (row) {
                        row.style.display = (f === 'all' || row.dataset.type === f) ? '' : 'none';
                    });
                });
            });
        })();
    </script>
</x-filament-panels::page>
