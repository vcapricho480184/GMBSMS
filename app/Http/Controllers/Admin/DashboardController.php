<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\BillingTransaction;
use App\Models\Attendance;
use App\Models\AvailedService;
use App\Models\GymService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMembers = User::where('role', 'member')->count();
        $activeMemberships = Membership::where('status', 'active')->count();
        $monthlyRevenue = BillingTransaction::where('payment_status', 'paid')
            ->whereMonth('payment_date', Carbon::now()->month)
            ->whereYear('payment_date', Carbon::now()->year)
            ->sum('amount');
        $expiringSoon = Membership::expiringSoon()->count();
        $todayAttendance = Attendance::whereDate('date', Carbon::today())->count();
        $pendingPayments = BillingTransaction::where('payment_status', 'pending')->count();

        // Revenue chart data (last 6 months)
        $revenueData = [];
        $revenueLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $revenueLabels[] = $date->format('M Y');
            $revenueData[] = BillingTransaction::where('payment_status', 'paid')
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');
        }

        // Membership distribution
        $planDistribution = MembershipPlan::withCount(['memberships' => function ($query) {
            $query->where('status', 'active');
        }])->get();

        // Recent members
        $recentMembers = User::where('role', 'member')
            ->latest()
            ->take(5)
            ->get();

        // Today's attendance
        $todayAttendanceRecords = Attendance::with('user')
            ->whereDate('date', Carbon::today())
            ->latest()
            ->take(10)
            ->get();

        // Top services
        $topServices = GymService::withCount('availedServices')
            ->orderByDesc('availed_services_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalMembers', 'activeMemberships', 'monthlyRevenue', 'expiringSoon',
            'todayAttendance', 'pendingPayments', 'revenueData', 'revenueLabels',
            'planDistribution', 'recentMembers', 'todayAttendanceRecords', 'topServices'
        ));
    }
}
