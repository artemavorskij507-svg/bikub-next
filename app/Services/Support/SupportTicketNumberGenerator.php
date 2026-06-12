<?php
namespace App\Services\Support;
use App\Models\SupportTicket;
class SupportTicketNumberGenerator {
 public function generate():string { do { $number='SUP-'.now()->format('Ymd').'-'.strtoupper(str()->random(4)); } while (SupportTicket::withTrashed()->where('ticket_number',$number)->exists()); return $number; }
}
