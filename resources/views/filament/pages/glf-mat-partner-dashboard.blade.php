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
        $colorClasses = [
            'gray'    => 'bg-gray-100 text-gray-700 dark:bg-gray-700/40 dark:text-gray-200',
            'warning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200',
            'info'    => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-200',
            'primary' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-200',
            'success' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200',
            'danger'  => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-200',
        ];

        $hasDispatch = \Illuminate\Support\Facades\Route::has('filament.admin.pages.dispatch-center');
        $hasWorkerOrders = \Illuminate\Support\Facades\Route::has('worker.orders.index');
    @endphp

    {{-- ===================== HEADER COCKPIT ===================== --}}
    <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-gradient-to-br from-amber-50 to-white dark:from-amber-950/30 dark:to-slate-900 p-5 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-2xl">🍽️</span>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">GLF MaT — партнерський модуль</h2>
                </div>
                <p class="text-sm text-slate-600 dark:text-slate-400">Доставка страв + бронювання столу · Кухні світу, битва смаків</p>
                <span class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1 rounded-full bg-amber-200/70 dark:bg-amber-800/40 text-amber-900 dark:text-amber-200 text-xs font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-600 dark:bg-amber-300"></span>
                    Режим ручного підтвердження
                </span>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/services/food" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 hover:border-amber-400 transition">
                    🔗 Публічна сторінка
                </a>
                <a href="{{ route('filament.admin.resources.orders.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 hover:border-amber-400 transition">
                    📋 Усі замовлення
                </a>
                @if($hasDispatch)
                <a href="{{ route('filament.admin.pages.dispatch-center') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 hover:border-amber-400 transition">
                    🛰️ Dispatch Center
                </a>
                @endif
                @if($hasWorkerOrders)
                <a href="{{ route('worker.orders.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm font-medium text-slate-700 dark:text-slate-200 hover:border-amber-400 transition">
                    🧑‍🔧 Замовлення воркерів
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ===================== KPI WIDGET ===================== --}}
    <div class="mb-6">
        @livewire(\App\Filament\Widgets\GLFMaTStatsOverview::class)
    </div>

    {{-- ===================== OPERATIONS BOARD ===================== --}}
    <div class="mb-6">
        <h3 class="text-sm font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-3">Операційна дошка</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div class="rounded-xl border border-amber-200 dark:border-amber-800 bg-white dark:bg-slate-800 overflow-hidden">
                <div class="px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-800 flex items-center justify-between">
                    <span class="font-semibold text-amber-900 dark:text-amber-200 text-sm">⏳ Очікують підтвердження</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-200 dark:bg-amber-800 text-amber-900 dark:text-amber-100">{{ $pending->count() }}</span>
                </div>
                <div class="p-3 space-y-2 max-h-72 overflow-y-auto">
                    @forelse($pending->take(8) as $order)
                        <a href="{{ route('filament.admin.resources.orders.view', $order) }}" class="block p-2.5 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-amber-300 transition">
                            <div class="flex justify-between text-xs font-mono text-slate-400">
                                <span>{{ $order->order_number }}</span>
                                <span>{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $order->customer_name ?? 'Без імені' }}</div>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Немає заявок, що очікують підтвердження.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-sky-200 dark:border-sky-800 bg-white dark:bg-slate-800 overflow-hidden">
                <div class="px-4 py-3 bg-sky-50 dark:bg-sky-900/20 border-b border-sky-200 dark:border-sky-800 flex items-center justify-between">
                    <span class="font-semibold text-sky-900 dark:text-sky-200 text-sm">▶️ Підтверджені / в роботі</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-sky-200 dark:bg-sky-800 text-sky-900 dark:text-sky-100">{{ $active->count() }}</span>
                </div>
                <div class="p-3 space-y-2 max-h-72 overflow-y-auto">
                    @forelse($active->take(8) as $order)
                        <a href="{{ route('filament.admin.resources.orders.view', $order) }}" class="block p-2.5 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-sky-300 transition">
                            <div class="flex justify-between text-xs font-mono text-slate-400">
                                <span>{{ $order->order_number }}</span>
                                <span>{{ $statusLabel[$order->status->value] ?? $order->status->value }}</span>
                            </div>
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $order->customer_name ?? 'Без імені' }}</div>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Немає активних замовлень.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-white dark:bg-slate-800 overflow-hidden">
                <div class="px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-200 dark:border-emerald-800 flex items-center justify-between">
                    <span class="font-semibold text-emerald-900 dark:text-emerald-200 text-sm">✅ Виконано</span>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-emerald-200 dark:bg-emerald-800 text-emerald-900 dark:text-emerald-100">{{ $completed->count() }}</span>
                </div>
                <div class="p-3 space-y-2 max-h-72 overflow-y-auto">
                    @forelse($completed->take(8) as $order)
                        <a href="{{ route('filament.admin.resources.orders.view', $order) }}" class="block p-2.5 rounded-lg border border-slate-100 dark:border-slate-700 hover:border-emerald-300 transition">
                            <div class="flex justify-between text-xs font-mono text-slate-400">
                                <span>{{ $order->order_number }}</span>
                                <span>{{ $order->completed_at?->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $order->customer_name ?? 'Без імені' }}</div>
                        </a>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-6">Ще немає виконаних замовлень.</p>
                    @endforelse
                </div>
            </div>
        </div>

        @if($glfOrders->isEmpty())
        <div class="mt-4 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 p-6 text-center">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Ще немає жодної реальної заявки GLF MaT.
                Надішліть форму на <a href="/services/food" target="_blank" class="text-amber-600 dark:text-amber-400 font-semibold hover:underline">/services/food</a>, щоб створити першу заявку.
            </p>
        </div>
        @endif
    </div>

    {{-- ===================== LATEST REQUESTS TABLE ===================== --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 mb-6 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold text-slate-800 dark:text-slate-100">Останні заявки (10)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-left text-xs uppercase text-slate-500 dark:text-slate-400">
                        <th class="px-5 py-2.5">№</th>
                        <th class="px-5 py-2.5">Тип</th>
                        <th class="px-5 py-2.5">Клієнт</th>
                        <th class="px-5 py-2.5">Телефон</th>
                        <th class="px-5 py-2.5">Статус</th>
                        <th class="px-5 py-2.5">Створено</th>
                        <th class="px-5 py-2.5"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($glfOrders->take(10) as $order)
                        @php
                            $isDelivery = $order->service_scenario_key === 'delivery.meals';
                            $statusVal = $order->status->value;
                        @endphp
                        <tr class="border-b border-slate-100 dark:border-slate-700/60 hover:bg-slate-50 dark:hover:bg-slate-700/30">
                            <td class="px-5 py-3 font-mono text-xs">{{ $order->order_number }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $isDelivery ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' : 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200' }}">
                                    {{ $isDelivery ? 'Доставка' : 'Бронювання' }}
                                </span>
                            </td>
                            <td class="px-5 py-3">{{ $order->customer_name ?? '—' }}</td>
                            <td class="px-5 py-3">{{ $order->customer_phone ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $colorClasses[$statusColor[$statusVal] ?? 'gray'] }}">
                                    {{ $statusLabel[$statusVal] ?? $statusVal }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $order->created_at->format('d.m H:i') }}</td>
                            <td class="px-5 py-3">
                                <a href="{{ route('filament.admin.resources.orders.view', $order) }}" class="text-amber-600 dark:text-amber-400 font-medium hover:underline">Відкрити</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-slate-400 text-sm">
                                Жодної заявки GLF MaT поки немає.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ===================== BOOKING + DELIVERY PANELS ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

        <div class="rounded-xl border border-purple-200 dark:border-purple-800 bg-white dark:bg-slate-800 p-5">
            <h3 class="font-semibold text-purple-900 dark:text-purple-200 mb-1">🍽️ Бронювання столів</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Сценарій <code class="font-mono">restaurant.booking</code> — дата, час, кількість гостей зберігаються в Order.metadata.intake.</p>
            @forelse($booking->take(5) as $order)
                @php $intake = $order->metadata['intake'] ?? []; @endphp
                <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700 last:border-0">
                    <div>
                        <div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $order->customer_name ?? '—' }} · {{ $intake['guest_count'] ?? '?' }} гост.</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $intake['booking_date'] ?? '—' }} {{ $intake['booking_time'] ?? '' }}</div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $colorClasses[$statusColor[$order->status->value] ?? 'gray'] }}">{{ $statusLabel[$order->status->value] ?? $order->status->value }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-4">Запитів на бронювання ще немає. Календар столів не підключено — підтвердження виключно вручну.</p>
            @endforelse
        </div>

        <div class="rounded-xl border border-emerald-200 dark:border-emerald-800 bg-white dark:bg-slate-800 p-5">
            <h3 class="font-semibold text-emerald-900 dark:text-emerald-200 mb-1">🚚 Координація доставки</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-3">Сценарій <code class="font-mono">delivery.meals</code> — воркер призначається через Dispatch Center лише після підтвердження.</p>
            @forelse($delivery->take(5) as $order)
                @php $intake = $order->metadata['intake'] ?? []; @endphp
                <div class="flex items-center justify-between py-2 border-b border-slate-100 dark:border-slate-700 last:border-0">
                    <div>
                        <div class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ $order->customer_name ?? '—' }}</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ \Illuminate\Support\Str::limit($intake['dropoff_address'] ?? '—', 40) }}</div>
                    </div>
                    <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $colorClasses[$statusColor[$order->status->value] ?? 'gray'] }}">{{ $statusLabel[$order->status->value] ?? $order->status->value }}</span>
                </div>
            @empty
                <p class="text-xs text-slate-400 py-4">Запитів на доставку ще немає.</p>
            @endforelse
        </div>
    </div>

    {{-- ===================== MENU / PARTNER CONTENT LIMITATION ===================== --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 p-5 mb-6">
        <h3 class="font-semibold text-slate-700 dark:text-slate-200 mb-1">📖 Меню та контент партнера</h3>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Меню на публічній сторінці — статичний preview (8 страв, захардкоджені дані в Blade), без таблиці БД.
            Управління меню з адмінки наразі недоступне. Реальна модель меню (категорії, страви, опції) описана
            в <code class="font-mono text-xs">docs/GLF_MAT_MODEL_ARCHITECTURE.md</code> як майбутній етап, що потребує
            окремого підтвердження власника перед створенням міграцій.
        </p>
    </div>

    {{-- ===================== MANUAL WORKFLOW ===================== --}}
    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-5 mb-6">
        <h3 class="font-semibold text-slate-800 dark:text-slate-100 mb-4">🔁 Ручний робочий процес</h3>
        <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="px-3 py-2 rounded-lg bg-slate-100 dark:bg-slate-700 font-medium">1. Клієнт надсилає форму</span>
            <span class="text-slate-400">→</span>
            <span class="px-3 py-2 rounded-lg bg-amber-100 dark:bg-amber-900/40 font-medium">2. Адмін підтверджує заявку</span>
            <span class="text-slate-400">→</span>
            <span class="px-3 py-2 rounded-lg bg-sky-100 dark:bg-sky-900/40 font-medium">3. Dispatch призначає воркера</span>
            <span class="text-slate-400">→</span>
            <span class="px-3 py-2 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 font-medium">4. Воркер виконує</span>
            <span class="text-slate-400">→</span>
            <span class="px-3 py-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 font-medium">5. Підтвердження виконання</span>
        </div>
    </div>

    {{-- ===================== REALITY BLOCK ===================== --}}
    <div class="rounded-xl border border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/15 p-5">
        <h3 class="font-semibold text-rose-900 dark:text-rose-200 mb-2">⚠️ Чесний стан системи (без вигадок)</h3>
        <ul class="text-sm text-rose-800 dark:text-rose-200 space-y-1.5">
            <li>• <strong>Оплата:</strong> ручна / провайдер не підключено. Готівка або домовленість з рестораном.</li>
            <li>• <strong>Календар столів:</strong> не підключено. Кожне бронювання підтверджується людиною вручну.</li>
            <li>• <strong>ETA / GPS:</strong> з'являється лише після призначення воркера через Dispatch Center.</li>
            <li>• <strong>Партнерський акаунт GLF MaT:</strong> налаштування ще не завершено (немає окремої моделі Partner/RestaurantProfile).</li>
            <li>• Жодних фейкових замовлень, відгуків, рейтингів чи трекінгу на цій сторінці немає.</li>
        </ul>
    </div>

</x-filament-panels::page>
