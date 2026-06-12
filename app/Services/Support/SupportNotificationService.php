<?php
namespace App\Services\Support;
use App\Models\{SupportTicket,User};
use App\Notifications\Support\{CustomerSupportReplyAdded,SupportTicketAssigned,SupportTicketResolved,UrgentSupportTicketCreated,WorkerSupportReplyAdded};
use Illuminate\Notifications\Notification;
class SupportNotificationService {
 public function urgentCreated(SupportTicket $ticket):void{$this->notify($this->supportAdmins(),new UrgentSupportTicketCreated($ticket));}
 public function assigned(SupportTicket $ticket,User $assignee):void{if($assignee->can('admin.support.view'))$assignee->notify(new SupportTicketAssigned($ticket));}
 public function customerReply(SupportTicket $ticket):void{$this->notify($this->operationalRecipients($ticket),new CustomerSupportReplyAdded($ticket));}
 public function workerReply(SupportTicket $ticket):void{$this->notify($this->operationalRecipients($ticket),new WorkerSupportReplyAdded($ticket));}
 public function resolved(SupportTicket $ticket):void{$this->notify($this->operationalRecipients($ticket,true),new SupportTicketResolved($ticket));}
 private function supportAdmins(){return User::permission('admin.support.view')->get();}
 private function operationalRecipients(SupportTicket $ticket,bool $includeCreator=false){$ids=$this->supportAdmins()->pluck('id');if($ticket->assigned_to_id)$ids->push($ticket->assigned_to_id);if($includeCreator&&$ticket->created_by_id)$ids->push($ticket->created_by_id);return User::whereIn('id',$ids->unique())->get()->filter(fn(User $user)=>$user->can('admin.support.view'));}
 private function notify($users,Notification $notification):void{$users->unique('id')->each->notify($notification);}
}
