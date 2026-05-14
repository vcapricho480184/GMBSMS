@extends('layouts.admin')
@section('title', 'Membership Report')
@section('page-title', 'Membership Report')

@section('content')
<div class="filter-bar">
    <form class="d-flex gap-2" method="GET">
        <select name="status" class="form-select" style="width:180px;">
            <option value="active" {{ $status=='active'?'selected':'' }}>Active</option>
            <option value="expired" {{ $status=='expired'?'selected':'' }}>Expired</option>
            <option value="cancelled" {{ $status=='cancelled'?'selected':'' }}>Cancelled</option>
        </select>
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
    </form>
</div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table">
        <thead><tr><th>Member</th><th>Plan</th><th>Start</th><th>End</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($memberships as $m)
            <tr>
                <td class="fw-medium">{{ $m->user->name ?? 'N/A' }}</td>
                <td>{{ $m->membershipPlan->name ?? 'N/A' }} ({{ $m->membershipPlan->formatted_price ?? '' }})</td>
                <td>{{ $m->start_date->format('M d, Y') }}</td>
                <td>{{ $m->end_date->format('M d, Y') }}</td>
                <td><span class="badge badge-{{ $m->status }}">{{ ucfirst($m->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-3">No memberships found</td></tr>
            @endforelse
        </tbody>
    </table>
</div></div></div>
<div class="mt-3">{{ $memberships->links() }}</div>
@endsection
