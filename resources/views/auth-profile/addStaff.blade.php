@extends('common.layouts')

@section('content')

@extends('common.sidebar')
@extends('common.header')

<link rel="stylesheet" href="{{ asset('asset/css/userProfile.css') }}">

<main id="main" class="bgmain">
    <section class="SectionPadding">
        <div class="container">
            <div class="row mb-3">
                <div class="col-xl-8 col-md-12 d-flex userProfileicon">

                <nav aria-label="breadcrumb" class="QueueBreadCrumb profileBredcrum">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item Homebreadcrumb">
                                <a href="{{ url('dashboard') }}">
                                    <i class="fa fa-home" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ url('staff-access-manage') }}">Staff Access Management</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ url('addStaff') }}">Add Staff</a>
                            </li>
                        </ol>
                    </nav>
                   
                </div>
            </div>

            <!-- card -->
            <div class=" card card-body">
                @if(Session::has('success'))
                <div class="alert alert-success">
                    {!! Session::get('success') !!}
                </div>
                @elseif(Session::has('error'))
                <div class="alert alert-danger">
                    {!! Session::get('error') !!}
                </div>
                @endif
                <form action="{{ route('saveStaff') }}" method="post">
                    @csrf
                    <div class="row m-0">
                        <div class="col-md-11 ps-0 pt-2 pb-4">
                            <div class="LeftGreenborder ps-4">
                                <h5 class="FormHeading"><b>Staff Information</b></h5>
                                <p class="FormPara">Name</p>
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="staff_name" name="staff_name" value="{{ old('staff_name') }}">
                                    <span class="text-danger" id="nameError"></span> <!-- JavaScript validation error message -->
                                    @if ($errors->has('staff_name'))
                                    <span class="text-danger">{{ $errors->first('staff_name') }}</span>
                                    @endif
                                </div>

                                <p class="FormPara">Email</p>
                                <div class="mb-3">
                                    <input type="email" class="form-control" id="staff_email" name="staff_email" value="{{ old('staff_email') }}">
                                    <span class="text-danger" id="emailError"></span> <!-- JavaScript validation error message -->
                                    @if ($errors->has('staff_email'))
                                    <span class="text-danger">{{ $errors->first('staff_email') }}</span>
                                    @endif
                                </div>

                                <p class="FormPara">Password</p>
                                <div class="mb-3">
                                    <input type="password" class="form-control" id="staff_password" name="staff_password">
                                    <span class="text-danger" id="passwordError"></span> <!-- JavaScript validation error message -->
                                    @if ($errors->has('staff_password'))
                                    <span class="text-danger">{{ $errors->first('staff_password') }}</span>
                                    @endif
                                </div>

                                <p class="FormPara">Status</p>
                                <div class="mb-3">
                                    <select class="form-select" id="staff_status" name="staff_status">
                                        <option value="0">Inactive</option>
                                        <option value="1">Active</option>
                                    </select>
                                    @if ($errors->has('staff_status'))
                                    <span class="text-danger">{{ $errors->first('staff_status') }}</span>
                                    @endif
                                </div>


                            </div>
                        </div>
                    </div>

                    <!-- second row -->
                    <div class="row m-0">
                        <div class="col-md-12 ps-0 pt-2 pb-4">
                            <div class="LeftGreenborder ps-4">
                                <h4 class="FormHeading"><b>Permission</b></h4>
                                <div class="table-responsive">
                                    <table class="table mb-0">
                                        <thead class="table">
                                            <tr>
                                                <!-- Table headings for permissions -->
                                                <th class="border-0 py-0 tableheadFirst">
                                                    <h6 class="addstaffheading"></h6>
                                                </th>
                                                <th class="border-0 py-0">
                                                    <h6 class="addstaffheading">No Access</h6>
                                                </th>
                                                <th class="border-0 py-0">
                                                    <h6 class="addstaffheading">Read Only</h6>
                                                </th>
                                                <th class="border-0 py-0">
                                                    <h6 class="addstaffheading">Full Access</h6>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- @foreach($commonModules as $commonModule)
                                            <tr>
                                                <td class="border-0 d-flex">
                                                    <div>
                                                        
                                                        <h3 class="mt-2 mb-0 Ruletextdata">{{ $commonModule->name }}</h3>
                                                    </div>
                                                </td>

                                                <td class="border-0 text-center">
                                                    <input type="radio" name="common_{{ $commonModule->id }}" value="no_access" id="no_access_{{ $commonModule->id }}" checked>
                                                </td>
                                                <td class="border-0 text-center">
                                                    <input type="radio" name="common_{{ $commonModule->id }}" value="read_only" id="read_only_{{ $commonModule->id }}">
                                                </td>
                                                <td class="border-0 text-center">
                                                    <input type="radio" name="common_{{ $commonModule->id }}" value="full_access" id="full_access_{{ $commonModule->id }}">
                                                </td>
                                            </tr>
                                            @endforeach -->

                                            @foreach($rooms as $room)
                                            
                                            <tr>
                                                <td colspan="4">
                                                    <h4 class="mt-2 mb-0 FormHeading"><b>{{ $room->queue_room_name }}</b></h4>
                                                </td>
                                            </tr>
                                            <!-- Nested foreach loop for modules -->
                                            @php $i = 1; @endphp
                                            @foreach($modules as $module)
                                            <!-- Check if the module belongs to the current room -->
                                            <tr>
                                                <td class="border-0 d-flex">
                                                    <div>
                                                        <!-- Display module name -->
                                                        <h6 class="mt-2 mb-0 Ruletextdata">{{ $module->name }}</h6>
                                                    </div>
                                                </td>
                                                <!-- Radio buttons for permissions -->
                                                <!-- <td class="border-0 text-center">
                                                    <input type="radio" name="permission_{{ $room->id }}{{ $module->id }}" value="no_access" id="no_access_{{ $room->id }}_{{ $module->id }}_{{ $i }}">
                                                </td> -->
                                                <td class="border-0 text-center">
                                                    <input type="radio" name="permission_{{ $room->id }}{{ $module->id }}" value="no_access" id="no_access_{{ $room->id }}_{{ $module->id }}_{{ $i }}" checked>
                                                </td>
                                                <td class="border-0 text-center">
                                                    <input type="radio" name="permission_{{ $room->id }}{{ $module->id }}" value="read_only" id="read_only_{{ $room->id }}_{{ $module->id }}_{{ $i }}">
                                                </td>
                                                <td class="border-0 text-center">
                                                    <input type="radio" name="permission_{{ $room->id }}{{ $module->id }}" value="full_access" id="full_access_{{ $room->id }}_{{ $module->id }}_{{ $i }}">
                                                </td>
                                            </tr>
                                            @php $i++; @endphp
                                            @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- button -->
                    <div class="row m-0 mt-2">
                        <div class="col-md-12 ps-0 pt-2 pb-4 ">
                            <div class=" d-flex align-items-center justify-content-end">
                                <button class="btn bsb-btn-2xl subbtnbtn submitbtn d-flex align-items-center justify-content-center" type="submit"id="savesubmitButton">
                                    Save
                                    <span class="material-symbols-outlined ms-2">save</span>
                                </button>

                            </div>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </section>
</main>

<script src="{{ asset('asset/js/userProfile.js') }}" type="text/javascript"></script>

<script>
$(document).ready(function() {
    var email_verify = 0;
    var pwd_verify = 0;
    var name_verify = 0;


    // Disable submit button by default
    $('#savesubmitButton').prop('disabled', true);

    // Email validation
    $('#staff_email').on('keyup', function() {
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
    $('#staff_password').on('keyup', function() {
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

      // Staff Name validation
      $('#staff_name').on('keyup', function() {
            var name = $(this).val().trim();
            if (name.length > 0) {
                console.log('Valid name: ' + name);
                name_verify = 1;
                $('#nameError').text('');
            } else {
                console.log('Invalid name');
                name_verify = 0;
                $('#nameError').text('Name is required.');
            }
            checkSubmitButtonStatus();
        });



    function checkSubmitButtonStatus() {
        if (email_verify && pwd_verify && name_verify) {
            $('#savesubmitButton').prop('disabled', false);
        } else {
            $('#savesubmitButton').prop('disabled', true);
        }
    }
});
</script>

@endsection