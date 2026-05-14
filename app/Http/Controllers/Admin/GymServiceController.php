<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GymService;
use Illuminate\Http\Request;

class GymServiceController extends Controller
{
    public function index()
    {
        $services = GymService::withCount('availedServices')->latest()->paginate(15);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,unavailable'],
        ]);

        GymService::create($request->all());

        return redirect()->route('admin.services.index')
            ->with('success', 'Gym service created successfully.');
    }

    public function edit(GymService $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, GymService $service)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['required', 'in:available,unavailable'],
        ]);

        $service->update($request->all());

        return redirect()->route('admin.services.index')
            ->with('success', 'Gym service updated successfully.');
    }

    public function destroy(GymService $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')
            ->with('success', 'Gym service deleted successfully.');
    }
}
