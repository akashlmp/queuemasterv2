<?php

namespace App\Http\Controllers\admin;

use App\Services\StripeService;
use App\Http\Controllers\Controller;
use App\Models\admin\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PaymentInformation;

class SubscriptionPlanAdminController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    }
    public function index()
    {
        // Fetch all subscription plans from the database
        $subscriptionPlans = SubscriptionPlan::all();

        // Pass the fetched data to the view
        return view('admin.subscription-plan', ['subscriptionPlans' => $subscriptionPlans]);
    }

    public function addsubscriptionplan()
    {
        return view('admin.add-subscription-plan');
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'package_name' => 'required|string',
            'maximum_queue_room' => 'required|string',
            'price' => 'required',
            'package_desc' => 'required|string',
            'highlight_feature' => 'required|string',
            'maximum_traffic' => 'required|string',
            'maximum_sub_accounts' => 'required|string',
            'staff_access_management' => 'required|in:1,0',
            'featured_plan' => 'required|in:1,0',
            'setup_bypass' => 'required|in:1,0',
            'setup_pre_queue' => 'required|in:1,0',
            'setup_sms' => 'required|in:1,0',
            'setup_email' => 'required|in:1,0',
        ]);

        // Create a new SubscriptionPlan object with the validated data
        $subscriptionPlan = SubscriptionPlan::create($validatedData);

        return redirect()->route('subscription-plan')->with('success', 'Subscription plan added successfully.');
    }

    public function deleteSubscriptionPlan($id)
    {
        // Find the subscription plan by ID
        $plan = SubscriptionPlan::findOrFail($id);

        // Delete the plan
        $plan->delete();

        // Flash success message
        Session::flash('success', '<i class="fa fa-check-circle"></i> Subscription Plan deleted successfully!');

        // Redirect back to the previous page
        return redirect()->back();
    }

    public function editSubscriptionPlan($id)
    {
        // Find the subscription plan by ID
        $plan = SubscriptionPlan::findOrFail($id);

        // Pass the plan data to the view
        return view('admin.edit-subscription-plan', compact('plan'));
    }

    public function updateSubscriptionPlan(Request $request, $id)
    {
        $validatedData = $request->validate([
            'package_name' => 'required|string',
            'maximum_queue_room' => 'required|string',
            'price' => 'required',
            'package_desc' => 'required|string',
            'highlight_feature' => 'required|string',
            'maximum_traffic' => 'required|string',
            'maximum_sub_accounts' => 'required|string',
            'staff_access_management' => 'required|in:1,0',
            'featured_plan' => 'required|in:1,0',
            'setup_bypass' => 'required|in:1,0',
            'setup_pre_queue' => 'required|in:1,0',
            'setup_sms' => 'required|in:1,0',
            'setup_email' => 'required|in:1,0',
        ]);

        // Find the subscription plan by ID
        $subscriptionPlan = SubscriptionPlan::findOrFail($id);

        // Update the subscription plan attributes
        $subscriptionPlan->update([
            'package_name' => $validatedData['package_name'],
            'maximum_queue_room' => $validatedData['maximum_queue_room'],
            'price' => $validatedData['price'],
            'package_desc' => $validatedData['package_desc'],
            'highlight_feature' => $validatedData['highlight_feature'],
            'featured_plan' => $validatedData['featured_plan'],
            'maximum_traffic' => $validatedData['maximum_traffic'],
            'maximum_sub_accounts' => $validatedData['maximum_sub_accounts'],
            'staff_access_management' => $validatedData['staff_access_management'],
            'setup_bypass' => $validatedData['setup_bypass'],
            'setup_pre_queue' => $validatedData['setup_pre_queue'],
            'setup_sms' => $validatedData['setup_sms'],
            'setup_email' => $validatedData['setup_email'],
        ]);

        Session::flash('success', '<i class="fa fa-check-circle"></i> Subscription Plan updated successfully!');
        // Optionally, you can return a response or redirect the user after updating the plan
        return redirect()->route('subscription-plan', $id)->with('success', 'Subscription plan updated successfully.');
    }

    public function showSubscription()
    {
        $user = Auth::user();
       
        $paymentList = PaymentInformation::where('user_id', $user->id)->orderBy('id', 'desc')->paginate(10);
        $billingHistory = [];
        foreach ($paymentList as $payment)
        {
            $payData['currency'] = strtoupper($payment->currency);
            $payData['amount_total'] = $payment->amount_total;
            $payData['payment_status'] = $payment->payment_status;
            $payData['purchase_date'] = Carbon::parse($payment->created_at)->format('d F Y');
            $payData['end_date'] = Carbon::parse($payment->end_date)->format('d F Y');
            $payData['plan_name'] = $this->stripeService->getSubscriptionName($payment->subscription_id);
            $payData['receipt_url'] = $payment->invoice_id ? $this->stripeService->getReceiptURL($payment->invoice_id) : ["invoice_number" => null, "file_url" => null];

            $billingHistory[] = $payData;
        }

        // $finalData['status']            = 200;
        // $finalData['message']           = 'Success';
        // $finalData['data']              = $result;
        // $finalData['currentPage']       = $paymentList->currentPage();
        // $finalData['last_page']         = $paymentList->lastPage();
        // $finalData['total_record']      = $paymentList->total();
        // $finalData['per_page']          = $paymentList->perPage();
            

        if (Auth::user()->role == 1) {
            $plans = SubscriptionPlan::all();
            $plans = $plans->toArray();
            return view('subscription', compact('plans', 'billingHistory'));
        } else {
            Session::flash('warning', 'You don\'t have access this route.');
            return view('dashboard');
        }
    }
}
