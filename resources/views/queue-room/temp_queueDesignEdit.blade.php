@extends('common.layouts')
@section('content')
    @include('common.sidebar')
    @include('common.header')

    <?php
    $accessValue = request()->query('value');
    ?>

    <link rel="stylesheet" href="{{ asset('asset/css/queueRoomCreate.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('asset/css/queueDesign.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/css/pikaday.min.css">
    <main id="main" class="bgmain">
        <section class="SectionPadding">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <nav aria-label="breadcrumb" class="QueueBreadCrumb queueDesignQueueRoom">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item Homebreadcrumb"><a href="{{ url('dashboard') }}"><i
                                            class="fa fa-home" aria-hidden="true"></i></a>
                                </li>
                                <li class="breadcrumb-item"><a href="{{ url('temp-manage') }}">Template Management</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('temp-queue-design') }}">Queue Design</a></li>
                                <li class="breadcrumb-item active" aria-current="page"><a
                                        href="#">{{ $queueRoomTemplate[0]['template_name'] }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>

                @if ($accessValue != 1)
                    <form method="POST" action="{{ route('queueDesignUpdate', ['id' => $queueRoomTemplate[0]['id']]) }}">
                        @csrf
                        <div class="card card-body">
                            <div class="row m-0">
                                <div class="col-md-12 ps-0 pt-2 pb-5">
                                    <div class="LeftGreenborder ps-4">
                                        <h5 class="FormHeading"><b>{{ $queueRoomTemplate[0]['template_name'] }}</b></h5>

                                        <div class="form-group">
                                            <label for="SelectTemplate" class="FormInputBoxLabel">Template name</label>
                                            <input type="text" class="form-control FormInputBox" placeholder="2024"
                                                name="QueueRoomDesignTemplate_name"
                                                value="{{ $queueRoomTemplate[0]['template_name'] }}">
                                            @if ($errors->has('QueueRoomDesignTemplate_name'))
                                                <span
                                                    class="text-danger">{{ $errors->first('QueueRoomDesignTemplate_name') }}</span>
                                            @endif
                                        </div>

                                        <p class="FormPara pt-3 languagePara">What language do you want the queue room to
                                            display?</p>
                                        <p>your selected languages:
                                            @foreach ($matchedLanguages as $language)
                                                <div class="row mb-3">
                                                    <div class="col-md-5">
                                                        <input type="text" class="form-control selected_input"
                                                            value="{{ $language['name'] }} ({{ $language['native'] }})"
                                                            disabled>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <br>
                                            ( Note: The languages displayed are your selected languages. If you update
                                            them, the old languages will be replaced by the new ones.)
                                        </p>
                                        <div id="TempdynamicSelects">
                                            <!-- Dynamic select lists will be added here -->
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-5">
                                                <select id="TempmainSelect" class="form-select" name="setDefault"
                                                    aria-label="Default select example">
                                                    <option selected>Please select...</option>
                                                    @php
                                                        $sortedLanguages = $languages->sortBy('name');
                                                    @endphp
                                                    @foreach ($sortedLanguages as $language)
                                                        <option value="{{ $language->code }}"
                                                            {{ isset($queueRoomTemplate[0]['default_language']) && $queueRoomTemplate[0]['default_language'] == $language->code ? 'selected' : '' }}>
                                                            {{ $language->name . ' (' . $language->native . ')' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-7">
                                            </div>
                                        </div>
                                        <input type="hidden" id="jsonlang" name="queue_language"
                                            value="{{ $queueRoomTemplate[0]['languages'] }}">

                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <button type="button"
                                                    onclick="window.location.href='{{ env('APP_URL') . 'edit-template-inline-room/' . $queueRoomTemplate[0]['id'] . '/' . ($matchedLanguages[0]['code'] ?? '') . $roomId }}'"
                                                    class="btn editBtn" id="DesignEditBtn">Edit <i
                                                        class="fa fa-pencil-square-o ps-1" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($errors->has('queue_language'))
                                        <span class="text-danger">{{ $errors->first('queue_language') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 d-flex text-end justify-content-end">
                                    <button type="submit"
                                        class="btn btn-primary d-flex align-items-center justify-content-center saveBtn">Save
                                        <span class="material-symbols-outlined ps-2">
                                            save
                                        </span></button>
                                </div>
                            </div>

                        </div>


                    </form>
                @else
                    <form method="POST" action="{{ route('queueDesignUpdate', ['id' => $queueRoomTemplate[0]['id']]) }}">
                        @csrf
                        <div class="card card-body">
                            <div class="row m-0">
                                <div class="col-md-12 ps-0 pt-2 pb-5">
                                    <div class="LeftGreenborder ps-4">
                                        <h5 class="FormHeading"><b>{{ $queueRoomTemplate[0]['template_name'] }}</b></h5>

                                        <div class="form-group">
                                            <label for="SelectTemplate" class="FormInputBoxLabel">Template name</label>
                                            <input type="text" class="form-control FormInputBox" placeholder="2024"
                                                name="QueueRoomDesignTemplate_name"
                                                value="{{ $queueRoomTemplate[0]['template_name'] }}" disabled>
                                            @if ($errors->has('QueueRoomDesignTemplate_name'))
                                                <span
                                                    class="text-danger">{{ $errors->first('QueueRoomDesignTemplate_name') }}</span>
                                            @endif
                                        </div>
                                        <p class="FormPara pt-3 languagePara">What language do you want the queue room to
                                            display?</p>
                                        <p>your selected langueges :-
                                            <?php
                                            foreach ($matchedLanguages as $language) {
                                                echo '<div class="row mb-3">';
                                                echo '<div class="col-md-5">';
                                                echo '<input type="text" class="form-control selected_input" data-value="' . $language['code'] . '" value="' . $language['name'] . ' (' . $language['native'] . ')" disabled>';
                                                echo '</div>';
                                                echo '<div class="col-md-7">';
                                                echo '<div class="d-flex align-items-center">';
                                                echo '</div>';
                                                echo '</div>';
                                                echo '</div>';
                                            }
                                            
                                            ?>
                                            <br>
                                            ( Note : The languages displayed are your selected languages. If you update
                                            them, the old languages will be replaced by the new ones.)
                                        </p>

                                        <div id="TempdynamicSelects">
                                            <!-- Dynamic select lists will be added here -->
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-5">
                                                <select id="TempmainSelect" class="form-select"
                                                    aria-label="Default select example" disabled>
                                                    <option selected>Please select...</option>
                                                    @php
                                                        $sortedLanguages = $languages->sortBy('name');
                                                    @endphp
                                                    @foreach ($sortedLanguages as $language)
                                                        <option value="{{ $language->code }}">
                                                            {{ $language->name . ' (' . $language->native . ')' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-7">
                                            </div>
                                        </div>
                                        <input type="hidden" id="jsonlang" name="queue_language"
                                            value="<?php echo $queueRoomTemplate[0]['languages']; ?>" disabled>

                                    </div>
                                    @if ($errors->has('queue_language'))
                                        <span class="text-danger">{{ $errors->first('queue_language') }}</span>
                                    @endif
                                </div>
                            </div>

                        </div>


                    </form>
                @endif
            </div>
        </section>
    </main>

    <script src="{{ asset('asset/js/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('asset/js/queueedit.js') }}" type="text/javascript"></script>
    <script src="{{ asset('asset/js/moment.min.js') }}"></script>
    <script src="{{ asset('asset/js/moment-timezone-with-data.min.js') }}"></script>
    <script src="{{ asset('asset/js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.8.0/pikaday.min.js"></script>

@endsection
