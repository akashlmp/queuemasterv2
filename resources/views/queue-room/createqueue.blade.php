@extends('common.layouts')

@section('content')

    @extends('common.sidebar')

    @extends('common.header')
    <?php
    
    use App\Models\admin\SubscriptionPlan;
    
    $user_plan_id = auth()->user()->subscription_plan_id;
    
    $QueueRoompermission = SubscriptionPlan::where('id', $user_plan_id)
    
        ->select('setup_bypass', 'setup_pre_queue', 'setup_sms', 'setup_email', 'maximum_traffic')
    
        ->first();
    
    ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('asset/css/queueRoomCreate.css') }}">

    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-datetimepicker.min.css') }}">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Select2 CSS -->

    <style>
        .hide-element {

            display: none !important;

        } 
       
    </style>
                                                
            <style>
                                /* Default unchecked state */
                    .form-check-input {
                        width: 40px;
                        height: 20px;
                        background-color: gray !important;
                        border: none;
                        position: relative;
                        appearance: none;
                        border-radius: 20px;
                        cursor: pointer;
                        transition: background-color 0.3s ease-in-out;
                    }

                    /* White inner circle (toggle knob) */
                    .form-check-input::before {
                        content: "";
                        position: absolute;
                        width: 16px;
                        height: 16px;
                        background-color: white;
                        border-radius: 50%;
                        top: 2px;
                        left: 2px;
                        transition: transform 0.3s ease-in-out;
                    }

                    /* Checked state */
                    .form-check-input:checked {
                        background-color: #159aa1 !important; /* Blue when checked */
                    }

                    /* Move the knob when checked */
                    .form-check-input:checked::before {
                        transform: translateX(13px);
                    }

                    .disabled-field {
                     opacity: 0.5;
                    pointer-events: none;
                        }

            </style>
            <style>
        .btn-outline-custom-cancel {
            background-color: white;
            border: 2px solid red;
            color: red;
        }
    
        .btn-outline-custom-cancel:hover {
            background-color: red;
            color: white;
        }
    
        .btn-outline-custom-confirm {
            background-color: white;
            border: 2px solid teal;
            color: teal;
        }
    
        .btn-outline-custom-confirm:hover {
            background-color: teal;
            color: white;
        }
    
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .nav-link.active{
            border-bottom: 1px solid #159AA1 !important;
            color: #159AA1 !important;
            border: 0
        }
        .nav-link:hover{
            border: 0;
            color: #159AA1
        }
        .nav-link{
            color: black
        }
        .editBtn{
            background: transparent ;
            font-family: Arial;
            font-size: 20px;
            font-weight: 700;
            line-height: 23px;
            letter-spacing: 0.02em;
            text-align: left;
            padding: 10px 20px 10px 20px;
            border: 2px solid #159AA1;
            color: #159aa1;
            cursor: pointer;
}
    </style>

    <?php //dd(Session::all()); die;
    ?>

    <main id="main" class="bgmain">
        <section class="SectionPadding">

            <!-- =======  Section ======= -->

            <div class="container">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <nav aria-label="breadcrumb" class="QueueBreadCrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item Homebreadcrumb"><a href="{{ url('dashboard') }}"><i
                                            class="fa fa-home" aria-hidden="true"></i></a>

                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('queue-room-view') }}">Queue Room</a></li>

                                <li class="breadcrumb-item active" aria-current="page"><a
                                        href="{{ url('create-queue') }}">Create QueueRoom</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row queueTabsdata">
                    <div class="col-md-12">
                        <ul class="nav nav-tabs QueueingTabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <button class="nav-links active" id="step1-tab" data-bs-target="#step1" data-bs-toggle="tab"
                                    type="button" role="tab" aria-controls="step1" aria-selected="true">BASIC
                                    INFO</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-links" id="step2-tab" type="button" role="tab" aria-controls="step2"
                                    aria-selected="false" onclick="validateStep1()">CONFIGURATION</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-links" id="step3-tab" type="button" role="tab" aria-controls="step3"
                                    aria-selected="false" onclick="validateStep2()">DESIGN</button>
                            </li>

                            <li class="nav-item">
                                <button class="nav-links" id="step4-tab" type="button" role="tab" aria-controls="step4"
                                    aria-selected="false" onclick="validateStep3()">SMS / EMAIL</button>
                            </li>

                        </ul>

                        <div class="tab-content card py-3 px-2" id="myTabContent">

                            <!-- <form class="px-2" method="POST" action="{{ route('queue_setup') }}" enctype="multipart/form-data"> -->

                            <form class="px-2" id="main_queue_room" method="POST" action="{{ route('queue_setup') }}"
                                enctype="multipart/form-data">
                                @csrf

                                <!-- Step  1 -->
                                
                                    <div class="tab-pane fade show active" id="step1" role="tabpanel"
                                        aria-labelledby="step1-tab">
                                        <div class="row m-0">
                                            <div class="col-md-12 ps-0 pt-2 pb-5">
                                            <!-- Queue Room Toggle -->
                                        <div class="LeftRedborder ps-4">
                                            <h5 class="FormHeading"><b>Queue Room</b></h5>
                                            <p class="FormPara">Here is the place to decide whether to turn off the queue room.</p>
                                            <div class="mb-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span>Off</span>
                                                    <div class="form-check form-switch ps-0">
                                                        <input class="form-check-input ms-0 mt-0" type="checkbox" role="switch" id="flexSwitchCheckChecked" >
                                                    </div>
                                                    <span>On</span>
                                                </div>
                                                
                                                <div id="roomname-error" class="error-msg text-danger pt-2"></div>
                                                @if ($errors->has('roomname'))
                                                    <span class="text-danger">{{ $errors->first('roomname') }}</span>
                                                @endif
                                            </div>
                                        </div>
                            
                                            <br><br>
                                        <!-- Queue Room Name Section -->
                                        <div class="col-md-12 ps-0 pt-2 pb-5" id="queueRoomContainer">
                                            <div class="LeftRedborder ps-4">
                                                <h5 class="FormHeading"><b>Queue Room Name</b></h5>
                                                <p class="FormPara">This is the name of your Queue room show on the homepage</p>
                                                <div class="mb-3">
                                                    <input type="text" disabled
                                                        class="form-control FormInputBox @error('roomname') is-invalid @enderror"
                                                        id="roomname" name="roomname"
                                                        value="{{ old('roomname', $session_data['roomname'] ?? '') }}"
                                                        required >
                                                    <div id="roomname-error" class="error-msg text-danger pt-2"></div>
                                                    @if ($errors->has('roomname'))
                                                        <span class="text-danger">{{ $errors->first('roomname') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Queue Room Icon Section -->
                                        <div class="col-md-12 ps-0 pt-2 pb-5" id="queueRoomIconContainer">
                                            <div class="LeftGreenborder ps-4">
                                                <h5 class="FormHeading"><b>Queue Room Icon</b></h5>
                                                <p class="FormPara">This is the icone of your Queue room show on the homepage</p>
                                                <b>Current icon:</b>
                                                <br><br>
                                                <div class="mb-3">
                                                    @if (!empty($session_file_data['file_paths']))
                                                        <img src="{{ asset('images/' . $session_file_data['file_paths']) }}"
                                                            class="img-responsive iconQueueImg" id="queueIconPreview" alt="" /> 
                                                        <input type="file" class="form-control FormInputBox"
                                                            id="iconQueue" name="queue_icon" hidden>
                                                    @else
                                                        <img src="{{ asset('asset/img/blank.png') }}"
                                                            class="img-responsive iconQueueImg" id="queueIconPreview" alt="" />

                                                        <input type="file" class="form-control FormInputBox"
                                                            id="iconQueue" name="queue_icon" hidden 
                                                            accept=".jpg,.jpeg,.png">
                                                    @endif
                                                    
                                                    {{-- <label for="iconQueue" id="QueueIconLabel" class="d-flex align-items-center iconQueueLabel mt-3">
                                                        <span class="material-symbols-outlined pe-2"> upload</span> Upload
                                                    </label> --}}
                                                </div>
                                                <input type="file" id="iconQueue" style="display: none;">
                                            <button type="button" class="btn btn-primary editBtn" id="QueueIconLabel">
                                                <i class="fa-solid fa-arrow-up-from-bracket pe-2"></i> Upload
                                                    </button>
                                                <div id="iconQueue-error" class="error-msg text-danger pt-2"></div>
                                            </div>
                                                </div>

                                            <!-- JavaScript to Enable/Disable Fields -->
                                    

                                                <!-- CSS for Smooth Effect -->
                            


                                            <div class="col-md-12 ps-0 pt-2 pb-5" id="queueTypeContainer">
                                                <div class="LeftGreenborder ps-4">
                                                    <h5 class="FormHeading"><b>Which type you need to create queue?</b></h5>
                                                    <p class="FormPara">The type is for your own reference only.</p>
                                                    <div class="mb-3">
                                                        <div class="custom-dropdown">
                                                            <div class="Timzone selectdrop">
                                                                <select class="form-control FormInputBox TimezoneSelect custom-select select2" id="queuetype" name="queuetype" >
                                                                    <option value="" selected="selected">Select One</option>
                                                                    <option value="onetime">One-time</option>
                                                                    <option value="fulltime">24/7 queues</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 ps-0 pt-2 pb-5">

                                                <div class="LeftGreenborder ps-4" id="timezoneContainer">
                                                    <h5 class="FormHeading"><b>Queue Room Time Zone</b></h5>
                                                    <p class="FormPara">This would affect the start , end time, and  reporting.</p>
                                                
                                                    <div class="mb-3">
                                                        <div class="custom-dropdown">
                                                            <div class="Timzone selectdrop">
                                                                <select class="form-control FormInputBox TimezoneSelect custom-select select2"
                                                                        id="timezoneselect" name="timezone">
                                                                    <option value="" selected="selected">Select One</option>
                                                                    <?php
                                                                    $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                                                    foreach ($timezones as $timezone) {
                                                                        $tz = new DateTimeZone($timezone);
                                                                        $offset = $tz->getOffset(new DateTime()) / 3600;
                                                                        $offsetFormatted = sprintf('%+03d:%02d', $offset, (abs($offset) * 60) % 60);
                                                                        $timezoneName = str_replace(['/', '_'], [' - ', ' '], $timezone);
                                                                        $selected = ($session_data['timezone'] ?? '') == "$offsetFormatted|$timezoneName" ? 'selected="selected"' : '';
                                                                        echo "<option $selected data-timezone=\"$timezone\" value=\"$offsetFormatted|$timezoneName\">
                                                                                $timezoneName (GMT $offsetFormatted)
                                                                            </option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                
                                                        <div id="timezoneselect-error" class="error-msg text-danger pt-2"></div>
                                                
                                                        @if ($errors->has('timezone'))
                                                            <span class="text-danger">{{ $errors->first('timezone') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                

                                                    </div>

                                                        <div class="col-md-12 ps-0 pt-2 pb-5">

                                                <div class="LeftGreenborder ps-4" id="queueSectionContainer">

                                                    <h5 class="FormHeading"><b>Queue Room Start Time</b></h5>

                                                    <p>Pre-Queuening is dedicated to prevent website crashes from early demand and delivers online fairness.</p>
                                                    <div
                                                        class="d-flex align-items-center justify-content-start QueueDataCheckboxes FirstQueueDataCheckboxes">


                                                        <div class="form-check  ps-0 ">

                                                            <input id="startNowSelect" type="radio" id="Startnow"
                                                                class="queueCheckBox FormInputBox startCheckBox"
                                                                name="startTime" value="1"
                                                                {{ old('startTime', $session_data['startTime'] ?? '') == '1' ? 'checked' : '' }}>
                                                                
                                                            <label for="Startnow" class="form-check-label">Start now</label>

                                                        </div>

                                                        <!-- Hidden input field to store the timezone -->

                                                        <input type="hidden" id="dateValue" name="startDateValue"
                                                            value="{{ $session_data['startDateValue'] ?? '' }}">

                                                        <input type="hidden" id="timeValue" name="startTimeValue"
                                                            value="{{ $session_data['startTimeValue'] ?? '' }}">

                                                        <div class="form-check  ">

                                                            <input type="radio" id="CustomDateTime"
                                                                class="queueCheckBox startCheckBox" name="startTime"
                                                                value="0"
                                                                {{ old('startTime', $session_data['startTime'] ?? '') == '0' ? 'checked' : '' }}>

                                                            <label for="CustomDateTime" class="form-check-label"> Custom date
                                                                and time</label>

                                                        </div>

                                                    </div>

                                                    <div id="radio-error"
                                                        class="error-msg text-danger pt-2 startCheckBox-error"></div>

                                                    @if ($errors->has('startTime'))
                                                        <span class="text-danger">{{ $errors->first('startTime') }}</span>
                                                    @endif

                                                    <div id="CustomDateTimeId" class="CustomDateTime pt-2"
                                                        <?php echo ($session_data['startTime'] ?? '') == 0 ? 'style="display:block"' : ''; ?>>

                                                        <div class="row">

                                                            <div class="col-md-3 col-sm-3 col-xs-12 mb-3">

                                                                <!-- <label for="datepicker">Select Date:</label> -->

                                                                <div class="DateTimeIconBox">

                                                                    <span
                                                                        class="material-symbols-outlined DateTimeIcon">calendar_month</span>

                                                                    <?php if (!empty($session_data['custom_start_date'])) {
                                                                        echo '<script>var custom_start_date = 1;</script>';
                                                                    } ?>

                                                                    <input type="text" id="datepicker"
                                                                        class="form-control FormInputBox datepicker-input"
                                                                        name="custom_start_date"        placeholder="Select Date"
                                                                        value="{{ old('custom_start_date', $session_data['custom_start_date'] ?? '') }}">

                                                                    <span class="material-symbols-outlined DateTimeRightIcon"
                                                                        value="">

                                                                        expand_more

                                                                    </span>

                                                                </div>

                                                            </div>

                                                            <div class="col-md-3 col-sm-3 col-xs-12">

                                                                <!-- <label for="datepicker">Select Date:</label> -->

                                                                <div class="inputfield inputfieldTimepikcer DateTimeIconBox">

                                                                    <span class="material-symbols-outlined DateTimeIcon">

                                                                        pace

                                                                    </span>

                                                                    <input type="time" id="timePicker"
                                                                        class="form-control form-select FormInputBox"
                                                                        name="custom_start_time" 
                                                                        value="{{ old('custom_start_time', $session_data['custom_start_time'] ?? '') }}">

                                                                    <span class="material-symbols-outlined DateTimeRightIcon">

                                                                        expand_more

                                                                    </span>

                                                                </div>

                                                            </div>



                                                            <input type="hidden" id="convertedDateTime"
                                                                name="convertedstartDateTime"
                                                                value="{{ old('convertedstartDateTime', $session_data['convertedstartDateTime'] ?? '') }}">

                                                            <input type="hidden" id="startepochTime" name="startepochTime"
                                                                value="{{ old('startepochTime', $session_data['startepochTime'] ?? '') }}">

                                                            <div class="col-md-6 col-sm-6 col-xs-0">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br><br>
                                                    <h5 class="FormHeading"><b>Queue Room End Time</b></h5> 
                                                    <p>Pre-Queuening is dedicated to prevent website crashes from early demand and delivers online fairness.</p>
                                                    <div
                                                        class="d-flex align-items-center justify-content-start QueueDataCheckboxes SecQueueDataCheckboxes">
                                                        {{-- <div class="form-check mb-3 ">

                                                            <input type="radio" id="QueuingEnds"
                                                                class="queueCheckBox endCheckBox" name="endTime"
                                                                value="1"
                                                                {{ old('endTime', $session_data['endTime'] ?? '') == '1' ? 'checked' : '' }}>

                                                            <label for="QueuingEnds" class="form-check-label">End now</label>
                                                        </div> --}}

                                                        <div class="form-check mb-3 ps-0">

                                                            <input type="radio" id="Customdateandtime"
                                                                class="queueCheckBox endCheckBox" name="endTime"
                                                                value="0"
                                                                {{ old('endTime', $session_data['endTime'] ?? '') == '0' ? 'checked' : '' }}>

                                                            <label for="Customdateandtime" class="form-check-label">Custom
                                                                date and time</label>

                                                        </div>

                                                        <div class="form-check mb-3">

                                                            <input type="radio" id="HaveEndDateTime"
                                                                class="queueCheckBox endCheckBox" name="endTime"
                                                                value="2"
                                                                {{ old('endTime', $session_data['endTime'] ?? '') == '2' ? 'checked' : '' }}>

                                                            <label for="HaveEndDateTime" class="form-check-label">Do not have
                                                                end date and time</label>

                                                        </div>

                                                    </div>

                                                    <div id="radioSec-error" class="error-msg text-danger pt-2"></div>

                                                    <div id="EndCustomDateTimeId" class="CustomDateTime" <?php echo ($session_data['endTime'] ?? '') == 0 ? 'style="display:block"' : ''; ?>>

                                                        <div class="row">

                                                            <div class="col-md-3 col-sm-3 col-xs-12">

                                                                <!-- <label for="datepicker">Select Date:</label> -->

                                                                <div class="DateTimeIconBox">

                                                                    <span
                                                                        class="material-symbols-outlined DateTimeIcon">calendar_month</span>

                                                                    <?php if (!empty($session_data['custom_end_date'])) {
                                                                        echo '<script>var custom_end_date = 1;</script>';
                                                                    } ?>

                                                                    <input type="text" id="Enddatepicker"
                                                                        class="form-control FormInputBox datepicker-input"
                                                                        name="custom_end_date"
                                                                        value="{{ old('custom_end_date', $session_data['custom_end_date'] ?? '') }}">

                                                                    <span class="material-symbols-outlined DateTimeRightIcon">

                                                                        expand_more

                                                                    </span>

                                                                </div>

                                                            </div>

                                                            <div class="col-md-3 col-sm-3 col-xs-12">

                                                                <!-- <label for="datepicker">Select Date:</label> -->

                                                                <div class="inputfield inputfieldTimepikcer DateTimeIconBox">

                                                                    <span class="material-symbols-outlined DateTimeIcon">

                                                                        pace

                                                                    </span>

                                                                    <input type="time" id="EndtimePicker"
                                                                        class="form-control form-select FormInputBox"
                                                                        name="custom_end_time"
                                                                        value="{{ old('custom_end_time', $session_data['custom_end_time'] ?? '') }}">

                                                                    <span class="material-symbols-outlined DateTimeRightIcon">

                                                                        expand_more

                                                                    </span>

                                                                </div>

                                                            </div>

                                                            <div class="col-md-6 col-sm-6 col-xs-0">



                                                            </div>

                                                        </div>

                                                    </div>

                                                    <input type="hidden" id="convertedEndDateTime"
                                                        name="convertedEndDateTime"
                                                        value="{{ old('convertedEndDateTime', $session_data['convertedEndDateTime'] ?? '') }}">

                                                    <input type="hidden" id="epochEndTime" name="epochEndTime"
                                                        value="{{ old('epochEndTime', $session_data['epochEndTime'] ?? '') }}">

                                                    @if ($errors->has('endTime'))
                                                        <span class="text-danger">{{ $errors->first('endTime') }}</span>
                                                    @endif

                                                </div>

                                            </div>

                                     

                                            <div class="row">

                                                <div class="col-md-12 d-flex text-end justify-content-end StepFormButtonsGrp">
        
                                                    <button
                                                        class="btn btn-primary d-flex align-items-center justify-content-center nextBtn"
                                                        type="button" onclick="handleNextStep()" id="nextbtn">Next <span
                                                            class="material-symbols-outlined ps-2">arrow_forward_ios</span></button>
        
                                                </div>
        
                                            </div>


                                        </div>
                                    </div>
                                
                                    </div>
                                    
                                <!-- Step  2 -->

                                <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">

                                    <div class="row m-0">

                                        <div class="col-md-12 ps-0 pt-2 pb-3">

                                            <div class="LeftGreenborder ps-4">

                                                <h5 class="FormHeading"><b>Queue Room Language</b></h5>



                                                <div class="mb-3">

                                                    <p class="FormPara pt-2 mb-2">Please select</p>

                                                    <div id="qrmTamp" class="error-msg text-danger pt-2"></div>

                                                    <select class="TempCreateSlt" name="template_id"
                                                        id="TempCreateSltId">

                                                        <option value="0" <?php echo ($session_data['template_id'] ?? '') == '' ? 'selected' : ''; ?>>Create new template
                                                        </option>

                                                        <?php
                                                        
                                                        foreach ($queuetemplates as $queuetemplate) {
                                                            echo "<option value='{$queuetemplate->id}' " . ($queuetemplate->id == ($session_data['template_id'] ?? '') ? 'selected' : '') . " >{$queuetemplate->template_name}</option>";
                                                        }
                                                        
                                                        ?>

                                                    </select>

                                                    <div id="TempCreateSltId-error" class="error-msg text-danger pt-2">
                                                    </div>

                                                    @if ($errors->has('template_id'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('template_id') }}</span>
                                                    @endif

                                                </div>

                                                <div class="mb-3 custom-field1  <?php echo empty($session_data['template_id']) ? '' : ' hide-element'; ?>">

                                                    <p class="FormPara"> Template name</p>

                                                    <input type="text" class="form-control FormInputBox"
                                                        id="template_name" name="template_name"
                                                        value="{{ old('template_name', $session_data['template_name'] ?? '') }}">

                                                    <div id="template_name-error" class="error-msg text-danger pt-2">
                                                    </div>


                                                    @if ($errors->has('template_name'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('template_name') }}</span>
                                                    @endif

                                                </div>

                                                <div class="mb-3 custom-field1  <?php echo empty($session_data['template_id']) ? '' : ' hide-element'; ?>">

                                                    <p class="FormPara">Input URL <small>(Please enter a URL-encoded input
                                                            beginning with either http:// or https://)</small>:</p>

                                                    <input type="text" class="form-control FormInputBox"
                                                        id="input_url" name="input_url" placeholder='http://URL.com'
                                                        value="{{ old('input_url', $session_data['input_url'] ?? '') }}">

                                                    <div id="inputUrl" class="error-msg text-danger pt-2"></div>


                                                    @if ($errors->has('input_url'))
                                                        <span class="text-danger">{{ $errors->first('input_url') }}</span>
                                                    @endif

                                                </div>

                                                <div
                                                    class="d-flex align-items-center advanceSettngToggle mb-2 custom-field1  <?php echo empty($session_data['template_id']) ? '' : ' hide-element'; ?>">

                                                    <p class="FormPara mb-0">Advance settings</p>

                                                    <div class="form-checks form-switch">

                                                        <input class="form-check-input"
                                                            <?php echo empty($session_data['AdvanceSettingCheckBox']) ? '' : ' checked '; ?>type="checkbox"
                                                            id="AdvanceSettingCheckBox" name="AdvanceSettingCheckBox"
                                                            value="1">

                                                    </div>

                                                    @if ($errors->has('AdvanceSettingCheckBox'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('AdvanceSettingCheckBox') }}</span>
                                                    @endif

                                                    @if ($errors->has('advancedata'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('advancedata') }}</span>
                                                    @endif

                                                </div>

                                                <div class="AdvanceSettingBox custom-field1 " <?php echo !empty($session_data['AdvanceSettingCheckBox']) ? '' : ' style="display:none;"'; ?>>

                                                    <p class="FormPara">On top of the above setting, visitors would go to
                                                        the queuing room if</p>

                                                    User

                                                    <input type="hidden" name="advancedata" id="advancedata"
                                                        value="">

                                                    <div class="table-responsive">

                                                        <table id="AdvanceSettingTable">

                                                            <tbody>

                                                                <?php if(!empty($session_data['advancedata']['condition_place'])) {

                                                          foreach($session_data['advancedata']['condition_place'] as $key=>$value) {

                                                                   ?>

                                                                <tr>

                                                                    <?php if($key != 0) { ?>

                                                                    <td>

                                                                        <select
                                                                            class="form-select form-control FormInputBox"
                                                                            aria-label="Default select example"
                                                                            name="advancedata[operator][]">

                                                                            <option value="AND" <?php echo $session_data['advancedata']['operator'][$key - 1] == 'AND' ? 'selected' : ''; ?>>AND
                                                                            </option>

                                                                            <option value="OR" <?php echo $session_data['advancedata']['operator'][$key - 1] == 'OR' ? 'selected' : ''; ?>>OR
                                                                            </option>

                                                                        </select>

                                                                    </td>

                                                                    <?php } else { echo '<td></td>';} ?>

                                                                    <td>

                                                                        <select
                                                                            class="form-select form-control FormInputBox"
                                                                            aria-label="Default select example"
                                                                            name="advancedata[condition_place][]">

                                                                            <option value="HOST_NAME" <?php echo $value == 'HOST_NAME' ? 'selected' : ''; ?>>
                                                                                HOST NAME</option>

                                                                            <option value="PAGE_PATH" <?php echo $value == 'PAGE_PATH' ? 'selected' : ''; ?>>
                                                                                PAGE PATH</option>

                                                                            <option value="PAGE_URL" <?php echo $value == 'PAGE_URL' ? 'selected' : ''; ?>>
                                                                                PAGE URL</option>

                                                                        </select>



                                                                    </td>

                                                                    <td>

                                                                        <select
                                                                            class="form-select form-control FormInputBox"
                                                                            name="advancedata[condition][]">

                                                                            <option value="CONTAINS" <?php echo $session_data['advancedata']['condition_place'][$key] == 'CONTAINS' ? 'selected' : ''; ?>>
                                                                                CONTAINS</option>

                                                                            <option value="DOES_NOT_CONTAIN"
                                                                                <?php echo $session_data['advancedata']['condition_place'][$key] == 'DOES_NOT_CONTAIN' ? 'selected' : ''; ?>>DOES NOT CONTAIN
                                                                            </option>

                                                                            <option value="EQUALS" <?php echo $session_data['advancedata']['condition_place'][$key] == 'CONTAINS' ? 'EQUALS' : ''; ?>>
                                                                                EQUALS</option>

                                                                            <option value="DOES_NOT_EQUAL"
                                                                                <?php echo $session_data['advancedata']['condition_place'][$key] == 'DOES_NOT_EQUAL' ? 'selected' : ''; ?>>DOES NOT EQUAL</option>

                                                                        </select>

                                                                    </td>

                                                                    <td>

                                                                        <input type="text"
                                                                            class="form-control FormInputBox"
                                                                            id="roomname" value="<?php echo $session_data['advancedata']['value'][$key]; ?>"
                                                                            name="advancedata[value][]">

                                                                    </td>

                                                                    <td>

                                                                        <button id="addButton" class="AddTableRow"
                                                                            type="button"><span
                                                                                class="material-symbols-outlined">add</span></button>

                                                                    </td>

                                                                </tr>

                                                                <?php } } else {  ?>

                                                                <tr>

                                                                    <td></td>

                                                                    <td>





                                                                        <select
                                                                            class="form-select form-control FormInputBox"
                                                                            aria-label="Default select example"
                                                                            name="advancedata[condition_place][]">

                                                                            <option value="HOST_NAME" selected>HOST NAME
                                                                            </option>

                                                                            <option value="PAGE_PATH">PAGE PATH</option>

                                                                            <option value="PAGE_URL">PAGE URL</option>

                                                                        </select>



                                                                    </td>

                                                                    <td>

                                                                        <select
                                                                            class="form-select form-control FormInputBox"
                                                                            name="advancedata[condition][]">

                                                                            <option value="CONTAINS" selected>CONTAINS
                                                                            </option>

                                                                            <option value="DOES_NOT_CONTAIN">DOES NOT
                                                                                CONTAIN</option>

                                                                            <option value="EQUALS">EQUALS</option>

                                                                            <option value="DOES_NOT_EQUAL">DOES NOT EQUAL
                                                                            </option>

                                                                        </select>

                                                                    </td>

                                                                    <td>

                                                                        <input type="text"
                                                                            class="form-control FormInputBox"
                                                                            id="roomname" value=""
                                                                            name="advancedata[value][]">

                                                                    </td>

                                                                    <td>

                                                                        <button id="addButton" class="AddTableRow"
                                                                            type="button"><span
                                                                                class="material-symbols-outlined">add</span></button>

                                                                    </td>

                                                                </tr>

                                                                <?php } ?>

                                                            </tbody>

                                                        </table>

                                                    </div>



                                                </div>

                                            </div>

                                        </div>

                                        <!-- End Tab 2 Section 1 -->

                                        <div class="col-md-12 ps-0 pt-2 pb-5">

                                            <div class="LeftGreenborder ps-4">

                                                <h5 class="FormHeading"><b>What is the target URL after queuing?</b></h5>

                                                <p class="FormPara">A pre-queue can prevents your website crashes from
                                                    early demand and delivers online fairness.</p>

                                                <div class="d-flex align-items-center QueueDataCheckboxes SameAboveUrlRedio"
                                                    bis_skin_checked="1">

                                                    <div class="form-check mb-3 ps-0 " bis_skin_checked="1">



                                                        <input type="radio" id="SameAboveUrl"
                                                            class="queueCheckBox targetCheckBox" name="CustomURl"
                                                            value="1" <?php echo 1 == ($session_data['CustomURl'] ?? '') ? 'checked' : ''; ?>>

                                                        <label for="SameAboveUrl" class="form-check-label">Same as the URL
                                                            above</label>

                                                    </div>

                                                    <div class="form-check mb-3" bis_skin_checked="1">

                                                        <input type="radio" id="CustomURl"
                                                            class="queueCheckBox targetCheckBox" name="CustomURl"
                                                            value="2" <?php echo 2 == ($session_data['CustomURl'] ?? '') ? 'checked' : ''; ?>>

                                                        <label for="CustomURl" class="form-check-label">Custom URL</label>

                                                    </div>

                                                </div>

                                                <div id="SameAboveUrlRedio-error" class="error-msg text-danger pt-2">
                                                </div>

                                                <div class="form-group CustomUrlBox" id="CustomUrlBoxId"
                                                    <?php echo 2 == ($session_data['CustomURl'] ?? '') ? "style='display:block'" : ''; ?>>

                                                    <input type="link" class="form-control FormInputBox"
                                                        id="custom_url" name="custom_url" value="<?php echo $session_data['custom_url'] ?? ''; ?>">

                                                </div>

                                            </div>

                                            <div id="target-error" class="error-msg text-danger pt-2"></div>



                                        </div>

                                        <div class="col-md-12 ps-0 pt-2 pb-5">
                                            <div class="LeftGreenborder ps-4">
                                                <h5 class="FormHeading"><b>Choose the session type how do you want kill the
                                                        user session?</b></h5>
                                                <ul class="FormPara">
                                                    <li>If you choose the (According to time) then user get 2 min time to
                                                        complete there task in 2 min other wise user session kill after
                                                        complete the time.</li>
                                                    <li>If user choose the (According to script) then you need to impliment
                                                        one API call or script on you success page.</li>
                                                </ul>
                                                <select class="form-control FormInputBox" name="session_type"
                                                    id="selectSessiontype">
                                                    <option value="">-- Select Session type --</option>
                                                    <option value="1">According to time</option>
                                                    <option value="2">According to script</option>
                                                </select>

                                                <div id="sessionType" style="color: red;"></div>

                                                <!-- Input box (hidden by default) -->
                                                <div id="timeInputContainer" style="display: none; margin-top: 10px;">
                                                    <label for="timeInput">Enter Time:</label>
                                                    <input type="number" class="form-control" name="time_input"
                                                        id="timeInput" placeholder="Enter time in minutes (eg: 2)">
                                                </div>
                                                <div id="inputTime" style="color: red;"></div>
                                            </div>
                                        </div>

                                        <!-- End Tab 2 Section 2 -->

                                        <div class="col-md-12 ps-0 pt-2 pb-5">

                                            <div class="LeftGreenborder ps-4">

                                                <h5 class="FormHeading"><b>What is the maximum traffic to enter your
                                                        protected site?</b></h5>

                                                <p class="FormPara">
                                                    Maximum number of visitors allowed to access your website per minute.
                                                    The maximum number of visitors to set is
                                                    <span id="visitorLimitSpan"></span>.
                                                </p>



                                                <div class="TotalVisitortxt">

                                                    I want to allow <span><input type="number" class="TotalVisitor"
                                                            name="max_traffic" id="TotalVisitorid"
                                                            onkeyup="countchecker();"
                                                            value="<?php echo $session_data['max_traffic'] ?? ''; ?>"></span>visitors to enter the

                                                    protected site per minute.

                                                </div>
                                                <div id="maxTrafficMessaged" style="color: red;"></div>
                                                <!-- <div id="maxTrafficMessage" style="display: none; color: red;">Your maximum traffic limit is {{ $QueueRoompermission->maximum_traffic }}. Please keep your value within this limit.</div> -->

                                                <div id="TotalVisitorid-error" class="error-msg text-danger pt-2"></div>

                                                @if ($errors->has('max_traffic'))
                                                    <span class="text-danger">{{ $errors->first('max_traffic') }}</span>
                                                @endif

                                            </div>

                                        </div>

                                        <!-- End Tab 2 Section 3 -->

                                        @if ($QueueRoompermission->setup_bypass == 1)
                                            <div class="col-md-12 ps-0 pt-2 pb-5">

                                                <div class="LeftGreenborder ps-4">

                                                    <h5 class="FormHeading"><b>Do you want to allow people to bypass the
                                                            queue room?</b></h5>

                                                    <p class="FormPara">Such as allowing VIP to enter the site, or allow
                                                        admins to monitor even

                                                        during peak period.</p>



                                                    <div
                                                        class="d-flex align-items-center QueueDataCheckboxes SetpSecondrediofirst">

                                                        <div class="form-check mb-3 ps-0 ">

                                                            <input type="radio" id="SetupBypass"
                                                                class="queueCheckBox bypassroomqueue" name="SetupBypass"
                                                                value="1"
                                                                {{ old('SetupBypass', $session_data['SetupBypass'] ?? '') == '1' ? 'checked' : '' }}>

                                                            <label for="SetupBypass" class="form-check-label">I want to
                                                                setup bypass</label>

                                                        </div>

                                                        <div class="form-check mb-3">

                                                            <input type="radio" id="NotSetupBypass"
                                                                class="queueCheckBox bypassroomqueue" name="SetupBypass"
                                                                value="0"
                                                                {{ old('SetupBypass', $session_data['SetupBypass'] ?? '') == '0' ? 'checked' : '' }}>

                                                            <label for="NotSetupBypass" class="form-check-label">I do not
                                                                want to setup bypass</label>

                                                        </div>

                                                    </div>

                                                    <div id="SetpSecondrediofirst-error"
                                                        class="error-msg text-danger pt-2"></div>

                                                    @if ($errors->has('SetupBypass'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('SetupBypass') }}</span>
                                                    @endif



                                                    @php

                                                        $bypassDisplay =
                                                            old('SetupBypass', $session_data['SetupBypass'] ?? '') ==
                                                            '1'
                                                                ? ''
                                                                : 'display:none;';

                                                    @endphp



                                                    <div id="bypassFields" style="{{ $bypassDisplay }}">

                                                        <div class="form-group">

                                                            <label for="SelectTemplate" class="FormInputBoxLabel">Please
                                                                select</label>

                                                            <div class="row">

                                                                <div class="col-md-3">

                                                                    <select class="form-select FormInputBox"
                                                                        id="SelectTemplate" name="byPassSelectTemplateid">

                                                                        <option value="0">Create new template</option>

                                                                        <?php
                                                                        
                                                                        foreach ($bypasstemplates as $bypasstemplate) {
                                                                            echo '<option ' . ($bypasstemplate->id == ($session_data['byPassSelectTemplateid'] ?? '') ? 'selected' : '') . " value='{$bypasstemplate->id}'>{$bypasstemplate->template_name}</option>";
                                                                        }
                                                                        
                                                                        ?>

                                                                    </select>


                                                                    @if ($errors->has('byPassSelectTemplateid'))
                                                                        <span
                                                                            class="text-danger">{{ $errors->first('byPassSelectTemplateid') }}</span>
                                                                    @endif

                                                                </div>

                                                                <div class="col-md-9">

                                                                </div>

                                                            </div>

                                                        </div>

                                                        <div class="form-group bypassmanage  <?php echo !empty($session_data['byPassSelectTemplateid'] ?? '') ? 'hide-element' : ''; ?>">

                                                            <label for="templateName" class="FormInputBoxLabel">By pass
                                                                Template name</label>

                                                            <input type="text" class="form-control FormInputBox"
                                                                id="templateName" name="byPassSelectTemplate_name"
                                                                value="{{ old('byPassSelectTemplate_name', $session_data['byPassSelectTemplate_name'] ?? '') }}">

                                                            <div id="bypass-temp-error"
                                                                class="error-msg text-danger pt-2"></div>


                                                            @if ($errors->has('byPassSelectTemplate_name'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('byPassSelectTemplate_name') }}</span>
                                                            @endif

                                                        </div>

                                                        <div class="form-group bypassmanage <?php echo !empty($session_data['byPassSelectTemplateid'] ?? '') ? 'hide-element' : ''; ?>">

                                                            <label for="Bypassurl" class="FormInputBoxLabel">Bypass
                                                                URL</label>

                                                            <input type="link" class="form-control FormInputBox"
                                                                id="Bypassurl" name="Bypassurl"
                                                                value="{{ old('Bypassurl', $session_data['Bypassurl'] ?? '') }}">

                                                            <div id="bypass-url-error" class="error-msg text-danger pt-2">
                                                            </div>


                                                            @if ($errors->has('Bypassurl'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('Bypassurl') }}</span>
                                                            @endif

                                                        </div>

                                                        <div class="bypassmanage <?php echo !empty($session_data['byPassSelectTemplateid'] ?? '') ? 'hide-element' : ''; ?>">

                                                            <div class="d-flex align-items-center mt-3 downloadFlex">

                                                                <div class="form-check form-switch p-0 mt-3">

                                                                    <label class="form-check-label" for="toggleButton"
                                                                        style="color: #159AA1; font-weight: 800;">Passcode
                                                                        protection</label>

                                                                    <input class="form-check-input advancedtoggle"
                                                                        type="checkbox" id="toggleButtontwo">

                                                                </div>

                                                            </div>

                                                            <div id="buttonsContainertwo"
                                                                style="display: none; margin-top: 20px; margin-bottom: 20px;">

                                                                <div class="DownloadExcel me-3">

                                                                    <a href="{{ asset('export/Excel_Template.xlsx') }}"
                                                                        class="text-decoration-none" download><button
                                                                            type="button"
                                                                            class="btn DownloadExcelbtn d-flex align-items-center justify-content-between"><span
                                                                                class="material-symbols-outlined pe-3">add</span>
                                                                            Download Excel Template</button></a>

                                                                </div>

                                                                <div class="UploadExcel">

                                                                    <input id="upload" hidden type="file"
                                                                        name="filebyPass" />

                                                                    <label for="upload"
                                                                        class="btn UploadExcelbtn d-flex align-items-center justify-content-between"
                                                                        tabindex="0"><span
                                                                            class="material-symbols-outlined pe-3">add</span>
                                                                        Upload Excel</label>

                                                                </div>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                        @endif



                                        <!-- End Tab 2 Section 4 -->

                                        @if ($QueueRoompermission->setup_pre_queue == 1)
                                            <div class="col-md-12 ps-0 pt-2 pb-5">

                                                <div class="LeftGreenborder ps-4">

                                                    <h5 class="FormHeading"><b>Do you want to setup pre-queue?</b></h5>

                                                    <p class="FormPara">A pre-queue can prevent your website crashes from
                                                        early demand and deliver online fairness.</p>

                                                    <div
                                                        class="d-flex align-items-center QueueDataCheckboxes SetpSecondrediosecond">

                                                        <div class="form-check mb-3 ps-0">

                                                            <input type="radio" id="PreSetupBypass"
                                                                class="queueCheckBox" name="preQueueSetup" value="1"
                                                                {{ old('preQueueSetup', $session_data['preQueueSetup'] ?? '') == '1' ? 'checked' : '' }}>

                                                            <label for="PreSetupBypass" class="form-check-label">I want to
                                                                setup pre-queue</label>

                                                        </div>

                                                        <div class="form-check mb-3">

                                                            <input type="radio" id="PreNotSetupBypass"
                                                                class="queueCheckBox" name="preQueueSetup" value="0"
                                                                {{ old('preQueueSetup', $session_data['preQueueSetup'] ?? '') == '0' ? 'checked' : '' }}>

                                                            <label for="PreNotSetupBypass" class="form-check-label">I do
                                                                not want to setup pre-queue</label>

                                                        </div>

                                                    </div>

                                                    <div id="SetpSecondrediosecond-error"
                                                        class="error-msg text-danger pt-2"></div>

                                                    @if ($errors->has('preQueueSetup'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('preQueueSetup') }}</span>
                                                    @endif

                                                    <div class="TotalVisitortxt ps-4 hidden" id="preQueueField"
                                                        <?php echo ($session_data['preQueueSetup'] ?? '') == '1' ? 'style="display:block"' : ''; ?>>

                                                        Pre-queue starts:

                                                    </div>

                                                    <div class="TotalVisitortxt pt-2 ps-4 hidden" id="preQueueTimeField"
                                                        <?php echo ($session_data['preQueueSetup'] ?? '') == '1' ? 'style="display:block"' : ''; ?>>

                                                        <span><input type="text" class="TotalVisitor ms-0 "
                                                                name="BeforeTimeforPrequeue"
                                                                value="{{ old('BeforeTimeforPrequeue', $session_data['BeforeTimeforPrequeue'] ?? '60') }}"></span>
                                                        minutes before the queuing start time.

                                                    </div>

                                                    @if ($errors->has('BeforeTimeforPrequeue'))
                                                        <span
                                                            class="text-danger">{{ $errors->first('BeforeTimeforPrequeue') }}</span>
                                                    @endif

                                                </div>

                                            </div>
                                        @endif



                                        <!-- End Tab 2 Section 5 -->

                                    </div>

                                    <div class="row">

                                        <div class="col-md-12 d-flex text-end justify-content-end StepFormButtonsGrp">

                                            <button
                                                class="btn btn-secondary d-flex align-items-center justify-content-center me-2 backBtn"
                                                type="button" onclick="MultistepForm.prevStep(2)"><span
                                                    class="material-symbols-outlined pe-2">

                                                    arrow_back_ios_new

                                                </span> Back</button>

                                            <button
                                                class="btn btn-primary d-flex align-items-center justify-content-center nextBtn"
                                                type="button" onclick="handleNextStep1()">Next <span
                                                     class="material-symbols-outlined ps-2">

                                                    arrow_forward_ios

                                                </span></button>

                                                

                                        </div>

                                    </div>

                                    <!-- Next Prev Buttons -->

                                </div>



                                <!-- Step  3-->

                                <div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="step3-tab">

                                    <div class="row m-0">

                                        <div class="col-md-12 ps-0 pt-2 pb-5">

                                            <div class="LeftGreenborder ps-4">

                                                <h5 class="FormHeading"><b>Queue room design</b></h5>

                                                <span id="langErrorBox" class="text-danger"></span>



                                                <div class="row mt-3">

                                                    <div class="col-md-3">

                                                        <select class="form-select FormInputBox"
                                                            aria-label="Default select example" name="QueueRoomDesign_id"
                                                            id="QueueRoomDesignid">

                                                            <option value="0" <?php echo ($session_data['QueueRoomDesign_id'] ?? '') == '' ? 'selected' : ''; ?>>Create new template
                                                            </option>

                                                            <?php
                                                            
                                                            foreach ($designtemplates as $designtemplate) {
                                                                echo "<option value= '{$designtemplate->id}' " . ($designtemplate->id == ($session_data['QueueRoomDesign_id'] ?? '') ? 'selected' : '') . ">{$designtemplate->template_name}</option>";
                                                            }
                                                            
                                                            ?>

                                                        </select>

                                                        <div id="QueueRoomDesignid-error qrIdError"
                                                            class="error-msg text-danger pt-2"></div>

                                                        @if ($errors->has('QueueRoomDesign_id'))
                                                            <span
                                                                class="text-danger qrIdError">{{ $errors->first('QueueRoomDesign_id') }}</span>
                                                        @endif

                                                    </div>

                                                    <div class="col-md-9"></div>

                                                </div>



                                                <div class="form-group design-temp <?php echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">

                                                    <label for="SelectTemplate" class="FormInputBoxLabel">Template
                                                        name</label>

                                                    <input type="text" class="form-control FormInputBox"
                                                        name="QueueRoomDesignTemplate_name"
                                                        value="{{ old('QueueRoomDesignTemplate_name', $session_data['QueueRoomDesignTemplate_name'] ?? '') }}"
                                                        id="langTampName">
                                                    <div id="QueueRoomDesignTemplate_name-error"
                                                        class="error-msg text-danger pt-2"></div>

                                                    @if ($errors->has('QueueRoomDesignTemplate_name'))
                                                        <span
                                                            class="text-danger qrIdError">{{ $errors->first('QueueRoomDesignTemplate_name') }}</span>
                                                    @endif

                                                </div>



                                                <p class="FormPara pt-3 languagePara design-temp <?php echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">What
                                                    language do you want the queue room to display?</p>

                
                                                <div class="row mb-3 design-temp <?php echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">

                                                    <div class="col-md-5">

                                                        <select id="mainSelectDesignTemp" class="form-select"
                                                            aria-label="Default select example">

                                                            <option value="0" selected>Please select...</option>

                                                            @php

                                                                // Sort the $languages array alphabetically by name

                                                                $sortedLanguages = $languages->sortBy('name');

                                                            @endphp

                                                            @foreach ($sortedLanguages as $language)
                                                                <option value="{{ $language->code }}">
                                                                    {{ $language->name . ' (' . $language->native . ')' }}
                                                                </option>
                                                            @endforeach

                                                        </select>

                                                        <div id="mainSelectDesignTemp-error"
                                                            class="error-msg text-danger pt-2"></div>


                                                    </div>

                                               

                                                </div>



                                                <div class="row mt-3 design-temp <?php echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">

                                                    <div class="col-md-12">

                                                        <button type="submit" name="saveasdraft" value="1"
                                                            class="btn editBtn" id="DesignEditBtn">Edit <i
                                                                class="fa fa-pencil-square-o ps-1" aria-hidden="true"></i>

                                                        </button>

                                                    </div>

                                                </div>

                                                <div class="row mt-3 design-temp">
                                                    <p class="FormPara pt-3 languagePara design-temp">Upload your all 4
                                                        type of .html file, If you have your own templates!</p>
                                                    <div class="col-md-3">
                                                        <label for="queueHtmlFile" class="FormInputBoxLabel">Queue
                                                            page</label>
                                                        <input type="file" class="form-control FormInputBox"
                                                            name="queueHtmlFile" accept=".html" id="queueHtmlFile" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="preQueueHtmlFile" class="FormInputBoxLabel">Pre queue
                                                            page</label>
                                                        <input type="file" class="form-control FormInputBox"
                                                            name="preQueueHtmlFile" accept=".html"
                                                            id="preQueueHtmlFile" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="postQueueHtmlFile" class="FormInputBoxLabel">Post
                                                            queue page</label>
                                                        <input type="file" class="form-control FormInputBox"
                                                            name="postQueueHtmlFile" accept=".html"
                                                            id="postQueueHtmlFile" />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label for="priorityAccessPageHtmlFile"
                                                            class="FormInputBoxLabel">Priority access page</label>
                                                        <input type="file" class="form-control FormInputBox"
                                                            name="priorityAccessPageHtmlFile" accept=".html"
                                                            id="priorityAccessPageHtmlFile" />
                                                    </div>
                                                </div>

                                            </div>

                                            @if ($errors->has('queue_language'))
                                                <span
                                                    class="text-danger qrIdError">{{ $errors->first('queue_language') }}</span>
                                            @endif

                                            <div class="in_line_template" style="margin: 20px 10px;">

                                            </div>

                                            <input type="hidden" id="jsonlangDesignTemp" name="queue_language"
                                                value="">

                                        </div>

                                        <!-- End Tab 3 Section 1 -->

                                    </div>

                                    <div class="row">

                                        <div class="col-md-12 d-flex text-end justify-content-end StepFormButtonsGrp">



                                            <button type="button"
                                                class="btn btn-secondary d-flex align-items-center justify-content-center me-2 backBtn"
                                                onclick="MultistepForm.prevStep(3)"><span
                                                    class="material-symbols-outlined pe-2">

                                                    arrow_back_ios_new

                                                </span> Back</button>

                                            <button type="button"
                                                class="btn btn-primary d-flex align-items-center justify-content-center saveBtn"
                                                onclick="handleNextStep2()">Next <span
                                                    class="material-symbols-outlined ps-2">arrow_forward_ios</span></button>

                                        </div>

                                    </div>

                                </div>



                                <!-- Step  4 -->

                                <div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab">

                                    <div class="row m-0">

                                        @if ($QueueRoompermission->setup_sms == 1)
                                            <div class="col-md-12 ps-0 pt-2 pb-5">

                                                <div class="LeftGreenborder ps-4">

                                                    <h5 class="FormHeading"><b>SMS Notice</b></h5>

                                                    <div class="row mt-3">

                                                        <div class="col-md-3">

                                                            <select class="form-select FormInputBox"
                                                                name="SMSCreateTemplate" id="SMSCreateTemplateid">

                                                                <option selected value="0">Create new template
                                                                </option>

                                                                <?php
                                                                
                                                                foreach ($smstemplates as $smstemplate) {
                                                                    echo '<option ' . ($smstemplate->id == ($session_data['SMSCreateTemplate'] ?? '') ? 'selected' : '') . " value='{$smstemplate->id}'>{$smstemplate->sms_template_name}</option>";
                                                                }
                                                                
                                                                ?>

                                                            </select>

                                                            <div id="SMSCreateTemplateid-error"
                                                                class="error-msg text-danger pt-2"></div>

                                                        </div>

                                                        <div class="col-md-9"></div>

                                                    </div>






                                                    <div class="form-group smsTemp  <?php echo !empty($session_data['SMSCreateTemplate'] ?? '') ? 'hide-element' : ''; ?>">

                                                        <label for="SMSTemplate" class="FormInputBoxLabel">Template
                                                            name</label>

                                                        <input type="text" class="form-control FormInputBox"
                                                            name="SMSTemplate" id="SMSTemplate"
                                                            value="<?php echo $session_data['SMSTemplate'] ?? ''; ?>">

                                                        <div id="SMSTemplate-error" class="error-msg text-danger pt-2">
                                                        </div>


                                                    </div>

                                                    <div class="row mt-3">

                                                        <input type="hidden" id="editorsmsContent"
                                                            name="editorsmsContent" value="<?php echo $session_data['editorsmsContent'] ?? ''; ?>">



                                                        <div class="col-md-12 smsTemp">

                                                            <!-- <button type="button" class="btn editBtn" id="SMSEditBtn">Edit <i class="fa fa-pencil-square-o ps-1" aria-hidden="true"></i>

                                                                                                                                                                                                                                                                          </button> -->



                                                            <button type="button" class="btn btn-primary btn editBtn"
                                                                id="SMSEditBtn" data-bs-toggle="modal"
                                                                data-bs-target="#smsModal">

                                                                Edit<i class="fa fa-pencil-square-o ps-1"
                                                                    aria-hidden="true"></i>

                                                            </button>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                        @endif

                                        @if ($QueueRoompermission->setup_email == 1)
                                            <div class="col-md-12 ps-0 pt-2 pb-5">

                                                <div class="LeftGreenborder ps-4">

                                                    <h5 class="FormHeading"><b>Email notice</b></h5>

                                                    <div class="row mt-3">

                                                        <div class="col-md-3">

                                                            <select class="form-select FormInputBox"
                                                                name="EmailCreateTemplate" id="EmailCreateTemplateid">

                                                                <option value="0" selected>Create new template
                                                                </option>

                                                                <?php
                                                                
                                                                foreach ($emailtemplates as $emailtemplate) {
                                                                    echo '<option ' . ($emailtemplate->id == ($session_data['EmailCreateTemplate'] ?? '') ? 'selected' : '') . " value='{$emailtemplate->id}'>{$emailtemplate->email_template_name}</option>";
                                                                }
                                                                
                                                                ?>

                                                            </select>


                                                        </div>

                                                        <div class="col-md-9"></div>

                                                    </div>



                                                    <div class="form-group emailTemp <?php echo !empty($session_data['EmailCreateTemplate'] ?? '') ? 'hide-element' : ''; ?>">

                                                        <label for="EmailTemplate" class="FormInputBoxLabel">Template
                                                            name</label>

                                                        <input type="text" class="form-control FormInputBox"
                                                            name="EmailTemplate" id="EmailTemplate"
                                                            value="<?php echo $session_data['EmailTemplate'] ?? ''; ?>">

                                                        <div id="EmailCreateTemplateid-error"
                                                            class="error-msg text-danger pt-2"></div>


                                                    </div>

                                                    <input type="hidden" id="editoremailContent"
                                                        name="editoremailContent" value="<?php echo $session_data['editoremailContent'] ?? ''; ?>">

                                                    <div class="row mt-3 emailTemp">

                                                        <div class="col-md-12">

                                                            <button type="button" class="btn editBtn" id="EmailEditBtn"
                                                                data-bs-toggle="modal" data-bs-target="#emailModal">Edit
                                                                <i class="fa fa-pencil-square-o ps-1"
                                                                    aria-hidden="true"></i>

                                                            </button>

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                        @endif

                                    </div>

                                    @if ($QueueRoompermission->setup_email == 0 && $QueueRoompermission->setup_sms == 0)
                                        <div class="card" style="width: 100%; margin-bottom:15px;">
                                            <div class="card-body">
                                                <h5 class="card-title"> SMS and Email Setup</h5>
                                                <p class="card-text">

                                                    Please note that the current plan you are subscribed to does not include
                                                    the SMS and Email setup features you are trying to access. These
                                                    functionalities are available in higher-tier plans.
                                                </p>
                                                <p class="card-text">
                                                    However, you can continue with your current plan without setting up SMS
                                                    and Email services. Should you need these features in the future, you
                                                    can always upgrade your plan.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                    <!-- End Tab 4 section -->

                                    <div class="row">

                                        <div class="col-md-12 d-flex text-end justify-content-end StepFormButtonsGrp">

                                            <button type="button"
                                                class="btn btn-secondary d-flex align-items-center justify-content-center me-2 backBtn"
                                                onclick="MultistepForm.prevStep(4)"><span
                                                    class="material-symbols-outlined pe-2">

                                                    arrow_back_ios_new

                                                </span> Back</button>

                                            <button type="button"
                                                class="btn btn-secondary d-flex align-items-center justify-content-center me-2 backBtn Saveasdraft"
                                                onclick="submitForm('draft')"> Save as draft</button>

                                            <button type="button"
                                                class="btn btn-primary d-flex align-items-center justify-content-center saveBtn"
                                                onclick="submitForm('publish')">Save and publish</button>

                                        </div>

                                    </div>

                                    <!-- Next Prev Buttons -->

                                </div>

                                <!-- End Tab 4 -->

                            </form>

                        </div>

                    </div>

                </div>

            </div>

            <!-- End  Section -->

        </section>

    </main>

    <script>
        $(document).ready(function () {
    $("#QueueIconLabel").click(function () {
        $("#iconQueue").click();
    });
});
    </script>
    <script>
        let calculateVisitorCount = 0;
        let formCount = 0;


        function countchecker() {
            console.log(calculateVisitorCount, 'calculateVisitorCount');
            const id = $('#TotalVisitorid').val();
            if (calculateVisitorCount == 0) {
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var checkIdUrl = "{{ route('getQueueTotalvisitors') }}";
                if (id) {
                    $.ajax({
                        url: checkIdUrl, // URL to the Laravel route
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Set CSRF token in headers
                        },
                        data: {
                            count: id,
                            type: 1
                        },
                        success: function(response) {
                            console.log(response.calculateVisitorCount, 'response');
                            if (response.status == 1) {
                                calculateVisitorCount = response.calculateVisitorCount;
                                console.log(calculateVisitorCount, 'calculateVisitorCount1');

                                // $('#TotalVisitorid').val(id);
                            } else {
                                var error_message = 'Your usage has reached the limit of ' + response
                                .calculateVisitorCount + ' . Please upgrade.';
                                
                                $('#maxTrafficMessaged').text(error_message);
                            }
                            // Update the result paragraph with the response

                        },
                        error: function(xhr) {
                            $('#maxTrafficMessaged').text('Error occurred.');
                        }
                    });
                } else {
                    $('#maxTrafficMessaged').text('');
                }
            } else if (calculateVisitorCount < id) {
                var error_message = 'Your usage has reached the limit of ' + calculateVisitorCount + ' . Please upgrade.';
                $('#maxTrafficMessaged').text(error_message);
                
            } else {
                var error_message = '';
                $('#maxTrafficMessaged').text(error_message);
                
            }
        }
        
        $(window).on('load', function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content'); // Retrieve CSRF token from meta tag
            const checkIdUrl = "{{ route('getQueueTotalvisitors') }}"; // Define the URL for the Laravel route
            
            $.ajax({
                url: checkIdUrl, // URL to the Laravel route
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Set CSRF token in headers
                },
                data: {
                    type: 2
                },
                success: function(response) {
                    if (response.data.setup_email == 1 && response.data.setup_sms == 1) {
                        // $('#step4-tab').show();
                        // $('#step4').show();
                    } else {
                        // $('#step4-tab').hide();
                        // $('#step4').hide();
                    }
                },
                error: function(xhr) {

                }
            });
        });

        function submitForm(action) {

            
            $('#SMSTemplate-error').html('');
            

            var SMSCreateTemplateid = document.getElementById("SMSCreateTemplateid").value;

            
            if (SMSCreateTemplateid == 0) {
                var SMSTemplate = document.getElementById("SMSTemplate").value;
                console.log(SMSTemplate, 'SMSTemplateSMSTemplate');
                if (!SMSTemplate) {
                    $('#SMSTemplate-error').html('This is a required field');
                    return false;
                }

            }

            var EmailCreateTemplateid = document.getElementById("EmailCreateTemplateid").value;
            
            
            if (EmailCreateTemplateid == 0) {
                var EmailTemplate = document.getElementById("EmailTemplate").value;
                console.log(EmailTemplate, 'EmailTemplate');
                if (!EmailTemplate) {
                    $('#EmailCreateTemplateid-error').html('This is a required field');
                    return false;
                }

            }

            



            if (action === 'draft') {
                // Create a hidden input field for saveasdraft
                var saveAsDraftInput = document.createElement('input');
                saveAsDraftInput.type = 'hidden';
                saveAsDraftInput.name = 'saveasdraft';
                saveAsDraftInput.value = '1';

                // Append the input to the form
                document.getElementById('main_queue_room').appendChild(saveAsDraftInput);
            }

            console.log(formCount, 'formCountformCount');
            if (formCount == 0) {
                document.getElementById("main_queue_room").submit();
                formCount++;
            }
        }
        
        function handleNextStep() {
            if (validateStep1()) { // Agar validateStep1() true return kare
                MultistepForm.nextStep(1);
            }
        }

        function handleNextStep1() {

            // Validate both steps before proceeding to the next step


            $('#TempCreateSltId-error').html('');
            
            $('#target-error').html('');
            
            $('#TotalVisitorid-error').html('');
            
            $('#qrmTamp').html('');
            
            $('#sessionType').html('');
            
            $('#queueCheckBox_error_id').html('');
            
            $('#queueCheckBox_error').html('');
            
            $('#iconQueue-error').html('');
            
            $('#SetpSecondrediofirst-error').html('');

            $('#SetpSecondrediosecond-error').html('');
            
            $('#bypass-temp-error').html('');
            
            $('#bypass-url-error').html('');

            if (validateStep2() && validateStep1()) {
                MultistepForm.nextStep(2); // Proceed to the next step
            } else {
                //alert("Validation failed. Please correct the errors and try again."); // Error message
            }
        }
        
        function handleNextStep2() {

            console.log(validateStep2(), 'validateStep2()');
            console.log(validateStep1(), 'validateStep1()');
            console.log(validateStep3(), 'validateStep1()');
            console.log(validateStep2() && validateStep1() && validateStep3());
            
            
            // Validate both steps before proceeding to the next step
            if (validateStep2() && validateStep1() && validateStep3()) {
                MultistepForm.nextStep(3); // Proceed to the next step
            } else {
                //alert("Validation failed. Please correct the errors and try again."); // Error message
            }
        }

        function validateStep1() {


            
            var step2TabButton = document.getElementById("step2-tab");
            
            step2TabButton.removeAttribute("data-bs-target", "#step2");
            
            step2TabButton.removeAttribute("data-bs-toggle", "tab");
            
            
            
            
            $('#timezoneselect-error').html('');

            $('#queuetype-error').html('');
            
            $('#roomname-error').html('');
            
            $('.startCheckBox-error').html('');
            
            $('#radioSec-error').html('');
            
            
            
            
            
            var timezoneselect = document.getElementById("timezoneselect").value;
            
            var roomname = document.getElementById("roomname").value;
            var iconQueue = document.getElementById("iconQueue").value;
            var queuetype = document.getElementById("queuetype").value;
            
            
            
            
            
            
            
            var start_radioButtons = document.querySelectorAll('.startCheckBox');
            
            var start_atLeastOneChecked = false;
            
            start_radioButtons.forEach(function(start_radioButtons) {

                if (start_radioButtons.checked) {

                    start_atLeastOneChecked = true;

                }

            });

            
            
            var end_radioButtons = document.querySelectorAll('.endCheckBox');
            
            var end_atLeastOneChecked = false;
            
            end_radioButtons.forEach(function(end_radioButtons) {

                if (end_radioButtons.checked) {
                    
                    end_atLeastOneChecked = true;
                    
                }

            });

            
            
            
            
            if (roomname && timezoneselect && start_atLeastOneChecked && end_atLeastOneChecked && iconQueue && queuetype) {

                step2TabButton.removeAttribute("data-bs-target", "#step2");
                
                step2TabButton.removeAttribute("data-bs-toggle", "tab");
                
                showStep('step2');
                
                var divElement = document.getElementById('step2');
                
                divElement.classList.remove('fade');
                
                divElement.classList.add('active', 'show');

                return true;

            } else {
                
                if (!timezoneselect) {

                    $('#timezoneselect-error').html('This is a required field');
                    
                }

                if (!queuetype) {

                    $('#queuetype-error').html('This is a required field');
                    
                }

                
                
                if (!roomname) {

                    $('#roomname-error').html('This is a required field');
                    
                }

                if (!iconQueue || iconQueue.trim() === "") {
                    $('#iconQueue-error').html('This is a required field');
                } else {
                    $('#iconQueue-error').html('');
                }

                
                if (!start_atLeastOneChecked) {

                    $('.startCheckBox-error').html('This is a required field');
                    
                }

                if (!end_atLeastOneChecked) {

                    $('#radioSec-error').html('This is a required field');

                }

                return false;

            }

        }

        
        
        function validateStep2() {
            console.log(calculateVisitorCount, 'calculateVisitorCount');
            var step3TabButton = document.getElementById("step3-tab");
            
            step3TabButton.removeAttribute("data-bs-target", "#step3");
            
            step3TabButton.removeAttribute("data-bs-toggle", "tab");
            
            

            $('#TempCreateSltId-error').html('');
            
            $('#target-error').html('');
            
            $('#TotalVisitorid-error').html('');

            $('#qrmTamp').html('');
            
            $('#sessionType').html('');

            $('#queueCheckBox_error_id').html('');
            
            $('#queueCheckBox_error').html('');
            
            $('#iconQueue-error').html('');

            $('#SetpSecondrediofirst-error').html('');
            
            $('#SetpSecondrediosecond-error').html('');
            
            $('#bypass-temp-error').html('');
            
            $('#bypass-url-error').html('');


            
            var TempCreateSltId = document.getElementById("TempCreateSltId").value;
            
            var TotalVisitorid = document.getElementById("TotalVisitorid").value;

            var session_Type = document.getElementById("selectSessiontype").value;
            
            var QueueRoomDesign_id = document.getElementById("QueueRoomDesignid").value;
            
            var template_name = document.getElementById("template_name").value;
            
            var custom_url = document.querySelector('input[name="CustomURl"]:checked');
            
            var SetupBypass = document.querySelector('input[name="SetupBypass"]:checked');
            var preQueueSetup = document.querySelector('input[name="preQueueSetup"]:checked');
            
            
            
            
            
            const urlPattern = /^(https?:\/\/)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[^\s]*)?$/;
            
            var inputurrrrll = document.getElementById('input_url').value;
            
            
            
            var qRoomTempFlag = false;
            
            var qRoomTempFlagm = false;

            if (TempCreateSltId == 0) {

                if ((template_name.length > 0)) {

                    qRoomTempFlag = true;
                    
                } else if ((input_url.length > 0)) {
                    qRoomTempFlag = true;
                } else

                {

                    qRoomTempFlag = false;
                    
                }
                var checkurls = urlPattern.test(inputurrrrll);
                if (checkurls) {
                    qRoomTempFlagm = true;
                } else {
                    qRoomTempFlagm = false;
                }

            } else {
                
                qRoomTempFlag = true;
                qRoomTempFlagm = true;
                
            }


            
            
            
            var qr_queueCheckBox = document.querySelectorAll('.protectionBoxID');

            var queueCheckBox_flag = false;

            qr_queueCheckBox.forEach(function(qr_queueCheckBoxs) {

                if (qr_queueCheckBoxs.checked) {

                    queueCheckBox_flag = true;
                    
                }

            });


            
            
            

            if ((TotalVisitorid < calculateVisitorCount) && qRoomTempFlag && session_Type && qRoomTempFlagm &&
                SetupBypass != null && preQueueSetup != null) {

                    if (SetupBypass.value == 1) {
                    var SelectTemplate = document.getElementById('templateName').value;
                    var Bypassurl = document.getElementById('Bypassurl').value;
                    
                    
                    
                    if (!SelectTemplate) {
                        $('#bypass-temp-error').html('This is a required field');
                    }

                    if (!Bypassurl) {
                        $('#bypass-url-error').html('This is a required field');
                        return false;
                        
                    }
                }

                step3TabButton.removeAttribute("data-bs-target", "#step3");
                
                step3TabButton.removeAttribute("data-bs-toggle", "tab");
                
                showStep('step3');
                
                var divElement = document.getElementById('step3');
                
                divElement.classList.remove('fade');

                divElement.classList.add('active', 'show');
                
                return true;
                
            } else {
                
                if (!TotalVisitorid) {

                    $('#TotalVisitorid-error').html('This is a required field');

                }

                if (TotalVisitorid < calculateVisitorCount) {

                    $('#TotalVisitorid-error').html('he maximum number of visitors to set is ' + calculateVisitorCount);
                    
                }
                if (!session_Type) {

                    $('#sessionType').html('This is a required field');

                } else {
                    if (session_Type == 1) {

                        var timeInput = document.getElementById("timeInput").value;
                        if (!timeInput) {
                            $('#inputTime').html('This is a required field');
                        } else {
                            $('#inputTime').html('');

                        }


                    } else {
                        $('#sessionType').html('');
                        $('#inputTime').html('');

                    }
                    $('#sessionType').html('');

                }
                if (!inputurrrrll) {
                    document.getElementById('inputUrl').innerHTML = 'This is a required field';
                } else if (!urlPattern.test(inputurrrrll)) {
                    document.getElementById('inputUrl').innerHTML = 'Please enter a valid URL.';
                } else {
                    document.getElementById('inputUrl').innerHTML = '';

                }

                // if (!queueCheckBox_flag) {

                //   $('#queueCheckBox_error_id').html('This is a required field');

                // }

                

                if (!custom_url || custom_url == null) {

                    $('#SameAboveUrlRedio-error').html('This is a required field');
                    
                } else {
                    
                    if (custom_url.value == 2) {

                        var CustomUrlBoxId = document.getElementById("custom_url").value;
                        
                        if (!CustomUrlBoxId) {
                            $('#target-error').html('This is a required field');

                        } else {
                            $('#target-error').html('');

                        }

                    } else {
                        $('#SameAboveUrlRedio-error').html('');
                        $('#target-error').html('');
                        

                        
                    }

                    $('#SameAboveUrlRedio-error').html('');
                    
                }
                

                if (!preQueueSetup || preQueueSetup == null) {
                    $('#SetpSecondrediosecond-error').html('This is a required field');
                } else {
                    $('#SetpSecondrediosecond-error').html('');

                }

                if (!SetupBypass || SetupBypass == null) {
                    $('#SetpSecondrediofirst-error').html('This is a required field');
                } else {
                    $('#SetpSecondrediofirst-error').html('');
                    
                }


                
                
                if (!qRoomTempFlag) {

                    $('#qrmTamp').html('This is a required field');
                    $('#template_name-error').html('This is a required field');
                    
                    
                } else {
                    $('#template_name-error').html('');
                }

                if (SetupBypass) {

                    if (SetupBypass.value == 1) {
                        var SelectTemplate = document.getElementById('templateName').value;
                        var Bypassurl = document.getElementById('Bypassurl').value;
                        
                        if (!SelectTemplate) {
                            $('#bypass-temp-error').html('This is a required field');
                        }

                        if (!Bypassurl) {
                            $('#bypass-url-error').html('This is a required field');
                        }
                    }
                }


                
                return false;

            }

        }


        


        function validateStep3() {

            if (validateStep2() && validateStep1()) {} else {
                return false;
            }

            var step4TabButton = document.getElementById("step4-tab");
            
            step4TabButton.removeAttribute("data-bs-target", "#step4");
            
            step4TabButton.removeAttribute("data-bs-toggle", "tab");
            


            $('#langErrorBox').html('');
            $('#QueueRoomDesignTemplate_name-error').html('');
            $('#mainSelectDesignTemp-error').html('');
            
            
            
            var QueueRoomDesignid = document.getElementById("QueueRoomDesignid").value;
            console.log(QueueRoomDesignid, 'QueueRoomDesignid');
            
            var langTampName = document.getElementById("langTampName").value;
            var mainSelectDesignTemp = document.getElementById("jsonlangDesignTemp").value;

            
            console.log(mainSelectDesignTemp, 'mainSelectDesignTempmainSelectDesignTemp');

            var QueueRoomDesignidFlag = false;
            
            
            
            if (QueueRoomDesignid == 0) {

                if ((langTampName.length > 0)) {

                    QueueRoomDesignidFlag = true;
                    
                } else

                {
                    $('#QueueRoomDesignTemplate_name-error').html('This is a required field');
                    
                    
                    QueueRoomDesignidFlag = false;
                    // return false;

                    
                }

                if (mainSelectDesignTemp == 0) {
                    $('#mainSelectDesignTemp-error').html('This is a required field');
                    QueueRoomDesignidFlag = false;
                    
                    // return false;
                    
                    
                }

                

            } else {
                
                QueueRoomDesignidFlag = true;
                
            }


            
            
            
            console.log(QueueRoomDesignidFlag, 'QueueRoomDesignidFlagQueueRoomDesignidFlag');
            
            
            
            if (QueueRoomDesignidFlag) {

                step4TabButton.removeAttribute("data-bs-target", "#step4");
                
                step4TabButton.removeAttribute("data-bs-toggle", "tab");
                
                showStep('step4');

                var divElement = document.getElementById('step4');

                divElement.classList.remove('fade');
                
                divElement.classList.add('active', 'show');
                
                return true;
                
            } else {
                
                $('#langErrorBox').html('This is a required field');
                showStep('step3');
                var divElement = document.getElementById('step3');

                divElement.classList.remove('fade');
                
                divElement.classList.add('active', 'show');
                return false;
                
            }
            
        }

        
        
        
        
        
        
        function showStep(stepId) {

            document.querySelectorAll('.nav-links').forEach(function(button) {

                button.classList.remove('active');
                
            });

            document.querySelector('#' + stepId + '-tab').classList.add('active');
            
            document.querySelectorAll('.tab-content .tab-pane').forEach(function(tabPane) {

                tabPane.classList.remove('active', 'show');
                
            });

        }
    </script>



    <!-- SMS Template Modal -->

    <div class="modal fade" id="smsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="smsModalLabel" aria-hidden="true">

        <div
        class="modal-dialog modal-dialo                                                                                                                                                                                                                                                                                                                                                                                                                                                                  g-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="smsModalLabel">Edit SMS Template</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body">

                    <textarea id="smsEditor"></textarea>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button type="button" class="btn btn-primary" id="smsUnderstoodBtn">Save</button>

                </div>

            </div>

        </div>

    </div>

    <!-- Email Template Modal -->

    <div class="modal fade" id="emailModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="emailModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="emailModalLabel">Edit Email Template</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body">

                    <textarea id="emailEditor"></textarea>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button type="button" class="btn btn-primary" id="emailUnderstoodBtn">Save</button>

                </div>

            </div>

        </div>

    </div>






    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var maxTrafficInput = document.getElementById('TotalVisitorid');

            var maxTrafficMessage = document.getElementById('maxTrafficMessage');
            
            
            
            // maxTrafficInput.addEventListener('input', function() {

            //   var enteredValue = parseInt(maxTrafficInput.value);

            //   var maxTrafficLimit = parseInt(maxTrafficInput.getAttribute('max'));
            
            
            
            //   if (enteredValue > maxTrafficLimit) {

            //     maxTrafficInput.value = '';

            //     maxTrafficMessage.style.display = 'block';
            
            //   } else {

            //     maxTrafficMessage.style.display = 'none';

            //   }
            
            // });
            
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {



            function convertDateTime() {

                //var selectedTimezone = document.getElementById("timezoneselect").value;

                var timezoneSelect = document.getElementById("timezoneselect");

                var selectedOption = timezoneSelect.options[timezoneSelect.selectedIndex];

                var selectedTimezone = selectedOption.getAttribute("data-timezone");





                var customStartDate = document.getElementById("datepicker").value;

                var customStartTime = document.getElementById("timePicker").value;



                var endCustomDate = document.getElementById("Enddatepicker").value;

                var endCustomTime = document.getElementById("EndtimePicker").value;



                // Split customStartDate into day, month, and year

                var dateParts = customStartDate.split(" ");

                var day = parseInt(dateParts[0]);

                var month = dateParts[1];

                var year = parseInt(dateParts[2]);



                var datePartsend = endCustomDate.split(" ");

                var dayend = parseInt(datePartsend[0]);

                var monthend = datePartsend[1];

                var yearend = parseInt(datePartsend[2]);



                // Map month name to month number (1-based index)

                var monthMap = {

                    "January": 1,

                    "February": 2,

                    "March": 3,

                    "April": 4,

                    "May": 5,

                    "June": 6,

                    "July": 7,

                    "August": 8,

                    "September": 9,

                    "October": 10,

                    "November": 11,

                    "December": 12

                };



                var options = {

                    timeZone: selectedTimezone

                };



                if (typeof month !== "undefined") {

                    var monthNumber = monthMap[month];

                    // Convert month name to month number and format day and year

                    var formattedDate = year + "-" + monthNumber.toString().padStart(2, '0') + "-" + day.toString()
                        .padStart(2, '0');

                    // Combine formattedDate and customStartTime into datetime string

                    var datetimeString = formattedDate + "T" + customStartTime;

                    var convertedDateTime = new Date(datetimeString);



                    // Check if the convertedDateTime is a valid date

                    if (isNaN(convertedDateTime)) {

                        console.error("Invalid date or time format");

                        return;

                    }



                    var formattedDateTime = convertedDateTime.toLocaleString('en-US', options);

                    var epochValueStart = new Date(formattedDateTime).getTime() / 1000;

                    document.getElementById("convertedDateTime").value = formattedDateTime;

                    document.getElementById("startepochTime").value = epochValueStart;

                }

                if (typeof monthend !== "undefined") {

                    var monthNumberend = monthMap[monthend];

                    var formattedDateend = yearend + "-" + monthNumberend.toString().padStart(2, '0') + "-" + dayend
                        .toString().padStart(2, '0');

                    var datetimeStringend = formattedDateend + "T" + endCustomTime;

                    var convertedDateTimeend = new Date(datetimeStringend);



                    // Check if the convertedDateTime is a valid date

                    if (isNaN(convertedDateTimeend)) {

                        console.error("Invalid date or time format");

                        return;

                    }

                    var formattedDateTimeend = convertedDateTimeend.toLocaleString('en-US', options);

                    var epochValueEnd = new Date(formattedDateTimeend).getTime() / 1000;



                    document.getElementById("convertedEndDateTime").value = formattedDateTimeend;

                    document.getElementById("epochEndTime").value = epochValueEnd;

                }

            }



            // Call the convertDateTime function whenever the timezone, date, or time changes

            document.getElementById("timezoneselect").addEventListener("change", convertDateTime);

            document.getElementById("datepicker").addEventListener("change", convertDateTime);

            document.getElementById("timePicker").addEventListener("change", convertDateTime);

            document.getElementById("Enddatepicker").addEventListener("change", convertDateTime);

            document.getElementById("EndtimePicker").addEventListener("change", convertDateTime);

            document.getElementById("Startnow").addEventListener("change", convertDateTime);

            document.getElementById("CustomDateTime").addEventListener("change", convertDateTime);

            document.getElementById("QueuingEnds").addEventListener("change", convertDateTime);

            document.getElementById("Customdateandtime").addEventListener("change", convertDateTime);

            document.getElementById("HaveEndDateTime").addEventListener("change", convertDateTime);





            // Initially call the function to populate the hidden field on page load

            convertDateTime();

        });
    </script>

    <script>
        document.querySelectorAll('input[name="startTime"]').forEach(function(radio) {

            radio.addEventListener('change', function() {

                if (this.value === '0') {

                    document.getElementById('CustomDateTimeId').style.display = 'block';

                } else {

                    document.getElementById('CustomDateTimeId').style.display = 'none';

                }

            });

        });



        document.addEventListener("DOMContentLoaded", function() {

            var sameAboveUrlCheckbox = document.getElementById('SameAboveUrl');

            var customUrlCheckbox = document.getElementById('CustomURl');

            var customUrlBox = document.getElementById('CustomUrlBoxId');

            customUrlCheckbox.addEventListener('change', function() {

                if (this.checked) {

                    customUrlBox.style.display = 'block';

                } else {

                    customUrlBox.style.display = 'none';

                }

            });

            sameAboveUrlCheckbox.addEventListener('change', function() {

                if (this.checked) {

                    customUrlBox.style.display = 'none';

                }

            });

        });
    </script>

    <script>
        function validateFile() {
            const fileInput = document.getElementById('iconQueue');
            const file = fileInput.files[0];
            const errorDiv = document.getElementById('iconQueue-error');

            errorDiv.innerHTML = ''; // Clear any previous error

            if (file && file.size > 1 * 1024 * 1024) { // 1MB = 1 * 1024 * 1024 bytes
                errorDiv.innerHTML = 'File size should not exceed 1 MB.';
                document.getElementById('iconQueue').value = '';
                fileInput.value = ''; // Clear the invalid file
            }
        }
    </script>
    <script>
        $(document).ready(function() {

            // Hide fields if the selected option value is not 0

            $('select[name="SMSCreateTemplate"]').change(function() {

                var selectedValue = $(this).val();

                if (selectedValue == '0') {

                    $('.smsTemp').removeClass('hide-element');

                } else {

                    $('.smsTemp').addClass('hide-element');

                }

            });

        });
    </script>

    <script>
        $(document).ready(function() {

            // Hide fields if the selected option value is not 0

            $('select[name="EmailCreateTemplate"]').change(function() {

                var selectedValue = $(this).val();

                if (selectedValue == '0') {

                    $('.emailTemp').removeClass('hide-element');

                } else {

                    $('.emailTemp').addClass('hide-element');

                }

            });

        });
    </script>
          <script>
                        document.addEventListener("DOMContentLoaded", function () {
                        const toggleSwitch = document.getElementById("flexSwitchCheckChecked");
                        const roomInput = document.getElementById("roomname");
                        const iconQueue = document.getElementById("iconQueue");
                        const queueIconLabel = document.getElementById("QueueIconLabel");
                        const queueRoomContainer = document.getElementById("queueRoomContainer");
                        const queueRoomIconContainer = document.getElementById("queueRoomIconContainer");
                        const queueTypeContainer = document.getElementById("queueTypeContainer");
                        const timezoneContainer = document.getElementById("timezoneContainer");
                        const queueSectionContainer = document.getElementById("queueSectionContainer");
                        // const nextbtn = document.getElementById("nextbtn");

                        // Add all the fields you want to control
                        const fieldsToToggle = [
                        roomInput,
                        iconQueue,
                        nextbtn,
                        queueIconLabel,
                        document.getElementById("queuetype"),
                        document.getElementById("timezoneselect"),
                        document.getElementById("startNowSelect"),
                        document.getElementById("CustomDateTime"),
                        document.getElementById("datepicker"),
                        document.getElementById("timePicker"),
                        document.getElementById("Customdateandtime"),
                        document.getElementById("HaveEndDateTime"),
                        document.getElementById("Enddatepicker"),
                        document.getElementById("EndtimePicker")
                        // document.getElementById("nextbtn");
                        ];

                        function toggleForm() {
                        if (toggleSwitch.checked) {
                        // Enable all fields
                        fieldsToToggle.forEach(field => {
                            if (field) {
                                field.disabled = false;
                                field.style.opacity = "1";
                                field.style.pointerEvents = "auto";
                            }
                        });
                        queueRoomContainer.style.opacity = "1";
                        queueRoomIconContainer.style.opacity = "1";
                        queueTypeContainer.style.opacity = "1";
                        timezoneContainer.style.opacity = "1";
                        queueSectionContainer.style.opacity = "1";
                        // nextbtn.style.opacity ="1";
                        } else {
                        // Disable all fields
                        fieldsToToggle.forEach(field => {
                            if (field) {
                                field.disabled = true;
                                field.style.opacity = "0.5";
                                field.style.pointerEvents = "none";
                            }
                        });
                        queueRoomContainer.style.opacity = "0.5";
                        queueRoomIconContainer.style.opacity = "0.5";
                        queueTypeContainer.style.opacity = "0.5";
                        timezoneContainer.style.opacity = "0.5";
                        queueSectionContainer.style.opacity = "0.5";
                        // nextbtn.style.opacity ="0.5";
                        }
                        }

                        toggleForm();
                        toggleSwitch.addEventListener("change", toggleForm);
                        });
        </script>
    <script>
        $(document).ready(function() {

            // Hide fields if the selected option value is not 0

            $('select[name="QueueRoomDesign_id"]').change(function() {

                var selectedValue = $('select[name="QueueRoomDesign_id"]').val();

                if (selectedValue == '0') {

                    $('.design-temp').find('input[name="QueueRoomDesignTemplate_name"]').val('').prop(
                        'readonly', false);

                    $('#mainSelectDesignTemp').show().prop('disabled', false);

                    $('#hideshowlanguege').hide();

                } else {

                    $.ajax({

                        url: '<?php echo env('APP_URL') . 'get-design-temp-data/'; ?>' + selectedValue,

                        type: 'GET',

                        success: function(response) {

                            var data = JSON.parse(response);



                            $('.design-temp').find('input[name="QueueRoomDesignTemplate_name"]')
                                .val(data.template_name).prop('readonly', true);



                            $('#mainSelectDesignTemp').hide();



                            $('#hideshowlanguege').empty();



                            $.each(data.languageData, function(index, language) {

                                var languageHtml = '<div class="row mb-3">';

                                languageHtml += '<div class="col-md-5">';

                                languageHtml +=
                                    '<input type="text" class="form-control selected_input" data-value="' +
                                    language.code + '" value="' + language.name + ' (' +
                                    language.native + ')" readonly>';

                                languageHtml += '</div>';

                                languageHtml += '<div class="col-md-7">';

                                languageHtml +=
                                    '<div class="d-flex align-items-center">';

                                languageHtml += '</div>';

                                languageHtml += '</div>';

                                languageHtml += '</div>';



                                $('#hideshowlanguege').append(languageHtml);

                            });

                            if (typeof data.in_line_template != 'undefined') {

                                var tab_h =
                                    '<ul class="nav nav-tabs design_template_tab" id="design_template_tab" role="designtablist">';

                                var tab_c =
                                    '<div class="tab-content card py-3 px-2" id="design_template_tabContent" style="height: auto;">';

                                $.each(data.in_line_template, function(indexkey,
                                    singleTemplate) {

                                    if (singleTemplate.type == 'queue_page')

                                        $(".in_line_template").html('<div class="">' +
                                            singleTemplate.htm_data + '</div>')

                                });



                            }

                        }

                    });

                }



                var selectedValue = $(this).val();

                if (selectedValue == '0') {

                    $('.design-temp').removeClass('hide-element');

                } else {

                    $('.design-temp').addClass('hide-element');

                }

            });

            <?php if(!empty($session_data['QueueRoomDesign_id'])) { ?>

            $('select[name="QueueRoomDesign_id"]').trigger('change');

            <?php } ?>

        });
    </script>

    <script>
        $(document).ready(function() {

            // Hide fields if the selected option value is not 0

            $('#SelectTemplate').change(function() {

                var selectedValue = $(this).val();

                if (selectedValue == '0') {

                    $('.bypassmanage').removeClass('hide-element');

                } else {

                    $('.bypassmanage').addClass('hide-element');

                }

            });

        });
    </script>

    <script>
        $(document).ready(function() {

            $('select[name="template_id"]').change(function() {

                var selectedValue = $(this).val();

                if (selectedValue == '0') {

                    $('.custom-field1').removeClass('hide-element');

                } else {

                    $('.custom-field1').addClass('hide-element');

                }

            });

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var smsEditor, emailEditor;



            // Initialize CKEditor for SMS Template Modal

            ClassicEditor

                .create(document.querySelector('#smsEditor'))

                .then(newEditor => {

                    smsEditor = newEditor;

                })

                .catch(error => {

                    console.error('CKEditor Error:', error);

                });



            // Initialize CKEditor for Email Template Modal

            ClassicEditor

                .create(document.querySelector('#emailEditor'))

                .then(newEditor => {

                    emailEditor = newEditor;
                })

                .catch(error => {

                    console.error('CKEditor Error:', error);

                });



            // "Understood" button for SMS Template Modal

            document.getElementById('smsUnderstoodBtn').addEventListener('click', function() {

                var smsEditorData = smsEditor.getData();

                document.getElementById('editorsmsContent').value = smsEditorData;

                $('#smsModal').modal('hide'); // Hide modal after storing data

            });



            // "Understood" button for Email Template Modal

            document.getElementById('emailUnderstoodBtn').addEventListener('click', function() {

                var emailEditorData = emailEditor.getData();

                document.getElementById('editoremailContent').value = emailEditorData;

                $('#emailModal').modal('hide'); // Hide modal after storing data

            });

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            var bypassRadio = document.querySelectorAll('.bypassroomqueue');



            bypassRadio.forEach(function(radio) {

                radio.addEventListener('change', function() {

                    if (this.value === '1') {

                        document.getElementById('bypassFields').style.display = '';

                    } else {

                        document.getElementById('bypassFields').style.display = 'none';

                    }

                });

            });

        });
    </script>

    <script>
        $(document).ready(function() {

            // Initially hide the fields

            <?php echo ($session_data['preQueueSetup'] ?? '') == '1' ?: "$('#preQueueField, #preQueueTimeField').hide();"; ?>





            // Show the fields when "I want to setup bypass" is clicked

            $('#PreSetupBypass').change(function() {

                if ($(this).is(':checked')) {

                    $('#preQueueField').show();

                    $('#preQueueTimeField').show();

                }

            });



            // Hide the fields when "I do not want to setup bypass" is clicked

            $('#PreNotSetupBypass').change(function() {

                if ($(this).is(':checked')) {

                    $('#preQueueField').hide();

                    $('#preQueueTimeField').hide();

                }

            });

        });
    </script>

    <script>
        // Get the toggle button

        const toggleButton = document.getElementById('toggleButtontwo');



        // Get the container div for buttons

        const buttonsContainer = document.getElementById('buttonsContainertwo');



        // Add event listener to toggle button

        toggleButton.addEventListener('change', function() {

            if (this.checked) {

                // If toggle is checked, show the buttons

                buttonsContainer.style.display = 'flex';

            } else {

                // If toggle is unchecked, hide the buttons

                buttonsContainer.style.display = 'none';

            }

        });
    </script>

    /*
    <script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>*/
    <script src="{{ asset('asset/js/external/ckeditor.js') }}" type="text/javascript"></script>


    <script src="{{ asset('asset/js/jquery.min.js') }}" type="text/javascript"></script>

    <script src="{{ asset('asset/js/queueRoom.js') }}" type="text/javascript"></script>

    <script src="{{ asset('asset/js/moment.min.js') }}"></script>

    <script src="{{ asset('asset/js/moment-timezone-with-data.min.js') }}"></script>

    <script src="{{ asset('asset/js/bootstrap-datetimepicker.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {

            $('.select2').select2({

                dropdownAutoWidth: true,

                width: 'auto'

            }).on('select2:open', function() {

                $('.select2-search__field').focus();

            });

        });

        /** For show new input box for the enter the time */
        document.getElementById('selectSessiontype').addEventListener('change', function() {
            const selectedValue = this.value;
            const timeInputContainer = document.getElementById('timeInputContainer');

            if (selectedValue === '1') {
                // Show the input box
                timeInputContainer.style.display = 'block';
            } else {
                // Hide the input box
                timeInputContainer.style.display = 'none';
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startRadio = document.querySelector('#Startnow');
            const endRadio = document.querySelector('#QueuingEnds');
            const errorMessage = document.querySelector('#radio-error');

            checkRadioSelection();

            startRadio.addEventListener('change', checkRadioSelection);
            endRadio.addEventListener('change', checkRadioSelection);

            function checkRadioSelection() {
                if (startRadio.checked && endRadio.checked) {
                    errorMessage.textContent = "Can't select both 'Start now' and 'End now' at the same time.";
                    startRadio.checked = false;
                    endRadio.checked = false;
                } else {
                    errorMessage.textContent = '';
                }
            }
        });
    </script>
    <script>
        function maximunTrafic() {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var checkIdUrl = "{{ route('getQueueTotalvisitors') }}";

            $.ajax({
                url: checkIdUrl,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: {
                    type: 1
                },
                success: function(response) {
                    console.log(response.calculateVisitorCount, 'response');
                    if (response.status == 1) {

                        $('#visitorLimitSpan').text(response.calculateVisitorCount);
                    }
                },
                error: function(xhr) {
                    console.error('Error occurred while fetching visitor limit.');
                }
            });
        }

        $(document).ready(function() {
            maximunTrafic();
        });
    </script>
@endsection
	