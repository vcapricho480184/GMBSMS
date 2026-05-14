@extends('layouts.member')
@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
<div class="row justify-content-center"><div class="col-lg-8">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-receipt me-2"></i>{{ $payment->invoice_number }}</span>
            @if($payment->payment_status == 'paid')
            <a href="{{ route('member.payments.receipt', $payment) }}" class="btn btn-sm btn-primary"><i class="bi bi-download me-1"></i>Download Receipt</a>
            @endif
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Transaction Info</h6>
                    <p class="mb-1"><strong>Type:</strong> {{ ucfirst($payment->type) }}</p>
                    <p class="mb-1"><strong>Description:</strong> {{ $payment->description ?? '—' }}</p>
                    @if($payment->membership)
                        <p class="mb-0"><strong>Plan:</strong> {{ $payment->membership->membershipPlan->name ?? 'N/A' }}</p>
                    @endif
                    @if($payment->availedService)
                        <p class="mb-0"><strong>Service:</strong> {{ $payment->availedService->gymService->name ?? 'N/A' }}</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-2">Payment</h6>
                    <p class="mb-1"><strong>Amount:</strong> <span class="fs-5 fw-bold text-primary">{{ $payment->formatted_amount }}</span></p>
                    <p class="mb-1"><strong>Method:</strong> {{ $payment->payment_method_label }}</p>
                    <p class="mb-1"><strong>Date:</strong> {{ $payment->payment_date ? $payment->payment_date->format('M d, Y') : '—' }}</p>
                    <p class="mb-0"><strong>Status:</strong> <span class="badge badge-{{ $payment->payment_status }}">{{ ucfirst($payment->payment_status) }}</span></p>
                </div>
            </div>

            @if($payment->payment_status == 'pending')
            <hr class="my-4">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="alert alert-warning border-warning">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle fs-3 me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Payment Required</h5>
                                <p class="mb-0">This invoice is pending payment. Complete the transaction below to activate your service.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Process Payment</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('member.payments.process', $payment) }}">
                                @csrf
                                
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="payment_method" class="form-label fw-bold">
                                                Payment Method <span class="text-danger">*</span>
                                            </label>
                                            <select name="payment_method" id="payment_method" class="form-select form-select-lg" required>
                                                <option value="">-- Select Payment Method --</option>
                                                <option value="cash">💵 Cash</option>
                                                <option value="card">💳 Credit/Debit Card</option>
                                                <option value="gcash">📱 GCash</option>
                                                <option value="paymaya">📱 PayMaya</option>
                                                <option value="bank_transfer">🏦 Bank Transfer</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="reference_number" class="form-label fw-bold">
                                                Reference Number <span class="text-muted">(Optional)</span>
                                            </label>
                                            <input type="text" name="reference_number" id="reference_number" class="form-control form-control-lg" placeholder="e.g., TXN123456789" maxlength="100">
                                            <small class="text-muted">
                                                <i class="bi bi-info-circle me-1"></i>
                                                For online payments, enter the transaction reference number
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <strong>Amount to Pay:</strong> <span class="fs-4 text-primary">{{ $payment->formatted_amount }}</span>
                                </div>

                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="{{ route('member.payments.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="bi bi-arrow-left me-1"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-check-circle me-1"></i> Submit Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center mt-3">
                <a href="{{ route('member.payments.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Payments
                </a>
            </div>
            @endif
        </div>
    </div>
</div></div>
@endsection
