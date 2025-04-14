@extends('admin.common.layouts')
@extends('admin.common.sidebar')
@extends('admin.common.navbar')


@section('content')
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
<a href="{{ url('admin/add-plan')}}" class="btn btn-primary">Add Plan</a>
<table class="table table-hover" id=subscriptionTable>
  <thead>
    <tr>
        <th scope="col">Name of Subscription Package</th>
        <th scope="col">Max. Number of queue room</th>
        <th scope="col">Maximum traffic per minute</th>
        <th scope="col">Maximum number of sub accounts</th>
        <th scope="col">Allow staff access management</th>
        <th scope="col">Allow to setup bypass</th>
        <th scope="col">Allow to setup pre-queue</th>
        <th scope="col">Allow to setup SMS</th>
        <th scope="col">Allow to setup Email</th>
        <th scope="col">Action</th>

      
    </tr>
  </thead>
  <tbody>
    @foreach($subscriptionPlans as $plan)
    <tr>
        <td>{{ $plan->package_name }}</td>
        <td>{{ $plan->maximum_queue_room }}</td>
        <td>{{ $plan->maximum_traffic }}</td>
        <td>{{ $plan->maximum_sub_accounts }}</td>
        <td>
            @if($plan->staff_access_management == 1)
                <i class="fas fa-check-circle text-success"></i> 
            @else
                <i class="fas fa-times-circle text-danger"></i>
            @endif
        </td>
        <td>
            @if($plan->setup_bypass == 1)
                <i class="fas fa-check-circle text-success"></i> 
            @else
                <i class="fas fa-times-circle text-danger"></i>
            @endif
        </td>
        <td>
            @if($plan->setup_pre_queue == 1)
                <i class="fas fa-check-circle text-success"></i> 
            @else
                <i class="fas fa-times-circle text-danger"></i>
            @endif
        </td>
        <td>
            @if($plan->setup_sms == 1)
                <i class="fas fa-check-circle text-success"></i> 
            @else
                <i class="fas fa-times-circle text-danger"></i>
            @endif
        </td>
        <td>
            @if($plan->setup_email == 1)
                <i class="fas fa-check-circle text-success"></i> 
            @else
                <i class="fas fa-times-circle text-danger"></i>
            @endif
        </td>

        <td>  
            
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-outline-success"><a href="{{ route('edit-subscription-plan', $plan->id) }}" class="text-success">Edit</a></button>
                <button type="button" class="btn btn-outline-danger"><a href="{{ route('delete-subscription-plan', $plan->id) }}" class="text-danger">Delete</a></button>
            </div>
        </td>
    </tr>
    @endforeach
  </tbody>
</table>
@endsection