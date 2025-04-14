@extends('admin.common.layouts')
@extends('admin.common.sidebar')
@extends('admin.common.navbar')


@section('content')
<div class="container">
    <h2>Add Subscription Plan</h2>
    <form method="POST" action="{{ route('store-plan') }}">
        @csrf
        <div class="mb-3">
            <label for="package_queue_room" class="form-label">Package Name</label>
            <input type="text" class="form-control" id="package_queue_room" name="package_name">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="text" class="form-control" id="price" name="price">
        </div>
        <div class="mb-3">
            <label for="Package_desc" class="form-label">Package Description</label>
            <input type="text" class="form-control" id="Package_desc" name="package_desc">
        </div>
        <div class="mb-3">
            <label for="maximum_queue_room" class="form-label">Maximum number of queue room</label>
            <input type="text" class="form-control" id="maximum_queue_room" name="maximum_queue_room">
        </div>
        <div class="mb-3">
            <label for="maximum_traffic" class="form-label">Maximum traffic per minute</label>
            <input type="text" class="form-control" id="maximum_traffic" name="maximum_traffic">
        </div>
        <div class="mb-3">
            <label for="maximum_sub_accounts" class="form-label">Maximum number of sub accounts</label>
            <input type="text" class="form-control" id="maximum_sub_accounts" name="maximum_sub_accounts">
        </div>
        <div class="mb-3">
            <label for="highlight" class="form-label">Highlight Feature</label>
            <input type="text" class="form-control" id="highlight" name="highlight_feature">
        </div>
        <div class="mb-3">
            <label class="form-label">Allow staff access management</label><br>
            <div class="form-check form-check-inline">
                <input type="radio" id="staff_access_management_yes" name="staff_access_management" value="1">
                <label for="staff_access_management_yes">Yes</label><br>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="staff_access_management_no" name="staff_access_management" value="0">
                <label for="staff_access_management_no">No</label><br>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Featured Plan</label><br>
            <div class="form-check form-check-inline">
                <input type="radio" id="feature_yes" name="featured_plan" value="1">
                <label for="feature_yes">Yes</label><br>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="feature_no" name="featured_plan" value="0">
                <label for="feature_no">No</label><br>
            </div>
        </div>     
        <div class="mb-3">
            <label class="form-label">Allow to setup bypass</label><br>
            <div class="form-check form-check-inline">
                <input type="radio" id="setup_bypass_yes" name="setup_bypass" value="1">
                <label for="setup_bypass_yes">Yes</label><br>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="setup_bypass_no" name="setup_bypass" value="0">
                <label for="setup_bypass_no">No</label><br>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Allow to setup pre-queue</label><br>
            <div class="form-check form-check-inline">
                <input type="radio" id="setup_pre_queue_yes" name="setup_pre_queue" value="1">
                <label for="setup_pre_queue_yes">Yes</label><br>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="setup_pre_queue_no" name="setup_pre_queue" value="0">
                <label for="setup_pre_queue_no">No</label><br>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Allow to setup SMS</label><br>
            <div class="form-check form-check-inline">
                <input type="radio" id="setup_sms_yes" name="setup_sms" value="1">
                <label for="setup_sms_yes">Yes</label><br>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="setup_sms_no" name="setup_sms" value="0">
                <label for="setup_sms_no">No</label><br>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Allow to setup Email</label><br>
            <div class="form-check form-check-inline">
                <input type="radio" id="setup_email_yes" name="setup_email" value="1">
                <label for="setup_email_yes">Yes</label><br>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" id="setup_email_no" name="setup_email" value="0">
                <label for="setup_email_no">No</label><br>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

@endsection