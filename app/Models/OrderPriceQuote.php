<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPriceQuote extends Model
{
    protected $fillable = ['order_id', 'quote_number', 'status', 'currency', 'subtotal', 'fees_total', 'discounts_total', 'tax_total', 'total', 'breakdown', 'calculation_inputs', 'expires_at', 'accepted_at'];

    protected function casts(): array
    {
        return ['subtotal' => 'decimal:2', 'fees_total' => 'decimal:2', 'discounts_total' => 'decimal:2', 'tax_total' => 'decimal:2', 'total' => 'decimal:2', 'breakdown' => 'array', 'calculation_inputs' => 'array', 'expires_at' => 'datetime', 'accepted_at' => 'datetime'];
    }

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function billingDocuments() { return $this->hasMany(BillingDocument::class); }
}
