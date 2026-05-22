<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingTransaction;
use App\Models\User;
use App\Models\Membership;
use App\Models\AvailedService;
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

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('method')) {
            $query->where('payment_method', $request->input('method'));
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15)->appends($request->query());

        $totalPaid = BillingTransaction::paid()->sum('amount');
        $totalPending = BillingTransaction::pending()->sum('amount');
        $totalRevenue = BillingTransaction::paid()->whereMonth('created_at', now()->month)->sum('amount');

        return view('admin.billing.index', compact('transactions', 'totalPaid', 'totalPending', 'totalRevenue'));
    }

    public function create()
    {
        $members = User::where('role', 'member')->orderBy('name')->get();

        $memberships = Membership::with(['user', 'membershipPlan'])
            ->where('status', 'active')
            ->unbilled()
            ->get();

        $services = AvailedService::with(['user', 'gymService'])
            ->where('status', 'approved')
            ->unbilled()
            ->get();

        return view('admin.billing.create', compact('members', 'memberships', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'type' => ['required', 'in:membership,service,other'],
            'description' => ['required', 'string', 'max:500'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:cash,card,gcash,paymaya,bank_transfer,other'],
            'payment_status' => ['required', 'in:pending,paid'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'related_id' => ['nullable', 'integer'],
        ]);

        $validated['invoice_number'] = BillingTransaction::generateInvoiceNumber();

        if ($validated['payment_status'] === 'paid' && empty($validated['payment_date'])) {
            $validated['payment_date'] = now();
        }

        $billing = BillingTransaction::create(collect($validated)->except('related_id')->toArray());

        if ($request->filled('related_id')) {
            if ($validated['payment_status'] === 'paid') {
                if ($validated['type'] === 'membership') {
                    Membership::where('id', $validated['related_id'])->update(['billed' => true]);
                } elseif ($validated['type'] === 'service') {
                    AvailedService::where('id', $validated['related_id'])->update(['billed' => true]);
                }
            }

            BillingTransaction::where('type', $validated['type'])
                ->where('user_id', $validated['user_id'])
                ->where('description', $validated['description'])
                ->where('payment_status', 'pending')
                ->where('id', '!=', $billing->id)
                ->delete();
        }

        return redirect()
            ->route('admin.billing.show', $billing)
            ->with('success', 'Transaction created successfully.');
    }

    public function show(BillingTransaction $billing)
    {
        $billing->load('user');
        return view('admin.billing.show', compact('billing'));
    }

    public function update(Request $request, BillingTransaction $billing)
    {
        $validated = $request->validate([
            'payment_status' => ['required', 'in:paid,pending,refunded'],
            'payment_method' => ['nullable', 'in:cash,card,gcash,paymaya,bank_transfer,other'],
            'payment_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validated['payment_status'] === 'paid' && empty($validated['payment_date'])) {
            $validated['payment_date'] = now();
        }

        $billing->update($validated);

        if ($validated['payment_status'] === 'paid') {
            if ($billing->type === 'membership') {
                $planName = str_replace(' Membership Plan', '', $billing->description);
                $membership = Membership::where('user_id', $billing->user_id)
                    ->whereHas('membershipPlan', function($q) use ($planName) {
                        $q->where('name', $planName);
                    })->first();
                if ($membership) {
                    $membership->update(['billed' => true]);
                }
            } elseif ($billing->type === 'service') {
                $service = AvailedService::where('user_id', $billing->user_id)
                    ->whereHas('gymService', function($q) use ($billing) {
                        $q->where('name', $billing->description);
                    })->first();
                if ($service) {
                    $service->update(['billed' => true]);
                }
            }
        }

        return back()->with('success', 'Transaction updated successfully.');
    }

    public function destroy(BillingTransaction $billing)
    {
        if ($billing->payment_status === 'paid') {
            return back()->with('error', 'Cannot delete paid transactions.');
        }

        if ($billing->type === 'membership') {
            $planName = str_replace(' Membership Plan', '', $billing->description);
            $membership = Membership::where('user_id', $billing->user_id)
                ->whereHas('membershipPlan', function($q) use ($planName) {
                    $q->where('name', $planName);
                })->first();
            
            if ($membership) {
                $membership->update(['billed' => false]);
            }
        } elseif ($billing->type === 'service') {
            $service = AvailedService::where('user_id', $billing->user_id)
                ->whereHas('gymService', function($q) use ($billing) {
                    $q->where('name', $billing->description);
                })->first();
                
            if ($service) {
                $service->update(['billed' => false]);
            }
        }

        $billing->delete();

        return redirect()
            ->route('admin.billing.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function invoice(BillingTransaction $billing)
    {
        $billing->load('user');
        return view('admin.billing.invoice', compact('billing'));
    }

    public function downloadInvoice(BillingTransaction $billing)
    {
        $billing->load('user');

        $pdf = Pdf::loadView('pdf.invoice', compact('billing'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . $billing->invoice_number . '.pdf');
    }

    public function receipt(BillingTransaction $billing)
    {
        $billing->load('user');

        $pdf = Pdf::loadView('pdf.receipt', compact('billing'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('receipt-' . $billing->invoice_number . '.pdf');
    }

    public function previewInvoice(BillingTransaction $billing)
    {
        $billing->load('user');

        $pdf = Pdf::loadView('pdf.invoice', compact('billing'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-' . $billing->invoice_number . '.pdf');
    }
}
