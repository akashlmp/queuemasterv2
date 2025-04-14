@extends('common.layouts')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Card -->
                <div class="card shadow-lg" style="background-color: #f8f9fa;">
                    <!-- Card Header with Logo -->
                    <div class="card-header text-center bg-light text-dark">
                        <img src="{{ asset('asset/img/Logo=with-text.png') }}" alt="Logo" class="img-fluid mb-2" style="max-width: 150px;">
                        <h2>{{ $page->name }}</h2>
                    </div>

                    <!-- Card Body with Content -->
                    <div class="card-body" style="background-color: #ffffff;">
                        <div class="content">
                            {!! $page->page_data !!}
                        </div>
                    </div>

                    <!-- Card Footer (optional) -->
                    <div class="card-footer text-center text-muted" style="background-color: #f1f3f5;">
                        © {{ date('Y') }} Queuingmaster. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
