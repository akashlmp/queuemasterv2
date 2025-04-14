@extends('common.layouts')

@section('content')

<div class="row justify-content-center mt-5">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Dashboard</div>
            <div class="card-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        {{ $message }}
                    </div>
                @else
                    <div class="alert alert-success">
                        Visit your email and verify your account.
                    </div>
                @endif

                <!-- Resend Verification Button -->
                <form action="{{ route('verification.resend')}}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user_id }}">
                    <button type="submit" class="btn btn-primary">Resend Verification Email</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
