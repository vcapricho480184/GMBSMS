<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MembershipPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'member')->with('activeMembership.membershipPlan');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereHas('activeMembership');
            } elseif ($request->status === 'inactive') {
                $query->whereDoesntHave('activeMembership');
            }
        }

        $members = $query->latest()->paginate(15)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function create()
    {
        $plans = MembershipPlan::active()->get();
        return view('admin.members.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->except(['password_confirmation', 'photo']);
        $data['password'] = Hash::make($request->password);
        $data['role'] = 'member';

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        $user = User::create($data);

        return redirect()->route('admin.members.index')
            ->with('success', 'Member created successfully.');
    }

    public function show(User $member)
    {
        $member->load([
            'memberships.membershipPlan',
            'availedServices.gymService',
            'billingTransactions',
            'attendance' => fn($q) => $q->latest()->take(20),
        ]);

        return view('admin.members.show', compact('member'));
    }

    public function edit(User $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, User $member)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $member->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', 'in:male,female,other'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->except(['photo', '_token', '_method']);

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $data['photo'] = $request->file('photo')->store('members', 'public');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $member->update($data);

        return redirect()->route('admin.members.index')
            ->with('success', 'Member updated successfully.');
    }

    public function destroy(User $member)
    {
        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }
        $member->delete();

        return redirect()->route('admin.members.index')
            ->with('success', 'Member deleted successfully.');
    }
}
