<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WorkerProfileController extends Controller
{
    public function index(Request $request)
    {
        return view('worker.profile', ['user' => $request->user()->load('workerAvailability', 'workerProfile')]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'phone'        => ['nullable', 'string', 'max:50'],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
        ]);

        $request->user()->update(['name' => $data['name']]);

        if ($request->user()->workerProfile) {
            $profileData = array_filter([
                'phone'        => $data['phone'] ?? null,
                'vehicle_type' => $data['vehicle_type'] ?? null,
            ], fn ($v) => !is_null($v));

            if ($profileData) {
                $request->user()->workerProfile->update($profileData);
            }
        }

        return back()->with('status', 'Profile updated successfully.');
    }
}
