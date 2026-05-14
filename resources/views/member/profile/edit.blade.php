@extends('layouts.member')
@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')
<div class="row justify-content-center"><div class="col-lg-8"><div class="card">
    <div class="card-header"><i class="bi bi-pencil me-2"></i>Update Your Information</div>
    <div class="card-body">
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Full Name *</label><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" value="{{ $user->email }}" disabled></div>
                <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}"></div>
                <div class="col-md-6"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">Select</option><option value="male" {{ old('gender',$user->gender)=='male'?'selected':'' }}>Male</option><option value="female" {{ old('gender',$user->gender)=='female'?'selected':'' }}>Female</option><option value="other" {{ old('gender',$user->gender)=='other'?'selected':'' }}>Other</option></select></div>
                <div class="col-md-6"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"></div>
                <div class="col-md-6"><label class="form-label">Emergency Contact</label><input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact', $user->emergency_contact) }}"></div>
                <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea></div>
                <div class="col-md-6"><label class="form-label">Photo</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
            </div>
            <div class="mt-4 d-flex gap-2"><button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save Changes</button><a href="{{ route('member.profile.show') }}" class="btn btn-outline-secondary">Cancel</a></div>
        </form>
    </div>
</div></div></div>
@endsection
