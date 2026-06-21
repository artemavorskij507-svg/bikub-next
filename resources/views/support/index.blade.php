@extends($portal === 'account' ? 'layouts.account-shell' : 'worker.layout')

@section('title', $portal === 'worker' ? 'Центр поддержки' : 'Support')

@section('content')
@php
$isWorker = $portal === 'worker';

$statusLabels = [
    'open'        => 'Открыт',
    'in_progress' => 'В работе',
    'resolved'    => 'Решён',
    'closed'      => 'Закрыт',
];
$statusColors = [
    'open'        => ['bg' => 'rgba(85,217,255,.1)',  'border' => 'rgba(85,217,255,.28)',  'color' => '#dff7ff'],
    'in_progress' => ['bg' => 'rgba(245,189,84,.1)',  'border' => 'rgba(245,189,84,.28)',  'color' => '#fef3cd'],
    'resolved'    => ['bg' => 'rgba(var(--brand-rgb),.1)',  'border' => 'rgba(var(--brand-rgb),.28)',  'color' => '#d0fce8'],
    'closed'      => ['bg' => 'rgba(148,163,184,.08)','border' => 'rgba(148,163,184,.2)',  'color' => '#8fa5bd'],
];
$priorityColors = [
    'high'   => ['bg' => 'rgba(251,113,133,.1)', 'border' => 'rgba(251,113,133,.3)', 'color' => '#ffd9df'],
    'medium' => ['bg' => 'rgba(245,189,84,.1)',  'border' => 'rgba(245,189,84,.28)', 'color' => '#fef3cd'],
    'low'    => ['bg' => 'rgba(148,163,184,.08)','border' => 'rgba(148,163,184,.2)', 'color' => '#8fa5bd'],
    'normal' => ['bg' => 'rgba(148,163,184,.08)','border' => 'rgba(148,163,184,.2)', 'color' => '#8fa5bd'],
];
$priorityLabels = [
    'high'   => 'Высокий',
    'medium' => 'Средний',
    'low'    => 'Низкий',
    'normal' => 'Обычный',
];

$statsByStatus = [
    'open'        => $tickets->where('status', 'open')->count(),
    'in_progress' => $tickets->where('status', 'in_progress')->count(),
    'resolved'    => $tickets->where('status', 'resolved')->count(),
    'closed'      => $tickets->where('status', 'closed')->count(),
];

$activeFilter = request('status', '');
$activeCat    = request('category', '');

$categories = [
    ''             => 'Все',
    'order'        => 'Проблемы с заказом',
    'payment'      => 'Проблема с выплатой',
    'gps'          => 'GPS / маршрут',
    'shift'        => 'Смена / доступность',
    'documents'    => 'Документы / БазID',
];

$faqs = [
    [
        'q' => 'Почему мой заказ не подтверждён после прибытия?',
        'a' => 'Убедитесь, что вы нажали «Прибыл на точку» в кокпите. Если кнопка не работает — обновите страницу или откройте тикет, указав номер заказа и время прибытия.',
    ],
    [
        'q' => 'Выплата не поступила в ожидаемый срок — что делать?',
        'a' => 'Выплаты обрабатываются в рабочие дни до 15:00. Если средства не поступили в течение 2 рабочих дней — откройте тикет с темой «Проблема с выплатой» и укажите период.',
    ],
    [
        'q' => 'GPS не передаёт координаты — как исправить?',
        'a' => 'Разрешите доступ к местоположению в настройках браузера. В Chrome: значок замка → Местоположение → Разрешить. На iOS/Safari: Настройки → Safari → Местоположение → Разрешить. После изменения перезагрузите страницу.',
    ],
    [
        'q' => 'Как изменить доступность или отменить смену?',
        'a' => 'Изменения в расписании вносятся в разделе «Расписание». Отмена смены менее чем за 2 часа может повлиять на рейтинг. При форс-мажоре откройте тикет заблаговременно.',
    ],
];
@endphp

