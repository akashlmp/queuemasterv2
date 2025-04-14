@extends('admin.common.layouts')

@extends('admin.common.sidebar')
@extends('admin.common.navbar')
@section('content')

<!-- <div class="mainbg"> -->
<!-- <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.1/ckeditor5.css"> -->
    <div class="card">
    <div class="card-header">
       Terms of Use
    </div>
   
    <div class="card-body">
            <div class="col-12 col-md-6  col-lg-6 cardwidth">
                <form method="POST" action="{{ url('admin/addPrivacyPolicysave') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="">Title</label>
                        <input type="text" class="form-control FormInputBox @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $privacyPolicy->name ?? '') }}" required>

                        <div id="title-error" class="error-msg text-danger pt-2"></div>

                        @if ($errors->has('title'))

                        <span class="text-danger">{{ $errors->first('title') }}</span>

                        @endif

                      </div>

                      
                        <input type="hidden" class="form-control FormInputBox @error('slug') is-invalid @enderror" id="slug" name="slug" value="privacy-policy" required readonly>

                        
                     <label for="">Content</label>
                    <textarea class="addHeight" name="textData" id="editor" row="10" col="6">{{ old('textData', $privacyPolicy->page_data ?? '') }}</textarea>

                    <button class="btn bsb-btn-2xl submitbtn mt-4 btn-primary" type="submit" id="submitButton">Register QueueMaster</button>
                </form>
            </div>
        </div>
    </div>
<!-- </div> -->
<!-- CKEditor code | start -->
<script type="importmap">
		{
			"imports": {
				"ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.1/ckeditor5.js",
				"ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.1/"
			}
		}
	</script>
		<script type="module">
			import {
				ClassicEditor,
                Essentials,
                Paragraph,
                Bold,
                Italic,
                Font,
                Heading,
                Link,
                List,
                Alignment,
                Image,
                Table,
                Code,
                BlockQuote,
                Highlight,
                MediaEmbed,
                Strikethrough
			} from 'ckeditor5';
            
			ClassicEditor
				.create( document.querySelector( '#editor' ), {
					plugins: [ 
						Essentials, Paragraph, Bold, Italic, Font,
						Heading, Link, List, Alignment, Image, Table,
						Code, BlockQuote, Highlight, MediaEmbed, Strikethrough 
					],
					toolbar: [
						'heading', '|', 'bold', 'italic', 'underline', 'strikethrough', '|',
						'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
						'link', 'blockQuote', 'code', 'highlight', '|',
						'bulliedList', 'numberedList', 'outdent', 'indent', '|',
						'imageUpload', 'mediaEmbed', 'insertTable', 'alignment', '|',
						'undo', 'redo'
					]
				} )
				.then( editor => {
					window.editor = editor;
				} )
				.catch( error => {
					console.error( error );
				} );
		</script>
		<!-- A friendly reminder to run on a server, remove this during the integration. -->
		<script>
			window.onload = function() {
				if ( window.location.protocol === 'file:' ) {
					alert( 'This sample requires an HTTP server. Please serve this file with a web server.' );
				}
			};
		</script>
    <!-- CKEditor code | end -->
@endsection