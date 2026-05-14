<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BillingTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $user->load('activeMembership.membershipPlan');

        $membership = $user->activeMembership;
        $visitsThisMonth = Attendance::where('user_id', $user->id)
            ->whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->count();

        $recentAttendance = Attendance::where('user_id', $user->id)
            ->latest('date')->take(5)->get();

        $recentPayments = BillingTransaction::where('user_id', $user->id)
            ->latest('payment_date')->take(5)->get();

        $totalSpent = BillingTransaction::where('user_id', $user->id)
            ->where('payment_status', 'paid')->sum('amount');

        $pendingPayments = BillingTransaction::where('user_id', $user->id)
            ->where('payment_status', 'pending')
            ->count();

        return view('member.dashboard', compact(
            'user', 'membership', 'visitsThisMonth',
            'recentAttendance', 'recentPayments', 'totalSpent', 'pendingPayments'
        ));
    }
}
