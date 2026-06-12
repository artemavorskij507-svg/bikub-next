<?php
namespace App\Filament\Resources\SupportTickets\Pages;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\{SupportTicket,User};
use App\Services\Support\SupportTicketService;
use Filament\Actions\Action;
use Filament\Forms\Components\{Select,Textarea};
use Filament\Resources\Pages\ViewRecord;
class ViewSupportTicket extends ViewRecord {
 protected static string $resource=SupportTicketResource::class;
 protected function getHeaderActions():array{return [
  Action::make('internal_note')->schema([Textarea::make('body')->required()])->action(fn(array $d)=>app(SupportTicketService::class)->addMessage($this->record,['body'=>$d['body'],'message_type'=>'internal_note','visibility'=>'internal'],auth()->user())),
  Action::make('customer_message')->schema([Textarea::make('body')->required()->helperText('Visible later when the customer support portal is enabled.')])->action(fn(array $d)=>app(SupportTicketService::class)->addMessage($this->record,['body'=>$d['body'],'message_type'=>'public_reply','visibility'=>'customer_visible'],auth()->user())),
  Action::make('worker_message')->schema([Textarea::make('body')->required()->helperText('Visible later when the worker support portal is enabled.')])->action(fn(array $d)=>app(SupportTicketService::class)->addMessage($this->record,['body'=>$d['body'],'message_type'=>'public_reply','visibility'=>'worker_visible'],auth()->user())),
  Action::make('assign_me')->action(fn()=>app(SupportTicketService::class)->assignTicket($this->record,auth()->user(),auth()->user())),
  Action::make('assign')->schema([Select::make('user_id')->options(User::role(['owner','admin','support'])->pluck('email','id'))->required()])->action(fn(array $d)=>app(SupportTicketService::class)->assignTicket($this->record,User::findOrFail($d['user_id']),auth()->user())),
  Action::make('resolve')->schema([Textarea::make('note')->required()])->action(fn(array $d)=>app(SupportTicketService::class)->resolveTicket($this->record,auth()->user(),$d['note'])),
  Action::make('reopen')->schema([Textarea::make('reason')->required()])->action(fn(array $d)=>app(SupportTicketService::class)->reopenTicket($this->record,auth()->user(),$d['reason']))->visible(fn()=>in_array($this->record->status,['resolved','closed'],true)),
 ]; }
}
