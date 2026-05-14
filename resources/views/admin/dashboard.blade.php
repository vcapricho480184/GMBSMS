@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-icon primary"><i class="bi bi-people"></i></div>
            <div class="metric-info">
                <h3>{{ $totalMembers }}</h3>
                <p>Total Members</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-icon success"><i class="bi bi-person-check"></i></div>
            <div class="metric-info">
                <h3>{{ $activeMemberships }}</h3>
                <p>Active Memberships</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-icon info"><i class="bi bi-currency-exchange"></i></div>
            <div class="metric-info">
                <h3>₱{{ number_format($monthlyRevenue, 2) }}</h3>
                <p>Monthly Revenue</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-icon warning"><i class="bi bi-exclamation-triangle"></i></div>
            <div class="metric-info">
                <h3>{{ $expiringSoon }}</h3>
                <p>Expiring Soon</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-icon success"><i class="bi bi-calendar-check"></i></div>
            <div class="metric-info">
                <h3>{{ $todayAttendance }}</h3>
                <p>Today's Attendance</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="metric-card">
            <div class="metric-icon danger"><i class="bi bi-clock-history"></i></div>
            <div class="metric-info">
                <h3>{{ $pendingPayments }}</h3>
                <p>Pending Payments</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-bar-chart me-2"></i>Monthly Revenue</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-pie-chart me-2"></i>Plan Distribution</span>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="planChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-3">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-people me-2"></i>Recent Members</span>
                <a href="{{ route('admin.members.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr><th>Name</th><th>Email</th><th>Joined</th></tr>
                        </thead>
                        <tbody>
                            @forelse($recentMembers as $m)
                            <tr>
                                <td class="fw-medium">{{ $m->name }}</td>
                                <td class="text-muted">{{ $m->email }}</td>
                                <td>{{ $m->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No members yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <span><i class="bi bi-trophy me-2"></i>Top Services</span>
                <a href="{{ route('admin.services.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr><th>Service</th><th>Price</th><th>Used</th></tr>
                        </thead>
                        <tbody>
                            @forelse($topServices as $s)
                            <tr>
                                <td class="fw-medium">{{ $s->name }}</td>
                                <td>{{ $s->formatted_price }}</td>
                                <td><span class="badge bg-primary">{{ $s->availed_services_count }}x</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">No services yet</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: @json($revenueLabels),
            datasets: [{
                label: 'Revenue (₱)',
                data: @json($revenueData),
                backgroundColor: 'rgba(79, 70, 229, 0.8)',
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => '₱' + Number(ctx.raw).toLocaleString('en-PH', {minimumFractionDigits: 2})
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => '₱' + Number(v).toLocaleString(),
                        font: { size: 11 }
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    ticks: { font: { size: 11 } },
                    grid: { display: false }
                }
            }
        }
    });

    const planCtx = document.getElementById('planChart').getContext('2d');
    new Chart(planCtx, {
        type: 'doughnut',
        data: {
            labels: @json($planDistribution->pluck('name')),
            datasets: [{
                data: @json($planDistribution->pluck('memberships_count')),
                backgroundColor: ['#4F46E5','#0EA5E9','#10B981','#F59E0B','#EF4444','#8B5CF6'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { size: 11 }, padding: 15 } }
            }
        }
    });
</script>
@endpush
