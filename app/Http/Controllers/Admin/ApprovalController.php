<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BillingTransaction;
use App\Models\AvailedService;
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

        $requests = $query->latest()->paginate(20)->appends($request->query());
        $pendingCount = AvailedService::pending()->count();

        return view('admin.approvals.index', compact('requests', 'pendingCount'));
    }

    public function approve(Request $request, AvailedService $availedService)
    {
        if ($availedService->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $availedService->update([
            'status' => 'approved',
            'admin_notes' => null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        BillingTransaction::createForAvailedService($availedService);

        return back()->with('success', 'Service request approved successfully!');
    }

    public function reject(Request $request, AvailedService $availedService)
    {
        if ($availedService->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $availedService->update([
            'status' => 'rejected',
            'admin_notes' => null,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Service request rejected.');
    }
}
