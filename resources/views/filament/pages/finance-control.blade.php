<x-filament-panels::page>
<main class="finance-cockpit">
    <header class="fc-head">
        <div><span>REVENUE OPERATIONS</span><h1>Finance Control</h1><p>Payment readiness, real quotes and provider blockers.</p></div>
        <nav><a href="{{ route('filament.admin.pages.orders-hub') }}">Orders Hub</a><a href="{{ $pricingRulesUrl }}">Pricing Rules</a><a href="{{ route('filament.admin.pages.support-center') }}">Support Center</a><a href="{{ route('filament.admin.pages.operations-settings') }}">Operations Settings</a></nav>
    </header>

    <section class="fc-kpis">
        @foreach([
            ['Quoted orders',$metrics['quoted'],'ok'],['Missing quote',$metrics['missing_quote'],'warn'],
            ['Provider',$metrics['provider_enabled']?'Enabled':'Disabled','danger'],['Payment ready',$metrics['ready'],'ok'],
            ['Blocked actions',$metrics['blocked'],'danger'],['Payment issues',$metrics['payment_issues'],'warn'],
            ['Customer linked',$metrics['with_owner'],'ok'],['Completed today',$metrics['completed_today'],'plain']
        ] as [$label,$value,$tone])
            <article class="fc-card fc-card--{{ $tone }}"><span>{{ $label }}</span><strong>{{ $value }}</strong></article>
        @endforeach
    </section>
    <section class="fc-kpis">@foreach([['Billing documents',$contractMetrics['documents'],'plain'],['Draft invoices',$contractMetrics['draft_documents'],'warn'],['Payment records',$contractMetrics['payment_records'],'plain'],['Provider blocked',$contractMetrics['provider_blocked'],'danger'],['Webhook events',$contractMetrics['webhooks'],'plain']] as [$label,$value,$tone])<article class="fc-card fc-card--{{ $tone }}"><span>{{ $label }}</span><strong>{{ $value }}</strong></article>@endforeach</section>

    <section class="fc-grid">
        <aside class="fc-panel fc-queue">
            <div class="fc-panel-head"><div><span>FINANCE QUEUE</span><h2>Orders</h2></div><strong>{{ $queue->count() }}</strong></div>
            <div class="fc-tabs">
                @foreach(['readiness'=>'Readiness','missing_quote'=>'Missing quote','provider_blocked'=>'Provider blocked','payment_issues'=>'Payment issues','missing_owner'=>'Missing owner','manual_review'=>'Manual review','completed'=>'Completed'] as $key=>$label)
                    <button wire:click="setQueueFilter('{{ $key }}')" class="{{ $this->queueFilter===$key?'active':'' }}">{{ $label }}</button>
                @endforeach
            </div>
            <div class="fc-list">
                @forelse($queue as $order)
                    @php($q=$order->latestPriceQuote())
                    <button wire:click="selectOrder({{ $order->id }})" class="{{ $selectedOrder?->id===$order->id?'selected':'' }}">
                        <strong>{{ $order->order_number }}</strong><span>{{ $order->customer?->name ?? 'Customer ownership not linked' }}</span>
                        <small>{{ $q ? number_format((float)$q->total,2).' '.$q->currency : 'Quote missing' }} · {{ str($order->payment_status->value)->replace('_',' ')->title() }}</small>
                    </button>
                @empty <p class="fc-empty">No orders in this finance queue.</p> @endforelse
            </div>
        </aside>

        <section class="fc-panel fc-main">
            @if($selectedOrder)
                <div class="fc-panel-head"><div><span>SELECTED ORDER</span><h2>{{ $selectedOrder->order_number }}</h2></div><b>{{ str($selectedOrder->payment_status->value)->replace('_',' ')->title() }}</b></div>
                <div class="fc-actions"><a href="{{ $orderUrl }}">Open order</a><a href="{{ route('filament.admin.pages.orders-hub') }}">Open Orders Hub</a>
                    @if($supportUrl)<a href="{{ $supportUrl }}">Open payment ticket</a>@else<button wire:click="createPaymentSupportTicket({{ $selectedOrder->id }})">Create payment support ticket</button>@endif
                </div>
                <div class="fc-section"><span>QUOTE & INVOICE ACTIONS</span><div class="fc-actions">
                    @if($quote)<button wire:click="recalculateQuote({{ $selectedOrder->id }})">Recalculate quote</button>@else<button wire:click="calculateQuote({{ $selectedOrder->id }})">Calculate quote</button>@endif
                    @if($quote && $quote->status === 'estimated' && (float)$quote->total > 0)<button wire:click="createDraftInvoice({{ $selectedOrder->id }})">Create draft invoice</button>@else<button disabled title="Create a real quote before invoice issuance.">Create draft invoice</button>@endif
                    @if($latestInvoice?->status === 'draft')<button wire:click="issueLatestInvoice({{ $selectedOrder->id }})">Issue invoice</button>@else<button disabled title="A draft invoice is required.">Issue invoice</button>@endif
                </div>
                @if($quote)<label class="fc-reason">Recalculation reason<input wire:model="quoteReason" placeholder="Required before recalculation"></label>@endif
                @if(!$quotePreview['ready'])<div class="fc-blocker"><strong>Quote blocked</strong><p>{{ $quotePreview['blockers'][0]['reason'] }}</p></div>@endif
                @if($latestInvoice)<p>Latest invoice: <strong>{{ $latestInvoice->document_number }}</strong> · {{ str($latestInvoice->status)->title() }} · {{ number_format((float)$latestInvoice->total_amount,2) }} {{ $latestInvoice->currency }}</p>@endif
                </div>
                <div class="fc-disabled"><button disabled>Create payment intent</button><button disabled>Capture payment</button><button disabled>Refund payment</button><p>{{ $readiness['disabled_reason'] ?? 'Payment provider not connected yet.' }}</p></div>
                <div class="fc-section"><span>QUOTE BREAKDOWN</span>
                    @if($quote)<dl><div><dt>Quote</dt><dd>{{ $quote->quote_number }}</dd></div><div><dt>Subtotal</dt><dd>{{ number_format((float)$quote->subtotal,2) }} {{ $quote->currency }}</dd></div><div><dt>Fees</dt><dd>{{ number_format((float)$quote->fees_total,2) }} {{ $quote->currency }}</dd></div><div><dt>Total</dt><dd>{{ number_format((float)$quote->total,2) }} {{ $quote->currency }}</dd></div><div><dt>Status</dt><dd>{{ str($quote->status)->replace('_',' ')->title() }}</dd></div></dl>
                    @else <p class="fc-empty">No real quote exists. Quote recalculation service not available yet.</p> @endif
                </div>
                <div class="fc-section"><span>PAYMENT BLOCKERS</span>
                    @forelse($readiness['blockers'] as $blocker)<article class="fc-blocker"><strong>{{ $blocker['label'] }}</strong><p>{{ $blocker['reason'] }}</p></article>@empty<p class="fc-empty">No payment readiness blockers.</p>@endforelse
                </div>
                @if($paymentTicket)<div class="fc-section"><span>INTERNAL PAYMENT NOTE</span><textarea wire:model="supportNote" placeholder="Add an internal note to the payment support ticket"></textarea><button wire:click="addPaymentSupportNote({{ $selectedOrder->id }})">Add internal note</button></div>@endif
            @else <p class="fc-empty">No order selected.</p> @endif
        </section>

        <aside class="fc-rail">
            <section class="fc-panel"><span>PROVIDER READINESS</span><h3>{{ $provider['state'] }}</h3><p>{{ $provider['reason'] }}</p><a href="{{ route('filament.admin.pages.operations-settings') }}">Open payment settings</a></section>
            @if($selectedOrder)
            <section class="fc-panel"><span>CUSTOMER OWNERSHIP</span><h3>{{ $selectedOrder->customer?->name ?? 'Not linked' }}</h3><p>{{ $selectedOrder->customer?->email ?? 'Customer payment visibility remains blocked.' }}</p></section>
            <section class="fc-panel"><span>SUPPORT / PAYMENT</span><h3>{{ $paymentTicket?->ticket_number ?? 'No open payment ticket' }}</h3><p>{{ $paymentTicket?->status ?? 'Create a real internal payment issue ticket when review is required.' }}</p></section>
            <section class="fc-panel"><span>ORDER STATUS</span><h3>{{ str($selectedOrder->status->value)->replace('_',' ')->title() }}</h3><p>Updated {{ $selectedOrder->updated_at?->diffForHumans() }}</p></section>
            @endif
        </aside>
    </section>
    @if($selectedOrder)<section class="fc-panel" aria-labelledby="payout-readiness-heading"><div class="fc-panel-head"><div><span>COMPLETION & PAYOUT READINESS</span><h2 id="payout-readiness-heading">{{ count($completion['payout_blockers']) ? 'Payout blocked' : 'Ready for further review' }}</h2></div><b>{{ str($completion['status'])->replace('_',' ')->title() }}</b></div><div class="fc-section"><dl><div><dt>Invoice</dt><dd>{{ $latestInvoice?->status ? str($latestInvoice->status)->title() : 'Missing' }}</dd></div><div><dt>Payment captured</dt><dd>{{ $selectedOrder->paymentRecords->where('status','captured')->isNotEmpty() ? 'Yes' : 'No' }}</dd></div><div><dt>Completion proof</dt><dd>{{ str($completion['status'])->replace('_',' ')->title() }}</dd></div><div><dt>Worker assignment</dt><dd>{{ $selectedOrder->dispatchAssignments->whereIn('status',['assigned','accepted'])->isNotEmpty() ? 'Present' : 'Missing' }}</dd></div></dl>@foreach($completion['payout_blockers'] as $reason)<article class="fc-blocker"><strong>Payout blocked</strong><p>{{ $reason }}</p></article>@endforeach</div></section>@endif