@if($isWorker)
{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- WORKER PORTAL — FULL DESIGN                                          --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}

<style>
.sp-header{display:grid;grid-template-columns:1fr auto;gap:1rem;align-items:flex-start;margin-bottom:1.5rem}
.sp-title-eyebrow{margin:0;color:var(--green);font-size:.68rem;font-weight:950;letter-spacing:.12em;text-transform:uppercase}
.sp-title{margin:.3rem 0 .35rem;font-size:clamp(1.6rem,4vw,2.4rem);font-weight:950;line-height:1.05;letter-spacing:-.02em}
.sp-subtitle{margin:0;color:var(--muted);font-size:.88rem;line-height:1.5}
.sp-actions{display:flex;gap:.65rem;align-items:center;flex-shrink:0;flex-wrap:wrap}
.sp-btn-primary{display:inline-flex;align-items:center;gap:.45rem;padding:.72rem 1.1rem;border-radius:10px;background:linear-gradient(135deg,var(--brand-a),var(--brand-b));border:1px solid rgba(var(--brand-rgb),.4);color:#fff;font-weight:850;font-size:.88rem;text-decoration:none;cursor:pointer;white-space:nowrap;box-shadow:0 4px 18px rgba(var(--brand-rgb),.2)}
.sp-btn-primary:hover{box-shadow:0 6px 24px rgba(var(--brand-rgb),.32)}
.sp-btn-urgent{display:inline-flex;align-items:center;gap:.45rem;padding:.7rem 1rem;border-radius:10px;background:rgba(245,189,84,.07);border:1px solid rgba(245,189,84,.4);color:var(--amber);font-weight:850;font-size:.88rem;text-decoration:none;cursor:pointer;white-space:nowrap}
.sp-btn-urgent:hover{background:rgba(245,189,84,.12)}

.sp-cats{display:flex;gap:.5rem;overflow-x:auto;padding-bottom:.25rem;margin-bottom:1.25rem;scrollbar-width:none;-webkit-overflow-scrolling:touch}
.sp-cats::-webkit-scrollbar{display:none}
.sp-cat{flex-shrink:0;padding:.42rem .85rem;border-radius:999px;border:1px solid var(--line);background:rgba(14,30,49,.6);color:var(--muted);font-size:.79rem;font-weight:750;text-decoration:none;white-space:nowrap;cursor:pointer;transition:all .15s}
.sp-cat:hover{border-color:rgba(var(--brand-rgb),.28);color:#d4faed;background:rgba(var(--brand-rgb),.06)}
.sp-cat.is-active{border-color:rgba(var(--brand-rgb),.45);background:rgba(var(--brand-rgb),.1);color:#c8f7e4;font-weight:900}

.sp-body{display:grid;grid-template-columns:1fr 240px;gap:1.25rem;align-items:flex-start}
.sp-list{display:grid;gap:.75rem;min-width:0}
.sp-sidebar{display:grid;gap:.85rem;position:sticky;top:1rem}

.sp-ticket-card{display:block;text-decoration:none;border:1px solid var(--line);border-radius:14px;background:var(--panel);padding:1rem 1.1rem;transition:border-color .15s,background .15s,box-shadow .15s;cursor:pointer}
.sp-ticket-card:hover{border-color:rgba(var(--brand-rgb),.25);background:rgba(12,27,45,.92);box-shadow:0 8px 32px rgba(0,0,0,.28)}
.sp-ticket-top{display:flex;justify-content:space-between;align-items:flex-start;gap:.75rem;margin-bottom:.55rem}
.sp-ticket-num{font-size:.7rem;font-weight:950;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);background:rgba(148,163,184,.1);border:1px solid rgba(148,163,184,.15);border-radius:6px;padding:.18rem .5rem}
.sp-ticket-pills{display:flex;gap:.4rem;align-items:center;flex-wrap:wrap;flex-shrink:0}
.sp-pill{display:inline-flex;align-items:center;padding:.2rem .55rem;border-radius:999px;font-size:.69rem;font-weight:900;text-transform:uppercase;letter-spacing:.05em;white-space:nowrap}
.sp-ticket-subject{font-size:.94rem;font-weight:750;color:var(--text);line-height:1.4;margin-bottom:.4rem}
.sp-ticket-meta{display:flex;align-items:center;gap:.75rem;font-size:.76rem;color:var(--muted)}
.sp-ticket-meta-dot{width:3px;height:3px;border-radius:50%;background:rgba(148,163,184,.35);flex-shrink:0}

.sp-stat-card{border:1px solid var(--line);border-radius:13px;background:var(--panel2);padding:.9rem 1rem}
.sp-stat-title{margin:0 0 .7rem;font-size:.65rem;font-weight:950;letter-spacing:.1em;text-transform:uppercase;color:var(--muted)}
.sp-stat-row{display:flex;justify-content:space-between;align-items:center;padding:.4rem 0;border-bottom:1px solid rgba(148,163,184,.1)}
.sp-stat-row:last-child{border-bottom:none;padding-bottom:0}
.sp-stat-label{font-size:.8rem;color:var(--muted)}
.sp-stat-count{font-size:.88rem;font-weight:900}

.sp-faq-section{margin-top:1.75rem}
.sp-faq-title{margin:0 0 .85rem;font-size:.68rem;font-weight:950;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)}
.sp-faq-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:.75rem}
.sp-faq-card{border:1px solid var(--line);border-radius:13px;background:var(--panel2);padding:.95rem 1rem}
.sp-faq-q{font-size:.85rem;font-weight:850;color:var(--text);margin:0 0 .45rem;line-height:1.4}
.sp-faq-a{font-size:.8rem;color:var(--muted);line-height:1.6;margin:0}

