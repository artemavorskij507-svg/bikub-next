<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Services\Account\CustomerOwnershipService;
use App\Services\Support\SupportTicketService;
use Illuminate\Http\Request;

class AccountSupportController extends Controller
{
    public function index(Request $request) { return view('support.index', ['tickets' => SupportTicket::where('customer_id', $request->user()->id)->latest()->get(), 'portal' => 'account']); }
    public function create() { return view('support.create'); }
    public function store(Request $request, SupportTicketService $service)
    {
        $data = $request->validate(['subject' => 'required|string|max:255', 'summary' => 'required|string|max:5000']);
        $ticket = $service->createTicket([...$data, 'customer_id' => $request->user()->id, 'category' => 'customer_question', 'priority' => 'normal', 'source' => 'account', 'visibility' => 'customer_visible'], $request->user());
        $service->addMessage($ticket, ['body' => $data['summary'], 'message_type' => 'public_reply', 'visibility' => 'customer_visible', 'author_type' => 'customer'], $request->user());
        return redirect()->route('account.support.show', $ticket);
    }
    public function show(Request $request, SupportTicket $ticket, CustomerOwnershipService $ownership)
    {
        abort_unless($ownership->canViewSupportTicket($request->user(), $ticket), 403);
        return view('support.show', ['ticket' => $ticket->load(['messages' => fn ($query) => $query->whereIn('visibility', ['customer_visible'])->orWhere(fn ($q) => $q->where('is_system', true)->where('visibility', 'customer_visible'))]), 'portal' => 'account']);
    }
    public function reply(Request $request, SupportTicket $ticket, SupportTicketService $service, CustomerOwnershipService $ownership)
    {
        abort_unless($ownership->canViewSupportTicket($request->user(), $ticket), 403);
        $data = $request->validate(['body' => 'required|string|max:10000']);
        $service->addMessage($ticket, [...$data, 'message_type' => 'public_reply', 'visibility' => 'customer_visible', 'author_type' => 'customer'], $request->user());
        return back();
    }
}
