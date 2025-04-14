@php
$currentPath = Request::path();
@endphp
<style>
    
</style>
<div id="sidebar">
    <div class="d-flex flex-column">

        <div class="profile">
            <a href="<?php echo env('APP_URL')?>"><img src="{{ asset('asset/img/Logo=with-text.png') }}" alt="" class="img-fluid"></a>
        </div>

        <nav id="navbar" class="nav-menu navbar">
            <ul class="navbar-nav">
                <li>
                    {{-- <a href="{{ url('dashboard') }}" class="nav-link scrollto {{ $currentPath == 'dashboard' ? 'active' : '' }} NavHome">
                        <span class="material-symbols-outlined navIcons">home</span>
                        <span>Home</span>
                    </a> --}}
                </li>
                <li>
                    <a href="{{ url('queue-room-view') }}" class="nav-link scrollto {{ $currentPath == 'queue-room-view' ? 'active' : '' }} NavQueueRoom">
                        <span class="material-symbols-outlined navIcons">chair</span>
                        <span>Queue Room</span>
                    </a>
                </li>
                <li>
                    <a href="{{ url('stats-room-view') }}" class="nav-link scrollto {{ $currentPath == 'stats-room-view' ? 'active' : '' }} NavStats">
                        <span class="material-symbols-outlined navIcons">trending_up</span>
                        <span>Stats</span>
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <!-- <a href="#" class="nav-link dropdown-toggle scrollto  {{ $currentPath == 'temp-manage' ? 'active' : '' }} NavTemplateManagement"  data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="material-symbols-outlined navIcons">bookmark</span>
                        <span class='tempmargin'>Template Management</span>
                    </a> -->
                    <a class="nav-link dropdown-toggle scrollto NavTemplateManagement {{ ($currentPath == 'temp-queue-design' || $currentPath == 'in-out-rules' || $currentPath == 'email-notice') ? 'active show' : '' }}" 
                        href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="material-symbols-outlined navIcons">bookmark</span>
                        <span class='tempmargin'>Template Management</span>
                    </a>

                    <ul class="dropdown-menu border-0 {{ ($currentPath == 'temp-queue-design' || $currentPath == 'in-out-rules' || $currentPath == 'email-notice') ? 'show' : '' }}">
                        <li>
                            <a href="{{ url('temp-queue-design') }}" class="nav-link scrollto {{ $currentPath == 'temp-queue-design' ? 'active' : '' }} NavQueueDesign ps-5">
                                <span>Queue Design</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('in-out-rules') }}" class="nav-link scrollto {{ $currentPath == 'in-out-rules' ? 'active' : '' }} NavInOutRules ps-5">
                                <span>In / Out Rules</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('email-notice') }}" class="nav-link scrollto {{ $currentPath == 'email-notice' ? 'active' : '' }} NavSMSEmailNotice ps-5">
                                <span>SMS / Email Notice</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li>
                    <a href="{{ url('developer') }}" class="nav-link scrollto {{ $currentPath == 'developer' ? 'active' : '' }} NavDeveloper">
                        <span class="material-symbols-outlined navIcons">code</span>
                        <span>Developer</span>
                    </a>
                </li>
            </ul>
        </nav><!-- .nav-menu -->
    </div>
</div>
