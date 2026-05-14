@extends('layouts.member')
@section('title', 'My Membership')
@section('page-title', 'My Membership')

@section('content')
<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table">
        <thead><tr><th>Plan</th><th>Price</th><th>Start Date</th><th>End Date</th><th>Days Left</th><th>Status</th></tr></thead>
        <tbody>
            @forelse($memberships as $m)
            <tr>
                <td class="fw-medium">{{ $m->membershipPlan->name ?? 'N/A' }}</td>
                <td>{{ $m->membershipPlan->formatted_price ?? 'N/A' }}</td>
                <td>{{ $m->start_date->format('M d, Y') }}</td>
                <td>{{ $m->end_date->format('M d, Y') }}</td>
                <td>@if($m->status=='active')<span class="{{ $m->days_remaining<=7?'text-warning fw-bold':'text-success' }}">{{ $m->days_remaining }} days</span>@else — @endif</td>
                <td><span class="badge badge-{{ $m->status }}">{{ ucfirst($m->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="6" class="empty-state"><i class="bi bi-card-checklist"></i><p>No membership history</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div></div></div>
<div class="mt-3">{{ $memberships->links() }}</div>
@endsection
