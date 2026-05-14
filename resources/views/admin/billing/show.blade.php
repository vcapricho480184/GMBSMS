@extends('layouts.admin')
@section('title', 'Transaction Details')
@section('page-title', 'Transaction Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-receipt me-2"></i>{{ $billing->invoice_number }}</span>
                <a href="{{ route('admin.billing.receipt', $billing) }}" class="btn btn-sm btn-primary"><i class="bi bi-download me-1"></i>Download Receipt</a>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Member</h6>
                        <p class="fw-medium mb-1">{{ $billing->user->name ?? 'N/A' }}</p>
                        <p class="text-muted mb-0">{{ $billing->user->email ?? '' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Transaction Info</h6>
                        <p class="mb-1"><strong>Type:</strong> {{ ucfirst($billing->type) }}</p>
                        <p class="mb-1"><strong>Description:</strong> {{ $billing->description ?? '—' }}</p>
                        @if($billing->membership)
                            <p class="mb-0"><strong>Plan:</strong> {{ $billing->membership->membershipPlan->name ?? 'N/A' }}</p>
                        @endif
                        @if($billing->availedService)
                            <p class="mb-0"><strong>Service:</strong> {{ $billing->availedService->gymService->name ?? 'N/A' }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Payment</h6>
                        <p class="mb-1"><strong>Amount:</strong> <span class="fs-5 fw-bold text-primary">{{ $billing->formatted_amount }}</span></p>
                        <p class="mb-1"><strong>Method:</strong> {{ $billing->payment_method_label }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $billing->payment_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Status</h6>
                        <span class="badge badge-{{ $billing->payment_status }} fs-6">{{ ucfirst($billing->payment_status) }}</span>

                        <form method="POST" action="{{ route('admin.billing.update', $billing) }}" class="mt-3">
                            @csrf @method('PUT')
                            <div class="row g-2">
                                <div class="col-6">
                                    <select name="payment_status" class="form-select form-select-sm">
                                        @foreach(['paid','pending','failed','refunded'] as $s)
                                            <option value="{{ $s }}" {{ $billing->payment_status==$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-6">
                                    <button type="submit" class="btn btn-sm btn-outline-primary">Update Status</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
