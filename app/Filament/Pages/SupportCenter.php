<?php

namespace App\Filament\Pages;

use App\Models\SupportTicket;
use Filament\Pages\Page;
use App\Services\Support\SupportTicketService;
use Filament\Notifications\Notification;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Contracts\Support\Htmlable;

class SupportCenter extends Page
{
    public string $search = '';

    public string $queueFilter = 'incoming';

    public string $categoryFilter = 'all';

    public ?int $selectedTicketId = null;

    public string $composerMode = 'internal';

    public string $messageBody = '';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Support Center';

    protected static string|\UnitEnum|null $navigationGroup = 'Support';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Support Center';

    protected static ?string $slug = 'support-center';

    protected string $view = 'filament.pages.support-center';

    public static function canAccess(): bool
    {
        return auth()->user()?->can('admin.support.view') ?? false;
    }

    public function mount(): void
    {
        $this->selectedTicketId = SupportTicket::query()
            ->whereNotIn('status', ['resolved', 'closed'])
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 WHEN status = 'escalated' THEN 1 ELSE 2 END")
            ->latest('updated_at')
            ->value('id');
    }

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getViewData(): array
    {
        $open = SupportTicket::query()->whereNotIn('status', ['resolved', 'closed']);
        $activeQueue = SupportTicket::query()
            ->with(['order', 'customer', 'workerProfile', 'assignee'])
            ->when($this->queueFilter === 'mine', fn ($query) => $query->where('assigned_to_id', auth()->id()))
            ->when($this->queueFilter === 'urgent', fn ($query) => $query->where('priority', 'urgent'))
            ->when($this->queueFilter === 'unassigned', fn ($query) => $query->whereNull('assigned_to_id'))
            ->when($this->queueFilter === 'waiting_customer', fn ($query) => $query->where('status', 'pending_customer'))
            ->when($this->queueFilter === 'waiting_worker', fn ($query) => $query->where('status', 'pending_worker'))
            ->when($this->queueFilter === 'resolved', fn ($query) => $query->where('status', 'resolved'), fn ($query) => $query->whereNotIn('status', ['resolved', 'closed']))
            ->when($this->categoryFilter !== 'all', fn ($query) => $query->where('category', $this->categoryFilter))
            ->when(filled($this->search), function ($query) {
                $term = '%'.trim($this->search).'%';
                $query->where(fn ($search) => $search
                    ->where('ticket_number', 'ilike', $term)
                    ->orWhere('subject', 'ilike', $term)
                    ->orWhereHas('order', fn ($order) => $order->where('order_number', 'ilike', $term))
                    ->orWhereHas('customer', fn ($customer) => $customer->where('email', 'ilike', $term)));
            })
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 WHEN status = 'escalated' THEN 1 WHEN assigned_to_id IS NULL THEN 2 ELSE 3 END")
            ->latest('updated_at')
            ->limit(30)
            ->get();
        $selectedTicket = SupportTicket::query()
            ->with(['order', 'customer', 'workerProfile', 'workerDocument', 'dispatchAssignment', 'assignee', 'messages.author', 'events.actor'])
            ->find($this->selectedTicketId ?? $activeQueue->first()?->id);
        $firstResponses = SupportMessage::query()
            ->whereIn('author_type', ['admin', 'support'])
            ->selectRaw('support_ticket_id, MIN(created_at) as first_response_at')
            ->groupBy('support_ticket_id')
            ->get()
            ->map(function ($response) {
                $ticket = SupportTicket::find($response->support_ticket_id);
                return $ticket ? $ticket->created_at->diffInMinutes($response->first_response_at) : null;
            })->filter();

        return [
            'metrics' => [
                'open' => (clone $open)->count(),
                'urgent' => (clone $open)->where('priority', 'urgent')->count(),
                'escalated' => (clone $open)->where('status', 'escalated')->count(),
                'unassigned' => (clone $open)->whereNull('assigned_to_id')->count(),
                'mine' => (clone $open)->where('assigned_to_id', auth()->id())->count(),
                'waiting_customer' => (clone $open)->where('status', 'pending_customer')->count(),
                'waiting_worker' => (clone $open)->where('status', 'pending_worker')->count(),
                'resolved_today' => SupportTicket::query()->whereDate('resolved_at', today())->count(),
                'avg_first_response' => $firstResponses->isNotEmpty() ? round($firstResponses->average()).' min' : 'Not enough data',
            ],
            'latestTicketAt' => SupportTicket::query()->latest('updated_at')->value('updated_at'),
            'selectedTicket' => $selectedTicket,
            'activeQueue' => $activeQueue,
            'supportAgents' => User::role(['owner', 'admin', 'support'])->withCount(['assignedSupportTickets' => fn ($query) => $query->whereNotIn('status', ['resolved', 'closed'])])->get(),
            'attentionTickets' => SupportTicket::query()->where(fn ($query) => $query->where('priority', 'urgent')->orWhere('status', 'escalated'))->whereNotIn('status', ['resolved', 'closed'])->latest('updated_at')->limit(5)->get(),
        ];
    }

    public function selectTicket(int $ticketId): void
    {
        abort_unless(auth()->user()?->can('admin.support.view'), 403);
        $this->selectedTicketId = SupportTicket::findOrFail($ticketId)->id;
        $this->messageBody = '';
        $this->composerMode = 'internal';
    }

    public function setQueueFilter(string $filter): void
    {
        abort_unless(in_array($filter, ['incoming', 'mine', 'urgent', 'unassigned', 'waiting_customer', 'waiting_worker', 'resolved'], true), 422);
        $this->queueFilter = $filter;
        $this->selectedTicketId = null;
        $this->selectedTicketId = $this->getViewData()['activeQueue']->first()?->id;
    }

    public function setCategoryFilter(string $category): void
    {
        abort_unless(in_array($category, ['all', 'order_issue', 'payment_issue', 'worker_issue', 'document_issue', 'system_issue'], true), 422);
        $this->categoryFilter = $category;
        $this->selectedTicketId = null;
        $this->selectedTicketId = $this->getViewData()['activeQueue']->first()?->id;
    }

    public function setComposerMode(string $mode): void
    {
        abort_unless(in_array($mode, ['internal', 'customer', 'worker'], true), 422);
        $this->composerMode = $mode;
    }

    public function sendMessage(): void
    {
        $this->validate(['messageBody' => ['required', 'string', 'max:10000']]);
        $ticket = SupportTicket::findOrFail($this->selectedTicketId);
        abort_if($this->composerMode === 'customer' && ! $ticket->customer_id, 422, 'No linked customer account.');
        abort_if($this->composerMode === 'worker' && ! $ticket->worker_profile_id, 422, 'No linked worker profile.');
        $visibility = match ($this->composerMode) {
            'customer' => 'customer_visible',
            'worker' => 'worker_visible',
            default => 'internal',
        };
        app(SupportTicketService::class)->addMessage($ticket, [
            'body' => $this->messageBody,
            'message_type' => $this->composerMode === 'internal' ? 'internal_note' : 'public_reply',
            'visibility' => $visibility,
        ], auth()->user());
        $this->messageBody = '';
        Notification::make()->title($this->composerMode === 'internal' ? 'Internal note added' : 'Reply recorded')->success()->send();
    }

    public function assignToMe(int $ticketId): void
    {
        app(SupportTicketService::class)->assignTicket(SupportTicket::findOrFail($ticketId), auth()->user(), auth()->user());
        Notification::make()->title('Ticket assigned to you')->success()->send();
    }
}
