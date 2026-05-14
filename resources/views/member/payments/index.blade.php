@extends('layouts.member')
@section('title', 'Payments')
@section('page-title', 'My Payments')

@section('content')
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon warning"><i class="bi bi-clock-history"></i></div>
            <div class="metric-info">
                <h3>₱{{ number_format($pendingTotal, 2) }}</h3>
                <p>Pending Payments</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon success"><i class="bi bi-check-circle"></i></div>
            <div class="metric-info">
                <h3>{{ $payments->where('payment_status', 'paid')->count() }}</h3>
                <p>Paid Transactions</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon primary"><i class="bi bi-receipt"></i></div>
            <div class="metric-info">
                <h3>{{ $payments->total() }}</h3>
                <p>Total Transactions</p>
            </div>
        </div>
    </div>
</div>

@if($pendingTotal > 0)
<div class="alert alert-warning mb-3 d-flex justify-content-between align-items-center">
    <div>
        <i class="bi bi-exclamation-triangle me-2"></i>
        You have <strong>₱{{ number_format($pendingTotal, 2) }}</strong> in pending payments. Process them now to avoid service interruption.
    </div>
    <button class="btn btn-warning btn-sm" onclick="scrollToPending()">
        <i class="bi bi-arrow-down"></i> View Pending
    </button>
</div>
@endif

<!-- Pending Payments Section -->
@if($payments->where('payment_status', 'pending')->count() > 0)
<div class="card mb-3" id="pending-payments">
    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-exclamation-circle me-2"></i>
            <strong>Pending Payments - Action Required</strong>
        </div>
        <button type="button" class="btn btn-dark btn-sm" id="processPaymentsBtn">
            <i class="bi bi-credit-card me-1"></i> Process Selected Payments
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th width="40">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th>Invoice</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Due Date</th>
                        <th width="80">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments->where('payment_status', 'pending') as $p)
                    <tr class="table-warning">
                        <td>
                            <input type="checkbox" class="form-check-input payment-checkbox" value="{{ $p->id }}" data-amount="{{ $p->amount }}">
                        </td>
                        <td class="fw-medium">{{ $p->invoice_number }}</td>
                        <td>{{ $p->description ?? '—' }}</td>
                        <td class="fw-bold text-danger">{{ $p->formatted_amount }}</td>
                        <td>{{ $p->created_at->format('M d, Y') }}</td>
                        <td>
                            <a href="{{ route('member.payments.show', $p) }}" class="btn btn-sm btn-outline-secondary" title="View Details">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Payment Processing Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="bi bi-credit-card me-2"></i>Process Payments
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('member.payments.process-multiple') }}" id="paymentForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Selected Payments:</strong> <span id="selectedCount">0</span><br>
                        <strong>Total Amount:</strong> <span id="totalAmount" class="fs-5 text-primary">₱0.00</span>
                    </div>

                    <input type="hidden" name="payment_ids" id="paymentIds">

                    <div class="mb-3">
                        <label for="payment_method" class="form-label fw-bold">
                            Payment Method <span class="text-danger">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="">-- Select Payment Method --</option>
                            <option value="cash">💵 Cash</option>
                            <option value="card">💳 Credit/Debit Card</option>
                            <option value="gcash">📱 GCash</option>
                            <option value="paymaya">📱 PayMaya</option>
                            <option value="bank_transfer">🏦 Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="reference_number" class="form-label fw-bold">
                            Reference Number <span class="text-muted">(Optional)</span>
                        </label>
                        <input type="text" name="reference_number" id="reference_number" class="form-control" placeholder="e.g., TXN123456789" maxlength="100">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            For online payments, enter the transaction reference number
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Submit Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- All Payments History -->
<div class="card">
    <div class="card-header">
        <i class="bi bi-clock-history me-2"></i>
        <strong>Payment History</strong>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr>
                        <td class="fw-medium">{{ $p->invoice_number }}</td>
                        <td>{{ $p->description ?? '—' }}</td>
                        <td class="fw-medium">{{ $p->formatted_amount }}</td>
                        <td>{{ $p->payment_method_label }}</td>
                        <td><span class="badge badge-{{ $p->payment_status }}">{{ ucfirst($p->payment_status) }}</span></td>
                        <td>{{ $p->payment_date ? $p->payment_date->format('M d, Y') : '—' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('member.payments.show', $p) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($p->payment_status == 'paid')
                                <a href="{{ route('member.payments.receipt', $p) }}" class="btn btn-sm btn-outline-secondary" title="Download Receipt">
                                    <i class="bi bi-download"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-receipt"></i>
                            <p>No payment history</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $payments->links() }}</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.payment-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');
    const processBtn = document.getElementById('processPaymentsBtn');
    const paymentModalElement = document.getElementById('paymentModal');
    const selectedCountSpan = document.getElementById('selectedCount');
    const totalAmountSpan = document.getElementById('totalAmount');
    const paymentIdsInput = document.getElementById('paymentIds');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // Initialize Bootstrap modal only if element exists
    let paymentModal = null;
    if (paymentModalElement) {
        paymentModal = new bootstrap.Modal(paymentModalElement, {
            backdrop: 'static',
            keyboard: false
        });
    }

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updatePaymentSummary();
        });
    }

    // Individual checkbox change
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updatePaymentSummary();
            // Update select all checkbox
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = Array.from(checkboxes).every(c => c.checked);
            }
        });
    });

    // Process payments button
    if (processBtn) {
        processBtn.addEventListener('click', function() {
            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (selectedIds.length === 0) {
                alert('Please select at least one payment to process.');
                return;
            }

            // Set payment IDs in hidden input
            if (paymentIdsInput) {
                paymentIdsInput.value = selectedIds.join(',');
            }
            
            // Hide sidebar overlay if visible
            if (sidebarOverlay) {
                sidebarOverlay.style.display = 'none';
                sidebarOverlay.classList.remove('show');
            }
            
            // Show modal
            if (paymentModal) {
                paymentModal.show();
            }
        });
    }

    function updatePaymentSummary() {
        const selected = Array.from(checkboxes).filter(cb => cb.checked);
        const count = selected.length;
        const total = selected.reduce((sum, cb) => sum + parseFloat(cb.dataset.amount), 0);

        if (selectedCountSpan) {
            selectedCountSpan.textContent = count;
        }
        if (totalAmountSpan) {
            totalAmountSpan.textContent = '₱' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }
    }

    // Initialize summary
    updatePaymentSummary();

    // Handle modal show event
    if (paymentModalElement) {
        paymentModalElement.addEventListener('show.bs.modal', function () {
            // Ensure sidebar overlay is hidden
            if (sidebarOverlay) {
                sidebarOverlay.style.display = 'none';
                sidebarOverlay.classList.remove('show');
            }
            // Force modal to be on top
            setTimeout(() => {
                paymentModalElement.style.zIndex = '9999';
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.style.zIndex = '9998';
                }
            }, 10);
        });

        paymentModalElement.addEventListener('hidden.bs.modal', function () {
            // Reset form when modal is closed
            const form = document.getElementById('paymentForm');
            if (form) {
                form.reset();
            }
        });
    }
});

// Scroll to pending payments section
function scrollToPending() {
    const pendingSection = document.getElementById('pending-payments');
    if (pendingSection) {
        pendingSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>
@endpush
