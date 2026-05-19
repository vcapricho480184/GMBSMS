@extends('layouts.admin')
@section('title', 'Attendance')
@section('page-title', 'Attendance Management')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon success"><i class="bi bi-calendar-check"></i></div>
            <div class="metric-info"><h3>{{ $todayCount }}</h3><p>Today's Check-ins</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon info"><i class="bi bi-person-walking"></i></div>
            <div class="metric-info"><h3>{{ $checkedInNow }}</h3><p>Currently In Gym</p></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="mb-3">Quick Check-in / Check-out</h6>
                <form method="POST" action="{{ route('admin.attendance.check-in') }}" class="d-flex gap-2 mb-2">
                    @csrf
                    <select name="user_id" class="form-select form-select-sm" required>
                        <option value="">Select Member</option>
                        @foreach(\App\Models\User::where('role','member')->orderBy('name')->get() as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-success text-nowrap">Check In</button>
                </form>
                <form method="POST" action="{{ route('admin.attendance.check-out') }}" class="d-flex gap-2">
                    @csrf
                    <select name="user_id" class="form-select form-select-sm" required>
                        <option value="">Select Member</option>
                        @foreach(\App\Models\Attendance::with('user')->whereDate('date', today())->whereNull('check_out')->get() as $a)
                            <option value="{{ $a->user_id }}">{{ $a->user->name }}</option>
                        @endforeach
                    </select>
                    <button class="btn btn-sm btn-warning text-nowrap">Check Out</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="filter-bar">
    <form class="d-flex gap-2 flex-wrap" method="GET">
        <input type="text" name="search" class="form-control" placeholder="Search member..." value="{{ request('search') }}" style="width:200px;">
        <input type="date" name="date" class="form-control" value="{{ request('date') }}" style="width:170px;">
        <button class="btn btn-primary"><i class="bi bi-search me-1"></i>Filter</button>
        @if(request()->hasAny(['search','date']))<a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary">Clear</a>@endif
    </form>
</div>

<div class="card"><div class="card-body p-0"><div class="table-responsive">
    <table class="table">
        <thead><tr><th>Member</th><th>Date</th><th>Check In</th><th>Check Out</th><th>Duration</th></tr></thead>
        <tbody>
            @forelse($attendance as $a)
            <tr>
                <td class="fw-medium">{{ $a->user->name ?? 'N/A' }}</td>
                <td>{{ $a->date->format('M d, Y') }}</td>
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
            <tr><td colspan="5" class="empty-state"><i class="bi bi-calendar-check"></i><p>No attendance records</p></td></tr>
            @endforelse
        </tbody>
    </table>
</div></div></div>
<div class="mt-3">{{ $attendance->links() }}</div>
@endsection
