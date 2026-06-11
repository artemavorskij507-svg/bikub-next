<?php

namespace App\Services\Pricing;

use App\Models\OrderPriceQuote;
use Illuminate\Support\Str;

class PriceQuoteNumberGenerator
{
    public function generate(): string
    {
        do {
            $number = 'QTE-'.now()->format('ymd').'-'.Str::upper(Str::random(6));
        } while (OrderPriceQuote::where('quote_number', $number)->exists());

        return $number;
    }
}
