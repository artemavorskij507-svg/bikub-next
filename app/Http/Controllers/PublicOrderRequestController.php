<?php

namespace App\Http\Controllers;

use App\Models\ServiceScenario;
use App\Models\ServiceScenarioField;
use App\Services\Orders\OrderEngine;
use App\Services\Pricing\PricingEngine;
use App\Services\PublicSite\PageDataBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicOrderRequestController extends Controller
{
    public function deliveryCategory(PageDataBuilder $builder): View
    {
        $scenario = ServiceScenario::active()
            ->with(['fields' => fn ($query) => $query->active()])
            ->where('slug', 'delivery')
            ->first();

        // Try DB-backed published page first; fall back to static blade config if not found.
        $builderPageData = $builder->forRoute('/category/delivery');

        return view('public.categories.delivery', compact('scenario', 'builderPageData'));
    }

    public function create(string $serviceSlug): View
    {
        $scenario = ServiceScenario::active()->with(['fields' => fn ($query) => $query->active()])->where('slug', $serviceSlug)->firstOrFail();
        return view('public.orders.request', compact('scenario'));
    }

    public function store(Request $request, string $serviceSlug, OrderEngine $engine, PricingEngine $pricing): RedirectResponse
    {
        $scenario = ServiceScenario::active()->with(['fields' => fn ($query) => $query->active()])->where('slug', $serviceSlug)->firstOrFail();
        abort_if($scenario->fields->isEmpty(), 422, 'Scenario intake fields are not configured yet.');

        $rules = [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255', 'required_without:customer_phone'],
            'customer_phone' => ['nullable', 'string', 'max:50', 'required_without:customer_email'],
            'customer_notes' => ['nullable', 'string', 'max:5000'],
        ];

        foreach ($scenario->fields as $field) {
            $rules["intake.{$field->field_key}"] = $this->rulesFor($field);
        }

        $data = $request->validate($rules);
        $intake = collect($data['intake'] ?? [])->filter(fn ($value) => $value !== null && $value !== '')->all();
        $scheduledAt = collect($scenario->fields)->first(fn ($field) => in_array($field->field_key, ['delivery_window', 'preferred_time', 'preferred_date'], true));

        $order = $engine->submit($engine->createDraftFromScenario($scenario, [
            ...collect($data)->except('intake')->all(),
            'scheduled_at' => $scheduledAt ? ($intake[$scheduledAt->field_key] ?? null) : null,
            'metadata' => ['intake' => $intake],
            'source' => 'public',
            'locale' => app()->getLocale(),
        ]));
        $pricing->quoteForOrder($order);

        return redirect()->route('public.orders.confirmation', $order->order_number);
    }

    public function confirmation(string $orderNumber): View
    {
        $order = \App\Models\Order::with(['scenario.fields', 'priceQuotes'])->where('order_number', $orderNumber)->firstOrFail();
        return view('public.orders.confirmation', compact('order'));
    }

    private function rulesFor(ServiceScenarioField $field): array
    {
        $rules = [$field->required ? 'required' : 'nullable'];

        return match ($field->type) {
            'email' => [...$rules, 'email', 'max:255'],
            'number' => [...$rules, 'numeric'],
            'boolean' => [...$rules, 'boolean'],
            'date', 'datetime' => [...$rules, 'date'],
            'select' => [...$rules, 'string', 'max:255', 'in:'.implode(',', array_keys($field->options ?? []))],
            'textarea' => [...$rules, 'string', 'max:5000'],
            'phone' => [...$rules, 'string', 'max:50'],
            default => [...$rules, 'string', 'max:500'],
        };
    }
}
