@extends('common.layouts')
@section('content')
@extends('common.sidebar')
@extends('common.header')
<?php

use App\Models\PermissionAccess;
use App\Models\admin\SubscriptionPlan;

$user_plan_id = auth()->user()->subscription_plan_id;
$maximum_sub_accounts = SubscriptionPlan::where('id', $user_plan_id)->value('maximum_sub_accounts');
$user_count = count($users);

$user_id = auth()->user()->id;
$common_module_id = PermissionAccess::where('user_id', $user_id)->value('common_module_id');
$moduleIds_permission = json_decode($common_module_id);
$queue_room_access = PermissionAccess::where('user_id', $user_id)->value('queue_room_access');
// print_r($queue_room_access);
// exit;
?>
<link rel="stylesheet" href="{{ asset('asset/css/userProfile.css') }}">
<main id="main" class="bgmain">
    <section class="SectionPadding">
        <div class="container">
            <div class="row mb-3">
                <div class="col-xl-12 col-md-12 d-flex userProfileicon">

                    <nav aria-label="breadcrumb" class="QueueBreadCrumb profileBredcrum">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item Homebreadcrumb">
                                <a href="{{ url('dashboard') }}">
                                    <i class="fa fa-home" aria-hidden="true"></i>
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ url('staff-access-manage') }}">Staff Access Management</a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

            <!-- nav tabs -->
            <div class="row mb-3">
                <div class="col-xl-12 col-md-12 ">
                    
                    <ul class="nav nav-pills navborder" >
                        @if(Auth::user()->role == 2 || Auth::user()->role == 3 || Auth::user()->role == 4)
                            @foreach($moduleIds_permission as $module)
                                @if($module->module_id == 2 && $module->permission > 0)
                                    <li class="nav-item">
                                        <a href="{{ url('profile') }}"><button class="nav-link  mynavtabs" id="pills-home-tab" type="button">MY PROFILE</button></a>
                                    </li>
                                @endif
                                @if($module->module_id == 3 && $module->permission > 0)
                                    <li class="nav-item">
                                        <a href="{{ url('staff-access-manage') }}"><button class="nav-link active mynavtabs new" type="button">STAFF ACCESS MANAGEMENT</button></a>
                                    </li>
                                @endif
                                @if($module->module_id == 4 && $module->permission > 0)
                                    <li class="nav-item">
                                        <a href="#!"><button class="nav-link mynavtabs" type="button">SUBSCRIPTION PLAN</button></a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>

                    <div class="" >
                        @if(Session::has('success'))
                            <div class="alert alert-success">
                                {!! Session::get('success') !!}
                            </div>
                        @elseif(Session::has('error'))
                            <div class="alert alert-danger">
                                {!! Session::get('error') !!}
                            </div>
                        @endif
                         <!-- search -->
                         <div class="row d-flex justify-content-between mt-5">
                            <div class="col-md-3 col-xs-12 col-sm-6 mb-3">
                                <div class="form-group has-search">
                                    <span class="fa fa-search form-control-feedback"></span>
                                    <input type="text" class="form-control" placeholder="Search" id="staffAccessInput">
                                </div>
                            </div>
            
                            <div class="col-md-4 col-xs-12 col-sm-6  mb-3 text-end">
                            @if(Auth::user()->role == 1 && $user_count < ($maximum_sub_accounts + 1))
                                 <a href="{{ url('addStaff') }}"> <button class="btn bsb-btn-2xl subbtnbtn submitbtn w-auto px-4 py-0" type="submit">
                                        <div class="d-flex justify-content-center align-items-center subbtnbtnBoxr">
                                        <span class="material-symbols-outlined  plusicon pe-3">
                                            add
                                            </span>
                                        <h6 class="addstaff mt-2 ">Add Staff</h6>
                                            </div>
                                    </button> </a>
                                    @else
                    <div class="alert alert-danger" role="alert">
                        You have exceeded your Adding staff limit as per your subscription plan.
                    </div>
                @endif 
                            </div>
                         </div>


                         <!-- table -->
                         <div class="row">
                            <div class="col-xl-12 col-md-12 col-sm-12">
                                <div class="card allcard">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table mb-0 spacetable align-middle addstafftable" id="staffAccessTable">
                                                <thead class="table bgtable">
                                            <tr>
                                                <th class="border-0 py-0 align-middle"><h6 class="staffname p-0 mb-0">Name</h6></th>
                                                <th class="border-0 py-0 align-middle"><h6 class="staffname p-0 mb-0">Email</h6></th>
                                                <!-- <th class="border-0 py-0 align-middle"><h6 class="staffname p-0 mb-0">Role</h6></th> -->
                                                <th class="border-0 py-0 align-middle"><h6 class="staffname p-0 mb-0">Status</h6></th>
                                                <th class="border-0 py-0 align-middle"><h6 class="staffname p-0 mb-0">Created By</h6></th>
                                                @if(Auth::user()->role == 1)
                                                <th class="border-0 py-0 align-middle"><h6 class="staffname p-0 mb-0">Action</h6></th>
                                                @endif
                                            </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($users as $user)
                                                    <tr>
                                                        <td class="border-0 ">
                                                            
                                                                <h6 class="mt-2 textdata">{{ $user->company_person_name }}</h6>
                                                            
                                                        </td>
                                                        <td class="border-0">
                                                            <h6 class="mt-2  textdata">{{ $user->email }}</h6>
                                                        </td>
                                                        <!-- <td class="border-0 ">
                                                            <h6 class="mt-2  textdata">{{ $user->role_name }}</h6>
                                                        </td> -->
                                                        <td class="border-0">
                                                            @if($user->status == 1)
                                                            <i class="fa fa-circle me-2 text-success" aria-hidden="true"></i> <span class="text-success">Activated</span>
                                                            @else
                                                            <i class="fa fa-circle me-2 text-danger" aria-hidden="true"></i><span class="text-danger">Deactivated</span>
                                                            @endif
                                                        </td>
                                                        <td class="border-0 ">
                                                            <div>
                                                            @if($user->pr_user_id)
                                                                <h6 class="mt-2  mb-0 textdata">{{ $user->createdBy->company_person_name }}</h6>
                                                                <small class=" text-muted">{{ $user->created_at }}</small>
                                                            @else
                                                                <h6 class="mt-2  mb-0 textdata">-</h6>
                                                            @endif
                                                            </div>
                                                        </td>
                                                        <td class="border-0">
                                                            @if(Auth::user()->role == 1 && isset($user) && $user->role != 1)
                                                                <a href="{{ route('editStaff', $user->id) }}" class="btn btn-primary">Edit</a>
                                                            
                                                                    <a href="{{ route('deleteStaff', $user->id) }}" class="btn btn-danger delete-user">Delete</a>
                                                                    <a href="{{ route('activateDeactivate', $user->id) }}" class="btn btn-{{ $user->status == 1 ? 'info' : 'success' }}">
                                                                        {{ $user->status == 1 ? 'Deactivate' : 'Activate' }}
                                                                    </a>
                                                            @endif
                                                        </td>

                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                         </div>
                                    </div>
                                 </div>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>
        </div>
    </section>
</main>

<script src="{{ asset('asset/js/userProfile.js') }}" type="text/javascript"></script>
@endsection
