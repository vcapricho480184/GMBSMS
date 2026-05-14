@extends('layouts.admin')
@section('title', 'Attendance Report')
@section('page-title', 'Attendance Report')

@section('content')
<div class="filter-bar">
    <form class="d-flex gap-2" method="GET">
        <input type="date" name="date" class="form-control" value="{{ $date }}" style="width:180px;">
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Generate</button>
    </form>
</div>

<div class="card mb-3">
    <div class="card-body text-center">
        <h6 class="text-muted">Attendance for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h6>
        <h3>{{ $records->count() }} Members</h3>
    </div>
</div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table">
        <thead><tr><th>Member</th><th>Check In</th><th>Check Out</th><th>Duration</th></tr></thead>
        <tbody>
            @forelse($records as $r)
            <tr>
                <td class="fw-medium">{{ $r->user->name ?? 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($r->check_in)->format('h:i A') }}</td>
                <td>{{ $r->check_out ? \Carbon\Carbon::parse($r->check_out)->format('h:i A') : '—' }}</td>
                <td>{{ $r->duration ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted py-3">No attendance for this date</td></tr>
            @endforelse
        </tbody>
    </table>
</div></div></div>
@endsection
