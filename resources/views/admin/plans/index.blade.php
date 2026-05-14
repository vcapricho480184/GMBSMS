@extends('layouts.admin')
@section('title', 'Membership Plans')
@section('page-title', 'Membership Plans')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div></div>
    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Plan</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr><th>Plan Name</th><th>Price</th><th>Duration</th><th>Active Members</th><th>Status</th><th width="120">Actions</th></tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr>
                        <td><div class="fw-medium">{{ $plan->name }}</div><small class="text-muted">{{ Str::limit($plan->description, 60) }}</small></td>
                        <td class="fw-medium">{{ $plan->formatted_price }}</td>
                        <td>{{ $plan->duration_label }}</td>
                        <td><span class="badge bg-primary">{{ $plan->memberships_count }}</span></td>
                        <td><span class="badge badge-{{ $plan->status == 'active' ? 'active' : 'unavailable' }}">{{ ucfirst($plan->status) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty-state"><i class="bi bi-card-checklist"></i><p>No plans found</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $plans->links() }}</div>
@endsection
