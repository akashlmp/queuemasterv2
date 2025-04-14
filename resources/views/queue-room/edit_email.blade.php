@extends('common.layouts')
@section('content')
@include('common.sidebar')
@include('common.header')
<link rel="stylesheet" href="{{ asset('asset/css/queueRoomCreate.css') }}">
<main id="main" class="bgmain">
    <!-- =======  Section ======= -->
    <section class="SectionPadding">
        <div class="container">
            <div class="row mb-3">
                <div class="col-xl-12 col-md-12 d-flex queueDesignicon">
                    <nav aria-label="breadcrumb " class="QueueBreadCrumb emailTempQueueRoom">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item Homebreadcrumb">
                                <a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ url('temp-manage') }}">Template Management</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ url('email-notice') }}">SMS / Email Notice</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#">{{ $template->email_template_name }}</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="row m-0">
                <div class="col-md-12 p-4 card">
                    <div class="LeftGreenborder ps-4">
                      <h5 class="FormHeading"><b>Email Notice</b></h5>
                      <form action="{{ url('update-email-temp/'.$template->id) }}" method="POST">
                        @csrf
                          <div class="form-group smsTemp">
                            <label for="SMSTemplate" class="FormInputBoxLabel">Template name</label>
                            <input type="text" class="form-control FormInputBox" name="SMSTemplate" value="{{ $template->email_template_name }}">
                          </div>
                          <input type="hidden" value="{{ $template->html_content }}" id="editoremailContent" name="html_content">
                          <div class="row mt-3 emailTemp">
                            <div class="col-md-12">
                              <button type="button" class="btn editBtn" id="EmailEditBtn" data-bs-toggle="modal" data-bs-target="#emailModal">Edit <i class="fa fa-pencil-square-o ps-1" aria-hidden="true"></i>
                              </button>
                            </div>
                          </div>
                          <div class="row mt-3 emailTemp">
                            <div class="col-md-12 text-end">
                               <button type="submit" class="btn editBtn">submit</button>
                            </div>
                          </div>
                          
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<!-- Email Template Modal -->
<div class="modal fade" id="emailModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
      <h5 class="FormHeading"><b>Email notice</b></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <textarea id="emailEditor"></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="emailUnderstoodBtn">Save</button>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset('asset/js/jquery.min.js') }}" type="text/javascript"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/34.0.0/classic/ckeditor.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var emailEditor;

    // Initialize CKEditor for Email Template Modal
    ClassicEditor
      .create(document.querySelector('#emailEditor'))
      .then(newEditor => {
          emailEditor = newEditor;
          emailEditor.setData(`{!! addslashes($template->html_content) !!}`);
      })
      .catch(error => {
          console.error('CKEditor Error:', error);
      });

    

    // "Understood" button for Email Template Modal
    document.getElementById('emailUnderstoodBtn').addEventListener('click', function() {
      var emailEditorData = emailEditor.getData();
      document.getElementById('editoremailContent').value = emailEditorData;
      $('#emailModal').modal('hide'); // Hide modal after storing data
    });
  });
</script>
@endsection