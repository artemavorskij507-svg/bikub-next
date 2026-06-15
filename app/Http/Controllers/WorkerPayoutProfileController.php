<?php
namespace App\Http\Controllers;
use App\Services\Finance\WorkerPayoutProfileService;
use Illuminate\Http\Request;
class WorkerPayoutProfileController extends Controller {
 public function show(Request $r,WorkerPayoutProfileService $s){return view('worker.payout-profile',['readiness'=>$s->getReadiness($r->user()),'profile'=>$s->getOrCreateDraft($r->user())]);}
 public function update(Request $r,WorkerPayoutProfileService $s){$data=$r->validate(['payout_method'=>'required|in:manual_bank_review,vipps_deferred,external_provider_deferred','account_holder_name'=>'required|string|max:255','country'=>'required|in:NO','currency'=>'required|in:NOK','bank_account'=>'nullable|string|min:4|max:34','iban'=>'nullable|string|min:4|max:34','swift_bic'=>'nullable|string|min:4|max:16','vipps_phone'=>'nullable|string|min:4|max:24']);$s->updateDraft($r->user(),$data);return back()->with('status','Payout profile saved encrypted. No payout was executed.');}
 public function submit(Request $r,WorkerPayoutProfileService $s){$s->submit($r->user());return back()->with('status','Payout profile submitted for review.');}
}
