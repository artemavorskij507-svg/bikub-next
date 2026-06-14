<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Order extends Model
{
    use LogsActivity;
    protected $fillable = ['order_number', 'customer_id', 'service_scenario_id', 'service_scenario_key', 'customer_name', 'customer_email', 'customer_phone', 'status', 'payment_status', 'source', 'locale', 'currency', 'estimated_total', 'final_total', 'scheduled_at', 'submitted_at', 'accepted_at', 'completed_at', 'cancelled_at', 'metadata', 'customer_notes', 'internal_notes'];

    protected function casts(): array
    {
        return ['status' => OrderStatus::class, 'payment_status' => PaymentStatus::class, 'estimated_total' => 'decimal:2', 'final_total' => 'decimal:2', 'scheduled_at' => 'datetime', 'submitted_at' => 'datetime', 'accepted_at' => 'datetime', 'completed_at' => 'datetime', 'cancelled_at' => 'datetime', 'metadata' => 'array'];
    }

    public function scenario(): BelongsTo { return $this->belongsTo(ServiceScenario::class, 'service_scenario_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function items(): HasMany { return $this->hasMany(OrderItem::class); }
    public function events(): HasMany { return $this->hasMany(OrderEvent::class)->orderByDesc('created_at'); }
    public function priceQuotes(): HasMany { return $this->hasMany(OrderPriceQuote::class)->orderByDesc('created_at'); }
    public function latestPriceQuote(): ?OrderPriceQuote { return $this->priceQuotes()->first(); }
    public function dispatchAssignments(): HasMany { return $this->hasMany(DispatchAssignment::class)->orderByDesc('created_at'); }
    public function activeDispatchAssignment(): ?DispatchAssignment { return $this->dispatchAssignments()->whereIn('status', ['assigned', 'accepted'])->first(); }
    public function dispatchEvents(): HasMany { return $this->hasMany(DispatchEvent::class)->orderByDesc('created_at'); }
    public function workerLocationPings(): HasMany { return $this->hasMany(WorkerLocationPing::class)->orderByDesc('created_at'); }
    public function supportTickets(): HasMany { return $this->hasMany(SupportTicket::class)->orderByDesc('created_at'); }
    public function billingDocuments(): HasMany { return $this->hasMany(BillingDocument::class)->orderByDesc('created_at'); }
    public function completionProofs(): HasMany { return $this->hasMany(OrderCompletionProof::class)->orderByDesc('created_at'); }
    public function paymentRecords(): HasMany { return $this->hasMany(PaymentRecord::class)->orderByDesc('created_at'); }
    public function workerSettlementEntries(): HasMany { return $this->hasMany(WorkerSettlementEntry::class)->orderByDesc('created_at'); }
    public function isDispatchReady(): bool { return $this->dispatchEvents()->where('event_type', 'dispatch.ready')->exists(); }
    public function scopeWithStatus(Builder $query, OrderStatus|string $status): Builder { return $query->where('status', $status instanceof OrderStatus ? $status->value : $status); }
    public function canTransitionTo(OrderStatus $status): bool { return $this->status->canTransitionTo($status); }
    public function getActivitylogOptions(): LogOptions { return LogOptions::defaults()->logOnly(['customer_id', 'status', 'payment_status', 'estimated_total', 'final_total', 'internal_notes'])->logOnlyDirty(); }
}
