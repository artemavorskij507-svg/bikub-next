<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\PricingRules\PricingRuleResource;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\Order;
use App\Services\Finance\PaymentReadinessService;
use App\Services\Support\SupportTicketService;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class FinanceControl extends AdminOsModulePage
{
    public string $queueFilter = 'readiness';
    public ?int $selectedOrderId = null;
    public string $supportNote = '';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Finance Control';
    protected static string|\UnitEnum|null $navigationGroup = 'Finance';
    protected static ?int $navigationSort = 10;
    protected static ?string $title = 'Finance Control';
    protected string $view = 'filament.pages.finance-control';

    public function getModuleKey(): string { return 'finance'; }
    public function getHeading(): string|Htmlable { return ''; }

    public function mount(): void
    {
        $this->selectedOrderId = Order::whereIn('payment_status', ['pending', 'failed'])->latest('updated_at')->value('id')
            ?? Order::latest('updated_at')->value('id');
    }

    public function selectOrder(int $id): void { $this->selectedOrderId = Order::findOrFail($id)->id; }

    public function setQueueFilter(string $filter): void
    {
        abort_unless(in_array($filter, ['readiness', 'missing_quote', 'provider_blocked', 'payment_issues', 'missing_owner', 'manual_review', 'completed'], true), 422);
        $this->queueFilter = $filter;
        $this->selectedOrderId = $this->queue()->value('id');
    }

    public function createPaymentSupportTicket(int $id): void
    {
        abort_unless(auth()->user()?->can('admin.support.manage'), 403);
        $order = Order::findOrFail($id);
        $ticket = app(SupportTicketService::class)->createTicket([
            'subject' => 'Payment issue: '.$order->order_number,
            'category' => 'payment_issue',
            'priority' => 'normal',
            'source' => 'admin',
            'visibility' => 'internal',
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
        ], auth()->user());

        Notification::make()->title('Payment support ticket '.$ticket->ticket_number.' created')->success()->send();
    }

    public function addPaymentSupportNote(int $id): void
    {
        abort_unless(auth()->user()?->can('admin.support.internal_notes'), 403);
        $this->validate(['supportNote' => ['required', 'string', 'max:5000']]);
        $ticket = Order::findOrFail($id)->supportTickets()->where('category', 'payment_issue')->whereNotIn('status', ['resolved', 'closed'])->latest()->firstOrFail();
        app(SupportTicketService::class)->addMessage($ticket, ['body' => $this->supportNote, 'author_type' => 'admin', 'message_type' => 'internal_note', 'visibility' => 'internal'], auth()->user());
        $this->supportNote = '';
        Notification::make()->title('Internal payment support note added')->success()->send();
    }

    public function getViewData(): array
    {
        $selected = $this->selected();
        $service = app(PaymentReadinessService::class);
        $paymentTicket = $selected?->supportTickets->first(fn ($ticket) => $ticket->category === 'payment_issue' && ! in_array($ticket->status, ['resolved', 'closed'], true));

        return [
            'metrics' => $service->getFinanceMetrics(),
            'provider' => $service->getProviderStatus(),
            'queue' => $this->queue()->get(),
            'selectedOrder' => $selected,
            'quote' => $selected?->latestPriceQuote(),
            'readiness' => $selected ? $service->getOrderPaymentReadiness($selected) : null,
            'paymentTicket' => $paymentTicket,
            'orderUrl' => $selected ? OrderResource::getUrl('edit', ['record' => $selected]) : null,
            'pricingRulesUrl' => PricingRuleResource::getUrl(),
            'supportUrl' => $paymentTicket ? SupportTicketResource::getUrl('view', ['record' => $paymentTicket]) : null,
        ];
    }

    private function base(): Builder
    {
        return Order::with(['customer', 'scenario', 'priceQuotes', 'supportTickets.assignee', 'events']);
    }

    private function queue(): Builder
    {
        return $this->base()
            ->when($this->queueFilter === 'missing_quote', fn ($query) => $query->whereDoesntHave('priceQuotes'))
            ->when($this->queueFilter === 'provider_blocked', fn ($query) => $query->whereIn('payment_status', ['pending', 'failed']))
            ->when($this->queueFilter === 'payment_issues', fn ($query) => $query->whereHas('supportTickets', fn ($tickets) => $tickets->where('category', 'payment_issue')->whereNotIn('status', ['resolved', 'closed'])))
            ->when($this->queueFilter === 'missing_owner', fn ($query) => $query->whereNull('customer_id'))
            ->when($this->queueFilter === 'manual_review', fn ($query) => $query->whereHas('priceQuotes', fn ($quotes) => $quotes->where('status', 'manual_review_required')))
            ->when($this->queueFilter === 'completed', fn ($query) => $query->whereDate('completed_at', today()))
            ->latest('updated_at')->limit(50);
    }

    private function selected(): ?Order
    {
        return $this->selectedOrderId ? $this->base()->find($this->selectedOrderId) : null;
    }
}
