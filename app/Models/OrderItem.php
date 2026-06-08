<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'title', 'description', 'quantity', 'unit_price', 'total_price', 'metadata'];
    protected function casts(): array { return ['quantity' => 'decimal:2', 'unit_price' => 'decimal:2', 'total_price' => 'decimal:2', 'metadata' => 'array']; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
