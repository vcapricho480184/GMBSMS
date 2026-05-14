<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - FitCore Elite</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
    <style>
        /* Pagination fix - ensure no overlapping */
        nav[aria-label="Pagination Navigation"],
        nav.d-flex.justify-items-center {
            margin-bottom: 2rem !important;
            padding-bottom: 1rem !important;
            overflow: visible !important;
        }
        .pagination {
            margin-bottom: 0 !important;
            flex-wrap: wrap !important;
        }
        .page-link {
            font-size: 0.875rem !important;
            padding: 0.375rem 0.75rem !important;
            line-height: 1.5 !important;
        }
        .page-item {
            margin: 2px !important;
        }
        nav .small.text-muted {
            line-height: 2.5 !important;
            margin-bottom: 0 !important;
            padding: 0.5rem 0 !important;
        }
        /* Ensure content wrapper has bottom padding */
        .content-wrapper {
            padding-bottom: 3rem !important;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon"><i class="bi bi-heart-pulse"></i></div>
            <div>
                <h5>FitCore Elite</h5>
                <small>Admin Panel</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-section">Main</div>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i> Dashboard
            </a>

            <div class="nav-section">Management</div>
            <a href="{{ route('admin.members.index') }}" class="{{ request()->routeIs('admin.members.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i> Members
            </a>
            <a href="{{ route('admin.plans.index') }}" class="{{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                <i class="bi bi-card-checklist"></i> Membership Plans
            </a>
            <a href="{{ route('admin.memberships.index') }}" class="{{ request()->routeIs('admin.memberships.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i> Memberships
            </a>

            <div class="nav-section">Services</div>
            <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard2-pulse"></i> Gym Services
            </a>
            <a href="{{ route('admin.availed-services.index') }}" class="{{ request()->routeIs('admin.availed-services.*') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i> Availed Services
            </a>
            <a href="{{ route('admin.approvals.index') }}" class="{{ request()->routeIs('admin.approvals.*') ? 'active' : '' }}">
                <i class="bi bi-check-circle"></i> Approvals
                @php
                    $pendingCount = \App\Models\AvailedService::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                @endif
            </a>

            <div class="nav-section">Finance</div>
            <a href="{{ route('admin.billing.index') }}" class="{{ request()->routeIs('admin.billing.*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Billing
            </a>

            <div class="nav-section">Tracking</div>
            <a href="{{ route('admin.attendance.index') }}" class="{{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                <i class="bi bi-calendar-check"></i> Attendance
            </a>
            <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="bi bi-bar-chart-line"></i> Reports
            </a>
        </nav>
        <div class="sidebar-user">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="user-info">
                <p>{{ auth()->user()->name }}</p>
                <small>Administrator</small>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light border-0" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted" style="font-size:0.8rem;">{{ now()->format('M d, Y') }}</span>
            </div>
        </header>

        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle me-1"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        });
        document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('sidebarOverlay').classList.remove('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
