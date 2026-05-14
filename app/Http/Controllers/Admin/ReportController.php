<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingTransaction;
use App\Models\Membership;
use App\Models\Attendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function revenue(Request $request)
    {
        $from = $request->get('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $to = $request->get('date_to', Carbon::now()->format('Y-m-d'));

        $transactions = BillingTransaction::with('user')
            ->where('payment_status', 'paid')
            ->whereBetween('payment_date', [$from, $to])
            ->latest('payment_date')
            ->get();

        $total = $transactions->sum('amount');

        if ($request->get('export') === 'pdf') {
            $pdf = Pdf::loadView('pdf.report', [
                'title' => 'Revenue Report',
                'from' => $from, 'to' => $to,
                'transactions' => $transactions, 'total' => $total,
            ]);
            return $pdf->download('revenue-report.pdf');
        }

        return view('admin.reports.revenue', compact('transactions', 'total', 'from', 'to'));
    }

    public function memberships(Request $request)
    {
        $status = $request->get('status', 'active');
        $memberships = Membership::with(['user', 'membershipPlan'])
            ->where('status', $status)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reports.memberships', compact('memberships', 'status'));
    }

    public function attendanceReport(Request $request)
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $records = Attendance::with('user')
            ->whereDate('date', $date)
            ->latest('check_in')
            ->get();

        return view('admin.reports.attendance', compact('records', 'date'));
    }
}
