<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingTransaction;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $query = Membership::with(['user', 'membershipPlan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $memberships = $query->latest()->paginate(15)->appends($request->query());

        return view('admin.memberships.index', compact('memberships'));
    }

    public function create()
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        $plans = MembershipPlan::active()->get();
        return view('admin.memberships.create', compact('members', 'plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'membership_plan_id' => ['required', 'exists:membership_plans,id'],
            'start_date' => ['required', 'date'],
        ]);

        $plan = MembershipPlan::findOrFail($request->membership_plan_id);

        // Expire any active membership
        Membership::where('user_id', $request->user_id)
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $startDate = Carbon::parse($request->start_date);
        $membership = Membership::create([
            'user_id' => $request->user_id,
            'membership_plan_id' => $plan->id,
            'start_date' => $startDate,
            'end_date' => $startDate->copy()->addDays($plan->duration_days),
            'status' => 'active',
        ]);

        BillingTransaction::createForMembership($membership);

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership assigned successfully. You can bill this member from the Billing module.');
    }

    public function destroy(Membership $membership)
    {
        $membership->update(['status' => 'cancelled']);

        return redirect()->route('admin.memberships.index')
            ->with('success', 'Membership cancelled successfully.');
    }
}
