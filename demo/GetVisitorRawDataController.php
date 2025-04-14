<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

class GetVisitorRawDataController extends Controller
{
    public function __construct()
    {
        $this->this_epoch_time = time();
    }

    public function getData(Request $request)
    {
        // Extract data from JSON request
        $requestData = $request->all();
        Log::channel('visitor-raw-data')->info(json_encode($requestData));

        // Extract relevant data from JSON
        $miscellaneousData = $requestData['miscellaneous'] ?? [];
        $scriptAttributesData = $requestData['scriptAttributes'] ?? [];
        $cookiesData = $requestData['cookies'] ?? [];

        $queue_room_id          =   base64_decode($requestData['scriptAttributes']['intercept']);
        // $queue_room_id          =   $requestData['scriptAttributes']['intercept'];



        $browser_session_id = $cookiesData['qSessionId'] ?? null;
        $customer_id = $scriptAttributesData['cid'] ?? null;
        $ip_address = $miscellaneousData['ipaddress'] ?? null;
        $browser_url = $miscellaneousData['encodedURL'] ?? null;
        $browser_time_zone = $miscellaneousData['timeZone'] ?? null;
        $browser_language = $miscellaneousData['browserLanguage'] ?? null;
        $browserTime = $miscellaneousData['browserTime'] ?? null;
        $json_data = json_encode($requestData);

        // Check if the customer exists and is verified
        $user = DB::table('queuetb_users')->where('id', $customer_id)->where('verify', 1)->first();

        if ($user) {
            // Proceed with logging data
            $visitor_data = [
                'browser_session_id' => $browser_session_id,
                'customer_id' => $customer_id,
                'ip_address' => $ip_address,
                'browser_url' => $browser_url,
                'browser_time_zone' => $browser_time_zone,
                'browser_language' => $browser_language,
                'browser_time' => $browserTime,
                'json_data' => $json_data,
            ];


            $this->insertIntoDb('queuetb_raw_user_request_log', $visitor_data);

            // Check if the customer has a valid plan
            $has_customer_valid_plan = $this->checkCustomerValidPlan($customer_id);

            if ($has_customer_valid_plan) {

                $query = "SELECT id,currunt_traffic_count,max_traffic_visitor,target_url,is_prequeue,prequeue_starttime,start_time_epoch,is_started  FROM queuetb_queue_room WHERE id = " . $queue_room_id;
                $queue_room_data = DB::select($query);



                $currunt_traffic_count =  (int)$queue_room_data[0]->currunt_traffic_count;
                $max_traffic_visitor =  (int)$queue_room_data[0]->max_traffic_visitor;
                // Check if the session is already in queue operations  
                $session_in_operations = DB::table('queuetb_raw_queue_operations')->where('browser_session_id', $browser_session_id)->first();

                if (!$session_in_operations) {

                    //check if user is accessing from same url mentioned in queue rooms
                    $domain_url  =  $scriptAttributesData['domain'];
                    $data = $this->checkUserDomain($domain_url);

                    if ($data) {


                        $query = "SELECT qr.id, qr.target_url, qr.bypass_template_id, qr.enable_bypass, bp_tamp.bypass_url,qr.queue_room_icon,qr.is_started,qr.is_prequeue,qr.prequeue_starttime,qr.start_time_epoch
                                FROM queuetb_queue_room as qr
                                INNER JOIN queue_room_template as qr_tamp ON qr.queue_room_template_id   = qr_tamp.id
                                INNER JOIN bypass_template as bp_tamp ON qr.bypass_template_id = bp_tamp.id
                                WHERE (qr.parent_user_id = " . $customer_id . ") 
                                AND (qr.id  = " . $queue_room_id . ")";
                        $queue_room_data = DB::select($query);



                        $is_enable_byepass = $queue_room_data[0]->enable_bypass;

                        $bypass_url = $queue_room_data[0]->bypass_url;
                        $target_url = $queue_room_data[0]->target_url;
                        $queue_room_image_url = env('APP_URL') . "public/images/" . $queue_room_data[0]->queue_room_icon;


                        $is_started =  (int)$queue_room_data[0]->is_started;
                        $is_prequeue =  (int)$queue_room_data[0]->is_prequeue;
                        $prequeue_starttime =  (int)$queue_room_data[0]->prequeue_starttime;
                        $start_time_epoch =  (int)$queue_room_data[0]->start_time_epoch;
                        $main_preque_time =  $start_time_epoch - ($prequeue_starttime * 60);
                        $main_preque_time = (int)$main_preque_time;

                        if (($is_prequeue == 1) && ($this->this_epoch_time > $main_preque_tim) && ($this->this_epoch_time < $start_time_epoch) && ($is_started == 0)) {

                            // Insert data into pre queue operations                                                
                            $visitor_data['status'] = 4;
                            $visitor_data['last_updated_epoch'] =  $this->this_epoch_time;
                            $visitor_data['room_id'] =  $queue_room_id;
                            $this->insertIntoDb('queuetb_raw_queue_operations', $visitor_data);


                            $query = "UPDATE queuetb_raw_user_request_log SET status = " . $visitor_data['status'] . " WHERE browser_session_id = " . $browser_session_id;
                            DB::select($query);


                            //$htmlBody = View::make($blade_file, $blade_file_data)->render();
                            $htmlBody = "";
                            $final_response = [
                                'status' => 4,
                                'message' => 'Prequeue has been started',
                                'htmlBody' => $htmlBody,
                            ];
                            $response_code = 200;
                        } else {

                            // Insert data into queue operations                                                
                            $visitor_data['status'] = 3;
                            $visitor_data['last_updated_epoch'] =  $this->this_epoch_time;
                            $visitor_data['room_id'] =  $queue_room_id;
                            $this->insertIntoDb('queuetb_raw_queue_operations', $visitor_data);


                            $query = "UPDATE queuetb_raw_user_request_log SET status = " . $visitor_data['status'] . " WHERE browser_session_id = " . $browser_session_id;
                            DB::select($query);

                            $query = "UPDATE queuetb_queue_room SET currunt_traffic_count  = ( currunt_traffic_count + 1 )  WHERE id = " . $queue_room_id;
                            DB::select($query);

                            if (($is_enable_byepass) && ($browser_url == $bypass_url)) {
                                // $cookie1 = Cookie::forget('qProcessOp');
                                // $cookie2 = Cookie::forget('qSessionId');

                                $visitor_data['status'] = 2;

                                $blade_file = "bypass-tamplates.bypass_access_code";
                                $blade_file_data = [
                                    'room_id' =>  $queue_room_id,
                                    'customer_id' => $customer_id,
                                    'queue_room_image_url' => $queue_room_image_url,
                                    'redirect_url' => $bypass_url,
                                    'browser_session_id' => $browser_session_id,
                                    'target_url' => $target_url,

                                ];
                                $htmlBody = View::make($blade_file, $blade_file_data)->render();

                                $final_response = [
                                    'status' => 2,
                                    'message' => 'Visitor has bypass access needed passcode access',
                                    'qProcessOp' => true,
                                    'checkByepassStatus' => true,
                                    'htmlBody' => $htmlBody,
                                ];


                                $query = "UPDATE queuetb_queue_room SET currunt_traffic_count  = ( currunt_traffic_count - 1 )  WHERE id = " . $queue_room_id;
                                DB::select($query);


                                $query = "UPDATE queuetb_raw_queue_operations SET status = " . $visitor_data['status'] . " WHERE browser_session_id = " . $browser_session_id;
                                DB::select($query);


                                $query = "UPDATE queuetb_raw_user_request_log SET status = " . $visitor_data['status'] . " WHERE browser_session_id = " . $browser_session_id;
                                DB::select($query);
                            } else if ($max_traffic_visitor > $currunt_traffic_count) {

                                $cookie1 = Cookie::forget('qProcessOp');
                                $cookie2 = Cookie::forget('qSessionId');
                                $cookie3 = Cookie::forget('checkByepassStatus');

                                $target_url = $queue_room_data[0]->target_url;
                                $visitor_data['status'] = 10;
                                $final_response = [
                                    'status' => 10,
                                    'message' => 'Page has not traffic , redirecting to site',
                                    'qProcessOp' => false,
                                    'redirectionUrl' => $target_url
                                ];
                                $response_code = 200;


                                $query = "UPDATE queuetb_raw_queue_operations SET status = " . $visitor_data['status'] . " WHERE browser_session_id = " . $browser_session_id;
                                DB::select($query);


                                $query = "UPDATE queuetb_raw_user_request_log SET status = " . $visitor_data['status'] . " WHERE browser_session_id = " . $browser_session_id;
                                DB::select($query);
                            } else {
                                $final_response = [
                                    'status' => 'success',
                                    'message' => 'Request added for further queue operations',
                                    'qProcessOp' => true
                                ];
                            }
                            $response_code = 200;
                        }
                    } else {
                        $cookie1 = Cookie::forget('qProcessOp');
                        $cookie2 = Cookie::forget('qSessionId');
                        $cookie3 = Cookie::forget('checkByepassStatus');
                        $final_response = [
                            'status' => 'error',
                            'message' => 'Customer domain is not valid as per queue rooms',
                        ];
                        $response_code = 403;
                    }
                } else {

                    $queue_room_id          =   base64_decode($requestData['scriptAttributes']['intercept']);
                    // $queue_room_id          =   $requestData['scriptAttributes']['intercept'];

                    $query = "SELECT qr.id, qr.target_url, qr.bypass_template_id, qr.enable_bypass, bp_tamp.bypass_url,qr.queue_room_icon
                            FROM queuetb_queue_room as qr
                            INNER JOIN queue_room_template as qr_tamp ON qr.queue_room_template_id   = qr_tamp.id
                            INNER JOIN bypass_template as bp_tamp ON qr.bypass_template_id = bp_tamp.id
                            WHERE (qr.parent_user_id = " . $customer_id . ") 
                            AND (qr.id  = " . $queue_room_id . ")";
                    $queue_room_data = DB::select($query);



                    $is_enable_byepass = $queue_room_data[0]->enable_bypass;

                    $bypass_url = $queue_room_data[0]->bypass_url;
                    $target_url = $queue_room_data[0]->target_url;
                    $queue_room_image_url = env('APP_URL') . "public/images/" . $queue_room_data[0]->queue_room_icon;

                    if (($is_enable_byepass) && ($browser_url == $bypass_url)) {
                        // $cookie1 = Cookie::forget('qProcessOp');
                        // $cookie2 = Cookie::forget('qSessionId');

                        $visitor_data['status'] = 2;

                        $blade_file = "bypass-tamplates.bypass_access_code";
                        $blade_file_data = [
                            'room_id' =>  $queue_room_id,
                            'customer_id' => $customer_id,
                            'queue_room_image_url' => $queue_room_image_url,
                            'redirect_url' => $bypass_url,
                            'browser_session_id' => $browser_session_id,
                            'target_url' => $target_url,

                        ];
                        $htmlBody = View::make($blade_file, $blade_file_data)->render();

                        $final_response = [
                            'status' => 2,
                            'message' => 'Visitor has bypass access needed passcode access',
                            'qProcessOp' => true,
                            'htmlBody' => $htmlBody,
                        ];


                        $query = "UPDATE queuetb_queue_room SET currunt_traffic_count  = ( currunt_traffic_count - 1 )  WHERE id = " . $queue_room_id;
                        DB::select($query);
                    } else {
                        $final_response = [
                            'status' => 'success',
                            'message' => 'Session ID is already in process',
                            'qProcessOp' => true
                        ];
                    }
                    $response_code = 200;
                }
            } else {

                $cookie1 = Cookie::forget('qProcessOp');
                $cookie2 = Cookie::forget('qSessionId');
                $cookie3 = Cookie::forget('checkByepassStatus');
                $final_response = [
                    'status' => 'error',
                    'message' => 'Customer does not have a valid plan',
                ];
                $response_code = 404;
            }
        } else {
            // Customer does not exist or is not authorized

            $cookie1 = Cookie::forget('qProcessOp');
            $cookie2 = Cookie::forget('qSessionId');
            $cookie3 = Cookie::forget('checkByepassStatus');


            $final_response = [
                'status' => 'error',
                'message' => 'Customer does not exist or is not authorized',
            ];
            $response_code = 400;
        }

        $this->addResponseLog($browser_session_id, $final_response, $response_code);

        if (isset($cookie1) && isset($cookie2)) {

            return response()->json($final_response, $response_code)
                ->withCookie($cookie1)
                ->withCookie($cookie2)
                ->withCookie($cookie3);
        } else {

            return response()->json($final_response, $response_code);
        }
    }

    public function insertIntoDb($tableName, $data)
    {
        return DB::table($tableName)->insert($data);
    }

    public function addResponseLog($session_id, $response, $response_code)
    {
        $log_message = "Response for session_id: " . json_encode($session_id) . " code: " . $response_code . " is - " . json_encode($response);
        Log::channel('visitor-raw-data')->info($log_message);
    }

    public function checkCustomerValidPlan($customer_id)
    {
        return true;
    }

    public function checkUserDomain($domain_url)
    {
        $domain_url = parse_url($domain_url);
        $domain_url = $domain_url['host'];

        $query = "SELECT id 
                    FROM queue_room_template 
                    WHERE parent_user_id = 27
                    AND SUBSTRING_INDEX(SUBSTRING_INDEX(input_url, '://', -1), '/', 1) = '" . $domain_url . "'";



        $data = DB::select($query);
        return $data;
    }
}
