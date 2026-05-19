<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingTransaction;
use App\Models\User;
use App\Models\Membership;
use App\Models\AvailedService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $pendingTransactions = (clone $query)
            ->where('payment_status', 'pending')
            ->latest()
            ->paginate(10, ['*'], 'pending_page')
            ->appends($request->query());

        $paidTransactions = (clone $query)
            ->where('payment_status', 'paid')
            ->latest()
            ->paginate(10, ['*'], 'paid_page')
            ->appends($request->query());

        $otherTransactions = (clone $query)
            ->whereNotIn('payment_status', ['pending', 'paid'])
            ->latest()
            ->get();

        $totalPaid = BillingTransaction::paid()->sum('amount');
        $totalPending = BillingTransaction::pending()->sum('amount');
        $totalRevenue = BillingTransaction::paid()->whereMonth('created_at', now()->month)->sum('amount');

        return view('admin.billing.index', compact('pendingTransactions', 'paidTransactions', 'otherTransactions', 'totalPaid', 'totalPending', 'totalRevenue'));
    }

    public function create()
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        $memberships = Membership::with(['user', 'membershipPlan'])
            ->where('status', 'active')
            ->whereDoesntHave('billingItems')
            ->get();
        $services = AvailedService::with(['user', 'gymService'])
            ->where('status', 'approved')
            ->whereDoesntHave('billingItems')
            ->get();

        return view('admin.billing.create', compact('members', 'memberships', 'services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'membership_ids' => ['nullable', 'array'],
            'membership_ids.*' => ['integer', 'exists:memberships,id'],
            'service_ids' => ['nullable', 'array'],
            'service_ids.*' => ['integer', 'exists:availed_services,id'],
            'custom_items' => ['nullable', 'array'],
            'custom_items.*.description' => ['nullable', 'string', 'max:500'],
            'custom_items.*.amount' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:500'],
            'discount' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment_method' => ['required', 'in:cash,card,gcash,paymaya,bank_transfer,other'],
            'payment_status' => ['required', 'in:pending,paid'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::beginTransaction();
        try {
            $items = [];
            $membershipIds = $validated['membership_ids'] ?? [];
            $serviceIds = $validated['service_ids'] ?? [];

            $memberships = Membership::with('membershipPlan')
                ->whereIn('id', $membershipIds)
                ->where('user_id', $validated['user_id'])
                ->where('status', 'active')
                ->whereDoesntHave('billingItems')
                ->get();

            if (count($membershipIds) !== $memberships->count()) {
                return back()->withInput()->with('error', 'Some memberships are invalid or already billed.');
            }

            foreach ($memberships as $membership) {
                $plan = $membership->membershipPlan;
                if (!$plan) {
                    continue;
                }

                $items[] = [
                    'item_type' => 'membership',
                    'membership_id' => $membership->id,
                    'availed_service_id' => null,
                    'description' => $plan->name . ' Membership Plan',
                    'quantity' => 1,
                    'unit_price' => $plan->price,
                    'amount' => $plan->price,
                ];
            }

            $services = AvailedService::with('gymService')
                ->whereIn('id', $serviceIds)
                ->where('user_id', $validated['user_id'])
                ->where('status', 'approved')
                ->whereDoesntHave('billingItems')
                ->get();

            if (count($serviceIds) !== $services->count()) {
                return back()->withInput()->with('error', 'Some services are invalid or already billed.');
            }

            foreach ($services as $service) {
                $gymService = $service->gymService;
                if (!$gymService) {
                    continue;
                }

                $items[] = [
                    'item_type' => 'service',
                    'membership_id' => null,
                    'availed_service_id' => $service->id,
                    'description' => $gymService->name,
                    'quantity' => 1,
                    'unit_price' => $gymService->price,
                    'amount' => $gymService->price,
                ];
            }

            $customItems = $validated['custom_items'] ?? [];
            foreach ($customItems as $customItem) {
                $description = trim((string) ($customItem['description'] ?? ''));
                $amount = $customItem['amount'] ?? null;

                if ($description === '' || $amount === null || $amount === '') {
                    continue;
                }

                $items[] = [
                    'item_type' => 'other',
                    'membership_id' => null,
                    'availed_service_id' => null,
                    'description' => $description,
                    'quantity' => 1,
                    'unit_price' => $amount,
                    'amount' => $amount,
                ];
            }

            if (empty($items)) {
                return back()->withInput()->with('error', 'Please add at least one item to the invoice.');
            }

            $subtotal = collect($items)->sum('amount');
            $discountAmount = $subtotal * (($validated['discount'] ?? 0) / 100);
            $afterDiscount = $subtotal - $discountAmount;
            $taxAmount = $afterDiscount * (($validated['tax_rate'] ?? 0) / 100);
            $totalAmount = $afterDiscount + $taxAmount;

            $typeSet = collect($items)->pluck('item_type')->unique()->values();
            $invoiceType = $typeSet->count() > 1 ? 'mixed' : $typeSet->first();
            $description = trim((string) ($validated['description'] ?? ''));
            if ($description === '') {
                $description = $this->buildInvoiceDescription($items);
            }

            if ($validated['payment_status'] === 'paid' && empty($validated['payment_date'])) {
                $validated['payment_date'] = now();
            }

            $billing = BillingTransaction::create([
                'user_id' => $validated['user_id'],
                'invoice_number' => BillingTransaction::generateInvoiceNumber(),
                'type' => $invoiceType,
                'description' => Str::limit($description ?: 'Multiple items', 500, ''),
                'amount' => $totalAmount,
                'subtotal' => $subtotal,
                'discount_percentage' => $validated['discount'] ?? 0,
                'discount_amount' => $discountAmount,
                'tax_rate' => $validated['tax_rate'] ?? 0,
                'tax_amount' => $taxAmount,
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_status'],
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $billing->items()->createMany($items);

            DB::commit();

            return redirect()
                ->route('admin.billing.show', $billing)
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    protected function buildInvoiceDescription(array $items): string
    {
        $groups = collect($items)
            ->groupBy('item_type')
            ->map(function ($group, $type) {
                $label = match ($type) {
                    'membership' => 'Memberships',
                    'service' => 'Approved Services',
                    'other' => 'Custom Items',
                    default => ucfirst($type),
                };

                $names = $group
                    ->pluck('description')
                    ->filter()
                    ->values()
                    ->implode(', ');

                return $names ? $label . ': ' . $names : null;
            })
            ->filter()
            ->values()
            ->all();

        return implode(' | ', $groups);
    }

    public function show(BillingTransaction $billing)
    {
        $billing->load([
            'user',
            'items.membership.membershipPlan',
            'items.availedService.gymService',
            'membership.membershipPlan',
            'availedService.gymService',
        ]);
        return view('admin.billing.show', compact('billing'));
    }

    public function edit(BillingTransaction $billing)
    {
        $members = User::where('role', 'member')->orderBy('name')->get();
        $memberships = Membership::with(['user', 'membershipPlan'])
            ->where('status', 'active')
            ->get();
        $services = AvailedService::with(['user', 'gymService'])
            ->where('status', 'approved')
            ->get();

        return view('admin.billing.edit', compact('billing', 'members', 'memberships', 'services'));
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

        return back()->with('success', 'Transaction updated successfully.');
    }

    public function destroy(BillingTransaction $billing)
    {
        if ($billing->payment_status === 'paid') {
            return back()->with('error', 'Cannot delete paid transactions.');
        }

        $billing->delete();

        return redirect()
            ->route('admin.billing.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    public function invoice(BillingTransaction $billing)
    {
        $billing->load([
            'user',
            'items.membership.membershipPlan',
            'items.availedService.gymService',
            'membership.membershipPlan',
            'availedService.gymService',
        ]);
        return view('admin.billing.invoice', compact('billing'));
    }

    public function downloadInvoice(BillingTransaction $billing)
    {
        $billing->load([
            'user',
            'items.membership.membershipPlan',
            'items.availedService.gymService',
            'membership.membershipPlan',
            'availedService.gymService',
        ]);

        $pdf = Pdf::loadView('pdf.invoice', compact('billing'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('invoice-' . $billing->invoice_number . '.pdf');
    }

    public function receipt(BillingTransaction $billing)
    {
        $billing->load([
            'user',
            'items.membership.membershipPlan',
            'items.availedService.gymService',
            'membership.membershipPlan',
            'availedService.gymService',
        ]);

        $pdf = Pdf::loadView('pdf.receipt', compact('billing'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('receipt-' . $billing->invoice_number . '.pdf');
    }

    public function previewInvoice(BillingTransaction $billing)
    {
        $billing->load([
            'user',
            'items.membership.membershipPlan',
            'items.availedService.gymService',
            'membership.membershipPlan',
            'availedService.gymService',
        ]);

        $pdf = Pdf::loadView('pdf.invoice', compact('billing'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('invoice-' . $billing->invoice_number . '.pdf');
    }
}
