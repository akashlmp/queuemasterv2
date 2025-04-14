<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\View;

use App\Models\QueueRoom;
use App\Models\QueuedbUser;
use App\Helpers\Logics;

class GetVisitorRawDataController extends Controller
{
    public function __construct()
    {
        $this->this_epoch_time = time();
        $this->def_lang = "en";
    }

    public function insertIntoDb($tableName, $data)
    {
        return DB::table($tableName)->insert($data);
    }

    public function checkUserDomain($domain_url)
    {
        $domain_url = parse_url($domain_url);
        $domain_url = $domain_url['host'];
     
        $query = "SELECT id 
                    FROM queue_room_template 
                    WHERE  SUBSTRING_INDEX(SUBSTRING_INDEX(input_url, '://', -1), '/', 1) = '" . $domain_url . "'";
        $data = DB::select($query);
        //Time: 0.3ms  
        return $data;
    }

    public function evaluateCondition($rule, $visitorAccessUrlDomain, $visitorAccessUrlPagePath, $visitorAccessUrl)
    {
        $condition_place = $rule['condition_place'];
        $condition = $rule['condition'];
        $value = $rule['value'];

        switch ($condition_place) {
            case 'HOST_NAME':
                switch ($condition) {
                    case 'EQUALS':
                        return ($visitorAccessUrlDomain === $value);
                    case 'DOES_NOT_EQUAL':
                        return ($visitorAccessUrlDomain !== $value);
                    case 'CONTAINS':
                        return strpos($visitorAccessUrlDomain, $value) !== false;
                    case 'DOES_NOT_CONTAIN':
                        return strpos($visitorAccessUrlDomain, $value) === false;
                    default:
                        return false;
                }
            case 'PAGE_PATH':
                switch ($condition) {
                    case 'EQUALS':
                        return ($visitorAccessUrlPagePath === $value);
                    case 'DOES_NOT_EQUAL':
                        return ($visitorAccessUrlPagePath !== $value);
                    case 'CONTAINS':
                        return strpos($visitorAccessUrlPagePath, $value) !== false;
                    case 'DOES_NOT_CONTAIN':
                        return strpos($visitorAccessUrlPagePath, $value) === false;
                    default:
                        return false;
                }
            case 'PAGE_URL':
                switch ($condition) {
                    case 'EQUALS':
                        return ($visitorAccessUrl === $value);
                    case 'DOES_NOT_EQUAL':
                        return ($visitorAccessUrl !== $value);
                    case 'CONTAINS':
                        return strpos($visitorAccessUrl, $value) !== false;
                    case 'DOES_NOT_CONTAIN':
                        return strpos($visitorAccessUrl, $value) === false;
                    default:
                        return false;
                }
            default:
                return false;
        }
    }

