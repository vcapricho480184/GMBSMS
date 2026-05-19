<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BillingTransaction;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index()
    {
        $baseQuery = BillingTransaction::where('user_id', auth()->id());

        $pendingPayments = (clone $baseQuery)
            ->where('payment_status', 'pending')
            ->latest('created_at')
            ->get();

        $paidPayments = (clone $baseQuery)
            ->where('payment_status', 'paid')
            ->latest('created_at')
            ->paginate(15, ['*'], 'paid_page')
            ->appends(request()->query());

        $pendingTotal = (clone $baseQuery)
            ->where('payment_status', 'pending')
            ->sum('amount');

        return view('member.payments.index', compact('pendingPayments', 'paidPayments', 'pendingTotal'));
    }

    public function show(BillingTransaction $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load([
            'items.membership.membershipPlan',
            'items.availedService.gymService',
            'membership.membershipPlan',
            'availedService.gymService',
        ]);
        return view('member.payments.show', compact('payment'));
    }

    public function receipt(BillingTransaction $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load([
            'user',
            'items.membership.membershipPlan',
            'items.availedService.gymService',
            'membership.membershipPlan',
            'availedService.gymService',
        ]);
        $billing = $payment;
        $pdf = Pdf::loadView('pdf.receipt', compact('billing'));
        return $pdf->download('receipt-' . $payment->invoice_number . '.pdf');
    }
}
