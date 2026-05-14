<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\GymService;
use App\Models\AvailedService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function available()
    {
        $services = GymService::available()->get();
        return view('member.services.available', compact('services'));
    }

    public function request(Request $request)
    {
        $request->validate([
            'gym_service_id' => 'required|exists:gym_services,id',
            'availed_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        AvailedService::create([
            'user_id' => auth()->id(),
            'gym_service_id' => $request->gym_service_id,
            'availed_date' => $request->availed_date,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Service request submitted successfully! Waiting for admin approval.');
    }

    public function availed()
    {
        $availed = auth()->user()->availedServices()
            ->with('gymService')
            ->latest()
            ->paginate(15);

        return view('member.services.availed', compact('availed'));
    }

    public function edit(AvailedService $availedService)
    {
        // Check if the service belongs to the authenticated user
        if ($availedService->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow editing if status is pending
        if ($availedService->status !== 'pending') {
            return back()->with('error', 'You can only edit pending service requests.');
        }

        $services = GymService::available()->get();
        return view('member.services.edit', compact('availedService', 'services'));
    }

    public function update(Request $request, AvailedService $availedService)
    {
        // Check if the service belongs to the authenticated user
        if ($availedService->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow editing if status is pending
        if ($availedService->status !== 'pending') {
            return back()->with('error', 'You can only edit pending service requests.');
        }

        $request->validate([
            'gym_service_id' => 'required|exists:gym_services,id',
            'availed_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ]);

        $availedService->update([
            'gym_service_id' => $request->gym_service_id,
            'availed_date' => $request->availed_date,
            'notes' => $request->notes,
        ]);

        return redirect()->route('member.services.availed')->with('success', 'Service request updated successfully!');
    }

    public function destroy(AvailedService $availedService)
    {
        // Check if the service belongs to the authenticated user
        if ($availedService->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow deleting if status is pending
        if ($availedService->status !== 'pending') {
            return back()->with('error', 'You can only cancel pending service requests.');
        }

        $availedService->delete();

        return back()->with('success', 'Service request cancelled successfully.');
    }
}
