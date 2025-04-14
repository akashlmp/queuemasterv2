@extends('common.layouts')
@section('content')
    @include('common.sidebar')
    @include('common.header')
    <?php
    
    use App\Models\QueuedbUser;
    use App\Models\QueuetbDesignTemplate;
    
    // Use the already paginated data
    $queueRooms = $queueRoomtemps;
    $groupedQueueRooms = [];
    foreach ($queueRooms as $room) {
        $templateId = $room['queue_room_design_tempid'];
        if (!isset($groupedQueueRooms[$templateId])) {
            $groupedQueueRooms[$templateId] = [];
        }
        $groupedQueueRooms[$templateId][] = $room;
    }
    ?>
    <link rel="stylesheet" href="{{ asset('asset/css/queueDesign.css') }}">
    <main id="main" class="bgmain">
        <!-- ======= About Section ======= -->
        <section class="SectionPadding">
            <div class="bgmain">
                <div class="container">
                    <div class="row mb-3">
                        <div class="col-xl-8 col-md-12 d-flex queueDesignicon">
                            <nav aria-label="breadcrumb" class="QueueBreadCrumb queueDesignQueueRoom">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item Homebreadcrumb"><a href="{{ url('dashboard') }}"><i
                                                class="fa fa-home" aria-hidden="true"></i></a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('temp-manage') }}">Template Management</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('temp-queue-design') }}">Queue Design</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <!-- search -->
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="row d-flex justify-content-between">
                        <div class="col-md-3 col-xs-12 col-sm-6 mb-3">
                            <div class="form-group has-search">
                                <span class="fa fa-search form-control-feedback"></span>
                                <input type="text" class="form-control" placeholder="Search" id="queueRoomSearch">
                            </div>
                        </div>
                    </div>
                    <!-- table -->
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12">
                            <div class="allcard">
                                <div class="p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0 spacetable align-middle" id="queueRoomTable">
                                            <thead class="table bgtable">
                                                <tr>
                                                    <th class="border-0 p-2">
                                                        <h6 class="queueth p-0 mb-0">Design</h6>
                                                    </th>
                                                    <th class="border-0 p-2">
                                                        <h6 class="queueth p-0 mb-0">Used by</h6>
                                                    </th>
                                                    <th class="border-0 p-2">
                                                        <h6 class="queueth p-0 mb-0">Last Modified</h6>
                                                    </th>
                                                    <th class="border-0 p-2">
                                                        <h6 class="queueth p-0 mb-0">Status</h6>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($groupedQueueRooms as $val)
                                                    <?php
                                                    $user = QueuedbUser::find($val[0]['last_modified_by']);
                                                    
                                                    if ($val[0]['queue_room_design_tempid'] != null) {
                                                        $queueRoomTemplates = QueuetbDesignTemplate::where('id', $val[0]['queue_room_design_tempid'])->get();
                                                        $queueRoomTemplate = $queueRoomTemplates->toArray();
                                                    }
                                                    
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            {{ strlen($queueRoomTemplate[0]['template_name']) > 18 ? substr($queueRoomTemplate[0]['template_name'], 0, 18) . '...' : $queueRoomTemplate[0]['template_name'] }}

                                                        </td>
                                                        <td>
                                                            @foreach ($val as $value)
                                                                @php
                                                                    $imagePath = $value['queue_room_icon']
                                                                        ? asset('images/' . $value['queue_room_icon'])
                                                                        : asset('images/queue.png');
                                                                    $originalString = $value['queue_room_name'];
                                                                    $truncatedString =
                                                                        strlen($originalString) > 18
                                                                            ? substr($originalString, 0, 18) . '...'
                                                                            : $originalString;
                                                                @endphp

                                                                <img src="{{ $imagePath }}" class="me-2 quesingIMG"
                                                                    width="50" height="50">
                                                                {{ $truncatedString }}<br>
                                                            @endforeach
                                                        </td>

                                                        <td class="border-0">
                                                            <div bis_skin_checked="1">
                                                                <h6 class="mt-0 mb-0 textdata mb-2">
                                                                    {{ $user->company_person_name }}</h6>
                                                                <small
                                                                    class="text-muted">{{ \Carbon\Carbon::parse($queueRoomTemplates[0]['updated_at'])->format('d F Y \a\t h:i A') }}</small>
                                                            </div>
                                                        </td>
                                                        @if (Auth::user()->role == 1 or $val[0]['permission'] == 2)
                                                            <td class="border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <a href="{{ url('temp-queue-design-edit/' . $queueRoomTemplate[0]['id'] . '/' . $val[0]['id']) }}"
                                                                        class="text-decoration-none editicon">
                                                                        <img src="{{ asset('asset/img/editbtnimg3.png') }}"
                                                                            class="img-responsive" alt=""
                                                                            width="20px" height="20px" />
                                                                    </a>
                                                                    <a href="{{ url('queue-design-delete/' . $queueRoomTemplate[0]['id'] . '/' . $val[0]['id']) }}"
                                                                        class="ms-2 text-decoration-none deleteBtnNew editicon">
                                                                        <span class="material-symbols-outlined">
                                                                            delete
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        @else
                                                            <td>
                                                                <div class="editicondiv">
                                                                    <a href="{{ url('temp-queue-design-edit/' . $queueRoomTemplate[0]['id'] . '?value=1') }}"
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
                                        <!-- Pagination links -->
                                        <div class="pagination-wrapper">
                                            {!! $queueRoomtemps->links() !!}
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
            $("#queueRoomSearch").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("#queueRoomTable tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>
@endsection
