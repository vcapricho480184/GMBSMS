@extends('layouts.member')
@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('content')
@php
    $typeColors = ['membership' => 'primary', 'service' => 'info', 'mixed' => 'dark', 'other' => 'secondary'];
    $typeColor = $typeColors[$payment->type] ?? 'secondary';
@endphp
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
                    <p class="mb-1"><strong>Type:</strong> <span class="badge bg-{{ $typeColor }}">{{ ucfirst($payment->type) }}</span></p>
                    <p class="mb-1"><strong>Description:</strong> {{ $payment->description ?? '—' }}</p>
                    @if($payment->items->count())
                        <p class="mb-0"><strong>Items:</strong> {{ $payment->items->count() }}</p>
                    @else
                        @if($payment->membership)
                            <p class="mb-0"><strong>Plan:</strong> {{ $payment->membership->membershipPlan->name ?? 'N/A' }}</p>
                        @endif
                        @if($payment->availedService)
                            <p class="mb-0"><strong>Service:</strong> {{ $payment->availedService->gymService->name ?? 'N/A' }}</p>
                        @endif
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

            @if($payment->items->count())
            <hr class="my-4">
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
                        @foreach($payment->items as $item)
                        @php
                            $itemColor = $typeColors[$item->item_type] ?? 'secondary';
                        @endphp
                        <tr>
                            <td>
                                {{ $item->description }}
                                @if($item->membership && $item->membership->membershipPlan)
                                    <br><small class="text-muted">Plan: {{ $item->membership->membershipPlan->name }} ({{ $item->membership->membershipPlan->duration_label }})</small>
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
            @endif

            @if($payment->payment_status == 'pending')
            <hr class="my-4">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="alert alert-warning border-warning">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle fs-3 me-3"></i>
                            <div>
                                <h5 class="alert-heading mb-1">Payment Pending</h5>
                                <p class="mb-0">This invoice is pending. Payments are handled by staff and this page updates when the status changes.</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('member.payments.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to Payments
                        </a>
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
