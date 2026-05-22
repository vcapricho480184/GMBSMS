@extends('layouts.admin')
@section('title', 'Transaction Details')
@section('page-title', 'Transaction Details')

@section('content')
@php
    $typeColors = ['membership' => 'primary', 'service' => 'info', 'other' => 'secondary'];
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
                        @if($billing->user->phone ?? false)
                        <p class="text-muted mb-0">{{ $billing->user->phone }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Transaction Info</h6>
                        <p class="mb-1"><strong>Type:</strong> <span class="badge bg-{{ $typeColor }}">{{ ucfirst($billing->type) }}</span></p>
                        <p class="mb-1"><strong>Description:</strong> {{ $billing->description ?? '—' }}</p>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Amount</h6>
                        <span class="fs-4 fw-bold text-primary">{{ $billing->formatted_amount }}</span>
                    </div>

                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Payment Information</h6>
                        <p class="mb-1"><strong>Method:</strong> {{ $billing->payment_method_label }}</p>
                        <p class="mb-1"><strong>Payment Date:</strong> {{ $billing->payment_date->format('M d, Y') }}</p>
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
