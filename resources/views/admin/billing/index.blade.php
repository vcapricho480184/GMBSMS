@extends('layouts.admin')
@section('title', 'Billing')
@section('page-title', 'Billing & Transactions')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="metric-card">
            <div class="metric-icon success"><i class="bi bi-check-circle"></i></div>
            <div class="metric-info"><h3>₱{{ number_format($totalPaid, 2) }}</h3><p>Total Paid</p></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="metric-card">
            <div class="metric-icon warning"><i class="bi bi-clock"></i></div>
            <div class="metric-info"><h3>₱{{ number_format($totalPending, 2) }}</h3><p>Pending Payments</p></div>
        </div>
    </div>
</div>

<div class="filter-bar">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Invoice # or member..." value="{{ request('search') }}" style="width:200px;">
        <select name="status" class="form-select" style="width:140px;"><option value="">All Status</option><option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option><option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option><option value="failed" {{ request('status')=='failed'?'selected':'' }}>Failed</option><option value="refunded" {{ request('status')=='refunded'?'selected':'' }}>Refunded</option></select>
        <select name="type" class="form-select" style="width:140px;"><option value="">All Types</option><option value="membership" {{ request('type')=='membership'?'selected':'' }}>Membership</option><option value="service" {{ request('type')=='service'?'selected':'' }}>Service</option></select>
        <select name="method" class="form-select" style="width:140px;"><option value="">All Methods</option><option value="cash" {{ request('method')=='cash'?'selected':'' }}>Cash</option><option value="card" {{ request('method')=='card'?'selected':'' }}>Card</option><option value="gcash" {{ request('method')=='gcash'?'selected':'' }}>GCash</option><option value="paymaya" {{ request('method')=='paymaya'?'selected':'' }}>PayMaya</option><option value="bank_transfer" {{ request('method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option></select>
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="width:150px;" placeholder="From">
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="width:150px;" placeholder="To">
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        @if(request()->hasAny(['search','status','type','method','date_from','date_to']))<a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">Clear</a>@endif
    </form>
</div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table">
        <thead><tr><th>Invoice</th><th>Member</th><th>Type</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th><th width="100">Actions</th></tr></thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td class="fw-medium">{{ $t->invoice_number }}</td>
                <td>{{ $t->user->name ?? 'N/A' }}</td>
                <td><span class="badge bg-{{ $t->type=='membership'?'primary':'secondary' }}">{{ ucfirst($t->type) }}</span></td>
                <td class="fw-medium">{{ $t->formatted_amount }}</td>
                <td>{{ $t->payment_method_label }}</td>
                <td><span class="badge badge-{{ $t->payment_status }}">{{ ucfirst($t->payment_status) }}</span></td>
                <td>{{ $t->payment_date ? $t->payment_date->format('M d, Y') : '—' }}</td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="{{ route('admin.billing.show', $t) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                        @if($t->payment_status == 'paid')
                        <a href="{{ route('admin.billing.receipt', $t) }}" class="btn btn-sm btn-outline-secondary" title="Receipt"><i class="bi bi-download"></i></a>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="empty-state"><i class="bi bi-receipt"></i><p>No transactions found</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div></div></div>
<div class="mt-3">{{ $transactions->links() }}</div>
@endsection
