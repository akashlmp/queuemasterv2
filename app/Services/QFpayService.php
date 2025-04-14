<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QFPayService
{
    protected $url;
    protected $app_code;
    protected $app_key;

    public function __construct()
    {
        $this->url = 'https://test-openapi-hk.qfapi.com';
        $this->app_code = '7A3AD020DDC04C3EBD6B07952FD4F6DB'; 
        $this->app_key = '9EB197983B184E0CB64C695DA6FDDB00';
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

    public function createPayment($pay_type, $amount, $currency = 'HKD')
    {
        $now_time = now()->format('Y-m-d H:i:s');
        $fields = [
            'pay_type' => $pay_type,
            'out_trade_no' => $this->getRandStr(20),
            'txcurrcd' => $currency,
            'txamt' => $amount,
            'txdtm' => $now_time,
        ];

        ksort($fields);
        $fields_string = http_build_query($fields);
        $sign = strtoupper(md5($fields_string . $this->app_key));

        $response = Http::withHeaders([
            'X-QF-APPCODE' => $this->app_code,
            'X-QF-SIGN' => $sign,
        ])->post($this->url . '/trade/v1/payment', $fields);

        return $response->json();
    }
}
