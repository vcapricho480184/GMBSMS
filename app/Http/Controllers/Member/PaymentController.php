<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\BillingTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = BillingTransaction::where('user_id', auth()->id())
            ->latest('created_at')
            ->paginate(15);

        $pendingTotal = BillingTransaction::where('user_id', auth()->id())
            ->where('payment_status', 'pending')
            ->sum('amount');

        return view('member.payments.index', compact('payments', 'pendingTotal'));
    }

    public function show(BillingTransaction $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load(['membership.membershipPlan', 'availedService.gymService']);
        return view('member.payments.show', compact('payment'));
    }

    public function processPayment(Request $request, BillingTransaction $payment)
    {
        // Check if payment belongs to authenticated user
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if payment is already paid
        if ($payment->payment_status === 'paid') {
            return back()->with('error', 'This payment has already been processed.');
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,card,gcash,paymaya,bank_transfer',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $updateData = [
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'paid',
            'payment_date' => now(),
        ];

        // Add reference number to description if provided
        if (!empty($validated['reference_number'])) {
            $currentDescription = $payment->description ?? '';
            $updateData['description'] = $currentDescription . ' [Ref: ' . $validated['reference_number'] . ']';
        }

        $payment->update($updateData);

        return redirect()->route('member.payments.index')
            ->with('success', 'Payment processed successfully! Invoice: ' . $payment->invoice_number);
    }

    public function processMultiplePayments(Request $request)
    {
        $validated = $request->validate([
            'payment_ids' => 'required|string',
            'payment_method' => 'required|in:cash,card,gcash,paymaya,bank_transfer',
            'reference_number' => 'nullable|string|max:100',
        ]);

        // Parse payment IDs
        $paymentIds = explode(',', $validated['payment_ids']);
        
        // Get payments that belong to the authenticated user and are pending
        $payments = BillingTransaction::whereIn('id', $paymentIds)
            ->where('user_id', auth()->id())
            ->where('payment_status', 'pending')
            ->get();

        if ($payments->isEmpty()) {
            return back()->with('error', 'No valid pending payments found.');
        }

        $processedCount = 0;
        $totalAmount = 0;
        $invoiceNumbers = [];

        foreach ($payments as $payment) {
            $updateData = [
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'payment_date' => now(),
            ];

            // Add reference number to description if provided
            if (!empty($validated['reference_number'])) {
                $currentDescription = $payment->description ?? '';
                $updateData['description'] = $currentDescription . ' [Ref: ' . $validated['reference_number'] . ']';
            }

            $payment->update($updateData);
            
            $processedCount++;
            $totalAmount += $payment->amount;
            $invoiceNumbers[] = $payment->invoice_number;
        }

        $message = "Successfully processed {$processedCount} payment(s) totaling ₱" . number_format($totalAmount, 2) . ". ";
        $message .= "Invoices: " . implode(', ', $invoiceNumbers);

        return redirect()->route('member.payments.index')
            ->with('success', $message);
    }

    public function receipt(BillingTransaction $payment)
    {
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        $payment->load(['user', 'membership.membershipPlan', 'availedService.gymService']);
        $billing = $payment;
        $pdf = Pdf::loadView('pdf.receipt', compact('billing'));
        return $pdf->download('receipt-' . $payment->invoice_number . '.pdf');
    }
}
