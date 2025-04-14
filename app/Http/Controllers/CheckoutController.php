<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        return view('checkout');
    }

    public function process(Request $request)
    {
        $validated = $request->validate([
            'goods_name' => 'required|string',
            'out_trade_no' => 'required|string',
            'txamt' => 'required|numeric',
        ]);

        $obj = [
            'appcode' => env('QFPAY_APPCODE'),
            // 'appcode' => '7A3AD020DDC04C3EBD6B07952FD4F6DB',
            'goods_name' => $validated['goods_name'],
            'out_trade_no' => $validated['out_trade_no'],
            'paysource' => 'remotepay_checkout',
            'return_url' => route('checkout.success'),
            'failed_url' => route('checkout.failed'),
            'notify_url' => route('checkout.notify'),
            'sign_type' => 'sha256',
            'txamt' => $validated['txamt'],
            'txcurrcd' => 'HKD',
            'txdtm' => now()->format('Y-m-d H:i:s'),
        ];

        $api_key = env('QFPAY_API_KEY');
        $params = $this->paramStringify($obj);
        $sign = hash('sha256', $params . $api_key);

        $obj['sign'] = $sign;


        $url = 'https://test-openapi-hk.qfapi.com/checkstand/#/?' . http_build_query($obj);
        //         $url = 'https://test-openapi-hk.qfapi.com';
        // $api_type = '/trade/v1/payment';
        // $url = 'https://openapi-int.qfapi.com/trade/v1/payment/' . http_build_query($obj);

        return redirect($url);
    }

    public function freeTrial(Request $request)
    {
        $validated = $request->validate([
            'plan_id' => 'required|integer',
        ]);

        $userId = Auth::id();

        try {
            DB::table('queuetb_users')
                ->where('id', $userId)
                ->update(['subscription_plan_id' => $validated['plan_id']]);

            return response()->json([
                'success' => true,
                'message' => 'Free trial activated successfully!',
                'redirect' => route('dashboard'),
            ]);
        } catch (\Exception $e) {
            // Handle exception
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate free trial. Please try again.',
            ], 500);
        }
    }

    private function paramStringify($json)
    {
        $str = '';
        $keysArr = array_keys($json);
        sort($keysArr);
        foreach ($keysArr as $val) {
            if (empty($json[$val])) {
                continue;
            }
            $str .= $val . '=' . $json[$val] . '&';
        }
        return rtrim($str, '&');
    }
}
