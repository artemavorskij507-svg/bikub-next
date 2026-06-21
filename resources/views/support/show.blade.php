@extends($portal === 'account' ? 'layouts.account-shell' : 'worker.layout')
@section('title', $ticket->ticket_number)
@section('content')
@php($isWorker = $portal === 'worker')
<style>
.ticket-shell{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:1rem;align-items:start}
.ticket-main{display:grid;gap:.75rem}
.ticket-detail-panel{display:grid;gap:.75rem}
.tp{border:1px solid var(--line);border-radius:14px;background:var(--panel)}
.tp-inner{padding:1rem}
.tp-title{font-size:.72rem;font-weight:950;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin:0 0 .75rem}
.kv-row{display:flex;justify-content:space-between;align-items:center;padding:.42rem 0;border-bottom:1px solid rgba(148,163,184,.08);font-size:.82rem}
.kv-row:last-child{border-bottom:none}
.s-pill{display:inline-flex;align-items:center;border-radius:999px;padding:.18rem .55rem;font-size:.64rem;font-weight:900;text-transform:uppercase;letter-spacing:.04em;border:1px solid}
.sp-open{border-color:rgba(var(--brand-rgb),.3);background:rgba(var(--brand-rgb),.08);color:var(--green)}
.sp-in_progress{border-color:rgba(85,217,255,.3);background:rgba(85,217,255,.08);color:var(--blue)}
.sp-resolved{border-color:rgba(245,189,84,.3);background:rgba(245,189,84,.08);color:var(--amber)}
.sp-closed{border-color:rgba(148,163,184,.2);background:rgba(148,163,184,.05);color:var(--muted)}
.pr-high{border-color:rgba(251,113,133,.3);background:rgba(251,113,133,.06);color:var(--danger)}
.pr-medium{border-color:rgba(245,189,84,.3);background:rgba(245,189,84,.08);color:var(--amber)}
.pr-low,.pr-normal{border-color:rgba(148,163,184,.2);background:rgba(148,163,184,.05);color:var(--muted)}
.thread{display:flex;flex-direction:column;gap:.55rem}
.msg-row{display:flex;flex-direction:column}
.msg-row.worker-msg{align-items:flex-end}
.msg-row.support-msg{align-items:flex-start}
.msg-row.system-msg{align-items:center}
.bubble-wrap{display:flex;align-items:flex-end;gap:.45rem;max-width:82%}
.bubble-wrap.worker-msg{flex-direction:row-reverse}
.av{width:28px;height:28px;border-radius:50%;display:grid;place-items:center;font-size:.7rem;font-weight:900;flex-shrink:0}
.av-w{background:linear-gradient(135deg,var(--brand-mark-a),var(--brand-mark-b));color:#02130d}
.av-s{background:rgba(85,217,255,.15);border:1px solid rgba(85,217,255,.25);color:var(--blue)}
.av-sys{background:rgba(148,163,184,.08);border:1px solid rgba(148,163,184,.15);color:var(--muted)}
.bubble{border-radius:13px;padding:.6rem .85rem;font-size:.83rem;line-height:1.55;word-break:break-word}
.bubble.bw{background:linear-gradient(145deg,rgba(20,52,38,.9),rgba(12,31,24,.95));border:1px solid rgba(var(--brand-rgb),.22);color:#dffcf0;border-bottom-right-radius:3px}
.bubble.bs{background:rgba(15,31,50,.88);border:1px solid rgba(85,217,255,.18);color:#d4ecff;border-bottom-left-radius:3px}
.bubble.bsys{background:rgba(148,163,184,.05);border:1px solid rgba(148,163,184,.1);color:var(--muted);font-size:.74rem;font-style:italic;border-radius:999px;padding:.28rem .9rem}
.msg-meta{font-size:.63rem;color:rgba(148,163,184,.45);margin:.25rem .25rem 0}
.reply-form textarea{width:100%;border:1px solid var(--line);border-radius:11px;background:rgba(4,12,23,.75);color:var(--text);padding:.75rem;font-family:inherit;font-size:.86rem;resize:vertical;min-height:90px;box-sizing:border-box;transition:border-color .2s}
.reply-form textarea:focus{outline:none;border-color:rgba(var(--brand-rgb),.4)}
.send-btn{display:inline-flex;align-items:center;gap:.5rem;margin-top:.6rem;padding:.62rem 1.2rem;border-radius:10px;border:1px solid rgba(var(--brand-rgb),.45);background:linear-gradient(135deg,var(--brand-a),var(--brand-b));color:#fff;font-weight:950;font-size:.86rem;cursor:pointer}
@media(max-width:900px){.ticket-shell{grid-template-columns:1fr}.ticket-detail-panel{display:none}}
</style>

@php
$stMap  = ['open'=>'Открыт','in_progress'=>'В работе','resolved'=>'Решён','closed'=>'Закрыт'];
$prMap  = ['high'=>'Высокий','medium'=>'Средний','low'=>'Низкий','normal'=>'Обычный'];
$stLabel = $stMap[$ticket->status] ?? ucfirst($ticket->status);
$prLabel = $prMap[$ticket->priority] ?? ucfirst($ticket->priority ?? 'normal');
@endphp

{{-- Header --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;margin-bottom:1rem;flex-wrap:wrap">
    <div>
        @if($isWorker)<p style="color:var(--muted);font-size:.62rem;font-weight:900;text-transform:uppercase;letter-spacing:.1em;margin:0 0 4px">Тикет поддержки</p>@endif
        <h1 style="margin:0 0 .3rem;font-size:1.45rem;font-weight:950;line-height:1">{{ $ticket->ticket_number }}</h1>
        <p style="margin:0;color:var(--muted);font-size:.82rem">{{ str($ticket->subject)->limit(80) }}</p>
    </div>
    <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap">
        <span class="s-pill sp-{{ $ticket->status }}">{{ $stLabel }}</span>
        <span class="s-pill pr-{{ $ticket->priority ?? 'normal' }}">{{ $prLabel }}</span>
        <a href="{{ route($portal.'.support.index') }}" style="padding:.42rem .75rem;border-radius:8px;border:1px solid var(--line);text-decoration:none;color:var(--muted);font-size:.78rem">← Все тикеты</a>
    </div>
</div>

<div class="ticket-shell">
    <div class="ticket-main">
        {{-- Thread --}}
        <div class="tp">
            <div class="tp-inner">
                <p class="tp-title">Переписка</p>
                @if($ticket->messages->isEmpty())
                <div style="text-align:center;padding:2rem;color:var(--muted)">
                    <div style="font-size:1.8rem;margin-bottom:.5rem">💬</div>
                    <p style="margin:0;font-size:.82rem">Сообщений пока нет. Используйте форму ниже для ответа.</p>
                </div>
                @else
                <div class="thread">
                    @foreach($ticket->messages as $msg)
                    @php
                        $isSystem   = $msg->is_system ?? false;
                        $authorType = $msg->author_type ?? 'system';
                        $isW   = $authorType === 'worker';
                        $isSys = $isSystem || $authorType === 'system';
                        $rowClass = $isSys ? 'system-msg' : ($isW ? 'worker-msg' : 'support-msg');
                        $avClass  = $isSys ? 'av-sys' : ($isW ? 'av-w' : 'av-s');
                        $avLabel  = $isSys ? '⚙' : ($isW ? mb_substr(auth()->user()?->name ?? 'W', 0, 1) : 'B');
                        $bClass   = $isSys ? 'bsys' : ($isW ? 'bw' : 'bs');
                        $metaName = $isSys ? '' : ($isW ? 'Вы' : 'Поддержка BiKuBe');
                    @endphp
                    <div class="msg-row {{ $rowClass }}">
                        <div class="bubble-wrap {{ $rowClass }}">
                            @if(!$isSys)<div class="av {{ $avClass }}">{{ $avLabel }}</div>@endif
                            <div class="bubble {{ $bClass }}">{{ $msg->body }}</div>
                        </div>
                        @if($msg->created_at)
                        <div class="msg-meta" style="{{ $isW ? 'text-align:right' : '' }}">
                            @if($metaName){{ $metaName }} · @endif{{ $msg->created_at->format('d.m.Y H:i') }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Reply form --}}
        @if(in_array($ticket->status, ['open','in_progress']))
        <div class="tp">
            <div class="tp-inner reply-form">
                <p class="tp-title">Ответить</p>
                <form method="POST" action="{{ route($portal.'.support.reply', $ticket) }}">
                    @csrf
                    <textarea name="body" required placeholder="Опишите проблему или уточните детали…"></textarea>
                    <button type="submit" class="send-btn">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                        Отправить ответ
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="tp" style="border-color:rgba(148,163,184,.1)">
            <div class="tp-inner" style="text-align:center;color:var(--muted);padding:1.2rem">
                <p style="margin:0;font-size:.8rem">Тикет {{ strtolower($stLabel) }} — ответы недоступны.<br>Создайте новый тикет если вопрос не решён.</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Right details --}}
    <aside class="ticket-detail-panel">
        <div class="tp">
            <div class="tp-inner">
                <p class="tp-title">Детали тикета</p>
                <div class="kv-row"><span style="color:var(--muted)">Номер</span><strong style="font-size:.8rem">{{ $ticket->ticket_number }}</strong></div>
                <div class="kv-row"><span style="color:var(--muted)">Статус</span><span class="s-pill sp-{{ $ticket->status }}">{{ $stLabel }}</span></div>
                <div class="kv-row"><span style="color:var(--muted)">Приоритет</span><span class="s-pill pr-{{ $ticket->priority ?? 'normal' }}">{{ $prLabel }}</span></div>
                <div class="kv-row"><span style="color:var(--muted)">Создан</span><span style="font-size:.76rem;color:var(--text)">{{ $ticket->created_at?->format('d.m.Y H:i') }}</span></div>
                @if($ticket->updated_at && $ticket->updated_at->ne($ticket->created_at))
                <div class="kv-row"><span style="color:var(--muted)">Обновлён</span><span style="font-size:.76rem">{{ $ticket->updated_at->diffForHumans() }}</span></div>
                @endif
                <div class="kv-row"><span style="color:var(--muted)">Сообщений</span><strong>{{ $ticket->messages->count() }}</strong></div>
            </div>
        </div>
        @if($ticket->summary)
        <div class="tp">
            <div class="tp-inner">
                <p class="tp-title">Описание</p>
                <p style="color:#c8d8ec;font-size:.8rem;margin:0;line-height:1.55">{{ $ticket->summary }}</p>
            </div>
        </div>
        @endif
        <div class="tp" style="border-color:rgba(251,113,133,.18)">
            <div class="tp-inner">
                <p class="tp-title" style="color:var(--danger)">Срочная помощь</p>
                <p style="color:var(--muted);font-size:.75rem;margin:0 0 .6rem;line-height:1.5">Если ситуация критическая — свяжитесь с поддержкой напрямую.</p>
                <a href="{{ route($portal.'.support.index') }}" style="display:flex;align-items:center;gap:.5rem;padding:.55rem .75rem;border-radius:9px;border:1px solid rgba(251,113,133,.25);background:rgba(251,113,133,.05);color:var(--danger);font-size:.8rem;font-weight:850;text-decoration:none">
                    🚨 Все тикеты
                </a>
            </div>
        </div>
    </aside>
</div>
@endsection
