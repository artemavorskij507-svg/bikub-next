<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use App\Services\Support\SupportTicketService;
use Illuminate\Http\Request;

class WorkerSupportController extends Controller
{
    public function index(Request $request) { return view('support.index', ['tickets' => SupportTicket::where('worker_profile_id', $request->user()->workerProfile->id)->latest()->get(), 'portal' => 'worker']); }
    public function show(Request $request, SupportTicket $ticket)
    {
        abort_unless($ticket->worker_profile_id === $request->user()->workerProfile->id, 403);
        return view('support.show', ['ticket' => $ticket->load(['messages' => fn ($query) => $query->where('visibility', 'worker_visible')->orWhere(fn ($q) => $q->where('is_system', true)->where('visibility', 'worker_visible'))]), 'portal' => 'worker']);
    }
    public function reply(Request $request, SupportTicket $ticket, SupportTicketService $service)
    {
        abort_unless($ticket->worker_profile_id === $request->user()->workerProfile->id, 403);
        $data = $request->validate(['body' => 'required|string|max:10000']);
        $service->addMessage($ticket, [...$data, 'message_type' => 'public_reply', 'visibility' => 'worker_visible', 'author_type' => 'worker'], $request->user());
        return back();
    }
}
