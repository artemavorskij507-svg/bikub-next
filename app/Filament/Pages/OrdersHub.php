<?php

namespace App\Filament\Pages;

use App\Enums\OrderStatus;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\Order;
use App\Models\WorkerLocationPing;
use App\Services\Dispatch\DispatchEngine;
use App\Services\Orders\OrderHealthService;
use App\Services\Support\SupportTicketService;
use App\Settings\OperationsSettings;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class OrdersHub extends AdminOsModulePage
{
    public string $queueFilter = 'active';
    public ?int $selectedOrderId = null;
    public string $supportNote = '';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Orders Hub';
    protected static string|\UnitEnum|null $navigationGroup = 'Orders';
    protected static ?int $navigationSort = 10;
    protected static ?string $slug = 'orders-hub';
    protected string $view = 'filament.pages.orders-hub';
    public function getModuleKey(): string { return 'orders'; }
    public function getHeading(): string|Htmlable { return ''; }
    public function mount(): void { $this->selectedOrderId = Order::whereIn('status', ['submitted','accepted','in_progress'])->latest('updated_at')->value('id') ?? Order::latest()->value('id'); }
    public function selectOrder(int $id): void { $this->selectedOrderId = Order::findOrFail($id)->id; }
    public function setQueueFilter(string $filter): void { abort_unless(in_array($filter, ['active','waiting','unassigned','assigned','in_progress','support','payment','missing_owner','completed'], true), 422); $this->queueFilter=$filter; $this->selectedOrderId=$this->queue()->value('id'); }
    public function markReady(int $id): void { try { app(DispatchEngine::class)->markReadyForDispatch(Order::findOrFail($id), 'Marked ready from Orders Hub.'); Notification::make()->title('Order marked dispatch-ready')->success()->send(); } catch (ValidationException $e) { Notification::make()->title(collect($e->errors())->flatten()->first())->warning()->send(); } }
    public function createSupportTicket(int $id): void { $order=Order::findOrFail($id); $a=$order->activeDispatchAssignment(); $ticket=app(SupportTicketService::class)->createTicket(['subject'=>'Order issue: '.$order->order_number,'category'=>'order_issue','priority'=>'normal','source'=>'admin','visibility'=>'internal','order_id'=>$order->id,'customer_id'=>$order->customer_id,'dispatch_assignment_id'=>$a?->id,'worker_profile_id'=>$a?->assignedUser?->workerProfile?->id],auth()->user()); Notification::make()->title('Support ticket '.$ticket->ticket_number.' created')->success()->send(); }
    public function addSupportNote(int $id): void { $this->validate(['supportNote'=>['required','string','max:5000']]); $ticket=Order::findOrFail($id)->supportTickets()->whereNotIn('status',['resolved','closed'])->latest()->firstOrFail(); app(SupportTicketService::class)->addMessage($ticket,['body'=>$this->supportNote,'author_type'=>'admin','message_type'=>'internal_note','visibility'=>'internal'],auth()->user()); $this->supportNote=''; Notification::make()->title('Internal support note added')->success()->send(); }
    public function getViewData(): array
    {
        $selected=$this->selected(); $active=['submitted','accepted','in_progress']; $operations=rescue(fn()=>app(OperationsSettings::class),null,report:false);
        return ['metrics'=>['active'=>Order::whereIn('status',$active)->count(),'waiting'=>Order::whereIn('status',['submitted','accepted'])->count(),'unassigned'=>Order::whereIn('status',['submitted','accepted'])->whereDoesntHave('dispatchAssignments',fn($q)=>$q->whereIn('status',['assigned','accepted']))->count(),'in_progress'=>Order::where('status','in_progress')->count(),'completed_today'=>Order::whereDate('completed_at',today())->count(),'support'=>Order::whereHas('supportTickets',fn($q)=>$q->whereNotIn('status',['resolved','closed']))->count(),'missing_owner'=>Order::whereNull('customer_id')->count(),'payment_not_ready'=>Order::whereIn('payment_status',['pending','failed'])->count(),'with_gps'=>WorkerLocationPing::whereNotNull('order_id')->distinct('order_id')->count('order_id'),'without_gps'=>Order::whereIn('status',$active)->whereDoesntHave('workerLocationPings')->count()], 'queue'=>$this->queue()->get(),'selectedOrder'=>$selected,'assignment'=>$selected?->activeDispatchAssignment(),'quote'=>$selected?->latestPriceQuote(),'latestTicket'=>$selected?->supportTickets->first(),'openTickets'=>$selected?->supportTickets->whereNotIn('status',['resolved','closed'])??collect(),'blockers'=>$selected?app(OrderHealthService::class)->evaluate($selected):[],'timeline'=>$this->timeline($selected),'paymentProviderEnabled'=>(bool)($operations?->payment_provider_enabled??false),'customerTrackingEnabled'=>(bool)($operations?->customer_tracking_enabled??false)];
    }
    private function base(): Builder { return Order::with(['scenario','customer','priceQuotes','dispatchAssignments.assignedUser.workerProfile','dispatchAssignments.assignedUser.workerAvailability','dispatchEvents','supportTickets.assignee','workerLocationPings','events']); }
    private function queue(): Builder { return $this->base()->when($this->queueFilter==='active',fn($q)=>$q->whereIn('status',['submitted','accepted','in_progress']))->when($this->queueFilter==='waiting',fn($q)=>$q->whereIn('status',['submitted','accepted']))->when($this->queueFilter==='unassigned',fn($q)=>$q->whereIn('status',['submitted','accepted'])->whereDoesntHave('dispatchAssignments',fn($a)=>$a->whereIn('status',['assigned','accepted'])))->when($this->queueFilter==='assigned',fn($q)=>$q->whereHas('dispatchAssignments',fn($a)=>$a->whereIn('status',['assigned','accepted'])))->when($this->queueFilter==='in_progress',fn($q)=>$q->where('status','in_progress'))->when($this->queueFilter==='support',fn($q)=>$q->whereHas('supportTickets',fn($t)=>$t->whereNotIn('status',['resolved','closed'])))->when($this->queueFilter==='payment',fn($q)=>$q->whereIn('payment_status',['pending','failed']))->when($this->queueFilter==='missing_owner',fn($q)=>$q->whereNull('customer_id'))->when($this->queueFilter==='completed',fn($q)=>$q->whereDate('completed_at',today()))->latest('updated_at')->limit(50); }
    private function selected(): ?Order { return $this->selectedOrderId ? $this->base()->find($this->selectedOrderId) : null; }
    private function timeline(?Order $order): array { if(!$order)return []; return collect()->merge($order->events->map(fn($e)=>['at'=>$e->created_at,'type'=>'Order','text'=>$e->event_type]))->merge($order->dispatchEvents->map(fn($e)=>['at'=>$e->created_at,'type'=>'Dispatch','text'=>$e->note?:$e->event_type]))->merge($order->supportTickets->map(fn($e)=>['at'=>$e->created_at,'type'=>'Support','text'=>$e->ticket_number.' ? '.$e->status]))->sortByDesc('at')->take(16)->values()->all(); }
}
