@extends('worker.layout')
@section('title', 'Уведомления')
@section('content')
<style>
.cockpit-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,330px);gap:1rem;align-items:start}
.main-stack,.side-stack{display:grid;gap:.75rem}
.panel{border:1px solid var(--line);border-radius:14px;background:var(--panel)}
.panel-inner{padding:1rem}
.panel-head{display:flex;justify-content:space-between;align-items:center;gap:.75rem;margin-bottom:.85rem}
.panel-title{font-size:.96rem;font-weight:950;margin:0}
.badge{display:inline-flex;align-items:center;border:1px solid var(--line);border-radius:999px;padding:.15rem .52rem;font-size:.68rem;font-weight:900;letter-spacing:.04em;color:var(--muted);background:rgba(148,163,184,.08)}
.badge-green{border-color:rgba(var(--brand-rgb),.32);background:rgba(var(--brand-rgb),.1);color:var(--green)}
.badge-blue{border-color:rgba(85,217,255,.28);background:rgba(85,217,255,.08);color:var(--blue)}
.badge-amber{border-color:rgba(245,189,84,.28);background:rgba(245,189,84,.08);color:var(--amber)}
.badge-muted{border-color:rgba(148,163,184,.18);background:rgba(148,163,184,.07);color:var(--muted)}
.status-pill{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .75rem;border-radius:999px;border:1px solid rgba(var(--brand-rgb),.28);background:rgba(var(--brand-rgb),.08);color:var(--green);font-size:.75rem;font-weight:900;white-space:nowrap}
.status-dot{width:.45rem;height:.45rem;border-radius:999px;background:var(--green);box-shadow:0 0 8px rgba(var(--brand-rgb),.6);flex-shrink:0}
.side-card{border:1px solid var(--line);border-radius:11px;background:var(--panel2);padding:.75rem}
.list-row{display:flex;justify-content:space-between;align-items:center;gap:.75rem;padding:.55rem 0;border-bottom:1px solid var(--line)}
.list-row:last-child{border-bottom:none}
.card-list{display:grid;gap:.55rem}
/* Stat boxes */
.stat-boxes{display:grid;grid-template-columns:repeat(3,1fr);gap:.65rem;margin-bottom:.85rem}
.stat-box{border:1px solid var(--line);border-radius:11px;background:var(--panel2);padding:.7rem .85rem}
.stat-box-label{color:var(--muted);font-size:.68rem;font-weight:950;text-transform:uppercase;letter-spacing:.05em;margin:0 0 .3rem}
.stat-box-value{font-size:1.55rem;font-weight:950;line-height:1}
.stat-box-value.green{color:var(--green)}
.stat-box-value.blue{color:var(--blue)}
.stat-box-value.amber{color:var(--amber)}
/* Category tabs */
.cat-tabs-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;padding-bottom:.25rem;margin-bottom:.75rem}
.cat-tabs{display:flex;gap:.35rem;white-space:nowrap}
.cat-tab{display:inline-flex;align-items:center;gap:.3rem;padding:.38rem .72rem;border-radius:8px;border:1px solid transparent;font-size:.8rem;font-weight:850;cursor:pointer;background:transparent;color:var(--muted);transition:background .13s,color .13s,border-color .13s}
.cat-tab.is-active{border-color:rgba(var(--brand-rgb),.28);background:rgba(var(--brand-rgb),.08);color:var(--green)}
.cat-tab:hover:not(.is-active){background:rgba(148,163,184,.07);color:var(--text)}
/* Date group headers */
.date-group-head{display:flex;align-items:center;gap:.65rem;margin:.85rem 0 .45rem;color:var(--muted);font-size:.75rem;font-weight:950;text-transform:uppercase;letter-spacing:.06em}
.date-group-head:first-child{margin-top:0}
.date-group-line{flex:1;height:1px;background:var(--line)}
/* Notification cards */
.item-card{display:grid;grid-template-columns:2.5rem 1fr auto;gap:.75rem;align-items:start;border:1px solid var(--line);border-radius:12px;background:var(--panel2);padding:.85rem;color:inherit}
.item-card.unread{border-color:rgba(var(--brand-rgb),.28);background:rgba(var(--brand-rgb),.04)}
.notif-icon-circle{width:2.5rem;height:2.5rem;border-radius:999px;display:grid;place-items:center;font-size:1.1rem;flex-shrink:0;background:rgba(var(--brand-rgb),.1);border:1px solid rgba(var(--brand-rgb),.18)}
.notif-icon-circle.cat-orders{background:rgba(85,217,255,.1);border-color:rgba(85,217,255,.2)}
.notif-icon-circle.cat-schedule{background:rgba(245,189,84,.1);border-color:rgba(245,189,84,.2)}
.notif-icon-circle.cat-finance{background:rgba(var(--brand-rgb),.12);border-color:rgba(var(--brand-rgb),.22)}
.notif-icon-circle.cat-status{background:rgba(148,163,184,.1);border-color:rgba(148,163,184,.18)}
.notif-icon-circle.cat-system{background:rgba(148,163,184,.08);border-color:rgba(148,163,184,.14)}
.item-meta{display:flex;flex-wrap:wrap;align-items:center;gap:.35rem;margin-bottom:.3rem}
.notif-title{margin:0 0 .2rem;font-weight:950;font-size:.92rem;color:var(--text)}
.notif-body{margin:0;font-size:.81rem;line-height:1.45;color:var(--muted)}
.notif-time{font-size:.72rem;color:var(--muted);white-space:nowrap;margin-top:.15rem;text-align:right}
.notif-actions{display:flex;flex-direction:column;align-items:flex-end;gap:.35rem;flex-shrink:0}
.a-btn{display:inline-flex;align-items:center;justify-content:center;min-height:2.3rem;border:1px solid var(--line);border-radius:9px;background:rgba(14,30,49,.86);color:var(--text);padding:.45rem .8rem;font-weight:850;text-decoration:none;cursor:pointer;font-size:.82rem;white-space:nowrap;transition:background .15s}
.a-btn-primary{border-color:rgba(var(--brand-rgb),.48);background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#fff}
.a-btn-ghost{border-color:transparent;background:transparent;color:var(--muted)}
.a-btn-sm{min-height:1.9rem;padding:.32rem .6rem;font-size:.76rem}
.a-btn:disabled{opacity:.45;cursor:not-allowed}
.toolbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:.75rem}
.empty-state{display:grid;place-items:center;min-height:6rem;color:var(--muted);font-size:.85rem;text-align:center;padding:1.5rem}
.compact-table{width:100%;border-collapse:collapse;font-size:.82rem}
.compact-table th{color:var(--muted);font-size:.68rem;text-transform:uppercase;letter-spacing:.04em;padding:.6rem .75rem;border-bottom:1px solid var(--line);text-align:left;white-space:nowrap}
.compact-table td{padding:.65rem .75rem;border-bottom:1px solid var(--line);color:#cbd5e1}
.compact-table tr:last-child td{border-bottom:none}
.new-badge{display:inline-flex;align-items:center;border-radius:999px;padding:.1rem .42rem;font-size:.62rem;font-weight:900;letter-spacing:.04em;background:rgba(var(--brand-rgb),.18);color:var(--green);border:1px solid rgba(var(--brand-rgb),.32)}
@media(max-width:860px){.cockpit-grid{grid-template-columns:1fr}.stat-boxes{grid-template-columns:repeat(3,1fr)}.item-card{grid-template-columns:2.5rem 1fr}}
</style>

@php
use Carbon\Carbon;

$allNotifs  = $unread->merge($read)->sortByDesc('created_at');
$todayCount = $allNotifs->filter(fn($n) => $n->created_at->isToday())->count();
$actionCount = $allNotifs->filter(fn($n) => data_get($n->data,'action_url'))->count();

// Date groups for unread
$today     = $unread->filter(fn($n) => $n->created_at->isToday());
$yesterday = $unread->filter(fn($n) => $n->created_at->isYesterday());
$earlier   = $unread->filter(fn($n) => !$n->created_at->isToday() && !$n->created_at->isYesterday());
@endphp

<div id="notif-root">
    {{-- Page head --}}
    <div class="worker-page-head">
        <div>
            <p class="worker-hero-eyebrow">Центр уведомлений</p>
            <h1 style="margin:0;font-size:clamp(1.6rem,3vw,2.4rem);line-height:1">Уведомления</h1>
            <p class="muted" style="margin:.3rem 0 0">События заказов, изменения статусов и системные оповещения.</p>
        </div>
        <div class="status-pill">
            <span class="status-dot"></span>
            <span>{{ $unread->count() }} непрочитанных</span>
        </div>
    </div>

    <div class="cockpit-grid">
        <div class="main-stack">
            {{-- Mark all read --}}
            @if($unread->isNotEmpty())
            <div class="panel" id="mark-all-wrap">
                <div class="panel-inner" style="display:flex;justify-content:space-between;align-items:center;gap:1rem">
                    <p class="muted" style="margin:0;font-size:.83rem">У вас <strong style="color:var(--text)">{{ $unread->count() }}</strong> непрочитанных уведомлений.</p>
                    <button class="a-btn a-btn-primary" type="button" id="mark-all-btn">
                        Отметить все прочитанными
                    </button>
                </div>
            </div>
            @endif

            {{-- Category tabs --}}
            <div class="cat-tabs-wrap">
                <div class="cat-tabs" id="cat-tabs" role="tablist" aria-label="Категории уведомлений">
                    <button class="cat-tab is-active" data-cat="all" role="tab" aria-selected="true">Все <span class="badge" id="cnt-all">{{ $unread->count() }}</span></button>
                    <button class="cat-tab" data-cat="orders"   role="tab" aria-selected="false">📦 Заказы</button>
                    <button class="cat-tab" data-cat="schedule" role="tab" aria-selected="false">🗓 Смены</button>
                    <button class="cat-tab" data-cat="finance"  role="tab" aria-selected="false">💳 Финансы</button>
                    <button class="cat-tab" data-cat="status"   role="tab" aria-selected="false">📊 Статусы</button>
                    <button class="cat-tab" data-cat="system"   role="tab" aria-selected="false">⚙️ Система</button>
                </div>
            </div>

            {{-- Unread --}}
            <section id="unread" class="panel">
                <div class="panel-inner">
                    <div class="panel-head">
                        <h2 class="panel-title">Непрочитанные</h2>
                        <span class="muted" style="font-size:.8rem">{{ $unread->count() }} записей</span>
                    </div>

                    @if($unread->isEmpty())
                        <div class="empty-state">
                            <div>
                                <div style="font-size:2rem;margin-bottom:.5rem">🔔</div>
                                Непрочитанных уведомлений нет.
                            </div>
                        </div>
                    @else
                        <div class="card-list" id="unread-list">

                            @php
                            $groups = [
                                'Сегодня'  => $today,
                                'Вчера'    => $yesterday,
                                'Ранее'    => $earlier,
                            ];
                            @endphp

                            @foreach($groups as $groupLabel => $groupItems)
                                @if($groupItems->isNotEmpty())
                                <div class="date-group-head" data-group="{{ $groupLabel }}">
                                    <span>{{ $groupLabel }}</span>
                                    <span class="date-group-line"></span>
                                    <span class="badge">{{ $groupItems->count() }}</span>
                                </div>
                                @foreach($groupItems as $n)
                                @php
                                    $title    = data_get($n->data,'title') ?? data_get($n->data,'subject') ?? class_basename($n->type);
                                    $body     = data_get($n->data,'body') ?? data_get($n->data,'message') ?? data_get($n->data,'text');
                                    $icon     = data_get($n->data,'icon') ?? '🔔';
                                    $category = data_get($n->data,'category') ?? 'system';
                                    $catClass = 'cat-' . $category;
                                @endphp
                                <article class="item-card unread" data-notif-id="{{ $n->id }}" data-category="{{ $category }}">
                                    <div class="notif-icon-circle {{ $catClass }}" aria-hidden="true">{{ $icon }}</div>
                                    <div>
                                        <div class="item-meta">
                                            <span class="new-badge">Новое</span>
                                            <span class="badge badge-muted">{{ $category }}</span>
                                        </div>
                                        <p class="notif-title">{{ $title }}</p>
                                        @if($body)<p class="notif-body">{{ $body }}</p>@endif
                                    </div>
                                    <div class="notif-actions">
                                        <span class="notif-time">{{ $n->created_at->format('H:i') }}</span>
                                        <button type="button"
                                            class="a-btn a-btn-ghost a-btn-sm mark-read-btn"
                                            data-url="{{ route('worker.notifications.read', $n->id) }}"
                                            data-csrf="{{ csrf_token() }}">
                                            ✓ Прочитано
                                        </button>
                                    </div>
                                </article>
                                @endforeach
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            {{-- History --}}
            <section id="history" class="panel">
                <div class="panel-inner">
                    <div class="panel-head">
                        <h2 class="panel-title">История</h2>
                        <span class="muted" style="font-size:.8rem">{{ $read->count() }} записей</span>
                    </div>
                    @if($read->isEmpty())
                        <div class="empty-state">История уведомлений пуста.</div>
                    @else
                        <div style="overflow-x:auto">
                            <table class="compact-table">
                                <thead>
                                    <tr>
                                        <th>Событие</th>
                                        <th>Описание</th>
                                        <th>Дата</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($read as $n)
                                    @php
                                        $title = data_get($n->data,'title') ?? data_get($n->data,'subject') ?? class_basename($n->type);
                                        $body  = data_get($n->data,'body') ?? data_get($n->data,'message') ?? '—';
                                    @endphp
                                    <tr data-notif>
                                        <td style="font-weight:850;color:var(--text)">{{ $title }}</td>
                                        <td class="muted">{{ Str::limit($body, 80) }}</td>
                                        <td style="white-space:nowrap">{{ $n->created_at->format('d.m.Y H:i') }}</td>
                                        <td><span class="badge badge-green">Прочитано</span></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- Sidebar --}}
        <aside class="side-stack">
            <section class="panel">
                <div class="panel-inner">
                    <div class="panel-head"><h2 class="panel-title">Статистика</h2></div>
                    <div class="stat-boxes">
                        <div class="stat-box">
                            <p class="stat-box-label">Непрочит.</p>
                            <div class="stat-box-value green">{{ $unread->count() }}</div>
                        </div>
                        <div class="stat-box">
                            <p class="stat-box-label">Сегодня</p>
                            <div class="stat-box-value blue">{{ $todayCount }}</div>
                        </div>
                        <div class="stat-box">
                            <p class="stat-box-label">С ссылкой</p>
                            <div class="stat-box-value amber">{{ $actionCount }}</div>
                        </div>
                    </div>
                    <div class="side-card">
                        <div class="list-row"><span class="muted">Непрочитанных</span><strong>{{ $unread->count() }}</strong></div>
                        <div class="list-row"><span class="muted">Прочитанных</span><strong>{{ $read->count() }}</strong></div>
                        <div class="list-row"><span class="muted">Всего</span><strong>{{ $unread->count() + $read->count() }}</strong></div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-inner">
                    <div class="panel-head"><h2 class="panel-title">Быстрые действия</h2></div>
                    <div class="card-list">
                        <a href="{{ route('worker.orders.index') }}" class="a-btn" style="justify-content:flex-start;gap:.5rem">📦 Открыть заказы</a>
                        <a href="{{ route('worker.support.index') }}" class="a-btn" style="justify-content:flex-start;gap:.5rem">🛟 Служба поддержки</a>
                        <a href="{{ route('worker.dashboard') }}" class="a-btn" style="justify-content:flex-start;gap:.5rem">🏠 Главная</a>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>

