@extends('layouts.admin')
@section('title', 'Billing')
@section('page-title', 'Billing & Invoices')

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
    <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Billing Transactions</h5>
    <a href="{{ route('admin.billing.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Create Invoice
    </a>
</div>

<div class="filter-bar">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Invoice # or member..." value="{{ request('search') }}" style="width:200px;">
        <select name="type" class="form-select" style="width:140px;">
            <option value="">All Types</option>
            <option value="membership" {{ request('type')=='membership'?'selected':'' }}>Membership</option>
            <option value="service" {{ request('type')=='service'?'selected':'' }}>Service</option>
            <option value="mixed" {{ request('type')=='mixed'?'selected':'' }}>Mixed</option>
            <option value="other" {{ request('type')=='other'?'selected':'' }}>Other</option>
        </select>
        <select name="method" class="form-select" style="width:140px;"><option value="">All Methods</option><option value="cash" {{ request('method')=='cash'?'selected':'' }}>Cash</option><option value="card" {{ request('method')=='card'?'selected':'' }}>Card</option><option value="gcash" {{ request('method')=='gcash'?'selected':'' }}>GCash</option><option value="paymaya" {{ request('method')=='paymaya'?'selected':'' }}>PayMaya</option><option value="bank_transfer" {{ request('method')=='bank_transfer'?'selected':'' }}>Bank Transfer</option></select>
        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" style="width:150px;" placeholder="From">
        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" style="width:150px;" placeholder="To">
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        @if(request()->hasAny(['search','type','method','date_from','date_to']))<a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">Clear</a>@endif
    </form>
</div>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-warning">
            <div class="card-header bg-warning-subtle d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 text-warning-emphasis"><i class="bi bi-clock me-2"></i>Pending Transactions</h6>
                    <small class="text-muted">Invoices waiting for payment</small>
                </div>
                <span class="badge bg-warning text-dark">{{ $pendingTransactions->total() }} records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th><th>Member</th><th>Type</th><th>Amount</th><th>Method</th><th>Date</th><th width="180">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingTransactions as $t)
                            <tr class="table-warning">
                                <td class="fw-medium">{{ $t->invoice_number }}</td>
                                <td>{{ $t->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $t->type=='membership'?'primary':($t->type=='service'?'info':($t->type=='mixed'?'dark':'secondary')) }}">
                                        {{ ucfirst($t->type) }}
                                    </span>
                                </td>
                                <td class="fw-medium">{{ $t->formatted_amount }}</td>
                                <td>{{ $t->payment_method_label }}</td>
                                <td>{{ $t->payment_date ? $t->payment_date->format('M d, Y') : '—' }}</td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="{{ route('admin.billing.show', $t) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.billing.invoice', $t) }}" class="btn btn-sm btn-outline-info" title="Invoice"><i class="bi bi-file-text"></i></a>
                                        <form method="POST" action="{{ route('admin.billing.update', $t) }}" class="d-inline" onsubmit="return confirm('Mark this transaction as paid?');">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="payment_status" value="paid">
                                            <input type="hidden" name="payment_method" value="{{ $t->payment_method }}">
                                            <input type="hidden" name="payment_date" value="{{ now()->format('Y-m-d') }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Mark as Paid">
                                                <i class="bi bi-check2-circle"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="empty-state"><i class="bi bi-clock"></i><p>No pending transactions</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">{{ $pendingTransactions->links() }}</div>
        </div>
    </div>

    <div class="col-12">
        <div class="card border-success">
            <div class="card-header bg-success-subtle d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 text-success-emphasis"><i class="bi bi-check-circle me-2"></i>Paid Transactions</h6>
                    <small class="text-muted">Completed and receipted invoices</small>
                </div>
                <span class="badge bg-success">{{ $paidTransactions->total() }} records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th><th>Member</th><th>Type</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th><th width="140">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paidTransactions as $t)
                            <tr>
                                <td class="fw-medium">{{ $t->invoice_number }}</td>
                                <td>{{ $t->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $t->type=='membership'?'primary':($t->type=='service'?'info':($t->type=='mixed'?'dark':'secondary')) }}">
                                        {{ ucfirst($t->type) }}
                                    </span>
                                </td>
                                <td class="fw-medium">{{ $t->formatted_amount }}</td>
                                <td>{{ $t->payment_method_label }}</td>
                                <td><span class="badge badge-{{ $t->payment_status }}">{{ ucfirst($t->payment_status) }}</span></td>
                                <td>{{ $t->payment_date ? $t->payment_date->format('M d, Y') : '—' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.billing.show', $t) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.billing.invoice', $t) }}" class="btn btn-sm btn-outline-info" title="Invoice"><i class="bi bi-file-text"></i></a>
                                        <a href="{{ route('admin.billing.receipt', $t) }}" class="btn btn-sm btn-outline-success" title="Receipt"><i class="bi bi-download"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="empty-state"><i class="bi bi-check-circle"></i><p>No paid transactions</p></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">{{ $paidTransactions->links() }}</div>
        </div>
    </div>

    @if($otherTransactions->count())
    <div class="col-12">
        <div class="card border-secondary">
            <div class="card-header bg-secondary-subtle d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0 text-secondary-emphasis"><i class="bi bi-info-circle me-2"></i>Other Status Records</h6>
                    <small class="text-muted">Legacy or non-standard payment statuses</small>
                </div>
                <span class="badge bg-secondary">{{ $otherTransactions->count() }} records</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Invoice</th><th>Member</th><th>Type</th><th>Amount</th><th>Status</th><th>Date</th><th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($otherTransactions as $t)
                            <tr>
                                <td class="fw-medium">{{ $t->invoice_number }}</td>
                                <td>{{ $t->user->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-{{ $t->type=='membership'?'primary':($t->type=='service'?'info':($t->type=='mixed'?'dark':'secondary')) }}">{{ ucfirst($t->type) }}</span></td>
                                <td class="fw-medium">{{ $t->formatted_amount }}</td>
                                <td><span class="badge badge-{{ $t->payment_status }}">{{ ucfirst($t->payment_status) }}</span></td>
                                <td>{{ $t->payment_date ? $t->payment_date->format('M d, Y') : '—' }}</td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('admin.billing.show', $t) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('admin.billing.invoice', $t) }}" class="btn btn-sm btn-outline-info" title="Invoice"><i class="bi bi-file-text"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
