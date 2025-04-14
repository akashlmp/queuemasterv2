<?php

use App\Models\PermissionAccess;
use App\Models\admin\SubscriptionPlan;

$user_id = auth()->user()->id;
$user_plan_id = auth()->user()->subscription_plan_id;
$staffAccessManagement = SubscriptionPlan::where('id', $user_plan_id)->value('staff_access_management');

$common_module_id = PermissionAccess::where('user_id', $user_id)->value('common_module_id');
$moduleIds_permission = json_decode($common_module_id);
?>
<header id="header">
  <div class="d-flex justify-content-between align-items-center">
    <div></div>
    <div class="sideContent d-flex align-items-center justify-content-between ">
      <div class="d-flex flex-column flex-sm-row align-items-center">
        @if(Auth::user()->role == 1)
        <div class='sideposition'>
                    <a href="{{ url('subscription') }}" class="text-dark text-decoration-none">
                    <div class="d-flex align-items-center me-3">

                    <span class="material-symbols-outlined">planner_review</span>
                      <span>Usage and plan</span>

                    </div>
                    </a>
        </div>
        @endif

        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <div class="nav-item dropdown">
              <div class="d-flex align-items-center me-5 headershow" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <!-- <img src="{{ asset('asset/img/profile.png') }}" alt="Profile Icon" class="profile-icon me-1"> -->
                <span class="material-symbols-outlined me-1">account_circle</span>
                <span class="me-1">
                  @if(Auth::check())
                  @if(Auth::user()->company_person_name !== null)
                  {{ Auth::user()->company_person_name }}
                  @else
                  {{ Auth::user()->email }}
                  @endif
                  @endif
                </span>
                <button class="btn btn-link me-1">
                  <span class="material-symbols-outlined dropbutton">
                    expand_more
                  </span>
                </button>
              </div>
              <ul class="dropdown-menu NavDropdown" aria-labelledby="profileDropdown">

                @if(Auth::user()->role == 2 || Auth::user()->role == 3 || Auth::user()->role == 4)
                @foreach($moduleIds_permission as $module)
                @if($module->module_id == 2 && $module->permission > 0)

                <li>
                  <a class="dropdown-item" href="{{ url('profile') }}">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined pe-2"> person</span>
                      <span>My profile</span>
                    </div>
                  </a>
                </li>

                <!-- <li>
                  <hr class="dropdown-divider">
                </li> -->
                @endif
                @if($module->module_id == 3 && $module->permission > 0 )
                <li>
                  <a class="dropdown-item" href="{{ url('staff-access-manage') }}">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined pe-2">groups</span>
                      <span>Staff access management</span>
                    </div>
                  </a>
                </li>

                <!-- <li>
                  <hr class="dropdown-divider">
                </li> -->
                @endif
                @if($module->module_id == 4 && $module->permission > 0)
                <!-- <li>
                  <div class="dropdown-item" href="#">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined pe-2">description</span>
                      <span>Subscription plan</span>
                    </div>
                  </div>
                </li> -->

                <!-- <li>
                  <hr class="dropdown-divider">
                </li> -->
                @endif
                @endforeach
                @elseif(Auth::check() && Auth::user()->role == 1)
                <li>
                  <a class="dropdown-item" href="{{ url('profile') }}">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined pe-2"> person</span>
                      <span>My profile</span>
                    </div>
                  </a>
                </li>

                <li>
                  <!-- <hr class="dropdown-divider"> -->
                </li>
                @if($staffAccessManagement == 1)
                <li>
                  <a class="dropdown-item" href="{{ url('staff-access-manage') }}">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined pe-2">groups</span>
                      <span>Staff access management</span>
                    </div>
                  </a>
                </li>

                 <li>
                  <!-- <hr class="dropdown-divider"> -->
                </li>
                @endif
                <!-- <li>
                  <div class="dropdown-item" href="#">
                    <div class="d-flex align-items-center">
                      <span class="material-symbols-outlined pe-2">description</span>
                      <span>Subscription plan</span>
                    </div>
                  </div>
                </li> -->

                <li>
                  <hr class="dropdown-divider">
                </li>
                @endif
                <li>
                  <a class="dropdown-item" href="#">
                    <div class="d-flex align-items-center">
                      <form action="{{ route('logout') }}" method="POST" class="w-100">
                        @csrf
                        <button type="submit" class="logoutbutton pe-2 w-100 d-flex align-center ps-0 pt-0 pb-0">
                          <span class="material-symbols-outlined logoutbutton pe-2">logout</span>
                          <span>Log out</span>
                        </button>
                      </form>
                    </div>
                  </a>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
      <i class="bi bi-list mobile-nav-toggle d-xl-none">
        <span class="material-symbols-outlined">
          menu
        </span>
      </i>
    </div>
  </div>
</header>
