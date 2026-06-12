<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Http\JsonResponse;

class AdminSupportActivityController extends Controller
{
    public function __invoke(): JsonResponse
    {
        abort_unless(auth()->user()?->can('admin.support.view'), 403);

        return response()->json([
            'latest_ticket_id' => SupportTicket::max('id') ?? 0,
            'latest_message_id' => SupportMessage::max('id') ?? 0,
            'open_count' => SupportTicket::whereNotIn('status', ['resolved', 'closed'])->count(),
        ]);
    }
}
