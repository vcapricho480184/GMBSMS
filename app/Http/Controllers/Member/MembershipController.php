<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = auth()->user()->memberships()
            ->with('membershipPlan')
            ->latest()
            ->paginate(10);

        return view('member.membership.index', compact('memberships'));
    }
}
