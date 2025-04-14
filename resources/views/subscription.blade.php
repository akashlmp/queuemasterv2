@extends('common.layouts')

@section('content')

@include('common.sidebar')
@include('common.header')
<style>
    .bgmain {
        background: #fff;
    }

       /* Tooltip container */
    .help-icon {
      position: relative;
      display: inline-block; /* Ensures the container fits around the icon */
      cursor: pointer;
    }

    /* Tooltip text */
    .help-icon::after {
      content: attr(data-tooltip);
      position: absolute;
      top: 0%;
        left: 290%;
        width: 320px;
      transform: translateX(-50%);
      background-color: #333;
      color: #fff;
      padding: 10px 15px;
      border-radius: 6px;
      white-space: normal; /* Allows the text to wrap */
      opacity: 0;
      visibility: hidden;
      transition: opacity 0.3s, visibility 0.3s;
      font-size: 14px;
      font-family: 'Arial', sans-serif; /* Customizable font family */
      text-align: center;
      z-index: 1000;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Adds a subtle shadow for better visibility */
      line-height: 1.4; /* Increases line height for better readability */
    }

    /* Show the tooltip on hover */
    .help-icon:hover::after {
      opacity: 1;
      visibility: visible;
    }



</style>
<?php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

$userId = Auth::id();
$subscriptionPlanId = DB::table('queuetb_users')
                    ->where('id', $userId)
                    ->value('subscription_plan_id');

