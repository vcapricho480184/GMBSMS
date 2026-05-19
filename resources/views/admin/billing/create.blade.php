@extends('layouts.admin')
@section('title', 'Create Invoice')
@section('page-title', 'Create New Invoice')

@section('content')
@php
    $oldMembershipIds = collect(old('membership_ids', []))->map(fn($value) => (int) $value)->all();
    $oldServiceIds = collect(old('service_ids', []))->map(fn($value) => (int) $value)->all();
    $oldCustomItems = old('custom_items', []);
    $selectedMemberId = old('user_id');

    if (empty($oldCustomItems)) {
        $oldCustomItems = [['description' => '', 'amount' => '']];
    }
@endphp
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h5 class="mb-1"><i class="bi bi-receipt me-2"></i>Create a Transaction Invoice</h5>
                    <p class="text-muted mb-0">Pick the member, choose billable memberships and approved services, then review the generated breakdown before saving.</p>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">Auto-generated summary</span>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.billing.store') }}" id="invoiceForm">
            @csrf

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Member</h6>
                    <small class="text-muted">Select one member to filter billable items</small>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Member <span class="text-danger">*</span></label>
                            <select name="user_id" id="memberSelect" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Select Member</option>
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}" {{ (string) old('user_id') === (string) $member->id ? 'selected' : '' }}>
                                        {{ $member->name }} ({{ $member->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <div class="small text-muted">Only active memberships and approved services that have not been billed yet will appear below.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0">Current Payments</h6>
                        <small class="text-muted">Select the member's current billable memberships and approved services</small>
                    </div>
                    <div class="form-check mb-0">
                        <input type="checkbox" class="form-check-input" id="selectAllPayments">
                        <label class="form-check-label" for="selectAllPayments">Select all</label>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 48px;"></th>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th class="text-end" style="width: 140px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody id="paymentTable">
                                @foreach($memberships as $membership)
                                <tr class="payment-row" data-user-id="{{ $membership->user_id }}">
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="membership_ids[]"
                                            value="{{ $membership->id }}"
                                            class="form-check-input billable-item current-payment-item"
                                            data-amount="{{ $membership->membershipPlan->price }}"
                                            data-type="membership"
                                            data-label="{{ $membership->membershipPlan->name }}"
                                            data-details="{{ $membership->membershipPlan->duration_label }}"
                                            {{ in_array($membership->id, $oldMembershipIds, true) ? 'checked' : '' }}
                                        >
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $membership->membershipPlan->name }}</div>
                                        <div class="text-muted small">{{ $membership->user->name }} | {{ $membership->membershipPlan->duration_label }}</div>
                                        <div class="text-muted small">Valid: {{ $membership->start_date->format('M d, Y') }} - {{ $membership->end_date->format('M d, Y') }}</div>
                                    </td>
                                    <td><span class="badge bg-primary-subtle text-primary">Membership</span></td>
                                    <td class="text-end">₱{{ number_format($membership->membershipPlan->price, 2) }}</td>
                                </tr>
                                @endforeach

                                @foreach($services as $service)
                                <tr class="payment-row" data-user-id="{{ $service->user_id }}">
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="service_ids[]"
                                            value="{{ $service->id }}"
                                            class="form-check-input billable-item current-payment-item"
                                            data-amount="{{ $service->gymService->price }}"
                                            data-type="service"
                                            data-label="{{ $service->gymService->name }}"
                                            data-details="Availed {{ $service->availed_date ? $service->availed_date->format('M d, Y') : 'N/A' }}"
                                            {{ in_array($service->id, $oldServiceIds, true) ? 'checked' : '' }}
                                        >
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $service->gymService->name }}</div>
                                        <div class="text-muted small">{{ $service->user->name }} | Availed: {{ $service->availed_date ? $service->availed_date->format('M d, Y') : 'N/A' }}</div>
                                    </td>
                                    <td><span class="badge bg-info text-dark">Service</span></td>
                                    <td class="text-end">₱{{ number_format($service->gymService->price, 2) }}</td>
                                </tr>
                                @endforeach

                                @if($memberships->isEmpty() && $services->isEmpty())
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No current payments available for the selected member.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div id="paymentEmpty" class="text-muted small px-3 py-3 d-none">Select a member to load current payments.</div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Custom Items</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addCustomItem">
                        <i class="bi bi-plus-circle me-1"></i>Add Item
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0" id="customItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th style="width: 160px;">Amount</th>
                                    <th style="width: 100px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($oldCustomItems as $index => $customItem)
                                <tr>
                                    <td>
                                        <input type="text" name="custom_items[{{ $index }}][description]" class="form-control form-control-sm custom-desc" value="{{ $customItem['description'] ?? '' }}" placeholder="Item description">
                                    </td>
                                    <td>
                                        <input type="number" name="custom_items[{{ $index }}][amount]" class="form-control form-control-sm custom-amount" step="0.01" min="0" value="{{ $customItem['amount'] ?? '' }}" placeholder="0.00">
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-custom">Remove</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Payment and Totals</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Invoice Summary</label>
                            <textarea name="description" id="invoiceDescription" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Auto-generated from selected items" readonly>{{ old('description') }}</textarea>
                            <div class="form-text">This summary updates automatically from the selected memberships, services, and custom items.</div>
                            @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Discount (%)</label>
                            <input type="number" name="discount" id="discount" step="0.01" min="0" max="100" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount', 0) }}">
                            @error('discount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" id="tax_rate" step="0.01" min="0" max="100" class="form-control @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate', 0) }}">
                            @error('tax_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                <option value="">Select Method</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                <option value="gcash" {{ old('payment_method') == 'gcash' ? 'selected' : '' }}>GCash</option>
                                <option value="paymaya" {{ old('payment_method') == 'paymaya' ? 'selected' : '' }}>PayMaya</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Payment Status <span class="text-danger">*</span></label>
                            <select name="payment_status" class="form-select @error('payment_status') is-invalid @enderror" required>
                                <option value="pending" {{ old('payment_status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ old('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                            </select>
                            <div class="form-text">Choose Pending if the invoice is due later, or Paid if the payment is collected now.</div>
                            @error('payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notes (Optional)</label>
                            <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                                    <div>
                                        <h6 class="mb-1">Live Amount Breakdown</h6>
                                        <div class="text-muted small">This reflects the selected items before the invoice is finalized.</div>
                                    </div>
                                    <span class="badge bg-info text-dark" id="selectedCountBadge">0 items selected</span>
                                </div>

                                <div id="selectedItemSummary" class="mb-3 text-muted small">No items selected yet.</div>

                                <div class="d-flex justify-content-between mb-1">
                                    <span>Subtotal:</span>
                                    <strong id="previewSubtotal">₱0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Discount:</span>
                                    <strong id="previewDiscount" class="text-danger">-₱0.00</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span>Tax:</span>
                                    <strong id="previewTax">+₱0.00</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5">Total Amount:</span>
                                    <strong id="previewTotal" class="fs-5 text-primary">₱0.00</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Create Invoice
                </button>
                <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-lg-top" style="top: 1.25rem;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Invoice Preview</h6>
                <span class="badge bg-dark" id="previewTypeBadge">Draft</span>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-muted small mb-1">Selected member</div>
                    <div class="fw-medium" id="previewMember">No member selected</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-2">Description</div>
                    <div class="border rounded-3 bg-light p-3 small" id="previewDescription">Auto-generated summary will appear here.</div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-2">Selected items</div>
                    <div id="previewItems" class="d-grid gap-2">
                        <div class="text-muted small">Nothing selected yet.</div>
                    </div>
                </div>

                <div class="bg-light rounded-3 p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong id="sidebarSubtotal">₱0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Discount</span>
                        <strong id="sidebarDiscount" class="text-danger">-₱0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-0">
                        <span>Tax</span>
                        <strong id="sidebarTax">+₱0.00</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-semibold">Total</span>
                        <strong class="fs-4 text-primary" id="sidebarTotal">₱0.00</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const memberSelect = document.getElementById('memberSelect');
    const discountInput = document.getElementById('discount');
    const taxRateInput = document.getElementById('tax_rate');
    const customItemsTable = document.getElementById('customItemsTable');
    const selectAllPayments = document.getElementById('selectAllPayments');
    const paymentEmpty = document.getElementById('paymentEmpty');
    const invoiceDescription = document.getElementById('invoiceDescription');
    const selectedItemSummary = document.getElementById('selectedItemSummary');
    const selectedCountBadge = document.getElementById('selectedCountBadge');
    const previewMember = document.getElementById('previewMember');
    const previewDescription = document.getElementById('previewDescription');
    const previewItems = document.getElementById('previewItems');
    const previewTypeBadge = document.getElementById('previewTypeBadge');
    const sidebarSubtotal = document.getElementById('sidebarSubtotal');
    const sidebarDiscount = document.getElementById('sidebarDiscount');
    const sidebarTax = document.getElementById('sidebarTax');
    const sidebarTotal = document.getElementById('sidebarTotal');

    let customIndex = customItemsTable.querySelectorAll('tbody tr').length;

    function visibleRows(selector) {
        return Array.from(document.querySelectorAll(selector)).filter(row => !row.classList.contains('d-none'));
    }

    function filterRows() {
        const selectedMember = memberSelect.value;
        document.querySelectorAll('.payment-row').forEach(row => {
            const rowUserId = (row.getAttribute('data-user-id') || '').trim();
            const shouldShow = !!selectedMember && rowUserId == (selectedMember || '').toString().trim();
            row.classList.toggle('d-none', !shouldShow);

            const checkbox = row.querySelector('.billable-item');
            if (checkbox) {
                checkbox.disabled = !shouldShow;
                if (!shouldShow) {
                    checkbox.checked = false;
                }
            }
        });

        const hasPaymentRows = visibleRows('#paymentTable tr.payment-row').length > 0;

        paymentEmpty.textContent = selectedMember
            ? 'No current payments for the selected member.'
            : 'Select a member to load current payments.';
        paymentEmpty.classList.toggle('d-none', hasPaymentRows);

        selectAllPayments.checked = false;
        selectAllPayments.disabled = !selectedMember || !hasPaymentRows;
        updateInvoicePreview();
    }

    function getSelectedItems() {
        const items = [];

        document.querySelectorAll('.billable-item:checked').forEach(item => {
            items.push({
                type: item.dataset.type || 'other',
                label: item.dataset.label || 'Item',
                details: item.dataset.details || '',
                amount: parseFloat(item.getAttribute('data-amount')) || 0,
            });
        });

        document.querySelectorAll('#customItemsTable tbody tr').forEach(row => {
            const descriptionInput = row.querySelector('.custom-desc');
            const amountInput = row.querySelector('.custom-amount');
            const label = descriptionInput ? descriptionInput.value.trim() : '';
            const amount = amountInput ? parseFloat(amountInput.value) : NaN;

            if (label && !Number.isNaN(amount)) {
                items.push({
                    type: 'other',
                    label,
                    details: 'Custom item',
                    amount,
                });
            }
        });

        return items;
    }

    function buildDescription(items) {
        if (!items.length) {
            return '';
        }

        const grouped = items.reduce((accumulator, item) => {
            const key = item.type || 'other';
            if (!accumulator[key]) {
                accumulator[key] = [];
            }
            accumulator[key].push(item.label);
            return accumulator;
        }, {});

        const order = ['membership', 'service', 'other'];
        const labels = {
            membership: 'Memberships',
            service: 'Approved Services',
            other: 'Custom Items',
        };

        return order
            .filter(type => grouped[type] && grouped[type].length)
            .map(type => `${labels[type]}: ${grouped[type].join(', ')}`)
            .join(' | ');
    }

    function calculateTotal(items) {
        const subtotal = items.reduce((total, item) => total + item.amount, 0);
        const discount = parseFloat(discountInput.value) || 0;
        const taxRate = parseFloat(taxRateInput.value) || 0;

        const discountAmount = subtotal * (discount / 100);
        const afterDiscount = subtotal - discountAmount;
        const taxAmount = afterDiscount * (taxRate / 100);
        const total = afterDiscount + taxAmount;

        document.getElementById('previewSubtotal').textContent = '₱' + subtotal.toFixed(2);
        document.getElementById('previewDiscount').textContent = '-₱' + discountAmount.toFixed(2);
        document.getElementById('previewTax').textContent = '+₱' + taxAmount.toFixed(2);
        document.getElementById('previewTotal').textContent = '₱' + total.toFixed(2);

        sidebarSubtotal.textContent = '₱' + subtotal.toFixed(2);
        sidebarDiscount.textContent = '-₱' + discountAmount.toFixed(2);
        sidebarTax.textContent = '+₱' + taxAmount.toFixed(2);
        sidebarTotal.textContent = '₱' + total.toFixed(2);

        return { subtotal, discountAmount, taxAmount, total };
    }

    function addCustomItemRow() {
        const tbody = customItemsTable.querySelector('tbody');
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>
                <input type="text" name="custom_items[${customIndex}][description]" class="form-control form-control-sm custom-desc" placeholder="Item description">
            </td>
            <td>
                <input type="number" name="custom_items[${customIndex}][amount]" class="form-control form-control-sm custom-amount" step="0.01" min="0" placeholder="0.00">
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger remove-custom">Remove</button>
            </td>
        `;

        tbody.appendChild(row);
        customIndex += 1;
    }

    function renderSelectedItems(items) {
        const count = items.length;
        selectedCountBadge.textContent = count + (count === 1 ? ' item selected' : ' items selected');

        if (!count) {
            selectedItemSummary.textContent = 'No items selected yet.';
            previewDescription.textContent = 'Auto-generated summary will appear here.';
            previewItems.innerHTML = '<div class="text-muted small">Nothing selected yet.</div>';
            previewTypeBadge.textContent = 'Draft';
            previewTypeBadge.className = 'badge bg-dark';
            invoiceDescription.value = '';
            return;
        }

        const formattedItems = items.map(item => {
            return `
                <div class="border rounded-3 p-2 bg-white d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <div class="fw-medium">${item.label}</div>
                        <div class="text-muted small">${item.details || 'Billable item'}</div>
                    </div>
                    <strong>₱${item.amount.toFixed(2)}</strong>
                </div>
            `;
        }).join('');

        previewItems.innerHTML = formattedItems;
        selectedItemSummary.textContent = items.map(item => item.label).join(', ');

        const autoDescription = buildDescription(items);
        invoiceDescription.value = autoDescription;
        previewDescription.textContent = autoDescription || 'Auto-generated summary will appear here.';

        const typeCount = new Set(items.map(item => item.type)).size;
        previewTypeBadge.textContent = typeCount > 1 ? 'Mixed' : (items[0].type === 'membership' ? 'Membership' : items[0].type === 'service' ? 'Service' : 'Custom');
        previewTypeBadge.className = 'badge ' + (typeCount > 1 ? 'bg-dark' : items[0].type === 'membership' ? 'bg-primary' : items[0].type === 'service' ? 'bg-info text-dark' : 'bg-secondary');
    }

    function updateInvoicePreview() {
        const selectedMemberId = memberSelect.value;
        const memberName = memberSelect.selectedOptions[0] && memberSelect.selectedOptions[0].value ? memberSelect.selectedOptions[0].text : 'No member selected';
        previewMember.textContent = selectedMemberId ? memberName : 'No member selected';

        const items = getSelectedItems();
        renderSelectedItems(items);
        const totals = calculateTotal(items);

        if (!items.length) {
            selectedCountBadge.textContent = '0 items selected';
        } else {
            selectedCountBadge.textContent = items.length + (items.length === 1 ? ' item selected' : ' items selected');
        }

        return totals;
    }

    memberSelect.addEventListener('change', filterRows);
    discountInput.addEventListener('input', updateInvoicePreview);
    taxRateInput.addEventListener('input', updateInvoicePreview);

    document.addEventListener('input', function(event) {
        if (event.target.classList.contains('billable-item') || event.target.classList.contains('custom-amount')) {
            updateInvoicePreview();
        }
    });

    document.addEventListener('input', function(event) {
        if (event.target.classList.contains('custom-desc')) {
            updateInvoicePreview();
        }
    });

    selectAllPayments.addEventListener('change', function() {
        visibleRows('#paymentTable tr.payment-row').forEach(row => {
            const checkbox = row.querySelector('.billable-item');
            if (checkbox && !checkbox.disabled) {
                checkbox.checked = selectAllPayments.checked;
            }
        });
        updateInvoicePreview();
    });

    document.getElementById('addCustomItem').addEventListener('click', function() {
        addCustomItemRow();
        updateInvoicePreview();
    });

    customItemsTable.addEventListener('click', function(event) {
        if (event.target.classList.contains('remove-custom')) {
            event.target.closest('tr').remove();
            updateInvoicePreview();
        }
    });

    filterRows();
    updateInvoicePreview();
});
</script>
@endsection
