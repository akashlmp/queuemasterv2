<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
?>
@extends('common.layouts')
@section('content')
    <link rel="stylesheet" href="{{ asset('asset/css/canvas.css') }}">
    <link href="{{ asset('asset/css/grapes.min.css') }}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <script>
        function confirmBeforeLeaving() {
            return confirm("Changes might not be saved. Are you sure you want to leave?");
        }
    </script>

    <!-- <main id="main" class="bgmain">                -->
    @if (Session::has('success'))
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
    <section class="SectionPaddingg">
        <!-- =======  Section ======= -->
        <div class="row">
            <div class="columnclassborder col-2">

                <?php
                // Check if $languages is a string and not already an array
                if (is_string($languages)) {
                    $languages = json_decode($languages, true);
                }
                
                for ($i = 0; $i < count($languages); $i++) {    
                    $query = "SELECT code FROM languages WHERE code LIKE '" . $languages[$i] . "'";
                    $lang_name = DB::select($query);
                
                    if ($languages[$i] == $lang_id) {
                        $href_link = '#';
                        $checker_function = '';
                    } else {
                        if (!empty($template_id)) {
                            $href_link = env('APP_URL') . 'edit-template-inline-room/' . $template_id . '/' . $languages[$i];
                            if (!empty($room_id)) {
                                $href_link .= '/' . $room_id;
                            }
                        } elseif (!empty($room_id)) {
                            $href_link = env('APP_URL') . 'edit-inline-room/' . $room_id . '/' . $languages[$i];
                        }
                
                        $checker_function = "onclick='return confirmBeforeLeaving()'";
                    }
                }
                ?>

                <div class="imgdiv">
                    <h6 class="fontcolor"><?php echo $lang_name[0]->code; ?></h6>

                    <a href="<?php echo $href_link; ?>" <?php echo $checker_function; ?>>
                        <button class="btnpadding">
                            <!-- <img src="https://queuing.lambetech.com/public/asset/img/ss.png" alt="logo" class="colfirstimg" id="image1"> -->
                            <img src="{{ asset('/asset/img/ss.png') }}" alt="logo" class="colfirstimg"
                                id="image1">
                        </button>
                    </a>
                </div>
                <?php
                
                ?>
            </div>
            <div class="col-10">
                <!-- Nav tabs -->
                <div class="queue-gjs-TopHeader">
                    <!-- <button class="btn Canelandbackbtn"><span class="material-symbols-outlined arrowicon">arrow_back_ios</span> <span class="textcolor">Cancel and back</span></button> -->
                    <ul class="nav nav-tabs " id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="Queue-Page-tab" data-bs-toggle="tab"
                                data-bs-target="#QueuePage" type="button" role="tab" aria-controls="QueuePage"
                                aria-selected="true">Queue Page</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="Pre-queue-Page-tab" data-bs-toggle="tab"
                                data-bs-target="#PrequeuePage" type="button" role="tab" aria-controls="PrequeuePage"
                                aria-selected="false">Pre-queue Page</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="PostqueuePage-tab" data-bs-toggle="tab"
                                data-bs-target="#PostqueuePage" type="button" role="tab" aria-controls="PostqueuePage"
                                aria-selected="false">Post-queue Page</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="PriorityAccessPage-tab" data-bs-toggle="tab"
                                data-bs-target="#PriorityAccessPage" type="button" role="tab"
                                aria-controls="PriorityAccessPage" aria-selected="false">Priority Access Page</button>
                        </li>
                    </ul>
                    <button class="btn SaveandexitBtn" id="SaveBtn"><span
                            class="material-symbols-outlined arrowicon">save</span><span class="textcolor">Save and
                            exit</span></button>
                </div>
                <!-- Tab panes -->
                <div class="tab-content queue-gjs-TopContent">
                    <div class="tab-pane active" id="QueuePage" role="tabpanel" aria-labelledby="Queue-Page-tab"
                        tabindex="0">
                        <div class="queuegjs">
                            <div id="gjs" style="height: 100vh; width: 100%;">
                                <!-- GrapesJS will render here -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="PrequeuePage" role="tabpanel" aria-labelledby="Pre-queue-Page-tab"
                        tabindex="0">
                        <div class="queuegjs">
                            <div id="gjs2" style="height: 100vh; width: 100%;">
                                <!-- GrapesJS will render here -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="PostqueuePage" role="tabpanel" aria-labelledby="PostqueuePage-tab"
                        tabindex="0">
                        <div class="queuegjs">
                            <div id="gjs3" style="height: 100vh; width: 100%;">
                                <!-- GrapesJS will render here -->
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="PriorityAccessPage" role="tabpanel"
                        aria-labelledby="PriorityAccessPage-tab" tabindex="0">
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
        function hideLayerManager(editorContainer) {
            var layerManagerButton = document.querySelector(editorContainer + ' .gjs-pn-btn.fa.fa-bars');
            if (layerManagerButton) {
                layerManagerButton.style.display = 'none'; // Hide the Layer Manager button
            }
        }

        var editor1 = grapesjs.init({
            container: '#gjs',
            components: `<?php echo $queue_page_tab; ?>`,
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

        editor1.on('load', function() {
            // Hide Layer Manager button for editor1
            hideLayerManager('#gjs');
            var transitionBlock1 = document.querySelector('#gjs .gjs-sm-property__transition');
            var transformBlock1 = document.querySelector('#gjs .gjs-sm-property__transform');
            var transformBlock2 = document.querySelector('.gjs-frame ');

                var iframeDoc = transformBlock2.contentDocument || transformBlock2.contentWindow.document;

                var elementInIframe = iframeDoc.querySelector('#ifw5q');
                elementInIframe.style.pointerEvents = 'none';

                var elementInIframe = iframeDoc.querySelector('#i378h');
                elementInIframe.style.pointerEvents = 'none';

                var elementInIframe = iframeDoc.querySelector('#i4wxz');
                elementInIframe.style.pointerEvents = 'none';

                var elementInIframe = iframeDoc.querySelector('#islrl');
                elementInIframe.style.pointerEvents = 'none';

                var elementInIframe = iframeDoc.querySelector('#ibx7v');
                elementInIframe.style.pointerEvents = 'none';



                console.log(elementInIframe,'transformBlock2 ');

            

            if (transitionBlock1) {
                transitionBlock1.style.display = 'none'; // Hide the Transition block
            }

            if (transformBlock1) {
                transformBlock1.style.display = 'none'; // Hide the Transform block
            }

            var viewCodeButton1 = document.querySelector('#gjs .gjs-pn-btn.fa.fa-code');
            if (viewCodeButton1) {
                viewCodeButton1.style.display = 'none'; // Hide the button
            }

            var openBlocksButton = document.querySelector('#gjs .gjs-pn-btn.fa.fa-th-large');
            if (openBlocksButton) {
                openBlocksButton.click(); // Simulate a click to select the "Open Blocks" button
            }
        });
        // Add additional components
        // editor1.BlockManager.add('custom-section', {
        //     label: '<div><div><span class="material-symbols-outlined">check_box_outline_blank</span></div><div><span style="font-size:12px; margin-bottom:4px;">Section</span> </div></div>', // Label for your block
        //     content: {
        //         type: 'div',
        //         components: [],
        //         style: {
        //             'min-height': '200px',
        //             'padding': '20px'
        //         }
        //     },

        // });

        // editor1.BlockManager.add('heading', {
        //     label: '<div><div><img src="{{ asset('asset/img/Heading.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Heading</span> </div></div>',
        //     content: '<h1 class="text-center">Heading Text</h1>',
        // });
        editor1.BlockManager.add('text-editor', {
            label: '<div><div><img src="{{ asset('asset/img/Text-Editor.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Text Editor</span> </div></div>',
            attributes: {
                class: 'gjs-fonts gjs-f-text'
            },
            content: {
                type: 'text',
                content: 'Design Your Text Here',
                style: {
                    padding: '10px',
                    'min-height': '50px',
                    'text-align': 'center',
                    'min-height': '50px',
                    'background': 'rgba(25, 92, 198, 8.1)',
                    'color': 'white',
                    'padding': '25px',
                    'font-weight': '600',
                    'border': '4px solid black'
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

        /* editor1.BlockManager.add('video', {
            label: ' <div><div><span class="material-symbols-outlined">smart_display</span></div><div><span style="font-size:12px; margin-bottom:4px;">Video</span> </div></div>',
            content: '<div class="video-container"><iframe width="560" height="315" src="https://www.youtube.com/embed/YOUR_VIDEO_ID_HERE" frameborder="0" allowfullscreen></iframe></div>',
        });
         editor1.BlockManager.add('progress-bar', {
            label: '<div><div><img src="{{ asset('asset/img/Progress-Bar.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">progress-bar</span> </div></div>',
            content: '<div id="iyb66" style="--progress: 0%; height: 20px; position: relative; padding: 2px 1px; margin-top: 12px; width: 100%;" class="progress"><div id="ie1np" title="progress_bar" style="width: var(--progress); height: 100%; transition-duration: 3s; transition-timing-function: ease; transition-delay: 3s; transition-property: width; --progress: 75%;" class="bar"></div></div>',
        });

        editor1.BlockManager.add('divQueuePositionfield', {
            label: '<div><div><img src="{{ asset('asset/img/Queue-Number.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Queue Position</span> </div></div>',
            content: '<div class="editable-div" contenteditable="true">Your number in line: <span style="color:rgb(20,88,194);font-weight:600;fomt-size:16px">5,213</span></div>'
        });

        editor1.BlockManager.add('divExpectedWaitingTimefield', {
            label: '<div><div><img src="{{ asset('asset/img/Group.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Expected Waiting Time</span> </div></div>',
            content: '<div class="editable-div" contenteditable="true">Your estimated wait time: <span  style="color:rgb(20,88,194);font-weight:600;fomt-size:16px">5 minutes</span></div>'
        });


        // editor1.BlockManager.add('divSystemTimefield', {
        //     label: '<div><div><img src="{{ asset('asset/img/System-time.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">System Time</span> </div></div>',
        //     content: '<div class="editable-div" contenteditable="true">System Time: <span>5 minutes</span></div>'
        // });
        editor1.BlockManager.add('form', {
            label: '<div><div><img src="{{ asset('asset/img/mail.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Notify User</span> </div></div>',
            content: '<form class="custom-form "><div style="position:relative"><div class="form-group"><input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter your email address / Phone No" style="width:100%;padding:10px;background:rgb(241,241,241);border:none;"><button type="submit" class="btn btn-primary" style="padding:10px;background:rgb(20,88,194);color:white;border:none;position:absolute;right:0;">Notify me</button></div></div></form>',
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
                    'text-align': 'center',
                    'background': 'rgba(20, 88, 194, 0.1)',
                    'color': 'rgba(20, 88, 194)',
                    'padding': '25px'
                },
                activeOnRender: 1
            }
        });
           */
    </script>
    <!-- QueuePage end -->

    <!-- prequeue page start -->
    <script>
        var editor2 = grapesjs.init({
            container: '#gjs2',
            components: `<?php echo $pre_queue_page_tab; ?>`,
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

        editor2.on('load', function() {
            // Hide Layer Manager button for editor2
            hideLayerManager('#gjs2');
                            var transformBlock2 = document.querySelector('.gjs-frame ');

                            
                var transitionBlock2 = document.querySelector('#gjs2 .gjs-sm-property__transition');
                 var transformBlock2 = document.querySelector('#gjs2 .gjs-sm-property__transform');
                var iframeDoc = transformBlock2.contentDocument || transformBlock2.contentWindow.document;

                var elementInIframe = iframeDoc.querySelector('#ifw5q');
                elementInIframe.style.pointerEvents = 'none';

                var elementInIframe = iframeDoc.querySelector('#i378h');
                elementInIframe.style.pointerEvents = 'none';

                var elementInIframe = iframeDoc.querySelector('#i4wxz');
                elementInIframe.style.pointerEvents = 'none';

                var elementInIframe = iframeDoc.querySelector('#islrl');
                elementInIframe.style.pointerEvents = 'none';

                var elementInIframe = iframeDoc.querySelector('#ibx7v');
                elementInIframe.style.pointerEvents = 'none';



                console.log(elementInIframe,'transformBlock2 ');

            if (transitionBlock2) {
                transitionBlock2.style.display = 'none'; // Hide the Transition block
            }

            if (transformBlock2) {
                transformBlock2.style.display = 'none'; // Hide the Transform block
            }

            var viewCodeButton2 = document.querySelector('#gjs2 .gjs-pn-btn.fa.fa-code');
            if (viewCodeButton2) {
                viewCodeButton2.style.display = 'none'; // Hide the button
            }
            var openBlocksButton2 = document.querySelector('#gjs2 .gjs-pn-btn.fa.fa-th-large');
            if (openBlocksButton2) {
                openBlocksButton2.click(); // Simulate a click to select the "Open Blocks" button
            }
        });
        // Add additional components
        // editor2.BlockManager.add('custom-section2', {
        //     label: '<div><div><span class="material-symbols-outlined">check_box_outline_blank</span></div><div><span style="font-size:12px; margin-bottom:4px;">Section</span> </div></div>', // Label for your block
        //     content: {
        //         type: 'div',
        //         components: [],
        //         style: {
        //             'min-height': '200px',
        //             'padding': '20px'
        //         }
        //     },

        // });

        // editor2.BlockManager.add('heading2', {
        //     label: '<div><div><img src="{{ asset('asset/img/Heading.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Heading</span> </div></div>',
        //     content: '<h1 class="text-center">Heading Text</h1>',
        // });
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
                    'text-align': 'center',
                    'min-height': '50px',
                    'background': 'rgba(25, 92, 198, 8.1)',
                    'color': 'white',
                    'padding': '25px',
                    'font-weight': '600',
                    'border': '4px solid black'
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

        /* editor2.BlockManager.add('video2', {
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
                    'text-align': 'center',
                    'background': 'rgba(20, 88, 194, 0.1)',
                    'color': 'rgba(20, 88, 194)',
                    'padding': '25px'
                },
                activeOnRender: 1
            }
        });
        editor2.BlockManager.add('divQueueStartTimefield2', {
            label: '<div><div><span class="material-symbols-outlined">schedule</span></div><div><span style="font-size:12px; margin-bottom:4px;">Queue Start Time</span> </div></div>',
            content: '<div class="editable-div" contenteditable="true"><span style="color:rgb(20,88,194);font-weight:600;font-size:15px">1 hour : 24 minutes : 23 seconds</span></div>'
        });
        // editor2.BlockManager.add('divSystemTimefield2', {
        //     label: '<div><div><img src="{{ asset('asset/img/System-time.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">System Time</span> </div></div>',
        //     content: '<div class="editable-div" contenteditable="true">System Time: <span>5 minutes</span></div>'
        // });
        editor2.BlockManager.add('form2', {
            label: '<div><div><span class="material-symbols-outlined">mail</span></div><div><span style="font-size:12px; margin-bottom:4px;">Notify User</span> </div></div>',
            content: '<form class="custom-form "><div style="position:relative"><div class="form-group"><input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="access code" style="width:100%;padding:10px;background:rgb(241,241,241);border:none;"><button type="submit" class="btn btn-primary" style="padding:10px;background:rgb(20,88,194);color:white;border:none;position:absolute;right:0;font-size:17px;">Notify me when the queue start</button></div></div></form>',
        });
        */
    </script>
    <!-- PrequeuePage end -->

    <!-- post queue page start -->
    <script>
        var editor3 = grapesjs.init({
            container: '#gjs3',
            components: `<?php echo $postqueue_page_tab; ?>`,
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

        editor3.on('load', function() {
            // Hide Layer Manager button for editor3
            hideLayerManager('#gjs3');
                        var transformBlock2 = document.querySelector('.gjs-frame ');

                        var iframeDoc = transformBlock2.contentDocument || transformBlock2.contentWindow.document;
                        var transitionBlock3 = document.querySelector('#gjs3 .gjs-sm-property__transition');
                        var transformBlock3 = document.querySelector('#gjs3 .gjs-sm-property__transform');

            var elementInIframe = iframeDoc.querySelector('#ifw5q');
            elementInIframe.style.pointerEvents = 'none';

            var elementInIframe = iframeDoc.querySelector('#i378h');
            elementInIframe.style.pointerEvents = 'none';

            var elementInIframe = iframeDoc.querySelector('#i4wxz');
            elementInIframe.style.pointerEvents = 'none';

            var elementInIframe = iframeDoc.querySelector('#islrl');
            elementInIframe.style.pointerEvents = 'none';

            var elementInIframe = iframeDoc.querySelector('#ibx7v');
            elementInIframe.style.pointerEvents = 'none';



            console.log(elementInIframe,'transformBlock2 ');


            if (transitionBlock3) {
                transitionBlock3.style.display = 'none'; // Hide the Transition block
            }

            if (transformBlock3) {
                transformBlock3.style.display = 'none'; // Hide the Transform block
            }

            var viewCodeButton3 = document.querySelector('#gjs3 .gjs-pn-btn.fa.fa-code');
            if (viewCodeButton3) {
                viewCodeButton3.style.display = 'none'; // Hide the button
            }
            var openBlocksButton3 = document.querySelector('#gjs3 .gjs-pn-btn.fa.fa-th-large');
            if (openBlocksButton3) {
                openBlocksButton3.click(); // Simulate a click to select the "Open Blocks" button
            }
        });
        // Add additional components
        // editor3.BlockManager.add('custom-section2', {
        //     label: '<div><div><span class="material-symbols-outlined">check_box_outline_blank</span></div><div><span style="font-size:12px; margin-bottom:4px;">Section</span> </div></div>', // Label for your block
        //     content: {
        //         type: 'div',
        //         components: [],
        //         style: {
        //             'min-height': '200px',
        //             padding: '20px',
        //         }
        //     },

        // });

        // editor3.BlockManager.add('heading2', {
        //     label: '<div><div><img src="{{ asset('asset/img/Heading.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Heading</span> </div></div>',
        //     content: '<h1 class="text-center">Heading Text</h1>',
        // });
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
                    'text-align': 'center',
                    'min-height': '50px',
                    'background': 'rgba(25, 92, 198, 8.1)',
                    'color': 'white',
                    'padding': '25px',
                    'font-weight': '600',
                    'border': '4px solid black'
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

        /* editor3.BlockManager.add('video2', {
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
                    'text-align': 'center',
                    'background': 'rgba(20, 88, 194, 0.1)',
                    'color': 'rgba(20, 88, 194)',
                    'padding': '25px'
                },
                activeOnRender: 1
            }
        });
        */
        // editor3.BlockManager.add('divSystemTimefield2', {
        //     label: '<div><div><img src="{{ asset('asset/img/System-time.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">System Time</span> </div></div>',
        //     content: '<div class="editable-div" contenteditable="true">System Time: <span>5 minutes</span></div>'
        // });
    </script>
    <!-- PostqueuePage end -->

    <!-- PriorityAccessPage start -->
    <script>
        var editor4 = grapesjs.init({
            container: '#gjs4',
            components: `<?php echo $priority_access_page_tab; ?>`,
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

        editor4.on('load', function() {
            // Hide Layer Manager button for editor4
            hideLayerManager('#gjs4');

            var transitionBlock4 = document.querySelector('#gjs4 .gjs-sm-property__transition');
            var transformBlock4 = document.querySelector('#gjs4 .gjs-sm-property__transform');
            var transformBlock2 = document.querySelector('.gjs-frame ');

            var iframeDoc = transformBlock2.contentDocument || transformBlock2.contentWindow.document;

            var elementInIframe = iframeDoc.querySelector('#ifw5q');
            elementInIframe.style.pointerEvents = 'none';

            var elementInIframe = iframeDoc.querySelector('#i378h');
            elementInIframe.style.pointerEvents = 'none';

            var elementInIframe = iframeDoc.querySelector('#i4wxz');
            elementInIframe.style.pointerEvents = 'none';

            var elementInIframe = iframeDoc.querySelector('#islrl');
            elementInIframe.style.pointerEvents = 'none';

            var elementInIframe = iframeDoc.querySelector('#ibx7v');
            elementInIframe.style.pointerEvents = 'none';



            console.log(elementInIframe,'transformBlock2 ');

            if (transitionBlock4) {
                transitionBlock4.style.display = 'none';
            }

            if (transformBlock4) {
                transformBlock4.style.display = 'none';
            }

            var viewCodeButton4 = document.querySelector('#gjs4 .gjs-pn-btn.fa.fa-code');
            if (viewCodeButton4) {
                viewCodeButton4.style.display = 'none';
            }
            var openBlocksButton4 = document.querySelector('#gjs4 .gjs-pn-btn.fa.fa-th-large');
            if (openBlocksButton4) {
                openBlocksButton4.click();
            }
        });
        // Add additional components
        // editor4.BlockManager.add('custom-section4', {
        //     label: '<div><div><span class="material-symbols-outlined">check_box_outline_blank</span></div><div><span style="font-size:12px; margin-bottom:4px;">Section</span> </div></div>', // Label for your block
        //     content: {
        //         type: 'div',
        //         components: [],
        //         style: {
        //             'min-height': '200px',
        //             'padding': '20px'
        //         }
        //     },

        // });
        // editor4.BlockManager.add('heading4', {
        //     label: '<div><div><img src="{{ asset('asset/img/Heading.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Heading</span> </div></div>',
        //     content: '<h1 class="text-center">Heading Text</h1>',
        // });
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
                    'text-align': 'center',
                    'min-height': '50px',
                    'background': 'rgba(25, 92, 198, 8.1)',
                    'color': 'white',
                    'padding': '25px',
                    'font-weight': '600',
                    'border': '4px solid black'
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

        /* editor4.BlockManager.add('video2', {
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
                     'text-align': 'center',
                     'background': 'rgba(20, 88, 194, 0.1)',
                     'color': 'rgba(20, 88, 194)',
                     'padding': '25px'
                 },
                 activeOnRender: 1
             }
         });
         // editor4.BlockManager.add('divSystemTimefield2', {
         //     label: '<div><div><img src="{{ asset('asset/img/System-time.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">System Time</span> </div></div>',
         //     content: '<div class="editable-div" contenteditable="true">System Time: <span>5 minutes</span></div>'
         // });
         editor4.BlockManager.add('form2', {
             label: '<div><div><img src="{{ asset('asset/img/access-code.png') }}"/></div><div><span style="font-size:12px; margin-bottom:4px;">Access code</span> </div></div>',
             content: '<form class="custom-form "><div style="position:relative"><div class="form-group"><input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="access code" style="width:100%;padding:10px;background:rgb(241,241,241);border:none;"><button type="submit" class="btn btn-primary" style="padding:10px;background:rgb(20,88,194);color:white;border:none;position:absolute;right:0;font-size:17px;">Submit</button></div></div></form>',
         });
         */
    </script>

    <!-- priority end -->

    <script type="text/javascript">
        // function saveEditorsContent() {
        //     var editor1Html = editor1.getHtml();
        //     var editor1Css = editor1.getCss();

        //     var editor2Html = editor2.getHtml();
        //     var editor2Css = editor2.getCss();

        //     var editor3Html = editor3.getHtml();
        //     var editor3Css = editor3.getCss();

        //     var editor4Html = editor4.getHtml();
        //     var editor4Css = editor4.getCss();

        //     var editor1HtmlFormat = '<!DOCTYPE html><html><head><style>' + editor1Css + '</style></head><body>' +
        //         editor1Html + '</body></html>';
        //     var editor2HtmlFormat = '<!DOCTYPE html><html><head><style>' + editor2Css + '</style></head><body>' +
        //         editor2Html + '</body></html>';
        //     var editor3HtmlFormat = '<!DOCTYPE html><html><head><style>' + editor3Css + '</style></head><body>' +
        //         editor3Html + '</body></html>';
        //     var editor4HtmlFormat = '<!DOCTYPE html><html><head><style>' + editor4Css + '</style></head><body>' +
        //         editor4Html + '</body></html>';


        //     var dataToSend = {
        //         queue_page: appendInputFields(editor1Html, editor1Css, "queue_page"),
        //         prequeue_page: appendInputFields(editor2Html, editor2Css, "prequeue_page"),
        //         postqueue_page: appendInputFields(editor3Html, editor3Css, "postqueue_page"),
        //         priority_access_page: appendInputFields(editor4Html, editor4Css, "priority_access_page"),
        //         room_id: "<?php echo $room_id ?? null; ?>",
        //         template_id: "<?php echo $template_id ?? null; ?>"
        //     };
        //     var dataToSend = {
        //         queue_page: appendInputFields(editor1Html, editor1Css, "queue_page"),
        //         prequeue_page: appendInputFields(editor2Html, editor2Css, "prequeue_page"),
        //         postqueue_page: appendInputFields(editor3Html, editor3Css, "postqueue_page"),
        //         priority_access_page: appendInputFields(editor4Html, editor4Css, "priority_access_page"),
        //         room_id: "<?php echo $room_id; ?>",
        //         template_id: "<?php echo $template_id ?? null; ?>",
        //         lang_id: "<?php echo $lang_id; ?>",
        //     };

        //     // Make AJAX request
        //     $.ajax({
        //         url: '{{ url('canvas-save') }}',
        //         type: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}',
        //             'Content-Type': 'application/json'
        //         },
        //         data: JSON.stringify(dataToSend),
        //         // console.log(data);
        //         success: function(response) {
        //             if (response.templateCount < 1) {
        //                 location.href = "<?php echo env('APP_URL') . 'create-queue'; ?>";
        //             } else {
        //                 <?php if(!empty($template_id)) { ?>
        //                 location.href = "<?php echo env('APP_URL') . 'temp-queue-design'; ?>";
        //                 //location.href="https://queuing.walkingdreamz.com/create-queue";
        //                 <?php } else {  ?>
        //                 location.href = "<?php echo env('APP_URL') . 'queue-room-edit'; ?>/<?php echo $room_id ?? null; ?>";
        //                 // location.reload();
        //                 <?php } ?>
        //             }
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Error occurred while saving data:', error);
        //             location.reload();
        //         }
        //     });
        // }
        function saveEditorsContent() {
            var editor1Html = editor1.getHtml();
            var editor1Css = editor1.getCss();

            var editor2Html = editor2.getHtml();
            var editor2Css = editor2.getCss();

            var editor3Html = editor3.getHtml();
            var editor3Css = editor3.getCss();

            var editor4Html = editor4.getHtml();
            var editor4Css = editor4.getCss();

            var dataToSend = {
                queue_page: appendInputFields(editor1Html, editor1Css, "queue_page"),
                prequeue_page: appendInputFields(editor2Html, editor2Css, "prequeue_page"),
                postqueue_page: appendInputFields(editor3Html, editor3Css, "postqueue_page"),
                priority_access_page: appendInputFields(editor4Html, editor4Css, "priority_access_page"),
                room_id: "<?php echo $room_id; ?>",
                template_id: "<?php echo $template_id ?? null; ?>",
                lang_id: "<?php echo $lang_id; ?>",
            };

            // Send AJAX request asynchronously (does not block execution)
            $.ajax({
                url: '{{ url('canvas-save') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                data: JSON.stringify(dataToSend),
                success: function(response) {
                    console.log('Data saved successfully');
                },
                error: function(xhr, status, error) {
                    console.error('Error occurred while saving data:', error);
                }
            });

            // **Redirect Immediately Without Waiting for AJAX Response**
            var redirectUrl = "<?php echo !empty($template_id) ? env('APP_URL') . 'temp-queue-design' : env('APP_URL') . 'queue-room-edit/' . ($room_id ?? null); ?>";
            window.location.href = redirectUrl;
        }

        // function saveEditorsContent() {
        //     var editor1Html = editor1.getHtml();
        //     var editor1Css = editor1.getCss();

        //     var editor2Html = editor2.getHtml();
        //     var editor2Css = editor2.getCss();

        //     var editor3Html = editor3.getHtml();
        //     var editor3Css = editor3.getCss();

        //     var editor4Html = editor4.getHtml();
        //     var editor4Css = editor4.getCss();

        //     var dataToSend = {
        //         queue_page: appendInputFields(editor1Html, editor1Css, "queue_page"),
        //         prequeue_page: appendInputFields(editor2Html, editor2Css, "prequeue_page"),
        //         postqueue_page: appendInputFields(editor3Html, editor3Css, "postqueue_page"),
        //         priority_access_page: appendInputFields(editor4Html, editor4Css, "priority_access_page"),
        //         room_id: "<?php echo $room_id; ?>",
        //         template_id: "<?php echo $template_id ?? null; ?>",
        //         lang_id: "<?php echo $lang_id; ?>",
        //     };

        //     // Convert data to a JSON string
        //     var jsonData = JSON.stringify(dataToSend);

        //     // **Use navigator.sendBeacon() for better performance**
        //     var url = '{{ url('canvas-save') }}';
        //     var blob = new Blob([jsonData], {
        //         type: 'application/json'
        //     });

        //     // Send data in the background without blocking the redirect
        //     navigator.sendBeacon(url, blob);

        //     // **Redirect Immediately**
        //     var redirectUrl = "<?php echo !empty($template_id) ? env('APP_URL') . 'temp-queue-design' : env('APP_URL') . 'queue-room-edit/' . ($room_id ?? null); ?>";
        //     window.location.href = redirectUrl;
        // }



        // function injectScript() {
        //   var script = document.createElement('script');
        //   script.src = 'https://queuing.walkingdreamz.com/swap-temp/js-test/ext.js';
        //   script.type = 'text/javascript';
        //   document.body.appendChild(script);
        //   console.log('External script injected!');
        // }


        function appendInputFields(html, css, form_type) {
            var this_url = "<?php echo env('APP_URL'); ?>" + "inline-edit-forms";
            var this_form_type = form_type;
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            var form = tempDiv.querySelector('form.example');
            if (form) {
                <?php if(!empty($room_id)) { ?>
                form.innerHTML += '<input type="hidden" name="room_id" value="<?php echo $room_id; ?>"  >';
                <?php }
            if(!empty($template_id)) { ?>
                form.innerHTML += '<input type="hidden" name="template_id" value="<?php echo $template_id; ?>">';
                <?php } ?>
                form.action = "";
            }

            /** Append the developer script | start */
            var script = document.createElement('script');
            // script.src = "https://queuing.walkingdreamz.com/swap-temp/js-test/ext.js";
            script.src = "<?php echo $script; ?>";
            script.type = "text/javascript";

            // Set the additional data attributes
            script.setAttribute('data-intercept-domain', '<?php echo $data_intercept_domain; ?>');
            script.setAttribute('data-intercept', '<?php echo $data_intercept; ?>');
            script.setAttribute('data-c', '<?php echo $data_c; ?>');
            script.setAttribute('data-call', "1");

            let scriptDataForJs = '<?php echo $scriptDataForJs; ?>';
            // Split the string into individual attributes
            var attributes = scriptDataForJs.split(' ');
            // Loop through each attribute and set it on the script element
            for (var i = 0; i < attributes.length; i++) {
                // Split each attribute into name and value
                var parts = attributes[i].split('=');
                if (parts.length === 2) {
                    // Remove any quotes around the value
                    var attributeName = parts[0].trim();
                    var attributeValue = parts[1].replace(/"/g, '').tri
                    script.setAttribute(attributeName, attributeValue); // Set the attribute
                }
            }

            tempDiv.appendChild(script);
            /** Append the developer script | end */
            // // Create and append the <script> tag for the external script
            // var script = document.createElement('script');
            // script.src = "<?php //echo url('swap-temp/js-test/ext.js');
            ?>"; // Set the script source
            // script.type = "text/javascript"; // Set the type attribute (optional)

            // // Append the script to the tempDiv
            // tempDiv.appendChild(script);
            return '<!DOCTYPE html><html><head><style>' + css + '</style></head><body>' + tempDiv.innerHTML +
                '</body></html>';
        }
        document.getElementById('SaveBtn').addEventListener('click', function() {
            saveEditorsContent();
        });

        $(".Canelandbackbtn").click(function() {
            // var roomId = <?php //echo $room_id;
            ?>
            // Pichhle page par redirect karna
            // location.href="<?php //echo env("APP_URL")."stats-edit";
            ?>/<?php //echo $room_id ?? null;
            ?>";
            <?php if(!empty($room_id)) { ?>
            location.href = "<?php echo env('APP_URL') . 'queue-room-view'; ?>";
            <?php  } else { ?>
            location.href = "<?php echo env('APP_URL') . 'temp-queue-design'; ?>";
            <?php } ?>
        });
    </script>
    <!-- PriorityAccessPage end -->
@endsection
