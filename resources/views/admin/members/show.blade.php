@extends('layouts.admin')
@section('title', 'Member Profile')
@section('page-title', 'Member Profile')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="mb-3">
                    <div style="width:80px;height:80px;border-radius:50%;background:var(--primary);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                </div>
                <h5 class="mb-1">{{ $member->name }}</h5>
                <p class="text-muted mb-2">{{ $member->email }}</p>
                @if($member->activeMembership)
                    <span class="badge badge-active">Active Member</span>
                @else
                    <span class="badge badge-expired">Inactive</span>
                @endif
                <hr>
                <div class="text-start" style="font-size:0.85rem;">
                    <p class="mb-2"><i class="bi bi-phone me-2 text-muted"></i>{{ $member->phone ?? 'N/A' }}</p>
                    <p class="mb-2"><i class="bi bi-geo-alt me-2 text-muted"></i>{{ $member->address ?? 'N/A' }}</p>
                    <p class="mb-2"><i class="bi bi-calendar me-2 text-muted"></i>{{ $member->date_of_birth ? $member->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                    <p class="mb-2"><i class="bi bi-person me-2 text-muted"></i>{{ ucfirst($member->gender ?? 'N/A') }}</p>
                    <p class="mb-0"><i class="bi bi-telephone me-2 text-muted"></i>{{ $member->emergency_contact ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <!-- Membership History -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-card-checklist me-2"></i>Membership History</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Plan</th><th>Start</th><th>End</th><th>Status</th></tr></thead>
                        <tbody>
                            @forelse($member->memberships as $m)
                            <tr>
                                <td class="fw-medium">{{ $m->membershipPlan->name ?? 'N/A' }}</td>
                                <td>{{ $m->start_date->format('M d, Y') }}</td>
                                <td>{{ $m->end_date->format('M d, Y') }}</td>
                                <td><span class="badge badge-{{ $m->status }}">{{ ucfirst($m->status) }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No memberships</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment History -->
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-receipt me-2"></i>Payment History</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Invoice</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            @forelse($member->billingTransactions->take(10) as $t)
                            <tr>
                                <td class="fw-medium">{{ $t->invoice_number }}</td>
                                <td>{{ $t->formatted_amount }}</td>
                                <td>{{ $t->payment_method_label }}</td>
                                <td><span class="badge badge-{{ $t->payment_status }}">{{ ucfirst($t->payment_status) }}</span></td>
                                <td>{{ $t->payment_date->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No payments</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Attendance -->
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-check me-2"></i>Recent Attendance</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Duration</th></tr></thead>
                        <tbody>
                            @forelse($member->attendance as $a)
                            <tr>
                                <td>{{ $a->date->format('M d, Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($a->check_in)->format('h:i A') }}</td>
                                <td>{{ $a->check_out ? \Carbon\Carbon::parse($a->check_out)->format('h:i A') : '—' }}</td>
                                <td>{{ $a->duration ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">No attendance records</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
