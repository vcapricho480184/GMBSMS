@extends('layouts.member')
@section('title', 'Attendance')
@section('page-title', 'My Attendance')

@section('content')
<div class="card mb-3">
    <div class="card-body">
        <h6 class="mb-1"><i class="bi bi-calendar-check me-2"></i>Attendance Records</h6>
        <p class="text-muted mb-0" style="font-size:0.85rem;">View your attendance history below</p>
    </div>
</div>

<div class="filter-bar">
    <form class="d-flex gap-2" method="GET">
        <input type="month" name="month" class="form-control" value="{{ request('month') }}" style="width:200px;">
        <button class="btn btn-primary btn-sm"><i class="bi bi-search me-1"></i>Filter</button>
    </form>
</div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table">
        <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Duration</th></tr></thead>
        <tbody>
            @forelse($attendance as $a)
            <tr>
                <td>{{ $a->date->format('M d, Y (D)') }}</td>
                <td>{{ \Carbon\Carbon::parse($a->check_in)->format('h:i A') }}</td>
                <td>
                    @if($a->check_out)
                        {{ \Carbon\Carbon::parse($a->check_out)->format('h:i A') }}
                    @else
                        —
                    @endif
                </td>
                <td>{{ $a->duration ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="empty-state"><i class="bi bi-calendar-check"></i><p>No attendance records</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div></div></div>
<div class="mt-3">{{ $attendance->links() }}</div>
@endsection
