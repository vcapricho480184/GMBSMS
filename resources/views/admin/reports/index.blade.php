@extends('layouts.admin')
@section('title', 'Reports')
@section('page-title', 'Reports')

@section('content')
<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><div style="width:60px;height:60px;border-radius:var(--radius);background:rgba(79,70,229,0.1);display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-currency-exchange fs-3 text-primary"></i></div></div>
                <h6>Revenue Report</h6>
                <p class="text-muted" style="font-size:0.85rem;">View revenue by date range with totals and export options.</p>
                <a href="{{ route('admin.reports.revenue') }}" class="btn btn-primary btn-sm">View Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><div style="width:60px;height:60px;border-radius:var(--radius);background:rgba(16,185,129,0.1);display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-person-badge fs-3 text-success"></i></div></div>
                <h6>Membership Report</h6>
                <p class="text-muted" style="font-size:0.85rem;">View memberships by status — active, expired, or cancelled.</p>
                <a href="{{ route('admin.reports.memberships') }}" class="btn btn-success btn-sm">View Report</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><div style="width:60px;height:60px;border-radius:var(--radius);background:rgba(245,158,11,0.1);display:inline-flex;align-items:center;justify-content:center;"><i class="bi bi-calendar-check fs-3 text-warning"></i></div></div>
                <h6>Attendance Report</h6>
                <p class="text-muted" style="font-size:0.85rem;">Daily attendance summary with check-in/out times.</p>
                <a href="{{ route('admin.reports.attendance') }}" class="btn btn-warning btn-sm">View Report</a>
            </div>
        </div>
    </div>
</div>
@endsection
