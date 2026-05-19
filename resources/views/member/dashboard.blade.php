@extends('layouts.member')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Membership Status Card -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-muted mb-1">Current Membership</h6>
                        @if($membership)
                            <h4 class="mb-1">{{ $membership->membershipPlan->name ?? 'N/A' }}</h4>
                            <span class="badge badge-active mb-3">Active</span>
                            <div class="row g-3 mt-1" style="font-size:0.85rem;">
                                <div class="col-auto"><strong>Start:</strong> {{ $membership->start_date->format('M d, Y') }}</div>
                                <div class="col-auto"><strong>End:</strong> {{ $membership->end_date->format('M d, Y') }}</div>
                                <div class="col-auto"><strong>Days Left:</strong> <span class="{{ $membership->days_remaining <= 7 ? 'text-warning fw-bold' : 'text-success fw-bold' }}">{{ $membership->days_remaining }} days</span></div>
                            </div>
                        @else
                            <h5 class="text-muted">No Active Membership</h5>
                            <p class="text-muted mb-0" style="font-size:0.85rem;">Contact the admin to get a membership plan.</p>
                        @endif
                    </div>
                    @if($membership)
                    @php
                        $progressPercent = 0;
                        if ($membership->membershipPlan && $membership->membershipPlan->duration_days > 0) {
                            $progressPercent = max(0, min(100, ($membership->days_remaining / $membership->membershipPlan->duration_days) * 100));
                        }
                    @endphp
                    <div class="text-end">
                        <div class="membership-progress-ring" data-progress="{{ $progressPercent }}">
                            <div class="membership-progress-inner">{{ $membership->days_remaining }}d</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="row g-3">
            <div class="col-6 col-lg-12">
                <div class="metric-card">
                    <div class="metric-icon success"><i class="bi bi-calendar-check"></i></div>
                    <div class="metric-info"><h3>{{ $visitsThisMonth }}</h3><p>Visits This Month</p></div>
                </div>
            </div>
            <div class="col-6 col-lg-12">
                <div class="metric-card">
                    <div class="metric-icon info"><i class="bi bi-wallet2"></i></div>
                    <div class="metric-info"><h3>₱{{ number_format($totalSpent, 2) }}</h3><p>Total Spent</p></div>
                </div>
            </div>
            <div class="col-6 col-lg-12">
                <div class="metric-card">
                    <div class="metric-icon warning"><i class="bi bi-exclamation-circle"></i></div>
                    <div class="metric-info"><h3>₱{{ number_format($pendingBalance, 2) }}</h3><p>Pending Balance</p></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Quick Action -->
<div class="card mb-4">
    <div class="card-body">
        <div>
            <h6 class="mb-1"><i class="bi bi-calendar-check me-2"></i>Attendance</h6>
            <p class="text-muted mb-0" style="font-size:0.85rem;">Your attendance is processed by the admin. <a href="{{ route('member.attendance.index') }}">View your records</a></p>
        </div>
    </div>
</div>

<!-- Pending Payments Alert -->
@if($pendingPayments > 0)
<div class="alert alert-warning mb-4 d-flex justify-content-between align-items-center">
    <div>
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>Pending Payments:</strong> You have {{ $pendingPayments }} pending payment{{ $pendingPayments > 1 ? 's' : '' }}. This is a notification only and updates when your status changes.
    </div>
    <a href="{{ route('member.payments.index') }}" class="btn btn-warning btn-sm">
        <i class="bi bi-eye me-1"></i> View Payments
    </a>
</div>
@endif

<!-- Recent Activity -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><span><i class="bi bi-calendar-check me-2"></i>Recent Attendance</span><a href="{{ route('member.attendance.index') }}" class="btn btn-sm btn-outline-primary">View All</a></div>
            <div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
                <thead><tr><th>Date</th><th>In</th><th>Out</th></tr></thead>
                <tbody>
                    @forelse($recentAttendance as $a)
                    <tr>
                        <td>{{ $a->date->format('M d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($a->check_in)->format('h:i A') }}</td>
                        <td>
                            @if($a->check_out)
                                {{ \Carbon\Carbon::parse($a->check_out)->format('h:i A') }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted py-3">No records</td></tr>
                    @endforelse
                </tbody>
            </table></div></div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>Recent Payments</span>
                <a href="{{ route('member.payments.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
                <thead><tr><th>Invoice</th><th>Amount</th><th>Status</th><th>Details</th></tr></thead>
                <tbody>
                    @forelse($recentPayments as $p)
                    <tr>
                        <td>{{ $p->invoice_number }}</td>
                        <td class="fw-medium">{{ $p->formatted_amount }}</td>
                        <td><span class="badge badge-{{ $p->payment_status }}">{{ ucfirst($p->payment_status) }}</span></td>
                        <td>
                            <a href="{{ route('member.payments.show', $p) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">No payments</td></tr>
                    @endforelse
                </tbody>
            </table></div></div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .membership-progress-ring {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: conic-gradient(var(--primary) 0%, #e2e8f0 0);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .membership-progress-inner {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('.membership-progress-ring').forEach((ring) => {
        const raw = ring.getAttribute('data-progress');
        const progress = Number.parseFloat(raw);
        const value = Number.isFinite(progress) ? Math.min(100, Math.max(0, progress)) : 0;
        ring.style.background = `conic-gradient(var(--primary) ${value}%, #e2e8f0 0)`;
    });
</script>
@endpush