.sp-empty{display:grid;place-items:center;min-height:16rem;text-align:center}
.sp-empty-icon{display:grid;width:4.5rem;height:4.5rem;place-items:center;border-radius:999px;background:rgba(148,163,184,.08);border:1px solid rgba(148,163,184,.12);font-style:normal;font-size:1.9rem;margin:0 auto .85rem}
.sp-empty-heading{font-size:1rem;font-weight:850;color:var(--text);margin:0 0 .35rem}
.sp-empty-sub{font-size:.84rem;color:var(--muted);max-width:26rem;margin:0 auto .9rem;line-height:1.6}

@media(max-width:860px){
    .sp-header{grid-template-columns:1fr}
    .sp-actions{justify-content:flex-start}
    .sp-body{grid-template-columns:1fr}
    .sp-sidebar{position:static}
    .sp-faq-grid{grid-template-columns:1fr}
    .sp-ticket-top{flex-wrap:wrap}
}
</style>

{{-- Header --}}
<div class="sp-header">
    <div>
        <p class="sp-title-eyebrow">Worker support</p>
        <h1 class="sp-title">Центр поддержки</h1>
        <p class="sp-subtitle">Помощь по заказам, выплатам, смене и GPS</p>
    </div>
    <div class="sp-actions">
        <span class="sp-btn-primary" title="Тикеты создаются диспетчером или автоматически при споре по заказу" style="opacity:.55;cursor:not-allowed">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Создать тикет
        </span>
        <a href="tel:+4780000000" class="sp-btn-urgent">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.72a19.79 19.79 0 01-3.07-8.63A2 2 0 012 1h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
            Срочная помощь
        </span>
    </div>
</div>

@if($isWorker)
<section style="display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.65rem;margin:-.35rem 0 1.15rem" class="sp-issue-grid">
  @foreach([['⚙️','Operational issue','Order buttons, proof or assignment problem'],['📍','GPS/location','Browser permission or map problem'],['💳','Payout issue','Settlement or payout readiness'],['🚨','Emergency','Use local emergency services first if safety is at risk']] as $item)
    <article style="border:1px solid var(--line);border-radius:16px;background:var(--panel2);padding:.82rem"><strong style="display:block;font-size:.86rem">{{ $item[0] }} {{ $item[1] }}</strong><span style="display:block;color:var(--muted);font-size:.72rem;margin-top:.22rem">{{ $item[2] }}</span></article>
  @endforeach
