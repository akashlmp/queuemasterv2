@extends('admin.common.layouts')
@extends('admin.common.sidebar')
@extends('admin.common.navbar')


@section('content')
<link rel="stylesheet" href="{{ asset('asset/css/developer.css') }}">
<main id="main" class="bgmain">
    <!-- ======= About Section ======= -->
    <section class="SectionPadding">
        <div class="container">
            <div class="row">
            @if(Session::has('success'))
                    <div class="alert alert-success">
                        {!! Session::get('success') !!}
                    </div>
                @elseif(Session::has('error'))
                    <div class="alert alert-danger">
                        {!! Session::get('error') !!}
                    </div>
                @endif
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
            <div class="card">
                <div class="card-body">
                    <div class="LeftRedborder ps-4">
                        <h5><b>Integration</b></h5>
                        <p>You must insert the JavaScript snippet below into the page(s) on your site to enable the trigger logic.</p>
                        <div>
                            <form id="updateScriptForm" method="POST" action="{{ route('update.script') }}">
                                @csrf
                                <textarea class="form-control" rows="10" id="developer" name="script" placeholder="">{{ $script }}</textarea>
                                <div class="text-end mt-4">
                                    <button type="submit" class="btn btn-danger ">
                                        <div class="d-flex align-items-center">
                                            <span>Update </span>
                                           
                                        </div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection