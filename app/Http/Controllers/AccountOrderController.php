<?php
namespace App\Http\Controllers;
use App\Models\Order;
use App\Services\Account\CustomerOwnershipService;
use Illuminate\Http\Request;
class AccountOrderController extends Controller {
 public function index(Request $request){return view('account.orders.index',['orders'=>$request->user()->customerOrders()->with('scenario')->latest()->get()]);}
 public function show(Request $request,Order $order,CustomerOwnershipService $ownership){abort_unless($ownership->canViewOrder($request->user(),$order),403);return view('account.orders.show',['order'=>$order->load(['scenario','priceQuotes','dispatchAssignments','completionProofs.events','supportTickets'=>fn($query)=>$query->where('customer_id',$request->user()->id),'billingDocuments'=>fn($query)=>$query->whereIn('status',['issued','paid','refunded'])])]);}
}
