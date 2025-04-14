<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $url = 'https://test-openapi-hk.qfapi.com';
    protected $app_code = '7A3AD020DDC04C3EBD6B07952FD4F6DB';
    protected $app_key = '9EB197983B184E0CB64C695DA6FDDB00';

    public function createPayment(Request $request)
    {
        $request->validate([
            'pay_type' => 'required|string',
            'amount' => 'required|numeric',
        ]);
        $api_type = '/trade/v1/payment';
        $fields_string = '';
        $pay_type = $request->input('pay_type');
        $amount = $request->input('amount');

        $currency = $request->input('currency', 'HKD');
        $now_time = now()->format('Y-m-d H:i:s');

        $fields = [
            'pay_type' => urlencode($pay_type),
            'out_trade_no' => urlencode($this->getRandStr(20)),
            'txcurrcd' => urlencode($currency),
            'txamt' => urlencode($amount),
            'txdtm' => $now_time,
        ];

        // print_r(ksort($fields));

        ksort($fields);
        foreach ($fields as $key => $value) {
            $fields_string .= $key.'='.$value.'&';
        }
        // $fields_string = http_build_query($fields);
        // $sign = strtoupper(md5($fields_string . $this->app_key));
        $fields_string = substr($fields_string, 0, strlen($fields_string) - 1);
        $sign = strtoupper(md5($fields_string . $this->app_key));

        $header = [];
        $header[] = 'X-QF-APPCODE: ' . $this->app_code;
        $header[] = 'X-QF-SIGN: ' . $sign;

        //Post Data
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . $api_type);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $output = curl_exec($ch);
        curl_close($ch);

        $final_data = json_decode($output, true);
        print_r($final_data);
        exit;

        return response()->json($response->json());
    }

    private function getRandStr($length)
    {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $len = strlen($str) - 1;
        $randstr = '';
        for ($i = 0; $i < $length; $i++) {
            $num = mt_rand(0, $len);
            $randstr .= $str[$num];
        }
        return $randstr;
    }
}
