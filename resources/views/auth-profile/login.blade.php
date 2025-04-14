@extends('common.layouts')

@section('content')

<div class="mainbg">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12  col-lg-12 vh-100 d-flex justify-content-center align-items-center">
                <div class="loginSectionDiv">
                <div class="alertlogin w-100">
                    @if(Session::has('success'))
                    <div class="alert alert-success alert-dismissible">
                        {!! Session::get('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @elseif(Session::has('error'))
                    <div class="alert alert-danger alert-dismissible">
                        {!! Session::get('error') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                </div>

                    <div class="card cardwidth  p-2" style="background-color: #FFFFFF;">
                    <div class="card-body mx-auto d-flex justify-content-center">
                            <img src="{{ asset('asset/img/Logo=with-text.png')}}">
                        </div>
                        <div class="container mt-3">
                        <form action="{{ route('authenticate') }}" method="post" id="loginForm">
                            @csrf
                            <h5 class="fw-bold">Login</h5>
                            <div class="mt-4">
                                <div>
                                    <p class="credential">Email</p>
                                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ $_COOKIE['email'] ?? null }}" placeholder="example@mail.com" required>
                                    <span class="text-danger" id="emailError"></span> <!-- JavaScript validation error message -->
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                         <!-- Blade validation error message -->
                                    @endif
                                </div>

                                    <div class="password-container mt-4">
                                        <p class="credential">Password
                                            <i class="bx bx-hide password-toggle float-end" id="password-toggle"></i>
                                        </p>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" value="{{ $_COOKIE['password'] ?? null }}" placeholder="●●●●●●" required>
                                        <span class="text-danger" id="passwordError"></span> <!-- JavaScript validation error message -->
                                        @if ($errors->has('password'))
                                            <span class="text-danger">{{ $errors->first('password') }}</span> <!-- Blade validation error message -->
                                        @endif
                                    </div>
                  

                                <div class="col-12">
                                    <div class="form-check mt-4">

                                          <input type="checkbox" name="remember" id="remember" class="form-check-input" @if(isset($_COOKIE['password']) && isset($_COOKIE['email'])) checked="" @endif>
                                         <label for="remember" class="form-check-label">Remember Me</label>
                                       <!--  <input class="form-check-input" type="checkbox" name="remember_me" value=""
                                            id="remember_me">
                                        <label class="form-check-label credential d-flex justify-content-between" for="remember_me" style="line-height: 24px;"> -->
                                            <a href="#" class="labelcheck"> <a href="{{ route('password.request') }}" class="labelcheck">Forgot Password.</a>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="d-grid mt-4 ">
                                        <button class="btn bsb-btn-2xl submitbtn" type="button" value="Login" id="submitButton">Login QueueMaster</button>
                                           
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <p class="credential">Dont't have account yet? <a href="{{ url('register') }}" class="labelcheck">New Account</a></p>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
// $(document).ready(function() {
//     var email_verify = 0;
//     var pwd_verify = 0;

//     // Disable submit button by default
//     $('#submitButton').prop('disabled', true);

//     // Email validation
//     $('#email').on('keyup', function() {
//         var email = $(this).val().trim();
//         var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//         if (email.length > 0 && emailPattern.test(email)) {
//             console.log('Valid email address: ' + email);
//             email_verify = 1;
//             $('#emailError').text('');
//         } else {
//             console.log('Invalid email address: ' + email);
//             email_verify = 0;
//             $('#emailError').text('Invalid email address');
//         }
//         checkSubmitButtonStatus();
//     });

//     // Password validation
    // $('#password').on('keyup', function() {
    //     var password = $(this).val().trim();
    //     var passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/;
    //     if (passwordPattern.test(password)) {
    //         console.log('Valid password: ' + password);
    //         pwd_verify = 1;
    //         $('#passwordError').text('');
    //     } else {
    //         console.log('Invalid password');
    //         pwd_verify = 0;
    //         $('#passwordError').text('Password must be at least 8 characters long and contain at least one letter, one number, and one special character.');
    //     }
    //     checkSubmitButtonStatus();
        
//     $('#password').on('keyup', function() {
//     var password = $(this).val().trim();
//     var isValid = password.length === 8;
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
//         function checkSubmitButtonStatus() {
//         if (email_verify && pwd_verify) {
//             $('#submitButton').prop('disabled', false);
//         } else {
//             $('#submitButton').prop('disabled', true);
//         }
//     }
// });
$(document).ready(function() {
	 // Disable submit button by default
    $('#submitButton').prop('disabled', true);
    var email_verify = 0;
    var pwd_verify = 0;
    var email_data = $('#email').val();
    var pass_data = $('#password').val();
    if(!email_data && !pass_data)
    {
    	  email_verify = 1;
          pwd_verify = 1;
       checkSubmitButtonStatus();	
    }
    
   

    // // Email validation
    // $('#email').on('keyup', function() {
    //     var email = $(this).val().trim();
    //     var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    //     if (email.length > 0 && emailPattern.test(email)) {
    //         console.log('Valid email address: ' + email);
    //         email_verify = 1;
    //         $('#emailError').text('');
    //     } else {
    //         console.log('Invalid email address: ' + email);
    //         email_verify = 0;
    //         $('#emailError').text('Invalid email address');
    //     }
    //     checkSubmitButtonStatus();
    // });

    // // Password validation
    // $('#password').on('keyup', function() {
    //     var password = $(this).val().trim();
    //     var passwordPattern = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/;
    //     if (passwordPattern.test(password)) {
    //         console.log('Valid password: ' + password);
    //         pwd_verify = 1;
    //         $('#passwordError').text('');
    //     } else {
    //         console.log('Invalid password');
    //         pwd_verify = 0;
    //         $('#passwordError').text('Password must be at least 8 characters long and contain at least one letter, one number, and one special character.');
    //     }
    //     checkSubmitButtonStatus();
    // });


    function validateEmailPassword() {
    // Email validation
    var email = $('#email').val().trim();
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

    // Password validation
    var password = $('#password').val().trim();
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
}


$('#email').on('keyup', function() {
    validateEmailPassword();
});

$('#password').on('keyup', function() {
    validateEmailPassword();
});


   var email = $('#email').val().trim();
    var password = $('#password').val().trim();
    if (email.length > 0 && password.length > 0) {
        validateEmailPassword();
    }




    function checkSubmitButtonStatus() {
         
        if (email_verify && pwd_verify) {
            $('#submitButton').prop('disabled', false);
        } else {
            $('#submitButton').prop('disabled', true);
        }
    }

      $('#submitButton').on('click', function(event) {
       
        checkSubmitButtonStatus(); // Assuming this checks the status of the button or other fields

        if (!(email_verify && pwd_verify)) {
            event.preventDefault(); // Prevent form submission if validations fail
        } else {
            // Disable the submit button to prevent multiple clicks
            $('#submitButton').prop('disabled', true);
            // Submit the form explicitly
            $('#loginForm').submit();
        }
    });

});

$(document).ready(function() {
        // Automatically close the alert after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
                // Clear the session data
                clearSession();
            });
        }, 3000);

        // Clear session data function
        function clearSession() {
            $.ajax({
                url: "{{ route('clear-session') }}", // Replace with your route to clear session
                method: "POST",
                data: {
                    "_token": "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log("Session cleared");
                },
                error: function(xhr, status, error) {
                    console.log("Error clearing session: " + error);
                }
            });
        }
    });
</script>

@endsection
