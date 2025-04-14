<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

use App\Helpers\Logics;

class PaymentGatewayController extends Controller
{
    public function paymentInquire(Request $request)
    {
        $response = Http::asForm()->post(env('PAYMENT_GATEWAY_URL'), [
            'apiOperation' => 'PAYMENT_OPTIONS_INQUIRY',
            // 'order.id' => $request->input('order_id'),
            'merchant' => env('MERCHANT_ID'),
            'apiPassword' => env('API_PASSWORD'),
            'apiUsername' => "merchant.".env('MERCHANT_ID'),
        ]);      

        $responseData = [];
        parse_str($response->body(), $responseData);

        if ($responseData['result'] == 'SUCCESS') {
            return response()->json(['status' => 'success', 'response' => $responseData]);
        } else {
            return response()->json(['status' => 'failure', 'error' => $responseData['response.gatewayCode'] ?? 'UNKNOWN_ERROR', 'message' => $responseData['response.gatewayRecommendation'] ?? 'Please try again.']);
        }
    }

    public function processPayment(Request $request)
    {
        $response = Http::asForm()->post(env('PAYMENT_GATEWAY_URL'), [
            'apiOperation'   => 'PAY',
            'order.id'       => mt_rand(1000, 9999),  // Unique order ID
            'order.amount'   => "100",  // Payment amount
            'order.currency' => 'HKD',  // Currency
            'sourceOfFunds.type' => 'CARD',
            'sourceOfFunds.provided.card.number' => "4508750015741019",  // Alternative test card number
            'sourceOfFunds.provided.card.expiry.month' => "01",  // Expiry month
            'sourceOfFunds.provided.card.expiry.year' => "39",  // Expiry year
            'sourceOfFunds.provided.card.securityCode' => "222",  // CVV
            'merchant' => env('MERCHANT_ID'),
            'apiPassword' => env('API_PASSWORD'),
            'apiUsername' => "merchant.".env('MERCHANT_ID'),
            'transaction.id' => mt_rand(10000000, 99999999),  // Unique transaction ID
        ]);

        // Convert the response to an array
        $responseData = [];
        parse_str($response->body(), $responseData);
        if ($responseData['result'] == 'SUCCESS') {
            return response()->json(['status' => 'success', 'response' => $responseData]);
        } else {
            return response()->json(['status' => 'failure', 'error' => 'Payment has been fail, Please try again.', 'response' => $responseData]);
        }
    }
    
    // public function processPayment(Request $request)
    // {
    //     // Sample card details (replace these with actual form inputs or test card details)
    //     $cardNumber = "4508750015741019";
    //     $expiryMonth = "01";
    //     $expiryYear = "39";
    //     $securityCode = "100";
    //     $amount = "100";  // Payment amount
    //     $currency = "HKD";  // Currency
    //     $orderId = mt_rand(1000, 9999);  // Unique order ID
    
    //     $response = Http::asForm()->post(env('PAYMENT_GATEWAY_URL'), [
    //         'apiOperation'   => 'PAY',  // or 'PAY'
    //         'order.id'       => $orderId,
    //         'order.amount'   => $amount,
    //         'order.currency' => $currency,
    //         'sourceOfFunds.type' => 'CARD',
    //         'sourceOfFunds.provided.card.number' => $cardNumber,
    //         'sourceOfFunds.provided.card.expiry.month' => $expiryMonth,
    //         'sourceOfFunds.provided.card.expiry.year' => $expiryYear,
    //         'sourceOfFunds.provided.card.securityCode' => $securityCode,
    //         'merchant' => env('MERCHANT_ID'),
    //         'apiPassword' => env('API_PASSWORD'),
    //         'apiUsername' => "merchant.".env('MERCHANT_ID'),
    //         'transaction.id' => mt_rand(10000000, 99999999),  // Unique transaction ID
    //         'transaction.source' => 'INTERNET',  // Transaction source
    //     ]);
    
    //     // Parse and return the response
    //     $responseData = [];
    //     parse_str($response->body(), $responseData);
    //     // return $responseData;
    //     if (isset($responseData['result']) && $responseData['result'] == 'SUCCESS') {
    //         // Payment was successful, handle success
    //         return response()->json([
    //             'status' => 'success',
    //             'transaction_id' => $responseData['transaction.id'],
    //             'order_id' => $orderId,
    //         ]);
    //     } else {
    //         // Payment failed, handle error
    //         return response()->json([
    //             'status' => 'failure',
    //             'error' => $responseData['response.gatewayCode'] ?? 'UNKNOWN_ERROR',
    //             'message' => $responseData['response.gatewayRecommendation'] ?? 'Please try again with different payment details.'
    //         ]);
    //     }
    // }

    public function initiateCheckout(Request $request)
    {
        $orderId = mt_rand(1000, 9999);  // Generate a unique order ID
        $amount = "100";  // Payment amount
        $currency = "HKD";  // Currency

        // Make a request to initiate the hosted checkout session
        $response = Http::asForm()->post(env('PAYMENT_GATEWAY_URL'), [
            'apiOperation'   => 'INITIATE_CHECKOUT',  // Correct operation for Hosted Checkout
            'order.id'       => $orderId,
            'order.amount'   => $amount,
            'order.currency' => $currency,
            'merchant'       => env('MERCHANT_ID'),
            'apiPassword'    => env('API_PASSWORD'),
            'apiUsername'    => 'merchant.' . env('MERCHANT_ID'),
            'interaction.operation' => 'PURCHASE',  // Specify the type of checkout
        ]);

        $responseData = [];
        parse_str($response->body(), $responseData);
        if ($responseData['result'] == 'SUCCESS') {
            $checkoutUrl = "https://test-dahsingbank.mtf.gateway.mastercard.com/checkout?sessionId=" . $responseData['session_id'];

            return response()->json([
                'status' => 'success',
                'session_id' => $responseData['session_id'],
                'order_id' => $orderId,
                'checkout_url' => $checkoutUrl,  // Redirect the user to this URL for payment
                'response' => $responseData,
            ]);
        } else {
            return response()->json([
                'status' => 'failure',
                'error' => $responseData['response.gatewayCode'] ?? 'UNKNOWN_ERROR',
                'message' => $responseData['response.gatewayRecommendation'] ?? 'Please try again.',
            ]);
        }
    }

}
