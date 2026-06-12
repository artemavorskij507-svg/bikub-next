<?php
namespace App\Notifications\Support;
class SupportTicketResolved extends SupportDatabaseNotification {protected function eventType():string{return 'ticket_resolved';} protected function title():string{return 'Support ticket resolved';}}
