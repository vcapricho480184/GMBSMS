@extends('layouts.admin')
@section('title', 'Transaction Details')
@section('page-title', 'Transaction Details')

@section('content')
@php
    $typeColors = ['membership' => 'primary', 'service' => 'info', 'mixed' => 'dark', 'other' => 'secondary'];
    $typeColor = $typeColors[$billing->type] ?? 'secondary';
@endphp
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>{{ $billing->invoice_number }}</span>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.billing.invoice', $billing) }}" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-file-text me-1"></i>View Invoice
                    </a>
                    @if($billing->payment_status == 'paid')
                    <a href="{{ route('admin.billing.receipt', $billing) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-download me-1"></i>Download Receipt
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Member</h6>
                        <p class="fw-medium mb-1">{{ $billing->user->name ?? 'N/A' }}</p>
                        <p class="text-muted mb-0">{{ $billing->user->email ?? '' }}</p>
                        @if($billing->user->phone)
                        <p class="text-muted mb-0">{{ $billing->user->phone }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Transaction Info</h6>
                        <p class="mb-1"><strong>Type:</strong> <span class="badge bg-{{ $typeColor }}">{{ ucfirst($billing->type) }}</span></p>
                        <p class="mb-1"><strong>Description:</strong> {{ $billing->description ?? '—' }}</p>
                        @if($billing->items->count())
                            <p class="mb-0"><strong>Items:</strong> {{ $billing->items->count() }}</p>
                        @else
                            @if($billing->membership)
                                <p class="mb-0"><strong>Plan:</strong> {{ $billing->membership->membershipPlan->name ?? 'N/A' }}</p>
                            @endif
                            @if($billing->availedService)
                                <p class="mb-0"><strong>Service:</strong> {{ $billing->availedService->gymService->name ?? 'N/A' }}</p>
                            @endif
                        @endif
                    </div>

                    @if($billing->items->count())
                    <div class="col-12">
                        <h6 class="text-muted mb-2">Invoice Items</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th>Type</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($billing->items as $item)
                                    @php
                                        $itemColor = $typeColors[$item->item_type] ?? 'secondary';
                                    @endphp
                                    <tr>
                                        <td>
                                            {{ $item->description }}
                                            @if($item->membership && $item->membership->membershipPlan)
                                                <br><small class="text-muted">Plan: {{ $item->membership->membershipPlan->name }} ({{ $item->membership->membershipPlan->duration_label }})</small>
                                                <br><small class="text-muted">Valid: {{ $item->membership->start_date->format('M d, Y') }} - {{ $item->membership->end_date->format('M d, Y') }}</small>
                                            @endif
                                            @if($item->availedService && $item->availedService->gymService)
                                                <br><small class="text-muted">Service: {{ $item->availedService->gymService->name }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge bg-{{ $itemColor }}">{{ ucfirst($item->item_type) }}</span></td>
                                        <td class="text-end">₱{{ number_format($item->amount, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Amount Breakdown -->
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Amount Details</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-end">{{ $billing->formatted_subtotal }}</td>
                            </tr>
                            @if($billing->discount_percentage > 0)
                            <tr>
                                <td><strong>Discount ({{ $billing->discount_percentage }}%):</strong></td>
                                <td class="text-end text-danger">-{{ $billing->formatted_discount }}</td>
                            </tr>
                            @endif
                            @if($billing->tax_rate > 0)
                            <tr>
                                <td><strong>Tax ({{ $billing->tax_rate }}%):</strong></td>
                                <td class="text-end">+{{ $billing->formatted_tax }}</td>
                            </tr>
                            @endif
                            <tr class="border-top">
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-end"><span class="fs-5 fw-bold text-primary">{{ $billing->formatted_amount }}</span></td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Payment Information</h6>
                        <p class="mb-1"><strong>Method:</strong> {{ $billing->payment_method_label }}</p>
                        <p class="mb-1"><strong>Payment Date:</strong> {{ $billing->payment_date->format('M d, Y') }}</p>
                        @if($billing->due_date)
                        <p class="mb-1"><strong>Due Date:</strong> {{ $billing->due_date->format('M d, Y') }}</p>
                        @endif
                        <p class="mb-1"><strong>Created:</strong> {{ $billing->created_at->format('M d, Y h:i A') }}</p>
                    </div>

                    @if($billing->notes)
                    <div class="col-12">
                        <h6 class="text-muted mb-2">Notes</h6>
                        <div class="alert alert-light mb-0">
                            {{ $billing->notes }}
                        </div>
                    </div>
                    @endif

                    <div class="col-12">
                        <h6 class="text-muted mb-2">Status</h6>
                        <span class="badge badge-{{ $billing->payment_status }} fs-6">{{ ucfirst($billing->payment_status) }}</span>
                        @if($billing->is_overdue)
                        <span class="badge bg-danger fs-6 ms-2">OVERDUE</span>
                        @endif

                        <form method="POST" action="{{ route('admin.billing.update', $billing) }}" class="mt-3">
                            @csrf @method('PUT')
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label form-label-sm">Payment Status</label>
                                    <select name="payment_status" class="form-select form-select-sm">
                                        @foreach(['paid','pending','refunded'] as $s)
                                            <option value="{{ $s }}" {{ $billing->payment_status==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label form-label-sm">Payment Method</label>
                                    <select name="payment_method" class="form-select form-select-sm">
                                        <option value="cash" {{ $billing->payment_method=='cash'?'selected':'' }}>Cash</option>
                                        <option value="card" {{ $billing->payment_method=='card'?'selected':'' }}>Card</option>
                                        <option value="gcash" {{ $billing->payment_method=='gcash'?'selected':'' }}>GCash</option>
                                        <option value="paymaya" {{ $billing->payment_method=='paymaya'?'selected':'' }}>PayMaya</option>
                                        <option value="bank_transfer" {{ $billing->payment_method=='bank_transfer'?'selected':'' }}>Bank Transfer</option>
                                        <option value="other" {{ $billing->payment_method=='other'?'selected':'' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label form-label-sm">&nbsp;</label>
                                    <button type="submit" class="btn btn-sm btn-primary d-block w-100">
                                        <i class="bi bi-check-circle me-1"></i>Update
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Billing
            </a>
            @if($billing->payment_status != 'paid')
            <form method="POST" action="{{ route('admin.billing.destroy', $billing) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i>Delete
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
