@extends('layouts.admin')
@section('title', 'Edit Plan')
@section('page-title', 'Edit Membership Plan')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil me-2"></i>Edit: {{ $plan->name }}</div>
            <div class="card-body">
                @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                <form method="POST" action="{{ route('admin.plans.update', $plan) }}">
                    @csrf @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label">Plan Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required></div>
                        <div class="col-md-6"><label class="form-label">Status *</label><select name="status" class="form-select"><option value="active" {{ $plan->status=='active'?'selected':'' }}>Active</option><option value="inactive" {{ $plan->status=='inactive'?'selected':'' }}>Inactive</option></select></div>
                        <div class="col-md-6"><label class="form-label">Price (₱) *</label><input type="number" name="price" class="form-control" step="0.01" min="0" value="{{ old('price', $plan->price) }}" required></div>
                        <div class="col-md-6"><label class="form-label">Duration (Days) *</label><input type="number" name="duration_days" class="form-control" min="1" value="{{ old('duration_days', $plan->duration_days) }}" required></div>
                        <div class="col-12"><label class="form-label">Description</label><textarea name="description" class="form-control" rows="3">{{ old('description', $plan->description) }}</textarea></div>
                    </div>
                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Update Plan</button>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