</section>
<style>@media(max-width:860px){.sp-issue-grid{grid-template-columns:1fr 1fr!important}}@media(max-width:520px){.sp-issue-grid{grid-template-columns:1fr!important}}</style>
@endif

{{-- Category filter --}}
<nav class="sp-cats" aria-label="Категории тикетов">
    @foreach($categories as $slug => $label)
        <a href="{{ route('worker.support.index', array_filter(['category' => $slug ?: null, 'status' => $activeFilter ?: null])) }}"
           class="sp-cat{{ $activeCat === $slug ? ' is-active' : '' }}">
            {{ $label }}
        </a>
    @endforeach
</nav>

{{-- Main body: list + sidebar --}}
<div class="sp-body">

    {{-- Ticket list --}}
    <div class="sp-list">
        @forelse($tickets as $ticket)
        @php
            $st  = $ticket->status ?? 'open';
            $pri = $ticket->priority ?? 'normal';
            $sc  = $statusColors[$st]  ?? $statusColors['open'];
            $pc  = $priorityColors[$pri] ?? $priorityColors['normal'];
            $sl  = $statusLabels[$st]  ?? ucfirst($st);
            $pl  = $priorityLabels[$pri] ?? ucfirst($pri);
        @endphp
        <a href="{{ route('worker.support.show', $ticket) }}" class="sp-ticket-card">
            <div class="sp-ticket-top">
                <span class="sp-ticket-num">{{ $ticket->ticket_number }}</span>
                <div class="sp-ticket-pills">
                    @if(in_array($pri, ['high', 'medium']))
                    <span class="sp-pill"
                          style="background:{{ $pc['bg'] }};border:1px solid {{ $pc['border'] }};color:{{ $pc['color'] }}">
                        {{ $pl }}
                    </span>
                    @endif
                    <span class="sp-pill"
                          style="background:{{ $sc['bg'] }};border:1px solid {{ $sc['border'] }};color:{{ $sc['color'] }}">
                        {{ $sl }}
                    </span>
                </div>
            </div>
            <p class="sp-ticket-subject">{{ str($ticket->subject)->limit(88) }}</p>
            <div class="sp-ticket-meta">
                <span>{{ $ticket->created_at?->format('d.m.Y') }}</span>
                @if(isset($ticket->messages) && ($msgCount = $ticket->messages->count()) > 0)
                    <span class="sp-ticket-meta-dot"></span>
                    <span>{{ $msgCount }} {{ $msgCount === 1 ? 'сообщение' : ($msgCount < 5 ? 'сообщения' : 'сообщений') }}</span>
                @endif
            </div>
        </a>
        @empty
        <div class="sp-empty">
            <div>
                <i class="sp-empty-icon">🗂</i>
                <h3 class="sp-empty-heading">У вас нет обращений</h3>
                <p class="sp-empty-sub">Здесь появятся ваши тикеты в службу поддержки. Создайте первое обращение, если у вас возникла проблема с заказом, выплатой или доступностью.</p>
                <p style="color:var(--muted);font-size:.78rem;margin:.65rem 0 0">Тикеты создаются диспетчером или автоматически при оспаривании заказа.</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Sidebar: stats --}}
    <aside class="sp-sidebar">
        <div class="sp-stat-card">
            <p class="sp-stat-title">Статусы тикетов</p>
            <div class="sp-stat-row">
                <span class="sp-stat-label">Открыт</span>
                <span class="sp-stat-count" style="color:#dff7ff">{{ $statsByStatus['open'] }}</span>
            </div>
            <div class="sp-stat-row">
                <span class="sp-stat-label">В работе</span>
                <span class="sp-stat-count" style="color:#fef3cd">{{ $statsByStatus['in_progress'] }}</span>
            </div>
            <div class="sp-stat-row">
                <span class="sp-stat-label">Решён</span>
                <span class="sp-stat-count" style="color:#d0fce8">{{ $statsByStatus['resolved'] }}</span>
            </div>
            <div class="sp-stat-row">
                <span class="sp-stat-label">Закрыт</span>
                <span class="sp-stat-count" style="color:var(--muted)">{{ $statsByStatus['closed'] }}</span>
            </div>
        </div>

        <div class="sp-stat-card" style="background:rgba(245,189,84,.05);border-color:rgba(245,189,84,.2)">
            <p class="sp-stat-title" style="color:rgba(245,189,84,.75)">Срочная помощь</p>
            <p style="font-size:.8rem;color:var(--muted);margin:0 0 .75rem;line-height:1.5">Критическая ситуация? Позвоните диспетчеру напрямую.</p>
            <a href="tel:+4780000000" class="sp-btn-urgent" style="width:100%;justify-content:center;font-size:.82rem">
                Позвонить
            </a>
        </div>
    </aside>