    public function getData(Request $request)
    {
        $data = $request->all();
        // Log::channel('visitor-row-data')->info($data);
        
        $miscellaneousData = $data['miscellaneous'] ?? [];
        $scriptAttributesData = $data['scriptAttributes'] ?? [];
        $cookiesData = $data['cookies'] ?? [];
        
        $queueRoomId = base64_decode($scriptAttributesData['intercept']);
        // $queueRoomId = $scriptAttributesData['intercept'];

        $browserSessionId = $cookiesData['qSessionId'] ?? null;
        $customerId = $scriptAttributesData['cid'] ?? null;
        $ipAddress = $miscellaneousData['ipaddress'] ?? null;
        $deviceId = $miscellaneousData['deviceid'] ?? null;
        $browserUrl = $miscellaneousData['encodedURL'] ?? null;
        $browserTimeZone = $miscellaneousData['timeZone'] ?? null;
        $browserLanguage = $miscellaneousData['browserLanguage'] ?? null;
        $browserTime = $miscellaneousData['browserTime'] ?? null;

        $query = "SELECT qr.id, qr.target_url, bp_tamp.bypass_url, qr.queue_room_icon FROM queuetb_queue_room AS qr LEFT JOIN bypass_template AS bp_tamp ON qr.bypass_template_id = bp_tamp.id WHERE qr.parent_user_id = :customer_id AND qr.id = :queue_room_id";
        
        $getRoomData = DB::selectOne($query, ['customer_id' => $customerId, 'queue_room_id' => $queueRoomId]);
        //         //Time: 0.41ms 
        // return $getRoomData;
        $bypassURL = $getRoomData->bypass_url;
        $targetURL = $getRoomData->target_url.'?start=true';
        $queueRoomImageURL = !empty($getRoomData->queue_room_icon)?env('APP_URL')."public/images/".$getRoomData->queue_room_icon:env('APP_URL')."public/images/queue.png";

        /** This function using for, checking the user is already visit our site or not */
        $checkDeviceId = DB::table('queuetb_raw_queue_operations')->select('device_id', 'status', 'customer_id')->where([['device_id', $deviceId], ['room_id', $queueRoomId]])->first();
        //Time: 0.42ms
        if (!empty($checkDeviceId) && $checkDeviceId->device_id == $deviceId && $checkDeviceId->customer_id == $customerId)
        {
         
             if($checkDeviceId->status == 10 || $checkDeviceId->status == 6)
             {
                 $cookie1 = Cookie::forget('qProcessOp');
                 $cookie2 = Cookie::forget('qSessionId');
                 $cookie3 = Cookie::forget('checkByepassStatus');
        
                $final_response = [
                    'status' => 10,
                    'message' => 'sattus 10',
                    'qProcessOp' => false,
                    'redirectionUrl' => $targetURL
                ];

                $response_code = 200;

             } else {
                //   $modifiedHtml = Logics::showPriorityAccessPage($queueRoomId, $queueRoomImageURL, $browserSessionId, $customerId, $targetURL, $bypassURL);

                  $final_response = [
                    'status' => 'success',
                    'message' => 'status',
                    'qProcessOp' => true
                ];
                $response_code = 200;
              }   
        }else{
            $user = DB::table('queuetb_users')->where('id', $customerId)->where('verify', 1)->first();
           
            if ($user) {
                /** Proceed with logging data */
                $visitorData = [
                    'browser_session_id' => $browserSessionId,
                    'customer_id' => $customerId,
                    'ip_address' => $ipAddress,
                    'browser_url' => $browserUrl,
                    'browser_time_zone' => $browserTimeZone,
                    'browser_language' => $browserLanguage,
                    'browser_time' => $browserTime,
                    'json_data' => json_encode($data),
                ];
    
                $getQueueRoom = QueueRoom::select('id', 'in_queue', 'currunt_traffic_count', 'max_traffic_visitor', 'target_url', 'is_prequeue', 'prequeue_starttime', 'start_time_epoch', 'is_started', 'bypass_template_id')->where('id', $queueRoomId)->first();
                //Time: 0.39ms
                
                if (!empty($getQueueRoom))
                {
                    $currentTrafficCount = ((int)$getQueueRoom->currunt_traffic_count ?? 0);
                    $maxTrafficVisitor = ((int)$getQueueRoom->max_traffic_visitor ?? 0);
                }
    
                $sessionInOperations = DB::table('queuetb_raw_queue_operations')->where('browser_session_id', $browserSessionId)->first();
                //Time: 0.42ms
    
                // try {
                    if (!$sessionInOperations)
                    {
                        $domainUrl = $scriptAttributesData['domain'];
                        $dataUrl = self::checkUserDomain($domainUrl);
        
                        if (sizeof($dataUrl))
                        {
                            $query = "
                                SELECT 
                                    qr.id,
                                    qr.target_url,
                                    qr.bypass_template_id,
                                    qr.enable_bypass,
                                    bp_tamp.bypass_url,
                                    qr.queue_room_icon,
                                    qr.is_started,
                                    qr.is_prequeue,
                                    qr.prequeue_starttime,
                                    qr.start_time_epoch,
                                    qr_design.default_language,
                                    qr.queue_room_name,
                                    qr.queue_room_template_id,
                                    qr.sms_notice_tempid,
                                    qr.email_notice_tempid,
                                    qr_tamp.is_advance_setting,
                                    qr_tamp.advance_setting_rules,
                                    qr_tamp.input_url,
                                    qr.max_traffic_visitor,
                                    qr.currunt_traffic_count,
                                    qr.is_ended,
                                    qr.current_space
                                FROM queuetb_queue_room AS qr
                                INNER JOIN queue_room_template AS qr_tamp ON qr.queue_room_template_id = qr_tamp.id
                                LEFT JOIN bypass_template AS bp_tamp ON qr.bypass_template_id = bp_tamp.id
                                INNER JOIN queuetb_design_template AS qr_design ON qr.queue_room_design_tempid = qr_design.id
                                WHERE qr.parent_user_id = :customer_id
                                AND qr.id = :queue_room_id";
        
                            $queueRoomData = DB::selectOne($query, ['customer_id' => $customerId, 'queue_room_id' => $queueRoomId]);
                            
                            if (isset($queueRoomData->default_language))
                            {
                                $defLang = $queueRoomData->default_language;
                            } else {
                                $defLang = $this->def_lang;
                            }
                            
                            $currentTrafficCount = (int)$queueRoomData->currunt_traffic_count ?? 0;
                            $maxTrafficVisitor = (int)$queueRoomData->max_traffic_visitor ?? 0;
                            $isEnableBypass = $queueRoomData->enable_bypass;
                            $isStarted = (int)$queueRoomData->is_started;
                            $isPrequeue = (int)$queueRoomData->is_prequeue;
                            $prequeueStartTime = (int)$queueRoomData->prequeue_starttime;
                            $startTimeEpoch = (int)$queueRoomData->start_time_epoch;
                            $mainPrequeTime = (int)($startTimeEpoch - ($prequeueStartTime * 60));
        
                            /** Here we remove the if condition which is define as if (false) */
                            $visitorData['status'] = 3;
                            $visitorData['device_id'] = $deviceId;
                            $visitorData['last_updated_epoch'] = $this->this_epoch_time;
                            $visitorData['room_id'] = $queueRoomId;
                            // DB::table('queuetb_raw_queue_operations')->insert($visitorData); // Time: 5.51ms 
                            $getInsertId = DB::table('queuetb_raw_queue_operations')->insertGetId($visitorData); // Time: 5.51ms 
        
                            DB::table('queuetb_raw_queue_operations')->where('id', $getInsertId)->update(['queue_serial_number_id' => $getInsertId]);
                            $query = "UPDATE queuetb_queue_room SET currunt_traffic_count = currunt_traffic_count + 1 WHERE id = :queue_room_id";
                            DB::update($query, ['queue_room_id' => $queueRoomId]); // Time: 5.36ms 

                            if ($isEnableBypass && $browserUrl == $bypassURL) {
                                // $bladeFile = "bypass-templates.bypass_access_code";
                                // $bladeFileData = [
                                //     'room_id' =>  $queueRoomId,
                                //     'customer_id' => $customerId,
                                //     'queue_room_image_url' => $queueRoomImageURL,
                                //     'redirect_url' => $bypassURL,
                                //     'browser_session_id' => $browserSessionId,
                                //     'target_url' => $targetURL,
                                // ];
        
                                // $query = "SELECT htm_data FROM in_line_tamplates WHERE type = :type AND room_id = :room_id AND language = :language";
        
                                // $lang_data = DB::selectOne($query, [
                                //     'type' => 'priority_access_page',
                                //     'room_id' => $queueRoomId,
                                //     'language' => $defLang
                                // ]);
        
                                $modifiedHtml = Logics::showPriorityAccessPage($queueRoomId, $queueRoomImageUrl, $browserSessionId, $customerId, $targetURL, $bypassURL);

                                $final_response = [
                                    'status' => 2,
                                    'message' => 'status 2',
                                    'qProcessOp' => true,
                                    'checkByepassStatus' => true,
                                    'htmlBody' => $modifiedHtml,
                                ];
        
                                // Update currunt_traffic_count
                                $query = "UPDATE queuetb_queue_room SET currunt_traffic_count = currunt_traffic_count - 1 WHERE id = :queue_room_id";
                                DB::update($query, ['queue_room_id' => $queueRoomId]);
        
                                // Update status in queuetb_raw_queue_operations
                                $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
                                DB::update($query, [
                                    'status' => 2,
                                    'browser_session_id' => $browserSessionId
                                ]);
        
                              
                            }else if ($isPrequeue == 1) {
                                $prequeueStartTime = (int)$queueRoomData->prequeue_starttime;
                                $startTimeEpoch = (int)$queueRoomData->start_time_epoch;
                                $checkStartCondition = Logics::checkStartCondition($prequeueStartTime, $startTimeEpoch);

                                if ($checkStartCondition) {
                                    QueueRoom::where('id', $queueRoomId)->update(['is_prequeue' => 0]);
                                    $cookie1 = Cookie::forget('qProcessOp');
                                    $cookie2 = Cookie::forget('qSessionId');
                                    $cookie3 = Cookie::forget('checkByepassStatus');
                                    $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
                                    DB::update($query, [
                                        'status' => 3,
                                        'browser_session_id' => $browserSessionId
                                    ]); // Time: 6.07ms

                                    $final_response = [
                                        'status' => 'success',
                                        'message' => 'status',
                                        'qProcessOp' => true
                                    ];

                                    $response_code = 200;
                                } else {
                                    $cookie1 = Cookie::forget('preQueue');
                                     $cookie2 = Cookie::forget('qSessionId');
                                     $cookie3 = Cookie::forget('checkByepassStatus');
                                     $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
                                     DB::update($query, [
                                         'status' => 4,
                                         'browser_session_id' => $browserSessionId
                                     ]); // Time: 6.07ms

                                     $final_response = [
                                         'status' => 'success',
                                         'message' => 'status',
                                         'preQueue' => true
                                     ];

                                     $response_code = 200;
                                }
                            } else if ($maxTrafficVisitor > $currentTrafficCount && $getQueueRoom->in_queue == 0 && $queueRoomData->current_space > 0) {
                                $cookie1 = Cookie::forget('qProcessOp');
                                $cookie2 = Cookie::forget('qSessionId');
                                $cookie3 = Cookie::forget('checkByepassStatus');
        
                                $final_response = [
                                    'status' => 10,
                                    'message' => 'sattus 10',
                                    'qProcessOp' => false,
                                    'redirectionUrl' => $targetURL
                                ];
                                $response_code = 200;
        
                                // Update status in queuetb_raw_queue_operations
                                $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
                                DB::update($query, [
                                    'status' => 10,
                                    'browser_session_id' => $browserSessionId
                                ]); // Time: 6.07ms

                                QueueRoom::where('id', $queueRoomId)->decrement('current_space', 1);

                            }else {  
                                $query = "UPDATE queuetb_queue_room SET in_queue = in_queue + 1 WHERE id = :queue_room_id";
                                DB::update($query, ['queue_room_id' => $queueRoomId]); // Time: 5.36ms 

                                $isAdvanceSetting = $queueRoomData->is_advance_setting;
                                $inputURL = $queueRoomData->input_url;
        
                                $parsedUrl = parse_url($browserUrl);
                                $visitorAccessUrlDomain = $parsedUrl['host'] ?? '';
                                $visitorAccessUrlPagePath = $parsedUrl['path'] ?? '/';
        
                                $visitorAccessUrl = $visitorAccessUrlDomain . $visitorAccessUrlPagePath;
                                if ($isAdvanceSetting == 1) {
                                    $advanceSettingRules = json_decode($queueRoomData->advance_setting_rules, true);
                                    // return $advanceSettingRules;die;
                                    $putInQueue = false;
                                    foreach ($advanceSettingRules as $rule) {
                                        // Determine value to check
                                        $valueToCheck = '';
                                        switch ($rule['condition_place']) {
                                            case 'HOST_NAME':
                                                $valueToCheck = $visitorAccessUrlDomain;
                                                break;
                                            case 'PAGE_PATH':
                                                $valueToCheck = $visitorAccessUrlPagePath;
                                                break;
                                            case 'PAGE_URL':
                                                $valueToCheck = $visitorAccessUrl;
                                                break;
                                            default:
                                                continue 2; // Skip unknown condition places
                                        }
                                    
                                        // Evaluate condition
                                        $conditionMet = false;
                                        switch ($rule['condition']) {
                                            case 'EQUALS':
                                                $conditionMet = ($valueToCheck === $rule['value']);
                                                break;
                                            case 'DOES_NOT_EQUAL':
                                                $conditionMet = ($valueToCheck !== $rule['value']);
                                                break;
                                            case 'CONTAINS':
                                                $conditionMet = (strpos($valueToCheck, $rule['value']) !== false);
                                                break;
                                            case 'DOES_NOT_CONTAIN':
                                                $conditionMet = (strpos($valueToCheck, $rule['value']) === false);
                                                break;
                                        }
                                    
                                        // Apply operator (default to OR if no operator specified)
                                        if (isset($rule['operator']) && $rule['operator'] === 'AND' && $rule['operator'] == null) {
                                            $putInQueue = $putInQueue && $conditionMet;
                                            $putInQueue = true;
                                        } else {
                                            $putInQueue = $putInQueue || $conditionMet;
                                            $putInQueue = true;
                                        }
                                    
                                        // Early exit if put_in_queue is true (optimization)
                                        // if ($putInQueue) {
                                        //     break;
                                        // }
                                    }

                                    // return $putInQueue;die;
                                }
        
                                if (!isset($putInQueue) || $putInQueue === true) {
                                    $final_response = [
                                        'status' => 'success',
                                        'message' => 'status',
                                        'qProcessOp' => true
                                    ];

                                    $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
                                    DB::update($query, [
                                        'status' => 3,
                                        'browser_session_id' => $browserSessionId
                                    ]);
                                } else {
                                    // If $putInQueue is explicitly false
                                    
                                    // Update status in queuetb_raw_queue_operations
                                    $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
                                    DB::update($query, [
                                        'status' => 10,
                                        'browser_session_id' => $browserSessionId
                                    ]);
                                
                                    
                                    // Forget cookies
                                    $cookie1 = Cookie::forget('qProcessOp');
                                    $cookie2 = Cookie::forget('qSessionId');
                                    $cookie3 = Cookie::forget('checkByepassStatus');
                                
                                    $final_response = [
                                        'status' => 10,
                                        'message' => 'advance condition failed',
                                        'qProcessOp' => false,
                                        'redirectionUrl' => false
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
                                'message' => 'status',
                            ];
                            $response_code = 403;
                        }
                        
                    } else {
                        $queueRoomId = base64_decode($data['scriptAttributes']['intercept']);
                        // $queueRoomId = $data['scriptAttributes']['intercept'];
                        $query = "SELECT qr.id, qr.target_url, qr.bypass_template_id, qr.enable_bypass, bp_tamp.bypass_url,
                         qr.queue_room_icon, qr_tamp.is_advance_setting, qr_tamp.advance_setting_rules, qr_tamp.input_url FROM queuetb_queue_room AS qr INNER JOIN queue_room_template AS qr_tamp ON qr.queue_room_template_id = qr_tamp.id LEFT JOIN bypass_template AS bp_tamp ON qr.bypass_template_id = bp_tamp.id WHERE qr.parent_user_id = :customer_id AND qr.id = :queue_room_id";
        
                        $queueRoomData = DB::select($query, [
                            'customer_id' => $customerId,
                            'queue_room_id' => $queueRoomId,
                        ]); // Time: 1.22ms 
        
                        $isEnableBypass = $queueRoomData->enable_bypass??null;
                        $bypassURL = $queueRoomData->bypass_url??null;
                        $targetURL = $queueRoomData->target_url.'?start=true'??null;
                        $queueRoomImageUrl = env('APP_URL')."public/images/".$queueRoomData->queue_room_icon;
        
                        if ($isEnableBypass && $browserUrl == $bypassURL) {
                            $bladeFile = "bypass-templates.bypass_access_code";
                            $bladeFileData = [
                                'room_id' =>  $queueRoomId,
                                'customer_id' => $customerId,
                                'queue_room_image_url' => $queueRoomImageURL,
                                'redirect_url' => $bypassURL,
                                'browser_session_id' => $browserSessionId,
                                'target_url' => $targetURL,
                            ];
        
                            $htmlBody = view::make($bladeFile, $bladeFileData)->rander();
                            $final_response = [
                                'status' => 2,
                                'message' => 'status 2',
                                'qProcessOp' => true,
                                'htmlBody' => $htmlBody
                            ];
        
                            $query = "UPDATE queuetb_queue_room SET currunt_traffic_count = currunt_traffic_count - 1 WHERE id = :queue_room_id";
        
                            DB::update($query, [
                                'queue_room_id' => $queueRoomId,
                            ]);
        
                        } else {
                            $isAdvanceSetting = $queueRoomData->is_advance_setting;
                            $inputURL = $queueRoomData->input_url;
        
                            $parsedUrl = parse_url($browserUrl);
                            $visitorAccessUrlDomain = $parsedUrl['host'] ?? '';
                            $visitorAccessUrlPagePath = $parsedUrl['path'] ?? '/';
        
                            $visitorAccessUrl = $visitorAccessUrlDomain . $visitorAccessUrlPagePath;
                            if ($isAdvanceSetting == 1) {
                                $advanceSettingRules = json_decode($queueRoomData->advance_setting_rules, true);
                                // return $advanceSettingRules;die;
                                $putInQueue = false; // Initialize to false by default
                            
                                foreach ($advanceSettingRules as $rule) {
                                    $conditionOp = isset($rule['operator']) ? $rule['operator'] : 'AND'; // Default to AND if operator not specified
                                    
                                    switch ($conditionOp) {
                                        case 'OR':
                                           
                                            $putInQueue = $putInQueue || self::evaluateCondition($rule, $visitorAccessUrlDomain, $visitorAccessUrlPagePath, $visitorAccessUrl);
                                            break;
                                        case 'AND':
                                        default:
                                            // $putInQueue = $putInQueue && evaluateCondition($rule, $visitorAccessUrlDomain, $visitorAccessUrlPagePath, $visitorAccessUrl);
                                            $putInQueue = $putInQueue && self::evaluateCondition($rule, $visitorAccessUrlDomain, $visitorAccessUrlPagePath, $visitorAccessUrl);
                                            break;
                                    }
                                    
                                    // If already true in OR condition, no need to continue checking
                                    if ($conditionOp == 'OR' && $putInQueue) {
                                        break;
                                    }
                                }
                            }
                            
                            // Prepare final response based on $putInQueue
                            if (isset($putInQueue) && $putInQueue) {
                                $final_response = [
                                    'status' => 'success',
                                    'message' => 'status',
                                    'qProcessOp' => true
                                ];
                            } else {
                                $visitor_data['status'] = 10;
                                $cookie1 = Cookie::forget('qProcessOp');
                                $cookie2 = Cookie::forget('qSessionId');
                                $cookie3 = Cookie::forget('checkByepassStatus');
                                $final_response = [
                                    'status' => $visitor_data['status'],
                                    'message' => 'advance condition failed',
                                    'qProcessOp' => false,
                                    'redirectionUrl' => false
                                ];
                            
                                // Perform DB updates if necessary
                                $query = "UPDATE queuetb_raw_queue_operations SET status = " . $visitor_data['status'] . " WHERE browser_session_id = " . $browserSessionId;
                                DB::select($query);
                            
                            }
                            
                        }
                        $response_code = 200;
                    }
                       
            } else {
                $cookie1 = Cookie::forget('qProcessOp');
                $cookie2 = Cookie::forget('qSessionId');
                $cookie3 = Cookie::forget('checkByepassStatus');
    
                $final_response = [
                    'status' => 'error',
                    'message' => 'status',
                ];
    
                $response_code = 400;
            }
        }
            
        self::addResponseLog($browserSessionId, $final_response, $response_code);
    
        if (isset($cookie1) && isset($cookie2)) {
    
            return response()->json($final_response, $response_code)
                ->withCookie($cookie1)
                ->withCookie($cookie2)
                ->withCookie($cookie3);
        } else {
    
            return response()->json($final_response, $response_code);
        }
    }

    // public function getData(Request $request)
    // {
    //     $data = $request->all();
    //     // Log::channel('visitor-row-data')->info($data);
        
    //     $miscellaneousData = $data['miscellaneous'] ?? [];
    //     $scriptAttributesData = $data['scriptAttributes'] ?? [];
    //     $cookiesData = $data['cookies'] ?? [];
        
    //     $queueRoomId = base64_decode($scriptAttributesData['intercept']);

    //     $browserSessionId = $cookiesData['qSessionId'] ?? null;
    //     $customerId = $scriptAttributesData['cid'] ?? null;
    //     $ipAddress = $miscellaneousData['ipaddress'] ?? null;
    //     $deviceId = $miscellaneousData['deviceid'] ?? null;
    //     $browserUrl = $miscellaneousData['encodedURL'] ?? null;
    //     $browserTimeZone = $miscellaneousData['timeZone'] ?? null;
    //     $browserLanguage = $miscellaneousData['browserLanguage'] ?? null;
    //     $browserTime = $miscellaneousData['browserTime'] ?? null;

    //     $query = "SELECT qr.id, qr.target_url, bp_tamp.bypass_url, qr.queue_room_icon FROM queuetb_queue_room AS qr LEFT JOIN bypass_template AS bp_tamp ON qr.bypass_template_id = bp_tamp.id WHERE qr.parent_user_id = :customer_id AND qr.id = :queue_room_id";
        
    //     $getRoomData = DB::selectOne($query, ['customer_id' => $customerId, 'queue_room_id' => $queueRoomId]);
    //     //         //Time: 0.41ms 
    //     // return $getRoomData;
    //     $bypassURL = $getRoomData->bypass_url;
    //     $targetURL = $getRoomData->target_url;
    //     $queueRoomImageURL = !empty($getRoomData->queue_room_icon)?env('APP_URL')."public/images/".$getRoomData->queue_room_icon:env('APP_URL')."public/images/queue.png";

    //     /** This function using for, checking the user is already visit our site or not */
    //     $checkDeviceId = DB::table('queuetb_raw_queue_operations')->select('device_id', 'status', 'customer_id')->where([['device_id', $deviceId], ['room_id', $queueRoomId]])->first();
    //     //Time: 0.42ms
    //     if (!empty($checkDeviceId) && $checkDeviceId->device_id == $deviceId && $checkDeviceId->customer_id == $customerId)
    //     {
         
    //          if($checkDeviceId->status == 10 || $checkDeviceId->status == 6)
    //          {
    //              $cookie1 = Cookie::forget('qProcessOp');
    //              $cookie2 = Cookie::forget('qSessionId');
    //              $cookie3 = Cookie::forget('checkByepassStatus');
        
    //             $final_response = [
    //                 'status' => 10,
    //                 'message' => 'sattus 10',
    //                 'qProcessOp' => false,
    //                 'redirectionUrl' => $targetURL
    //             ];

    //             $response_code = 200;

    //          } else {
    //             //   $modifiedHtml = Logics::showPriorityAccessPage($queueRoomId, $queueRoomImageURL, $browserSessionId, $customerId, $targetURL, $bypassURL);

    //               $final_response = [
    //                 'status' => 'success',
    //                 'message' => 'status',
    //                 'qProcessOp' => true
    //             ];
    //             $response_code = 200;
    //           }   
    //     }else{
    //         $user = DB::table('queuetb_users')->where('id', $customerId)->where('verify', 1)->first();
           
    //         if ($user) {
    //             /** Proceed with logging data */
    //             $visitorData = [
    //                 'browser_session_id' => $browserSessionId,
    //                 'customer_id' => $customerId,
    //                 'ip_address' => $ipAddress,
    //                 'browser_url' => $browserUrl,
    //                 'browser_time_zone' => $browserTimeZone,
    //                 'browser_language' => $browserLanguage,
    //                 'browser_time' => $browserTime,
    //                 'json_data' => json_encode($data),
    //             ];
    
    //             $getQueueRoom = QueueRoom::select('id', 'in_queue', 'currunt_traffic_count', 'max_traffic_visitor', 'target_url', 'is_prequeue', 'prequeue_starttime', 'start_time_epoch', 'is_started', 'bypass_template_id')->where('id', $queueRoomId)->first();
    //             //Time: 0.39ms
                
    //             if (!empty($getQueueRoom))
    //             {
    //                 $currentTrafficCount = ((int)$getQueueRoom->currunt_traffic_count ?? 0);
    //                 $maxTrafficVisitor = ((int)$getQueueRoom->max_traffic_visitor ?? 0);
    //             }
    
    //             $sessionInOperations = DB::table('queuetb_raw_queue_operations')->where('browser_session_id', $browserSessionId)->first();
    //             //Time: 0.42ms
    
    //             // try {
    //                 if (!$sessionInOperations)
    //                 {
    //                     $domainUrl = $scriptAttributesData['domain'];
    //                     $dataUrl = self::checkUserDomain($domainUrl);
        
    //                     if (sizeof($dataUrl))
    //                     {
    //                         $query = "
    //                             SELECT 
    //                                 qr.id,
    //                                 qr.target_url,
    //                                 qr.bypass_template_id,
    //                                 qr.enable_bypass,
    //                                 bp_tamp.bypass_url,
    //                                 qr.queue_room_icon,
    //                                 qr.is_started,
    //                                 qr.is_prequeue,
    //                                 qr.prequeue_starttime,
    //                                 qr.start_time_epoch,
    //                                 qr_design.default_language,
    //                                 qr.queue_room_name,
    //                                 qr.queue_room_template_id,
    //                                 qr.sms_notice_tempid,
    //                                 qr.email_notice_tempid,
    //                                 qr_tamp.is_advance_setting,
    //                                 qr_tamp.advance_setting_rules,
    //                                 qr_tamp.input_url,
    //                                 qr.max_traffic_visitor,
    //                                 qr.currunt_traffic_count,
    //                                 qr.is_ended
    //                             FROM queuetb_queue_room AS qr
    //                             INNER JOIN queue_room_template AS qr_tamp ON qr.queue_room_template_id = qr_tamp.id
    //                             LEFT JOIN bypass_template AS bp_tamp ON qr.bypass_template_id = bp_tamp.id
    //                             INNER JOIN queuetb_design_template AS qr_design ON qr.queue_room_design_tempid = qr_design.id
    //                             WHERE qr.parent_user_id = :customer_id
    //                             AND qr.id = :queue_room_id";
        
    //                         $queueRoomData = DB::selectOne($query, ['customer_id' => $customerId, 'queue_room_id' => $queueRoomId]);
                            
    //                         if (isset($queueRoomData->default_language))
    //                         {
    //                             $defLang = $queueRoomData->default_language;
    //                         } else {
    //                             $defLang = $this->def_lang;
    //                         }
                            
    //                         $currentTrafficCount = (int)$queueRoomData->currunt_traffic_count ?? 0;
    //                         $maxTrafficVisitor = (int)$queueRoomData->max_traffic_visitor ?? 0;
    //                         $isEnableBypass = $queueRoomData->enable_bypass;
    //                         $isStarted = (int)$queueRoomData->is_started;
    //                         $isPrequeue = (int)$queueRoomData->is_prequeue;
    //                         $prequeueStartTime = (int)$queueRoomData->prequeue_starttime;
    //                         $startTimeEpoch = (int)$queueRoomData->start_time_epoch;
    //                         $mainPrequeTime = (int)($startTimeEpoch - ($prequeueStartTime * 60));
        
    //                         /** Here we remove the if condition which is define as if (false) */
    //                         $visitorData['status'] = 3;
    //                         $visitorData['device_id'] = $deviceId;
    //                         $visitorData['last_updated_epoch'] = $this->this_epoch_time;
    //                         $visitorData['room_id'] = $queueRoomId;
    //                         DB::table('queuetb_raw_queue_operations')->insert($visitorData); // Time: 5.51ms 
        
    //                         $query = "UPDATE queuetb_queue_room SET currunt_traffic_count = currunt_traffic_count + 1 WHERE id = :queue_room_id";
    //                         DB::update($query, ['queue_room_id' => $queueRoomId]); // Time: 5.36ms 

    //                         if ($isEnableBypass && $browserUrl == $bypassURL) {
    //                             // $bladeFile = "bypass-templates.bypass_access_code";
    //                             // $bladeFileData = [
    //                             //     'room_id' =>  $queueRoomId,
    //                             //     'customer_id' => $customerId,
    //                             //     'queue_room_image_url' => $queueRoomImageURL,
    //                             //     'redirect_url' => $bypassURL,
    //                             //     'browser_session_id' => $browserSessionId,
    //                             //     'target_url' => $targetURL,
    //                             // ];
        
    //                             // $query = "SELECT htm_data FROM in_line_tamplates WHERE type = :type AND room_id = :room_id AND language = :language";
        
    //                             // $lang_data = DB::selectOne($query, [
    //                             //     'type' => 'priority_access_page',
    //                             //     'room_id' => $queueRoomId,
    //                             //     'language' => $defLang
    //                             // ]);
        
    //                             $modifiedHtml = Logics::showPriorityAccessPage($queueRoomId, $queueRoomImageUrl, $browserSessionId, $customerId, $targetURL, $bypassURL);

    //                             $final_response = [
    //                                 'status' => 2,
    //                                 'message' => 'status 2',
    //                                 'qProcessOp' => true,
    //                                 'checkByepassStatus' => true,
    //                                 'htmlBody' => $modifiedHtml,
    //                             ];
        
    //                             // Update currunt_traffic_count
    //                             $query = "UPDATE queuetb_queue_room SET currunt_traffic_count = currunt_traffic_count - 1 WHERE id = :queue_room_id";
    //                             DB::update($query, ['queue_room_id' => $queueRoomId]);
        
    //                             // Update status in queuetb_raw_queue_operations
    //                             $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
    //                             DB::update($query, [
    //                                 'status' => 2,
    //                                 'browser_session_id' => $browserSessionId
    //                             ]);
        
                              
    //                         }else if ($isPrequeue == 1) {
    //                             $cookie1 = Cookie::forget('preQueue');
    //                             $cookie2 = Cookie::forget('qSessionId');
    //                             $cookie3 = Cookie::forget('checkByepassStatus');
    //                             $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
    //                             DB::update($query, [
    //                                 'status' => 4,
    //                                 'browser_session_id' => $browserSessionId
    //                             ]); // Time: 6.07ms

    //                             $final_response = [
    //                                 'status' => 'success',
    //                                 'message' => 'status',
    //                                 'preQueue' => true
    //                             ];

    //                             $response_code = 200;
    //                         } else if ($maxTrafficVisitor > $currentTrafficCount && $getQueueRoom->in_queue == 0) {
    //                             $cookie1 = Cookie::forget('qProcessOp');
    //                             $cookie2 = Cookie::forget('qSessionId');
    //                             $cookie3 = Cookie::forget('checkByepassStatus');
        
    //                             $final_response = [
    //                                 'status' => 10,
    //                                 'message' => 'sattus 10',
    //                                 'qProcessOp' => false,
    //                                 'redirectionUrl' => $targetURL
    //                             ];
    //                             $response_code = 200;
        
    //                             // Update status in queuetb_raw_queue_operations
    //                             $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
    //                             DB::update($query, [
    //                                 'status' => 10,
    //                                 'browser_session_id' => $browserSessionId
    //                             ]); // Time: 6.07ms

    //                         }else {  
    //                             $query = "UPDATE queuetb_queue_room SET in_queue = in_queue + 1 WHERE id = :queue_room_id";
    //                             DB::update($query, ['queue_room_id' => $queueRoomId]); // Time: 5.36ms 

    //                             $isAdvanceSetting = $queueRoomData->is_advance_setting;
    //                             $inputURL = $queueRoomData->input_url;
        
    //                             $parsedUrl = parse_url($browserUrl);
    //                             $visitorAccessUrlDomain = $parsedUrl['host'] ?? '';
    //                             $visitorAccessUrlPagePath = $parsedUrl['path'] ?? '/';
        
    //                             $visitorAccessUrl = $visitorAccessUrlDomain . $visitorAccessUrlPagePath;
    //                             if ($isAdvanceSetting == 1) {
    //                                 $advanceSettingRules = json_decode($queueRoomData->advance_setting_rules, true);
    //                                 // return $advanceSettingRules;die;
    //                                 $putInQueue = false;
    //                                 foreach ($advanceSettingRules as $rule) {
    //                                     // Determine value to check
    //                                     $valueToCheck = '';
    //                                     switch ($rule['condition_place']) {
    //                                         case 'HOST_NAME':
    //                                             $valueToCheck = $visitorAccessUrlDomain;
    //                                             break;
    //                                         case 'PAGE_PATH':
    //                                             $valueToCheck = $visitorAccessUrlPagePath;
    //                                             break;
    //                                         case 'PAGE_URL':
    //                                             $valueToCheck = $visitorAccessUrl;
    //                                             break;
    //                                         default:
    //                                             continue 2; // Skip unknown condition places
    //                                     }
                                    
    //                                     // Evaluate condition
    //                                     $conditionMet = false;
    //                                     switch ($rule['condition']) {
    //                                         case 'EQUALS':
    //                                             $conditionMet = ($valueToCheck === $rule['value']);
    //                                             break;
    //                                         case 'DOES_NOT_EQUAL':
    //                                             $conditionMet = ($valueToCheck !== $rule['value']);
    //                                             break;
    //                                         case 'CONTAINS':
    //                                             $conditionMet = (strpos($valueToCheck, $rule['value']) !== false);
    //                                             break;
    //                                         case 'DOES_NOT_CONTAIN':
    //                                             $conditionMet = (strpos($valueToCheck, $rule['value']) === false);
    //                                             break;
    //                                     }
                                    
    //                                     // Apply operator (default to OR if no operator specified)
    //                                     if (isset($rule['operator']) && $rule['operator'] === 'AND' && $rule['operator'] == null) {
    //                                         $putInQueue = $putInQueue && $conditionMet;
    //                                         $putInQueue = true;
    //                                     } else {
    //                                         $putInQueue = $putInQueue || $conditionMet;
    //                                         $putInQueue = true;
    //                                     }
                                    
    //                                     // Early exit if put_in_queue is true (optimization)
    //                                     // if ($putInQueue) {
    //                                     //     break;
    //                                     // }
    //                                 }

    //                                 // return $putInQueue;die;
    //                             }
        
    //                             if (!isset($putInQueue) || $putInQueue === true) {
    //                                 $final_response = [
    //                                     'status' => 'success',
    //                                     'message' => 'status',
    //                                     'qProcessOp' => true
    //                                 ];

    //                                 $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
    //                                 DB::update($query, [
    //                                     'status' => 3,
    //                                     'browser_session_id' => $browserSessionId
    //                                 ]);
    //                             } else {
    //                                 // If $putInQueue is explicitly false
                                    
    //                                 // Update status in queuetb_raw_queue_operations
    //                                 $query = "UPDATE queuetb_raw_queue_operations SET status = :status WHERE browser_session_id = :browser_session_id";
    //                                 DB::update($query, [
    //                                     'status' => 10,
    //                                     'browser_session_id' => $browserSessionId
    //                                 ]);
                                
                                    
    //                                 // Forget cookies
    //                                 $cookie1 = Cookie::forget('qProcessOp');
    //                                 $cookie2 = Cookie::forget('qSessionId');
    //                                 $cookie3 = Cookie::forget('checkByepassStatus');
                                
    //                                 $final_response = [
    //                                     'status' => 10,
    //                                     'message' => 'advance condition failed',
    //                                     'qProcessOp' => false,
    //                                     'redirectionUrl' => false
    //                                 ];
    //                             }                        

    //                             $response_code = 200;
    //                         }
                            
    //                     } else {
    //                         $cookie1 = Cookie::forget('qProcessOp');
    //                         $cookie2 = Cookie::forget('qSessionId');
    //                         $cookie3 = Cookie::forget('checkByepassStatus');
    //                         $final_response = [
    //                             'status' => 'error',
    //                             'message' => 'status',
    //                         ];
    //                         $response_code = 403;
    //                     }
                        
    //                 } else {
    //                     $queueRoomId = base64_decode($data['scriptAttributes']['intercept']);
    //                     $query = "SELECT qr.id, qr.target_url, qr.bypass_template_id, qr.enable_bypass, bp_tamp.bypass_url,
    //                      qr.queue_room_icon, qr_tamp.is_advance_setting, qr_tamp.advance_setting_rules, qr_tamp.input_url FROM queuetb_queue_room AS qr INNER JOIN queue_room_template AS qr_tamp ON qr.queue_room_template_id = qr_tamp.id LEFT JOIN bypass_template AS bp_tamp ON qr.bypass_template_id = bp_tamp.id WHERE qr.parent_user_id = :customer_id AND qr.id = :queue_room_id";
        
    //                     $queueRoomData = DB::select($query, [
    //                         'customer_id' => $customerId,
    //                         'queue_room_id' => $queueRoomId,
    //                     ]); // Time: 1.22ms 
        
    //                     $isEnableBypass = $queueRoomData->enable_bypass??null;
    //                     $bypassURL = $queueRoomData->bypass_url??null;
    //                     $targetURL = $queueRoomData->target_url??null;
    //                     $queueRoomImageUrl = env('APP_URL')."public/images/".$queueRoomData->queue_room_icon;
        
    //                     if ($isEnableBypass && $browserUrl == $bypassURL) {
    //                         $bladeFile = "bypass-templates.bypass_access_code";
    //                         $bladeFileData = [
    //                             'room_id' =>  $queueRoomId,
    //                             'customer_id' => $customerId,
    //                             'queue_room_image_url' => $queueRoomImageURL,
    //                             'redirect_url' => $bypassURL,
    //                             'browser_session_id' => $browserSessionId,
    //                             'target_url' => $targetURL,
    //                         ];
        
    //                         $htmlBody = view::make($bladeFile, $bladeFileData)->rander();
    //                         $final_response = [
    //                             'status' => 2,
    //                             'message' => 'status 2',
    //                             'qProcessOp' => true,
    //                             'htmlBody' => $htmlBody
    //                         ];
        
    //                         $query = "UPDATE queuetb_queue_room SET currunt_traffic_count = currunt_traffic_count - 1 WHERE id = :queue_room_id";
        
    //                         DB::update($query, [
    //                             'queue_room_id' => $queueRoomId,
    //                         ]);
        
    //                     } else {
    //                         $isAdvanceSetting = $queueRoomData->is_advance_setting;
    //                         $inputURL = $queueRoomData->input_url;
        
    //                         $parsedUrl = parse_url($browserUrl);
    //                         $visitorAccessUrlDomain = $parsedUrl['host'] ?? '';
    //                         $visitorAccessUrlPagePath = $parsedUrl['path'] ?? '/';
        
    //                         $visitorAccessUrl = $visitorAccessUrlDomain . $visitorAccessUrlPagePath;
    //                         if ($isAdvanceSetting == 1) {
    //                             $advanceSettingRules = json_decode($queueRoomData->advance_setting_rules, true);
    //                             // return $advanceSettingRules;die;
    //                             $putInQueue = false; // Initialize to false by default
                            
    //                             foreach ($advanceSettingRules as $rule) {
    //                                 $conditionOp = isset($rule['operator']) ? $rule['operator'] : 'AND'; // Default to AND if operator not specified
                                    
    //                                 switch ($conditionOp) {
    //                                     case 'OR':
                                           
    //                                         $putInQueue = $putInQueue || self::evaluateCondition($rule, $visitorAccessUrlDomain, $visitorAccessUrlPagePath, $visitorAccessUrl);
    //                                         break;
    //                                     case 'AND':
    //                                     default:
    //                                         // $putInQueue = $putInQueue && evaluateCondition($rule, $visitorAccessUrlDomain, $visitorAccessUrlPagePath, $visitorAccessUrl);
    //                                         $putInQueue = $putInQueue && self::evaluateCondition($rule, $visitorAccessUrlDomain, $visitorAccessUrlPagePath, $visitorAccessUrl);
    //                                         break;
    //                                 }
                                    
    //                                 // If already true in OR condition, no need to continue checking
    //                                 if ($conditionOp == 'OR' && $putInQueue) {
    //                                     break;
    //                                 }
    //                             }
    //                         }
                            
    //                         // Prepare final response based on $putInQueue
    //                         if (isset($putInQueue) && $putInQueue) {
    //                             $final_response = [
    //                                 'status' => 'success',
    //                                 'message' => 'status',
    //                                 'qProcessOp' => true
    //                             ];
    //                         } else {
    //                             $visitor_data['status'] = 10;
    //                             $cookie1 = Cookie::forget('qProcessOp');
    //                             $cookie2 = Cookie::forget('qSessionId');
    //                             $cookie3 = Cookie::forget('checkByepassStatus');
    //                             $final_response = [
    //                                 'status' => $visitor_data['status'],
    //                                 'message' => 'advance condition failed',
    //                                 'qProcessOp' => false,
    //                                 'redirectionUrl' => false
    //                             ];
                            
    //                             // Perform DB updates if necessary
    //                             $query = "UPDATE queuetb_raw_queue_operations SET status = " . $visitor_data['status'] . " WHERE browser_session_id = " . $browserSessionId;
    //                             DB::select($query);
                            
    //                         }
                            
    //                     }
    //                     $response_code = 200;
    //                 }
                       
    //         } else {
    //             $cookie1 = Cookie::forget('qProcessOp');
    //             $cookie2 = Cookie::forget('qSessionId');
    //             $cookie3 = Cookie::forget('checkByepassStatus');
    
    //             $final_response = [
    //                 'status' => 'error',
    //                 'message' => 'status',
    //             ];
    
    //             $response_code = 400;
    //         }
    //     }
            
    //     self::addResponseLog($browserSessionId, $final_response, $response_code);
    
    //     if (isset($cookie1) && isset($cookie2)) {
    
    //         return response()->json($final_response, $response_code)
    //             ->withCookie($cookie1)
    //             ->withCookie($cookie2)
    //             ->withCookie($cookie3);
    //     } else {
    
    //         return response()->json($final_response, $response_code);
    //     }
    // }

    public function addResponseLog($session_id, $response, $response_code)
    {
        $log_message = "Response for session_id: " . json_encode($session_id) . " code: " . $response_code . " is - " . json_encode($response);
        Log::channel('visitor-raw-data')->info($log_message);
    }
}

