@extends('layouts.admin')
@section('title', 'Service Approvals')
@section('page-title', 'Service Request Approvals')

@section('content')
@if($pendingCount > 0)
<div class="alert alert-warning mb-3">
    <i class="bi bi-exclamation-triangle me-1"></i>
    <strong>{{ $pendingCount }}</strong> service request(s) pending approval
</div>
@endif

<div class="filter-bar d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Search member..." value="{{ request('search') }}" style="width:200px;">
        <select name="status" class="form-select" style="width:160px;">
            <option value="">All Status</option>
            <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
            <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
            <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
        </select>
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.approvals.index') }}" class="btn btn-outline-secondary">Clear</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Requested</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="fw-medium">{{ $req->user->name ?? 'N/A' }}</td>
                        <td>
                            <div>{{ $req->gymService->name ?? 'N/A' }}</div>
                            @if($req->notes)
                                <small class="text-muted"><i class="bi bi-chat-left-text"></i> {{ $req->notes }}</small>
                            @endif
                        </td>
                        <td>{{ $req->availed_date->format('M d, Y') }}</td>
                        <td class="fw-medium">{{ $req->gymService->formatted_price ?? 'N/A' }}</td>
                        <td>
                            @if($req->status === 'pending')
                                <span class="badge badge-pending">Pending</span>
                            @elseif($req->status === 'approved')
                                <span class="badge badge-active">Approved</span>
                            @else
                                <span class="badge badge-expired">Rejected</span>
                            @endif
                        </td>
                        <td>{{ $req->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($req->status === 'pending')
                                <!-- Approve Form (Initially Hidden) -->
                                <div class="approve-form-{{ $req->id }}" style="display: none;">
                                    <form method="POST" action="{{ route('admin.approvals.approve', $req) }}" class="mb-2">
                                        @csrf
                                        <div class="mb-2">
                                            <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="Admin notes (optional)">
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                <i class="bi bi-check"></i> Confirm Approve
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm cancel-approve-{{ $req->id }}">Cancel</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Reject Form (Initially Hidden) -->
                                <div class="reject-form-{{ $req->id }}" style="display: none;">
                                    <form method="POST" action="{{ route('admin.approvals.reject', $req) }}" class="mb-2">
                                        @csrf
                                        <div class="mb-2">
                                            <input type="text" name="admin_notes" class="form-control form-control-sm" placeholder="Rejection reason (required)" required>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-x"></i> Confirm Reject
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm cancel-reject-{{ $req->id }}">Cancel</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Action Buttons (Initially Visible) -->
                                <div class="action-buttons-{{ $req->id }}">
                                    <button type="button" class="btn btn-sm btn-success show-approve-{{ $req->id }}" title="Approve">
                                        <i class="bi bi-check-circle"></i> Approve
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger show-reject-{{ $req->id }}" title="Reject">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </div>
                            @else
                                <div>
                                    <small class="text-muted">By: {{ $req->approvedBy->name ?? 'N/A' }}</small><br>
                                    <small class="text-muted">{{ $req->approved_at->format('M d, Y') }}</small>
                                    @if($req->admin_notes)
                                        <br><small class="text-info"><i class="bi bi-chat-left-text"></i> {{ $req->admin_notes }}</small>
                                    @endif
                                </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-state"><i class="bi bi-clipboard-check"></i><p>No service requests found</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $requests->links() }}</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @foreach($requests as $req)
        @if($req->status === 'pending')
        // Show approve form
        document.querySelector('.show-approve-{{ $req->id }}')?.addEventListener('click', function() {
            document.querySelector('.action-buttons-{{ $req->id }}').style.display = 'none';
            document.querySelector('.approve-form-{{ $req->id }}').style.display = 'block';
        });
        
        // Cancel approve
        document.querySelector('.cancel-approve-{{ $req->id }}')?.addEventListener('click', function() {
            document.querySelector('.approve-form-{{ $req->id }}').style.display = 'none';
            document.querySelector('.action-buttons-{{ $req->id }}').style.display = 'block';
        });
        
        // Show reject form
        document.querySelector('.show-reject-{{ $req->id }}')?.addEventListener('click', function() {
            document.querySelector('.action-buttons-{{ $req->id }}').style.display = 'none';
            document.querySelector('.reject-form-{{ $req->id }}').style.display = 'block';
        });
        
        // Cancel reject
        document.querySelector('.cancel-reject-{{ $req->id }}')?.addEventListener('click', function() {
            document.querySelector('.reject-form-{{ $req->id }}').style.display = 'none';
            document.querySelector('.action-buttons-{{ $req->id }}').style.display = 'block';
        });
        @endif
    @endforeach
});
</script>
@endpush
