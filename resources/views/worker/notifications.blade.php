@extends('worker.layout')
@section('title', 'Notifications')
@section('content')
<style>
.cockpit-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(280px,330px);gap:1rem;align-items:start}
.main-stack,.side-stack{display:grid;gap:.75rem}
.panel{border:1px solid var(--line);border-radius:14px;background:var(--panel)}
.panel-inner{padding:1rem}
.panel-head{display:flex;justify-content:space-between;align-items:center;gap:.75rem;margin-bottom:.85rem}
.panel-title{font-size:.96rem;font-weight:950;margin:0}
.badge{display:inline-flex;align-items:center;border:1px solid var(--line);border-radius:999px;padding:.15rem .52rem;font-size:.68rem;font-weight:900;letter-spacing:.04em;color:var(--muted);background:rgba(148,163,184,.08)}
.badge-green{border-color:rgba(52,230,154,.32);background:rgba(52,230,154,.1);color:var(--green)}
.badge-blue{border-color:rgba(85,217,255,.28);background:rgba(85,217,255,.08);color:var(--blue)}
.badge-amber{border-color:rgba(245,189,84,.28);background:rgba(245,189,84,.08);color:var(--amber)}
.status-pill{display:inline-flex;align-items:center;gap:.5rem;padding:.35rem .75rem;border-radius:999px;border:1px solid rgba(52,230,154,.28);background:rgba(52,230,154,.08);color:var(--green);font-size:.75rem;font-weight:900;white-space:nowrap}
.status-dot{width:.45rem;height:.45rem;border-radius:999px;background:var(--green);box-shadow:0 0 8px rgba(52,230,154,.6);flex-shrink:0}
.side-card{border:1px solid var(--line);border-radius:11px;background:var(--panel2);padding:.75rem}
.list-row{display:flex;justify-content:space-between;align-items:center;gap:.75rem;padding:.55rem 0;border-bottom:1px solid var(--line)}
.list-row:last-child{border-bottom:none}
.card-list{display:grid;gap:.55rem}
.item-card{display:grid;grid-template-columns:1fr auto;gap:.75rem;align-items:start;border:1px solid var(--line);border-radius:12px;background:var(--panel2);padding:.85rem;text-decoration:none;color:inherit}
.item-card.unread{border-color:rgba(52,230,154,.28);background:rgba(52,230,154,.03)}
.item-meta{display:flex;flex-wrap:wrap;align-items:center;gap:.4rem;margin-bottom:.35rem}
.a-btn{display:inline-flex;align-items:center;justify-content:center;min-height:2.3rem;border:1px solid var(--line);border-radius:9px;background:rgba(14,30,49,.86);color:var(--text);padding:.45rem .8rem;font-weight:850;text-decoration:none;cursor:pointer;font-size:.82rem;white-space:nowrap;transition:background .15s}
.a-btn-primary{border-color:rgba(52,230,154,.48);background:linear-gradient(135deg,#25c889,#0c7c5b);color:#fff}
.a-btn-ghost{border-color:transparent;background:transparent;color:var(--muted)}
.a-btn:disabled{opacity:.45;cursor:not-allowed}
.toolbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:.75rem;margin-bottom:.75rem}
.tabs{display:flex;gap:.35rem}
.tab{display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .75rem;border-radius:8px;border:1px solid transparent;font-size:.82rem;font-weight:850;text-decoration:none;color:var(--muted)}
.tab.is-active{border-color:rgba(52,230,154,.28);background:rgba(52,230,154,.08);color:var(--green)}
.search-box{border:1px solid var(--line);border-radius:9px;background:rgba(4,12,23,.75);color:var(--text);padding:.45rem .75rem;font-size:.82rem;min-width:200px;max-width:260px}
.empty-state{display:grid;place-items:center;min-height:6rem;color:var(--muted);font-size:.85rem;text-align:center;padding:1.5rem}
.compact-table{width:100%;border-collapse:collapse;font-size:.82rem}
.compact-table th{color:var(--muted);font-size:.68rem;text-transform:uppercase;letter-spacing:.04em;padding:.6rem .75rem;border-bottom:1px solid var(--line);text-align:left;white-space:nowrap}
.compact-table td{padding:.65rem .75rem;border-bottom:1px solid var(--line);color:#cbd5e1}
.compact-table tr:last-child td{border-bottom:none}
@media(max-width:860px){.cockpit-grid{grid-template-columns:1fr}.search-box{min-width:140px}}
</style>

<div x-data="{
    search: '',
    filter() {
        const q = this.search.toLowerCase().trim();
        document.querySelectorAll('[data-notif]').forEach(el => {
            el.style.display = !q || el.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
}">
    {{-- Page head --}}
    <div class="worker-page-head">
        <div>
            <p class="worker-hero-eyebrow">Notification Center</p>
            <h1 style="margin:0;font-size:clamp(1.6rem,3vw,2.4rem);line-height:1">Notifications</h1>
            <p class="muted" style="margin:.3rem 0 0">Order events, status changes and system alerts.</p>
        </div>
        <div class="status-pill">
            <span class="status-dot"></span>
            <span>{{ $unread->count() }} unread</span>
        </div>
    </div>

    <div class="cockpit-grid">
        <div class="main-stack">
            {{-- Mark all read --}}
            @if($unread->isNotEmpty())
            <div class="panel" x-data="{ loading: false, async markAll() {
                if (this.loading) return;
                this.loading = true;
                try {
                    const r = await fetch('{{ route('worker.notifications.read-all') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                    });
                    const d = await r.json();
                    if (d.success || r.ok) window.location.reload();
                } catch (e) { console.error(e); } finally { this.loading = false; }
            }}">
                <div class="panel-inner" style="display:flex;justify-content:space-between;align-items:center;gap:1rem">
                    <p class="muted" style="margin:0;font-size:.83rem">You have <strong style="color:var(--text)">{{ $unread->count() }}</strong> unread notifications. This action marks them as read in the database.</p>
                    <button class="a-btn a-btn-primary" type="button" @click="markAll()" :disabled="loading">
                        <span x-show="!loading">Mark all read</span>
                        <span x-show="loading">Processing…</span>
                    </button>
                </div>
            </div>
            @endif

            {{-- Toolbar --}}
            <div class="toolbar">
                <div class="tabs">
                    <a href="#unread" class="tab is-active">
                        🔔 Unread
                        <span class="badge badge-amber">{{ $unread->count() }}</span>
                    </a>
                    <a href="#history" class="tab">
                        ✅ History
                        <span class="badge">{{ $read->count() }}</span>
                    </a>
                </div>
                <input class="search-box" type="search" x-model="search" @input="filter()" placeholder="Search notifications…" aria-label="Search notifications">
            </div>

            {{-- Unread --}}
            <section id="unread" class="panel">
                <div class="panel-inner">
                    <div class="panel-head">
                        <h2 class="panel-title">Unread</h2>
                        <span class="muted" style="font-size:.8rem">{{ $unread->count() }} records</span>
                    </div>

                    @if($unread->isEmpty())
                        <div class="empty-state">
                            <div>
                                <div style="font-size:2rem;margin-bottom:.5rem">🔔</div>
                                No unread notifications.
                            </div>
                        </div>
                    @else
                        <div class="card-list">
                            @foreach($unread as $n)
                            @php
                                $title = data_get($n->data,'title') ?? data_get($n->data,'subject') ?? class_basename($n->type);
                                $body  = data_get($n->data,'body') ?? data_get($n->data,'message') ?? data_get($n->data,'text');
                                $icon  = data_get($n->data,'icon') ?? '🔔';
                            @endphp
                            <article class="item-card unread" data-notif x-data="{ hidden:false, loading:false }" x-show="!hidden">
                                <div>
                                    <div class="item-meta">
                                        <span class="badge badge-blue">New</span>
                                        <span class="badge">{{ $n->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p style="margin:0 0 .25rem;font-weight:950;font-size:.95rem;color:var(--text)">{{ $icon }} {{ $title }}</p>
                                    @if($body)<p class="muted" style="margin:0;font-size:.82rem;line-height:1.45">{{ $body }}</p>@endif
                                </div>
                                <button type="button" class="a-btn a-btn-ghost" :disabled="loading" @click="
                                    if (loading) return; loading = true;
                                    fetch('{{ route('worker.notifications.read', $n->id) }}', {
                                        method:'POST',
                                        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}
                                    }).then(r=>r.ok?r.json():null).then(d=>{ if(d&&d.success) hidden=true; }).finally(()=>loading=false);
                                ">
                                    ✓ Read
                                </button>
                            </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            </section>

            {{-- History --}}
            <section id="history" class="panel">
                <div class="panel-inner">
                    <div class="panel-head">
                        <h2 class="panel-title">History</h2>
                        <span class="muted" style="font-size:.8rem">{{ $read->count() }} records</span>
                    </div>
                    @if($read->isEmpty())
                        <div class="empty-state">Notification history is empty.</div>
                    @else
                        <div style="overflow-x:auto">
                            <table class="compact-table">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Status</th>
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
                                        <td><span class="badge badge-green">Read</span></td>
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
                    <div class="panel-head"><h2 class="panel-title">Summary</h2></div>
                    <div class="side-card">
                        <div class="list-row"><span class="muted">Unread</span><strong>{{ $unread->count() }}</strong></div>
                        <div class="list-row"><span class="muted">Read</span><strong>{{ $read->count() }}</strong></div>
                        <div class="list-row"><span class="muted">Source</span><span class="badge">Laravel notifications</span></div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-inner">
                    <div class="panel-head"><h2 class="panel-title">Quick actions</h2></div>
                    <div class="card-list">
                        <a href="{{ route('worker.orders.index') }}" class="a-btn" style="justify-content:flex-start;gap:.5rem">📦 Open assignments</a>
                        <a href="{{ route('worker.support.index') }}" class="a-btn" style="justify-content:flex-start;gap:.5rem">🛟 Support desk</a>
                        <a href="{{ route('worker.dashboard') }}" class="a-btn" style="justify-content:flex-start;gap:.5rem">🏠 Dashboard</a>
                    </div>
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
