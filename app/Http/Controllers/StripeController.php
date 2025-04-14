<?php

namespace App\Http\Controllers;

use App\Services\StripeService;
use Illuminate\Http\Request;
use DB;
use Auth;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Stripe;
use Stripe\Exception\CardException;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\PaymentMethod;
use Stripe\InvoiceItem;
use Stripe\Invoice;

use App\Models\QueuedbUser;
use App\Models\PaymentInformation;

class StripeController extends Controller
{
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createCheckout(Request $request)
    {
        $user = Auth::user();
        $customerId = $user->stripe_customer_id;
        
        if (!$customerId)
        {
          $customerId = $this->stripeService->createCustomer($user, $request->stripeToken);
          
          $user = DB::table('queuetb_users')->where('id', $user->id)->update(['stripe_customer_id' => $customerId]);
        }

        // Prepare line items for the session
        $lineItems = [
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $request->goods_name,
                ],
                'unit_amount' => $request->txamt, // Stripe expects amounts in cents
            ],
            'quantity' => 1,
        ];

        // Create checkout session
        $customerId = !$customerId ? $customerId : $customerId;
        $session = $this->stripeService->createCheckoutSession($lineItems, $customerId);

        $checkoutData = $this->stripeService->getCheckoutSession($session['id']);
        
        $savePaymentInfo = new PaymentInformation;
        $savePaymentInfo->user_id = $user->id;
        $savePaymentInfo->subscription_id = $request->subscription_id;
        $savePaymentInfo->session_id = $checkoutData['id'];
        $savePaymentInfo->currency = $checkoutData['currency'];
        $savePaymentInfo->amount_total = $checkoutData['amount_total'] / 100;
        $savePaymentInfo->payment_status = "1";

        $savePaymentInfo->save();
        return redirect($session->url);
        // return $session;
    }

    /** This function for save payment information */
    private function savePaymentInformation($checkoutData, $type)
    {
        $savePaymentInfo = PaymentInformation::where('session_id', $checkoutData['id'])->first();
        $endDate = $checkoutData['end_date'];
        $savePaymentInfo->txn_id = $checkoutData['payment_intent'] ?? null;
        $savePaymentInfo->invoice_id = $checkoutData['invoice'] ?? null;
        $savePaymentInfo->end_date = $endDate ?? null;
        $savePaymentInfo->payment_status = $type;

        $result = $savePaymentInfo->save();
        
        DB::table('queuetb_users')->where('id', $savePaymentInfo->user_id)->update(['subscription_plan_id' => $savePaymentInfo->subscription_id, 'subscription_end_date' => $endDate]);
        return true;
    }

    /** This function for view the payment success page */
    public function viewSuccessPage(Request $request)
    {
        $user = Auth::user();
        $sessionId = $request->query('session_id');
        $endDate = Carbon::now()->addDays(30);
        $checkoutData = $this->stripeService->getCheckoutSession($sessionId);
        $checkoutData['end_date'] = $endDate->format('Y-m-d\TH:i:s.u')??null;
        // return $checkoutData;die;
        $savePayment = self::savePaymentInformation($checkoutData, 2);
        return view('stripe.success');
    }

    /** This function for view the payment fail page */
    public function viewFailPage()
    {
        $sessionId = $request->query('session_id');
        $checkoutData = $this->stripeService->getCheckoutSession($sessionId);
        
        $savePayment = self::savePaymentInformation($checkoutData, 3);
        return view('stripe.fail');
    }

    /** This function for get the payment list according to the user ID */
    public function getPaymentList()
    {
        $user = Auth::user();
        $paymentList = PaymentInformation::where('user_id', $user->id)->paginate(10);
        $result = [];
        if (sizeof($paymentList))
        {
            foreach ($paymentList as $payment)
            {
                $payData['currency'] = strtoupper($payment->currency);
                $payData['amount_total'] = $payment->amount_total;
                $payData['purchase_date'] = Carbon::parse($payment->created_at)->format('d F Y');
                $payData['end_date'] = Carbon::parse($payment->end_date)->format('d F Y');
                $payData['plan_name'] = $this->stripeService->getSubscriptionName($payment->subscription_id);
                $payData['receipt_url'] = $payment->invoice_id ? $this->stripeService->getReceiptURL($payment->invoice_id) : ["invoice_number" => null, "file_url" => null];

                $result[] = $payData;
            }

            $finalData['status']            = 200;
            $finalData['message']           = 'Success';
            $finalData['data']              = $result;
            $finalData['currentPage']       = $paymentList->currentPage();
            $finalData['last_page']         = $paymentList->lastPage();
            $finalData['total_record']      = $paymentList->total();
            $finalData['per_page']          = $paymentList->perPage();

            return response()->json($finalData);
        } else {
            # code...
        }
        
    }

    /** This function for manage stripe payment data using with webhook call */
    public function stripeWebhook(Request $request)
    {
        $event = $request->all();
        $endDate = Carbon::now()->addDays(30);
        $checkoutData = $event['data']['object'];
        $checkoutData['end_date'] = $endDate->format('Y-m-d\TH:i:s.u')??null;
        if ($checkoutData['status'] == "complete")
        {
            $savePayment = self::savePaymentInformation($checkoutData, 2);
        } elseif ($checkoutData['status'] == "open") {
            $savePayment = self::savePaymentInformation($checkoutData, 1);
        } else {
            $savePayment = self::savePaymentInformation($checkoutData, 3);
        }

        return true;
    }
}
