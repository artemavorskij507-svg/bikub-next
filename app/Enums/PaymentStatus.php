<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case NotRequired = 'not_required';
    case Pending = 'pending';
    case Reserved = 'reserved';
    case Paid = 'paid';
    case Failed = 'failed';
    case Refunded = 'refunded';
}