@push('scripts')
<script>
(function () {
    'use strict';

    var CSRF = document.querySelector('meta[name="csrf-token"]')
              ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              : '';

    /* ── Mark-one-read ── */
    document.querySelectorAll('.mark-read-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (btn.disabled) return;
            btn.disabled = true;
            btn.textContent = '…';
            var url = btn.getAttribute('data-url');
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                }
            })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (d) {
                if (d && d.success) {
                    var card = btn.closest('[data-notif-id]');
                    if (card) {
                        card.style.transition = 'opacity .25s';
                        card.style.opacity = '0';
                        setTimeout(function () { card.remove(); }, 280);
                    }
                } else {
                    btn.disabled = false;
                    btn.textContent = '✓ Прочитано';
                }
            })
            .catch(function () {
                btn.disabled = false;
                btn.textContent = '✓ Прочитано';
            });
        });
    });

    /* ── Mark-all-read ── */
    var markAllBtn = document.getElementById('mark-all-btn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function () {
            if (markAllBtn.disabled) return;
            markAllBtn.disabled = true;
            markAllBtn.textContent = 'Обработка…';
            fetch('{{ route("worker.notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json'
                }
            })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (d) {
                if (d && (d.success || d.ok)) {
                    window.location.reload();
                } else {
                    markAllBtn.disabled = false;
                    markAllBtn.textContent = 'Отметить все прочитанными';
                }
            })
            .catch(function () {
                markAllBtn.disabled = false;
                markAllBtn.textContent = 'Отметить все прочитанными';
            });
        });
    }

    /* ── Category tab filter ── */
    var tabs = document.querySelectorAll('.cat-tab');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var cat = tab.getAttribute('data-cat');

            // Update active state
            tabs.forEach(function (t) {
                t.classList.remove('is-active');
                t.setAttribute('aria-selected', 'false');
            });
            tab.classList.add('is-active');
            tab.setAttribute('aria-selected', 'true');

            // Filter cards
            var cards = document.querySelectorAll('[data-notif-id]');
            var groupHeads = document.querySelectorAll('[data-group]');

            cards.forEach(function (card) {
                if (cat === 'all') {
                    card.style.display = '';
                } else {
                    card.style.display = card.getAttribute('data-category') === cat ? '' : 'none';
                }
            });

            // Hide empty group headers
            groupHeads.forEach(function (head) {
                var groupLabel = head.getAttribute('data-group');
                // Find the sibling cards belonging to this group
                var next = head.nextElementSibling;
                var anyVisible = false;
                while (next && !next.hasAttribute('data-group')) {
                    if (next.hasAttribute('data-notif-id') && next.style.display !== 'none') {
                        anyVisible = true;
                    }
                    next = next.nextElementSibling;
                }
                head.style.display = anyVisible ? '' : 'none';
            });
        });
    });

})();
</script>
@endpush
@endsection
