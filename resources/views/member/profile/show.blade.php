@extends('layouts.member')
@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body text-center py-4">
                <div style="width:80px;height:80px;border-radius:50%;background:var(--primary);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;margin-bottom:1rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted mb-3">{{ $user->email }}</p>
                <a href="{{ route('member.profile.edit') }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil me-1"></i>Edit Profile</a>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-person me-2"></i>Profile Information</div>
            <div class="card-body">
                <div class="row g-3" style="font-size:0.9rem;">
                    <div class="col-md-6"><label class="text-muted d-block mb-1">Full Name</label><strong>{{ $user->name }}</strong></div>
                    <div class="col-md-6"><label class="text-muted d-block mb-1">Email</label><strong>{{ $user->email }}</strong></div>
                    <div class="col-md-6"><label class="text-muted d-block mb-1">Phone</label><strong>{{ $user->phone ?? 'Not set' }}</strong></div>
                    <div class="col-md-6"><label class="text-muted d-block mb-1">Gender</label><strong>{{ ucfirst($user->gender ?? 'Not set') }}</strong></div>
                    <div class="col-md-6"><label class="text-muted d-block mb-1">Date of Birth</label><strong>{{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'Not set' }}</strong></div>
                    <div class="col-md-6"><label class="text-muted d-block mb-1">Emergency Contact</label><strong>{{ $user->emergency_contact ?? 'Not set' }}</strong></div>
                    <div class="col-12"><label class="text-muted d-block mb-1">Address</label><strong>{{ $user->address ?? 'Not set' }}</strong></div>
                    <div class="col-md-6"><label class="text-muted d-block mb-1">Member Since</label><strong>{{ $user->created_at->format('M d, Y') }}</strong></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