</div>

{{-- FAQ section --}}
<section class="sp-faq-section">
    <p class="sp-faq-title">Частые решения</p>
    <div class="sp-faq-grid">
        @foreach($faqs as $faq)
        <div class="sp-faq-card">
            <p class="sp-faq-q">{{ $faq['q'] }}</p>
            <p class="sp-faq-a">{{ $faq['a'] }}</p>
        </div>
        @endforeach
    </div>
</section>

@else
{{-- ══════════════════════════════════════════════════════════════════════ --}}
{{-- ACCOUNT PORTAL — BASIC                                               --}}
{{-- ══════════════════════════════════════════════════════════════════════ --}}

<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap">
    <div>
        <span class="shell-eyebrow">Account support</span>
        <h1 style="margin:.35rem 0 .4rem;font-size:clamp(1.5rem,4vw,2.2rem);font-weight:950">Support tickets</h1>
        <p style="color:var(--shell-muted);margin:0">Open a ticket for any issue with orders, payments or account access.</p>
    </div>
    <a class="shell-primary" href="{{ route('account.support.create') }}"
       style="display:inline-flex;align-items:center;text-decoration:none;padding:.65rem 1.1rem;border-radius:6px">
        New ticket
    </a>
</div>

@forelse($tickets as $ticket)
    <a href="{{ route('account.support.show', $ticket) }}"
       style="display:block;text-decoration:none;color:inherit;margin-bottom:.65rem">
        <article class="shell-card" style="display:grid;grid-template-columns:1fr auto;gap:.85rem;align-items:center">
            <div>
                <strong>{{ $ticket->ticket_number }}</strong>
                <p style="margin:.2rem 0 0;color:var(--shell-muted);font-size:.85rem">{{ str($ticket->subject)->limit(72) }}</p>
            </div>
            <div style="text-align:right">
                <span style="display:inline-flex;border:1px solid rgba(37,220,145,.24);border-radius:999px;background:rgba(37,220,145,.06);padding:.2rem .55rem;color:var(--bkb-accent,#25dc91);font-size:.72rem;font-weight:900;text-transform:uppercase">
                    @php $st = $ticket->status ?? 'open'; @endphp
                    {{ ['open'=>'Open','in_progress'=>'In progress','resolved'=>'Resolved','closed'=>'Closed'][$st] ?? ucfirst($st) }}
                </span>
                <p style="margin:.25rem 0 0;font-size:.75rem;color:var(--shell-muted)">{{ str($ticket->priority ?? 'normal')->title() }}</p>
            </div>
        </article>
    </a>
@empty
    <article class="shell-card" style="text-align:center;padding:3rem 1.5rem">
        <p style="margin:0 0 1rem;color:var(--shell-muted)">No support tickets yet.</p>
        <a class="shell-primary" href="{{ route('account.support.create') }}"
           style="display:inline-flex;text-decoration:none;padding:.65rem 1.1rem;border-radius:6px">
            Create first ticket
        </a>
    </article>
@endforelse

@endif

@endsection
