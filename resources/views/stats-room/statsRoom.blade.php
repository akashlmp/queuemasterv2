<?php use Illuminate\Support\Facades\DB; ?>

@extends('common.layouts')

@section('content')
    @extends('common.sidebar')
    @extends('common.header')

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Ensure CSS Loads Immediately -->
    <link rel="stylesheet" href="{{ asset('asset/css/statRoom.css') }}">

    <main id="main" class="bgmain">
        <section class="SectionPadding">
            <div class="container">
                <div class="row mb-3">
                    <div class="col-xl-8 col-md-12 d-flex">
                        <nav aria-label="breadcrumb" class="QueueBreadCrumb StatsQueueRoom">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item Homebreadcrumb">
                                    <a href="{{ url('dashboard') }}">
                                        <i class="fa fa-home" aria-hidden="true"></i>
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ url('stats-room-view') }}" class="statebgcolor">Stats</a>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 col-xs-12 col-sm-6 mb-3">
                        <div class="form-group has-search">
                            <span class="fa fa-search form-control-feedback"></span>
                            <input type="text" class="form-control" placeholder="Search" id="statRoomSearch">
                        </div>
                    </div>
                    <!--   <div class="col-md-3 col-xs-12 col-sm-6">
                                                                                                                <div class="nav-item dropdown">
                                                                                                                    <button class="liveroom dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                                                        <div class="d-flex w-100 align-items-center lastclass">
                                                                                                                            <span>Last Hour</span>
                                                                                                                            <span class="material-symbols-outlined">expand_more</span>
                                                                                                                        </div>
                                                                                                                    </button>
                                                                                                                    <div class="dropdown-menu " aria-labelledby="dropdownMenuButton">
                                                                                                                        <a class="dropdown-item list" href="#">Live Queue Room </a>
                                                                                                                        <a class="dropdown-item list" href="#">Ended </a>
                                                                                                                        <a class="dropdown-item list" href="#">Upcoming </a>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div> -->
                    <div class="col-md-3 col-xs-12 col-sm-6">
                        <select class="ChartFilter" id="chartFilter">
                            <option value="1">Live Queue Room</option>
                            <option value="2">Ended</option>
                            <option value="3">Upcoming</option>
                        </select>
                    </div>
                </div>
                <!-- table -->
                {{-- <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="card allcard">
                        <div class=" card-body p-0">
                            <div class="table-responsive">
                                <table class="table mb-0 spacetable" id="statRoomTable">
                                    <thead class="table bgtable">
                                        <tr>
                                            <th class="border-0 p-2">
                                                <h6 class="queueth p-0 mb-0">Queue Room</h6>
                                            </th>
                                            <th class="border-0 p-2">
                                                <h6 class="queueth p-0 mb-0">Waiting</h6>
                                            </th>
                                            <th class="border-0 p-2">
                                                <h6 class="queueth p-0 mb-0">Expected wait time</h6>
                                            </th>
                                            <th class="border-0 p-2">
                                                <h6 class="queueth p-0 mb-0">Queue rate</h6>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="chlajabsdk">
                                        <tr>
                                            @foreach ($queuestatsRooms as $queuestatsRoom)
                                            <?php
                                            $max_traffic_visitor = $queuestatsRoom['max_traffic_visitor'];
                                            $number_query = 'SELECT count(id) as count FROM queuetb_raw_queue_operations where ( room_id = ' . $queuestatsRoom['id'] . ' ) and (status = 3 OR status = 5)';
                                            $number_data = DB::select($number_query);
                                            
                                            if ($number_data) {
                                                $number_in_line = $number_data[0]->count;
                                            } else {
                                                $number_in_line = 0;
                                            }
                                            $expected_wait_time = floor($number_in_line / $max_traffic_visitor);
                                            $number_query = 'SELECT sum(storage_occupied_number) as count FROM queue_serial_number_management where ( room_id = ' . $queuestatsRoom['id'] . ' ) and (cron_status = 0 )';
                                            $total_number = DB::select($number_query);
                                            $total_number = $total_number[0]->count;
                                            if (!$total_number) {
                                                $total_number = 1;
                                            }
                                            
                                            $per = 1 - floor($number_in_line / $total_number);
                                            
                                            ?>

                                            <td>
                                                <?php
                                                $imageURL = 'https://queuing.lambetech.com/public/images/' . $queuestatsRoom['queue_room_icon'];
                                                
                                                // Check if the queue room icon is empty or not
                                                if (!empty($queuestatsRoom['queue_room_icon'])) {
                                                    $imageURL = asset('images/' . $queuestatsRoom['queue_room_icon']);
                                                } else {
                                                    $imageURL = asset('images/queue.png'); // Default image URL
                                                }
                                                ?>
                                                <img src="{{ $imageURL }}" width="50" height="50" class="me-3 quesingIMG">
                                                {{ (strlen($queuestatsRoom['queue_room_name']) > 18) ? substr($queuestatsRoom['queue_room_name'], 0, 18) . '...' : $queuestatsRoom['queue_room_name'] }}
                                            </td>

                                            <td class="border-0">
                                                <h6 class="mt-2  textdata"><?php echo $number_in_line; ?>/ <?php echo $total_number; ?></h6>
                                                <div class="progress queueProgressBar">
                                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $per; ?>%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="border-0 ">
                                                <div class="d-flex align-items-center">
                                                    <i class="fa fa-circle me-2 text-danger" aria-hidden="true"></i>

                                                    <h6 class="mt-2  textdata"><?php echo $expected_wait_time; ?> minutes</h6>
                                                </div>
                                            </td>
                                            <td class="border-0 ">
                                                <div class="d-flex align-items-center">
                                                    <h6 class="mt-2  textdata">{{ $queuestatsRoom['max_traffic_visitor'] }}</h6>
                                                    @if (Auth::user()->role == 1 or $queuestatsRoom['permission'] == 2)
                                                    <a href="{{ url('stats-edit/' . $queuestatsRoom['id']) }}" class="text-decoration-none editicon ms-2">
                                                        <img src="{{ asset('asset/img/editbtnimg2.png')}}" class="img-responsive" alt="" width="20px" height="20px"/>
                                                    </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="pagination-wrapper">
                                    {!! $queuestatsRooms->links() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
                <div class="row">
                    <div class="col-xl-12 col-md-12 col-sm-12">
                        <div class="card allcard">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table mb-0 spacetable" id="statRoomTable">
                                        <thead class="table bgtable">
                                            <tr>
                                                <th class="border-0 p-2">
                                                    <h6 class="queueth p-0 mb-0">Queue Room</h6>
                                                </th>
                                                <th class="border-0 p-2">
                                                    <h6 class="queueth p-0 mb-0">Waiting</h6>
                                                </th>
                                                <th class="border-0 p-2">
                                                    <h6 class="queueth p-0 mb-0">Expected Wait Time</h6>
                                                </th>
                                                <th class="border-0 p-2">
                                                    <h6 class="queueth p-0 mb-0">Queue Rate</h6>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($queuestatsRooms as $queuestatsRoom)
                                                <?php
                                                $max_traffic_visitor = max($queuestatsRoom['max_traffic_visitor'], 1);
                                                
                                                $number_query = "SELECT count(id) as count FROM queuetb_raw_queue_operations
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    WHERE room_id = ? AND (status = 3 OR status = 5)";
                                                $number_data = DB::select($number_query, [$queuestatsRoom['id']]);
                                                $number_in_line = $number_data[0]->count ?? 0;
                                                
                                                $expected_wait_time = floor($number_in_line / $max_traffic_visitor);
                                                
                                                $total_query = "SELECT sum(storage_occupied_number) as count FROM queue_serial_number_management
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    WHERE room_id = ? AND cron_status = 0";
                                                $total_number = DB::select($total_query, [$queuestatsRoom['id']]);
                                                $total_number = max($total_number[0]->count ?? 1, 1);
                                                
                                                $per = max(1 - $number_in_line / $total_number, 0) * 100;
                                                
                                                $imageURL = !empty($queuestatsRoom['queue_room_icon']) ? asset('images/' . $queuestatsRoom['queue_room_icon']) : asset('images/queue.png');
                                                ?>
                                                <tr>
                                                    <td>
                                                        <img src="{{ $imageURL }}" width="50" height="50"
                                                            class="me-3 quesingIMG">
                                                        {{ Str::limit($queuestatsRoom['queue_room_name'], 18, '...') }}
                                                    </td>
                                                    <td class="border-0">
                                                        <h6 class="mt-2 textdata">{{ $number_in_line }} /
                                                            {{ $total_number }}</h6>
                                                        <div class="progress queueProgressBar">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $per }}%"
                                                                aria-valuenow="{{ $per }}" aria-valuemin="0"
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="border-0">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fa fa-circle me-2 text-danger"></i>
                                                            <h6 class="mt-2 textdata">{{ $expected_wait_time }} minutes
                                                            </h6>
                                                        </div>
                                                    </td>
                                                    <td class="border-0">
                                                        <div class="d-flex align-items-center">
                                                            <h6 class="mt-2 textdata">
                                                                {{ $queuestatsRoom['max_traffic_visitor'] }}</h6>
                                                            @if (Auth::user()->role == 1 || $queuestatsRoom['permission'] == 2)
                                                                <a href="{{ url('stats-edit/' . $queuestatsRoom['id']) }}"
                                                                    class="text-decoration-none editicon ms-2">
                                                                    <img src="{{ asset('asset/img/editbtnimg2.png') }}"
                                                                        class="img-responsive" alt="" width="20px"
                                                                        height="20px" />
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <div class="pagination-wrapper">
                                        {!! $queuestatsRooms->links() !!}
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
        //     $(document).ready(function() {
        //     $('#statRoomSearch').on('keyup', function() {
        //         var searchValue = $(this).val().toLowerCase(); // Get the search value and convert it to lowercase
        //         $('#statRoomTable tbody tr').each(function() {
        //             var queueRoomName = $(this).find('td').eq(0).text().toLowerCase(); // Get the queue room name from the first column
        //             if (queueRoomName.indexOf(searchValue) > -1) {
        //                 $(this).show(); // Show the row if it matches the search value
        //             } else {
        //                 $(this).hide(); // Hide the row if it doesn't match the search value
        //             }
        //         });
        //     });
        // });

        $(document).ready(function() {
            $('#statRoomSearch').on('keyup', function() {
                var searchValue = $(this).val();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('stats.filter') }}',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    contentType: 'application/json',
                    data: JSON.stringify({
                        _token: csrfToken,
                        type: '1',
                        search: searchValue,
                        filterValue: null,
                    }),
                    success: function(response) {
                        // $('#statRoomTable tbody').html(response);

                        var tableBody = $('#chlajabsdk');
                        tableBody.empty(); // Clear existing content
                        $.each(response, function(index, queuestatsRoom) {
                            var truncatedName = truncateText(queuestatsRoom
                                .queue_room_name, 18);
                            var newRow = '<tr>' +
                                '<td>' +
                                '<img src="{{ asset('images') }}/' + (queuestatsRoom
                                    .queue_room_icon ? queuestatsRoom.queue_room_icon :
                                    'queue.png') +
                                '" width="50" height="50" class="quesingIMG me-3">' +
                                truncatedName +
                                '</td>' +

                                '<td class="border-0">' +
                                '<h6 class="mt-2 textdata">' + queuestatsRoom
                                .number_in_line + ' / ' + queuestatsRoom.total_number +
                                '</h6>' +
                                '<div class="progress queueProgressBar">' +
                                '<div class="progress-bar" role="progressbar" style="width: ' +
                                queuestatsRoom.per +
                                '%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>' +
                                '</div>' +
                                '</td>' +
                                '<td class="border-0">' +
                                '<div class="d-flex align-items-center">' +
                                '<i class="fa fa-circle me-2 text-danger" aria-hidden="true"></i>' +
                                '<h6 class="mt-2 textdata">' + queuestatsRoom
                                .expected_wait_time + ' minutes</h6>' +
                                '</div>' +
                                '</td>' +
                                '<td class="border-0">' +
                                '<div class="d-flex align-items-center">' +
                                '<h6 class="mt-2 textdata">' + queuestatsRoom
                                .max_traffic_visitor + '</h6>';
                            if (queuestatsRoom.permission == 2) {
                                newRow += '<a href="{{ url('stats-edit') }}/' +
                                    queuestatsRoom.id +
                                    '" class="text-decoration-none editicon">' +
                                    '<span class="material-symbols-outlined ms-2">edit_square</span>' +
                                    '</a>';
                            }
                            newRow += '</div>' +
                                '</td>' +
                                '</tr>';
                            tableBody.append(newRow);
                        });
                    }
                });
            });
        });
    </script>

    <script>
        // Event listener to trigger when dropdown value changes
        document.getElementById('chartFilter').addEventListener('change', function() {
            // Get selected value
            var selectedValue = this.value;

            // Call function to handle selected value
            handleFilterSelection(selectedValue);
        });

        function truncateText(text, maxLength) {
            if (text.length > maxLength) {
                return text.substring(0, maxLength) + '...'; // Yahaan '...' ke bajaay kuch aur bhi likh sakte ho
            } else {
                return text;
            }
        }
        // Function to handle selected filter value
        function handleFilterSelection(selectedValue) {
            console.log(selectedValue);
            var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Fetch CSRF token
            $.ajax({
                type: 'POST',
                url: '{{ route('stats.filter') }}',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                contentType: 'application/json',
                data: JSON.stringify({
                    _token: csrfToken, // Include CSRF token in data
                    filterValue: selectedValue,
                    type: '2'
                }),
                success: function(response) {
                    var tableBody = $('#chlajabsdk');
                    tableBody.empty(); // Clear existing content
                    $.each(response, function(index, queuestatsRoom) {
                        var truncatedName = truncateText(queuestatsRoom.queue_room_name, 18);
                        var newRow = '<tr>' +
                            '<td>' +
                            '<img src="{{ asset('images') }}/' + (queuestatsRoom.queue_room_icon ?
                                queuestatsRoom.queue_room_icon : 'queue.png') +
                            '" width="50" height="50" class="quesingIMG me-3">' + truncatedName +
                            '</td>' +

                            '<td class="border-0">' +
                            '<h6 class="mt-2 textdata">' + queuestatsRoom.number_in_line + ' / ' +
                            queuestatsRoom.total_number + '</h6>' +
                            '<div class="progress queueProgressBar">' +
                            '<div class="progress-bar" role="progressbar" style="width: ' +
                            queuestatsRoom.per +
                            '%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>' +
                            '</div>' +
                            '</td>' +
                            '<td class="border-0">' +
                            '<div class="d-flex align-items-center">' +
                            '<i class="fa fa-circle me-2 text-danger" aria-hidden="true"></i>' +
                            '<h6 class="mt-2 textdata">' + queuestatsRoom.expected_wait_time +
                            ' minutes</h6>' +
                            '</div>' +
                            '</td>' +
                            '<td class="border-0">' +
                            '<div class="d-flex align-items-center">' +
                            '<h6 class="mt-2 textdata">' + queuestatsRoom.max_traffic_visitor + '</h6>';
                        if (queuestatsRoom.permission == 2) {
                            newRow += '<a href="{{ url('stats-edit') }}/' + queuestatsRoom.id +
                                '" class="text-decoration-none editicon">' +
                                '<span class="material-symbols-outlined ms-2">edit_square</span>' +
                                '</a>';
                        }
                        newRow += '</div>' +
                            '</td>' +
                            '</tr>';
                        tableBody.append(newRow);
                    });
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
@endsection
