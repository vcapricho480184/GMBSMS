@extends('layouts.admin')
@section('title', 'Billing')
@section('page-title', 'Billing & Transactions')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon success"><i class="bi bi-check-circle"></i></div>
            <div class="metric-info"><h3>₱{{ number_format($totalPaid, 2) }}</h3><p>Total Paid</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon warning"><i class="bi bi-clock"></i></div>
            <div class="metric-info"><h3>₱{{ number_format($totalPending, 2) }}</h3><p>Pending Payments</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon primary"><i class="bi bi-graph-up"></i></div>
            <div class="metric-info"><h3>₱{{ number_format($totalRevenue, 2) }}</h3><p>This Month Revenue</p></div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>All Transactions</h5>
    <a href="{{ route('admin.billing.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>New Transaction
    </a>
</div>

<div class="filter-bar">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Invoice # or member..." value="{{ request('search') }}" style="width:200px;">
        <select name="type" class="form-select" style="width:140px;">
            <option value="">All Types</option>
            <option value="membership" {{ request('type')=='membership'?'selected':'' }}>Membership</option>
            <option value="service" {{ request('type')=='service'?'selected':'' }}>Service</option>
            <option value="other" {{ request('type')=='other'?'selected':'' }}>Other</option>
        </select>
        <select name="status" class="form-select" style="width:140px;">
            <option value="">All Status</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
            <option value="refunded" {{ request('status')=='refunded'?'selected':'' }}>Refunded</option>
        </select>
        <select name="method" class="form-select" style="width:140px;"><option value="">All Methods</option><option value="cash" {{ request('method')=='cash'?'selected':'' }}>Cash</option><option value="card" {{ request('method')=='card'?'selected':'' }}>Card</option><option value="gcash" {{ request('method')=='gcash'?'selected':'' }}>GCash</option><option value="paymaya" {{ request('method')=='paymaya'?'selected':'' }}>PayMaya</option><option value="bank_transfer" {{ request('method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option></select>
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="width:150px;" placeholder="From">
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="width:150px;" placeholder="To">
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        @if(request()->hasAny(['search','type','status','method','date_from','date_to']))<a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">Clear</a>@endif
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th><th>Member</th><th>Type</th><th>Description</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th><th width="180">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td class="fw-medium">{{ $t->invoice_number }}</td>
                        <td>{{ $t->user->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-{{ $t->type=='membership'?'primary':($t->type=='service'?'info':'secondary') }}">
                                {{ ucfirst($t->type) }}
                            </span>
                        </td>
                        <td><small>{{ Str::limit($t->description, 40) }}</small></td>
                        <td class="fw-medium">{{ $t->formatted_amount }}</td>
                        <td>{{ $t->payment_method_label }}</td>
                        <td><span class="badge badge-{{ $t->payment_status }}">{{ ucfirst($t->payment_status) }}</span></td>
                        <td>{{ $t->payment_date ? $t->payment_date->format('M d, Y') : '—' }}</td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('admin.billing.show', $t) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.billing.invoice', $t) }}" class="btn btn-sm btn-outline-info" title="Invoice"><i class="bi bi-file-text"></i></a>
                                @if($t->payment_status == 'paid')
                                <a href="{{ route('admin.billing.receipt', $t) }}" class="btn btn-sm btn-outline-success" title="Receipt"><i class="bi bi-download"></i></a>
                                @endif
                                @if($t->payment_status == 'pending')
                                <form method="POST" action="{{ route('admin.billing.update', $t) }}" class="d-inline" onsubmit="return confirm('Mark this transaction as paid?');">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="payment_status" value="paid">
                                    <input type="hidden" name="payment_method" value="{{ $t->payment_method }}">
                                    <input type="hidden" name="payment_date" value="{{ now()->format('Y-m-d') }}">
                                    <button type="submit" class="btn btn-sm btn-success" title="Mark as Paid"><i class="bi bi-check2-circle"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="empty-state"><i class="bi bi-receipt"></i><p>No transactions found</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer">{{ $transactions->links() }}</div>
</div>
@endsection
