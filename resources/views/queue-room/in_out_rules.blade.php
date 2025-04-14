@extends('common.layouts')

@section('content')

@include('common.sidebar')
@include('common.header')
<?php

use App\Models\QueuedbUser;
use App\Models\QueueRoomTemplate;

$queueRooms = $queueRoomtemps;

$groupedQueueRooms = [];
foreach ($queueRooms as $room) {
    $templateId = $room['queue_room_template_id'];
    if (!isset($groupedQueueRooms[$templateId])) {
        // If template id is not yet a key in groupedQueueRooms, create an empty array for it
        $groupedQueueRooms[$templateId] = [];
    }
    // Add the room to the corresponding template id group
    $groupedQueueRooms[$templateId][] = $room;
}
$groupedQueueRooms = array_values($groupedQueueRooms);
// $groupedQueueRooms = $groupedQueueRooms->toArray();
                                        

?>
<link rel="stylesheet" href="{{ asset('asset/css/queueDesign.css') }}">
<main id="main" class="bgmain">
    <!-- ======= About Section ======= -->
    <section class="SectionPadding">
        <div class="bgmain">
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
            <div class="container">
                <div class="row mb-3">
                    <div class="col-xl-8 col-md-12 d-flex queueDesignicon">
                        <nav aria-label="breadcrumb" class="QueueBreadCrumb queueDesignQueueRoom">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item Homebreadcrumb">
                                    <a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('temp-manage') }}">Template Management</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('in-out-rules') }}">In / Out Rules</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- search -->
                <div class="row d-flex justify-content-between">
                    <div class="col-md-3 col-xs-12 col-sm-6 mb-3">
                        <div class="form-group has-search">
                            <span class="fa fa-search form-control-feedback"></span>
                            <input type="text" class="form-control" placeholder="Search" id="inOutRoleSearch">
                        </div>
                    </div>
                </div>
                <!-- table -->
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12">
                        <div class=" allcard">
                            <div class=" p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0 spacetable align-middle" id="inOutRoleTable">
                                        <thead class="table bgtable">
                                            <tr>
                                                <th class="border-0 p-1">
                                                    <h6 class="queueth">In / Out Rules</h6>
                                                </th>
                                                <th class="border-0 p-1">
                                                    <h6 class="queueth">Used by</h6>
                                                </th>
                                                <th class="border-0 p-1">
                                                    <h6 class="queueth">Last Modified</h6>
                                                </th>
                                                {{--<!-- <th class="border-0 p-1">
                                                    <h6 class="queueth">Status</h6>
                                                </th> -->--}}
                                            </tr>
                                        </thead>
                                        <tbody>
    @foreach($groupedQueueRooms as $value)
    <?php
        $user = QueuedbUser::find($value[0]['last_modified_by']);
        $queueRoomTemplates = QueueRoomTemplate::where('id', $value[0]['queue_room_template_id'])->get();
        $queueRoomTemplate = $queueRoomTemplates->toArray();
    ?>
    <tr>
        <td>
            @if(!empty($queueRoomTemplate))
                {{ (strlen($queueRoomTemplate[0]['template_name']) > 18) ? substr($queueRoomTemplate[0]['template_name'], 0, 18) . '...' : $queueRoomTemplate[0]['template_name'] }}
            @endif
        </td>
        <td>
            @foreach($value as $val)
            <?php
                $imageName = isset($val['queue_room_icon']) && !empty($val['queue_room_icon']) ? $val['queue_room_icon'] : 'queue.png';
                $originalString = $val['queue_room_name'];
                $truncatedString = strlen($originalString) > 18 ? substr($originalString, 0, 18) . '...' : $originalString;
            ?>
            <img src="{{ asset('images/'. $imageName )}}" class="me-2 quesingIMG" width="50" height="50">
            {{ $truncatedString }}<br>
            @endforeach
        </td>
        <td class="border-0">
            <div>
                <h6 class="mt-0 mb-0 textdata mb-2">{{ $user->company_person_name }}</h6>
                <small class="text-muted">{{ \Carbon\Carbon::parse($queueRoomTemplates[0]['updated_at'])->format('d F Y \a\t h:i A') }}</small>
            </div>
        </td>
        @if(Auth::user()->role == 1 OR (isset($val['permission']) && $val['permission'] == 2))
        <td class="border-0 ">
            <div class="d-flex align-items-center">
                <a href="{{ url('in-out-rules-edit/'.$queueRoomTemplate[0]['id'] ) }}" class="text-decoration-none editicon">
                    <img src="{{ asset('asset/img/editbtnimg3.png')}}" class="img-responsive" alt="" width="20px" height="20px" />
                </a>
                <a href="#" class="text-decoration-none editicon deleteBtnNew ms-2">
                    <span class="material-symbols-outlined">
                        delete
                    </span>
                </a>
            </div>
        </td>
       {{-- <!-- @else
        <td>
            @for ($i = 0; $i < count($value); $i++)
            <div class="editicondiv">
                <a href="{{ url('in-out-rules-edit/'.$value[$i]['id'] ) }}" class="text-decoration-none editicon">
                    <span class="material-symbols-outlined">visibility</span>
                </a>
            </div>
            @endfor
        </td> -->--}}
        @endif
    </tr>
    @endforeach
</tbody>

                                        
                                    </table>
                                     <!-- Pagination Links -->
                       <div class="pagination-wrapper">
                                    {!! $queueRooms->links() !!}
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<script>
    // table Search
    $(document).ready(function() {
        $("#inOutRoleSearch").on("keyup", function() {
            var value = $(this).val().toLowerCase();

            $("#inOutRoleTable tbody tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
@endsection