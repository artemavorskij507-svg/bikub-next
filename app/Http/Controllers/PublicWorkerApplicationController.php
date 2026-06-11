<?php
namespace App\Http\Controllers;
use App\Services\Workers\WorkerOnboardingService;
use Illuminate\Http\Request;
class PublicWorkerApplicationController extends Controller {
 public function create(){return view('public.workers.apply');}
 public function store(Request $request,WorkerOnboardingService $service){$data=$request->validate(['name'=>'required|string|max:255','email'=>'required|email|max:255','phone'=>'nullable|string|max:255','worker_type'=>'required|in:courier,worker,driver','desired_service_area'=>'nullable|string|max:255','vehicle_type'=>'nullable|string|max:255','capabilities'=>'required|array|min:1','capabilities.*'=>'in:delivery,moving,eco,handyman,towing,errands','experience_notes'=>'nullable|string|max:5000','compliance_acknowledged'=>'accepted']);$data['compliance_notes']='Applicant acknowledged that admin approval and compliance review are required.';$service->submitApplication($data);return redirect()->route('public.workers.received');}
 public function received(){return view('public.workers.received');}
}
