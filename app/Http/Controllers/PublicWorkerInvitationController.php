<?php
namespace App\Http\Controllers; use App\Services\Workers\WorkerAccountInvitationService; use Illuminate\Http\Request;
class PublicWorkerInvitationController extends Controller {
 public function show(string $token,WorkerAccountInvitationService $s){try{$i=$s->resolve($token);return view('public.workers.invitation',['invitation'=>$i,'token'=>$token]);}catch(\Throwable $e){return response()->view('public.workers.invitation-error',['message'=>'Invitation is expired, cancelled, accepted, or invalid.'],410);}}
 public function store(Request $r,string $token,WorkerAccountInvitationService $s){$d=$r->validate(['name'=>'required|string|max:255','password'=>'required|string|min:12|confirmed']);$s->acceptInvitation($token,$d);return redirect()->route('public.worker-invitations.received');}
 public function received(){return view('public.workers.account-created');}
}
