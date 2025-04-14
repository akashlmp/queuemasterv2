    @extends('common.layouts')

    @section('content')

        @extends('common.sidebar')
        @extends('common.header')

        <?php
        
        use Illuminate\Support\Facades\DB;
        // echo "<pre>";
        // print_r($queueRoom);
        // exit;
        ?>

        <?php
        use App\Models\admin\SubscriptionPlan;
        
        $user_plan_id = auth()->user()->subscription_plan_id;
        $QueueRoompermission = SubscriptionPlan::where('id', $user_plan_id)->select('setup_bypass', 'setup_pre_queue', 'setup_sms', 'setup_email', 'maximum_traffic')->first();
        
        ?>


        <link rel="stylesheet" href="{{ asset('asset/css/queueRoomCreate.css') }}">
        <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-datetimepicker.min.css') }}">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <style>
            .hide-element {
                display: none !important;
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
        <?php //dd($queueRoom);
        ?>
        <?php // if (Auth::user()->role == 1 AND $permission_for_this == 2) {
        //   $swap_permission_string = "";
        // } else {
        
        //   $swap_permission_string = "disabled";
        // }
        //dd($queueRoom);
        $swap_permission_string = '';
        ?>

        <main id="main" class="bgmain">
            <section class="SectionPadding">

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @elseif(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
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
                                            href="{{ url('create-queue') }}">{{ $queueRoom->queue_room_name }}</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs QueueingTabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-links active" id="step1-tab" data-bs-target="#step1" data-bs-toggle="tab"
                                        type="button" role="tab" aria-controls="step1" aria-selected="true">BASIC
                                        INFO</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-links" id="step2-tab" data-bs-target="#step2" data-bs-toggle="tab"
                                        type="button" role="tab" aria-controls="step2"
                                        aria-selected="false">CONFIGURATION</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-links" id="step3-tab" data-bs-target="#step3" data-bs-toggle="tab"
                                        type="button" role="tab" aria-controls="step3"
                                        aria-selected="false">DESIGN</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-links" id="step4-tab" data-bs-target="#step4" data-bs-toggle="tab"
                                        type="button" role="tab" aria-controls="step4" aria-selected="false">SMS /
                                        EMAIL</button>
                                </li>
                            </ul>
                            <form class="px-2" id="myForm" method="POST" action="{{ route('queue.update', $queueRoom->queue_room_id) }}"
                                enctype="multipart/form-data">
                                <div class="tab-content card py-3 px-2" id="myTabContent">

                                    @csrf
                                    <div class="tab-pane fade show active" id="step1" role="tabpanel"
                                        aria-labelledby="step1-tab">

                                        <div class="row m-0">
                                            <div class="col-md-12 ps-0 pt-2 pb-5">
                                                <div class="LeftRedborder ps-4">
                                                    <h5 class="FormHeading"><b>What’s the name of this queue room?</b></h5>
                                                    <p class="FormPara">The name is for your own reference only.</p>
                                                    <div class="mb-3">
                                                        <input type="text"
                                                            class="form-control FormInputBox @error('roomname') is-invalid @enderror"
                                                            id="roomname" name="roomname"
                                                            value="{{ $queueRoom->queue_room_name }}" <?php echo $swap_permission_string; ?>>
                                                        @if ($errors->has('roomname'))
                                                            <span class="text-danger">{{ $errors->first('roomname') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 ps-0 pt-2 pb-5">
                                                <div class="LeftGreenborder ps-4">
                                                    <h5 class="FormHeading"><b>What is the icon of this queue room?</b></h5>
                                                    <p class="FormPara">The icon is for your own reference only.</p>
                                                    <div class="mb-3">
                                                        @if (!empty($queueRoom->queue_room_icon))
                                                            <img src="{{ asset('images/' . $queueRoom->queue_room_icon) }}"
                                                                class="img-responsive iconQueueImg" alt="" />
                                                        @else
                                                            <img src="{{ asset('asset/img/image1.png') }}"
                                                                class="img-responsive iconQueueImg" alt="" />
                                                        @endif
                                                        <input type="file" class="form-control FormInputBox"
                                                            id="iconQueue" name="queue_icon" hidden>
                                                        <label for="iconQueue"
                                                            class="d-flex align-items-center iconQueueLabel mt-3"><span
                                                                class="material-symbols-outlined pe-2"> upload</span>
                                                            Upload</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 ps-0 pt-2 pb-5">
                                                <div class="LeftGreenborder ps-4">
                                                    <h5 class="FormHeading"><b>Which type you need to crete queue?</b></h5>
                                                    <p class="FormPara">The type is for your own reference only.</p>
                                                    <div class="mb-3">
                                                        <div class="custom-dropdown">
                                                            <div class="Timzone selectdrop">
                                                                <select
                                                                    class=" form-control FormInputBox TimezoneSelect custom-select  select2"
                                                                    id="queuetype" name="queuetype">
                                                                    {{-- <option value="" selected="selected">Select One</option> --}}
                                                                    <option value="onetime"
                                                                        {{ $queueRoom->queue_room_type === 'onetime' ? 'selected' : '' }}>
                                                                        One-time</option>
                                                                    <option value="fulltime"
                                                                        {{ $queueRoom->queue_room_type === 'fulltime' ? 'selected' : '' }}>
                                                                        24/7 queues</option>
                                                                </select>
                                                            </div>
                                                            <div id="queuetype-error" class="error-msg text-danger pt-2">
                                                            </div>
                                                            @if ($errors->has('queuetype'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('queuetype') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 ps-0 pt-2 pb-5">
                                                <div class="LeftGreenborder ps-4">
                                                    <h5 class="FormHeading"><b>What time zone is the queue room based on?</b>
                                                    </h5>
                                                    <p class="FormPara">This would affect the start and end time, as well as
                                                        the reporting.</p>
                                                    <div class="mb-3">
                                                        <div class="custom-dropdown">
                                                            <div class="Timzone selectdrop">
                                                                <select
                                                                    class="form-control FormInputBox TimezoneSelect custom-select select2"
                                                                    id="timezone" name="timezone">
                                                                    <?php
                                                                    $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
                                                                    foreach ($timezones as $timezone) {
                                                                        $tz = new DateTimeZone($timezone);
                                                                        $offset = $tz->getOffset(new DateTime()) / 3600;
                                                                        $offsetFormatted = sprintf('%+03d:%02d', $offset, (abs($offset) * 60) % 60);
                                                                        $timezoneName = str_replace('/', ' - ', $timezone);
                                                                        $timezoneName = str_replace('_', ' ', $timezoneName);
                                                                        // Compare the formatted offset with the queue_timezone
                                                                        $selected = $queueRoom->queue_timezone_name == $timezoneName ? 'selected' : '';
                                                                        echo "<option value=\"$offsetFormatted|$timezoneName\" $selected>$timezoneName (GMT $offsetFormatted)</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>

                                                            @if ($errors->has('timezone'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('timezome') }}</span>
                                                            @endif
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 ps-0 pt-2 pb-5">
                                                <div class="LeftGreenborder ps-4">
                                                    <h5 class="FormHeading"><b>When does the queue room start and end?</b></h5>
                                                    <p class="FormPara ">Queuing Starts:</p>
                                                    <div
                                                        class="d-flex align-items-center justify-content-start QueueDataCheckboxes">

                                                        <div class="form-check ps-0">
                                                            <input type="radio" id="Startnow" class="queueCheckBox"
                                                                name="startTime" value="1"
                                                                {{ $queueRoom->is_started == '1' ? 'checked' : '' }}
                                                                <?php echo $swap_permission_string; ?>>
                                                            <label for="Startnow" class="form-check-label">Start now</label>

                                                        </div>


                                                        <!-- Hidden input field to store the timezone -->
                                                        <input type="hidden" id="dateValue" name="startDateValue"
                                                            value="" <?php echo $swap_permission_string; ?>>
                                                        <input type="hidden" id="timeValue" name="startTimeValue"
                                                            value="" <?php echo $swap_permission_string; ?>>
                                                        <div class="form-check  ">
                                                            <input type="radio" id="CustomDateTime" class="queueCheckBox"
                                                                name="startTime" value="0"
                                                                {{ $queueRoom->is_started == '0' ? 'checked' : '' }}
                                                                <?php echo $swap_permission_string; ?>>
                                                            <label for="CustomDateTime" class="form-check-label"> Custom date
                                                                and time</label>
                                                        </div>
                                                    </div>
                                                    <!-- Error message positioned below the radio buttons -->
                                                    <p id="errorMessage" style="color: red; margin-top: 5px; display: none;">
                                                        You can't select both at the same time.</p>

                                                    @if ($errors->has('startTime'))
                                                        <span class="text-danger">{{ $errors->first('startTime') }}</span>
                                                    @endif
                                                    <div id="CustomDateTimeId" class="CustomDateTime pt-2"
                                                        style="display:none;">
                                                        <div class="row">
                                                            <div class="col-md-3 col-sm-3 col-xs-12 mb-3">
                                                                <!-- <label for="datepicker">Select Date:</label> -->
                                                                <div class="DateTimeIconBox">
                                                                    <span
                                                                        class="material-symbols-outlined DateTimeIcon">calendar_month</span>
                                                                    <input type="text" id="datepicker"
                                                                        class="form-control FormInputBox datepicker-input"
                                                                        name="custom_start_date" <?php echo $swap_permission_string; ?>>
                                                                    <span class="material-symbols-outlined DateTimeRightIcon"
                                                                        value="" <?php echo $swap_permission_string; ?>>
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
                                                                    <!-- <input type="time" id="timePicker" class="form-control form-select FormInputBox" name="custom_start_time"> -->
                                                                    <input type="time" id="timePicker"
                                                                        class="form-control form-select FormInputBox"
                                                                        name="custom_start_time">

                                                                    <span class="material-symbols-outlined DateTimeRightIcon">
                                                                        expand_more
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" id="convertedDateTime"
                                                                name="convertedstartDateTime"
                                                                value="{{ $queueRoom->timezone_based_start_datetime }}"
                                                                <?php echo $swap_permission_string; ?>>
                                                            <input type="hidden" id="startepochTime" name="startepochTime"
                                                                value="{{ $queueRoom->start_time_epoch }}"
                                                                <?php echo $swap_permission_string; ?>>
                                                            <div class="col-md-6 col-sm-6 col-xs-0">

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <br>
                                                    <p class="FormPara pt-3">Queuing Ends:</p>
                                                    <div
                                                        class="d-flex align-items-center justify-content-start QueueDataCheckboxes">

                                                        <div class="form-check mb-3 ps-0">
                                                            <input type="radio" id="QueuingEnds" class="queueCheckBox"
                                                                name="endTime" value="1"
                                                                {{ $queueRoom->is_ended == '1' ? 'checked' : '' }}
                                                                <?php echo $swap_permission_string; ?>>
                                                            <label for="QueuingEnds" class="form-check-label">End now</label>
                                                        </div>
                                                        <input type="hidden" id="endDate" name="endDateValue"
                                                            <?php echo $swap_permission_string; ?>>
                                                        <input type="hidden" id="endedTime" name="endTimeValue"
                                                            <?php echo $swap_permission_string; ?>>
                                                        <div class="form-check mb-3">
                                                            <input type="radio" id="Customdateandtime"
                                                                class="queueCheckBox" name="endTime" value="0"
                                                                {{ $queueRoom->is_ended == '0' ? 'checked' : '' }}
                                                                <?php echo $swap_permission_string; ?>>
                                                            <label for="Customdateandtime" class="form-check-label">Custom
                                                                date and time</label>
                                                        </div>
                                                        <div class="form-check mb-3">
                                                            <input type="radio" id="HaveEndDateTime" class="queueCheckBox"
                                                                name="endTime" value="2"
                                                                {{ $queueRoom->is_ended == '2' ? 'checked' : '' }}
                                                                <?php echo $swap_permission_string; ?>>
                                                            <label for="HaveEndDateTime" class="form-check-label">Do not have
                                                                end date and time</label>
                                                        </div>
                                                    </div>
                                                    <div id="EndCustomDateTimeId" class="CustomDateTime">
                                                        <div class="row">
                                                            <div class="col-md-3 col-sm-3 col-xs-12">
                                                                <!-- <label for="datepicker">Select Date:</label> -->
                                                                <div class="DateTimeIconBox">
                                                                    <span
                                                                        class="material-symbols-outlined DateTimeIcon">calendar_month</span>
                                                                    <input type="text" id="Enddatepicker"
                                                                        class="form-control FormInputBox datepicker-input"
                                                                        name="custom_end_date" <?php echo $swap_permission_string; ?>>
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
                                                                    <input type="time"
                                                                        class="form-control form-select FormInputBox"
                                                                        name="custom_end_time" <?php echo $swap_permission_string; ?>
                                                                        value="{{$queueRoom->end_time}}">
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
                                                        value="{{ $queueRoom->timezone_based_end_datetime }}"
                                                        <?php echo $swap_permission_string; ?>>
                                                    <input type="hidden" id="epochEndTime" name="epochEndTime"
                                                        value="{{ $queueRoom->end_time_epoch }}" <?php echo $swap_permission_string; ?>>
                                                    @if ($errors->has('endTime'))
                                                        <span class="text-danger">{{ $errors->first('endTime') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 d-flex text-end justify-content-end StepFormButtonsGrp">
                                                <button
                                                    class="btn btn-primary d-flex align-items-center justify-content-center nextBtn"
                                                    type="button" onclick="MultistepForm.nextStep(1)">Next <span
                                                        class="material-symbols-outlined ps-2">arrow_forward_ios</span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Tab 1 -->
                                    <div class="tab-pane fade" id="step2" role="tabpanel" aria-labelledby="step2-tab">
                                        <div class="row m-0">
                                            <div class="col-md-12 ps-0 pt-2 pb-5">
                                                <div class="LeftGreenborder ps-4">
                                                    <h5 class="FormHeading"><b>Who will redirect to the queue room?</b></h5>

                                                    <div class="mb-3">
                                                        <p class="FormPara pt-2 mb-2">Please select</p>
                                                        <select class="TempCreateSlt" name="template_id_data">
                                                            <option value="0">Create new template</option>
                                                            <?php
                                                            foreach ($queuetemplates as $queuetemplate) {
                                                                $selected = $queuetemplate->id == $queueRoom->queue_room_template_id ? 'selected' : '';
                                                                echo "<option value='{$queuetemplate->id}' $selected>{$queuetemplate->template_name}</option>";
                                                            }
                                                            ?>
                                                        </select>

                                                        @if ($errors->has('template_id_data'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('template_id_data') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3 custom-field1">
                                                        <p class="FormPara">Template name</p>
                                                        <input type="text" class="form-control FormInputBox"
                                                            id="template_name" name="template_name"
                                                            value="{{ $queueRoom->room_template_name }}" readonly
                                                            <?php echo $swap_permission_string; ?>>
                                                        @if ($errors->has('template_name'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('template_name') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="mb-3 custom-field1">
                                                        <p class="FormPara">Input URL:</p>
                                                        <input type="link" class="form-control FormInputBox"
                                                            id="input_url" name="input_url"
                                                            value="{{ $queueRoom->room_template_input_url }}" readonly
                                                            <?php echo $swap_permission_string; ?>>
                                                        @if ($errors->has('input_url'))
                                                            <span class="text-danger">{{ $errors->first('input_url') }}</span>
                                                        @endif
                                                    </div>

                                                    <div
                                                        class="d-flex align-items-center advanceSettngToggle mb-2 custom-field1">
                                                        <p class="FormPara mb-0">Advance settings</p>
                                                        <div class="form-checks form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="AdvanceSettingCheckBox" name="AdvanceSettingCheckBox"
                                                                value="1" disabled
                                                                {{ $queueRoom->room_template_is_advance_setting == 1 ? 'checked' : '' }}
                                                                <?php echo $swap_permission_string; ?>>
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
                                                    <div class="AdvanceSettingBox custom-field1">
                                                        <p class="FormPara">On top of the above setting, visitors would go to
                                                            the queuing room if</p>
                                                        User
                                                        <input type="hidden" name="advancedata" id="advancedata"
                                                            value="{{ $queueRoom->room_template_advance_setting_rules }}"
                                                            <?php echo $swap_permission_string; ?>>
                                                        <div class="table-responsive">
                                                            <table id="AdvanceSettingTable">
                                                                <tbody>
                                                                    <div id="advancesettingmodule">
                                                                        <?php
                                    $advance_setting = json_decode($queueRoom->room_template_advance_setting_rules);
                                    if (!is_null($advance_setting)) {

                                    ?>
                                                                        @foreach ($advance_setting as $index => $rule)
                                                                            <tr id="row_">
                                                                                <td>
                                                                                    @if ($index > 0)
                                                                                        <select
                                                                                            class="form-select form-control FormInputBox"
                                                                                            disabled
                                                                                            aria-label="Default select example"
                                                                                            <?php echo $swap_permission_string; ?>>
                                                                                            <option value="AND"
                                                                                                {{ $rule->operator == 'AND' ? 'selected' : '' }}>
                                                                                                AND</option>
                                                                                            <option value="OR"
                                                                                                {{ $rule->operator == 'OR' ? 'selected' : '' }}>
                                                                                                OR</option>
                                                                                        </select>
                                                                                    @else
                                                                                        <input type="hidden"
                                                                                            name="advance[operator][]"
                                                                                            id="advancedata"
                                                                                            value=<?php echo null; ?>
                                                                                            <?php echo $swap_permission_string; ?>>
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    <select
                                                                                        class="form-select form-control FormInputBox"
                                                                                        disabled
                                                                                        aria-label="Default select example"
                                                                                        <?php echo $swap_permission_string; ?>>
                                                                                        <option value="HOST_NAME"
                                                                                            {{ $rule->condition_place == 'HOST_NAME' ? 'selected' : '' }}>
                                                                                            HOST NAME</option>
                                                                                        <option value="PAGE_PATH"
                                                                                            {{ $rule->condition_place == 'PAGE_PATH' ? 'selected' : '' }}>
                                                                                            PAGE PATH</option>
                                                                                        <option value="PAGE_URL"
                                                                                            {{ $rule->condition_place == 'PAGE_URL' ? 'selected' : '' }}>
                                                                                            PAGE URL</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <select
                                                                                        class="form-select form-control FormInputBox"
                                                                                        disabled>
                                                                                        <option value="CONTAINS"
                                                                                            {{ $rule->condition == 'CONTAINS' ? 'selected' : '' }}>
                                                                                            CONTAINS</option>
                                                                                        <option value="DOES_NOT_CONTAIN"
                                                                                            {{ $rule->condition == 'DOES_NOT_CONTAIN' ? 'selected' : '' }}>
                                                                                            DOES NOT CONTAIN</option>
                                                                                        <option value="EQUALS"
                                                                                            {{ $rule->condition == 'EQUALS' ? 'selected' : '' }}>
                                                                                            EQUALS</option>
                                                                                        <option value="DOES_NOT_EQUAL"
                                                                                            {{ $rule->condition == 'DOES_NOT_EQUAL' ? 'selected' : '' }}>
                                                                                            DOES NOT EQUAL</option>
                                                                                    </select>
                                                                                </td>
                                                                                <td>
                                                                                    <input type="text"
                                                                                        class="form-control FormInputBox"
                                                                                        id="roomname" readonly
                                                                                        placeholder="registration"
                                                                                        value="{{ $rule->value }}"
                                                                                        <?php echo $swap_permission_string; ?>>
                                                                                </td>
                                                                                <td>
                                                                                    <button class="DeleteTableRow"
                                                                                        type="button" readonly>
                                                                                        <span
                                                                                            class="material-symbols-outlined">delete</span>
                                                                                    </button>
                                                                                </td>
                                                                                <!-- <td>  -->
                                                                            </tr>
                                                                        @endforeach
                                                                        <?php } ?>
                                                                    </div>
                                                                </tbody>
                                                            </table>
                                                            <table id="AdvanceSettingTable2">
                                                                <tbody>
                                                                    <tr>
                                                                        <td></td>
                                                                        <td>
                                                                            <select
                                                                                class="form-select form-control FormInputBox"
                                                                                aria-label="Default select example"
                                                                                name="advancedata[condition_place][]"
                                                                                <?php echo $swap_permission_string; ?>>
                                                                                <option value="HOST_NAME" selected>HOST NAME
                                                                                </option>
                                                                                <option value="PAGE_PATH">PAGE PATH</option>
                                                                                <option value="PAGE_URL">PAGE URL</option>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select
                                                                                class="form-select form-control FormInputBox"
                                                                                name="advancedata[condition][]"
                                                                                <?php echo $swap_permission_string; ?>>
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
                                                                                name="advancedata[value][]"
                                                                                <?php echo $swap_permission_string; ?>>
                                                                        </td>
                                                                        <td>
                                                                            <button id="addButton" class="AddTableRow"
                                                                                type="button"><span
                                                                                    class="material-symbols-outlined">add</span></button>
                                                                        </td>
                                                                    </tr>
                                                                    <input type="hidden" id="advancedata"
                                                                        name="advancedata">
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
                                                    <div class="d-flex align-items-center QueueDataCheckboxes"
                                                        bis_skin_checked="1">
                                                        <div class="form-check mb-3 ps-0" bis_skin_checked="1">
                                                            <input type="radio" id="SameAboveUrl" class="queueCheckBox"
                                                                name="CustomURl" value="1"
                                                                {{ $queueRoom->is_customurl == 1 ? 'checked' : '' }}
                                                                <?php echo $swap_permission_string; ?>>
                                                            <label for="SameAboveUrl" class="form-check-label">Same as the URL
                                                                above</label>
                                                        </div>
                                                        <div class="form-check mb-3" bis_skin_checked="1">
                                                            <input type="radio" id="CustomURl" class="queueCheckBox"
                                                                name="CustomURl" value="2"
                                                                {{ $queueRoom->is_customurl == 2 ? 'checked' : '' }}
                                                                <?php echo $swap_permission_string; ?>>
                                                            <label for="CustomURl" class="form-check-label">Custom URL</label>
                                                        </div>
                                                    </div>
                                                    <div class="form-group " id="CustomUrlBoxId">
                                                        <input type="link" class="form-control FormInputBox"
                                                            id="custom_url" name="custom_url"
                                                            value="{{ $queueRoom->target_url }}" <?php echo $swap_permission_string; ?>>
                                                    </div>
                                                </div>
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
                                                        <option value="1"
                                                            {{ $queueRoom->session_type == 1 ? 'selected' : '' }}>According to
                                                            time</option>
                                                        <option value="2"
                                                            {{ $queueRoom->session_type == 2 ? 'selected' : '' }}>According to
                                                            script</option>
                                                    </select>

                                                    <div id="sessionType" style="color: red;"></div>

                                                    <!-- Input box (hidden by default) -->
                                                    <div id="timeInputContainer" style="display: none; margin-top: 10px;">
                                                        <label for="timeInput">Enter Time:</label>
                                                        <input type="text" class="form-control" name="time_input"
                                                            id="timeInput" placeholder="Enter time in minutes (e.g., 2)"
                                                            value="{{ $queueRoom->time_input }}">
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
                                                        <!-- I want to allow <span><input type="number" id="TotalVisitorid" onkeyup="countchecker();" class="TotalVisitor" name="max_traffic" max="{{ $QueueRoompermission ? $QueueRoompermission->maximum_traffic : '' }}" value="{{ $QueueRoompermission ? $QueueRoompermission->maximum_traffic : '' }}" <?php echo $swap_permission_string; ?>></span>visitors to enter the -->
                                                        I want to allow <span><input type="number" id="TotalVisitorid"
                                                                onkeyup="countchecker();" class="TotalVisitor"
                                                                name="max_traffic"
                                                                max="{{ $QueueRoompermission->maximum_traffic }}"
                                                                value="{{ $queueRoom->max_traffic_visitor }}"
                                                                <?php echo $swap_permission_string; ?>></span>visitors to enter the
                                                        protected site per minute.
                                                    </div>
                                                    <div id="maxTrafficMessaged" style="color: red;"></div>
                                                    @if ($errors->has('max_traffic'))
                                                        <span class="text-danger">{{ $errors->first('max_traffic') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <!-- End Tab 2 Section 3 -->
                                            @if (isset($QueueRoompermission) && $QueueRoompermission->setup_bypass == 1)
                                                <div class="col-md-12 ps-0 pt-2 pb-5">
                                                    <div class="LeftGreenborder ps-4">
                                                        <h5 class="FormHeading"><b>Do you want to allow people to bypass the
                                                                queue room?</b></h5>
                                                        <p class="FormPara">Such as allowing VIP to enter the site, or allow
                                                            admins to monitor even during peak period.</p>

                                                        <div class="d-flex align-items-center QueueDataCheckboxes">
                                                            <div class="form-check mb-3 ps-0">
                                                                <input type="radio" id="SetupBypass"
                                                                    class="queueCheckBox bypassroomqueue" name="SetupBypass"
                                                                    value="1"
                                                                    {{ $queueRoom->enable_bypass == 1 ? 'checked' : '' }}
                                                                    <?php echo $swap_permission_string; ?>>
                                                                <label for="SetupBypass" class="form-check-label">I want to
                                                                    setup bypass</label>
                                                            </div>
                                                            <div class="form-check mb-3">
                                                                <input type="radio" id="NotSetupBypass"
                                                                    class="queueCheckBox bypassroomqueue" name="SetupBypass"
                                                                    value="0"
                                                                    {{ $queueRoom->enable_bypass == 0 ? 'checked' : '' }}
                                                                    <?php echo $swap_permission_string; ?>>
                                                                <label for="NotSetupBypass" class="form-check-label">I do not
                                                                    want to setup bypass</label>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('SetupBypass'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('SetupBypass') }}</span>
                                                        @endif

                                                        @php
                                                            $bypassDisplay =
                                                                old('SetupBypass') == '1' ? '' : 'display:none;';
                                                        @endphp

                                                        <div id="bypassFields" style="{{ $bypassDisplay }}">
                                                            <div class="form-group">
                                                                <label for="SelectTemplate" class="FormInputBoxLabel">Please
                                                                    select</label>
                                                                <div class="row">
                                                                    <div class="col-md-3">
                                                                        <select class="form-select FormInputBox"
                                                                            id="SelectTemplate" name="byPassSelectTemplateid"
                                                                            <?php echo $swap_permission_string; ?>>
                                                                            <option value="0">Create new template</option>
                                                                            <?php
                                                                            foreach ($bypasstemplates as $bypasstemplate) {
                                                                                $selected = $bypasstemplate->id == $queueRoom->bypass_temp_id ? 'selected' : '';
                                                                                echo "<option value='{$bypasstemplate->id}' $selected>{$bypasstemplate->template_name}</option>";
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                        @if ($errors->has('byPassSelectTemplateid'))
                                                                            <span
                                                                                class="text-danger">{{ $errors->first('byPassSelectTemplateid') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-9">
                                                                    <!-- Toggle Button -->
                                                                    <!-- End of Toggle Button -->
                                                                    <!-- Buttons to be toggled -->
                                                                    <!-- End of Buttons to be toggled -->
                                                                </div>
                                                            </div>
                                                            <div class="form-group bypassmanage">
                                                                <label for="templateName" class="FormInputBoxLabel">Template
                                                                    name</label>
                                                                <input type="text" class="form-control FormInputBox"
                                                                    id="templateName" readonly
                                                                    name="byPassSelectTemplate_name"
                                                                    value="{{ $queueRoom->bypass_temp_name }}"
                                                                    <?php echo $swap_permission_string; ?>>
                                                                @if ($errors->has('byPassSelectTemplate_name'))
                                                                    <span
                                                                        class="text-danger">{{ $errors->first('byPassSelectTemplate_name') }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="form-group bypassmanage">
                                                                <label for="Bypassurl" class="FormInputBoxLabel">Bypass
                                                                    URL</label>
                                                                <input type="link" class="form-control FormInputBox"
                                                                    id="Bypassurl" readonly name="Bypassurl"
                                                                    value="{{ $queueRoom->bypass_temp_url }}"
                                                                    <?php echo $swap_permission_string; ?>>
                                                                @if ($errors->has('Bypassurl'))
                                                                    <span
                                                                        class="text-danger">{{ $errors->first('Bypassurl') }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="">
                                                                <div class="form-check form-switch p-0 mt-3">
                                                                    <label class="form-check-label" for="toggleButton"
                                                                        style="color: #159AA1; font-weight: 800;">Passcode
                                                                        protection</label>
                                                                    <input class="form-check-input advancedtoggle"
                                                                        type="checkbox" id="toggleButton"
                                                                        <?php echo $swap_permission_string; ?>>
                                                                </div>
                                                                <div id="toggleButtons"
                                                                    style="display: none; margin-top: 10px;">
                                                                    <div class="d-flex align-items-center mt-3 downloadFlex ">
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
                                                                                name="filebyPass">
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
                                                </div>
                                            @endif


                                            <!-- End Tab 2 Section 4 -->
                                            @if (isset($QueueRoompermission) && $QueueRoompermission->setup_pre_queue == 1)
                                                <div class="col-md-12 ps-0 pt-2 pb-5">
                                                    <div class="LeftGreenborder ps-4">
                                                        <h5 class="FormHeading"><b>Do you want to setup pre-queue?</b></h5>
                                                        <p class="FormPara">A pre-queue can prevent your website crashes from
                                                            early demand and deliver online fairness.</p>
                                                        <div class="d-flex align-items-center QueueDataCheckboxes">
                                                            <div class="form-check mb-3 ps-0">
                                                                <input type="radio" id="PreSetupBypass"
                                                                    class="queueCheckBox" name="preQueueSetup" value="1"
                                                                    {{ $queueRoom->is_prequeue == 1 ? 'checked' : '' }}
                                                                    <?php echo $swap_permission_string; ?>>
                                                                <label for="PreSetupBypass" class="form-check-label">I want to
                                                                    setup pre-queue</label>
                                                            </div>
                                                            <div class="form-check mb-3">
                                                                <input type="radio" id="PreNotSetupBypass"
                                                                    class="queueCheckBox" name="preQueueSetup" value="0"
                                                                    {{ $queueRoom->is_prequeue == 0 ? 'checked' : '' }}
                                                                    <?php echo $swap_permission_string; ?>>
                                                                <label for="PreNotSetupBypass" class="form-check-label">I do
                                                                    not want to setup pre-queue</label>
                                                            </div>
                                                        </div>
                                                        @if ($errors->has('preQueueSetup'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('preQueueSetup') }}</span>
                                                        @endif
                                                        <div class="TotalVisitortxt ps-4 hidden" id="preQueueField">
                                                            Pre-queue starts:
                                                        </div>
                                                        <div class="TotalVisitortxt pt-2 ps-4 hidden" id="preQueueTimeField">
                                                            <span><input type="text" class="TotalVisitor ms-0 "
                                                                    name="BeforeTimeforPrequeue"
                                                                    value="{{ $queueRoom->prequeue_starttime }}"
                                                                    <?php echo $swap_permission_string; ?>></span> minutes before the queuing
                                                            start time.
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
                                                    type="button" onclick="MultistepForm.nextStep(2)">Next <span
                                                        class="material-symbols-outlined ps-2">
                                                        arrow_forward_ios
                                                    </span></button>
                                            </div>
                                        </div>
                                        <!-- Next Prev Buttons -->
                                    </div>

                                    <!-- End Tab 2 -->
                                    <div class="tab-pane fade" id="step3" role="tabpanel" aria-labelledby="step3-tab">
                                        <div class="row m-0">
                                            <div class="col-md-12 ps-0 pt-2 pb-5">
                                                <div class="LeftGreenborder ps-4">
                                                    <h5 class="FormHeading"><b>Queue room design</b></h5>
                                                    <span>Please create or selected template</span>
                                                    <div class="row mt-3">
                                                        <div class="col-md-3">
                                                            <select class="form-select FormInputBox"
                                                                aria-label="Default select example" name="QueueRoomDesign_id"
                                                                <?php echo $swap_permission_string; ?>>
                                                                <option value="0">Create new template</option>
                                                                <?php
                                                                foreach ($designtemplates as $designtemplate) {
                                                                    $selected = $designtemplate->id == $queueRoom->design_template_id ? 'selected' : '';
                                                                    echo "<option value='{$designtemplate->id}' $selected>{$designtemplate->template_name}</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                            @if ($errors->has('QueueRoomDesign_id'))
                                                                <span
                                                                    class="text-danger">{{ $errors->first('QueueRoomDesign_id') }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-9"></div>
                                                    </div>

                                                    <div class="form-group design-temp">
                                                        <label for="SelectTemplate" class="FormInputBoxLabel">Please Enter Your Template Name</label>
                                                        <input type="text" class="form-control FormInputBox"
                                                            name="QueueRoomDesignTemplate_name" readonly
                                                            value="{{ $queueRoom->design_template_name }}"
                                                            <?php echo $swap_permission_string; ?>>
                                                        @if ($errors->has('QueueRoomDesignTemplate_name'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('QueueRoomDesignTemplate_name') }}</span>
                                                        @endif
                                                    </div>
        
                                                    <div class="form-group design-temp <?php echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">
                                                        <label for="SelectTemplate"   class="FormInputBoxLabel">Status Message Setting</label>
                                                        <input type="text" placeholder="You can set up the real-time message shown on the template"  class="form-control FormInputBox"
                                                            name="QueueRoomDesignTemplate_name_2"
                                                            value="{{ old('QueueRoomDesignTemplate_name_2', $session_data['QueueRoomDesignTemplate_name_2'] ?? '') }}"
                                                            id="statusMessageInput">
                                                        <div id="QueueRoomDesignTemplate_name-error-2" class="error-msg text-danger pt-2"></div>
                                                    
                                                        @if ($errors->has('QueueRoomDesignTemplate_name_2'))
                                                            <span class="text-danger qrIdError">{{ $errors->first('QueueRoomDesignTemplate_name_2') }}</span>
                                                        @endif
                                                    
                                                        <div class="mt-2">
                                                            <button type="button" id="cancelBtn" class="btn btn-outline-custom-cancel" disabled>Cancel</button>
                                                            <button type="button" id="confirmBtn" class="btn btn-outline-custom-confirm" disabled>Confirm</button>
                                                        </div>
                                                    </div>
                                                    <br><br>

                                                    <h5 class="FormHeading"><b>Pre-Queue room design</b></h5>

                                                <span id="langErrorBox" class="text-danger"></span>

                                                <span>Please create or selected template</span>

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

                                                
                                                        <label for="SelectTemplate" class="FormInputBoxLabel">Please Enter Your Template Name</label>
                                                        <input type="text" class="form-control FormInputBox"
                                                            name="QueueRoomDesignTemplate_name" readonly
                                                            value="{{ $queueRoom->design_template_name }}"
                                                            <?php echo $swap_permission_string; ?>>
                                                        @if ($errors->has('QueueRoomDesignTemplate_name'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('QueueRoomDesignTemplate_name') }}</span>
                                                        @endif
                                            

                                                </div>
                                                  
                                                <div class="form-group design-temp <?php echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">
                                                    <label for="SelectTemplate"   class="FormInputBoxLabel">Status Message Setting</label>
                                                    <input type="text" placeholder="You can set up the real-time message shown on the template"  class="form-control FormInputBox"
                                                        name="QueueRoomDesignTemplate_name_2"
                                                        value="{{ old('QueueRoomDesignTemplate_name_2', $session_data['QueueRoomDesignTemplate_name_2'] ?? '') }}"
                                                        id="statusMessageInput2">
                                                    <div id="QueueRoomDesignTemplate_name-error-2" class="error-msg text-danger pt-2"></div>
                                                
                                                    @if ($errors->has('QueueRoomDesignTemplate_name_2'))
                                                        <span class="text-danger qrIdError">{{ $errors->first('QueueRoomDesignTemplate_name_2') }}</span>
                                                    @endif
                                                
                                                    <div class="mt-2">
                                                        <button type="button" id="cancelBtn2" class="btn btn-outline-custom-cancel" disabled>Cancel</button>
                                                        <button type="button" id="confirmBtn2" class="btn btn-outline-custom-confirm" disabled>Confirm</button>
                                                    </div>
                                                </div>

                                                    {{-- <p class="FormPara pt-3 languagePara design-temp <?php echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">What
                                                        language do you want the queue room to display?</p> --}}

                                                 

                                                <br><br>
                                                <h5 class="FormHeading"><b>Post-Queue room design</b></h5>

                                                <span id="langErrorBox" class="text-danger"></span>

                                                <span>Please create or selected template</span>

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

                                                
                                                        <label for="SelectTemplate" class="FormInputBoxLabel">Please Enter Your Template Name</label>
                                                        <input type="text" class="form-control FormInputBox"
                                                            name="QueueRoomDesignTemplate_name" readonly
                                                            value="{{ $queueRoom->design_template_name }}"
                                                            <?php echo $swap_permission_string; ?>>
                                                        @if ($errors->has('QueueRoomDesignTemplate_name'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('QueueRoomDesignTemplate_name') }}</span>
                                                        @endif
                                                   
                                                </div>
                                                  
                                                <div class="form-group design-temp <?php echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">
                                                    <label for="SelectTemplate"   class="FormInputBoxLabel">Status Message Setting</label>
                                                    <input type="text" placeholder="You can set up the real-time message shown on the template"  class="form-control FormInputBox"
                                                        name="QueueRoomDesignTemplate_name_2"
                                                        value="{{ old('QueueRoomDesignTemplate_name_2', $session_data['QueueRoomDesignTemplate_name_2'] ?? '') }}"
                                                        id="statusMessageInput3">
                                                    <div id="QueueRoomDesignTemplate_name-error-2" class="error-msg text-danger pt-2"></div>
                                                
                                                    @if ($errors->has('QueueRoomDesignTemplate_name_2'))
                                                        <span class="text-danger qrIdError">{{ $errors->first('QueueRoomDesignTemplate_name_2') }}</span>
                                                    @endif
                                                
                                                    <div class="mt-2">
                                                        <button type="button" id="cancelBtn3" class="btn btn-outline-custom-cancel" disabled>Cancel</button>
                                                        <button type="button" id="confirmBtn3" class="btn btn-outline-custom-confirm" disabled>Confirm</button>
                                                    </div>
                                                </div>


                                                <div class="row mb-3 design-temp <?php   echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">

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

                                                    <div class="col-md-7 design-temp">

                                                    </div>

                                                </div>

                                                <br><br>
                                                <h5 class="FormHeading"><b>Priority Access room page</b></h5>

                                                <span id="langErrorBox" class="text-danger"></span>

                                                <span>Please create or selected template</span>

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

                                                 
                                                        <label for="SelectTemplate" class="FormInputBoxLabel">Please Enter Your Template Name</label>
                                                        <input type="text" class="form-control FormInputBox"
                                                            name="QueueRoomDesignTemplate_name" readonly
                                                            value="{{ $queueRoom->design_template_name }}"
                                                            <?php echo $swap_permission_string; ?>>
                                                        @if ($errors->has('QueueRoomDesignTemplate_name'))
                                                            <span
                                                                class="text-danger">{{ $errors->first('QueueRoomDesignTemplate_name') }}</span>
                                                        @endif
                                                  
                                                </div>
                                                  
                                                <div class="form-group design-temp <?php echo ($session_data['QueueRoomDesign_id'] ?? '') != '' ? 'hide-element' : ''; ?>">
                                                    <label for="SelectTemplate"   class="FormInputBoxLabel">Status Message Setting</label>
                                                    <input type="text" placeholder="You can set up the real-time message shown on the template"  class="form-control FormInputBox"
                                                        name="QueueRoomDesignTemplate_name_2"
                                                        value="{{ old('QueueRoomDesignTemplate_name_2', $session_data['QueueRoomDesignTemplate_name_2'] ?? '') }}"
                                                        id="statusMessageInput4">
                                                    <div id="QueueRoomDesignTemplate_name-error-2" class="error-msg text-danger pt-2"></div>
                                                
                                                    @if ($errors->has('QueueRoomDesignTemplate_name_2'))
                                                        <span class="text-danger qrIdError">{{ $errors->first('QueueRoomDesignTemplate_name_2') }}</span>
                                                    @endif
                                                
                                                    <div class="mt-2">
                                                        <button type="button" id="cancelBtn4" class="btn btn-outline-custom-cancel" disabled>Cancel</button>
                                                        <button type="button" id="confirmBtn4" class="btn btn-outline-custom-confirm" disabled>Confirm</button>
                                                    </div>
                                                </div>
                                                    {{-- <p class="FormPara pt-3 languagePara design-temp">What language do you want
                                                        the queue room to display?</p>
                                                    <div id="hideshowlanguege">
                                                        <p>your selected langueges :-
                                                            <?php
                                                            
                                                            $designTemplateLanguages = json_decode($queueRoom->design_template_languages, true);
                                                            
                                                            if (json_last_error() === JSON_ERROR_NONE && is_array($designTemplateLanguages)) {
                                                                $matchedLanguages = DB::table('languages')->whereIn('code', $designTemplateLanguages)->get();
                                                            } else {
                                                                // Handle the case where the JSON is invalid or not an array
                                                                $matchedLanguages = [];
                                                            }
                                                            
                                                            foreach ($matchedLanguages as $language) {
                                                                echo '<div class="row mb-3">';
                                                                echo '<div class="col-md-5">';
                                                                echo '<input type="text" class="form-control selected_input" data-value="' . $language->code . '" value="' . $language->name . ' (' . $language->native . ')" readonly>';
                                                                echo '</div>';
                                                                echo '<div class="col-md-7">';
                                                                echo '<div class="d-flex align-items-center">';
                                                                echo '</div>';
                                                                echo '</div>';
                                                                echo '</div>';
                                                            }
                                                            ?>


                                                            <br>
                                                            ( Note : The languages displayed are your selected languages. If you
                                                            update them, the old languages will be replaced by the new ones.)
                                                        </p>
                                                    </div> --}}
                                                
                                                    <div id="dynamicSelectsDesignTemp">
                                                        <!-- Dynamic select lists will be added here -->
                                                    </div>
                                                    {{-- <div class="row mb-3 design-temp">
                                                        <div class="col-md-5">
                                                            <select id="mainSelectDesignTemp" class="form-select"
                                                                aria-label="Default select example" disabled>
                                                                <option selected>Please select...</option>
                                                                @php
                                                                    $sortedLanguages = $languages->sortBy('name');
                                                                @endphp
                                                                @foreach ($sortedLanguages as $language)
                                                                    <option value="{{ $language->code }}">
                                                                        {{ $language->name . ' (' . $language->native . ')' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>

                                                        </div>
                                                        <div class="col-md-7 design-temp">
                                                        </div>
                                                    </div> --}}

                                                    
                                                    {{-- <p class="FormPara pt-3 languagePara design-temp">Upload your all 4
                                                        type of .html file, If you have your own templates!</p>
                                                        <div class="col-md-3">
                                                            <label for="queueHtmlFile" class="FormInputBoxLabel">Queue
                                                                page</label>
                                                            <input type="file" class="form-control FormInputBox"
                                                            name="queueHtmlFile" accept=".html" id="queueHtmlFile" />
                                                            <p style="font-size: 12px; color: red; font-weight: bold;">
                                                                <?php if (!empty($queueRoom->queue_html_page_url)) { ?>File is already uploade. If you need to
                                                                update your file please select and update.<?php }?>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="preQueueHtmlFile" class="FormInputBoxLabel">Pre queue
                                                                page</label>
                                                            <input type="file" class="form-control FormInputBox"
                                                                name="preQueueHtmlFile" accept=".html"
                                                                id="preQueueHtmlFile" />
                                                                <p style="font-size: 12px; color: red; font-weight: bold;">
                                                                <?php if (!empty($queueRoom->postqueue_html_page_url)) { ?>File is already uploade. If you need to
                                                                update your file please select and update.<?php }?>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="postQueueHtmlFile" class="FormInputBoxLabel">Post
                                                                queue page</label>
                                                            <input type="file" class="form-control FormInputBox"
                                                                name="postQueueHtmlFile" accept=".html"
                                                                id="postQueueHtmlFile" />
                                                            <p style="font-size: 12px; color: red; font-weight: bold;">
                                                                <?php if (!empty($queueRoom->priorityqueue_html_page_url)) { ?>File is already uploade. If you need to
                                                                update your file please select and update.<?php }?>
                                                            </p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label for="priorityAccessPageHtmlFile"
                                                                class="FormInputBoxLabel">Priority access page</label>
                                                            <input type="file" class="form-control FormInputBox"
                                                                name="priorityAccessPageHtmlFile" accept=".html"
                                                                id="priorityAccessPageHtmlFile" />
                                                                <p style="font-size: 12px; color: red; font-weight: bold;">
                                                                <?php if (!empty($queueRoom->prequeue_html_page_url)) { ?>File is already uploade. If you need to
                                                                update your file please select and update.<?php }?>
                                                            </p>
                                                        </div>
                                                    </div> --}}
                                                    
                                  
                                                @if ($errors->has('queue_language'))
                                                    <span class="text-danger">{{ $errors->first('queue_language') }}</span>
                                                @endif
                                            <div class=" mt-3 design-temp d-flex align-items-center  justify-content-between">
                                                <ul class="nav nav-tabs gap-4 " id="myTab" role="tablist">
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link active" id="Queue-Page-tab" data-target="queue" data-bs-toggle="tab" 
                                                            data-bs-target="#QueuePage" type="button" role="tab" aria-controls="QueuePage"
                                                            aria-selected="true">Queue Page</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="Pre-queue-Page-tab" data-target="prequeue" data-bs-toggle="tab" 
                                                            data-bs-target="#PrequeuePage" type="button" role="tab" aria-controls="PrequeuePage"
                                                            aria-selected="false">Pre-queue Page</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="PostqueuePage-tab" data-target="postqueue" data-bs-toggle="tab" 
                                                            data-bs-target="#PostqueuePage" type="button" role="tab" aria-controls="PostqueuePage"
                                                            aria-selected="false">Post-queue Page</button>
                                                    </li>
                                                    <li class="nav-item" role="presentation">
                                                        <button class="nav-link" id="PriorityAccessPage-tab" data-target="priority" data-bs-toggle="tab" 
                                                            data-bs-target="#PriorityAccessPage" type="button" role="tab" aria-controls="PriorityAccessPage"
                                                            aria-selected="false">Priority Access Page</button>
                                                    </li>
                                                </ul>
                                                
                                                @if ($queueRoom->is_uploaded != 1)
                                                    <div class="row  design-temp">
                                                        <div class="col-md-12">
                                                            <button type="button"
                                                                onclick="window.location.href='<?php echo env('APP_URL') . 'edit-inline-room/' . $queueRoom->queue_room_id; ?>'"
                                                                class="btn editBtn" id="DesignEditBtn">Edit Template<i
                                                                    class="fa fa-pencil-square-o ps-1"
                                                                    aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                                <div class="in-line-template" style="margin: 20px 10px;height: 100vh;">
                                                    <?php if(!empty($in_line_template)) { 
                                                      echo html_entity_decode($in_line_template);
                                                      } ?>
                                                </div>
                                                <input type="hidden" id="jsonlangDesignTemp" name="queue_language"
                                                    value="" <?php echo $swap_permission_string; ?>>
                                            </div>
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
                                                    onclick="MultistepForm.nextStep(3)">Next <span
                                                        class="material-symbols-outlined ps-2">arrow_forward_ios</span></button>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- tab 3 end -->
                                    <div class="tab-pane fade" id="step4" role="tabpanel" aria-labelledby="step4-tab">
                                        <div class="row m-0">

                                            @if (isset($QueueRoompermission) && $QueueRoompermission->setup_sms == 1)
                                                <div class="col-md-12 ps-0 pt-2 pb-5">
                                                    <div class="LeftGreenborder ps-4">
                                                        <h5 class="FormHeading"><b>SMS Notice</b></h5>
                                                        <div class="row mt-3">
                                                            <div class="col-md-3">
                                                                <select class="form-select FormInputBox"
                                                                    name="SMSCreateTemplate" <?php echo $swap_permission_string; ?>>
                                                                    <option value="0">Create new template</option>
                                                                    <?php
                                                                    foreach ($smstemplates as $smstemplate) {
                                                                        $selected = $smstemplate->id == $queueRoom->sms_template_id ? 'selected' : '';
                                                                        echo "<option value='{$smstemplate->id}' $selected>{$smstemplate->sms_template_name}</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-9"></div>
                                                        </div>

                                                        <div class="form-group smsTemp">
                                                            <label for="SMSTemplate" class="FormInputBoxLabel">Template
                                                                name</label>
                                                            <input type="text" class="form-control FormInputBox"
                                                                name="SMSTemplate"
                                                                value="{{ $queueRoom->sms_template_name }}" readonly
                                                                <?php echo $swap_permission_string; ?>>
                                                        </div>
                                                        <div class="row mt-3">
                                                            <input type="hidden" id="editorsmsContent"
                                                                name="editorsmsContent"
                                                                value="{{ $queueRoom->sms_template_html_content }}"
                                                                <?php echo $swap_permission_string; ?>>

                                                            <div class="col-md-12 smsTemp">
                                                                <button type="button" class="btn btn-primary btn editBtn"
                                                                    id="SMSEditBtn" data-bs-toggle="modal"
                                                                    data-bs-target="#smsModal">
                                                                    Edit<i class="fa fa-pencil-square-o ps-1"
                                                                        aria-hidden="true" <?php echo $swap_permission_string; ?>></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if (isset($QueueRoompermission) && $QueueRoompermission->setup_email == 1)
                                                <div class="col-md-12 ps-0 pt-2 pb-5">
                                                    <div class="LeftGreenborder ps-4">
                                                        <h5 class="FormHeading"><b>Email notice</b></h5>
                                                        <div class="row mt-3">
                                                            <div class="col-md-3">
                                                                <select class="form-select FormInputBox"
                                                                    name="EmailCreateTemplate" <?php echo $swap_permission_string; ?>>
                                                                    <option value="0">Create new template</option>
                                                                    <?php
                                                                    foreach ($emailtemplates as $emailtemplate) {
                                                                        $selected = $emailtemplate->id == $queueRoom->email_template_id ? 'selected' : '';
                                                                        echo "<option value='{$emailtemplate->id}' $selected>{$emailtemplate->email_template_name}</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-9"></div>
                                                        </div>

                                                        <div class="form-group emailTemp">
                                                            <label for="EmailTemplate" class="FormInputBoxLabel">Template
                                                                name</label>
                                                            <input type="text" class="form-control FormInputBox"
                                                                name="EmailTemplate"
                                                                value="{{ $queueRoom->email_template_name }}" readonly>
                                                        </div>
                                                        <input type="hidden" id="editoremailContent"
                                                            name="editoremailContent"
                                                            value="{{ $queueRoom->email_template_html_content }}">
                                                        <div class="row mt-3 emailTemp">
                                                            <div class="col-md-12">
                                                                <button type="button" class="btn editBtn" id="EmailEditBtn"
                                                                    data-bs-toggle="modal" data-bs-target="#emailModal">
                                                                    Edit <i class="fa fa-pencil-square-o ps-1"
                                                                        aria-hidden="true" <?php echo $swap_permission_string; ?>></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif


                                        </div>
                                        <!-- End Tab 4 section -->
                                        <div class="row">
                                            <div class="col-md-12 d-flex text-end justify-content-end StepFormButtonsGrp">
                                                <button type="button"
                                                    class="btn btn-secondary d-flex align-items-center justify-content-center me-2 backBtn"
                                                    onclick="MultistepForm.prevStep(4)"><span
                                                        class="material-symbols-outlined pe-2">
                                                        arrow_back_ios_new
                                                    </span> Back</button>
                                                <?php if (Auth::user()->role == 1  or $permission_for_this != 0) { ?>
                                                <button type="submit"
                                                    class="btn btn-primary d-flex align-items-center justify-content-center saveBtn" style="margin-right: 10px;">Update
                                                    Queue-Room</button>
                                                <?php } ?>
                                                <button type="button"
                                                    class="btn btn-secondary d-flex align-items-center justify-content-center me-2 backBtn Saveasdraft"
                                                    onclick="submitForm('draft')">Update as draft</button>
                                            </div>
                                            
                                                
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- SMS Template Modal -->
        <div class="modal fade" id="smsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="smsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
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

        {{-- <script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script> --}}
        <script src="{{ asset('asset/js/external/ckeditor.js') }}" type="text/javascript"></script>


        <script>
            document.addEventListener("DOMContentLoaded", function () {
    // Select the tab buttons
    const queueTab = document.getElementById("Queue-Page-tab");
    const prequeueTab = document.getElementById("Pre-queue-Page-tab");
    const queueTab = document.getElementById("PostqueuePage-tab");
    const inlineTemplate = document.querySelector(".in-line-template");

    // Hide the div initially
    inlineTemplate.style.display = "none";

    function showInlineTemplate(event) {    
        // Show div when Queue or Pre-queue is clicked
        inlineTemplate.style.display = "block";
    }

    function hideInlineTemplate(event) {
        // Hide div when other tabs are clicked
        inlineTemplate.style.display = "none";
    }

    // Add event listeners for clicking on tabs
    queueTab.addEventListener("click", showInlineTemplate);
    prequeueTab.addEventListener("click", showInlineTemplate);

    // Hide div when other tabs are clicked
    document.getElementById("PostqueuePage-tab").addEventListener("click", hideInlineTemplate);
    document.getElementById("PostqueuePage-tab").addEventListener("click", hideInlineTemplate);
    document.getElementById("PriorityAccessPage-tab").addEventListener("click", hideInlineTemplate);
});

        </script>
        <script>
            let calculateVisitorCount = 0;

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
                                if (response.status == 1) {
                                    calculateVisitorCount = response.calculateVisitorCount;

                                    //$('#TotalVisitorid').val(id);
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
            window.onload = function() {
                var startDate = "<?php echo $queueRoom->start_date; ?>";
                var startTime = "<?php echo $queueRoom->start_time; ?>";
                var endDate = "<?php echo $queueRoom->end_date; ?>";
                var endTime = "<?php echo $queueRoom->end_time; ?>";

                document.getElementById("datepicker").value = startDate;
                document.getElementById("timePicker").value = startTime;
                document.getElementById("Enddatepicker").value = endDate;
                document.getElementById("EndtimePicker").value = endTime;
            };
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                var startDate = "<?php echo $queueRoom->start_date; ?>";
                var startTime = "<?php echo $queueRoom->start_time; ?>";
                var endDate = "<?php echo $queueRoom->end_date; ?>";
                var endTime = "<?php echo $queueRoom->end_time; ?>";

                document.getElementById("datepicker").value = startDate;
                document.getElementById("timePicker").value = startTime;
                document.getElementById("Enddatepicker").value = endDate;
                document.getElementById("EndtimePicker").value = endTime;
                // Function to set current date and time
                function setCurrentDateTime() {
                    var currentDate = new Date();
                    var day = ("0" + currentDate.getDate()).slice(-2);
                    var monthNames = ["January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"
                    ];
                    var monthIndex = currentDate.getMonth();
                    var year = currentDate.getFullYear();

                    var formattedDate = day + " " + monthNames[monthIndex] + " " + year;

                    var hours = ("0" + currentDate.getHours()).slice(-2);
                    var minutes = ("0" + currentDate.getMinutes()).slice(-2);
                    var formattedTime = hours + ":" + minutes;

                    // Set current date and time to the datepicker and timePicker fields
                    $("#dateValue").val(formattedDate);
                    $("#timeValue").val(formattedTime);
                }

                // Call the function when checkbox is checked
                $('.queueCheckBox').change(function() {
                    if ($('#Startnow').is(':checked')) {
                        setCurrentDateTime();
                    }
                });

                // Call the function on DOM load if the checkbox is initially checked
                if ($('#Startnow').is(':checked')) {
                    setCurrentDateTime();
                }
            });
        </script>
<script>
    let formCount = 0; // Initialize formCount

    function submitForm(action) {
        var form = document.getElementById('myForm'); // Ensure this is the correct form ID

        if (action === 'draft') {
            // Create a hidden input field for saveasdraft
            var saveAsDraftInput = document.createElement('input');
            saveAsDraftInput.type = 'hidden';
            saveAsDraftInput.name = 'saveasdraft';
            saveAsDraftInput.value = '1';

            // Append the input to the form
            form.appendChild(saveAsDraftInput);
        }

        console.log(formCount, 'formCountformCount');
        if (formCount == 0) {
            form.submit(); // Submit the form
            formCount++;
        }
    }
</script>
        <script type="text/javascript">
            $(document).ready(function() {
                // Function to set current date and time
                function setCurrentDateTimeend() {
                    var currentDate = new Date();
                    var day = ("0" + currentDate.getDate()).slice(-2);
                    var monthNames = ["January", "February", "March", "April", "May", "June",
                        "July", "August", "September", "October", "November", "December"
                    ];
                    var monthIndex = currentDate.getMonth();
                    var year = currentDate.getFullYear();

                    var formattedDate = day + " " + monthNames[monthIndex] + " " + year;

                    var hours = ("0" + currentDate.getHours()).slice(-2);
                    var minutes = ("0" + currentDate.getMinutes()).slice(-2);
                    var formattedTime = hours + ":" + minutes;

                    // Set current date and time to the hidden fields
                    $("#endDate").val(formattedDate);
                    $("#endedTime").val(formattedTime);
                }

                // Call the function when checkbox is checked
                $('.queueCheckBox').change(function() {
                    if ($('#QueuingEnds').is(':checked')) {
                        setCurrentDateTimeend();
                    }
                });

                // Call the function on DOM load if the checkbox is initially checked
                if ($('#QueuingEnds').is(':checked')) {
                    setCurrentDateTimeend();
                }
            });
        </script>


        <script>
            document.addEventListener("DOMContentLoaded", function() {

                function convertDateTime() {
                    var selectedTimezone = document.getElementById("timezone").value.split("|")[
                        0]; // Extract the timezone offset
                    var customStartDate = document.getElementById("datepicker").value;
                    var customStartTime = document.getElementById("timePicker").value;

                    var endCustomDate = document.getElementById("Enddatepicker").value;
                    var endCustomTime = document.getElementById("EndtimePicker").value;

                    // Check which radio button is selected for start time
                    var startNowChecked = document.getElementById("Startnow").checked;
                    var customStartChecked = document.getElementById("CustomDateTime").checked;

                    // Check which radio button is selected for end time
                    var endNowChecked = document.getElementById("QueuingEnds").checked;
                    var customEndChecked = document.getElementById("Customdateandtime").checked;
                    var noEndTimeChecked = document.getElementById("HaveEndDateTime").checked;

                    if (startNowChecked) {
                        var currentDate = new Date();
                        document.getElementById("dateValue").value = currentDate.toISOString().split('T')[
                            0]; // Set current date
                        document.getElementById("timeValue").value = currentDate.toTimeString().split(' ')[
                            0]; // Set current time
                    } else if (customStartChecked) {
                        // Split customStartDate into day, month, and year
                        var dateParts = customStartDate.split(" ");
                        var day = parseInt(dateParts[0]);
                        var month = dateParts[1];
                        var year = parseInt(dateParts[2]);

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
                        var monthNumber = monthMap[month];

                        var formattedDate = year + "-" + monthNumber.toString().padStart(2, '0') + "-" + day.toString()
                            .padStart(2, '0');
                        var datetimeString = formattedDate + "T" + customStartTime;

                        var convertedDateTime = new Date(datetimeString);
                        if (isNaN(convertedDateTime.getTime())) {
                            return;
                        }

                        var options = {
                            timeZone: selectedTimezone
                        };
                        var formattedDateTime = convertedDateTime.toLocaleString('en-US', options);
                        var epochValueStart = new Date(formattedDateTime).getTime() / 1000;

                        document.getElementById("convertedDateTime").value = formattedDateTime;
                        document.getElementById("startepochTime").value = epochValueStart;
                    }

                    if (endNowChecked) {
                        var currentDate = new Date();
                        document.getElementById("endDate").value = currentDate.toISOString().split('T')[
                            0]; // Set current date
                        document.getElementById("endedTime").value = currentDate.toTimeString().split(' ')[
                            0]; // Set current time
                    } else if (customEndChecked) {
                        var datePartsend = endCustomDate.split(" ");
                        var dayend = parseInt(datePartsend[0]);
                        var monthend = datePartsend[1];
                        var yearend = parseInt(datePartsend[2]);

                        var monthNumberend = monthMap[monthend];

                        var formattedDateend = yearend + "-" + monthNumberend.toString().padStart(2, '0') + "-" + dayend
                            .toString().padStart(2, '0');
                        var datetimeStringend = formattedDateend + "T" + endCustomTime;

                        var convertedDateTimeend = new Date(datetimeStringend);
                        if (isNaN(convertedDateTimeend.getTime())) {
                            return;
                        }

                        var optionsend = {
                            timeZone: selectedTimezone
                        };
                        var formattedDateTimeend = convertedDateTimeend.toLocaleString('en-US', optionsend);
                        var epochValueEnd = new Date(formattedDateTimeend).getTime() / 1000;

                        document.getElementById("convertedEndDateTime").value = formattedDateTimeend;
                        document.getElementById("epochEndTime").value = epochValueEnd;
                    } else if (noEndTimeChecked) {
                        document.getElementById("convertedEndDateTime").value = '';
                        document.getElementById("epochEndTime").value = '';
                    }
                }

                document.getElementById("timezone").addEventListener("change", convertDateTime);
                document.getElementById("datepicker").addEventListener("change", convertDateTime);
                document.getElementById("timePicker").addEventListener("change", convertDateTime);
                document.getElementById("Enddatepicker").addEventListener("change", convertDateTime);
                document.getElementById("EndtimePicker").addEventListener("change", convertDateTime);
                document.getElementById("Startnow").addEventListener("change", convertDateTime);
                document.getElementById("CustomDateTime").addEventListener("change", convertDateTime);
                document.getElementById("QueuingEnds").addEventListener("change", convertDateTime);
                document.getElementById("Customdateandtime").addEventListener("change", convertDateTime);
                document.getElementById("HaveEndDateTime").addEventListener("change", convertDateTime);

            });
        </script>


        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Get radio buttons
                const startNowRadio = document.getElementById("Startnow");
                const customDateTimeRadio = document.getElementById("CustomDateTime");

                // Get CustomDateTimeId div and time picker input
                const customDateTimeDiv = document.getElementById("CustomDateTimeId");
                const timePicker = document.getElementById("timePicker");

                // Function to toggle visibility of CustomDateTimeId div and enable/disable time picker
                function toggleCustomDateTimeDiv() {
                    if (customDateTimeRadio.checked) {
                        customDateTimeDiv.style.display = "block";
                        timePicker.disabled = false; // Enable the time picker
                    } else {
                        customDateTimeDiv.style.display = "none";
                        timePicker.disabled = true; // Disable the time picker
                    }
                }

                // Initial toggle based on checked radio
                toggleCustomDateTimeDiv();

                // Listen for changes on radio buttons
                startNowRadio.addEventListener("change", toggleCustomDateTimeDiv);
                customDateTimeRadio.addEventListener("change", toggleCustomDateTimeDiv);
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Get radio buttons
                const endNowRadio = document.getElementById("QueuingEnds");
                const customEndDateTimeRadio = document.getElementById("Customdateandtime");

                const customEndDateTimeDiv = document.getElementById("EndCustomDateTimeId");

                function toggleEndCustomDateTimeDiv() {
                    if (customEndDateTimeRadio.checked) {
                        customEndDateTimeDiv.style.display = "block";
                    } else {
                        customEndDateTimeDiv.style.display = "none";
                    }
                }

                // Initial toggle based on checked radio
                toggleEndCustomDateTimeDiv();

                // Listen for changes on radio buttons
                endNowRadio.addEventListener("change", toggleEndCustomDateTimeDiv);
                customEndDateTimeRadio.addEventListener("change", toggleEndCustomDateTimeDiv);
            });
        </script>

        <script>
            $(document).ready(function() {
                function checkSelectedValue() {
                    var selectedValue = $('select[name="SMSCreateTemplate"]').val();
                    if (selectedValue == '0') {
                        $('.smsTemp').find('input[name="SMSTemplate"]').prop('readonly', false).val('');
                    } else {

                        $.ajax({
                            url: '<?php echo env('APP_URL') . 'get-sms-data/'; ?>' + selectedValue,
                            type: 'GET',
                            success: function(response) {
                                var data = JSON.parse(response);
                                $('.smsTemp').find('input[name="SMSTemplate"]').val(data.sms_template_name)
                                    .prop('readonly', true);
                            }
                        });
                    }
                }

                checkSelectedValue();

                $('select[name="SMSCreateTemplate"]').change(function() {
                    checkSelectedValue();
                });
            });
        </script>

        <script>
            $(document).ready(function() {
                function checkSelectedValue() {
                    var selectedValue = $('select[name="EmailCreateTemplate"]').val();
                    if (selectedValue == '0') {
                        $('.emailTemp').find('input[name="EmailTemplate"]').prop('readonly', false).val('');
                    } else {

                        $.ajax({
                            url: '<?php echo env('APP_URL') . 'get-email-data/'; ?>' + selectedValue,
                            type: 'GET',
                            success: function(response) {
                                var data = JSON.parse(response);
                                $('.emailTemp').find('input[name="EmailTemplate"]').val(data
                                    .email_template_name).prop('readonly', true);
                            }
                        });
                    }
                }

                checkSelectedValue();

                $('select[name="EmailCreateTemplate"]').change(function() {
                    // Call the function whenever the dropdown value changes
                    checkSelectedValue();
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                function checkSelectedValue() {
                    var selectedValue = $('select[name="QueueRoomDesign_id"]').val();
                    if (selectedValue == '0') {
                        $('.design-temp').find('input[name="QueueRoomDesignTemplate_name"]').val('').prop('readonly',
                            false);
                        $('#mainSelectDesignTemp').show().prop('disabled', false);
                        $('#hideshowlanguege').hide();
                    } else {
                        $.ajax({
                            url: '<?php echo env('APP_URL') . 'get-design-temp-data/'; ?>' + selectedValue,
                            type: 'GET',
                            success: function(response) {
                                var data = JSON.parse(response);

                                $('.design-temp').find('input[name="QueueRoomDesignTemplate_name"]').val(
                                    data.template_name).prop('readonly', true);

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
                                    languageHtml += '<div class="d-flex align-items-center">';
                                    languageHtml += '</div>';
                                    languageHtml += '</div>';
                                    languageHtml += '</div>';

                                    $('#hideshowlanguege').append(languageHtml);
                                });
                            }
                        });

                    }
                }

                // Call the function on page load
                checkSelectedValue();

                // Handle change event of the dropdown
                $('select[name="QueueRoomDesign_id"]').change(function() {
                    // Call the function whenever the dropdown value changes
                    checkSelectedValue();
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                // Function to check the selected value and show/hide .bypassmanage
                function checkSelectedValue() {
                    var selectedValue = $('#SelectTemplate').val();
                    if (selectedValue == '0') {
                        $('.bypassmanage').find('input[name="byPassSelectTemplate_name"]').val('').prop('readonly',
                            false);
                        $('.bypassmanage').find('input[name="Bypassurl"]').val('').prop('readonly', false);
                    } else {
                        $.ajax({
                            url: '<?php echo env('APP_URL') . 'get-bypass-data/'; ?>' + selectedValue,
                            type: 'GET',
                            success: function(response) {
                                var data = JSON.parse(response);
                                $('.bypassmanage').find('input[name="byPassSelectTemplate_name"]').val(data
                                    .template_name).prop('readonly', true);
                                $('.bypassmanage').find('input[name="Bypassurl"]').val(data.bypass_url)
                                    .prop('readonly', true);
                            }
                        });
                    }
                }

                // Call the function on page load
                checkSelectedValue();

                // Handle change event of the dropdown
                $('#SelectTemplate').change(function() {
                    // Call the function whenever the dropdown value changes
                    checkSelectedValue();
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                // Function to check the selected value and show/hide .custom-field1
                function checkSelectedValue() {
                    var selectedValue = $('select[name="template_id_data"]').val();
                    if (selectedValue == '0') {
                        $('.custom-field1 input').val('').prop('readonly', false);
                        $('.custom-field1 input[type="radio"]').prop('disabled', false);
                        $('#ProtectRedioURL').val(1);
                        $('#ProtectRedioSite').val(2);
                        $('#AdvanceSettingCheckBox').prop('disabled', false);
                        $('#AdvanceSettingTable').addClass('hide-element');
                        $('#AdvanceSettingTable2').removeClass('hide-element');
                    } else {
                        $('.custom-field1 input').prop('readonly', true);

                        $.ajax({
                            url: '<?php echo env('APP_URL') . 'get-template-data/'; ?>' + selectedValue,
                            type: 'GET',
                            success: function(response) {
                                var data = JSON.parse(response);
                                console.log(data);
                                $('.custom-field1').find('input[name="template_name"]').val(data
                                    .template_name).prop('readonly', true);
                                $('.custom-field1').find('input[name="input_url"]').val(data.input_url)
                                    .prop('readonly', true);
                                $('.custom-field1 input[type="radio"]').prop('disabled', true);
                                if (data.protection_level == '1') {
                                    $('#ProtectRedioURL').prop('checked', true);
                                } else if (data.protection_level == '2') {
                                    $('#ProtectRedioSite').prop('checked', true);
                                }
                                $('#AdvanceSettingCheckBox').prop('disabled', true);
                                if (data.is_advance_setting == '1') {
                                    $('#AdvanceSettingCheckBox').prop('checked', true);
                                    $('#AdvanceSettingTable').removeClass('hide-element');
                                    $('#AdvanceSettingTable2').addClass('hide-element');

                                    // Parse advance_setting_rules JSON string
                                    var advanceSettingRules = JSON.parse(data.advance_setting_rules);

                                    // Clear previous rows in the table
                                    $('#AdvanceSettingTable tbody').empty();

                                    // Loop through each rule and create table rows
                                    $.each(advanceSettingRules, function(index, rule) {
                                        var rowHtml = '<tr>';
                                        rowHtml += '<td>';
                                        if (rule.operator != null) {
                                            rowHtml +=
                                                '<select class="form-select form-control FormInputBox" disabled aria-label="Default select example">';
                                            rowHtml += '<option value="AND" ' + (rule.operator ==
                                                'AND' ? 'selected' : '') + '>AND</option>';
                                            rowHtml += '<option value="OR" ' + (rule.operator ==
                                                'OR' ? 'selected' : '') + '>OR</option>';
                                            rowHtml += '</select>';
                                        } else {
                                            rowHtml +=
                                                '<input type="hidden" name="advance[operator][]" id="advancedata" value="">';
                                        }
                                        rowHtml += '</td>';
                                        rowHtml += '<td>';
                                        rowHtml +=
                                            '<select class="form-select form-control FormInputBox" disabled aria-label="Default select example">';
                                        rowHtml += '<option value="HOST_NAME" ' + (rule
                                                .condition_place == 'HOST_NAME' ? 'selected' : '') +
                                            '>HOST NAME</option>';
                                        rowHtml += '<option value="PAGE_PATH" ' + (rule
                                                .condition_place == 'PAGE_PATH' ? 'selected' : '') +
                                            '>PAGE PATH</option>';
                                        rowHtml += '<option value="PAGE_URL" ' + (rule
                                                .condition_place == 'PAGE_URL' ? 'selected' : '') +
                                            '>PAGE URL</option>';
                                        rowHtml += '</select>';
                                        rowHtml += '</td>';
                                        rowHtml += '<td>';
                                        rowHtml +=
                                            '<select class="form-select form-control FormInputBox" disabled>';
                                        rowHtml += '<option value="CONTAINS" ' + (rule.condition ==
                                            'CONTAINS' ? 'selected' : '') + '>CONTAINS</option>';
                                        rowHtml += '<option value="DOES_NOT_CONTAIN" ' + (rule
                                            .condition == 'DOES_NOT_CONTAIN' ? 'selected' : ''
                                        ) + '>DOES NOT CONTAIN</option>';
                                        rowHtml += '<option value="EQUALS" ' + (rule.condition ==
                                            'EQUALS' ? 'selected' : '') + '>EQUALS</option>';
                                        rowHtml += '<option value="DOES_NOT_EQUAL" ' + (rule
                                                .condition == 'DOES_NOT_EQUAL' ? 'selected' : '') +
                                            '>DOES NOT EQUAL</option>';
                                        rowHtml += '</select>';
                                        rowHtml += '</td>';
                                        rowHtml += '<td>';
                                        rowHtml +=
                                            '<input type="text" class="form-control FormInputBox" id="roomname" readonly placeholder="registration" value="' +
                                            rule.value + '">';
                                        rowHtml += '</td>';
                                        rowHtml += '<td>';
                                        rowHtml +=
                                            '<button class="DeleteTableRow" type="button" readonly>';
                                        rowHtml +=
                                            '<span class="material-symbols-outlined">delete</span>';
                                        rowHtml += '</button>';
                                        rowHtml += '</td>';
                                        rowHtml += '</tr>';

                                        $('#AdvanceSettingTable tbody').append(rowHtml);
                                    });
                                } else {
                                    $('#AdvanceSettingCheckBox').prop('checked', false);
                                    $('#AdvanceSettingTable').addClass('hide-element');
                                    $('#AdvanceSettingTable2').addClass('hide-element');
                                }
                            }
                        });
                    }
                }

                // Call the function on page load
                checkSelectedValue();

                // Handle change event of the dropdown
                $('select[name="template_id_data"]').change(function() {
                    // Call the function whenever the dropdown value changes
                    checkSelectedValue();
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
                var bypassFields = document.getElementById('bypassFields');

                // Function to update display based on selected value
                function updateDisplay() {
                    if (this.value === '1') {
                        bypassFields.style.display = '';
                    } else {
                        bypassFields.style.display = 'none';
                    }
                }

                // Initial check on page load
                bypassRadio.forEach(function(radio) {
                    if (radio.checked) {
                        updateDisplay.call(radio);
                    }
                });

                // Event listener for change event
                bypassRadio.forEach(function(radio) {
                    radio.addEventListener('change', updateDisplay);
                });
            });
        </script>
        <script>
            $(document).ready(function() {
                // Initially hide or show the fields based on the selected radio button
                if ($('#PreSetupBypass').is(':checked')) {
                    $('#preQueueField').show();
                    $('#preQueueTimeField').show();
                } else {
                    $('#preQueueField').hide();
                    $('#preQueueTimeField').hide();
                }

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
            // Get the toggle button element
            var toggleButton = document.getElementById('toggleButton');

            // Get the div containing the buttons to be toggled
            var toggleButtonsDiv = document.getElementById('toggleButtons');

            // Add event listener to the toggle button
            toggleButton.addEventListener('change', function() {
                if (toggleButton.checked) {
                    // Show the buttons if toggle is checked
                    toggleButtonsDiv.style.display = 'block';
                } else {
                    // Hide the buttons if toggle is unchecked
                    toggleButtonsDiv.style.display = 'none';
                }
            });
        </script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const addButton = document.getElementById('addButton');
                const table = document.querySelector('#AdvanceSettingTable2 tbody');
                let rowCount = 0; // Counter for rows

                // Function to update input data
                function updateInputData() {
                    const rowsData = Array.from(table.querySelectorAll('tr')).map(row => {
                        const cells = row.querySelectorAll('td select, td input');
                        return Array.from(cells).map(cell => cell.value);
                    });
                    document.getElementById('advancedata').value = JSON.stringify(rowsData);
                }

                // Function to insert new row
                function insertNewRow() {
                    rowCount++; // Increment row counter
                    const newRow = document.createElement('tr');

                    // Generate unique ID for the new row
                    const rowId = `row_${rowCount}`;
                    newRow.id = rowId;

                    // Create columns for the new row
                    const newCell1 = document.createElement('td');
                    const newCell2 = document.createElement('td');
                    const newCell3 = document.createElement('td');
                    const newCell4 = document.createElement('td');
                    const newCell5 = document.createElement('td'); // Cell for delete button
                    const newCell6 = document.createElement('td'); // Cell for delete button

                    // Select Fields
                    const select1 = document.createElement('select');
                    select1.classList.add('form-select', 'form-control', 'FormInputBox');
                    select1.innerHTML = '<option value="AND">AND</option><option value="OR">OR</option>';
                    select1.setAttribute('name', 'advancedata[operator][]');
                    newCell1.appendChild(select1);

                    const select2 = document.createElement('select');
                    select2.classList.add('form-select', 'form-control', 'FormInputBox');
                    select2.innerHTML =
                        '<option value="HOST_NAME">HOST NAME</option><option value="PAGE_PATH">PAGE PATH</option><option value="PAGE_URL">PAGE URL</option>';
                    select2.setAttribute('name', 'advancedata[condition_place][]');
                    newCell2.appendChild(select2);

                    const select3 = document.createElement('select');
                    select3.classList.add('form-select', 'form-control', 'FormInputBox');
                    select3.innerHTML =
                        '<option value="CONTAINS">CONTAINS</option><option value="DOES_NOT_CONTAIN">DOES NOT CONTAIN</option><option value="EQUALS">EQUALS</option><option value="DOES_NOT_EQUAL">DOES NOT EQUAL</option>';
                    select3.setAttribute('name', 'advancedata[condition][]');
                    newCell3.appendChild(select3);

                    // Delete button
                    const deleteButton = document.createElement('button');
                    deleteButton.innerHTML = '<span class="material-symbols-outlined">delete</span>';
                    deleteButton.classList.add('DeleteTableRow');
                    deleteButton.addEventListener('click', () => {
                        table.removeChild(newRow); // Remove the row when delete button is clicked
                        updateInputData(); // Update advancedata input field after deletion
                        showPlusButton();
                    });
                    newCell5.appendChild(deleteButton);

                    // Input Field
                    const newInput4 = document.createElement('input');
                    newInput4.type = 'text';
                    newInput4.classList.add('form-control', 'FormInputBox');
                    newInput4.setAttribute('name', 'advancedata[value][]');
                    newCell4.appendChild(newInput4);

                    const plusButton = document.createElement('button');
                    plusButton.innerHTML = '<span class="material-symbols-outlined">add</span>';
                    plusButton.classList.add('AddTableRow');
                    plusButton.type = 'button'; // Specify type as button
                    plusButton.addEventListener('click', insertNewRow);
                    newCell6.appendChild(plusButton);

                    // Append columns to the row
                    newRow.appendChild(newCell1);
                    newRow.appendChild(newCell2);
                    newRow.appendChild(newCell3);
                    newRow.appendChild(newCell4);
                    newRow.appendChild(newCell5);
                    newRow.appendChild(newCell6);
                    // Append delete button column

                    // Append row to the table
                    table.appendChild(newRow);

                    // Update advancedata input field after insertion
                    updateInputData();
                    showPlusButton();
                }

                // Function to show plus button
                function showPlusButton() {
                    const allPlusButtons = table.querySelectorAll('.AddTableRow');
                    allPlusButtons.forEach(button => {
                        button.style.display = 'none';
                    });
                    // Show plus button in the last row
                    const lastRowPlusButtons = table.lastElementChild.querySelector('.AddTableRow');
                    if (lastRowPlusButtons) {
                        lastRowPlusButtons.style.display = 'inline-block';
                    }
                }

                // Event listener for add button
                addButton.addEventListener('click', insertNewRow);
            });
            document.addEventListener("DOMContentLoaded", function() {
                var sameAboveUrlCheckbox = document.getElementById('SameAboveUrl');
                var customUrlCheckbox = document.getElementById('CustomURl');
                var customUrlBox = document.getElementById('CustomUrlBoxId');

                // Function to handle checkbox change
                function handleCheckboxChange() {
                    if (customUrlCheckbox.checked) {
                        customUrlBox.style.display = 'block';
                    } else {
                        customUrlBox.style.display = 'none';
                    }
                }

                // Add event listeners for checkbox change
                customUrlCheckbox.addEventListener('change', handleCheckboxChange);
                sameAboveUrlCheckbox.addEventListener('change', function() {
                    if (this.checked) {
                        customUrlBox.style.display = 'none';
                    }
                });

                // Call handleCheckboxChange function to handle initial state
                handleCheckboxChange();
            });

            /** For show new input box for the enter the time */
            function toggleTimeInputContainer() {
                const selectedValue = document.getElementById('selectSessiontype').value;
                const timeInputContainer = document.getElementById('timeInputContainer');
                const timeInput = document.getElementById('timeInput');

                if (selectedValue === '1') {
                    // Show the input box
                    timeInputContainer.style.display = 'block';
                } else {
                    // Hide the input box
                    timeInputContainer.style.display = 'none';
                }
            }

            // Initial check on page load
            window.addEventListener('DOMContentLoaded', function() {
                toggleTimeInputContainer();
            });
            // Event listener for dropdown changes
            document.getElementById('selectSessiontype').addEventListener('change', toggleTimeInputContainer);
        </script>

        <script src="{{ asset('asset/js/jquery.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('asset/js/editqueue.js') }}" type="text/javascript"></script>
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
        </script>
        <script>
            const startNow = document.getElementById('Startnow');
            const queuingEnds = document.getElementById('QueuingEnds');
            const errorMessage = document.getElementById('errorMessage');

            // Add event listeners to check when either radio button is clicked
            startNow.addEventListener('change', validateSelection);
            queuingEnds.addEventListener('change', validateSelection);

            function validateSelection() {
                if (startNow.checked && queuingEnds.checked) {
                    errorMessage.style.display = 'block'; // Show error message
                    startNow.checked = false; // Uncheck Start now
                    queuingEnds.checked = false; // Uncheck End now
                } else {
                    errorMessage.style.display = 'none'; // Hide error message
                }
            }
        </script>
        <script>
            function updateVisitorLimit() {
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
                updateVisitorLimit();
            });
        </script>

 <script>
    document.addEventListener("DOMContentLoaded", function () {
   const inputField = document.getElementById("statusMessageInput2");
   const cancelButton = document.getElementById("cancelBtn2");
   const confirmButton = document.getElementById("confirmBtn2");
                                   
   inputField.addEventListener("input", function () {
   const hasText = inputField.value.trim().length > 0;
    cancelButton.disabled = !hasText;
   confirmButton.disabled = !hasText;
     });
                                   
    cancelButton.addEventListener("click", function () {
    inputField.value = "";
   cancelButton.disabled = true;
   confirmButton.disabled = true;
       });
                                   
   confirmButton.addEventListener("click", function () {
   alert("Button is working");
      });
       });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
   const inputField = document.getElementById("statusMessageInput");
   const cancelButton = document.getElementById("cancelBtn");
   const confirmButton = document.getElementById("confirmBtn");
                                   
   inputField.addEventListener("input", function () {
   const hasText = inputField.value.trim().length > 0;
    cancelButton.disabled = !hasText;
   confirmButton.disabled = !hasText;
     });
                                   
    cancelButton.addEventListener("click", function () {
    inputField.value = "";
   cancelButton.disabled = true;
   confirmButton.disabled = true;
       });
                                   
   confirmButton.addEventListener("click", function () {
   alert("Button is working");
      });
       });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
   const inputField = document.getElementById("statusMessageInput3");
   const cancelButton = document.getElementById("cancelBtn3");
   const confirmButton = document.getElementById("confirmBtn3");
                                   
   inputField.addEventListener("input", function () {
   const hasText = inputField.value.trim().length > 0;
    cancelButton.disabled = !hasText;
   confirmButton.disabled = !hasText;
     });
                                   
    cancelButton.addEventListener("click", function () {
    inputField.value = "";
   cancelButton.disabled = true;
   confirmButton.disabled = true;
       });
                                   
   confirmButton.addEventListener("click", function () {
   alert("Button is working");
      });
       });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
   const inputField = document.getElementById("statusMessageInput4");
   const cancelButton = document.getElementById("cancelBtn4");
   const confirmButton = document.getElementById("confirmBtn4");
                                   
   inputField.addEventListener("input", function () {
   const hasText = inputField.value.trim().length > 0;
    cancelButton.disabled = !hasText;
   confirmButton.disabled = !hasText;
     });
                                   
    cancelButton.addEventListener("click", function () {
    inputField.value = "";
   cancelButton.disabled = true;
   confirmButton.disabled = true;
       });
                                   
   confirmButton.addEventListener("click", function () {
   alert("Button is working");
      });
       });
</script>
    @endsection