?>
<link rel="stylesheet" href="{{ asset('asset/css/subscription.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/card-info/2.1.1/card-info.min.css" />
<main id="main" class="bgmain">
    <section>
        <div class="container mt-3 ">
            <nav class="bg-light nav1 navplantab">
                <div class="nav nav-tabs border-0" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-Plans-tab" data-bs-toggle="tab" data-bs-target="#nav-Plans" type="button" role="tab" aria-controls="nav-Plans" aria-selected="true">Plans</button>
                    <!-- <button class="nav-link " id="nav-Payment_methods-tab button " data-bs-toggle="tab" data-bs-target="#nav-Payment_methods" type="button" role="tab" aria-controls="nav-Payment_methods" aria-selected="false">Payment methods</button> -->
                    <button class="nav-link" id="nav-Billing_history-tab button" data-bs-toggle="tab" data-bs-target="#nav-Billing_history" type="button" role="tab" aria-controls="nav-Billing_history" aria-selected="false">Biling history</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <div class="tab-pane fade show active pt-4" id="nav-Plans" role="tabpanel" aria-labelledby="nav-Plans-tab">
                    <div class="container">
                        <div class="row mt-5 ms-0 me-0">

                        @foreach ($plans as $plan)
                            <div class="col-md-3 col-sm-6 col-xs-12 navbox mb-3">
                                <div class="border planbox">
                                    <div class="planName">{{ $plan['package_name'] }}</div>
                                    <p class="plandes">{{ $plan['package_desc'] }}</p>
                                    <div class="navlogo pt-4 pb-0">
                                        ${{ $plan['price'] }}
                                        <p class="duration">per month</p>
                                    </div>
                                    <button type="button" class="btn btn-dark mt-4 w-100 selectplan {{ $plan['id'] == $subscriptionPlanId ? 'currentplan' : '' }}"data-plan='@json($plan)' {{ $plan['id'] == $subscriptionPlanId ? 'disabled' : '' }}> {{ $plan['id'] == $subscriptionPlanId ? 'Your Current Plan' : 'Select this Plan' }}
                                    </button>

                                    <hr class="mt-4 mb-4 linehr">
                                    @foreach (explode(',', $plan['highlight_feature']) as $feature)
                                        <div class="d-flex gap-1 planlist align-items-center">
                                            <div class="planicon">
                                                <span class="material-symbols-outlined text-success">check</span>
                                            </div>
                                            <div class="plancontent">
                                                <p>{{ trim($feature) }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                        </div>
                        <div class="row mt-4 tablebox ms-0 me-0">
                            <p class="text-center headingtxt pb-3">All features</p>
                            <div class="table-responsive">

                                <table class="table planTable">
                                    <thead>
                                        <tr>
                                            <th scope="col text-center"></th>
                                            @foreach ($plans as $plan)
                                                <th scope="col text-center">
                                                    {{ $plan['package_name'] }}
                                                    <p class="p1">${{ $plan['price'] }}/month</p>
                                                </th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                           <td>
                                          Setup queue room
                                          <span class="material-symbols-outlined help-icon ms-2" data-tooltip="This feature allows you to organize and monitor incoming tasks or requests efficiently.">help</span>
                                        </td>

                                            @foreach ($plans as $plan)
                                                <td><span class="material-symbols-outlined text-success">check</span></td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Reporting <span class="material-symbols-outlined help-icon ms-2"  data-tooltip="Generate and analyze comprehensive reports to track performance, monitor key metrics, and gain insights for informed decision-making.">help</span></td>
                                            @foreach ($plans as $plan)
                                                <td><span class="material-symbols-outlined text-success">check</span></td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Maximum number of queue rooms <span class="material-symbols-outlined help-icon ms-2"  data-tooltip="Set the limit for the number of queue rooms that can be created to organize and manage tasks effectively.">help</span></td>
                                            @foreach ($plans as $plan)
                                                <td>{{ $plan['maximum_queue_room'] }}</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Maximum traffic <span class="material-symbols-outlined help-icon ms-2"  data-tooltip="Define the maximum number of simultaneous users or requests allowed to ensure optimal performance and prevent system overload.">help</span></td>
                                            @foreach ($plans as $plan)
                                                <td>{{ $plan['maximum_traffic'] }} / min</td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Sub accounts <span class="material-symbols-outlined help-icon ms-2"  data-tooltip="Create and manage additional accounts under a main account for better organization, access control, and delegation of responsibilities.">help</span></td>
                                            @foreach ($plans as $plan)
                                                <td>
                                                    @if ($plan['maximum_sub_accounts'] > 0)
                                                        <span class="material-symbols-outlined text-success">check</span>
                                                    @else
                                                        <span class="material-symbols-outlined text-secondary">close</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Maximum sub-accounts <span class="material-symbols-outlined help-icon ms-2"  data-tooltip="Specify the maximum number of sub-accounts that can be created under a main account to manage user access and organization.">help</span></td>
                                            @foreach ($plans as $plan)
                                                <td>
                                                    @if ($plan['maximum_sub_accounts'] > 0)
                                                        {{ $plan['maximum_sub_accounts'] }}
                                                    @else
                                                        <span class="material-symbols-outlined text-secondary">close</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Bypass queue <span class="material-symbols-outlined help-icon ms-2"  data-tooltip="Allow certain tasks or users to skip the queue and receive immediate attention or processing, bypassing standard wait times.">help</span></td>
                                            @foreach ($plans as $plan)
                                                <td>
                                                    @if ($plan['setup_bypass'])
                                                        <span class="material-symbols-outlined text-success">check</span>
                                                    @else
                                                        <span class="material-symbols-outlined text-secondary">close</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Pre-queue <span class="material-symbols-outlined help-icon ms-2"  data-tooltip="Configure a staging area where tasks or requests are placed before they enter the main queue, allowing for preliminary processing or prioritization.">help</span></td>
                                            @foreach ($plans as $plan)
                                                <td>
                                                    @if ($plan['setup_pre_queue'])
                                                        <span class="material-symbols-outlined text-success">check</span>
                                                    @else
                                                        <span class="material-symbols-outlined text-secondary">close</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>SMS notification <span class="material-symbols-outlined help-icon ms-2"  data-tooltip="Send real-time updates and alerts via SMS to keep users informed about important events, tasks, or changes.">help</span></td>
                                            @foreach ($plans as $plan)
                                                <td>
                                                    @if ($plan['setup_sms'])
                                                        <span class="material-symbols-outlined text-success">check</span>
                                                    @else
                                                        <span class="material-symbols-outlined text-secondary">close</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            <td>Email notification <span class="material-symbols-outlined help-icon ms-2"  data-tooltip="Receive automated updates and alerts via email to stay informed about important events, tasks, and changes.">help</span></td>
                                            @foreach ($plans as $plan)
                                                <td>
                                                    @if ($plan['setup_email'])
                                                        <span class="material-symbols-outlined text-success">check</span>
                                                    @else
                                                        <span class="material-symbols-outlined text-secondary">close</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-Payment_methods" role="tabpanel" aria-labelledby="nav-Payment_methods-tab">
                    <div class="payment-method pt-5 container">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <p class="payment-heading">Add and manage your cards</p>
                                <div class="payment-subheading">Add and save credit cards to your account to use for your subscription plans.</div>
                                <div class="paymentbox">
                                    <div class="card-container pt-5">
                                        <div class="card-header">
                                            <div class="d-flex align-items-center cardlogodetails">
                                                <div class="card-img">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/4/41/Visa_Logo.png" alt="Visa Logo" width="73px">
                                                </div>
                                                <div class="card-details w-100">
                                                    ************2732 (1/29)
                                                </div>
                                            </div>
                                            <span class="incheck">In use <span class="material-symbols-outlined ms-3">check</span></span>
                                        </div>

                                        <div class="card-actions">
                                            <span></span>
                                            <button type="button" class="add-card btn p-0" data-bs-toggle="modal" data-bs-target="#addcardModal"><span class="d-flex align-items-center"><span class="material-symbols-outlined me-2">add</span> Add new card</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="nav-Billing_history" role="tabpanel" aria-labelledby="nav-Billing_history-tab">
                    <div class="billing_history pt-5 container">
                        <div class="row m-0">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table billingHistory">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Item</th>
                                                <th>Price</th>
                                                <th>Payment Status</th>
                                                <th>Receipt</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($billingHistory as $history)
                                            <tr>
                                                <td>{{ $history['purchase_date'] }}</td>
                                                <td>{{ $history['plan_name'] }}</td>
                                                <td>{{ $history['currency'] }} {{ $history['amount_total'] }}</td>
                                                <td>
                                                    @if($history['payment_status'] == 1)
                                                        Pending
                                                    @elseif($history['payment_status'] == 2)
                                                        Complete
                                                    @else
                                                        Fail
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($history['receipt_url']['file_url'])
                                                        <a href="{{ $history['receipt_url']['file_url'] }}" class="btn uploadBtn" target="_blank">
                                                            <span class="material-symbols-outlined uploadIcon">upload</span>
                                                        </a>
                                                    @else
                                                        <span>No Receipt</span>
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
    </section>
</main>

{{-- <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST" style="display: none;"> --}}
<form id="checkout-form" action="{{ Auth::check() && is_null(Auth::user()->country) ? route('checkout.process') : route('checkout.process.qfpay') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" id="subscription_id" name="subscription_id">
    <input type="hidden" id="goods_name" name="goods_name">
    <input type="hidden" id="out_trade_no" name="out_trade_no">
    <input type="hidden" id="txamt" name="txamt">
</form>
<!-- Modal -->
<div class="modal fade addcardModal" id="addcardModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addcardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addcardModallLabel">Add new card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                    <div id="cardLogo"></div>
                        <div class="one col-md-6 col-sm-6 col-xs-12">
                            <label for="">Name on card</label>
                            <input placeholder="Enter Name" type="text" class="form-control">
                        </div>

                        <div class="two col-md-6 col-sm-6 col-xs-12">
                            <label for="">Card Number</label>
                            <input maxlength="16" id="cardNumber" placeholder="Enter card number" type="number" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="three col-md-4 col-sm-4 col-xs-12">
                            <label for="">Expiry Date</label>
                            <select class="form-select">
                                <option>January</option>
                                <option>February</option>
                                <option>March</option>
                                <option>April</option>
                                <option>May</option>
                                <option>June</option>
                                <option>July</option>
                                <option>August</option>
                                <option>September</option>
                                <option>October</option>
                                <option>November</option>
                                <option>December</option>
                            </select>
                        </div>
                        <div class="four col-md-4 col-sm-4 col-xs-12">
                            <!-- blank character -->
                            <label for="">&ZeroWidthSpace;</label>
                            <select class="form-select" id="yearSelect">
                            </select>
                        </div>
                        <div class="five col-md-4 col-sm-4 col-xs-12">
                            <label for="">CVV</label>
                            <input maxlength="3" placeholder="Enter Pin" type="number" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary savecard">Save changes</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Current year
    var currentYear = new Date().getFullYear();

    // Select element
    var selectElement = document.getElementById("yearSelect");

    // Adding options for next 50 years
    for (var i = 0; i < 50; i++) {
        var option = document.createElement("option");
        option.text = currentYear + i;
        option.value = currentYear + i;
        selectElement.appendChild(option);
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/creditcardjs/1.0.0/creditcard.min.js"></script>
    <script>
        document.getElementById('cardNumber').addEventListener('input', function() {
    const cardNumber = this.value.replace(/\s+/g, ''); // Remove spaces
    const cardLogoDiv = document.getElementById('cardLogo');
    cardLogoDiv.innerHTML = ''; // Clear previous logo

    if (cardNumber.length >= 6 && CreditCard.cardType(cardNumber)) {
        try {
            const cardType = CreditCard.cardType(cardNumber);
            const logo = document.createElement('img');
            logo.src = `https://www.freeiconspng.com/uploads/${cardType}-credit-card-icon-2.png`; // Example URL
            logo.alt = cardType;
            cardLogoDiv.appendChild(logo);
        } catch (error) {
            console.error('Error fetching card info:', error);
        }
    }
});

    </script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.selectplan').forEach(button => {
        button.addEventListener('click', function () {
            const plan = JSON.parse(this.getAttribute('data-plan'));
            console.log(plan.id);
            // Check if the plan price is 0 (free trial)
            if (parseFloat(plan.price) === 0) {
                // Use jQuery AJAX to send request for free trial subscription
                $.ajax({
                    url: '{{ route("checkout.freeTrial") }}',
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        plan_id: plan.id // Assuming the plan has an ID
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.success) {
                            // Handle successful subscription
                            alert('You are subscribed to the free trial!');
                            window.location.href = data.redirect || '{{ route("dashboard") }}'; // Redirect to a desired page
                        } else {
                            // Handle errors
                            alert('Subscription failed!');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error:', error);
                        alert('An error occurred.');
                    },
                });
            } else {
                // Populate the hidden form for paid plans
                document.getElementById('subscription_id').value = plan.id;
                document.getElementById('goods_name').value = plan.package_name;
                document.getElementById('out_trade_no').value = Date.now().toString(); // Generate a unique trade number
                document.getElementById('txamt').value = plan.price * 100;

                // Submit the form
                document.getElementById('checkout-form').submit();
            }
        });
    });
});

</script>
@endsection
