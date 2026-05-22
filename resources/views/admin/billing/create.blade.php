@extends('layouts.admin')
@section('title', 'New Transaction')
@section('page-title', 'Create New Transaction')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="mb-1"><i class="bi bi-receipt me-2"></i>New Transaction</h5>
                <p class="text-muted mb-0">Record a simple payment transaction for a member.</p>
            </div>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.billing.store') }}" id="transactionForm">
            @csrf

            <div class="card mb-4">
                <div class="card-header"><h6 class="mb-0">Transaction Details</h6></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
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

                        <div class="col-md-6">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" id="typeSelect" class="form-select @error('type') is-invalid @enderror" required>
                                <option value="membership" {{ old('type', 'membership') == 'membership' ? 'selected' : '' }}>Membership</option>
                                <option value="service" {{ old('type') == 'service' ? 'selected' : '' }}>Service</option>
                                <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Membership picker --}}
                        <div class="col-12" id="membershipSection">
                            <label class="form-label">Select Membership</label>
                            <select id="membershipPicker" class="form-select">
                                <option value="">— Choose a membership —</option>
                                @foreach($memberships as $m)
                                    <option value="{{ $m->id }}"
                                            data-user="{{ $m->user_id }}"
                                            data-desc="{{ $m->membershipPlan->name }} Membership Plan"
                                            data-amount="{{ $m->membershipPlan->price }}">
                                        {{ $m->user->name }} — {{ $m->membershipPlan->name }} (₱{{ number_format($m->membershipPlan->price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Active memberships. Selecting one auto-fills the description and amount.</div>
                        </div>

                        {{-- Service picker --}}
                        <div class="col-12 d-none" id="serviceSection">
                            <label class="form-label">Select Service</label>
                            <select id="servicePicker" class="form-select">
                                <option value="">— Choose a service —</option>
                                @foreach($services as $s)
                                    <option value="{{ $s->id }}"
                                            data-user="{{ $s->user_id }}"
                                            data-desc="{{ $s->gymService->name }}"
                                            data-amount="{{ $s->gymService->price }}">
                                        {{ $s->user->name }} — {{ $s->gymService->name }} (₱{{ number_format($s->gymService->price, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Approved services awaiting payment.</div>
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="related_id" id="relatedIdInput" value="{{ old('related_id') }}">
                            <label class="form-label">Description <span class="text-danger">*</span></label>
                            <input type="text" name="description" id="descriptionInput" class="form-control @error('description') is-invalid @enderror" value="{{ old('description') }}" placeholder="e.g. Monthly membership, Personal training session" required>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amountInput" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="0.00" required>
                            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
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
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>Create Transaction
                </button>
                <a href="{{ route('admin.billing.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const memberSelect = document.getElementById('memberSelect');
    const typeSelect = document.getElementById('typeSelect');
    const membershipSection = document.getElementById('membershipSection');
    const serviceSection = document.getElementById('serviceSection');
    const membershipPicker = document.getElementById('membershipPicker');
    const servicePicker = document.getElementById('servicePicker');
    const descriptionInput = document.getElementById('descriptionInput');
    const amountInput = document.getElementById('amountInput');

    function toggleSections() {
        const type = typeSelect.value;
        membershipSection.classList.toggle('d-none', type !== 'membership');
        serviceSection.classList.toggle('d-none', type !== 'service');

        // Reset pickers when switching type
        membershipPicker.value = '';
        servicePicker.value = '';
        document.getElementById('relatedIdInput').value = '';
    }

    function filterPickerOptions(picker, userId) {
        Array.from(picker.options).forEach(opt => {
            if (!opt.value) return;
            opt.style.display = (opt.dataset.user === userId) ? '' : 'none';
        });
        picker.value = '';
    }

    function applyPickerSelection(picker) {
        const opt = picker.selectedOptions[0];
        if (opt && opt.value) {
            descriptionInput.value = opt.dataset.desc || '';
            amountInput.value = opt.dataset.amount || '';
            document.getElementById('relatedIdInput').value = opt.value;
        } else {
            document.getElementById('relatedIdInput').value = '';
        }
    }

    typeSelect.addEventListener('change', toggleSections);

    memberSelect.addEventListener('change', function() {
        const userId = this.value;
        filterPickerOptions(membershipPicker, userId);
        filterPickerOptions(servicePicker, userId);
    });

    membershipPicker.addEventListener('change', function() {
        applyPickerSelection(this);
    });

    servicePicker.addEventListener('change', function() {
        applyPickerSelection(this);
    });

    // Initialize
    toggleSections();
    if (memberSelect.value) {
        filterPickerOptions(membershipPicker, memberSelect.value);
        filterPickerOptions(servicePicker, memberSelect.value);
    }
});
</script>
@endsection
