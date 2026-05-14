@extends('layouts.admin')
@section('title', 'Assign Membership')
@section('page-title', 'Assign Membership')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-badge me-2"></i>Assign Membership to Member</div>
            <div class="card-body">
                @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('admin.memberships.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Member *</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Select Member</option>
                                @foreach($members as $m)<option value="{{ $m->id }}" {{ old('user_id')==$m->id?'selected':'' }}>{{ $m->name }} ({{ $m->email }})</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Plan *</label>
                            <select name="membership_plan_id" class="form-select" required>
                                <option value="">Select Plan</option>
                                @foreach($plans as $p)<option value="{{ $p->id }}" {{ old('membership_plan_id')==$p->id?'selected':'' }}>{{ $p->name }} — {{ $p->formatted_price }} ({{ $p->duration_label }})</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Method *</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="cash">Cash</option>
                                <option value="card">Credit/Debit Card</option>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">PayMaya</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Payment Status *</label>
                            <select name="payment_status" class="form-select" required>
                                <option value="paid">Paid</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Assign Membership</button>
                        <a href="{{ route('admin.memberships.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
