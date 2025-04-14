@extends('common.layouts')

@section('content')
    @include('common.sidebar')
    @include('common.header')

    <?php
    use App\Models\QueuedbUser;
    use Illuminate\Support\Facades\Auth;
    use App\Models\admin\SubscriptionPlan;
    
    $user_plan_id = auth()->user()->subscription_plan_id;
    $maximumQueueRoom = SubscriptionPlan::where('id', $user_plan_id)->value('maximum_queue_room');
    $total_queueRoom_count = count($queueroomwithpermission);
    ?>

    <!-- Inline Critical CSS -->
    <style>
        body {
            background-color: #f8f9fa;
            /* Match main styles */
            font-family: Arial, sans-serif;
        }

        .QueueMasterheading {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .dot-button {
    width: 12px; /* Adjust size */
    height: 12px;
    background-color: red;
    border: none;
    border-radius: 50%; /* Makes it a circle */
    display: inline-block;
    cursor: pointer;
    padding: 0;
}

    </style>

    <!-- Preload CSS -->
    <link rel="preload" href="{{ asset('asset/css/queueRoom.css') }}" as="style"
        onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('asset/css/queueRoom.css') }}">
    </noscript>

    <main id="main" class="bgmain">
        <section class="SectionPadding">
            <div class="container">
                <div class="row mb-3">
                    <div class="col-xl-12 col-md-12 d-flex queuehomeicon">
                        <nav aria-label="breadcrumb" class="QueueBreadCrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item Homebreadcrumb">
                                    <a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('queue-room-view') }}">Queue Room</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>

                @if (Session::has('success'))
                    <div class="alert alert-success alert-dismissible">
                        {!! Session::get('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php Session::forget('success'); ?>
                @elseif(Session::has('error'))
                    <div class="alert alert-danger alert-dismissible">
                        {!! Session::get('error') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php Session::forget('error'); ?>
                @elseif(Session::has('warning'))
                    <div class="alert alert-warning alert-dismissible">
                        {!! Session::get('warning') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php Session::forget('warning'); ?>
                @endif



                <!-- search -->
                <div class="row d-flex justify-content-between" bis_skin_checked="1">
                    <div class="col-md-3 col-xs-12 col-sm-6 mb-3 d-flex" bis_skin_checked="1">
                        <div class="form-group has-search" bis_skin_checked="1">
                            <span class="fa fa-search form-control-feedback"></span>
                            <input type="text" class="form-control" placeholder="Search" id="queueRoomSearch1">
                        </div>
                        <div class="form-group ms-2">
                            <form method="GET" action="{{ url('queue-room-view') }}">
                                <div class="btn-group w-100">
                                    <button type="button" class="btn bg-white btn- dropdown-toggle" style="width: 135px"
                                        data-bs-toggle="dropdown" aria-expanded="false">

                                        @if (request('status') == 'live')
                                            <i class="fa fa-circle text-success"></i>
                                            Live
                                        @elseif(request('status') == 'ended')
                                            <i class="fa fa-circle text-danger"></i>
                                            Ended
                                        @elseif(request('status') == 'upcoming')
                                            <i class="fa fa-circle text-primary"></i>
                                            Upcoming
                                        @elseif(request('status') == 'draft')
                                            <i class="fa fa-circle text-secondary"></i>
                                            <!-- Case corrected --> 
                                            Draft
                                        @else
                                            <i class="fa fa-circle-o me-2 draft-icon"></i>
                                            All Status
                                        @endif
                                    </button>
                                    <ul class="dropdown-menu w-100">
                                        <li class="@if (request('status') == '') bg-primary @endif">
                                            <a class="dropdown-item"
                                                href="{{ route('queue-room-view', ['status' => '']) }}">
                                                <i class="fa fa-circle-o me-2 draft-icon"></i> All status
                                            </a>
                                        </li>

                                        <li class="@if (request('status') == 'live') bg-primary @endif">
                                            <a class="dropdown-item"
                                                href="{{ route('queue-room-view', ['status' => 'live']) }}"
                                                style="@if (request('status') == 'live') color: white !important; @endif">
                                                <i class="fa fa-circle text-success me-2 draft-icon"></i> Live
                                            </a>
                                        </li>


                                        <li class="@if (request('status') == 'ended') bg-primary @endif">
                                            <a class="dropdown-item"
                                                href="{{ route('queue-room-view', ['status' => 'ended']) }}"
                                                style="@if (request('status') == 'ended') color: white !important; @endif">
                                                <i class="fa fa-circle text-danger me-2 draft-icon"></i> Ended
                                            </a>
                                        </li>
                                        <li class="@if (request('status') == 'upcoming') bg-primary @endif">
                                            <a class="dropdown-item"
                                                href="{{ route('queue-room-view', ['status' => 'upcoming']) }}"
                                                style="@if (request('status') == 'upcoming') color: white !important; @endif">
                                                <i class="fa fa-circle text-primary me-2 draft-icon"></i> Upcoming
                                            </a>
                                        </li>
                                        <li class="@if (request('status') == 'draft') bg-primary @endif">
                                            <a class="dropdown-item"
                                                href="{{ route('queue-room-view', ['status' => 'draft']) }}"
                                                style="@if (request('status') == 'draft') color: white !important; @endif">
                                                <!-- Case corrected -->
                                                <i class="fa fa-circle text-secondary me-2"></i> Draft
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            </form>
                        </div>
                    </div>
                    @if (Auth::user()->role == 1 && $total_queueRoom_count < $maximumQueueRoom)
                        <div class="col-md-4 col-xs-12 col-sm-6  mb-3 text-end" bis_skin_checked="1">
                            <a href="{{ url('create-queue') }}">
                                <button class="btn bsb-btn-2xl subbtnbtn submitbtn w-auto px-4">
                                    <div class="d-flex justify-content-between align-items-center subbtnbtnBox"
                                        bis_skin_checked="1">
                                        <span class="material-symbols-outlined  plusicon pe-3">
                                            add
                                        </span>
                                        <span class="createqueue">Create queue room</span>
                                    </div>
                                </button>
                            </a>
                        </div>
                    @else
                        @if (Auth::user()->role == 1)
                            <div class="alert alert-danger" role="alert">
                                You have exceeded your queue room creation limit as per your subscription plan.
                            </div>
                        @endif
                    @endif

                </div>
                <!-- table -->
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
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12">
                        <div class=" allcard">
                            <div class=" p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0 spacetable align-middle" id="QueueRoomTable1">
                                        <thead class="table bgtable">
                                            <tr>
                                                <th class="border-0 py-0 align-middle">
                                                    <h6 class="queueth p-0 mb-0">Queue Room</h6>
                                                </th>
                                                <th class="border-0 py-0 align-middle">
                                                    <h6 class="queueth p-0 mb-0">Period</h6>
                                                </th>
                                                <th class="border-0 py-0 align-middle">
                                                    <h6 class="queueth p-0 mb-0">Last Modified</h6>
                                                </th>
                                                <th class="border-0 py-0 align-middle">
                                                    <h6 class="queueth p-0 mb-0">Status</h6>
                                                </th>
                                                <th class="border-0 py-0 align-middle">
                                                    <h6 class="queueth p-0 mb-0">Action</h6>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($queueroomwithpermission as $row)
                                                <?php
                                                $user = QueuedbUser::find($row['last_modified_by']);
                                                ?>
                                                <!-- first row -->
                                                <tr>
                                                    <td class="border-0 d-flex align-items-center">
                                                        <?php
                                                        if (isset($row['queue_room_icon']) && !empty($row['queue_room_icon'])) {
                                                            $imageName = $row['queue_room_icon'];
                                                        } else {
                                                            $imageName = 'queue.png';
                                                        }
                                                        ?>
                                                        <img src="{{ asset('images/' . $imageName) }}"
                                                            alt="Image Alt Text" width="50" height="50"
                                                            class="quesingIMG">

                                                        <div>
                                                            <h6 class="mt-2 ps-3 mb-0 textdata">
                                                                <?php
                                                                $originalString = $row['queue_room_name'];
                                                                // Truncate after 18 characters
                                                                if (strlen($originalString) > 18) {
                                                                    $truncatedString = substr($originalString, 0, 18) . '...';
                                                                } else {
                                                                    $truncatedString = $originalString;
                                                                }
                                                                
                                                                // echo $row['queue_room_name'];
                                                                
                                                                ?>
                                                                {{ $truncatedString }}
                                                            </h6>
                                                        </div>
                                                    </td>

                                                    <td class="border-0 ">
                                                        <div class="row align-items-center flex-nowrap">
                                                            <div class="col-md-5 col-sm-5 col-5">
                                                                <h6 class="mt-2 mb-2 textdata datedata">
                                                                    {{ \Carbon\Carbon::parse($row['start_date'])->format('d M Y') }}
                                                                </h6>
                                                                <small class="text-muted pt-2">{{ $row['start_time'] . '  ' . $row['queue_timezone'] }}</small>
                                                            </div>
                                                            <div class="col-md-2 col-sm-2 col-2">
                                                                <span class="material-symbols-outlined text-muted fs-6">remove</span>
                                                            </div>
                                                            <div class="col-md-5 col-sm-5 col-5">
                                                                @if ($row['is_ended'] != 2)
                                                                    <h6 class="mt-2 mb-2 textdata datedata">
                                                                        {{ \Carbon\Carbon::parse($row['end_date'])->format('d M Y') }}
                                                                    </h6>
                                                                    <small class="text-muted">{{ $row['end_time'] . '  ' . $row['queue_timezone'] }}</small>
                                                                @else
                                                                    No end Time
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="border-0 ">
                                                        <div>
                                                            <h6 class="mt-0 ps-0 mb-2 textdata">{{ $user->company_person_name }}</h6>
                                                            <small class="text-muted">{{ \Carbon\Carbon::parse($row['updated_at'])->format('d F Y \a\t h:i A') }}</small>
                                                        </div>
                                                    </td>
                                                    <td class="border-0">
                                                        <div class="d-flex align-items-center">
                                                            <?php
                                                            $status_icon = 'fa fa-circle text-success';
                                                            $status_text = 'Live';
                                                    
                                                            if ($row['is_draft'] == 1) {
                                                                $status_icon = 'fa fa-circle text-secondary';
                                                                $status_text = 'Draft';
                                                            } elseif ($row['is_started'] == 0) {
                                                                $status_icon = 'fa fa-circle text-primary';
                                                                $status_text = 'Upcoming';
                                                            } elseif ($row['is_ended'] == 1) {
                                                                if (\Carbon\Carbon::parse($row['end_date'])->isToday() && \Carbon\Carbon::parse($row['end_time'])->gt(\Carbon\Carbon::now())) {
                                                                    $status_icon = 'fa fa-circle text-success';
                                                                    $status_text = 'Live';
                                                                } else {
                                                                    $status_icon = 'fa fa-circle text-danger';
                                                                    $status_text = 'Ended';
                                                                }
                                                            }
                                                            ?>
                                                            <i class="<?php echo $status_icon; ?> me-2" aria-hidden="true"></i>
                                                            <h6 class="mt-2 me-3 textdata"><?php echo $status_text; ?></h6> <!-- Added "me-3" for spacing -->
                                                            <?php if ($status_text == 'Live' || $status_text == 'No end Time') { ?>
                                                                <button class="dot-button" onclick="endRoom(<?php echo $row['id']; ?>)"></button>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                    
                                                    
                                                    
                                                    

                                                    @if (Auth::user()->role == 1 or $row['permission'] == 2)
                                                        <td class="border-0 ">
                                                            <div class="d-flex align-items-center">
                                                                <a href="{{ url('queue-room-edit/' . $row['id']) }}"
                                                                    class="text-decoration-none editicon">

                                                                    <img src="{{ asset('asset/img/editbtnimg1.png') }}"
                                                                        class="img-responsive" alt=""
                                                                        width="20px" height="20px" />
                                                                </a>
                                                                <a href="{{ url('queue-room-delete/' . $row['id']) }}"
                                                                    class="text-decoration-none editicon ms-2"
                                                                    onclick="return confirmDelete();">
                                                                    <span class="material-symbols-outlined">
                                                                        delete
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td>
                                                            <div class="editicondiv">
                                                                <a href="{{ url('queue-room-edit/' . $row['id']) }}"
                                                                    class="text-decoration-none editicon">
                                                                    <span
                                                                        class="material-symbols-outlined">visibility</span>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach

                                        </tbody>

                                    </table>
                                    <div class="pagination-wrapper">
                                        {{-- {!! $queueroomwithpermission->links() !!} --}}
                                        {{ $queueroomwithpermission->appends(['status' => request()->get('status')])->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>

    <script type="text/javascript" src="{{ asset('asset/js/queueRoom.js') }}"></script>
    
<script>
function endRoom(roomId) {
    $.ajax({
        url: '/end-room/' + roomId,
        type: 'POST',
        data: {
            _token: '<?php echo csrf_token(); ?>'
        },
        success: function(response) {
            console.log(response); // Debugging statement
            if (response.success) {
                location.reload();
            } else {
                alert('Failed to end the room: ' + response.message);
            }
        }
    });
}



</script>
    
    <script>
        function confirmDelete() {
            // Display confirmation popup
            var confirmation = confirm("Do you really want to delete the queue room?");

            // If user confirms, proceed with the delete action
            if (confirmation) {
                return true; // Continue with the href link
            } else {
                return false; // Cancel the href link
            }
        }
    </script>
    <script>
        // table Search
        $(document).ready(function() {
            $("#queueRoomSearch1").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#QueueRoomTable1 tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>

    <!-- Optimize JavaScript -->
    <script defer>
        document.addEventListener("DOMContentLoaded", function() {
            // Automatically close alerts after 3 seconds
            setTimeout(function() {
                document.querySelectorAll('.alert').forEach(alert => {
                    alert.style.transition = "opacity 0.5s";
                    alert.style.opacity = 0;
                    setTimeout(() => alert.remove(), 500);
                });
            }, 3000);

            // Sidebar lazy load
            let sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.style.display = "block";
            }
        });
    </script>
@endsection
