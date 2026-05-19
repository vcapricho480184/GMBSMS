@extends('layouts.member')
@section('title', 'Payments')
@section('page-title', 'My Payments')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon warning"><i class="bi bi-clock-history"></i></div>
            <div class="metric-info">
                <h3>₱{{ number_format($pendingTotal, 2) }}</h3>
                <p>Pending Payments</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon success"><i class="bi bi-check-circle"></i></div>
            <div class="metric-info">
                <h3>{{ $paidPayments->total() }}</h3>
                <p>Paid Transactions</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon primary"><i class="bi bi-receipt"></i></div>
            <div class="metric-info">
                <h3>{{ $pendingPayments->count() + $paidPayments->total() }}</h3>
                <p>Total Transactions</p>
            </div>
        </div>
    </div>
</div>

@if($pendingTotal > 0)
<div class="alert alert-warning mb-3 d-flex justify-content-between align-items-center">
    <div>
        <i class="bi bi-exclamation-triangle me-2"></i>
        You have <strong>₱{{ number_format($pendingTotal, 2) }}</strong> in pending payments. This page is for information only and updates when your status changes.
    </div>
    <a class="btn btn-warning btn-sm" href="#pending-payments">
        <i class="bi bi-arrow-down"></i> View Pending
    </a>
</div>
@endif

<!-- Pending Payments Section -->
@if($pendingPayments->count() > 0)
<div class="card mb-3" id="pending-payments">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong>Pending Payments</strong>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingPayments as $p)
                    <tr class="table-warning">
                        <td class="fw-medium">{{ $p->invoice_number }}</td>
                        <td>{{ $p->description ?? '—' }}</td>
                        <td class="fw-bold text-danger">{{ $p->formatted_amount }}</td>
                        <td>{{ $p->payment_date ? $p->payment_date->format('M d, Y') : $p->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('member.payments.show', $p) }}" class="btn btn-sm btn-outline-secondary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- All Payments History -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-clock-history me-2"></i>
        <strong>Payment History</strong>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($paidPayments as $p)
                    <tr>
                        <td class="fw-medium">{{ $p->invoice_number }}</td>
                        <td>{{ $p->description ?? '—' }}</td>
                        <td class="fw-medium">{{ $p->formatted_amount }}</td>
                        <td>{{ $p->payment_method_label }}</td>
                        <td><span class="badge badge-{{ $p->payment_status }}">{{ ucfirst($p->payment_status) }}</span></td>
                        <td>{{ $p->payment_date ? $p->payment_date->format('M d, Y') : '—' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('member.payments.show', $p) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($p->payment_status == 'paid')
                                <a href="{{ route('member.payments.receipt', $p) }}" class="btn btn-sm btn-outline-secondary" title="Download Receipt">
                                    <i class="bi bi-download"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-receipt"></i>
                            <p>No payment history</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $paidPayments->links() }}</div>
@endsection
