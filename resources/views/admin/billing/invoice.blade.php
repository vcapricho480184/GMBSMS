@extends('layouts.admin')
@section('title', 'Invoice')
@section('page-title', 'Invoice Preview')

@section('content')
@php
    $typeColors = ['membership' => 'primary', 'service' => 'info', 'mixed' => 'dark', 'other' => 'secondary'];
@endphp
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-file-text me-2"></i>{{ $billing->invoice_number }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.billing.invoice.preview', $billing) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
                        <i class="bi bi-eye me-1"></i>Preview PDF
                    </a>
                    <a href="{{ route('admin.billing.invoice.download', $billing) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-download me-1"></i>Download Invoice
                    </a>
                    @if($billing->payment_status == 'paid')
                    <a href="{{ route('admin.billing.receipt', $billing) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-receipt me-1"></i>Download Receipt
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-4">
                <!-- Invoice Header -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h3 class="text-primary mb-1">FitCore Elite</h3>
                        <p class="text-muted mb-0">Premium Fitness & Wellness Center</p>
                        <p class="text-muted mb-0">123 Fitness Street, Gym City</p>
                        <p class="text-muted mb-0">Phone: (123) 456-7890</p>
                        <p class="text-muted mb-0">Email: info@fitcoreelite.com</p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h2 class="mb-3">INVOICE</h2>
                        <p class="mb-1"><strong>Invoice #:</strong> {{ $billing->invoice_number }}</p>
                        <p class="mb-1"><strong>Date:</strong> {{ $billing->created_at->format('F d, Y') }}</p>
                        <p class="mb-1"><strong>Payment Date:</strong> {{ $billing->payment_date->format('F d, Y') }}</p>
                        @if($billing->due_date)
                        <p class="mb-1"><strong>Due Date:</strong> {{ $billing->due_date->format('F d, Y') }}</p>
                        @endif
                    </div>
                </div>

                <hr>

                <!-- Bill To -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">BILL TO:</h6>
                        <p class="mb-1"><strong>{{ $billing->user->name ?? 'N/A' }}</strong></p>
                        <p class="mb-1">{{ $billing->user->email ?? '' }}</p>
                        <p class="mb-1">{{ $billing->user->phone ?? '' }}</p>
                        <p class="mb-0">{{ $billing->user->address ?? '' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">PAYMENT STATUS:</h6>
                        <span class="badge badge-{{ $billing->payment_status }} fs-6">{{ strtoupper($billing->payment_status) }}</span>
                        @if($billing->is_overdue)
                        <span class="badge bg-danger fs-6 ms-2">OVERDUE</span>
                        @endif
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th>Type</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($billing->items->count())
                                @foreach($billing->items as $item)
                                @php
                                    $itemColor = $typeColors[$item->item_type] ?? 'secondary';
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $item->description }}</strong>
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
                            @else
                                <tr>
                                    <td>
                                        <strong>{{ $billing->description ?? 'N/A' }}</strong>
                                        @if($billing->membership && $billing->membership->membershipPlan)
                                            <br><small class="text-muted">Plan: {{ $billing->membership->membershipPlan->name }} ({{ $billing->membership->membershipPlan->duration_label }})</small>
                                            <br><small class="text-muted">Valid: {{ $billing->membership->start_date->format('M d, Y') }} - {{ $billing->membership->end_date->format('M d, Y') }}</small>
                                        @endif
                                        @if($billing->availedService && $billing->availedService->gymService)
                                            <br><small class="text-muted">Service: {{ $billing->availedService->gymService->name }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-{{ $typeColors[$billing->type] ?? 'secondary' }}">{{ ucfirst($billing->type) }}</span></td>
                                    <td class="text-end">{{ $billing->formatted_subtotal }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Totals -->
                <div class="row">
                    <div class="col-md-6">
                        @if($billing->notes)
                        <div class="alert alert-light">
                            <h6 class="mb-2">Notes:</h6>
                            <p class="mb-0">{{ $billing->notes }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <td class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end" style="width: 150px;">{{ $billing->formatted_subtotal }}</td>
                            </tr>
                            @if($billing->discount_percentage > 0)
                            <tr>
                                <td class="text-end"><strong>Discount ({{ $billing->discount_percentage }}%):</strong></td>
                                <td class="text-end text-danger">-{{ $billing->formatted_discount }}</td>
                            </tr>
                            @endif
                            @if($billing->tax_rate > 0)
                            <tr>
                                <td class="text-end"><strong>Tax ({{ $billing->tax_rate }}%):</strong></td>
                                <td class="text-end">+{{ $billing->formatted_tax }}</td>
                            </tr>
                            @endif
                            <tr class="table-light">
                                <td class="text-end"><h5 class="mb-0">Total Amount:</h5></td>
                                <td class="text-end"><h5 class="mb-0 text-primary">{{ $billing->formatted_amount }}</h5></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="mb-2">Payment Information:</h6>
                            <p class="mb-1"><strong>Method:</strong> {{ $billing->payment_method_label }}</p>
                            <p class="mb-0"><strong>Status:</strong> <span class="badge badge-{{ $billing->payment_status }}">{{ ucfirst($billing->payment_status) }}</span></p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4 pt-4 border-top">
                    <p class="text-muted mb-1">Thank you for your business!</p>
                    <p class="text-muted small mb-0">For any questions regarding this invoice, please contact us at info@fitcoreelite.com</p>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Back to Billing
            </a>
            <a href="{{ route('admin.billing.show', $billing) }}" class="btn btn-outline-primary">
                <i class="bi bi-eye me-1"></i>View Transaction
            </a>
        </div>
    </div>
</div>
@endsection
