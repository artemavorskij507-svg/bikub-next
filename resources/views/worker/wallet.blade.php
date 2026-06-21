@extends('worker.layout')
@section('title', 'Finances')
@section('content')
<style>
/* ── Ledger rows ── */
.ledger-row{display:grid;grid-template-columns:auto 1fr auto;gap:.85rem;align-items:center;border:1px solid var(--line);border-radius:12px;background:var(--panel2);padding:.9rem 1rem;transition:border-color .2s}
.ledger-row.is-ready{border-color:rgba(var(--brand-rgb),.28)}
.ledger-row.is-paid{border-color:rgba(85,217,255,.18)}
.ledger-row.is-blocked{border-color:rgba(245,189,84,.22)}
.ledger-icon{display:grid;width:2.5rem;height:2.5rem;place-items:center;border-radius:11px;font-size:1.1rem;flex-shrink:0}
.ledger-icon.ready{background:rgba(var(--brand-rgb),.14)}
.ledger-icon.paid{background:rgba(85,217,255,.12)}
.ledger-icon.blocked{background:rgba(245,189,84,.12)}
.ledger-num{font-size:.72rem;color:var(--muted);margin:0 0 .15rem}
.ledger-status{display:inline-flex;align-items:center;border:1px solid transparent;border-radius:999px;padding:.15rem .5rem;font-size:.68rem;font-weight:900;text-transform:uppercase;letter-spacing:.06em}
.s-ready{border-color:rgba(var(--brand-rgb),.32);background:rgba(var(--brand-rgb),.1);color:var(--green)}
.s-paid{border-color:rgba(85,217,255,.28);background:rgba(85,217,255,.08);color:var(--blue)}
.s-blocked,.s-pending_capture{border-color:rgba(245,189,84,.28);background:rgba(245,189,84,.08);color:var(--amber)}
.s-pending,.s-created,.s-recalculated{border-color:rgba(148,163,184,.22);background:rgba(148,163,184,.06);color:var(--muted)}
.ledger-amount{font-size:1rem;font-weight:950;text-align:right}
.ledger-list{display:grid;gap:.55rem}
.blocker-list{display:grid;gap:.4rem;margin:.5rem 0 0}
.blocker-item{display:grid;grid-template-columns:1.2rem 1fr;gap:.4rem;font-size:.82rem;color:#ffd9df;align-items:start}

/* ── Virtual card ── */
.vcard-wrap{display:flex;justify-content:flex-start;margin-bottom:1.25rem}
.vcard{
    position:relative;width:100%;max-width:400px;height:220px;
    border-radius:16px;overflow:hidden;
    background:linear-gradient(135deg,#0d2137 0%,#071120 55%,#0a2a1c 100%);
    border:1px solid rgba(var(--brand-rgb),.22);
    box-shadow:0 8px 48px rgba(0,0,0,.6),0 0 0 1px rgba(var(--brand-rgb),.08) inset;
    padding:1.4rem 1.5rem;
    display:flex;flex-direction:column;justify-content:space-between;
    box-sizing:border-box;
}
.vcard::before{
    content:'';position:absolute;top:-60px;right:-60px;
    width:200px;height:200px;border-radius:999px;
    background:radial-gradient(circle,rgba(var(--brand-rgb),.18) 0%,transparent 70%);
    pointer-events:none;
}
.vcard::after{
    content:'';position:absolute;bottom:-40px;left:30px;
    width:160px;height:160px;border-radius:999px;
    background:radial-gradient(circle,rgba(85,217,255,.09) 0%,transparent 70%);
    pointer-events:none;
}
.vcard-top{display:flex;justify-content:space-between;align-items:flex-start;position:relative;z-index:1}
.vcard-logo{font-size:.88rem;font-weight:950;letter-spacing:.12em;color:var(--green);text-transform:uppercase}
.vcard-type{font-size:.66rem;color:rgba(143,165,189,.7);text-transform:uppercase;letter-spacing:.1em;margin-top:.25rem}
.vcard-chip{width:34px;height:26px;border-radius:5px;background:linear-gradient(135deg,#b8a060,#d4b96a);box-shadow:0 1px 4px rgba(0,0,0,.4);position:relative}
.vcard-chip::before{content:'';position:absolute;top:50%;left:0;right:0;height:1px;background:rgba(0,0,0,.3);transform:translateY(-50%)}
.vcard-chip::after{content:'';position:absolute;top:0;bottom:0;left:50%;width:1px;background:rgba(0,0,0,.25)}
.vcard-mid{position:relative;z-index:1}
.vcard-balance-label{font-size:.65rem;color:rgba(143,165,189,.65);text-transform:uppercase;letter-spacing:.1em;margin-bottom:.2rem}
.vcard-balance{font-size:2.1rem;font-weight:950;color:#ffffff;letter-spacing:.02em;line-height:1;text-shadow:0 0 32px rgba(var(--brand-rgb),.35)}
.vcard-balance-currency{font-size:1.1rem;font-weight:600;color:var(--green);margin-left:.2rem}
.vcard-bottom{display:flex;justify-content:space-between;align-items:flex-end;position:relative;z-index:1}
.vcard-holder{font-size:.75rem;font-weight:950;color:var(--text);text-transform:uppercase;letter-spacing:.08em}
.vcard-pan{font-size:.7rem;color:rgba(143,165,189,.55);letter-spacing:.12em;margin-top:.18rem;font-family:monospace}
.vcard-nfc{width:1.3rem;height:1.3rem;opacity:.3}

/* ── Bar chart ── */
.chart-section{margin-bottom:1.25rem}
.chart-section h3{font-size:.82rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;font-weight:950;margin:0 0 .75rem}
.chart-container{
    background:var(--panel);border:1px solid var(--line);border-radius:14px;
    padding:1rem 1.25rem .75rem;
}
.chart-bars{display:flex;align-items:flex-end;gap:6px;height:80px;padding-bottom:0}
.chart-bar-wrap{display:flex;flex-direction:column;align-items:center;gap:.3rem;flex:1;min-width:0}
.chart-bar{
    width:100%;border-radius:4px 4px 0 0;min-height:3px;
    background:linear-gradient(180deg,var(--green) 0%,rgba(var(--brand-rgb),.4) 100%);
    transition:opacity .15s;
}
.chart-bar.is-paid{background:linear-gradient(180deg,var(--blue) 0%,rgba(85,217,255,.4) 100%)}
.chart-bar.is-blocked{background:linear-gradient(180deg,var(--amber) 0%,rgba(245,189,84,.4) 100%)}
.chart-bar-label{font-size:.58rem;color:var(--muted);text-align:center;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:100%;padding:0 1px}
.chart-empty{color:var(--muted);font-size:.82rem;padding:.5rem 0;text-align:center}
.chart-legend{display:flex;gap:1rem;margin-top:.65rem;flex-wrap:wrap}
.chart-legend-item{display:flex;align-items:center;gap:.35rem;font-size:.68rem;color:var(--muted)}
.chart-legend-dot{width:.45rem;height:.45rem;border-radius:999px;flex-shrink:0}

/* ── KPI cards (improved) ── */
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;margin-bottom:1rem}
@media(max-width:900px){.kpi-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:520px){.kpi-grid{grid-template-columns:1fr}}
.kpi-card{
    border:1px solid var(--line);border-radius:14px;
    background:var(--panel);
    padding:1rem 1.1rem 1.1rem;
    display:flex;flex-direction:column;gap:.2rem;
    position:relative;overflow:hidden;
}
.kpi-card::before{
    content:'';position:absolute;top:0;left:0;right:0;height:2px;
    background:var(--kpi-accent,var(--line));
    opacity:.7;
}
.kpi-label{font-size:.65rem;color:var(--muted);text-transform:uppercase;letter-spacing:.09em;font-weight:950}
.kpi-value{font-size:1.65rem;font-weight:950;line-height:1.05;margin:.2rem 0 .1rem;color:var(--kpi-color,var(--text))}
.kpi-sub{font-size:.7rem;color:var(--muted)}

/* ── История выплат ── */
.history-section{margin-top:1.25rem}
.history-section h3{font-size:.82rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;font-weight:950;margin:0 0 .65rem}
.history-table{width:100%;border-collapse:collapse;font-size:.8rem}
.history-table th{text-align:left;color:var(--muted);font-size:.66rem;text-transform:uppercase;letter-spacing:.08em;font-weight:950;padding:.4rem .5rem;border-bottom:1px solid var(--line)}
.history-table td{padding:.5rem .5rem;border-bottom:1px solid rgba(148,163,184,.08);color:var(--text);vertical-align:middle}
.history-table tr:last-child td{border-bottom:none}
.history-table tbody tr:hover td{background:rgba(255,255,255,.025)}
</style>

<div class="worker-page-head">
    <div>
        <p class="worker-hero-eyebrow">Settlement</p>
        <h1>Finances</h1>
        <p class="muted">Settlement ledger, earnings summary and payout readiness. No balance is held — each entry maps to a completed order.</p>
    </div>
    <div class="actions">
        <a class="btn" href="{{ route('worker.payout-profile.show') }}">Payout profile</a>
        <a class="btn" href="{{ route('worker.payout-reviews.index') }}">Tax &amp; identity</a>
    </div>
</div>

{{-- ── Virtual card ── --}}
<div class="vcard-wrap">
    <div class="vcard">
        <div class="vcard-top">
            <div>
                <div class="vcard-logo">BiKuBe</div>
                <div class="vcard-type">расчётный журнал</div>
            </div>
            <div class="vcard-chip"></div>
        </div>
        <div class="vcard-mid">
            <div class="vcard-balance-label">К выплате</div>
            <div class="vcard-balance">
                @php
                    $parts = explode('.', number_format($readyAmount, 2, '.', ' '));
                    $whole = $parts[0];
                @endphp
                {{ $whole }}<span class="vcard-balance-currency">NOK</span>
            </div>
        </div>
        <div class="vcard-bottom">
            <div>
                <div class="vcard-holder">BiKuBe Courier</div>
                <div class="vcard-pan">no stored balance · no fake card</div>
            </div>
            <svg class="vcard-nfc" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color:var(--muted)">
                <path d="M1 6C1 6 5.5 2 12 2C18.5 2 23 6 23 6"/>
                <path d="M4 10C4 10 7.2 7 12 7C16.8 7 20 10 20 10"/>
                <path d="M7.5 14C7.5 14 9.3 12 12 12C14.7 12 16.5 14 16.5 14"/>
                <circle cx="12" cy="18" r="1.5" fill="currentColor" stroke="none"/>
            </svg>
        </div>
    </div>
</div>

{{-- ── Bar chart: График выплат ── --}}
<div class="chart-section">
    <h3>График выплат</h3>
    <div class="chart-container">
        @php
            $chartEntries = $entries->whereNotNull('worker_amount')->where('worker_amount', '>', 0)->take(20);
            $maxAmt = $chartEntries->max('worker_amount') ?: 1;
        @endphp
        @if($chartEntries->isEmpty())
            <div class="chart-empty">Нет данных за период</div>
        @else
            <div class="chart-bars">
                @foreach($chartEntries as $ce)
                @php
                    $pct = max(4, round(($ce->worker_amount / $maxAmt) * 100));
                    $cst = $ce->status;
                    $barClass = in_array($cst, ['paid']) ? 'is-paid' : (in_array($cst, ['blocked','pending_capture']) ? 'is-blocked' : '');
                @endphp
                <div class="chart-bar-wrap" title="#{{ $ce->order_id }} — {{ number_format((float)$ce->worker_amount, 0) }} {{ $ce->currency ?? 'NOK' }}">
                    <div class="chart-bar {{ $barClass }}" style="height:{{ $pct }}%"></div>
                    <div class="chart-bar-label">#{{ $ce->order_id }}</div>
                </div>
                @endforeach
            </div>
            <div class="chart-legend">
                <div class="chart-legend-item"><div class="chart-legend-dot" style="background:var(--green)"></div> Ready</div>
                <div class="chart-legend-item"><div class="chart-legend-dot" style="background:var(--blue)"></div> Paid</div>
                <div class="chart-legend-item"><div class="chart-legend-dot" style="background:var(--amber)"></div> Blocked</div>
            </div>
        @endif
    </div>
</div>

{{-- ── KPI row (improved) ── --}}
@php $profileStatus = $payoutProfile['profile']?->status ?? 'missing'; @endphp
<div class="kpi-grid" style="margin-bottom:1rem">
    <div class="kpi-card" style="--kpi-accent:var(--green);--kpi-color:var(--green)">
        <div class="kpi-label">К выплате</div>
        <div class="kpi-value">{{ number_format($readyAmount, 2) }}</div>
        <div class="kpi-sub">NOK · одобренные записи</div>
    </div>
    <div class="kpi-card" style="--kpi-accent:var(--blue);--kpi-color:var(--blue)">
        <div class="kpi-label">Выплачено</div>
        <div class="kpi-value">{{ number_format($paidAmount, 2) }}</div>
        <div class="kpi-sub">NOK · всего выплачено</div>
    </div>
    <div class="kpi-card" style="--kpi-accent:{{ $blockedCount > 0 ? 'var(--amber)' : 'var(--line)' }};--kpi-color:{{ $blockedCount > 0 ? 'var(--amber)' : 'var(--muted)' }}">
        <div class="kpi-label">Заблокировано</div>
        <div class="kpi-value">{{ $blockedCount }}</div>
        <div class="kpi-sub">Ожидает решения</div>
    </div>
    <div class="kpi-card" style="--kpi-accent:{{ $profileStatus === 'approved' ? 'var(--green)' : ($profileStatus === 'missing' ? 'var(--danger)' : 'var(--amber)') }};--kpi-color:{{ $profileStatus === 'approved' ? 'var(--green)' : ($profileStatus === 'missing' ? 'var(--danger)' : 'var(--amber)') }}">
        <div class="kpi-label">Профиль выплаты</div>
        <div class="kpi-value" style="font-size:1.15rem;margin-top:.35rem">{{ ucfirst(str_replace('_', ' ', $profileStatus)) }}</div>
        <div class="kpi-sub">Готовность к выплате</div>
    </div>
</div>

<div class="grid" style="grid-template-columns:minmax(0,1fr) minmax(0,320px);gap:1rem;align-items:start">
    {{-- Ledger --}}
    <article class="worker-card">
        <h3 style="margin:0 0 .9rem;font-size:.92rem;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;font-weight:950">Settlement ledger</h3>
        <div class="ledger-list">
            @forelse($entries as $entry)
            @php
                $st = $entry->status;
                $iconMap = ['ready'=>'✓','paid'=>'↑','blocked'=>'!','pending_capture'=>'⏳','created'=>'·','recalculated'=>'~'];
                $icon = $iconMap[$st] ?? '·';
                $amount = $entry->worker_amount;
                $currency = $entry->currency ?? 'NOK';
            @endphp
            <div class="ledger-row is-{{ $st }}">
                <div class="ledger-icon {{ in_array($st,['ready']) ? 'ready' : (in_array($st,['paid']) ? 'paid' : 'blocked') }}">{{ $icon }}</div>
                <div>
                    <p class="ledger-num">{{ $entry->entry_number }}</p>
                    <strong style="font-size:.88rem">Order #{{ $entry->order_id }}</strong>
                    <div style="margin-top:.3rem">
                        <span class="ledger-status s-{{ $st }}">{{ ucfirst(str_replace('_',' ',$st)) }}</span>
                    </div>
                    @if($entry->blocker_reason)
                    <p style="color:var(--muted);font-size:.74rem;margin:.25rem 0 0">{{ Str::limit($entry->blocker_reason, 120) }}</p>
                    @endif
                </div>
                <div class="ledger-amount" style="color:{{ $st==='ready' ? 'var(--green)' : ($st==='paid' ? 'var(--blue)' : 'var(--muted)') }}">
                    @if($amount !== null)
                        {{ number_format((float)$amount, 2) }}<span style="font-size:.72rem;color:var(--muted);font-weight:500"> {{ $currency }}</span>
                    @else
                        <span style="color:var(--muted);font-size:.78rem">Blocked</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="worker-empty">
                <div>
                    <i style="display:grid;width:4.2rem;height:4.2rem;place-items:center;border-radius:999px;background:rgba(148,163,184,.1);font-style:normal;font-size:1.8rem;margin:0 auto 1rem">₿</i>
                    <h3>No entries yet</h3>
                    <p class="muted">Complete your first assignment and it will appear here once settlement is calculated.</p>
                </div>
            </div>
            @endforelse
        </div>
        @if(count($entries) >= 20)
        <p class="muted" style="font-size:.78rem;margin:.85rem 0 0;text-align:center">Showing 20 most recent entries.</p>
        @endif

        {{-- ── История выплат ── --}}
        @php $paidEntries = $entries->where('status', 'paid'); @endphp
        @if($paidEntries->isNotEmpty())
        <div class="history-section">
            <h3>История выплат</h3>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Дата</th>
                        <th>Заказ</th>
                        <th style="text-align:right">Сумма</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paidEntries as $pe)
                    <tr>
                        <td style="color:var(--muted);white-space:nowrap">{{ $pe->created_at?->format('d.m.Y') ?? '—' }}</td>
                        <td>#{{ $pe->order_id }}</td>
                        <td style="text-align:right;font-weight:950;color:var(--blue)">
                            {{ number_format((float)($pe->worker_amount ?? 0), 2) }}
                            <span style="font-size:.68rem;color:var(--muted);font-weight:500">{{ $pe->currency ?? 'NOK' }}</span>
                        </td>
                        <td><span class="ledger-status s-paid">Paid</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </article>

    {{-- Right column --}}
    <div class="grid">
        {{-- Payout readiness --}}
        <article class="worker-card" @if(!$payoutProfile['ready']) style="border-color:rgba(251,113,133,.28)" @endif>
            <h3 style="margin:0 0 .6rem;font-size:.9rem">Payout readiness</h3>
            @if($payoutProfile['ready'])
                <p style="color:var(--green);font-size:.84rem;margin:0 0 .75rem">✓ Payout profile is approved and ready.</p>
            @else
                @if(($payoutProfile['blockers'] ?? []))
                <div class="blocker-list">
                    @foreach($payoutProfile['blockers'] as $b)
                    <div class="blocker-item"><span>⚠</span><span>{{ $b }}</span></div>
                    @endforeach
                </div>
                @endif
            @endif
            <div class="actions" style="margin-top:.85rem">
                <a class="btn {{ $payoutProfile['ready'] ? '' : 'primary' }}" href="{{ route('worker.payout-profile.show') }}" style="font-size:.82rem">
                    {{ $payoutProfile['ready'] ? 'View payout profile' : 'Fix payout profile' }}
                </a>
            </div>
        </article>

        {{-- How earnings work --}}
        <article class="worker-card" style="border-color:rgba(85,217,255,.18)">
            <h3 style="margin:0 0 .6rem;font-size:.9rem;color:var(--blue)">How settlement works</h3>
            <div class="kv"><span>Created</span><small>Calculated from completed order evidence</small></div>
            <div class="kv"><span>Blocked</span><small>Awaiting payment capture or compliance</small></div>
            <div class="kv"><span>Ready</span><small>Approved, queued for next payout cycle</small></div>
            <div class="kv"><span>Paid</span><small>Transferred to your payout profile</small></div>
            <p class="muted" style="font-size:.75rem;margin:.75rem 0 0">Settlement amounts are only visible once a worker settlement rule has been approved for your account and attached order type.</p>
        </article>
    </div>
</div>
@endsection
