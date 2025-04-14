    @extends('common.layouts')
    @section('content')
    @include('common.sidebar')
    @include('common.header')

    <link rel="stylesheet" href="{{ asset('asset/css/queueRoomCreate.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/queueDesign.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css">
    <main id="main" class="bgmain">
        <section class="SectionPadding">
            <?php
            if ($queueRoomTemplate['permission'] == 1) {
                $disable = 'disabled';
            } else {
                $disable = '';
            }

            ?>
            <!-- =======  Section ======= -->
            <div class="container">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <nav aria-label="breadcrumb" class="QueueBreadCrumb queueDesignQueueRoom">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item Homebreadcrumb"><a href="{{ url('dashboard') }}"><i class="fa fa-home" aria-hidden="true"></i></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('temp-manage') }}">Template Management</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('in-out-rules') }}">In /Out Rules</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{ $queueRoomTemplate['template_name'] }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <form class="px-2" method="POST" action="{{ route('update_queue_room_template', ['id' => $queueRoomTemplate['id']]) }}" id="form1" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="card card-body">
                        <div class="row m-0">
                            <div class="col-md-12 ps-0 pt-2 pb-5">
                                <div class="LeftGreenborder ps-4">
                                    <input type="hidden" id="condition_json" name="temp_id" value="{{ $queueRoomTemplate['id'] }}">
                                    <h5 class="FormHeading"><b>Who will redirect to the queue room?</b></h5>
                                    <div class="mb-3">
                                        <p class="FormPara">Template name</p>

                                        <input type="text" class="form-control FormInputBox" id="template_name" name="template_name" placeholder="Supreme SS 2024" value="{{ $queueRoomTemplate['template_name'] }}" <?php echo $disable; ?>>
                                        @if ($errors->has('template_name'))
                                        <span class="text-danger">{{ $errors->first('template_name') }}</span>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <p class="FormPara">Input URL :</p>





                                        <input type="link" class="form-control FormInputBox" id="input_url" name="input_url" placeholder="https://supreme.com/newrelease/apr2024" value="{{ $queueRoomTemplate['input_url'] }}" <?php echo $disable; ?>>
                                        @if ($errors->has('input_url'))
                                        <span class="text-danger">{{ $errors->first('input_url') }}</span>
                                        @endif
                                        <div class="card mb-4 mt-4">
                                            <div class="card-body statsCard infoCard">
                                                <div class="align-items-start d-flex">
                                                    <span class="material-symbols-outlined QueueMasterIcon staticon">
                                                        info
                                                    </span>
                                                    <div>
                                                        <h6 class="ms-2 mb-0 QueueMasterheading stattext">Informational message</h6>
                                                        <p class="mb-0 pt-1 pb-0 ms-2 mt-0"><small>(Please enter a URL-encoded input beginning with either http:// or https://) <br>
                                                                (When you change the input URL, the developer script will also change accordingly.)</small></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center advanceSettngToggle mb-2">
                                        <p class="FormPara mb-0">Advance settings</p>
                                        <div class="form-checks form-switch">
                                            <input class="form-check-input" type="checkbox" id="AdvanceSettingCheckBox" name="AdvanceSettingCheckBox" value="1" {{ $queueRoomTemplate['is_advance_setting'] == '1' ? 'checked' : '' }} <?php echo $disable; ?>>
                                        </div>
                                        @if ($errors->has('AdvanceSettingCheckBox'))
                                        <span class="text-danger">{{ $errors->first('AdvanceSettingCheckBox') }}</span>
                                        @endif
                                        @if ($errors->has('advancedata'))
                                        <span class="text-danger">{{ $errors->first('advancedata') }}</span>
                                        @endif
                                    </div>
                                    <div class="AdvanceSettingBox" style="display: {{ $queueRoomTemplate['is_advance_setting'] == '1' ? 'block' : 'none' }}">
                                        <p class="FormPara">On top of the above setting, visitors would go to the queuing room if</p>

                                        <input type="hidden" name="advancedata" id="advancedata" value="{{ json_encode($queueRoomTemplate['advance_setting_rules']) }}">

                                        <div class="table-responsive">
                                            <table id="AdvanceSettingTable">
                                                <tbody>
                                                    <?php $c = 0;
                                                    $rc = 0;
                                                    $advance_setting = json_decode($queueRoomTemplate['advance_setting_rules']);

                                                    if (isset($advance_setting)) {
                                                    ?>

                                                        @foreach ($advance_setting as $index => $rule)
                                                        <?php $c++;
                                                        $rc++; ?>
                                                        <tr id="row_<?php echo $c; ?>">
                                                            <td>
                                                                @if($index > 0)
                                                                <select class="form-select form-control FormInputBox" aria-label="Default select example" name="advance[operator][]" <?php echo $disable; ?>>
                                                                    <option value="AND" {{ $rule->operator == 'AND' ? 'selected' : '' }}>AND</option>
                                                                    <option value="OR" {{ $rule->operator == 'OR' ? 'selected' : '' }}>OR</option>
                                                                </select>
                                                                @else
                                                                <input type="hidden" name="advance[operator][]" id="advancedata" value=<?php echo null; ?>>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <select class="form-select form-control FormInputBox" aria-label="Default select example" name="advance[condition_place][]" <?php echo $disable; ?>>
                                                                    <option value="HOST_NAME" {{ $rule->condition_place == 'HOST_NAME' ? 'selected' : '' }}>HOST NAME</option>
                                                                    <option value="PAGE_PATH" {{ $rule->condition_place == 'PAGE_PATH' ? 'selected' : '' }}>PAGE PATH</option>
                                                                    <option value="PAGE_URL" {{ $rule->condition_place == 'PAGE_URL' ? 'selected' : '' }}>PAGE URL</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-select form-control FormInputBox" name="advance[condition][]" <?php echo $disable; ?>>
                                                                    <option value="CONTAINS" {{ $rule->condition == 'CONTAINS' ? 'selected' : '' }}>CONTAINS</option>
                                                                    <option value="DOES_NOT_CONTAIN" {{ $rule->condition == 'DOES_NOT_CONTAIN' ? 'selected' : '' }}>DOES NOT CONTAIN</option>
                                                                    <option value="EQUALS" {{ $rule->condition == 'EQUALS' ? 'selected' : '' }}>EQUALS</option>
                                                                    <option value="DOES_NOT_EQUAL" {{ $rule->condition == 'DOES_NOT_EQUAL' ? 'selected' : '' }}>DOES NOT EQUAL</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control FormInputBox" id="roomname" placeholder="registration" name="advance[value][]" value="{{ $rule->value }}" <?php echo $disable; ?>>
                                                            </td>
                                                            <td>
                                                                <button class="DeleteTableRow" type="button" <?php echo $disable; ?>>
                                                                    <span class="material-symbols-outlined">delete</span>
                                                                </button>
                                                            </td>
                                                            <!-- <td>  -->
                                                        </tr>
                                                        @endforeach
                                                    <?php } else {
                                                    ?>
                                                        <tr id="row_0">
                                                            <td></td>
                                                            <td>
                                                                <select class="form-select form-control FormInputBox" aria-label="Default select example" name="advance[condition_place][]">
                                                                    <option value="HOST_NAME">HOST NAME</option>
                                                                    <option value="PAGE_PATH">PAGE PATH</option>
                                                                    <option value="PAGE_URL">PAGE URL</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <select class="form-select form-control FormInputBox" name="advance[condition][]">
                                                                    <option value="CONTAINS">CONTAINS</option>
                                                                    <option value="DOES NOT CONTAIN">DOES NOT CONTAIN</option>
                                                                    <option value="EQUALS">EQUALS</option>
                                                                    <option value="DOES_NOT_EQUAL">DOES NOT EQUAL</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="text" class="form-control FormInputBox" id="roomname" placeholder="registration" name="advance[value][]" value="">
                                                            </td>
                                                            <!-- <td>  -->
                                                        </tr>
                                                    <?php } ?>
                                                    <input type="hidden" name="rowcount" id="rowcount" value="<?php echo $rc; ?>">

                                                    <tr class="additionalPlustr">
                                                        <td><button id="addButton" class="AddTableRow" type="button"><span class="material-symbols-outlined">add</span></button></td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 d-flex text-end justify-content-end">
                                <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center saveBtn">Save <span class="material-symbols-outlined ps-2">
                                        save
                                    </span></button>
                            </div>
                        </div>
                    </div>
                </form>




            </div>
        </section>
    </main>
    <script src="{{ asset('asset/js/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('asset/js/queueRoom1.js') }}" type="text/javascript"></script>
    <script src="{{ asset('asset/js/moment.min.js') }}"></script>
    <script src="{{ asset('asset/js/moment-timezone-with-data.min.js') }}"></script>
    <script src="{{ asset('asset/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('AdvanceSettingTable');

            // Event delegation for delete button
            table.addEventListener('click', function(e) {
                if (e.target.closest('.DeleteTableRow')) {
                    const row = e.target.closest('tr');
                    row.remove();
                    updateRowCount();
                }
            });

            const addButton = document.getElementById('addButton');
            addButton.addEventListener('click', insertNewRow);

            function insertNewRow() {
                const newRow = table.insertRow(table.rows.length - 1);
                newRow.innerHTML = `
                                    <td>
                                        <select class="form-select form-control FormInputBox" aria-label="Default select example" name="advance[operator][]">
                                            <option value="AND">AND</option>
                                            <option value="OR">OR</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-control" name="advance[condition_place][]">
                                            <option value="HOST_NAME">HOST NAME</option>
                                            <option value="PAGE_PATH">PAGE PATH</option>
                                            <option value="PAGE_URL">PAGE URL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-control" name="advance[condition][]">
                                            <option value="CONTAINS">CONTAINS</option>
                                            <option value="DOES NOT CONTAIN">DOES NOT CONTAIN</option>
                                            <option value="EQUALS">EQUALS</option>
                                            <option value="DOES_NOT_EQUAL">DOES NOT EQUAL</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="Value" name="advance[value][]">
                                    </td>
                                    <td>
                                        <button class="DeleteTableRow" type="button"><span class="material-symbols-outlined">delete</span></button>
                                    </td>
                                `;

                updateRowCount();
            }

            function updateRowCount() {
                const rows = table.querySelectorAll('tbody tr:not(.additionalPlustr)');
                const rowCount = rows.length - 1;
                document.getElementById('rowcount').value = rowCount;
                managePlusAndDeleteButtonVisibility();
            }

            function managePlusAndDeleteButtonVisibility() {
                const deleteButtons = table.querySelectorAll('.DeleteTableRow');
                const addButtons = table.querySelectorAll('.AddTableRow');

                deleteButtons.forEach(button => button.style.display = deleteButtons.length <= 1 ? 'none' : '');

                addButtons.forEach(button => button.style.display = 'none');
                if (addButtons.length > 0) {
                    addButtons[addButtons.length - 1].style.display = 'inline-block';
                }
            }

            updateRowCount();
        });
    </script>


    @endsection