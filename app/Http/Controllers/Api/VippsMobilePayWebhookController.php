<?php
namespace App\Http\Controllers\Api; use App\Http\Controllers\Controller; use App\Services\Finance\PaymentService; use Illuminate\Http\{JsonResponse,Request};
class VippsMobilePayWebhookController extends Controller { public function __invoke(Request $request,PaymentService $payments):JsonResponse{$event=$payments->recordWebhook('vipps_mobilepay',$request->all(),$request->headers->all());return response()->json(['accepted'=>false,'status'=>$event->status],401);}}
