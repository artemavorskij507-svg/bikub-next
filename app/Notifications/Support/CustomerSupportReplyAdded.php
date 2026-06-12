<?php
namespace App\Notifications\Support;
class CustomerSupportReplyAdded extends SupportDatabaseNotification {protected function eventType():string{return 'customer_reply_added';} protected function title():string{return 'Customer replied to support ticket';}}
