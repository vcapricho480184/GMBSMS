@extends('layouts.admin')
@section('title', 'Gym Services')
@section('page-title', 'Gym Services')

@section('content')
<div class="d-flex justify-content-between mb-3">
    <div></div>
    <a href="{{ route('admin.services.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Service</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Service</th><th>Price</th><th>Times Used</th><th>Status</th><th width="120">Actions</th></tr></thead>
                <tbody>
                    @forelse($services as $s)
                    <tr>
                        <td><div class="fw-medium">{{ $s->name }}</div><small class="text-muted">{{ Str::limit($s->description, 60) }}</small></td>
                        <td class="fw-medium">{{ $s->formatted_price }}</td>
                        <td><span class="badge bg-primary">{{ $s->availed_services_count }}x</span></td>
                        <td><span class="badge badge-{{ $s->status }}">{{ ucfirst($s->status) }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.services.edit', $s) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <form method="POST" action="{{ route('admin.services.destroy', $s) }}" onsubmit="return confirm('Delete this service?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="empty-state"><i class="bi bi-clipboard2-pulse"></i><p>No services found</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">{{ $services->links() }}</div>
@endsection
