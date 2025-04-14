@extends('common.layouts')
@section('content')
@include('common.sidebar')
@include('common.header')

<link rel="stylesheet" href="{{ asset('asset/css/email.css') }}">
<main id="main" class="bgmain">
    <!-- =======  Section ======= -->
    <section class="SectionPadding">
        <div class="container">
            <div class="row mb-3">
                <div class="col-xl-12 col-md-12 d-flex queueDesignicon">
                    <nav aria-label="breadcrumb" class="QueueBreadCrumb emailTempQueueRoom">
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
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- Tabs Section -->
            <ul class="nav nav-tabs" id="templateTabs" role="tablist">
                <li class="nav-item tabss" role="presentation" data-tab="sms">
                    <a class="nav-link {{ request()->get('tab') == 'sms' || !request()->has('tab') ? 'active' : '' }}" id="sms-tab" data-bs-toggle="tab" href="#sms" role="tab" aria-controls="sms" aria-selected="true">SMS</a>
                </li>
                <li class="nav-item tabss" role="presentation" data-tab="email">
                    <a class="nav-link {{ request()->get('tab') == 'email' ? 'active' : '' }}" id="email-tab" data-bs-toggle="tab" href="#email" role="tab" aria-controls="email" aria-selected="false">Email</a>
                </li>
            </ul>

            <div class="tab-content" id="templateTabsContent">
                <!-- SMS Table Tab -->
                <div class="tab-pane fade {{ request()->get('tab') == 'sms' || !request()->has('tab') ? 'show active' : '' }}" id="sms" role="tabpanel" aria-labelledby="sms-tab">
                    <div class="row mt-3">
                        <div class="col-xl-12 col-md-12 col-sm-12">
                            <div class="allcard">
                                <div class="p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0 spacetable align-middle" id="SMSTable">
                                            <thead class="table bgtable">
                                                <tr>
                                                    <th class="border-0 py-0 align-middle">
                                                        <h6 class="queueth mb-0 p-0">SMS</h6>
                                                    </th>
                                                    <th class="border-0 py-0 align-middle">
                                                        <h6 class="queueth mb-0 p-0">Used by</h6>
                                                    </th>
                                                    <th class="border-0 py-0 align-middle">
                                                        <h6 class="queueth mb-0 p-0">Last Modified</h6>
                                                    </th>
                                                    <th class="border-0 py-0 align-middle">
                                                        <h6 class="queueth mb-0 p-0">Status</h6>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($smsTemplates as $template)
                                                @if($template->status == 1)
                                                <tr>
                                                    <td class="border-0 ">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-5 col-sm-5 col-5">
                                                                <h6 class="mt-0 mb-0 textdata">{{ (strlen($template->sms_template_name) > 18) ? substr($template->sms_template_name, 0, 18) . '...' : $template->sms_template_name }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="border-0">
                                                        @if($template->queue_room_icon)
                                                            <img src="{{ asset('images/' . $template->queue_room_icon) }}" class="me-2 quesingIMG" alt="Image Alt Text" width="50" height="50">
                                                        @else
                                                            <img src="{{ asset('asset/img/profile.png') }}" alt="Default Image" class="me-2 quesingIMG" width="50" height="50">
                                                        @endif
                                                        <?php 
                                                            $originalString = $template->queue_room_name;
                                                            $truncatedString = strlen($originalString) > 18 ? substr($originalString, 0, 18) . '...' : $originalString;
                                                            echo $truncatedString;
                                                        ?>
                                                        <br><br>
                                                    </td>
                                                    <td class="border-0 ">
                                                        <div>
                                                            <h6 class="mt-0 mb-0 textdata mb-2">{{ $template->company_person_name }}</h6>
                                                            @if($template->updated_at)
                                                                <?php $updatedAt = new DateTime($template->updated_at); ?>
                                                                <small class="text-muted">{{ $updatedAt->format('d F Y \a\t h:i A') }}</small>
                                                            @else
                                                                <small class="text-muted">No update time available</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    @if(Auth::user()->role == 1 OR $template->permission == 2)
                                                    <td class="border-0 ">
                                                        <a href="{{ url('sms-temp-edit/'.$template->id . '?tab=sms') }}" class="text-decoration-none editicon">
                                                            <span class="material-symbols-outlined">add_to_photos</span>
                                                        </a>
                                                        <a href="{{ url('delete-email-notice/'.$template->id . '?tab=sms') }}" class="text-decoration-none editicon" onclick="return confirm('Are you sure you want to delete?');">
                                                            <span class="material-symbols-outlined">delete</span>
                                                        </a>
                                                    </td>
                                                    @else
                                                    <td>-</td>
                                                    @endif
                                                </tr>
                                                @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination for SMS Table -->
                                    @if(count($smsTemplates) > 0)
                                    <div class="pagination-wrapper">
                                          {{ $smsTemplates->appends(['tab' => 'sms'])->links() }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Table Tab -->
                <div class="tab-pane fade {{ request()->get('tab') == 'email' ? 'show active' : '' }}" id="email" role="tabpanel" aria-labelledby="email-tab">
                    <div class="row mt-3">
                        <div class="col-xl-12 col-md-12 col-sm-12">
                            <div class="allcard">
                                <div class="p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0 spacetable align-middle" id="EmailTable">
                                            <thead class="table bgtable">
                                                <tr>
                                                    <th class="border-0 py-0 align-middle">
                                                        <h6 class="queueth mb-0 p-0">Email</h6>
                                                    </th>
                                                    <th class="border-0 py-0 align-middle">
                                                        <h6 class="queueth mb-0 p-0">Used by</h6>
                                                    </th>
                                                    <th class="border-0 py-0 align-middle">
                                                        <h6 class="queueth mb-0 p-0">Last Modified</h6>
                                                    </th>
                                                    <th class="border-0 py-0 align-middle">
                                                        <h6 class="queueth mb-0 p-0">Status</h6>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                                @foreach($emailTemplates as $template)
                                                @if($template->status == 1)
                                                <tr>
                                                    <td class="border-0 ">
                                                        <div class="row align-items-center">
                                                            <div class="col-md-5 col-sm-5 col-5">
                                                                <h6 class="mt-0 mb-0 textdata">{{ (strlen($template->email_template_name) > 18) ? substr($template->email_template_name, 0, 18) . '...' : $template->email_template_name }}</h6>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="border-0">
                                                        @if($template->queue_room_icon)
                                                            <img src="{{ asset('images/' . $template->queue_room_icon) }}" class="me-2 quesingIMG" alt="Image Alt Text" width="50" height="50">
                                                        @else
                                                            <img src="{{ asset('asset/img/profile.png') }}" alt="Default Image" class="me-2 quesingIMG" width="50" height="50">
                                                        @endif
                                                        <?php 
                                                            $originalString = $template->queue_room_name;
                                                            $truncatedString = strlen($originalString) > 18 ? substr($originalString, 0, 18) . '...' : $originalString;
                                                            echo $truncatedString;
                                                        ?>
                                                        <br><br>
                                                    </td>
                                                    <td class="border-0 ">
                                                        <div>
                                                            <h6 class="mt-0 mb-0 textdata mb-2">{{ $template->company_person_name }}</h6>
                                                            @if($template->updated_at)
                                                                <?php $updatedAt = new DateTime($template->updated_at); ?>
                                                                <small class="text-muted">{{ $updatedAt->format('d F Y \a\t h:i A') }}</small>
                                                            @else
                                                                <small class="text-muted">No update time available</small>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    @if(Auth::user()->role == 1 OR $template->permission == 2)
                                                    <td class="border-0 ">
                                                        <a href="{{ url('email-temp-edit/'.$template->id . '?tab=email') }}" class="text-decoration-none editicon">
                                                            <span class="material-symbols-outlined">add_to_photos</span>
                                                        </a>
                                                        <a href="{{ url('delete-email-notice/'.$template->id . '?tab=email') }}" class="text-decoration-none editicon" onclick="return confirm('Are you sure you want to delete?');">
                                                            <span class="material-symbols-outlined">delete</span>
                                                        </a>
                                                    </td>
                                                    @else
                                                    <td>-</td>
                                                    @endif
                                                </tr>
                                                @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination for Email Table -->
                                    @if(count($emailTemplates) > 0)
                                    <div class="pagination-wrapper">
                                     
                                      {{ $emailTemplates->appends(['tab' => 'email'])->links() }}

                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>


<script>
$(document).ready(function() {
    // Tab click event ke liye
    $('.tabss').on('click', function() {
        // Har tab ka alag query string value set karein
        var tabValue = $(this).attr('data-tab');
        
        // Current URL ko le kar usmein query string add karein
        var newUrl = window.location.origin + window.location.pathname + "?tab=" + tabValue + "&page=1";
        
        // Nayi URL ke saath page reload karein
        window.location.href = newUrl;
    });
});


    // Restore the tab based on URL parameter on page load
    const activeTab = urlParams.get('tab') || 'sms'; // Default to 'sms' if no tab specified
    const tabToActivate = document.querySelector(`#${activeTab}-tab`);
    
    if (tabToActivate) {
        console.log(`Activating tab: ${activeTab}`);
        const tab = new bootstrap.Tab(tabToActivate);
        tab.show();
    } else {
        console.log('No tab to activate');
    }



</script>
@endsection
