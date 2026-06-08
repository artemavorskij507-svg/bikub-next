<?php

namespace App\Http\Controllers;

use App\Models\ServiceScenario;
use App\Services\Orders\OrderEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicOrderRequestController extends Controller
{
    public function create(string $serviceSlug): View
    {
        $scenario = ServiceScenario::active()->where('slug', $serviceSlug)->firstOrFail();
        return view('public.orders.request', compact('scenario'));
    }

    public function store(Request $request, string $serviceSlug, OrderEngine $engine): RedirectResponse
    {
        $scenario = ServiceScenario::active()->where('slug', $serviceSlug)->firstOrFail();
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255', 'required_without:customer_phone'],
            'customer_phone' => ['nullable', 'string', 'max:50', 'required_without:customer_email'],
            'customer_notes' => ['nullable', 'string', 'max:5000'],
            'scheduled_at' => ['nullable', 'date', 'after:now'],
        ]);
        $order = $engine->submit($engine->createDraftFromScenario($scenario, [...$data, 'source' => 'public', 'locale' => app()->getLocale()]));

        return redirect()->route('public.orders.confirmation', $order->order_number);
    }

    public function confirmation(string $orderNumber): View
    {
        $order = \App\Models\Order::where('order_number', $orderNumber)->firstOrFail();
        return view('public.orders.confirmation', compact('order'));
    }
}
