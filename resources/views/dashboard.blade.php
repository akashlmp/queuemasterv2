@extends('common.layouts')

@section('content')
    @include('common.sidebar')
    @include('common.header')

    <main id="main" class="bgmain">

        <!-- ======= About Section ======= -->
        <section class="SectionPadding">
            <div>
                <div class="container">
                    <div class="row">
                        @if (Session::has('success'))
                            <div class="alert alert-success alert-dismissible">
                                {!! Session::get('success') !!}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php Session::forget('success'); ?> <!-- Destroy the success session -->
                        @elseif(Session::has('error'))
                            <div class="alert alert-danger alert-dismissible">
                                {!! Session::get('error') !!}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            <?php Session::forget('error'); ?> <!-- Destroy the error session -->
                            else
                            <div class="alert alert-warning alert-dismissible">
                                {!! Session::get('warning') !!}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                            <?php Session::forget('warning'); ?> <!-- Destroy the error session -->
                        @endif
                        <div class="col-xl-12 col-md-12 d-flex align-items-center">
                            <h1>Welcome to QueueMaster</h1>
                            <img src="{{ asset('asset/img/hello.png') }}" class="ms-4">
                        </div>
                    </div>

                    <h6 class="text-muted mb-4">How could we help you ?</h6>
                </div>
                <div class="container">
                    <div class="row mb-4">
                        <div class="col-xl-12 col-md-12">
                            <div class="card allcard">
                                <div class="card-content">
                                    <div class="card-body statsCard">
                                        <div class="align-items-center d-flex">
                                            <span class="material-symbols-outlined QueueMasterIcon staticon">
                                                bar_chart
                                            </span>
                                            <h4 class="ms-2 mb-0 QueueMasterheading stattext">Stats</h4>
                                        </div>
                                        <div class="media-body">
                                            <p class="mb-0 pt-2 pb-3">Real-Time insights with comprehensive analytics
                                                showcaing your queue's performance.</p>
                                        </div>
                                        <div class="align-self-center">
                                            <a href="{{ url('stats-room-view') }}">
                                                <button class="btn btn-primary statbutton">Go</button></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-xl-12 col-md-12">
                            <div class="card allcard">
                                <div class="card-content">
                                    <div class="card-body queueCard">
                                        <div class="align-items-center d-flex">
                                            <span class="material-symbols-outlined QueueMasterIcon queueicon">
                                                meeting_room
                                            </span>
                                            <h4 class="ms-2 mb-0 queuetext QueueMasterheading">Queue Room</h4>
                                        </div>
                                        <div class="media-body">
                                            <p class="mb-0 pt-2 pb-3">Setup and customize queueing rooms to match your
                                                specific needs.</p>
                                        </div>
                                        <div class="align-self-center">
                                            <a href="{{ url('queue-room-view') }}">
                                                <button class="btn btn-primary queuebutton">Go</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-xl-12 col-md-12">
                            <div class="card allcard">
                                <div class="card-content">
                                    <div class="card-body tempCard">
                                        <div class="align-items-center d-flex">
                                            <span class="material-symbols-outlined QueueMasterIcon tempicon">
                                                team_dashboard
                                            </span>
                                            <h4 class="ms-2 mb-0 temptext QueueMasterheading">Template Management</h4>
                                        </div>
                                        <div class="media-body">
                                            <p class="mb-0 pt-2 pb-3">Manage design template in / out rules and
                                                notitfications.</p>
                                        </div>
                                        <div class="align-self-center">
                                            <a href="{{ url('temp-manage') }}">
                                                <button class="btn btn-primary tempbutton">Go</button></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-xl-12 col-md-12">
                            <div class="card allcard">
                                <div class="card-content">
                                    <div class="card-body devCard">
                                        <div class="align-items-center d-flex">
                                            <i class="fa fa-code developericon QueueMasterIcon" aria-hidden="true"></i>
                                            <h4 class="ms-2 mb-0 devtext QueueMasterheading">Developer</h4>
                                        </div>
                                        <div class="media-body">
                                            <p class="mb-0 pt-2 pb-3">API documentation, integration guides and developer
                                                tools,</p>
                                        </div>
                                        <div class="align-self-center">
                                            <a href="{{ url('developer') }}">
                                                <button class="btn btn-primary devbutton">Go</button></a>
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
    <script type="text/javascript">
        $(document).ready(function() {
            // Automatically close the alert after 3 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow', function() {
                    $(this).remove(); // Remove the alert from the DOM
                });
            }, 3000);
        });
    </script>
@endsection