@if($selectedOrder)<section class="fc-panel"><div class="fc-panel-head"><div><span>WORKER SETTLEMENT LEDGER</span><h2>{{ $settlement['entry']?->entry_number ?? 'Not calculated' }}</h2></div><b>{{ $settlement['entry']?->status ?? 'blocked' }}</b></div><p>Rule: {{ $settlement['rule']?->name ?? 'No active approved rule' }}</p><div class="fc-actions"><button wire:click="calculateSettlement({{$selectedOrder->id}})">Calculate settlement</button><a href="{{$settlementRulesUrl}}">Settlement rules</a><button disabled title="Settlement is not ready.">Approve unavailable</button><button disabled title="Payout provider/manual payout workflow is not configured.">Mark paid unavailable</button></div>@if($settlement['entry'])<p>Gross: {{number_format((float)$settlement['entry']->gross_amount,2)}} {{$settlement['entry']->currency}}</p><p>Worker amount: {{$settlement['entry']->worker_amount===null?'Blocked until an approved settlement rule is configured.':number_format((float)$settlement['entry']->worker_amount,2).' '.$settlement['entry']->currency}}</p><p>Platform fee: {{$settlement['entry']->platform_fee_amount===null?'Not calculated':number_format((float)$settlement['entry']->platform_fee_amount,2).' '.$settlement['entry']->currency}}</p>@endif @foreach($settlement['blockers'] as $reason)<article class="fc-blocker"><strong>Payout blocked</strong><p>{{$reason}}</p></article>@endforeach</section>@endif
</main>
<style>
.finance-cockpit{--line:#20364a;--panel:#071727;color:#e9f3ff;display:grid;gap:16px}.fc-head,.fc-panel,.fc-card{background:linear-gradient(145deg,#081827,#061321);border:1px solid var(--line);border-radius:8px}.fc-head{padding:20px;display:flex;justify-content:space-between;gap:20px}.fc-head span,.fc-panel span{color:var(--bkb-accent,#54e7b2);font-size:11px;font-weight:800}.fc-head h1{font-size:30px;margin:4px 0}.fc-head p,.fc-panel p{color:#91a8bf}.fc-head nav,.fc-actions{display:flex;flex-wrap:wrap;gap:8px;align-items:center}.fc-head a,.fc-actions a,.fc-actions button,.fc-section button,.fc-panel a{padding:9px 12px;border:1px solid #28506a;border-radius:6px;color:#dff7ff;background:#0a2232;font-weight:700}.fc-kpis{display:grid;grid-template-columns:repeat(8,minmax(110px,1fr));gap:10px}.fc-card{padding:14px}.fc-card span{display:block;color:#839bb3;font-size:11px}.fc-card strong{display:block;font-size:22px;margin-top:7px}.fc-card--danger strong{color:#ff7380}.fc-card--warn strong{color:#f7bd52}.fc-card--ok strong{color:#54e7b2}.fc-grid{display:grid;grid-template-columns:minmax(260px,1fr) minmax(420px,2fr) minmax(230px,.8fr);gap:14px}.fc-panel{padding:14px}.fc-panel-head{display:flex;justify-content:space-between;border-bottom:1px solid var(--line);padding-bottom:12px}.fc-panel h2,.fc-panel h3{margin:4px 0}.fc-tabs{display:flex;gap:5px;overflow:auto;padding:10px 0}.fc-tabs button{white-space:nowrap;background:#081b2b;border:1px solid var(--line);padding:7px;color:#9bb1c6;border-radius:5px}.fc-tabs .active{border-color:var(--bkb-accent,#54e7b2);color:var(--bkb-accent,#54e7b2)}.fc-list{display:grid;gap:6px;max-height:620px;overflow:auto}.fc-list button{display:grid;text-align:left;gap:4px;background:#061421;border:1px solid var(--line);padding:12px;color:#dfefff;border-radius:6px}.fc-list .selected{border-color:var(--bkb-accent,#54e7b2);background:#0a252b}.fc-list span,.fc-list small{color:#8ca6bd}.fc-main{display:grid;gap:14px;align-content:start}.fc-disabled{padding:12px;border:1px solid #5b3e2b;background:#251a12;border-radius:6px}.fc-disabled button{opacity:.45;margin-right:6px}.fc-disabled p{margin:8px 0 0;color:#f7bd52}.fc-section{border-top:1px solid var(--line);padding-top:14px}.fc-section dl{display:grid;grid-template-columns:repeat(2,1fr);gap:8px}.fc-section dl div{padding:10px;background:#061421;border:1px solid var(--line)}dt{color:#839bb3;font-size:11px}dd{margin:4px 0 0}.fc-blocker{border-left:3px solid #ff7380;padding:8px 12px;background:#1d141c;margin-top:8px}.fc-blocker p{margin:3px 0}.fc-section textarea{width:100%;min-height:90px;background:#061421;border:1px solid var(--line);color:#fff;padding:10px;margin:8px 0}.fc-rail{display:grid;gap:12px;align-content:start}.fc-empty{padding:16px;color:#91a8bf}@media(max-width:1200px){.fc-kpis{grid-template-columns:repeat(4,1fr)}.fc-grid{grid-template-columns:1fr 2fr}.fc-rail{grid-column:1/-1;grid-template-columns:repeat(3,1fr)}}@media(max-width:760px){.fc-head{display:block}.fc-head nav{margin-top:14px}.fc-kpis{grid-template-columns:repeat(2,1fr)}.fc-grid{grid-template-columns:1fr}.fc-rail{grid-template-columns:1fr}.fc-section dl{grid-template-columns:1fr}}
</style>
</x-filament-panels::page>
