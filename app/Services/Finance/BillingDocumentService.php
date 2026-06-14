<?php

namespace App\Services\Finance;

use App\Models\BillingDocument;
use App\Models\Order;
use App\Models\PaymentRecord;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class BillingDocumentService
{
    public function __construct(private PaymentNumberGenerator $numbers) {}

    public function createDraftInvoiceForOrder(Order $order, User $actor): BillingDocument
    {
        $quote = $order->latestPriceQuote();
        if (! $quote || $quote->status !== 'estimated' || (float) $quote->total <= 0) {
            throw ValidationException::withMessages(['quote' => 'Create a real quote before invoice issuance.']);
        }
        if (BillingDocument::where('order_price_quote_id', $quote->id)->where('document_type', 'invoice')->whereNotIn('status', ['void', 'cancelled'])->exists()) {
            throw ValidationException::withMessages(['invoice' => 'An active invoice already exists for the latest quote.']);
        }

        $document = BillingDocument::create([
            'document_number' => $this->numbers->document('invoice'), 'document_type' => 'invoice', 'status' => 'draft',
            'order_id' => $order->id, 'customer_id' => $order->customer_id, 'order_price_quote_id' => $quote->id,
            'currency' => $quote->currency, 'subtotal_amount' => $quote->subtotal, 'tax_amount' => $quote->tax_total,
            'total_amount' => $quote->total, 'created_by_id' => $actor->id, 'updated_by_id' => $actor->id,
        ]);
        $this->event($document, $actor, 'created', null, 'draft', 'Draft invoice created from real quote.');

        return $document;
    }

    public function issueInvoice(BillingDocument $document, User $actor): BillingDocument
    {
        if ($document->status !== 'draft') throw ValidationException::withMessages(['invoice' => 'Only a draft invoice can be issued.']);
        if (! $document->customer_id || (float) $document->total_amount <= 0) throw ValidationException::withMessages(['invoice' => 'Customer owner and positive total are required.']);
        $document->update(['status' => 'issued', 'issued_at' => now(), 'updated_by_id' => $actor->id]);
        $this->event($document, $actor, 'issued', 'draft', 'issued');
        return $document;
    }

    public function voidInvoice(BillingDocument $document, User $actor, string $reason): BillingDocument
    {
        if (blank($reason)) throw ValidationException::withMessages(['reason' => 'Reason required.']);
        $from = $document->status;
        $document->update(['status' => 'void', 'voided_at' => now(), 'updated_by_id' => $actor->id]);
        $this->event($document, $actor, 'voided', $from, 'void', $reason);
        return $document;
    }

    public function createReceiptForPayment(PaymentRecord $payment, User $actor): BillingDocument
    {
        if ($payment->status !== 'captured') throw ValidationException::withMessages(['payment' => 'Receipt requires a real captured payment.']);
        throw ValidationException::withMessages(['payment' => 'Receipt generation is unavailable until a real provider is connected.']);
    }

    public function addBillingNote(BillingDocument $document, User $actor, string $note): void
    {
        $this->event($document, $actor, 'note_added', null, null, $note);
    }

    private function event(BillingDocument $document, User $actor, string $type, ?string $from, ?string $to, ?string $description = null): void
    {
        $document->events()->create(['actor_id' => $actor->id, 'event_type' => $type, 'from_value' => $from, 'to_value' => $to, 'description' => $description, 'created_at' => now()]);
    }
}
