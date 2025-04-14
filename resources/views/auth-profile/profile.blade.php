@extends('common.layouts')

@section('content')

@extends('common.sidebar')
@extends('common.header')
<?php

use App\Models\PermissionAccess;

$user_id = auth()->user()->id;
$common_module_id = PermissionAccess::where('user_id', $user_id)->value('common_module_id');
$moduleIds_permission = json_decode($common_module_id);
$queue_room_access = PermissionAccess::where('user_id', $user_id)->value('queue_room_access');

?>
<link rel="stylesheet" href="{{ asset('asset/css/userProfile.css') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<main id="main" class="bgmain">
<section class="SectionPadding">
        <!-- =======  Section ======= -->
    <div class="container">
        <div class="row mb-3">
            <div class="col-xl-12 col-md-12 d-flex userProfileicon">
            <nav aria-label="breadcrumb" class="QueueBreadCrumb profileBredcrum">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item Homebreadcrumb">
                                <a href="{{ url('dashboard') }}">
                                    <i class="fa fa-home" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ url('profile') }}">My profile</a>
                            </li>
                        </ol>
                    </nav>
            </div>
        </div>

        <!-- nav tabs -->
        <div class="row mb-3">
            <div class="col-xl-12 col-md-12 ">
                <ul class="nav nav-pills navborder">
                    @if(Auth::user()->role == 2 || Auth::user()->role == 3 || Auth::user()->role == 4)
                        @foreach($moduleIds_permission as $module)
                            @if($module->module_id == 2 && $module->permission > 0)
                                <li class="nav-item">
                                    <a href="{{ url('profile') }}"><button class="nav-link active mynavtabs" id="pills-home-tab" type="button">MY PROFILE</button></a>
                                </li>
                            @endif
                            @if($module->module_id == 3 && $module->permission > 0)
                                <li class="nav-item">
                                    <a href="{{ url('staff-access-manage') }}"><button class="nav-link mynavtabs new" type="button">STAFF ACCESS MANAGEMENT</button></a>
                                </li>
                            @endif
                            @if($module->module_id == 4 && $module->permission > 0)
                            <li class="nav-item">
                                <button class="nav-link mynavtabs" type="button">SUBSCRIPTION PLAN</button>
                            </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
                <div class="" id="pills-tabContent">
                    <!-- first tab -->
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            {!! Session::get('success') !!}
                        </div>
                    @elseif(Session::has('error'))
                        <div class="alert alert-danger">
                            {!! Session::get('error') !!}
                        </div>
                    @endif
                    <form action="{{ route('profile.update', $user->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="card card-body">
                            <!-- first row -->
                            <div class="row m-0">
                                <div class="col-md-11 ps-0 pt-2 pb-4">
                                    <div class="LeftGreenborder ps-4">
                                        <h5 class="FormHeading"><b>Company Information</b></h5>
                                        <p class="FormPara">Company ID.</p>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" id="company_ID" name="company_ID" value="{{ $user->id }}" readonly>
                                        </div>

                                        <p class="FormPara">Name of Company:</p>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" id="companyName" name="company_name" value="{{ $user->company_name }}">
                                            <span class="text-danger" id="NameofCompanyError"></span> <!-- JavaScript validation error message -->
                                            @if ($errors->has('company_name'))
                                                <span class="text-danger">{{ $errors->first('company_name') }}</span>
                                            @endif
                                        </div>

                                        <h6 class="FormHeading"><b>Company Address</b></h6>
                                        <div class="d-flex justify-content-between align-items-center blockingdiv ">
                                            <p class="FormPara">Address Line 1.</p>
                                            <div class="col-md-8 mb-2">
                                                <input type="text" class="form-control" id="comAddress1" name="company_address" value="{{ $user->company_address }}" >
                                                <span class="text-danger" id="companyAddressOneError"></span> <!-- JavaScript validation error message -->
                                                @if ($errors->has('company_address'))
                                                    <span class="text-danger">{{ $errors->first('company_address') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center blockingdiv">
                                            <p class="FormPara">Address Line 2.</p>
                                            <div class="col-md-8 mb-2">
                                                <input type="text" class="form-control" id="comAddress2" name="company_address2" value="{{ $user->company_address2 }}">
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center blockingdiv">
                                            <p class="FormPara">Zip Postal Code.</p>
                                            <div class="col-md-8 mb-2">
                                                <input type="text" class="form-control" id="zip" name="company_zip" value="{{ $user->company_zip }}" >
                                                <span class="text-danger" id="ziperror"></span> <!-- JavaScript validation error message -->
                                                @if ($errors->has('company_zip'))
                                                    <span class="text-danger">{{ $errors->first('company_zip') }}</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center blockingdiv">
                                            <p class="FormPara">Country.</p>
                                            <div class="col-md-8 countrydiv">
                                                <select name="country" id="country" class="form-control" >
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country }}" {{ $user->country === $country ? 'selected' : '' }}>{{ $country }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            @if ($errors->has('country'))
                                                    <span class="text-danger">{{ $errors->first('country') }}</span>
                                                @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- second row -->
                            <div class="row m-0 mt-4">
                                <div class="col-md-11 ps-0 pt-2 pb-4">
                                    <div class="LeftGreenborder ps-4">
                                        <h5 class="FormHeading"><b>Contact Person Information</b></h5>
                                        <p class="FormPara">Name</p>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" id="company_person_name" name="company_person_name" value="{{ $user->company_person_name }}" >
                                            <span class="text-danger" id="companyPersonName"></span> <!-- JavaScript validation error message -->
                                            @if ($errors->has('company_person_name'))
                                                <span class="text-danger">{{ $errors->first('company_person_name') }}</span>
                                            @endif
                                        </div>

                                        <p class="FormPara">Email:</p>
                                        <div class="mb-3">
                                            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" readonly>
                                            @if ($errors->has('email'))
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            @endif                                        
                                        </div>
                                        
                                        <p class="FormPara">Mobile.</p>
                                        <div class="d-flex  align-items-center">
                                            <div class="col-md-2 calldiv">
                                                <select name="telephonePrefix" id="telephonePrefix" class="form-control">
                                                    @foreach($telephonePrefix as $prefix)
                                                        <option value="{{ $prefix }}" {{ $user->telephonePrefix === $prefix ? 'selected' : '' }}>
                                                            {{ $prefix }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-10 ">
                                                <input type="number" class="form-control bordernumberdiv" id="company_person_mobile" name="company_person_mobile" value="{{ $user->company_person_mobile }}" >
                                                <span class="text-danger" id="companyPersonMobile"></span> <!-- JavaScript validation error message -->
                                                @if ($errors->has('company_person_mobile'))
                                                    <span class="text-danger">{{ $errors->first('company_person_mobile') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- third row button -->
                            <div class="row m-0 mt-2">
                                <div class="col-md-12 ps-0 pt-2 pb-4">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <button class="btn bsb-btn-2xl subbtnbtn submitbtn d-flex align-items-center justify-content-center" type="submit" id="savesubmitButton">
                                            Save
                                            <span class="material-symbols-outlined ms-2">save</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                         <!-- third row button end -->

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</main>


<!-- Include jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>

    $(document).ready(function() {
        $('#telephonePrefix').select2();
        $("#country").select2({
            //templateResult: formatState
        });
    });
</script>

<script>
    $(document).ready(function() {
        
        checkSubmitButtonStatus();

        $('#companyName').on('keyup', function() {
            validateField($(this), 'NameofCompanyError', 'Company Name is required.');
            checkSubmitButtonStatus();
        });

        
        $('#comAddress1').on('keyup', function() {
            validateField($(this), 'companyAddressOneError', 'Company Address is required.');
            checkSubmitButtonStatus();
        });

        
        $('#zip').on('keyup', function() {
            validateZipField($(this), 'ziperror', 'Zip portal code is required.');
            checkSubmitButtonStatus();
        });

        
        $('#company_person_name').on('keyup', function() {
            validateField($(this), 'companyPersonName', 'Name is required.');
            checkSubmitButtonStatus();
        });

        
        $('#company_person_mobile').on('keyup', function() {
            validateField($(this), 'companyPersonMobile', 'Mobile Number is required .');
            checkSubmitButtonStatus();
        });

        function validateField(element, errorSpanId, errorMessage) {
            var value = element.val();
            var errorSpan = $('#' + errorSpanId);

            if (value.trim() === '') {
                // Display an error message or take any other action
                element.addClass('error');
                errorSpan.text(errorMessage);
            } else {
                // Remove the error class if the field is not empty
                element.removeClass('error');
                errorSpan.text('');
            }
        }

        function validateZipField(element, errorSpanId, errorMessage) {
            var value = element.val();
            var errorSpan = $('#' + errorSpanId);

            
            if (value.trim() === '' || /\./.test(value)) {
               
                element.addClass('error');
                errorSpan.text(errorMessage);
            } else {
                
                element.removeClass('error');
                errorSpan.text('');
            }
        }

        // function validateMobileNumber(element, errorSpanId, errorMessage) {
        //     var value = element.val();
        //     var errorSpan = $('#' + errorSpanId);

        //     var cleanValue = value.replace(/\D/g, ''); // Remove non-digit characters

        //     if (cleanValue.length !== 10) {
        //         // Display an error message or take any other action
        //         element.addClass('error');
        //         errorSpan.text(errorMessage);
        //     } else {
        //         // Remove the error class if the field is not empty and has 10 digits
        //         element.removeClass('error');
        //         errorSpan.text('');
        //     }
        // }

        function checkSubmitButtonStatus() {
            // Check the status of all relevant fields
            var companyName = $('#companyName').val().trim();
            var comAddress = $('#comAddress1').val().trim();
            var zip = $('#zip').val().trim();
            var personName = $('#company_person_name').val().trim();
            //var mobile = $('#company_person_mobile').val().replace(/\D/g, ''); 

            // Enable or disable the button based on the conditions
            if (companyName !== '' && comAddress !== '' && zip !== '' && personName !== '') {
                $('#savesubmitButton').prop('disabled', false);
            } else {
                $('#savesubmitButton').prop('disabled', true);
            }
        }
    });
</script>

@endsection