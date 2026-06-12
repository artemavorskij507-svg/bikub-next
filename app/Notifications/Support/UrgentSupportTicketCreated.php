<?php
namespace App\Notifications\Support;
class UrgentSupportTicketCreated extends SupportDatabaseNotification {protected function eventType():string{return 'urgent_ticket_created';} protected function title():string{return 'Urgent support ticket created';}}
