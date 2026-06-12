<?php
namespace App\Services\Account;
use App\Models\{Order,SupportTicket,User};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class CustomerOwnershipService {
 public function canViewOrder(User $user,Order $order):bool{return $order->customer_id===$user->id;}
 public function canViewSupportTicket(User $user,SupportTicket $ticket):bool{return $ticket->customer_id===$user->id&&(!$ticket->order_id||$ticket->order?->customer_id===$user->id);}
 public function linkOrderToCustomer(Order $order,User $user,User $actor,string $reason):void{if(blank($reason))throw ValidationException::withMessages(['reason'=>'Ownership link reason is required.']);DB::transaction(function()use($order,$user,$actor,$reason){$order->update(['customer_id'=>$user->id]);$order->supportTickets()->whereNull('customer_id')->update(['customer_id'=>$user->id]);activity()->performedOn($order)->causedBy($actor)->withProperties(['customer_id'=>$user->id,'reason'=>$reason])->log('order.customer_linked');});}
 public function unlinkOrderFromCustomer(Order $order,User $actor,string $reason):void{if(blank($reason))throw ValidationException::withMessages(['reason'=>'Ownership unlink reason is required.']);DB::transaction(function()use($order,$actor,$reason){$customerId=$order->customer_id;$order->supportTickets()->where('customer_id',$customerId)->update(['customer_id'=>null]);$order->update(['customer_id'=>null]);activity()->performedOn($order)->causedBy($actor)->withProperties(['customer_id'=>$customerId,'reason'=>$reason])->log('order.customer_unlinked');});}
 public function findSafeCustomerCandidate(Order $order):?User{if($order->customer_id)return $order->customer;return null;}
}
