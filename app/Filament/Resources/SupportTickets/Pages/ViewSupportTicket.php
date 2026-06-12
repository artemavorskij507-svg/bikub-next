<?php
namespace App\Filament\Resources\SupportTickets\Pages;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\User;
use App\Services\Support\SupportTicketService;
use Filament\Actions\Action;
use Filament\Forms\Components\{FileUpload,Select,Textarea};
use Filament\Resources\Pages\ViewRecord;
class ViewSupportTicket extends ViewRecord {
 protected static string $resource=SupportTicketResource::class;
 protected function getHeaderActions():array{return [
  Action::make('internal_note')->schema([Textarea::make('body')->required()])->action(fn(array $data)=>app(SupportTicketService::class)->addMessage($this->record,['body'=>$data['body'],'message_type'=>'internal_note','visibility'=>'internal'],auth()->user())),
  Action::make('customer_message')->schema([Textarea::make('body')->required()->helperText('Visible in the linked customer account support portal.')])->action(fn(array $data)=>app(SupportTicketService::class)->addMessage($this->record,['body'=>$data['body'],'message_type'=>'public_reply','visibility'=>'customer_visible'],auth()->user()))->visible(fn()=>filled($this->record->customer_id)),
  Action::make('worker_message')->schema([Textarea::make('body')->required()->helperText('Visible in the linked worker support portal.')])->action(fn(array $data)=>app(SupportTicketService::class)->addMessage($this->record,['body'=>$data['body'],'message_type'=>'public_reply','visibility'=>'worker_visible'],auth()->user()))->visible(fn()=>filled($this->record->worker_profile_id)),
  Action::make('assign_me')->action(fn()=>app(SupportTicketService::class)->assignTicket($this->record,auth()->user(),auth()->user())),
  Action::make('assign')->schema([Select::make('user_id')->options(User::role(['owner','admin','support'])->pluck('email','id'))->required()])->action(fn(array $data)=>app(SupportTicketService::class)->assignTicket($this->record,User::findOrFail($data['user_id']),auth()->user())),
  Action::make('change_status')->schema([Select::make('status')->options(SupportTicketResource::statuses())->required(),Textarea::make('note')])->action(fn(array $data)=>app(SupportTicketService::class)->changeStatus($this->record,$data['status'],auth()->user(),$data['note']??null)),
  Action::make('change_priority')->schema([Select::make('priority')->options(SupportTicketResource::priorities())->required(),Textarea::make('note')])->action(fn(array $data)=>app(SupportTicketService::class)->changePriority($this->record,$data['priority'],auth()->user(),$data['note']??null)),
  Action::make('resolve')->schema([Textarea::make('note')->required()])->action(fn(array $data)=>app(SupportTicketService::class)->resolveTicket($this->record,auth()->user(),$data['note'])),
  Action::make('reopen')->schema([Textarea::make('reason')->required()])->action(fn(array $data)=>app(SupportTicketService::class)->reopenTicket($this->record,auth()->user(),$data['reason']))->visible(fn()=>in_array($this->record->status,['resolved','closed'],true)),
  Action::make('attach')->label('Attach file')->schema([FileUpload::make('file')->disk('local')->directory('support-uat')->acceptedFileTypes(['application/pdf','image/png','image/jpeg','image/webp','text/plain'])->maxSize(10240)->required()])->action(fn(array $data)=>app(SupportTicketService::class)->attachFile($this->record,storage_path('app/private/'.$data['file']),auth()->user())),
 ]; }
}
