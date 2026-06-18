<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkerScheduleController extends Controller
{
    public function index(Request $request)
    {
        $user         = $request->user()->load('workerAvailability');
        $availability = $user->workerAvailability;
        $isOnline     = in_array($availability?->status, ['online', 'available'], true);

        $todayShifts    = collect();
        $upcomingShifts = collect();
        $pastShifts     = collect();

        return view('worker.schedule', compact('availability', 'isOnline', 'todayShifts', 'upcomingShifts', 'pastShifts'));
    }
}
