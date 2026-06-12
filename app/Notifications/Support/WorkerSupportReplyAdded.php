<?php
namespace App\Notifications\Support;
class WorkerSupportReplyAdded extends SupportDatabaseNotification {protected function eventType():string{return 'worker_reply_added';} protected function title():string{return 'Worker replied to support ticket';}}
