@extends('layouts.member')
@section('title', 'Available Services')
@section('page-title', 'Available Services')

@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Space+Grotesk:wght@400;500;600&display=swap');

.services-grid .service-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    background: #ffffff;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);
}

.service-media {
    position: relative;
    height: 150px;
}

.service-media svg {
    width: 100%;
    height: 100%;
    display: block;
}

.service-mark {
    position: absolute;
    left: 14px;
    bottom: 12px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.85);
    font-family: "Space Grotesk", "Segoe UI", sans-serif;
    font-weight: 600;
    font-size: 0.8rem;
    color: #0f172a;
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.18);
}

.service-mark .dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: #22c55e;
    box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.18);
}

.service-title {
    font-family: "Fraunces", "Georgia", serif;
    font-size: 1.05rem;
    letter-spacing: 0.2px;
}

.service-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 12px;
}

.service-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    background: #f1f5f9;
    color: #0f172a;
}

.service-chip i {
    font-size: 0.8rem;
}

.service-price {
    display: flex;
    align-items: baseline;
    gap: 8px;
}

.service-price small {
    color: #64748b;
}

.services-grid .badge-available {
    background: #e6fffa;
    color: #0f766e;
    border: 1px solid #99f6e4;
}
</style>
@endpush

@section('content')
<div class="row g-3 services-grid">
    @forelse($services as $s)
    <div class="col-md-6 col-xl-4">
        @php
            $defaultImage = 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=400&h=220&fit=crop';
            
            $serviceImages = [
                'Personal Training' => 'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?q=80&w=400&h=220&fit=crop',
                'Yoga Class' => 'https://images.unsplash.com/photo-1599901860904-17e6ed7083a0?q=80&w=400&h=220&fit=crop',
                'Zumba Class' => 'https://images.unsplash.com/photo-1524594152303-9fd13543fe6e?q=80&w=400&h=220&fit=crop',
                'Boxing Training' => 'https://images.unsplash.com/photo-1549719386-74dfcbf7dbed?q=80&w=400&h=220&fit=crop',
                'Nutrition Consultation' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=400&h=220&fit=crop',
                'Body Composition Analysis' => 'https://images.unsplash.com/photo-1576678927484-cc907957088c?q=80&w=400&h=220&fit=crop',
                'Swimming Pool Access' => 'https://images.unsplash.com/photo-1576610616656-d3aa5d1f4534?q=80&w=400&h=220&fit=crop',
                'Sauna Session' => 'https://images.unsplash.com/photo-1544365558-35aa4afcf11f?q=80&w=400&h=220&fit=crop',
            ];
            
            $imageUrl = $serviceImages[$s->name] ?? $defaultImage;
        @endphp
        <div class="card h-100 service-card">
            <div class="service-media">
                <img src="{{ $imageUrl }}" alt="{{ $s->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                <div class="service-mark">
                    <span class="dot"></span>
                    <span class="fw-bold">{{ $s->name }}</span>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-2">
                    <h6 class="mb-0 service-title">{{ $s->name }}</h6>
                    <span class="badge badge-available">Available</span>
                </div>
                <p class="text-muted mb-3" style="font-size:0.85rem;">{{ $s->description ?? 'No description available.' }}</p>
                <div class="service-meta">
                    <span class="service-chip"><i class="bi bi-people"></i>{{ $s->availed_services_count }} availed</span>
                    <span class="service-chip"><i class="bi bi-check2-circle"></i>{{ $s->approved_availed_count }} approved</span>
                </div>
                <div class="service-price mb-3">
                    <span class="fs-5 fw-bold text-primary">{{ $s->formatted_price }}</span>
                    <small>per session</small>
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
