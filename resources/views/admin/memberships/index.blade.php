@extends('layouts.admin')
@section('title', 'Memberships')
@section('page-title', 'Memberships')

@section('content')
<div class="filter-bar d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Search member..." value="{{ request('search') }}" style="width:200px;">
        <select name="status" class="form-select" style="width:150px;">
            <option value="">All Status</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
            <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
        </select>
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
    </form>
    <a href="{{ route('admin.memberships.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Assign Membership</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Member</th><th>Plan</th><th>Start Date</th><th>End Date</th><th>Days Left</th><th>Status</th><th width="80">Action</th></tr></thead>
                <tbody>
                    @forelse($memberships as $m)
                    <tr>
                        <td class="fw-medium">{{ $m->user->name ?? 'N/A' }}</td>
                        <td>{{ $m->membershipPlan->name ?? 'N/A' }}</td>
                        <td>{{ $m->start_date->format('M d, Y') }}</td>
                        <td>{{ $m->end_date->format('M d, Y') }}</td>
                        <td>
                            @if($m->status == 'active')
                                <span class="{{ $m->is_expiring_soon ? 'text-warning fw-bold' : '' }}">{{ $m->days_remaining }} days</span>
                            @else — @endif
                        </td>
                        <td><span class="badge badge-{{ $m->status }}">{{ ucfirst($m->status) }}</span></td>
                        <td>
                            @if($m->status == 'active')
                            <form method="POST" action="{{ route('admin.memberships.destroy', $m) }}" onsubmit="return confirm('Cancel this membership?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Cancel"><i class="bi bi-x-lg"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-state"><i class="bi bi-person-badge"></i><p>No memberships found</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $memberships->links() }}</div>
@endsection
