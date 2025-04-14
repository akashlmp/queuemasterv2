@extends('common.layouts')

@section('content')

@include('common.sidebar')
@include('common.header')
<link rel="stylesheet" href="{{ asset('asset/css/statRoom.css') }}">
    <main id="main" class="bgmain">

        <!-- =======  Section ======= -->
        <section class="SectionPadding">
            <div class="container">
                <div class="row mb-3">
                    <div class="col-xl-8 col-md-12 stathomeicon">
                        <div aria-label="breadcrumb ">
                            <!-- <ol class="breadcrumb navbreadcrum">
                                <li class="breadcrumb-item Homebreadcrumb"><a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
                                </li>
                                <li class="breadcrumb-item " aria-current="page"><a href="{{ url('stats-room-view') }}">
                                    <div class="stateText">Stats</div></a>
                                </li>
                                <li class="breadcrumb-item " aria-current="page">
                                    <div class="stateText">Supreme S/S Drop</div>
                                </li>
                            </ol> -->
                            <nav aria-label="breadcrumb" class="QueueBreadCrumb developerBreadCrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item Homebreadcrumb">
                                        <a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('stats-room-view') }}">Stats</a></li>
                                    <li class="breadcrumb-item " aria-current="page"><a href="{{ url('stats-edit') }}">Supreme S/S Drop</a></li>
                                    
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-xs-12 col-sm-6">
                    <div class="nav-item dropdown">
                        <button class="liveroom dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span>Last Hour</span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item" href="#">Action</a>
                            <a class="dropdown-item" href="#">Another action</a>
                            <a class="dropdown-item" href="#">Something else here</a>
                        </div>
                    </div>
                </div>
                <!-- chart -->
                <div class="card card-body cardborder mt-3 mb-3">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
                            <h6 class="textheading">Traffic over time</h6>
                        </div>
                        <!-- checkbox -->
                        <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12 mt-2">
                            <div class="checkboxdata d-flex flex-wrap">
                                <div class="checkbox-item d-flex align-items-center">
                                    <input type="checkbox" id="checkbox1">
                                    <label for="checkbox1">Traffic</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="checkbox2">
                                    <label for="checkbox2">Enter queue room</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="checkbox3">
                                    <label for="checkbox3">URL bypass</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="checkbox4">
                                    <label for="checkbox4">No traffic</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="checkbox5">
                                    <label for="checkbox5">Finished queue</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="checkbox6">
                                    <label for="checkbox6">Visitors</label>
                                </div>
                                <div class="checkbox-item">
                                    <input type="checkbox" id="checkbox7">
                                    <label for="checkbox7">Abandon queue</label>
                                </div>
                            </div>
                        </div>
                        <!-- line chart -->
                        <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
                            <canvas id="chLine"></canvas>
                        </div>
                        <!-- 3 card -->
                    </div>
                </div>
                <!-- end first card -->
                <!-- second card -->
                <div class="row mt-5">
                    <div class="col-xl-3 col-md-3 col-sm-3 col-xs-3 mb-2">
                        <div class="card card-body cardborder">
                        <p class="heading p-0 mb-0">Wait time</p>
                        <h6 class="fs-1">16<span class="fs-4 ms-2">minutes</span></h6>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-3 col-sm-3 col-xs-3 mb-2">
                        <div class="card card-body cardborder">
                        <p class="heading p-0 mb-0">Drop out rate</p>
                        <h6 class="fs-1">15<span class="fs-1">%</span></h6>
                        </div>
                    </div>
                    <div class="col-xl-4 col-md-4 col-sm-3 col-xs-3">
                        <div class="card card-body cardborder">
                        <p class="heading p-0 mb-0">Queue rate</p>
                        <div class="d-flex">
                            <h6 class="fs-1">200<span class="fs-5 ms-2">visitors /minutes</span></h6>
                            <a href="statEdit.html"
                        class="text-decoration-none editicon mt-3 ms-3 ">
                            <span class="material-symbols-outlined">edit_square</span>
                            </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- sanky plot -->
                <div class="card card-body cardborder mt-3 mb-3">
                    <div class="row">
                        <div class="col-xl-12 col-md-12 col-sm-12 col-xs-12">
                            <h6 class="textheading">Traffic distribution</h6>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('asset/js/chart.js') }}"></script>
    <script src="{{ asset('asset/js/statchart.js') }}"></script>
@endsection