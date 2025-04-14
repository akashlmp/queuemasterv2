<?php

namespace App\Services;

use DB;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Http;

class StripeService
{
    public function __construct()
    {
        // Set the Stripe secret key
        $this->stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    /** Create a stripe customer */
    public function createCustomer($user, $stripeToken)
    {
        $customerAdd = \Stripe\Customer::create([
            'name' => $user->name,
            'email' => $user->email,
            'source' => $stripeToken,
            'address' => ["country" => "CN"]
          ]);
          
        return $customerAdd['id'];
    }

    /**
     * Create a Stripe Checkout session.
     *
     * @param array $items
     * @return Session
     */
    public function createCheckoutSession($lineItems, $customerId)
    {
        // Create a new Checkout session
        $session = Session::create([
            'payment_method_types' => ['card'],
            'customer' => $customerId,
            'payment_intent_data' => ['setup_future_usage' => 'off_session'],
            'line_items' => [$lineItems],
            'customer_update' => [
                'address' => 'auto',
            ],
            'mode' => 'payment',
            'invoice_creation' => ['enabled' => true],
            // 'automatic_tax' => ['enabled' => true],
            // 'success_url' => 'https://queuing.walkingdreamz.com/dashboard',
            // 'cancel_url' => 'https://queuing.walkingdreamz.com/subscription',
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.fail') . '?session_id={CHECKOUT_SESSION_ID}',
        ]);

        return $session;
    }

    /** get the checkour session information using with checkour/session ID */
    public function getCheckoutSession($sessionId)
    {
        $getCheckoutData = $this->stripe->checkout->sessions->retrieve($sessionId, []);

        return $getCheckoutData;
    }

    /** This function using for get the subscription name */
    public function getSubscriptionName($subscriptionId)
    {
        $getSubscriptionName = DB::table('queuetb_subscription_plan')->where('id', $subscriptionId)->value('package_name');

        return $getSubscriptionName;
    }
    /** This function using for get the receipt url */
    public function getReceiptUrl($invoiceId)
    {
        $invoiceUrl = $this->stripe->invoices->retrieve($invoiceId, []);
        $splitURL = explode("/i/", $invoiceUrl->hosted_invoice_url);
        $response = Http::get("https://invoicedata.stripe.com/invoice_receipt_file_url/".$splitURL[1]);
        $body = $response->body();
        $response = $response->json();

        $data['invoice_number'] = $invoiceUrl->number;
        $data['file_url'] = $response['file_url'];
        return $data;
    }
}
