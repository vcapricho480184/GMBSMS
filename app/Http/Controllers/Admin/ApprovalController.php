<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AvailedService;
use App\Models\BillingTransaction;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = AvailedService::with(['user', 'gymService']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to pending
            $query->where('status', 'pending');
        }

        // Search by member name
        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%' . $request->search . '%'));
        }

        $requests = $query->latest()->paginate(20)->withQueryString();
        $pendingCount = AvailedService::pending()->count();

        return view('admin.approvals.index', compact('requests', 'pendingCount'));
    }

    public function approve(Request $request, AvailedService $availedService)
    {
        if ($availedService->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $availedService->update([
            'status' => 'approved',
            'admin_notes' => $request->admin_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Create billing transaction for approved service
        $service = $availedService->gymService;
        $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad($availedService->id, 4, '0', STR_PAD_LEFT);

        BillingTransaction::create([
            'user_id' => $availedService->user_id,
            'availed_service_id' => $availedService->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $service->price,
            'payment_method' => 'cash', // Default method, member can change when paying
            'payment_status' => 'pending',
            'type' => 'service',
            'description' => $service->name,
            'payment_date' => null,
        ]);

        return back()->with('success', 'Service request approved successfully!');
    }

    public function reject(Request $request, AvailedService $availedService)
    {
        if ($availedService->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:500',
        ]);

        $availedService->update([
            'status' => 'rejected',
            'admin_notes' => $request->admin_notes,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Service request rejected.');
    }
}
