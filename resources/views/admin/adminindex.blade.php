@extends('admin.common.layouts')
@extends('admin.common.sidebar')
@extends('admin.common.navbar')
@section('content')


<div class="card">
    <div class="card-header">
        User Profile
    </div>
    <div class="card-body">
        <p class="card-text">Click the button below to update user profile.</p>
        <a href="{{ route('user.profile.update') }}" class="btn btn-primary">Update Profile</a>
    </div>
</div>

@endsection