@extends('common.layouts')

@section('content')

<div class="mainbg">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-12  col-lg-12 vh-100 d-flex justify-content-center align-items-center">
                <div class="registerSectionDiv">
                    <div class="card cardwidth p-2" style="background-color: #FFFFFF;">
                        <div class="overlay-card">
                            <div class="card-body mx-auto d-flex justify-content-center">
                                <img src="{{ asset('asset/img/Logo=with-text.png') }}">
                            </div>
                            <div class="container mt-3">
                                <form action="{{ route('store') }}" method="post" id="registerForm">
                                    @csrf
                                    <h5 class="fw-bold">Register</h5>
                                    <div class="mt-4">
                                        <div>
                                            <p class="credential">Email</p>
                                            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="example@mail.com" required>
                                            <span class="text-danger" id="emailError"></span> <!-- JavaScript validation error message -->
                                            @if ($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span> <!-- Blade validation error message -->
                                            @endif
                                        </div>

                                        <div class="password-container mt-4">
                                            <p class="credential">Password
                                                <i class="bx bx-hide password-toggle float-end" id="password-toggle"></i>
                                            </p>
                                            <input type="password" class="form-control  @error('password') is-invalid @enderror" id="password" name="password" placeholder="●●●●●●" required>
                                            <span class="text-danger" id="passwordError"></span> <!-- JavaScript validation error message -->
                                            @if ($errors->has('password'))
                                            <span class="text-danger">{{ $errors->first('password') }}</span> <!-- Blade validation error message -->
                                            @endif
                                        </div>


                                        <div class="col-12">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" id="accept_terms" name="accept_terms" {{ old('accept_terms') ? 'checked' : '' }} required>
                                                <label class="form-check-label credential" for="accept_terms">
                                                    By creating an account you agree to the <a href="{{ route('termsOfUse') }}" target="_blank" class="labelcheck"> terms of
                                                        use</a> and our <a href="{{ route('privacyPolicy') }}"  target="_blank" class="labelcheck">privacy policy.</a>
                                                </label>
                                                <span class="text-danger" id="acceptTermsError"></span> <!-- JavaScript validation error message -->
                                                @if ($errors->has('accept_terms'))
                                                <span class="text-danger">{{ $errors->first('accept_terms') }}</span> <!-- Blade validation error message -->
                                                @endif
                                            </div>
                                        </div>


                                        <div class="col-12">
                                            <div class="d-grid mt-4">
                                                <button class="btn bsb-btn-2xl submitbtn" type="submit" id="submitButton">Register
                                                    QueueMaster</button>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-4">
                                            <p class="credential">Already have an account? <a href="{{ url('login') }}" class="labelcheck">Log in</a></p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var email_verify = 0;
        var pwd_verify = 0;
        var terms_accepted = 0;

        // Disable submit button by default
        $('#submitButton').prop('disabled', true);

        // Email validation
        $('#email').on('keyup', function() {
            var email = $(this).val().trim();
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.length > 0 && emailPattern.test(email)) {
                console.log('Valid email address: ' + email);
                email_verify = 1;
                $('#emailError').text('');
            } else {
                console.log('Invalid email address: ' + email);
                email_verify = 0;
                $('#emailError').text('Invalid email address');
            }
            checkSubmitButtonStatus();
        });

        // Password validation
        $('#password').on('keyup', function() {
            var password = $(this).val().trim();
            var passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/;
            if (passwordPattern.test(password)) {
                console.log('Valid password: ' + password);
                pwd_verify = 1;
                $('#passwordError').text('');
            } else {
                console.log('Invalid password');
                pwd_verify = 0;
                $('#passwordError').text('Password must be at least 8 characters long and contain at least one letter, one number, and one special character.');
            }
            checkSubmitButtonStatus();
        });
        // $('#password').on('keyup', function() {
        //     var password = $(this).val().trim();
        //     var isValid = password.length >= 8;
        //     if (isValid) {
        //         console.log('Valid password: ' + password);
        //         pwd_verify = 1;
        //         $('#passwordError').text('');
        //     } else {
        //         console.log('Invalid password');
        //         pwd_verify = 0;
        //         $('#passwordError').text('Password must be exactly 8 characters long.');
        //     }
        //     checkSubmitButtonStatus();
        // });

        // Checkbox validation
        $('#accept_terms').on('change', function() {
            terms_accepted = this.checked ? 1 : 0;
            if (terms_accepted) {
                $('#acceptTermsError').text('');
            } else {
                $('#acceptTermsError').text('You must accept the terms and conditions.');
            }
            checkSubmitButtonStatus();
        });

        // Function to check the status of the submit button
        function checkSubmitButtonStatus() {
            if (email_verify && pwd_verify && terms_accepted) {
                $('#submitButton').prop('disabled', false);
            } else {
                $('#submitButton').prop('disabled', true);
            }
        }

        // Disable submit button on form submission
    $('#registerForm').on('submit', function() {
        $('#submitButton').prop('disabled', true);
    });
    });
</script>


@endsection