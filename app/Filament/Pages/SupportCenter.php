<?php

namespace App\Filament\Pages;

use App\Models\SupportTicket;
use Filament\Pages\Page;
use App\Services\Support\SupportTicketService;
use Filament\Notifications\Notification;

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

    public function getViewData(): array
    {
        $open = SupportTicket::query()->whereNotIn('status', ['resolved', 'closed']);

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
        ];
    }

    public function assignToMe(int $ticketId): void
    {
        app(SupportTicketService::class)->assignTicket(SupportTicket::findOrFail($ticketId), auth()->user(), auth()->user());
        Notification::make()->title('Ticket assigned to you')->success()->send();
    }
}
