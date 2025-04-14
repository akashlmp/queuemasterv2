@extends('common.layouts')

@section('content')

@extends('common.sidebar')
@extends('common.header')
<link rel="stylesheet" href="{{ asset('asset/css/userProfile.css') }}">

<main id="main" class="bgmain">
    <div class="container">
        <div class="row mb-3">
            <div class="col-xl-8 col-md-12 d-flex userProfileicon">
                <nav aria-label="breadcrumb ">
                    <ol class="breadcrumb navbreadcrum">
                        <li class="breadcrumb-item"><a href="#">
                                <i class="fa fa-home userProfileicon" aria-hidden="true"></i></a>
                        </li>
                        <li class="breadcrumb-item " aria-current="page">
                            <a href="#" class="none">
                                <div class="userProfileText text-center">Template Management</div>
                            </a>
                        <li class="breadcrumb-item " aria-current="page">
                            <div class="userProfileText text-center">Edit </div>
                        </li>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- card -->
        <div class="card card-body">
            <form action="{{ route('update-email-notice', $email_notice->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row m-0">
                    <div class="col-md-11 ps-0 pt-2 pb-4">
                        <div class="LeftGreenborder ps-4">
                            <h5 class="FormHeading"><b>Email Notice Information</b></h5>
                            <p class="FormPara">Email Template</p>
                            <div class="mb-3">
                                <textarea class="form-control" id="email_template" name="email_template">{{ $email_notice->email_template }}</textarea>
                            </div>
                            <p class="FormPara">Name</p>
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="email_template_name" name="email_template_name" value="{{ $email_notice->email_template_name }}">
                                    <!-- Show validation errors if any -->
                                    @error('email_template_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <p class="FormPara">Status</p>
                                <div class="mb-3">
                                    <select class="form-select" id="emailNotice_status" name="emailNotice_status">
                                        <option value="0" {{ $email_notice->status == 0 ? 'selected' : '' }}>Inactive</option>
                                        <option value="1" {{ $email_notice->status == 1 ? 'selected' : '' }}>Active</option>
                                    </select>
                                    <!-- Show validation errors if any -->
                                    @error('emailNotice_status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                        </div>
                    </div>
                </div>

                <!-- button -->
                <div class="row m-0 mt-2">
                        <div class="col-md-12 ps-0 pt-2 pb-4">
                            <div class="d-flex align-items-center justify-content-end">
                                <button type="submit" class="btn bsb-btn-2xl subbtnbtn submitbtn d-flex align-items-center justify-content-center">
                                    Save
                                    <span class="material-symbols-outlined ms-2">save</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

<script src="{{ asset('asset/js/userProfile.js') }}" type="text/javascript"></script>

<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#email_template'))
        .then(editor => {
            console.log(editor);
            editor.model.document.on('change:data', () => {
                document.querySelector('#email_template').value = editor.getData();
            });
        })
        .catch(error => {
            console.error(error);
        });
</script>

@endsection