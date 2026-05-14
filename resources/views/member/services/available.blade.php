@extends('layouts.member')
@section('title', 'Available Services')
@section('page-title', 'Available Services')

@section('content')
<div class="row g-3">
    @forelse($services as $s)
    <div class="col-md-6 col-xl-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <h6 class="mb-0">{{ $s->name }}</h6>
                    <span class="badge badge-available">Available</span>
                </div>
                <p class="text-muted mb-3" style="font-size:0.85rem;">{{ $s->description ?? 'No description available.' }}</p>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fs-5 fw-bold text-primary">{{ $s->formatted_price }}</span>
                    <small class="text-muted">per session</small>
                </div>
                
                <!-- Request Form (Initially Hidden) -->
                <div class="request-form" id="form-{{ $s->id }}" style="display: none;">
                    <form method="POST" action="{{ route('member.services.request') }}">
                        @csrf
                        <input type="hidden" name="gym_service_id" value="{{ $s->id }}">
                        
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.85rem;">Service Date</label>
                            <input type="date" name="availed_date" class="form-control form-control-sm" required min="{{ date('Y-m-d') }}">
                        </div>
                        
                        <div class="mb-2">
                            <label class="form-label" style="font-size: 0.85rem;">Notes (Optional)</label>
                            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Any special requests..."></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success btn-sm flex-fill">
                                <i class="bi bi-check-circle me-1"></i>Submit
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm cancel-btn" data-service-id="{{ $s->id }}">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Request Button (Initially Visible) -->
                <button type="button" class="btn btn-primary btn-sm w-100 show-form-btn" id="btn-{{ $s->id }}" data-service-id="{{ $s->id }}">
                    <i class="bi bi-plus-circle me-1"></i>Request Service
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><div class="empty-state"><i class="bi bi-clipboard2-pulse"></i><p>No services available</p></div></div>
    @endforelse
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show form when button clicked
    document.querySelectorAll('.show-form-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceId = this.getAttribute('data-service-id');
            document.getElementById('btn-' + serviceId).style.display = 'none';
            document.getElementById('form-' + serviceId).style.display = 'block';
        });
    });
    
    // Hide form when cancel clicked
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const serviceId = this.getAttribute('data-service-id');
            document.getElementById('form-' + serviceId).style.display = 'none';
            document.getElementById('btn-' + serviceId).style.display = 'block';
        });
    });
});
</script>
@endpush
