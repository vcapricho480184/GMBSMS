<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingTransaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BillingController extends Controller
{
    public function index(Request $request)
    {
        $query = BillingTransaction::with('user');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($q2) => $q2->where('name', 'like', '%' . $request->search . '%'));
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        $totalPaid = BillingTransaction::paid()->sum('amount');
        $totalPending = BillingTransaction::pending()->sum('amount');

        return view('admin.billing.index', compact('transactions', 'totalPaid', 'totalPending'));
    }

    public function show(BillingTransaction $billing)
    {
        $billing->load(['user', 'membership.membershipPlan', 'availedService.gymService']);
        return view('admin.billing.show', compact('billing'));
    }

    public function update(Request $request, BillingTransaction $billing)
    {
        $request->validate([
            'payment_status' => ['required', 'in:paid,pending,failed,refunded'],
            'payment_method' => ['nullable', 'in:cash,card,gcash,paymaya,bank_transfer,other'],
        ]);

        $billing->update($request->only(['payment_status', 'payment_method']));

        return back()->with('success', 'Transaction updated successfully.');
    }

    public function receipt(BillingTransaction $billing)
    {
        $billing->load(['user', 'membership.membershipPlan', 'availedService.gymService']);

        $pdf = Pdf::loadView('pdf.receipt', compact('billing'));
        return $pdf->download('receipt-' . $billing->invoice_number . '.pdf');
    }
}
