<?php
namespace App\Notifications\Support;
class SupportTicketAssigned extends SupportDatabaseNotification {protected function eventType():string{return 'ticket_assigned';} protected function title():string{return 'Support ticket assigned to you';}}
