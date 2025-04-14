@extends('common.layouts')

@section('content')


<div class="mainbg">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-12 col-lg-12 vh-100 d-flex align-items-center justify-content-center">

            <div class="restPasswordDiv">
                <div class="card cardwidth p-2" style="background-color: #FFFFFF;">
                <div class="overlay-card">
                        <div class="card-body mx-auto d-flex justify-content-center">
                        <img src="{{ asset('asset/img/Logo=with-text.png') }}">
                    </div>
                    <div class="container mt-3">
                        <form action="{{ route('password.update') }}" method="post">
                            @csrf
                            <h5 class="fw-bold">Reset account password</h5>
                            <input type="hidden" name="token" value="{{ $token }}">
                                <div>

                                <div class="password-container mt-4">
                                        <!-- <p class="credential">Email</p> -->
                                        <input type="hidden" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $email }}" readonly>
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                </div>


                                    <div class="password-container mt-4">
                                        <p class="credential">New Password
                                            <i class="bx bx-hide password-toggle float-end" id="password-toggle-new"></i>
                                        </p>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="Newpassword" name="password" required>
                                        <span class="text-danger" id="newPasswordError"></span> <!-- JavaScript validation error message -->
                                        @error('password')
                                         <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div class="password-container mt-4">
                                        <p class="credential">Confirm Password
                                            <i class="bx bx-hide password-toggle float-end" id="password-toggle-confirm"></i>
                                        </p>
                                        <div >
                                            <input type="password" class="form-control" id="Confirmpassword" name="password_confirmation" required>
                                            <span class="text-danger" id="confirmPasswordError"></span> <!-- JavaScript validation error message -->
                                            
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="col-12 mb-5">
                                    <div class="d-grid mt-5">
                                        <button class="btn bsb-btn-2xl submitbtn" type="submit" id="resetButton">Reset password</button>
                                    </div>
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
    <script src="{{ asset('asset/js/resetpassword.js')}}"></script>
<script>
            $(document).ready(function() {
                var newPassword_verify = 0;
                var confirmPassword_verify = 0;

                // Disable submit button by default
                $('#resetButton').prop('disabled', true);

                // New Password validation
                $('#Newpassword, #Confirmpassword').on('keyup', function() {
                    var newPassword = $('#Newpassword').val().trim();
                    var confirmPassword = $('#Confirmpassword').val().trim();

                    // Password should be at least 8 characters long and meet the pattern requirements
                    var passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/;

                    if (passwordPattern.test(newPassword)) {
                        console.log('Valid new password: ' + newPassword);
                        newPassword_verify = 1;
                        $('#newPasswordError').text('');
                    } else {
                        console.log('Invalid new password');
                        newPassword_verify = 0;
                        $('#newPasswordError').text('Password must be 8+ characters and contain letters, numbers, and special characters.');
                    }

                    // Check if passwords match
                    if (confirmPassword === newPassword) {
                        console.log('Passwords match');
                        confirmPassword_verify = 1;
                        $('#confirmPasswordError').text('');
                    } else {
                        console.log('Passwords do not match');
                        confirmPassword_verify = 0;
                        $('#confirmPasswordError').text('Passwords do not match');
                    }

                    checkResetButtonStatus();
                });

                // Function to check the status of the reset button
                function checkResetButtonStatus() {
                    if (newPassword_verify && confirmPassword_verify) {
                        $('#resetButton').prop('disabled', false);
                    } else {
                        $('#resetButton').prop('disabled', true);
                    }
                }
            });
</script>
@endsection
