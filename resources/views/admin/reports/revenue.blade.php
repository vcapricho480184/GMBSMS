@extends('layouts.admin')
@section('title', 'Revenue Report')
@section('page-title', 'Revenue Report')

@section('content')
<div class="filter-bar d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="date" name="date_from" class="form-control" value="{{ $from }}" style="width:170px;">
        <input type="date" name="date_to" class="form-control" value="{{ $to }}" style="width:170px;">
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Generate</button>
    </form>
    <a href="{{ route('admin.reports.revenue', ['date_from' => $from, 'date_to' => $to, 'export' => 'pdf']) }}" class="btn btn-outline-danger"><i class="bi bi-file-pdf me-1"></i>Export PDF</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-4"><h6 class="text-muted">Total Revenue</h6><h3 class="text-primary">₱{{ number_format($total, 2) }}</h3></div>
            <div class="col-md-4"><h6 class="text-muted">Transactions</h6><h3>{{ $transactions->count() }}</h3></div>
            <div class="col-md-4"><h6 class="text-muted">Period</h6><h6>{{ \Carbon\Carbon::parse($from)->format('M d') }} — {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</h6></div>
        </div>
    </div>
</div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table">
        <thead><tr><th>Invoice</th><th>Member</th><th>Description</th><th>Amount</th><th>Method</th><th>Date</th></tr></thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td class="fw-medium">{{ $t->invoice_number }}</td>
                <td>{{ $t->user->name ?? 'N/A' }}</td>
                <td>{{ $t->description }}</td>
                <td class="fw-medium">{{ $t->formatted_amount }}</td>
                <td>{{ $t->payment_method_label }}</td>
                <td>{{ $t->payment_date->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center text-muted py-3">No transactions in this period</td></tr>
            @endforelse
        </tbody>
    </table>
</div></div></div>
@endsection
