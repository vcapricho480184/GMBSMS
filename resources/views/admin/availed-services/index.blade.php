@extends('layouts.admin')
@section('title', 'Availed Services')
@section('page-title', 'Availed Services')

@section('content')
<div class="alert alert-info mb-3">
    <i class="bi bi-info-circle me-2"></i>
    <strong>Note:</strong> Services appear here automatically when approved in the Approvals module.
</div>

<div class="filter-bar d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Search member..." value="{{ request('search') }}" style="width:200px;">
        <select name="service" class="form-select" style="width:200px;">
            <option value="">All Services</option>
            @foreach($services as $s)<option value="{{ $s->id }}" {{ request('service')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach
        </select>
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        <a href="{{ route('admin.availed-services.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-clockwise me-1"></i>Reset</a>
    </form>
</div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table">
        <thead><tr><th>Member</th><th>Service</th><th>Date Availed</th><th>Approved By</th><th>Status</th><th>Notes</th></tr></thead>
        <tbody>
            @forelse($availedServices as $as)
            <tr>
                <td class="fw-medium">{{ $as->user->name ?? 'N/A' }}</td>
                <td>{{ $as->gymService->name ?? 'N/A' }}</td>
                <td>{{ $as->availed_date->format('M d, Y') }}</td>
                <td class="text-muted">{{ $as->approvedBy->name ?? 'System' }}</td>
                <td><span class="badge badge-approved">Approved</span></td>
                <td class="text-muted">{{ $as->notes ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-state"><i class="bi bi-clipboard-check"></i><p>No approved services yet. Approve service requests in the Approvals module.</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div></div></div>
<div class="mt-3">{{ $availedServices->links() }}</div>
@endsection
