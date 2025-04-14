@extends('common.layouts')
@section('content')

<link rel="stylesheet" href="{{ asset('asset/css/canvas.css') }}">
<link href="{{ asset('asset/css/grapes.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
<style>

</style>

<!-- <main id="main" class="bgmain">                -->
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible">
                {!! Session::get('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @elseif(Session::has('error'))
            <div class="alert alert-danger alert-dismissible">
                {!! Session::get('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif
    <section class="SectionPadding">
        <!-- =======  Section ======= -->
        <div class="row">
            <div class="col-12">
                <!-- Nav tabs -->
                <div class="queue-gjs-TopHeader">
                    <button class="btn Canelandbackbtn"><span class="material-symbols-outlined arrowicon">arrow_back_ios</span> <span class="textcolor">Cancel and back</span></button>
                    <ul class="nav nav-tabs " id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="Queue-Page-tab" data-bs-toggle="tab" data-bs-target="#QueuePage" type="button" role="tab" aria-controls="QueuePage" aria-selected="true">Queue Page</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="Pre-queue-Page-tab" data-bs-toggle="tab" data-bs-target="#PrequeuePage" type="button" role="tab" aria-controls="PrequeuePage" aria-selected="false">Pre-queue Page</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="PostqueuePage-tab" data-bs-toggle="tab" data-bs-target="#PostqueuePage" type="button" role="tab" aria-controls="PostqueuePage" aria-selected="false">Post-queue Page</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="PriorityAccessPage-tab" data-bs-toggle="tab" data-bs-target="#PriorityAccessPage" type="button" role="tab" aria-controls="PriorityAccessPage" aria-selected="false">Priority Access Page</button>
                        </li>
                    </ul>
                    <button class="btn SaveandexitBtn" id="SaveBtn"><span class="material-symbols-outlined arrowicon">save</span><span class="textcolor">Save and exit</span></button>
                </div>
                <!-- Tab panes -->
                <div class="tab-content queue-gjs-TopContent">
                    <div class="tab-pane active" id="QueuePage" role="tabpanel" aria-labelledby="Queue-Page-tab" tabindex="0">
                        <div class="queuegjs">
                            <div id="gjs" style="height: 100vh; width: 100%;">
                                <!-- GrapesJS will render here -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="PrequeuePage" role="tabpanel" aria-labelledby="Pre-queue-Page-tab" tabindex="0">
                        <div class="queuegjs">
                            <div id="gjs2" style="height: 100vh; width: 100%;">
                                <!-- GrapesJS will render here -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="PostqueuePage" role="tabpanel" aria-labelledby="PostqueuePage-tab" tabindex="0">
                        <div class="queuegjs">
                            <div id="gjs3" style="height: 100vh; width: 100%;">
                                <!-- GrapesJS will render here -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="PriorityAccessPage" role="tabpanel" aria-labelledby="PriorityAccessPage-tab" tabindex="0">
                        <div class="queuegjs">
                            <div id="gjs4" style="height: 100vh; width: 100%;">
                                <!-- GrapesJS will render here -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- End  Section -->
    </section>
<!-- </main> -->
<!-- Include GrapesJS script -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script src="{{ asset('asset/js/grapes.min.js') }}"></script>


<script>
    var editor1 = grapesjs.init({
        container: '#gjs',
        components: `<?php echo $queue_page_tab; ?> `,
        style: '',
        storageManager: false,
        plugins: ['gjs-preset-webpage', 'grapesjs-lory-slider', 'grapesjs-tabs'],
        pluginsOpts: {
            'gjs-preset-webpage': {
                modalImportTitle: 'Import Template',
                modalImportButton: 'Import',
                modalImportLabel: '',
                modalImportContent: '',
            }
        },
        canvas: {
            scripts: [],
            styles: [],
        }
    });
    // Add additional components
    editor1.BlockManager.add('custom-section', {
        label: '<div><div><span class="material-symbols-outlined">check_box_outline_blank</span></div><div><span style="font-size:12px; margin-bottom:4px;">Section</span> </div></div>', // Label for your block
        content: {
            type: 'div',
            components: [],
            style: {
                'min-height': '200px',
                'padding': '20px'
            }
        },

    });

    editor1.BlockManager.add('heading', {
        label: '<div><div><img src="{{ asset('asset/img/Heading.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Heading</span> </div></div>',
        content: '<h1 class="text-center">Heading Text</h1>',
    });
    editor1.BlockManager.add('text-editor', {
        label: '<div><div><img src="{{ asset('asset/img/Text-Editor.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Text Editor</span> </div></div>',
        attributes: {
            class: 'gjs-fonts gjs-f-text'
        },
        content: {
            type: 'text',
            content: 'Editable Text',
            style: {
                padding: '10px',
                'min-height': '50px',
                'text-align': 'center'
            },
            activeOnRender: 1
        }
    });
    editor1.BlockManager.add('image', {
        label: '<div><div><img src="{{ asset('asset/img/image.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Image</span> </div></div>',
        content: {
            type: 'image',
            style: {
                color: 'black'
            },
            activeOnRender: 1
        },
    });

    editor1.BlockManager.add('video', {
        label: ' <div><div><span class="material-symbols-outlined">smart_display</span></div><div><span style="font-size:12px; margin-bottom:4px;">Video</span> </div></div>',
        content: '<div class="video-container"><iframe width="560" height="315" src="https://www.youtube.com/embed/YOUR_VIDEO_ID_HERE" frameborder="0" allowfullscreen></iframe></div>',
    });
    editor1.BlockManager.add('progress-bar', {
        label: '<div><div><img src="{{ asset('asset/img/Progress-Bar.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">progress-bar</span> </div></div>',
        content: '<div class="progress progressBar" style="min-height:40px; " id="progressbar"><div class="progress-bar Customprogress-bar" id="Customprogressbar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="min-height:40px;width:75%">75%</div></div>',
    });

    editor1.BlockManager.add('divQueuePositionfield', {
        label: '<div><div><img src="{{ asset('asset/img/Queue-Number.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Queue Position</span> </div></div>',
        content: '<div class="editable-div" contenteditable="true">Your number in line: <span>5,213</span></div>'
    });
    editor1.BlockManager.add('divExpectedWaitingTimefield', {
        label: '<div><div><img src="{{ asset('asset/img/Group.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Expected Waiting Time</span> </div></div>',
        content: '<div class="editable-div" contenteditable="true">Your estimated wait time: <span>5 minutes</span></div>'
    });
    editor1.BlockManager.add('divSystemTimefield', {
        label: '<div><div><img src="{{ asset('asset/img/System-time.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">System Time</span> </div></div>',
        content: '<div class="editable-div" contenteditable="true">System Time: <span>5 minutes</span></div>'
    });
    editor1.BlockManager.add('form', {
        label: '<div><div><img src="{{ asset('asset/img/mail.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Notify User</span> </div></div>',
        content: '<form class="custom-form "><div><div class="form-group"><input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email"></div><button type="submit" class="btn btn-primary">Submit</button></div></form>',
    });
    editor1.BlockManager.add('Announcement', {
        label: '<div><div><img src="{{ asset('asset/img/announcement.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Announcement</span> </div></div>',
        attributes: {
            class: 'gjs-fonts gjs-f-text'
        },
        content: {
            type: 'text',
            content: 'Editable Text',
            style: {
                padding: '10px',
                'min-height': '50px',
                'text-align': 'center'
            },
            activeOnRender: 1
        }
    });
</script>
<!-- QueuePage end -->

<script>
    var editor2 = grapesjs.init({
        container: '#gjs2',
        components: `<?php echo $pre_queue_page_tab; ?> `,
        style: '',
        storageManager: false,
        plugins: ['gjs-preset-webpage', 'grapesjs-lory-slider', 'grapesjs-tabs'],
        pluginsOpts: {
            'gjs-preset-webpage': {
                modalImportTitle: 'Import Template',
                modalImportButton: 'Import',
                modalImportLabel: '',
                modalImportContent: '',
            }
        },
        canvas: {
            scripts: [],
            styles: [],
        }
    });
    // Add additional components
    editor2.BlockManager.add('custom-section2', {
        label: '<div><div><span class="material-symbols-outlined">check_box_outline_blank</span></div><div><span style="font-size:12px; margin-bottom:4px;">Section</span> </div></div>', // Label for your block
        content: {
            type: 'div',
            components: [],
            style: {
                'min-height': '200px',
                'padding': '20px'
            }
        },

    });

    editor2.BlockManager.add('heading2', {
        label: '<div><div><img src="{{ asset('asset/img/Heading.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Heading</span> </div></div>',
        content: '<h1 class="text-center">Heading Text</h1>',
    });
    editor2.BlockManager.add('text-editor2', {
        label: '<div><div><img src="{{ asset('asset/img/Text-Editor.png ') }}"/></div><span style="font-size:12px; margin-bottom:4px;">Text Editor</span> </div></div>',
        attributes: {
            class: 'gjs-fonts gjs-f-text'
        },
        content: {
            type: 'text',
            content: 'Editable Text',
            style: {
                padding: '10px',
                'min-height': '50px',
                'text-align': 'center'
            },
            activeOnRender: 1
        }
    });
    editor2.BlockManager.add('image2', {
        label: '<div><div><span class="material-symbols-outlined">photo_library</span></div><div><span style="font-size:12px; margin-bottom:4px;">Image</span> </div></div>',
        content: {
            type: 'image',
            style: {
                color: 'black'
            },
            activeOnRender: 1
        },
    });

    editor2.BlockManager.add('video2', {
        label: ' <div><div><span class="material-symbols-outlined">smart_display</span></div><div><span style="font-size:12px; margin-bottom:4px;">Video</span> </div></div>',
        content: '<div class="video-container"><iframe width="560" height="315" src="https://www.youtube.com/embed/YOUR_VIDEO_ID_HERE" frameborder="0" allowfullscreen></iframe></div>',
    });

    editor2.BlockManager.add('Announcement2', {
        label: '<div><div><img src="{{ asset('asset/img/announcement.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Announcement</span> </div></div>',
        attributes: {
            class: 'gjs-fonts gjs-f-text'
        },
        content: {
            type: 'text',
            content: 'Editable Text',
            style: {
                padding: '10px',
                'min-height': '50px',
                'text-align': 'center'
            },
            activeOnRender: 1
        }
    });
    editor2.BlockManager.add('divQueueStartTimefield2', {
        label: '<div><div><span class="material-symbols-outlined">schedule</span></div><div><span style="font-size:12px; margin-bottom:4px;">Queue Start Time</span> </div></div>',
        content: '<div class="editable-div" contenteditable="true"><span>1 hour : 24 minutes : 23 seconds</span></div>'
    });
    editor2.BlockManager.add('divSystemTimefield2', {
        label: '<div><div><img src="{{ asset('asset/img/System-time.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">System Time</span> </div></div>',
        content: '<div class="editable-div" contenteditable="true">System Time: <span>5 minutes</span></div>'
    });
    editor2.BlockManager.add('form2', {
        label: '<div><div><span class="material-symbols-outlined">mail</span></div><div><span style="font-size:12px; margin-bottom:4px;">Notify User</span> </div></div>',
        content: '<form class="custom-form "><div><div class="form-group"><input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email"></div><button type="submit" class="btn btn-primary">Submit</button></div></form>',
    });
</script>
<!-- PrequeuePage end -->

<script>
    var editor3 = grapesjs.init({
        container: '#gjs3',
        components: `	<?php echo $postqueue_page_tab; ?> `,
        style: '',
        storageManager: false,
        plugins: ['gjs-preset-webpage', 'grapesjs-lory-slider', 'grapesjs-tabs'],
        pluginsOpts: {
            'gjs-preset-webpage': {
                modalImportTitle: 'Import Template',
                modalImportButton: 'Import',
                modalImportLabel: '',
                modalImportContent: '',
            }
        },
        canvas: {
            scripts: [],
            styles: [],
        }
    });
    // Add additional components
    editor3.BlockManager.add('custom-section2', {
        label: '<div><div><span class="material-symbols-outlined">check_box_outline_blank</span></div><div><span style="font-size:12px; margin-bottom:4px;">Section</span> </div></div>', // Label for your block
        content: {
            type: 'div',
            components: [],
            style: {
                'min-height': '200px',
                padding: '20px',
            }
        },

    });

    editor3.BlockManager.add('heading2', {
        label: '<div><div><img src="{{ asset('asset/img/Heading.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Heading</span> </div></div>',
        content: '<h1 class="text-center">Heading Text</h1>',
    });
    editor3.BlockManager.add('text-editor2', {
        label: '<div><div><img src="{{ asset('asset/img/Text-Editor.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Text Editor</span> </div></div>',
        attributes: {
            class: 'gjs-fonts gjs-f-text'
        },
        content: {
            type: 'text',
            content: 'Editable Text',
            style: {
                padding: '10px',
                'min-height': '50px',
                'text-align': 'center'
            },
            activeOnRender: 1
        }
    });
    editor3.BlockManager.add('image2', {
        label: '<div><div><span class="material-symbols-outlined">photo_library</span></div><div><span style="font-size:12px; margin-bottom:4px;">Image</span> </div></div>',
        content: {
            type: 'image',
            style: {
                color: 'black'
            },
            activeOnRender: 1
        },
    });

    editor3.BlockManager.add('video2', {
        label: ' <div><div><span class="material-symbols-outlined">smart_display</span></div><div><span style="font-size:12px; margin-bottom:4px;">Video</span> </div></div>',
        content: '<div class="video-container"><iframe width="560" height="315" src="https://www.youtube.com/embed/YOUR_VIDEO_ID_HERE" frameborder="0" allowfullscreen></iframe></div>',
    });


    editor3.BlockManager.add('Announcement2', {
        label: '<div><div><img src="{{ asset('asset/img/announcement.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Announcement</span> </div></div>',
        attributes: {
            class: 'gjs-fonts gjs-f-text'
        },
        content: {
            type: 'text',
            content: 'Editable Text',
            style: {
                padding: '10px',
                'min-height': '50px',
                'text-align': 'center'
            },
            activeOnRender: 1
        }
    });
    editor3.BlockManager.add('divSystemTimefield2', {
        label: '<div><div><img src="{{ asset('asset/img/System-time.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">System Time</span> </div></div>',
        content: '<div class="editable-div" contenteditable="true">System Time: <span>5 minutes</span></div>'
    });
</script>
<!-- PostqueuePage end -->

<script>
    var editor4 = grapesjs.init({
        container: '#gjs4',
        components: `	<?php echo $priority_access_page_tab; ?>`,
        style: '',
        storageManager: false,
        plugins: ['gjs-preset-webpage', 'grapesjs-lory-slider', 'grapesjs-tabs'],
        pluginsOpts: {
            'gjs-preset-webpage': {
                modalImportTitle: 'Import Template',
                modalImportButton: 'Import',
                modalImportLabel: '',
                modalImportContent: '',
            }
        },
        canvas: {
            scripts: [],
            styles: [],
        }
    });
    // Add additional components
    editor4.BlockManager.add('custom-section4', {
        label: '<div><div><span class="material-symbols-outlined">check_box_outline_blank</span></div><div><span style="font-size:12px; margin-bottom:4px;">Section</span> </div></div>', // Label for your block
        content: {
            type: 'div',
            components: [],
            style: {
                'min-height': '200px',
                'padding': '20px'
            }
        },

    });
    editor4.BlockManager.add('heading4', {
        label: '<div><div><img src="{{ asset('asset/img/Heading.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Heading</span> </div></div>',
        content: '<h1 class="text-center">Heading Text</h1>',
    });
    editor4.BlockManager.add('text-editor2', {
        label: '<div><div><img src="{{ asset('asset/img/Text-Editor.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Text Editor</span> </div></div>',
        attributes: {
            class: 'gjs-fonts gjs-f-text'
        },
        content: {
            type: 'text',
            content: 'Editable Text',
            style: {
                padding: '10px',
                'min-height': '50px',
                'text-align': 'center'
            },
            activeOnRender: 1
        }
    });
    editor4.BlockManager.add('image2', {
        label: '<div><div><span class="material-symbols-outlined">photo_library</span></div><div><span style="font-size:12px; margin-bottom:4px;">Image</span> </div></div>',
        content: {
            type: 'image',
            style: {
                color: 'black'
            },
            activeOnRender: 1
        },
    });

    editor4.BlockManager.add('video2', {
        label: ' <div><div><span class="material-symbols-outlined">smart_display</span></div><div><span style="font-size:12px; margin-bottom:4px;">Video</span> </div></div>',
        content: '<div class="video-container"><iframe width="560" height="315" src="https://www.youtube.com/embed/YOUR_VIDEO_ID_HERE" frameborder="0" allowfullscreen></iframe></div>',
    });
    editor4.BlockManager.add('Announcement2', {
        label: '<div><div><img src="{{ asset('asset/img/announcement.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Announcement</span> </div></div>',
        attributes: {
            class: 'gjs-fonts gjs-f-text'
        },
        content: {
            type: 'text',
            content: 'Editable Text',
            style: {
                padding: '10px',
                'min-height': '50px',
                'text-align': 'center'
            },
            activeOnRender: 1
        }
    });
    editor4.BlockManager.add('divSystemTimefield2', {
        label: '<div><div><img src="{{ asset('asset/img/System-time.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">System Time</span> </div></div>',
        content: '<div class="editable-div" contenteditable="true">System Time: <span>5 minutes</span></div>'
    });
    editor4.BlockManager.add('form2', {
        label: '<div><div><img src="{{ asset('asset/img/access-code.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Access code</span> </div></div>',
        content: '<form class="custom-form "><div><div class="form-group"><input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="access code"></div><button type="submit" class="btn btn-primary">Submit</button></div></form>',
    });
</script>

<script type="text/javascript">
    function saveEditorsContent() {
        var editor1Html = editor1.getHtml();
        var editor1Css = editor1.getCss();

        var editor2Html = editor2.getHtml();
        var editor2Css = editor2.getCss();

        var editor3Html = editor3.getHtml();
        var editor3Css = editor3.getCss();

        var editor4Html = editor4.getHtml();
        var editor4Css = editor4.getCss();


        var editor1HtmlFormat = '<!DOCTYPE html><html><head><style>' + editor1Css + '</style></head><body>' + editor1Html + '</body></html>';
        var editor2HtmlFormat = '<!DOCTYPE html><html><head><style>' + editor2Css + '</style></head><body>' + editor2Html + '</body></html>';
        var editor3HtmlFormat = '<!DOCTYPE html><html><head><style>' + editor3Css + '</style></head><body>' + editor3Html + '</body></html>';
        var editor4HtmlFormat = '<!DOCTYPE html><html><head><style>' + editor4Css + '</style></head><body>' + editor4Html + '</body></html>';


        var dataToSend = {
            queue_page: appendInputFields(editor1Html, editor1Css , "queue_page"),
            prequeue_page: appendInputFields(editor2Html, editor2Css , "prequeue_page" ),
            postqueue_page: appendInputFields(editor3Html, editor3Css , "postqueue_page"),
            priority_access_page: appendInputFields(editor4Html, editor4Css , "priority_access_page"),
            room_id : "<?php echo $room_id; ?>"
        };
        var dataToSend = {
            queue_page: appendInputFields(editor1Html, editor1Css , "queue_page"),
            prequeue_page: appendInputFields(editor2Html, editor2Css , "prequeue_page" ),
            postqueue_page: appendInputFields(editor3Html, editor3Css , "postqueue_page"),
            priority_access_page: appendInputFields(editor4Html, editor4Css , "priority_access_page"),
            room_id : "<?php echo $room_id; ?>",
            lang_id : "<?php echo $lang_id; ?>",
        };

        // Make AJAX request
        $.ajax({
            url: '{{ url('canvas-save') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify(dataToSend),
            success: function(response) {
               location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Error occurred while saving data:', error);
                location.reload();
            }
        });
    }


    function appendInputFields(html, css , form_type) 
    {
        var this_url = "<?php echo  env('APP_URL') ?>" + "inline-edit-forms";
        var this_form_type = form_type;
        var tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;

        var form = tempDiv.querySelector('form.example');
        if (form) {
            form.innerHTML += '<input type="hidden" name="room_id" value="<?php echo $room_id; ?>">';
            form.action = "";
        }

        return '<!DOCTYPE html><html><head><style>' + css + '</style></head><body>' + tempDiv.innerHTML + '</body></html>';
    }
    document.getElementById('SaveBtn').addEventListener('click', function() {
        saveEditorsContent();
    });
</script>
<!-- PriorityAccessPage end -->
@endsection