@extends('common.layouts')

@section('content')

<div class="mainbg">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12 vh-100 d-flex align-items-center justify-content-center">
                    <div class="forgotScctionDiv">
                <div class="alertlogin w-100">
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            {!! Session::get('success') !!}
                        </div>
                    @elseif(Session::has('error'))
                        <div class="alert alert-danger">
                            {!! Session::get('error') !!}
                        </div>
                    @endif
                </div>
                    <div class="card cardwidth p-2" style="background-color: #FFFFFF;">
                    <div class="overlay-card">
                        <div class="card-body mx-auto d-flex justify-content-center">
                            <img src="{{ asset('asset/img/Logo=with-text.png') }}">
                        </div>
                        <div class="container mt-3">
                        <form action="{{ route('password.email') }}" method="post">
                            @csrf

                            <h5 class="fw-bold">Forgot your password?</h5>
                            <p class="forgotp">We’ll email instructions to below address on how to reset it.</p>
                                <div>
                                    <div  class="email-container mt-2">
                                    <!-- <p class="credential">Email</p> -->
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="forgotemail" name="email" value="{{ old('email') }}" placeholder="Alibrabra@adidarci.com" required>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
 
                                <div class="col-12">
                                    <div class="d-grid mt-4 submitbtn">
                                        <button class="btn bsb-btn-2xl subbtnbtn" type="submit">Reset password</button>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <p class="credential"> <a href="{{ url('login') }}" class="labelcheck">Return to login</a></p>
                                </div>

                              </div>
                        </form>
                    </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
