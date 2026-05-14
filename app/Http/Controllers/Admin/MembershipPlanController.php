<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;

class MembershipPlanController extends Controller
{
    public function index()
    {
        $plans = MembershipPlan::withCount(['memberships' => fn($q) => $q->where('status', 'active')])
            ->latest()
            ->paginate(15);

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        MembershipPlan::create($request->all());

        return redirect()->route('admin.plans.index')
            ->with('success', 'Membership plan created successfully.');
    }

    public function edit(MembershipPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, MembershipPlan $plan)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $plan->update($request->all());

        return redirect()->route('admin.plans.index')
            ->with('success', 'Membership plan updated successfully.');
    }

    public function destroy(MembershipPlan $plan)
    {
        if ($plan->memberships()->where('status', 'active')->exists()) {
            return back()->with('error', 'Cannot delete plan with active memberships.');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Membership plan deleted successfully.');
    }
}
