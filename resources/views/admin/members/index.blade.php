@extends('layouts.admin')
@section('title', 'Members')
@section('page-title', 'Members')

@section('content')
<div class="filter-bar d-flex flex-wrap gap-2 align-items-center justify-content-between">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Search name, email, phone..." value="{{ request('search') }}" style="width:250px;">
        <select name="status" class="form-select" style="width:160px;">
            <option value="">All Status</option>
            <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
            <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
        </select>
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">Clear</a>
        @endif
    </form>
    <a href="{{ route('admin.members.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Member</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $member)
                    <tr>
                        <td class="fw-medium">{{ $member->name }}</td>
                        <td class="text-muted">{{ $member->email }}</td>
                        <td>{{ $member->phone ?? '—' }}</td>
                        <td>
                            @if($member->activeMembership)
                                <span class="badge bg-primary">{{ $member->activeMembership->membershipPlan->name ?? 'N/A' }}</span>
                            @else
                                <span class="text-muted">None</span>
                            @endif
                        </td>
                        <td>
                            @if($member->activeMembership)
                                <span class="badge badge-active">Active</span>
                            @else
                                <span class="badge badge-expired">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $member->created_at->format('M d, Y') }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.members.show', $member) }}" class="btn btn-sm btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('admin.members.edit', $member) }}" class="btn btn-sm btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.members.destroy', $member) }}" onsubmit="return confirm('Delete this member?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-state"><i class="bi bi-people"></i><p>No members found</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $members->links() }}</div>
@endsection
