@extends('layouts.member')
@section('title', 'My Services')
@section('page-title', 'My Availed Services')

@section('content')
<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table">
        <thead><tr><th>Service</th><th>Price</th><th>Date</th><th>Status</th><th>Notes</th><th width="120">Actions</th></tr></thead>
        <tbody>
            @forelse($availed as $a)
            <tr>
                <td class="fw-medium">{{ $a->gymService->name ?? 'N/A' }}</td>
                <td>{{ $a->gymService->formatted_price ?? 'N/A' }}</td>
                <td>{{ $a->availed_date->format('M d, Y') }}</td>
                <td>
                    @if($a->status === 'pending')
                        <span class="badge badge-pending">Pending</span>
                    @elseif($a->status === 'approved')
                        <span class="badge badge-active">Approved</span>
                    @else
                        <span class="badge badge-expired">Rejected</span>
                    @endif
                </td>
                <td>
                    <div class="text-muted" style="font-size:0.85rem;">{{ $a->notes ?? '—' }}</div>
                    @if($a->admin_notes)
                        <small class="text-danger"><strong>Admin:</strong> {{ $a->admin_notes }}</small>
                    @endif
                </td>
                <td>
                    @if($a->status === 'pending')
                        <div class="d-flex gap-1">
                            <a href="{{ route('member.services.edit', $a) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('member.services.destroy', $a) }}" onsubmit="return confirm('Cancel this request?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Cancel"><i class="bi bi-x-circle"></i></button>
                            </form>
                        </div>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-state"><i class="bi bi-clipboard-check"></i><p>No availed services</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div></div></div>
<div class="mt-3">{{ $availed->links() }}</div>
@endsection
