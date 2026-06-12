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

    public function getHeading(): string|Htmlable
    {
        return '';
    }

    public function getViewData(): array
    {
        $open = SupportTicket::query()->whereNotIn('status', ['resolved', 'closed']);
        $selectedTicket = SupportTicket::query()
            ->with(['order', 'customer', 'workerProfile', 'workerDocument', 'dispatchAssignment', 'assignee', 'messages.author', 'events.actor'])
            ->whereNotIn('status', ['resolved', 'closed'])
            ->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 WHEN status = 'escalated' THEN 1 ELSE 2 END")
            ->latest('updated_at')
            ->first();
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
            'queues' => [
                'Urgent / escalated' => SupportTicket::query()->where(fn ($query) => $query->where('priority', 'urgent')->orWhere('status', 'escalated'))->latest('updated_at')->limit(6)->get(),
                'Unassigned' => (clone $open)->whereNull('assigned_to_id')->latest('updated_at')->limit(6)->get(),
                'Assigned to me' => (clone $open)->where('assigned_to_id', auth()->id())->latest('updated_at')->limit(6)->get(),
                'Waiting customer' => (clone $open)->where('status', 'pending_customer')->latest('updated_at')->limit(6)->get(),
                'Waiting worker' => (clone $open)->where('status', 'pending_worker')->latest('updated_at')->limit(6)->get(),
                'Recently updated' => SupportTicket::query()->latest('updated_at')->limit(8)->get(),
            ],
            'latestTicketAt' => SupportTicket::query()->latest('updated_at')->value('updated_at'),
            'selectedTicket' => $selectedTicket,
            'activeQueue' => SupportTicket::query()
                ->with(['order', 'assignee'])
                ->whereNotIn('status', ['resolved', 'closed'])
                ->orderByRaw("CASE WHEN priority = 'urgent' THEN 0 WHEN status = 'escalated' THEN 1 WHEN assigned_to_id IS NULL THEN 2 ELSE 3 END")
                ->latest('updated_at')->limit(15)->get(),
            'supportAgents' => User::role(['owner', 'admin', 'support'])->withCount(['assignedSupportTickets' => fn ($query) => $query->whereNotIn('status', ['resolved', 'closed'])])->get(),
        ];
    }

    public function assignToMe(int $ticketId): void
    {
        app(SupportTicketService::class)->assignTicket(SupportTicket::findOrFail($ticketId), auth()->user(), auth()->user());
        Notification::make()->title('Ticket assigned to you')->success()->send();
    }
}
