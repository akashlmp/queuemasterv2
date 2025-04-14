@extends('admin.common.layouts')
@extends('admin.common.sidebar')
@extends('admin.common.navbar')
@section('content')


    <div class="container">
        <h2 class="mt-4 mb-4">Update Profile</h2>
        <form action="{{ route('profile.update22') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $companyName }}" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $email }}" required>
            </div>

            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



@endsection