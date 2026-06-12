<?php
namespace App\Notifications\Support;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
abstract class SupportDatabaseNotification extends Notification {
 use Queueable;
 public function __construct(public SupportTicket $ticket){}
 public function via(object $notifiable):array{return ['database'];}
 abstract protected function eventType():string;
 abstract protected function title():string;
 public function toArray(object $notifiable):array{return ['ticket_id'=>$this->ticket->id,'ticket_number'=>$this->ticket->ticket_number,'subject'=>$this->ticket->subject,'status'=>$this->ticket->status,'priority'=>$this->ticket->priority,'category'=>$this->ticket->category,'action_url'=>'/admin/support-tickets/'.$this->ticket->id,'event_type'=>$this->eventType(),'title'=>$this->title(),'created_at'=>now()->toIso8601String()];}
}
