@extends('common.layouts')

@section('content')

@extends('common.sidebar')
@extends('common.header')
<link rel="stylesheet" type="text/css" href="{{ asset('asset/css/queueRoom.css')}}">
<main id="main" class="bgmain">
  <section class="SectionPadding">
    <!-- =======  Section ======= -->
        <div class="container">
            <div class="row mb-3">
                <div class="col-xl-8 col-md-12 d-flex queuehomeicon">
                <nav aria-label="breadcrumb" class="QueueBreadCrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item Homebreadcrumb"><a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
              </li>
              <li class="breadcrumb-item"><a href="{{ url('queue-room-view') }}">Queue Room</a></li>
            </ol>
          </nav>

                 
                </div>
            </div>

            <!-- search -->
            <div class="row d-flex justify-content-between">
                <div class="col-md-3 col-xs-12 col-sm-6 mb-3">
                    <div class="form-group has-search">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" class="form-control" placeholder="Search">
                    </div>
                </div>

                <div class="col-md-4 col-xs-12 col-sm-6  mb-3 d-flex justify-content-end">

                      <a href="{{ url('create-queue') }}">  <button class="btn bsb-btn-2xl subbtnbtn submitbtn w-auto px-4" type="submit">
                            <div class="d-flex justify-content-between">
                            <span class="material-symbols-outlined  plusicon pe-3">
                                add
                                </span>
                            <h6 class="createqueue">Create queue room</h6>
                                </div>
                        </button></a>

                </div>
             </div>

             <!-- table -->
             <div class="row">
                <div class="col-xl-12 col-md-12 col-sm-12">
                    <div class="card allcard">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                    <table class="table mb-0 spacetable">
                        <thead class="table bgtable">
                       <tr>
                        <th class="border-0 py-0 align-middle"><h6 class="queueth">Period</h6></th>
                        <th class="border-0 py-0 align-middle"><h6 class="queueth">Queue Room</h6></th>
                        <th class="border-0 py-0 align-middle"><h6 class="queueth">Last Modified</h6></th>
                        <th class="border-0 py-0 align-middle"><h6 class="queueth">Status</h6></th>
                       </tr>
                        </thead>
                        <tbody>
                          @foreach ($queueRoomSetups as $row )


                            <!-- first row -->
                          <tr>
                            <td class="border-0 ">
                                <div class="row align-items-center">
                                <div class="col-md-5 col-sm-5 col-5">
                                    <h6 class="mt-2 ps-3 mb-0 textdata datedata">{{ $row->queue_start}}</h6>
                                   <small class="ps-3 text-muted">{{ $row->queue_timezone}}</small>
                                </div>

                                <div class="col-md-2 col-sm-2 col-2">
                                    <span class="material-symbols-outlined text-muted fs-6">
                                        remove
                                        </span>
                                </div>

                                <div class="col-md-5 col-sm-5 col-5">
                                    <h6 class="mt-2 ps-3 mb-0 textdata datedata"></h6>
                                   <small class="ps-3 text-muted">
                                    @if($row->queue_endtype !=3) {{$row->queue_end}}

                                   @else
                                    {No end Time}
                                   @endif</small>
                                </div>
                                </div>
                                </td>

                            <td class="border-0 d-flex">
                                <img src="./assest/hello.png" alt="Image Alt Text" width="50" height="50" >
                               <div>
                                <h6 class="mt-2 ps-3 mb-0 textdata"> {{$row->queue_room_name}}</h6>
                               <small class="ps-3 text-muted">No end time</small>
                            </div>
                            </td>

                        <td class="border-0 ">
                            <div>
                                {{-- <h6 class="mt-2 ps-3 mb-0 textdata"> Peter</h6> --}}
                               <small class="ps-3 text-muted">
                                @if($row->updated_at == NULL)
                                {{$row->created_at}}
                                @else
                                {{$row->created_at}}
                                @endif
                            </small>
                            </div>
                            </td>

                            <td class="border-0 ">
                                <div class="d-flex align-items-center">
                                <i class="fa fa-circle me-2 text-danger" aria-hidden="true"></i>
                                <h6 class="mt-2 ps-3 textdata">Live</h6>
                                </div>
                            </td>
                          </tr>
                          @endforeach

                        </tbody>
                    </table>
                </div>
                    </div>
                </div>
            </div>
            </div>
         </div>
</section>
</main>

<script type="text/javascript" src="{{ asset('asset/js/queueRoom.js')}}"></script>

@endsection
