@extends('common.layouts')

@section('content')

@include('common.sidebar')
@include('common.header')
<link rel="stylesheet" href="{{ asset('asset/css/temp-manage.css') }}">
<main id="main" class="bgmain">
    <!-- ======= About Section ======= -->
    <section class="SectionPadding">
        <div class="bgmain">
            <div class="container">
                <div class="row mb-3">
                    <div class="col-xl-8 col-md-12 d-flex temphomeicon">
                        <nav aria-label="breadcrumb" class="QueueBreadCrumb TempManageQueueRoom">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item Homebreadcrumb">
                                    <a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('temp_manage') }}">Template Management</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
                <!-- card -->
                <div class="container">
                    <div class="row mb-3">
                    <div class="col-xl-12 col-md-12">
                        <div class="card allcard">
                        <div class="card-content">
                            <div class="card-body tempCard">
                            <div class="align-self-center d-flex">
                                <span class="material-symbols-outlined tempicon">
                                    draw
                                    </span>
                                <h4 class="ms-2 temptext">Queue Design</h4>
                            </div>
                            <div class="media-body">
                                <p>Real-Time insights with comprehensive analytics showcaing your queue's performance.</p>
                            </div>
                            <div class="align-self-center">
                            <a href="{{ url('temp-queue-design') }}">
                            
                                <button class="btn btn-primary tempbutton">Go</button></a>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="row mb-3">
                    <div class="col-xl-12 col-md-12">
                        <div class="card allcard">
                        <div class="card-content">
                            <div class="card-body tempCard">
                            <div class="align-self-center d-flex">
                                <span class="material-symbols-outlined tempicon">
                                    rule
                                    </span>
                                    
                                <h4 class="ms-2 temptext">In / Out Rules</h4>
                            </div>
                            <div class="media-body">
                                <p>Setup and customize queueing rooms to match your specific needs.</p>
                            </div>
                            <div class="align-self-center">
                            <a href="{{ url('in-out-rules') }}">
                                <button class="btn btn-primary tempbutton">Go</button></a>
                            </div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>
                    <div class="row mb-3">
                    <div class="col-xl-12 col-md-12">
                        <div class="card allcard">
                        <div class="card-content">
                            <div class="card-body tempCard">
                            <div class="align-self-center d-flex">
                                <span class="material-symbols-outlined tempicon">
                                    mail
                                    </span>
                                <h4 class="ms-2 temptext">SMS / Email Notice</h4>
                            </div>
                            <div class="media-body">
                                <p>Manage design template in / out rules and notitfications.</p>
                            </div>
                            <div class="align-self-center">
                            <a href="{{ url('email-notice') }}">
                                <button class="btn btn-primary tempbutton">Go</button></a>
                            </div>
                            
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

@endsection