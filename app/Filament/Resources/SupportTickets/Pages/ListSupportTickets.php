<?php
namespace App\Filament\Resources\SupportTickets\Pages;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
class ListSupportTickets extends ListRecords {
 protected static string $resource=SupportTicketResource::class;
 protected function getHeaderActions():array{return [CreateAction::make()];}
 public function getTabs():array{return [
  'open'=>Tab::make()->modifyQueryUsing(fn(Builder $q)=>$q->whereNotIn('status',['resolved','closed'])),
  'unassigned'=>Tab::make()->modifyQueryUsing(fn(Builder $q)=>$q->whereNull('assigned_to_id')),
  'mine'=>Tab::make('Assigned to me')->modifyQueryUsing(fn(Builder $q)=>$q->where('assigned_to_id',auth()->id())),
  'urgent'=>Tab::make()->modifyQueryUsing(fn(Builder $q)=>$q->where('priority','urgent')),
  'escalated'=>Tab::make()->modifyQueryUsing(fn(Builder $q)=>$q->where('status','escalated')),
  'waiting_customer'=>Tab::make('Waiting customer')->modifyQueryUsing(fn(Builder $q)=>$q->where('status','pending_customer')),
  'waiting_worker'=>Tab::make('Waiting worker')->modifyQueryUsing(fn(Builder $q)=>$q->where('status','pending_worker')),
  'resolved'=>Tab::make()->modifyQueryUsing(fn(Builder $q)=>$q->where('status','resolved')),
  'closed'=>Tab::make()->modifyQueryUsing(fn(Builder $q)=>$q->where('status','closed')),
 ];}
}
