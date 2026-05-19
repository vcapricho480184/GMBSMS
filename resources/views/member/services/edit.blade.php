@extends('layouts.member')
@section('title', 'Edit Service Request')
@section('page-title', 'Edit Service Request')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Edit Service Request</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('member.services.update', $availedService) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Service</label>
                        <select name="gym_service_id" class="form-select" required>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ $availedService->gym_service_id == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }} - {{ $service->formatted_price }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Service Date</label>
                        <input type="date" name="availed_date" class="form-control @error('availed_date') is-invalid @enderror" 
                               value="{{ old('availed_date', $availedService->availed_date->format('Y-m-d')) }}" 
                               required min="{{ date('Y-m-d') }}">
                        @error('availed_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-1"></i>
                        <small>Changes will reset the approval status to pending. Admin will need to review your updated request.</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Update Request
                        </button>
                        <a href="{{ route('member.services.availed') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
