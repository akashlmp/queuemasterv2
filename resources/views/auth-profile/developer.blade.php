@extends('common.layouts')
@section('content')
@extends('common.sidebar')
@extends('common.header')
<link rel="stylesheet" href="{{ asset('asset/css/developer.css') }}">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<main id="main" class="bgmain">
    <!-- ======= About Section ======= -->
    <section class="SectionPadding">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <nav aria-label="breadcrumb" class="QueueBreadCrumb developerBreadCrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item Homebreadcrumb">
                                <a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('developer') }}">Developer</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
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
            <div class="row d-flex justify-content-between" bis_skin_checked="1">
                <div class="col-md-3 col-xs-12 col-sm-6 mb-3" bis_skin_checked="1">
                    <div class="form-group has-search" bis_skin_checked="1">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" class="form-control" id="developerRoomSearch" placeholder="Search">
                    </div>
                </div>
                <div class="col-md-9 col-xs-12 col-sm-6  mb-3 text-end" bis_skin_checked="1">

                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body statsCard infoCard">
                    <div class="align-items-start d-flex">
                        <span class="material-symbols-outlined QueueMasterIcon staticon">
                            info
                        </span>
                        <div>
                            <h6 class="ms-2 mb-0 QueueMasterheading stattext">Informational message</h6>
                            <p class="mb-0 pt-1 pb-0 ms-2 mt-0">You must insert the JavaScript snippet script into the &lt;head&gt; section of the site that you wish to integrate.</p>
                        </div>
                    </div>
                    <!-- <div class="media-body">
                       
                    </div> -->
                </div>
            </div>
            <!-- table -->
            <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class=" allcard">
                        <div class=" p-0">
                            <div class="table-responsive card">
                                <table class="table mb-0 spacetable align-middle" id="developerRoomTable">
                                    <thead class="table bgtable">
                                        <tr>
                                            <th class="border-0 p-2">
                                                <h6 class="queueth p-0 mb-0">Queue Room</h6>
                                            </th>
                                            <th class="border-0 p-2">
                                                <h6 class="queueth p-0 mb-0">DNS Setting</h6>
                                            </th>
                                            <th class="border-0 p-2">
                                                <h6 class="queueth p-0 mb-0">Session Setting</h6>
                                            </th>
                                            <th class="border-0 p-2">
                                                <h6 class="queueth p-0 mb-0">Javascript snippet</h6>
                                            </th>
                                            <!-- <th class="border-0 p-2">
                                                <h6 class="queueth p-0 mb-0">Action</h6>
                                            </th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($queueRoomtemps as $queueRoom)
                                        <?php
                                        $input_url = $queueRoom['input_url'];


                                        $pattern = '/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';


                                        if (preg_match($pattern, $input_url)) {
                                            if (!preg_match('/^https?:\/\//', $input_url)) {

                                                $input_url =  $input_url;
                                            }
                                        } else {
                                            // URL doesn't match the pattern, try to fix it
                                            // Add http:// to the URL
                                            $fixed_url =  ltrim($input_url, '/');

                                            // Check if the fixed URL matches the pattern
                                            if (preg_match($pattern, $fixed_url)) {
                                                // Fixed URL matches the pattern, update the input_url
                                                $input_url = $fixed_url;
                                            } else {
                                                // Fixed URL still doesn't match the pattern, handle it by making it a valid URL format
                                                $input_url =  preg_replace('/^[:\/]+/', '', $input_url);
                                            }
                                        }

                                        // Update the input_url in the $queueRoom array
                                        $queueRoom['input_url'] = $input_url;
                                        $scriptDataForJs = '';
                                        if (!empty($queueRoom['is_advance_setting']) && $queueRoom['is_advance_setting'] == 1 && !empty($queueRoom['advance_setting_rules'])) {
                                            $advance_setting_rules = json_decode($queueRoom['advance_setting_rules'], true);
                                            if (!empty($advance_setting_rules)) {
                                                $loop = 1;
                                                foreach ($advance_setting_rules as $advance_setting_rule) {
                                                    if (!empty($advance_setting_rule['operator'])) {
                                                        $scriptDataForJs .= ' data-condition-op-' . $loop . '="' . $advance_setting_rule['operator'] . '" ';
                                                    }
                                                    if (!empty($advance_setting_rule['condition_place'])) {
                                                        $scriptDataForJs .= ' data-condition-place-' . $loop . '="' . $advance_setting_rule['condition_place'] . '" ';
                                                    }
                                                    if (!empty($advance_setting_rule['condition'])) {
                                                        $scriptDataForJs .= ' data-condition-' . $loop . '="' . $advance_setting_rule['condition'] . '" ';
                                                    }
                                                    if (!empty($advance_setting_rule['value'])) {
                                                        $scriptDataForJs .= ' data-condition-value-' . $loop . '="' . $advance_setting_rule['value'] . '" ';
                                                    }
                                                    $loop++;
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td width="300px" class="firsttd">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        @php
                                                            $imageSource = !empty($queueRoom['queue_room_icon']) ? asset('images/'. $queueRoom['queue_room_icon']) : url('public/images/queue.png');
                                                        @endphp
                                                        <img src="{{ $imageSource }}" width="50" height="50" alt="{{ $queueRoom['queue_room_name'] }}" class="quesingIMG">
                                                    </div>
                                                    <div class="col-auto">
                                                        {{ (strlen($queueRoom['queue_room_name']) > 14) ? substr($queueRoom['queue_room_name'], 0, 14) . '...' : $queueRoom['queue_room_name'] }}
                                                    </div>

                                                </div>
                                            </td>
                                            <!-- <td class="secondtd"> -->
                                            <td>
                                                <div class="mb-2 custom-field1">
                                                    <label>CNAME:</label>
                                                    <input type='text' class="form-control FormInputBox " readonly value="{{$queueRoom['cname']}}">
                                                </div>

                                                <div class="mb-2 custom-field1">
                                                    @php
                                                        $prequeueUrlParts = !empty($queueRoom['prequeue_html_page_url']) ? explode('//', $queueRoom['prequeue_html_page_url']) : [];
                                                        $queueUrlParts = !empty($queueRoom['queue_html_page_url']) ? explode('//', $queueRoom['queue_html_page_url']) : [];
                                                    @endphp
                                                    @if($queueRoom['is_prequeue'] == 1)
                                                        <label>Template URL:</label>
                                                        <textarea type='text' rows="3" class="form-control" readonly>{{$prequeueUrlParts[1]}}</textarea>
                                                    @else
                                                        <label>Template URL:</label>
                                                        <textarea type='text' rows="3" class="form-control" readonly>{{$queueUrlParts[1]}}</textarea>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>
                                                <div class="DevelopmentTool">
                                                    @if($queueRoom['session_type'] == 1)
                                                        {{-- <textarea class="form-control js-snippet1" rows="7" readonly><script data-intercept-domain="{{ $queueRoom['input_url'] }}" data-intercept="{{ $queueRoom['id'] }}" data-c="{{ $user->pr_user_id }}" data-time="{{$queueRoom['time_input']}}" data-type="1" type="text/javascript" src="{{env('APP_URL')}}asset/js/checkSession.min.js"></script></textarea> --}}
                                                        
                                                        <textarea class="form-control js-snippet1" rows="7" readonly><script data-intercept-domain="{{ $queueRoom['input_url'] }}" data-intercept="{{ base64_encode($queueRoom['id']) }}" data-c="{{ $user->pr_user_id }}" data-time="{{$queueRoom['time_input']}}" data-type="1" type="text/javascript" src="{{env('APP_URL')}}asset/js/checkSession.min.js"></script></textarea>

                                                        <button class="btn btn-primary DevCopyBtn" onclick="copyToClipboard1(this)"><span class="material-symbols-outlined">content_copy</span></button>
                                                    @else
                                                        {{-- <textarea class="form-control js-snippet2" rows="7" readonly><script data-intercept-domain="{{ $queueRoom['input_url'] }}" data-intercept="{{ $queueRoom['id'] }}" data-c="{{ $user->pr_user_id }}" data-type="2" type="text/javascript" src="{{env('APP_URL')}}asset/js/checkSession.min.js"></script></textarea> --}}

                                                        <textarea class="form-control js-snippet2" rows="7" readonly><script data-intercept-domain="{{ $queueRoom['input_url'] }}" data-intercept="{{ base64_encode($queueRoom['id']) }}" data-c="{{ $user->pr_user_id }}" data-type="2" type="text/javascript" src="{{env('APP_URL')}}asset/js/checkSession.min.js"></script></textarea>



                                                        <button class="btn btn-primary DevCopyBtn" onclick="copyToClipboard2(this)"><span class="material-symbols-outlined">content_copy</span></button>
                                                        


                                                    @endif
                                                </div>
                                            </td>

                                            <td>
                                                <div class="DevelopmentTool">
                                                    <textarea class="form-control js-snippet " rows="7" readonly><script data-intercept-domain="{{ $queueRoom['input_url'] }}" data-intercept="{{ base64_encode($queueRoom['id']) }}" data-c="{{ $user->pr_user_id }}" data-call="0" type="text/javascript" src="{{ $script }}" {{ $scriptDataForJs }}></script></textarea>

                                                    {{-- <textarea class="form-control js-snippet " rows="7" readonly><script data-intercept-domain="{{ $queueRoom['input_url'] }}" data-intercept="{{ $queueRoom['id'] }}" data-c="{{ $user->pr_user_id }}" data-call="0" type="text/javascript" src="{{ $script }}" {{ $scriptDataForJs }}></script></textarea> --}}
                                                    <button class="btn btn-primary DevCopyBtn" onclick="copyToClipboard(this)"><span class="material-symbols-outlined">content_copy</span></button>
                                                </div>
                                            </td>
                                            <!-- <td width="250px" class="foruthtd">

                                                <?php //if ($queueRoom['is_ended'] == 1) {
                                                ?>
                                                    <a href="{{ route('markCompleted', $queueRoom['id']) }}" class="btn btn-success">
                                                        completed
                                                    </a>
                                                <?php
                                                //} else {
                                                ?>
                                                    <a href="{{ route('markCompleted', $queueRoom['id']) }}" class="btn btn-danger">
                                                        Mark as completed
                                                    </a>
                                                <?php
                                                //}

                                                ?>
                                            </td> -->
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                 @if($queueRooms instanceof \Illuminate\Pagination\Paginator || $queueRooms instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                <div class="pagination-wrapper">
                                    {!! $queueRooms->links() !!}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
    </section>
</main>
<script>
    function copyToClipboard(button) {
        var row = button.closest('tr');
        var textarea = row.querySelector('.js-snippet');
        textarea.select();
        document.execCommand('copy');
    }
    
    function copyToClipboard1(button) {
        var row = button.closest('tr');
        var textarea = row.querySelector('.js-snippet1');
        textarea.select();
        document.execCommand('copy');
    }
    
    function copyToClipboard2(button) {
        var row = button.closest('tr');
        var textarea = row.querySelector('.js-snippet2');
        textarea.select();
        document.execCommand('copy');
    }
</script>
<script>
    // table Search
    $(document).ready(function() {
        $("#developerRoomSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#developerRoomTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection