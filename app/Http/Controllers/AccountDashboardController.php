<?php

namespace App\Http\Controllers;

use App\Models\{BillingDocument, Order, SupportTicket};
use Illuminate\Http\Request;

class AccountDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();
        $orders = Order::where('customer_id', $user->id)->with(['scenario', 'billingDocuments', 'supportTickets', 'workerLocationPings'])->latest()->get();
        $documents = BillingDocument::whereIn('status', ['issued', 'paid', 'refunded'])
            ->where(fn ($query) => $query->where('customer_id', $user->id)->orWhereHas('order', fn ($order) => $order->where('customer_id', $user->id)))
            ->latest('issued_at')->get();
        $tickets = SupportTicket::where('customer_id', $user->id)->latest('last_message_at')->get();

        return view('account.dashboard', [
            'orders' => $orders,
            'documents' => $documents,
            'tickets' => $tickets,
            'activeOrder' => $orders->first(fn ($order) => ! in_array($order->status->value, ['completed', 'cancelled'], true)),
        ]);
    }
}
