<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\QueueRoom;

use App\Helpers\Logics;


class VisitorsDataQueueOperations extends Controller
{
    public function __construct()
    {
        $this->this_epoch_time = time();
        $this->default_language = "en";
    }

    /** This function implement with-out redis */
    public function processThisSession($queueRoomId)
    {
        $query = "SELECT qr.id, qr.target_url , qr_tamp.input_url
                  FROM queuetb_queue_room as qr 
                  INNER JOIN queue_room_template as qr_tamp 
                  ON qr.queue_room_template_id = qr_tamp.id 
                  WHERE qr.id = :queue_room_id";

        $queueRoomData = DB::selectOne($query, ['queue_room_id' => $queueRoomId]);

        if (empty($queueRoomData)) {
            return response()->json(['error' => 'Queue room not found'], 404);
        }

        // $targetURL = $queueRoomData->target_url;
        $targetURL = $queueRoomData->target_url.'?start=true';

        $sameTargetFlag = ($queueRoomData->target_url == $queueRoomData->input_url) ? true : false;

        $finalResponse = [
            'status' => 6,
            'message' => 'status 6',
            'qProcessOp' => false,
            'redirectionUrl' => $targetURL,
            'same_target_flag' => $sameTargetFlag
        ];
        $responseCode = 200;

        $cookie1 = Cookie::forget('qProcessOp');
        $cookie2 = Cookie::forget('qSessionId');
        $cookie3 = Cookie::forget('checkByepassStatus');

        return response()->json($finalResponse, $responseCode)
                        ->withCookie($cookie1)
                        ->withCookie($cookie2)
                        ->withCookie($cookie3);
    }

    /** This function implement with Redis */
    // public function processThisSession($queueRoomId)
    // {
    //     // Generate a unique cache key for Redis
    //     $cacheKey = 'queue_room_' . $queueRoomId;

    //     // Attempt to retrieve the data from Redis
    //     $queueRoomData = Redis::get($cacheKey);

    //     if (!$queueRoomData) {
    //         // If data is not found in Redis, fetch it from the database
    //         $query = "SELECT qr.id, qr.target_url , qr_tamp.input_url
    //                   FROM queuetb_queue_room as qr 
    //                   INNER JOIN queue_room_template as qr_tamp 
    //                   ON qr.queue_room_template_id = qr_tamp.id 
    //                   WHERE qr.id = :queue_room_id";

    //         $queueRoomData = DB::selectOne($query, ['queue_room_id' => $queueRoomId]);

    //         if (empty($queueRoomData)) {
    //             return response()->json(['error' => 'Queue room not found'], 404);
    //         }
    //         // Store the fetched data in Redis with an expiration time
    //         Redis::set($cacheKey, json_encode($queueRoomData));
    //         Redis::expire($cacheKey, 3600);  // Cache the data for 1 hour
    //     } else {
    //         // Decode the JSON string from Redis
    //         $queueRoomData = json_decode($queueRoomData);
    //     }

    //     // Process the target URL and sameTargetFlag
    //     $targetURL = $queueRoomData->target_url;
    //     $sameTargetFlag = ($queueRoomData->target_url == $queueRoomData->input_url) ? true : false;

    //     // Prepare the final response
    //     $finalResponse = [
    //         'status' => 6,
    //         'message' => 'status 6',
    //         'qProcessOp' => false,
    //         'redirectionUrl' => $targetURL,
    //         'same_target_flag' => $sameTargetFlag
    //     ];
    //     $responseCode = 200;

    //     // Forget the cookies
    //     $cookie1 = Cookie::forget('qProcessOp');
    //     $cookie2 = Cookie::forget('qSessionId');
    //     $cookie3 = Cookie::forget('checkByepassStatus');

    //     return response()->json($finalResponse, $responseCode)
    //                     ->withCookie($cookie1)
    //                     ->withCookie($cookie2)
    //                     ->withCookie($cookie3);
    // }


    /** This function implement with-out redis */
    public function updateRawtables($browserSessionId, $status, $deviceId)
    {
        $thisEpochTime = now()->timestamp; // or any method to get current epoch time
        DB::update("
                UPDATE queuetb_raw_queue_operations 
                SET status = ?, last_updated_epoch = ?
                WHERE browser_session_id = ? 
                AND status != ? 
                AND status != ?
            ", [$status, $thisEpochTime, $browserSessionId, '6', '10']);

        // if (!empty($deviceId)) {
        //     DB::update("UPDATE queuetb_raw_queue_operations SET status = ?, last_updated_epoch = ? WHERE device_id = ?", [$status, $thisEpochTime, $deviceId]);
        // } else {
            // DB::update("UPDATE queuetb_raw_queue_operations SET status = ?, last_updated_epoch = ? WHERE browser_session_id = ?", [$status, $thisEpochTime, $browserSessionId])->where([['status', '!=', '6'], ['status', '!=', '10']]);
        // }
    }

    /** This function implement with Redis */
    // public function updateRawtables($browserSessionId, $status, $deviceId)
    // {
    //     $thisEpochTime = now()->timestamp; // Current epoch time
    //     $cacheKey = $deviceId ? "browserSessionData:deviceId:{$deviceId}" : "browserSessionData:browserSessionId:{$browserSessionId}";
    
    //     $updateData = [
    //         'status' => $status,
    //         'last_updated_epoch' => $thisEpochTime
    //     ];
    
    //     // Update the database
    //     if ($deviceId) {
    //         DB::update("UPDATE queuetb_raw_queue_operations SET status = ?, last_updated_epoch = ? WHERE device_id = ?", 
    //             [$status, $thisEpochTime, $deviceId]);
    //     } else {
    //         DB::update("UPDATE queuetb_raw_queue_operations SET status = ?, last_updated_epoch = ? WHERE browser_session_id = ?", 
    //             [$status, $thisEpochTime, $browserSessionId]);
    //     }
    
    //     // Fetch existing data from Redis
    //     $existingDataJson = Redis::get($cacheKey);
    
    //     if ($existingDataJson) {
    //         $existingData = json_decode($existingDataJson, true);
        
    //         // Update the necessary fields
    //         $existingData['status'] = $status;
    //         $existingData['last_updated_epoch'] = $thisEpochTime;
        
    //         // Save the updated data back to Redis
    //         Redis::set($cacheKey, json_encode($existingData));
    //         Redis::expire($cacheKey, 3600);  // Set expiration to 1 hour

    //         if ($status == 6) {
    //             Redis::del($cacheKey);
    //         }
    //     } else {
    //         // If no existing data, set the new data
    //         Redis::setex($cacheKey, 3600, json_encode($updateData));
    //     }
    // }



    /** This function implement with-out redis */
    private function updateCurrentQueue($queueRoomId) 
    {
        // $query = "SELECT currunt_traffic_count  
        //           FROM queuetb_queue_room  
        //           WHERE id = :queue_room_id";

        // $qu = DB::selectOne($query, ['queue_room_id' => $queueRoomId]);

       return QueueRoom::where([['id', $queueRoomId], ['currunt_traffic_count', '>', 0]])->decrement('currunt_traffic_count', 1);

        // QueueRoom::where([['id', $queueRoomId], ['in_queue', '>', 0]])->decrement('in_queue', 1);

        // if (!empty($qu) && $qu->currunt_traffic_count > 0) {
        //     DB::update("UPDATE queuetb_queue_room 
        //                 SET currunt_traffic_count = currunt_traffic_count - 1 
        //                 WHERE id = ?", [$queueRoomId]);
        // }
    }

    /** This function implement with Redis */
    // private function updateCurrentQueue($queueRoomId) 
    // {
    //     // Generate a cache key for Redis based on the queue room ID
    //     // $cacheKey = 'queue_room_traffic_' . $queueRoomId;
    //     $cacheKey = "queueRoomData:{$queueRoomId}";
    //     // Attempt to retrieve the current traffic count from Redis
    //     $currentTrafficCount = Redis::get($cacheKey);

    //     if (!$currentTrafficCount) {
    //         // If not found in Redis, retrieve it from the database
    //         $query = "SELECT currunt_traffic_count  
    //                   FROM queuetb_queue_room  
    //                   WHERE id = :queue_room_id";

    //         $queueRoomData = DB::selectOne($query, ['queue_room_id' => $queueRoomId]);

    //         if (!empty($queueRoomData)) {
    //             $currentTrafficCount = $queueRoomData->currunt_traffic_count;
    //             // Store the current traffic count in Redis
    //             Redis::set($cacheKey, $currentTrafficCount);
    //             Redis::expire($cacheKey, 3600); // Cache the data for 1 hour
    //         }
    //     }

    //     // Decrement the traffic count and in_queue in the database
    //     QueueRoom::where([['id', $queueRoomId], ['currunt_traffic_count', '>', 0]])->decrement('currunt_traffic_count', 1);
    //     QueueRoom::where([['id', $queueRoomId], ['in_queue', '>', 0]])->decrement('in_queue', 1);

    //     // Update the cached traffic count in Redis
    //     if ($currentTrafficCount > 0) {
    //         $newTrafficCount = (int)$currentTrafficCount - 1;
    //         Redis::set($cacheKey, $newTrafficCount);
    //     }
    // }


    public function createAndAppendHiddenInput($dom, $form, $name, $value)
    {
        $input = $dom->createElement('input');
        $input->setAttribute('type', 'hidden');
        $input->setAttribute('name', $name);
        $input->setAttribute('value', $value);
        $form->appendChild($input);
    }

    /** This function implement with-out redis */
    public function checkByPass($queueRoomData, $browserUrl, $queueRoomId, $customerId, $browserSessionId)
    {
        $finalResponse = [];
        $responseCode = "";
        $conditionFlag = false;

        $isEnableByPass = $queueRoomData->enable_bypass;
        $bypassUrl = $queueRoomData->bypass_url;
        $targetUrl = $queueRoomData->target_url;
        $queueRoomImageUrl = env('APP_URL')."public/images/".$queueRoomData->queue_room_icon;
        if (($isEnableByPass)) {
            $visitorData['status'] = 2;
            $bladeFile = "bypass-tamplates.bypass_access_code";
            $bladeFileData = [
                'room_id' => $queueRoomId,
                'customer_id' => $customerId,
                'queue_room_image_url' => $queueRoomImageUrl,
                'redirect_url' => $bypassUrl,
                'browser_session_id' => $browserSessionId,
                'target_url' => $targetUrl
            ];

            $htmlBody = View::make($bladeFile, $bladeFileData)->render();

            $finalResponse = [
                'status' => 2,
                'messagge' => 'sttus 2',
                'qProcessOp' => true,
                'htmlBody' => $htmlBody
            ];

            $responseCode = 200;
            $conditionFlag = true;
        }

        $functionReturnData = [
            'final_response' => $finalResponse,
            'response_code' => $responseCode,
            'condition_flag' => $conditionFlag
        ];

        return $functionReturnData;
    }

    /** This function implement with Redis */
    // public function checkByPass($queueRoomData, $browserUrl, $queueRoomId, $customerId, $browserSessionId)
    // {
    //     $finalResponse = [];
    //     $responseCode = "";
    //     $conditionFlag = false;

    //     // Generate a cache key for Redis based on the browser session ID and queue room ID
    //     $cacheKey = 'bypass_' . $queueRoomId . '_' . $browserSessionId;

    //     // Attempt to retrieve the final response from Redis
    //     $cachedResponse = Redis::get($cacheKey);

    //     if ($cachedResponse) {
    //         // If found in Redis, decode the response and return it
    //         $functionReturnData = json_decode($cachedResponse, true);
    //         return $functionReturnData;
    //     }

    //     $isEnableByPass = $queueRoomData->enable_bypass;
    //     $bypassUrl = $queueRoomData->bypass_url;
    //     $targetUrl = $queueRoomData->target_url;
    //     $queueRoomImageUrl = env('APP_URL')."public/images/".$queueRoomData->queue_room_icon;

    //     if ($isEnableByPass) {
    //         $visitorData['status'] = 2;
    //         $bladeFile = "bypass-tamplates.bypass_access_code";
    //         $bladeFileData = [
    //             'room_id' => $queueRoomId,
    //             'customer_id' => $customerId,
    //             'queue_room_image_url' => $queueRoomImageUrl,
    //             'redirect_url' => $bypassUrl,
    //             'browser_session_id' => $browserSessionId,
    //             'target_url' => $targetUrl
    //         ];

    //         $htmlBody = View::make($bladeFile, $bladeFileData)->render();

    //         $finalResponse = [
    //             'status' => 2,
    //             'message' => 'status 2',
    //             'qProcessOp' => true,
    //             'htmlBody' => $htmlBody
    //         ];

    //         $responseCode = 200;
    //         $conditionFlag = true;
    //     }

    //     $functionReturnData = [
    //         'final_response' => $finalResponse,
    //         'response_code' => $responseCode,
    //         'condition_flag' => $conditionFlag
    //     ];

    //     // Store the final response in Redis for future requests
    //     Redis::set($cacheKey, json_encode($functionReturnData));
    //     Redis::expire($cacheKey, 3600); // Cache the data for 1 hour

    //     return $functionReturnData;
    // }


    // public function checkBypass($queue_room_data, $browser_url, $queue_room_id, $customer_id, $browser_session_id)
    // {
    //     $final_response = [];
    //     $response_code = "";
    //     $condition_flag = false;


    //     $is_enable_byepass = $queue_room_data->enable_bypass;

    //     $bypass_url = $queue_room_data->bypass_url;
    //     $target_url = $queue_room_data->target_url;
    //     $queue_room_image_url = env('APP_URL') . "public/images/" . $queue_room_data->queue_room_icon;

    //     if ((false)) {
    //         // $visitor_data['status'] = 2;

    //         $blade_file = "bypass-tamplates.bypass_access_code";
    //         $blade_file_data = [
    //             'room_id' =>  $queue_room_id,
    //             'customer_id' => $customer_id,
    //             'queue_room_image_url' => $queue_room_image_url,
    //             'redirect_url' => $bypass_url,
    //             'browser_session_id' => $browser_session_id,
    //             'target_url' => $target_url,
    //         ];
    //         $htmlBody = View::make($blade_file, $blade_file_data)->render();

    //         $final_response = [
    //             'status' => 2,
    //             'message' => 'sttus 2',
    //             'qProcessOp' => true,
    //             'htmlBody' => $htmlBody,
    //         ];
    //         $response_code = 200;
    //         $condition_flag = true;
    //     }

    //     $function_return_data = [
    //         'final_response' => $final_response,
    //         'response_code' => $response_code,
    //         'condition_flag' => $condition_flag,
    //     ];

    //     return $function_return_data;
    // }

    /** This function implement with-out redis */
    private function createNewQueueSerialNumber($room_id, $max_traffic_visitor)
    {
        $slot_number = 1;
        $queue_serial_number = "{$slot_number}-{$room_id}";
    
        $data = [
            'queue_serial_number' => $queue_serial_number,
            'slot_number' => $slot_number,
            'room_id' => $room_id,
            'started_at' => time(),
            'max_traffic_visitor' => $max_traffic_visitor,
            'storage_occupied_number' => 1,
            'storage_free_number' => $max_traffic_visitor - 1,
        ];
    
        if ($max_traffic_visitor == 1) {
            $data['storage_status'] = 1;
        }
    
        $insert = DB::table("queue_serial_number_management")->insertGetId($data);
    
        if ($insert) {
            return ['queue_serial_number' => $queue_serial_number, 'queue_serial_number_id' => $insert];
        } else {
            return false; // Handle insert failure
        }
    }

    /** This function implement with Redis */
    // private function createNewQueueSerialNumber($room_id, $max_traffic_visitor)
    // {
    //     // Generate a unique cache key based on the room ID
    //     $cacheKey = 'queue_serial_number_' . $room_id;

    //     // Try to acquire a lock in Redis to prevent race conditions
    //     $lockKey = 'lock_queue_serial_number_' . $room_id;
    //     $lockAcquired = Redis::setnx($lockKey, true);

    //     if (!$lockAcquired) {
    //         // If lock is not acquired, another process is already creating the serial number
    //         return response()->json(['error' => 'Another process is creating a queue serial number. Please try again.'], 429);
    //     }

    //     // Set an expiration time on the lock to avoid deadlocks
    //     Redis::expire($lockKey, 10); // 10 seconds expiration for the lock

    //     $slot_number = 1;
    //     $queue_serial_number = "{$slot_number}-{$room_id}";

    //     $data = [
    //         'queue_serial_number' => $queue_serial_number,
    //         'slot_number' => $slot_number,
    //         'room_id' => $room_id,
    //         'started_at' => time(),
    //         'max_traffic_visitor' => $max_traffic_visitor,
    //         'storage_occupied_number' => 1,
    //         'storage_free_number' => $max_traffic_visitor - 1,
    //     ];

    //     if ($max_traffic_visitor == 1) {
    //         $data['storage_status'] = 1;
    //     }

    //     // Insert the new serial number into the database
    //     $insert = DB::table("queue_serial_number_management")->insertGetId($data);

    //     if ($insert) {
    //         $result = [
    //             'queue_serial_number' => $queue_serial_number,
    //             'queue_serial_number_id' => $insert
    //         ];

    //         // Cache the result in Redis for future use
    //         Redis::set($cacheKey, json_encode($result));
    //         Redis::expire($cacheKey, 3600); // Cache for 1 hour

    //         // Release the lock
    //         Redis::del($lockKey);

    //         return $result;
    //     } else {
    //         // Release the lock on failure
    //         Redis::del($lockKey);

    //         return false; // Handle insert failure
    //     }
    // }

    /** This function implement with-out redis */
    // public function assignQueueSerialNumber($room_id, $max_traffic_visitor = false)
    // {
    //     // Fetch max_traffic_visitor if not provided
    //     if (!empty($max_traffic_visitor)) {
    //         $query = "SELECT id, max_traffic_visitor, currunt_traffic_count 
    //                   FROM queuetb_queue_room 
    //                   WHERE id = {$room_id}";
    //         $result = DB::selectOne($query);

    //         if (!empty($result)) {
    //             $max_traffic_visitor = $result->max_traffic_visitor;
    //         } else {
    //             return false; // Handle case where room_id is invalid or not found
    //         }
    //     }

    //     // Fetch latest queue serial number data
    //     $query = "SELECT id, slot_number, queue_serial_number, storage_status, storage_free_number, storage_occupied_number, max_traffic_visitor, cron_status 
    //               FROM queue_serial_number_management 
    //               WHERE room_id = {$room_id} 
    //               ORDER BY id DESC";
    //     $result = DB::selectOne($query);

    //     if (!empty($result)) {
    //         $queue_serial_number_data = $result;

    //         $slot_number = $queue_serial_number_data->slot_number;
    //         $this_status = $queue_serial_number_data->cron_status;

    //         if ($this_status == 0) {
    //             $storage_status = $queue_serial_number_data->storage_status;

    //             if ($storage_status == 0) {
    //                 $queue_serial_number = $queue_serial_number_data->queue_serial_number;
    //                 $storage_occupied_number = $queue_serial_number_data->storage_occupied_number;
    //                 $storage_free_number = $queue_serial_number_data->storage_free_number;

    //                 $updated_storage_occupied_number = $storage_occupied_number + 1;
    //                 $updated_storage_free_number = $storage_free_number - 1;

    //                 if ($updated_storage_occupied_number == $queue_serial_number_data->max_traffic_visitor) {
    //                     $storage_status = 1;
    //                 }

    //                 // Update existing record
    //                 $query = "UPDATE queue_serial_number_management SET 
    //                           storage_occupied_number = {$updated_storage_occupied_number}, 
    //                           storage_free_number = {$updated_storage_free_number}, 
    //                           storage_status = {$storage_status}, 
    //                           max_traffic_visitor = {$max_traffic_visitor} 
    //                           WHERE id = {$queue_serial_number_data->id}";
    //                 DB::update($query);

    //                 return ['queue_serial_number' => $queue_serial_number, 'queue_serial_number_id' => $queue_serial_number_data->id];
    //             } else {
    //                 // Create new record if storage status is not 0
    //                 return self::createNewQueueSerialNumber($room_id, $max_traffic_visitor);
    //             }
    //         } else {
    //             // Create new record if cron_status is not 0
    //             return self::createNewQueueSerialNumber($room_id, $max_traffic_visitor);
    //         }
    //     } else {
    //         // Create new record if no existing data found
    //         return self::createNewQueueSerialNumber($room_id, $max_traffic_visitor);
    //     }
    // }

    /** This function use for checking the current space in target page | start */
    public function checkUserSpace(Request $request)
    {
        $data = $request->all();
        $customerId = $data['scriptAttributes']['cid'];
        $queueRoomId = base64_decode($data['scriptAttributes']['intercept']);
        // $queueRoomId = $data['scriptAttributes']['intercept'];

        $currentTimestamp = now();

        $updatedAll = DB::table('queuetb_raw_queue_operations')
            ->where('room_id', $queueRoomId)
            ->where('updated_at', '<=', $currentTimestamp->subMinutes(2))
            ->whereIn('status', ['6', '10'])
            ->update(['status' => '7']);

        if ($updatedAll)
        {
            $getMaxTrafficCount = QueueRoom::where('id', $queueRoomId)->select('max_traffic_visitor', 'current_space')->first();
            if ($getMaxTrafficCount->max_traffic_visitor > $getMaxTrafficCount->current_space && $getMaxTrafficCount->max_traffic_visitor >= $updatedAll)
            {
                QueueRoom::where('id', $queueRoomId)->increment('current_space', $updatedAll);
                return true;
            }else{
                QueueRoom::where('id', $queueRoomId)->increment('current_space', 1);
                return true;
            }
        }

        return false;
    }
    /** This function use for checking the current space in target page | end */

    /** This function use for decrese the queue number status | start */
    public function updateQueueNumber(Request $request)
    {
        $queueRoomId = base64_decode($request['scriptAttributes']['intercept']);
        // $queueRoomId = $request['scriptAttributes']['intercept'];
        $result = QueueRoom::where([['id', $queueRoomId], ['in_queue', '>', 0]])->decrement('in_queue', 1);
        return $result;
    }
    /** This function use for decrese the queue number status | end */

    /** This function implement with Redis */
    public function operateQueue(Request $request)
    {
        $data = $request->all();

        $customerId = $data['scriptAttributes']['cid'];
        $customerDomain = $data['scriptAttributes']['domain'];
        $callFrom = $data['scriptAttributes']['dataCall'];

        $queueRoomId = base64_decode($data['scriptAttributes']['intercept']);
        // $queueRoomId = $data['scriptAttributes']['intercept'];
        $browserSessionId = $data['session_id'];
        $deviceId = $data['deviceid'] ?? null;

        $getOprationCount = DB::table('queuetb_raw_queue_operations')->where('status', 3)->count();
        if ($getOprationCount > 0)
        {
            QueueRoom::where('id', $queueRoomId)->update(['currunt_traffic_count' => 0]);
        }

        if (!empty($deviceId)) {
            $browserSessionData = DB::table('queuetb_raw_queue_operations')
            ->select('id', 'browser_url', 'status', 'queue_serial_number', 'queue_serial_number_id')
            ->where('device_id', $deviceId)
            ->first();
        } else {
            $browserSessionData = DB::table('queuetb_raw_queue_operations')
            ->select('id', 'browser_url', 'status', 'queue_serial_number', 'queue_serial_number_id')
            ->where('browser_session_id', $browserSessionId)
            ->first();
        }

        if ($browserSessionData->id)
        {
            if ($browserSessionData->status == 6)
            {
                $queueProcessData = self::processThisSession($queueRoomId);
                self::updateRawtables($browserSessionId, 6, $deviceId);

                $finalResponse = $queueProcessData->original;
                $responseCode = $queueProcessData->status();

                $cookie1 = $queueProcessData->headers->getCookies()[0];
                $cookie2 = $queueProcessData->headers->getCookies()[1];

                self::updateCurrentQueue($queueRoomId);
            }else if ($browserSessionData->status == 1) {
                $queueRoomData = DB::table('queuetb_queue_room')
                    ->select('id', 'target_url')
                    ->where('id', $queueRoomId)
                    ->first();

                $targetUrl = $queueRoomData->target_url;
                
                $finalResponse = [
                    'status' => 1,
                    'message' => 'status 1',
                    'qProcessOp' => false,
                    'redirectionUrl' => $targetURL
                ];

                $responseCode = 200;

                // Prepare cookies to be forgotten
                $cookie1 = Cookie::forget('qProcessOp');
                $cookie2 = Cookie::forget('qSessionId');
                $cookie3 = Cookie::forget('checkByepassStatus');

                self::updateCurrentQueue($queueRoomId);
            }else if ($browserSessionData->status == 4) {
                $queueRoomData = DB::table('queuetb_queue_room as qr')
                    ->join('queuetb_design_template as qr_design', 'qr.queue_room_design_tempid', '=', 'qr_design.id')
                    ->select('qr.id', 'qr.start_time_epoch', 'qr.max_traffic_visitor', 'qr.is_started', 'qr.prequeue_starttime', 'qr_design.default_language')
                    ->where('qr.id', $queueRoomId)
                    ->first();

                // Ensure the query result is not empty before accessing its elements
                if (is_null($queueRoomData)) {
                    return response()->json(['error' => 'Queue room not found'], 404);
                }

                // Extract the default language, with proper null checks
                $defLang = $queueRoomData->default_language ?? $this->default_language;

                // $modifiedHtml = Logics::showPreQueuePage($queueRoomId, $browserSessionId, $customerId, $this->this_epoch_time);

                $prequeueStartTime = (int)$queueRoomData->prequeue_starttime;
                $startTimeEpoch = (int)$queueRoomData->start_time_epoch;
                $checkStartCondition = Logics::checkStartCondition($prequeueStartTime, $startTimeEpoch);
                // return $checkStartCondition;die;
                if ($checkStartCondition) {
                    self::updateRawtables($browserSessionId, 5, $deviceId);
                    $numberQuery = "SELECT count(id) as count FROM queuetb_raw_queue_operations WHERE room_id = {$queueRoomId} AND (status = 4 OR status = 5) AND id <= {$browserSessionData->id}";
                    
                    $numberData = DB::selectOne($numberQuery);
                    $numberInLine = ($numberData) ? $numberData->count : 0;
                    $numberQuery = "SELECT sum(storage_occupied_number) as count FROM queue_serial_number_management WHERE room_id = {$queueRoomId} AND cron_status = 0";
                    $totalNumberData = DB::selectOne($numberQuery);
                    $totalNumber = ($totalNumberData && !empty($totalNumberData->count)) ? $totalNumberData->count : 1;
                    $per = (1 - ($numberInLine / $totalNumber));
                    $modifiedHtml = Logics::showQueuePage($queueRoomId, $browserSessionId, $customerId, $this->this_epoch_time, $per, $numberInLine);
                    
                    $finalResponse = [
                        'status' => 5,
                        'message' => 'status 5',
                        'queue_serial_number' => $queueSerialNumber,
                        'max_traffic_visitor' => $maxTrafficVisitor ?? 0,
                        'qProcessOp' => true,
                        'a' => 'ssa-' . $browserSessionData->id,
                        'current_queue_pos' => $numberInLine + 1,
                        'htmlBody' => $modifiedHtml,
                    ];
                    $responseCode = 200;
                }else{
                    // $modifiedHtml = Logics::showPreQueuePage($queueRoomId, $browserSessionId, $customerId, $this->this_epoch_time);
                    $modifiedHtml = Logics::showPreQueuePage($queueRoomId, $deviceId, $customerId, $this->this_epoch_time);
                    $finalResponse = [
                        'status' => 4,
                        'message' => 'status 4',
                        'htmlBody' => $modifiedHtml
                    ];
                    $responseCode = 200;
                }

                $finalResponse = [
                    'status' => 4,
                    'message' => 'status 4',
                    'htmlBody' => $modifiedHtml
                ];
                $responseCode = 200;

            }elseif ($browserSessionData->status == 10) {
                $targetUrl = DB::table('queuetb_queue_room')
                    ->where('id', $queueRoomId)
                    ->value('target_url');

                $finalResponse = [
                    'status' => 10,
                    'message' => 'Page has no traffic, redirecting to site',
                    'qProcessOp' => false,
                    'redirectionUrl' => $targetUrl
                ];
                $responseCode = 200;

                // Forget multiple cookies in one line
                $cookie1 = Cookie::forget('qProcessOp');
                $cookie2 = Cookie::forget('qSessionId');
                $cookie3 = Cookie::forget('checkByepassStatus');

                self::updateCurrentQueue($queueRoomId);
            }else {
                // $queueRoomData = QueueRoom::select(
                $queueRoomData = DB::table('queuetb_queue_room as qr')
                    ->select('qr.id',
                        'qr.queue_room_name',
                        'qr.queue_room_template_id',
                        'qr.target_url',
                        'qr.bypass_template_id',
                        'qr.sms_notice_tempid',
                        'qr.email_notice_tempid',
                        'qr.enable_bypass',
                        'bp_tamp.bypass_url',
                        'qr.queue_room_icon',
                        'qr_tamp.is_advance_setting',
                        'qr_tamp.advance_setting_rules',
                        'qr_tamp.input_url',
                        'qr.max_traffic_visitor',
                        'qr.currunt_traffic_count',
                        'qr.is_started',
                        'qr.is_ended',
                        'qr.current_space',
                        'qr.start_time_epoch',
                        'qr_design.default_language'
                    )
                    ->leftJoin('bypass_template as bp_tamp', 'qr.bypass_template_id', '=', 'bp_tamp.id')
                    ->join('queue_room_template as qr_tamp', 'qr.queue_room_template_id', '=', 'qr_tamp.id')
                    ->join('queuetb_design_template as qr_design', 'qr.queue_room_design_tempid', '=', 'qr_design.id')
                    ->where('qr.parent_user_id', $customerId)
                    ->where('qr.id', $queueRoomId)
                    ->first();

                // if ($finalResponse['status'] == 5)
                if ($browserSessionData->status == "5" && $callFrom == "1")
                {
                    if ($queueRoomData->current_space > 0)
                    {
                        $getUserNumber = DB::table('queuetb_raw_queue_operations')->where([['room_id', $queueRoomId], ['status', '5']])->orderBy('queue_serial_number_id', 'ASC')->first();
                        $changeQueueStatus = DB::table('queuetb_raw_queue_operations')->where('id', $getUserNumber->id)->update(['status' => '6', 'updated_at' => Carbon::now()]);
                        // QueueRoom::where([['id', $queueRoomId], ['in_queue', '==', 1]])->increment('in_queue', 1);

                        QueueRoom::where('id', $queueRoomId)->decrement('current_space', 1);
                    }
                }

                $defLang = $queueRoomData ? ($queueRoomData->default_language ?? $this->default_language) : $this->default_language;
                $isQueueStarted = $queueRoomData->is_started;
                $isQueueEnded = $queueRoomData->is_ended;
                $maxTrafficVisitor = $queueRoomData->max_traffic_visitor;
                $currentTrafficCount = $queueRoomData->currunt_traffic_count;
                $queueSerialNumber = $browserSessionData->queue_serial_number_id;

                /** create the queue room page URL | start */
                $parsedUrl = parse_url($queueRoomData->input_url, PHP_URL_HOST);
                if (str_contains($parsedUrl, 'www')) {
                    $mainURL = explode('www.', $parsedUrl);
                    $url = $mainURL[1];
                }else{
                    $url = $parsedUrl;
                }

                $url = 'https://queue.'.$url;
                // $url = 'http://queue.'.$url;
                /** create the queue room page URL | end */
                if ($isQueueStarted == 1 && $isQueueEnded == 0 || $isQueueEnded == 2)
                {
                    $bypassStatus = self::checkByPass($queueRoomData, $browserSessionData->browser_url, $queueRoomId, $customerId, $browserSessionId);
                    if ($bypassStatus['condition_flag']) {
                        self::updateRawtables($browserSessionId, 2, $deviceId);
                    
                        $finalResponse = $bypassStatus['final_response'];
                        $responseCode = $bypassStatus['response_code'];
                    
                        self::updateCurrentQueue($queueRoomId);
                    } else {
                        $isAdvanceSetting = $queueRoomData->is_advance_setting;
                        $inputUrl = $queueRoomData->input_url;
                    
                        $urlComponents = parse_url($browserSessionData->browser_url);
                        $visitorAccessUrlDomain = $urlComponents['host'] ?? '';
                        $visitorAccessUrlPagePath = $urlComponents['path'] ?? '';

                        // Construct the complete visitor access URL
                        $visitorAccessUrl = $visitorAccessUrlDomain . $visitorAccessUrlPagePath;
                    
                        if ($isAdvanceSetting == 1) {
                            $advanceSettingRules = json_decode($queueRoomData->advance_setting_rules, true);
                            
                            foreach ($advanceSettingRules as $rule) {
                                $conditionMet = false;
                    
                                switch ($rule['condition_place']) {
                                    case 'HOST_NAME':
                                        $value = $rule['value'];
                                        switch ($rule['condition']) {
                                            case 'EQUALS':
                                                $conditionMet = ($visitorAccessUrlDomain == $value);
                                                break;
                                            case 'DOES_NOT_EQUAL':
                                                $conditionMet = ($visitorAccessUrlDomain != $value);
                                                break;
                                            case 'CONTAINS':
                                                $conditionMet = (strpos($visitorAccessUrlDomain, $value));
                                                break;
                                            case 'DOES_NOT_CONTAIN':
                                                $conditionMet = (!strpos($visitorAccessUrlDomain, $value));
                                                break;
                                        }
                                        break;
                                    case 'PAGE_PATH':
                                        $value = $rule['value'];
                                        switch ($rule['condition']) {
                                            case 'EQUALS':
                                                $conditionMet = ($visitorAccessUrlPagePath == $value);
                                                break;
                                            case 'DOES_NOT_EQUAL':
                                                $conditionMet = ($visitorAccessUrlPagePath != $value);
                                                break;
                                            case 'CONTAINS':
                                                $conditionMet = (strpos($visitorAccessUrlPagePath, $value));
                                                break;
                                            case 'DOES_NOT_CONTAIN':
                                                $conditionMet = (!strpos($visitorAccessUrlPagePath, $value));
                                                break;
                                        }
                                        break;
                                    case 'PAGE_URL':
                                        $value = $rule['value'];
                                        switch ($rule['condition']) {
                                            case 'EQUALS':
                                                $conditionMet = ($visitorAccessUrl == $value);
                                                break;
                                            case 'DOES_NOT_EQUAL':
                                                $conditionMet = ($visitorAccessUrl != $value);
                                                break;
                                            case 'CONTAINS':
                                                $conditionMet = (strpos($visitorAccessUrl, $value));
                                                break;
                                            case 'DOES_NOT_CONTAIN':
                                                $conditionMet = (!strpos($visitorAccessUrl, $value));
                                                break;
                                        }
                                        break;
                                }
                    
                                if ($rule['operator'] == 'AND' && !$conditionMet) {
                                    $putInQueue = false;
                                    break;
                                } elseif ($rule['operator'] == 'OR' && $conditionMet) {
                                    $putInQueue = true;
                                    break;
                                }
                            }
                        }
                    
                        if ((isset($putInQueue) && $putInQueue == true) || !isset($putInQueue)) {
                            // $queueSerialNumber = $browserSessionData->queue_serial_number ?? '';
                    
                            // if (!$queueSerialNumber) {
                            //     $queueSerialData = self::assignQueueSerialNumber($queueRoomId, $maxTrafficVisitor, false);
                            //     $logMessage = "Added to queue for session_id: {$browserSessionId}, queue_serial_number: {$queueSerialData['queue_serial_number']}";
                            //     Log::channel('in-queue-room')->info($logMessage);
                    
                            //     $query = "UPDATE queuetb_raw_queue_operations SET queue_serial_number = '{$queueSerialData['queue_serial_number']}', queue_serial_number_id = {$queueSerialData['queue_serial_number_id']} WHERE browser_session_id = {$browserSessionId}";
                            //     DB::select($query);
                    
                            //     $queueSerialNumber = $queueSerialData['queue_serial_number'];
                            // }
                    
                            self::updateRawtables($browserSessionId, 5, $deviceId);
                    
                            $numberQuery = "SELECT count(id) as count FROM queuetb_raw_queue_operations WHERE room_id = {$queueRoomId} AND (status = 3 OR status = 5) AND id < {$browserSessionData->id}";
                            $numberData = DB::selectOne($numberQuery);
                            $numberInLine = ($numberData) ? $numberData->count : 0;
                    
                            $expectedWaitTime = (floor($numberInLine / $maxTrafficVisitor)) + 1;
                    
                            $numberQuery = "SELECT sum(storage_occupied_number) as count FROM queue_serial_number_management WHERE room_id = {$queueRoomId} AND cron_status = 0";
                            $totalNumberData = DB::selectOne($numberQuery);
                            $totalNumber = ($totalNumberData && !empty($totalNumberData->count)) ? $totalNumberData->count : 1;
                    
                            $per = (1 - ($numberInLine / $totalNumber));
                    
                            $modifiedHtml = Logics::showQueuePage($queueRoomId, $browserSessionId, $customerId, $this->this_epoch_time, $per, $numberInLine);

                            $finalResponse = [
                                'status' => 5,
                                'message' => 'status 5',
                                'queue_serial_number' => $queueSerialNumber,
                                'max_traffic_visitor' => $maxTrafficVisitor ?? 0,
                                'qProcessOp' => true,
                                'a' => 'ssa-' . $browserSessionData->id,
                                'current_queue_pos' => $numberInLine + 1,
                                'htmlBody' => $modifiedHtml,
                                'cname' => $url,
                            ];
                            $responseCode = 200;
                        } else {
                            $cookie1 = Cookie::forget('qProcessOp');
                            $cookie2 = Cookie::forget('qSessionId');
                            $cookie3 = Cookie::forget('checkByepassStatus');
                    
                            $finalResponse = [
                                'status' => 0,
                                'message' => 'status 0',
                                'qProcessOp' => false,
                            ];
                            $responseCode = 200;
                        }
                    }                    
                } else {
                    $startTimeEpoch = $queueRoomData->start_time_epoch;
                    if ($this->this_epoch_time > $startTimeEpoch) {
                        // $queueSerialNumber = $browserSessionData->queueSerialNumber ?? null;
                    
                        // if (!$queueSerialNumber) {
                        //     $queueSerialData = self::assignQueueSerialNumber($queueRoomId, $maxTrafficVisitor, $curruntTrafficCount = false);
                    
                        //     $log_message = "Added to queue for session_id: {$browser_session_id} queue_serial_number: " . json_encode($queue_serial_data);
                        //     Log::channel('in-queue-room')->info($log_message);
                    
                        //     $query = "UPDATE queuetb_raw_queue_operations SET queue_serial_number = '{$queue_serial_data['queue_serial_number']}', queue_serial_number_id = {$queue_serial_data['queue_serial_number_id']} WHERE browser_session_id = {$browser_session_id}";
                        //     DB::select($query);
                    
                        //     $queueSerialNumber = $queueSerialData['queue_serial_number'];
                        // }
                    
                        self::updateRawtables($browserSessionId, 5, $deviceId);
                    
                        // Get number in line
                        $numberQuery = "SELECT count(id) as count FROM queuetb_raw_queue_operations WHERE room_id = {$queueRoomId} AND (status = 3 OR status = 5) AND id < {$browserSessionData->id}";
                        $numberData = DB::selectOne($numberQuery);
                        $numberInLine = ($numberData) ? $numberData->count : 0;
                        
                        $expectedWaitTime = (floor($numberInLine / $maxTrafficVisitor)) + 1;
                    
                        // Get total number
                        $totalQuery = "SELECT COALESCE(SUM(storage_occupied_number), 1) as total FROM queue_serial_number_management WHERE room_id = {$queueRoomId} AND cron_status = 0";
                        $totalData = DB::selectOne($total_query);
                        $totalNumber = $totalData->total;
                    
                        $per = (1 - ($numberInLine / $totalNumber));
                    
                        $modifiedHtml = Logics::showQueuePageWithSwitch($queueRoomId, $numberInLine, $expectedWaitTime, $browserSessionId, $customerId);
                    
                        // Prepare final response
                        $finalResponse = [
                            'status' => 5,
                            'message' => 'status 5',
                            'queue_serial_number' => $queueSerialNumber,
                            'max_traffic_visitor' => $maxTrafficVisitor ?? 0,
                            'qProcessOp' => true,
                            'a' => 'ss',
                            'current_queue_pos' => ($numberInLine ?? 0) + 1,
                            // 'current_queue_pos' => ($currentQueuePos ?? 0) + 1,
                            'htmlBody' => $modifiedHtml,
                            'cname' => $url,
                        ];
                        $responseCode = 200;
                    } else {
                        // Clear cookies when conditions are not met
                        $cookie1 = Cookie::forget('qProcessOp');
                        $cookie2 = Cookie::forget('qSessionId');
                        $cookie3 = Cookie::forget('checkByepassStatus');
                    
                        $finalResponse = [
                            'status' => 0,
                            'message' => 'status 0',
                            'qProcessOp' => false,
                        ];
                        $responseCode = 200;
                    }                      
                }
            }
        } else {
            $cookie1 = Cookie::forget('qProcessOp');
            $cookie2 = Cookie::forget('qSessionId');
            $cookie3 = Cookie::forget('checkByepassStatus');

            $finalResponse = [
                'status' => 0,
                'message' => 'session id not found queue',
                'qProcessOp' => false,

            ];
            $responseCode = 404;
        }
        
        // self::addResponseLog($browserSessionId, $finalResponse, $responseCode);
        if (isset($cookie1) && isset($cookie2)) {
            // if ($finalResponse['status'] == "5")
            // {
            //     Log::info("call if");
            //     $getUserNumber = DB::table('queuetb_raw_queue_operations')->where([['room_id', $queueRoomId], ['status', '5']])->orderBy('queue_serial_number_id', 'ASC')
            //     ->first();
            //     $changeQueueStatus = DB::table('queuetb_raw_queue_operations')->where('id', $getUserNumber->id)->update(['status' => '6']);
            // }

            return response()->json($finalResponse, $responseCode)
                ->withCookie($cookie1)
                ->withCookie($cookie2);
        } else {
            // if ($finalResponse['status'] == "5")
            // {
            //     Log::info("call else");
            //     $getUserNumber = DB::table('queuetb_raw_queue_operations')->where([['room_id', $queueRoomId], ['status', '5']])->orderBy('queue_serial_number_id', 'ASC')
            //     ->first();
            //     $changeQueueStatus = DB::table('queuetb_raw_queue_operations')->where('id', $getUserNumber->id)->update(['status' => '6']);
            // }

            return response()->json($finalResponse, $responseCode);
        }
    }

    /** This function implement with Redis */
    // public function operateQueue(Request $request)
    // {
    //     // return Redis::flushdb();
    //     $data = $request->all();
    //     // return $data;die;
    //     $customerId = $data['scriptAttributes']['cid'];
    //     $customerDomain = $data['scriptAttributes']['domain'];
    //     $queueRoomId = base64_decode($data['scriptAttributes']['intercept']);
    //     $browserSessionId = $data['session_id'];
    //     $deviceId = $data['deviceid'] ?? null;

    //     $getOprationCount = DB::table('queuetb_raw_queue_operations')->where('status', 3)->count();
    //     if ($getOprationCount > 0)
    //     {
    //         QueueRoom::where('id', $queueRoomId)->update(['currunt_traffic_count' => 0]);
    //     }

    //     $browserSessionData = null;
    //     $redisKey = $deviceId ? "browserSessionData:deviceId:{$deviceId}" : "browserSessionData:browserSessionId:{$browserSessionId}";

    //     $cachedSessionData = Redis::get($redisKey);
    //     if ($cachedSessionData) {
    //         $browserSessionData = json_decode($cachedSessionData);
    //         // return $browserSessionData;die;ss
    //     } else {
    //         $query = DB::table('queuetb_raw_queue_operations')
    //             ->select('id', 'browser_url', 'status', 'queue_serial_number', 'queue_serial_number_id');
    
    //         if (!empty($deviceId)) {
    //             $query->where('device_id', $deviceId);
    //         } else {
    //             $query->where('browser_session_id', $browserSessionId);
    //         }
    
    //         $browserSessionData = $query->first();
    
    //         // Cache the session data in Redis
    //         Redis::set($redisKey, json_encode($browserSessionData));
    //     }

    //     if ($browserSessionData->id)
    //     {
    //         if ($browserSessionData->status == 6)
    //         {
    //             $queueProcessData = self::processThisSession($queueRoomId);
    //             self::updateRawtables($browserSessionId, 6, $deviceId);
    //             $finalResponse = $queueProcessData->original;
    //             $responseCode = $queueProcessData->status();
    //             $cookie1 = $queueProcessData->headers->getCookies()[0];
    //             $cookie2 = $queueProcessData->headers->getCookies()[1];
    //             self::updateCurrentQueue($queueRoomId);
    //         }elseif ($browserSessionData->status == 1) {
    //             $redisKey = "queueRoomData:{$queueRoomId}";
            
    //             $queueRoomData = Redis::get($redisKey);
            
    //             if (!$queueRoomData) {
    //                 $queueRoomData = DB::table('queuetb_queue_room')
    //                     ->select('id', 'target_url')
    //                     ->where('id', $queueRoomId)
    //                     ->first();
            
    //                 Redis::set($redisKey, json_encode($queueRoomData));
                    
    //                 // Optionally, set an expiration time for the cached data (e.g., 3600 seconds)
    //                 // Redis::setex($redisKey, 3600, json_encode($queueRoomData));
    //             } else {
    //                 $queueRoomData = json_decode($queueRoomData);
    //             }
            
    //             $targetUrl = $queueRoomData->target_url;
            
    //             $finalResponse = [
    //                 'status' => 1,
    //                 'message' => 'status 1',
    //                 'qProcessOp' => false,
    //                 'redirectionUrl' => $targetUrl
    //             ];
            
    //             $responseCode = 200;
            
    //             $cookie1 = Cookie::forget('qProcessOp');
    //             $cookie2 = Cookie::forget('qSessionId');
    //             $cookie3 = Cookie::forget('checkByepassStatus');
            
    //             self::updateCurrentQueue($queueRoomId);
    //         }elseif ($browserSessionData->status == 4) {
    //             $redisKey = "queueRoomData:{$queueRoomId}";
    //             $queueRoomData = Redis::get($redisKey);

    //             if (!$queueRoomData) {
    //                 $queueRoomData = DB::table('queuetb_queue_room as qr')
    //                     ->join('queuetb_design_template as qr_design', 'qr.queue_room_design_tempid', '=', 'qr_design.id')
    //                     ->select('qr.id', 'qr.start_time_epoch', 'qr.is_started', 'qr_design.default_language')
    //                     ->where('qr.id', $queueRoomId)
    //                     ->first();
                
    //                 // If the data is not found in the database, return an error response
    //                 if (is_null($queueRoomData)) {
    //                     return response()->json(['error' => 'Queue room not found'], 404);
    //                 }
                
    //                 Redis::set($redisKey, json_encode($queueRoomData));
    //             } else {
    //                 $queueRoomData = json_decode($queueRoomData);
    //             }

    //             // Extract the default language, with proper null checks
    //             $defLang = $queueRoomData->default_language ?? $this->default_language;

    //             // Call the function to show the pre-queue page
    //             $modifiedHtml = Logics::showPreQueuePage($queueRoomId, $browserSessionId, $customerId, $this->this_epoch_time);

    //             $finalResponse = [
    //                 'status' => 4,
    //                 'message' => 'status 4',
    //                 'htmlBody' => $modifiedHtml
    //             ];

    //             $responseCode = 200;
    //         }elseif ($browserSessionData->status == 10) {
    //             $redisKey = "queueRoomData:{$queueRoomId}";

    //             // Attempt to fetch the target URL from Redis
    //             $targetUrl = Redis::get($redisKey);

    //             if (!$targetUrl) {
    //                 $targetUrl = DB::table('queuetb_queue_room')
    //                     ->where('id', $queueRoomId)
    //                     ->value('target_url');
                
    //                 Redis::set($redisKey, $targetUrl);
    //             }

    //             $finalResponse = [
    //                 'status' => 10,
    //                 'message' => 'Page has no traffic, redirecting to site',
    //                 'qProcessOp' => false,
    //                 'redirectionUrl' => $targetUrl
    //             ];

    //             $responseCode = 200;

    //             // Forget multiple cookies in one line
    //             $cookie1 = Cookie::forget('qProcessOp');
    //             $cookie2 = Cookie::forget('qSessionId');
    //             $cookie3 = Cookie::forget('checkByepassStatus');

    //             self::updateCurrentQueue($queueRoomId);

    //         }else {
    //             $redisKey = "queueRoomData:{$queueRoomId}";
    //             $queueRoomData = Redis::get($redisKey);

    //             if ($queueRoomData) {
    //                 $queueRoomData = json_decode($queueRoomData);
    //             } else {
    //                 // Fetch from the database if not in Redis
    //                 $queueRoomData = DB::table('queuetb_queue_room as qr')
    //                     ->select('qr.id', 'qr.queue_room_name', 'qr.queue_room_template_id', 'qr.target_url', 'qr.bypass_template_id', 'qr.sms_notice_tempid', 'qr.email_notice_tempid', 'qr.enable_bypass', 'bp_tamp.bypass_url', 'qr.queue_room_icon', 'qr_tamp.is_advance_setting', 'qr_tamp.advance_setting_rules', 'qr_tamp.input_url', 'qr.max_traffic_visitor', 'qr.currunt_traffic_count', 'qr.is_started', 'qr.is_ended', 'qr.start_time_epoch', 'qr_design.default_language')
    //                     ->leftJoin('bypass_template as bp_tamp', 'qr.bypass_template_id', '=', 'bp_tamp.id')
    //                     ->join('queue_room_template as qr_tamp', 'qr.queue_room_template_id', '=', 'qr_tamp.id')
    //                     ->join('queuetb_design_template as qr_design', 'qr.queue_room_design_tempid', '=', 'qr_design.id')
    //                     ->where('qr.parent_user_id', $customerId)
    //                     ->where('qr.id', $queueRoomId)
    //                     ->first();
                
    //                 // Cache the result in Redis for future requests
    //                 Redis::set($redisKey, json_encode($queueRoomData));
    //             }

    //             $defLang = $queueRoomData ? ($queueRoomData->default_language ?? $this->default_language) : $this->default_language;

    //             $isQueueStarted = $queueRoomData->is_started;
    //             $isQueueEnded = $queueRoomData->is_ended;
    //             $maxTrafficVisitor = $queueRoomData->max_traffic_visitor;
    //             $currentTrafficCount = $queueRoomData->currunt_traffic_count;

    //             if ($isQueueStarted == 1 && $isQueueEnded == 0 || $isQueueEnded == 2) {
    //                 $bypassStatus = self::checkByPass($queueRoomData, $browserSessionData->browser_url, $queueRoomId, $customerId, $browserSessionId);

    //                 if ($bypassStatus['condition_flag']) {
    //                     self::updateRawtables($browserSessionId, 2, $deviceId);
                    
    //                     $finalResponse = $bypassStatus['final_response'];
    //                     $responseCode = $bypassStatus['response_code'];
                    
    //                     self::updateCurrentQueue($queueRoomId);
    //                 } else {
    //                     $isAdvanceSetting = $queueRoomData->is_advance_setting;
    //                     $inputUrl = $queueRoomData->input_url;
                    
    //                     $urlComponents = parse_url($browserSessionData->browser_url);
    //                     $visitorAccessUrlDomain = $urlComponents['host'] ?? '';
    //                     $visitorAccessUrlPagePath = $urlComponents['path'] ?? '';
    //                     $visitorAccessUrl = $visitorAccessUrlDomain . $visitorAccessUrlPagePath;
                    
    //                     if ($isAdvanceSetting == 1) {
    //                         $advanceSettingRules = json_decode($queueRoomData->advance_setting_rules, true);
                        
    //                         foreach ($advanceSettingRules as $rule) {
    //                             $conditionMet = false;
                            
    //                             switch ($rule['condition_place']) {
    //                                 case 'HOST_NAME':
    //                                     $value = $rule['value'];
    //                                     switch ($rule['condition']) {
    //                                         case 'EQUALS':
    //                                             $conditionMet = ($visitorAccessUrlDomain == $value);
    //                                             break;
    //                                         case 'DOES_NOT_EQUAL':
    //                                             $conditionMet = ($visitorAccessUrlDomain != $value);
    //                                             break;
    //                                         case 'CONTAINS':
    //                                             $conditionMet = (strpos($visitorAccessUrlDomain, $value) !== false);
    //                                             break;
    //                                         case 'DOES_NOT_CONTAIN':
    //                                             $conditionMet = (strpos($visitorAccessUrlDomain, $value) === false);
    //                                             break;
    //                                     }
    //                                     break;
    //                                 case 'PAGE_PATH':
    //                                     $value = $rule['value'];
    //                                     switch ($rule['condition']) {
    //                                         case 'EQUALS':
    //                                             $conditionMet = ($visitorAccessUrlPagePath == $value);
    //                                             break;
    //                                         case 'DOES_NOT_EQUAL':
    //                                             $conditionMet = ($visitorAccessUrlPagePath != $value);
    //                                             break;
    //                                         case 'CONTAINS':
    //                                             $conditionMet = (strpos($visitorAccessUrlPagePath, $value) !== false);
    //                                             break;
    //                                         case 'DOES_NOT_CONTAIN':
    //                                             $conditionMet = (strpos($visitorAccessUrlPagePath, $value) === false);
    //                                             break;
    //                                     }
    //                                     break;
    //                                 case 'PAGE_URL':
    //                                     $value = $rule['value'];
    //                                     switch ($rule['condition']) {
    //                                         case 'EQUALS':
    //                                             $conditionMet = ($visitorAccessUrl == $value);
    //                                             break;
    //                                         case 'DOES_NOT_EQUAL':
    //                                             $conditionMet = ($visitorAccessUrl != $value);
    //                                             break;
    //                                         case 'CONTAINS':
    //                                             $conditionMet = (strpos($visitorAccessUrl, $value) !== false);
    //                                             break;
    //                                         case 'DOES_NOT_CONTAIN':
    //                                             $conditionMet = (strpos($visitorAccessUrl, $value) === false);
    //                                             break;
    //                                     }
    //                                     break;
    //                             }
                            
    //                             if ($rule['operator'] == 'AND' && !$conditionMet) {
    //                                 $putInQueue = false;
    //                                 break;
    //                             } elseif ($rule['operator'] == 'OR' && $conditionMet) {
    //                                 $putInQueue = true;
    //                                 break;
    //                             }
    //                         }
    //                     }
                    
    //                     if ((isset($putInQueue) && $putInQueue == true) || !isset($putInQueue)) {
    //                         $queueSerialNumber = $browserSessionData->queue_serial_number ?? '';
                        
    //                         if (!$queueSerialNumber) {
    //                             $queueSerialData = self::assignQueueSerialNumber($queueRoomId, $maxTrafficVisitor, false);
                            
    //                             $query = "UPDATE queuetb_raw_queue_operations SET queue_serial_number = '{$queueSerialData['queue_serial_number']}', queue_serial_number_id = {$queueSerialData['queue_serial_number_id']} WHERE browser_session_id = {$browserSessionId}";
    //                             DB::select($query);
                            
    //                             $queueSerialNumber = $queueSerialData['queue_serial_number'];
    //                         }
                        
    //                         self::updateRawtables($browserSessionId, 5, $deviceId);
                        
    //                         $numberQuery = "SELECT count(id) as count FROM queuetb_raw_queue_operations WHERE room_id = {$queueRoomId} AND (status = 3 OR status = 5) AND id < {$browserSessionData->id}";
    //                         $numberData = DB::selectOne($numberQuery);
    //                         $numberInLine = ($numberData) ? $numberData->count : 0;
                        
    //                         $expectedWaitTime = (floor($numberInLine / $maxTrafficVisitor)) + 1;
                        
    //                         $numberQuery = "SELECT sum(storage_occupied_number) as count FROM queue_serial_number_management WHERE room_id = {$queueRoomId} AND cron_status = 0";
    //                         $totalNumberData = DB::selectOne($numberQuery);
    //                         $totalNumber = ($totalNumberData && !empty($totalNumberData->count)) ? $totalNumberData->count : 1;
                        
    //                         $per = (1 - ($numberInLine / $totalNumber));
                        
    //                         $modifiedHtml = Logics::showQueuePage($queueRoomId, $browserSessionId, $customerId, $this->this_epoch_time, $per, $numberInLine);
                        
    //                         $finalResponse = [
    //                             'status' => 5,
    //                             'message' => 'status 5',
    //                             'queue_serial_number' => $queueSerialNumber,
    //                             'max_traffic_visitor' => $maxTrafficVisitor ?? 0,
    //                             'qProcessOp' => true,
    //                             'a' => 'ssa-' . $browserSessionData->id,
    //                             'current_queue_pos' => $numberInLine + 1,
    //                             'htmlBody' => $modifiedHtml,
    //                         ];
    //                         $responseCode = 200;
    //                     } else {
    //                         $cookie1 = Cookie::forget('qProcessOp');
    //                         $cookie2 = Cookie::forget('qSessionId');
    //                         $cookie3 = Cookie::forget('checkByepassStatus');
                        
    //                         $finalResponse = [
    //                             'status' => 0,
    //                             'message' => 'status 0',
    //                             'qProcessOp' => false,
    //                         ];
    //                         $responseCode = 200;
    //                     }
    //                 }
    //             } else {
    //                 $startTimeEpoch = $queueRoomData->start_time_epoch;
    //                 if ($this->this_epoch_time > $startTimeEpoch) {
    //                     $queueSerialNumber = $browserSessionData->queueSerialNumber ?? null;
                    
    //                     if (!$queueSerialNumber) {
    //                         $queueSerialData = self::assignQueueSerialNumber($queueRoomId, $maxTrafficVisitor, $curruntTrafficCount = false);
                        
    //                         $log_message = "Added to queue for session_id: {$browser_session_id} queue_serial_number: " . json_encode($queue_serial_data);
    //                         Log::channel('in-queue-room')->info($log_message);
                        
    //                         $query = "UPDATE queuetb_raw_queue_operations SET queue_serial_number = '{$queue_serial_data['queue_serial_number']}', queue_serial_number_id = {$queue_serial_data['queue_serial_number_id']} WHERE browser_session_id = {$browser_session_id}";
    //                         DB::select($query);
                        
    //                         $queueSerialNumber = $queueSerialData['queue_serial_number'];
    //                     }
                    
    //                     self::updateRawtables($browserSessionId, 5, $deviceId);
                    
    //                     $numberQuery = "SELECT count(id) as count FROM queuetb_raw_queue_operations WHERE room_id = {$queueRoomId} AND (status = 3 OR status = 5) AND id < {$browserSessionData->id}";
    //                     $numberData = DB::selectOne($numberQuery);
    //                     $numberInLine = ($numberData) ? $numberData->count : 0;
                    
    //                     $expectedWaitTime = (floor($numberInLine / $maxTrafficVisitor)) + 1;
                    
    //                     $totalQuery = "SELECT COALESCE(SUM(storage_occupied_number), 1) as total FROM queue_serial_number_management WHERE room_id = {$queueRoomId} AND cron_status = 0";
    //                     $totalData = DB::selectOne($total_query);
    //                     $totalNumber = $totalData->total;
                    
    //                     $per = (1 - ($numberInLine / $totalNumber));
                    
    //                     $modifiedHtml = Logics::showQueuePageWithSwitch($queueRoomId, $numberInLine, $expectedWaitTime, $browserSessionId, $customerId);
                    
    //                     $finalResponse = [
    //                         'status' => 5,
    //                         'message' => 'status 5',
    //                         'queue_serial_number' => $queueSerialNumber,
    //                         'max_traffic_visitor' => $maxTrafficVisitor ?? 0,
    //                         'qProcessOp' => true,
    //                         'a' => 'ss',
    //                         'current_queue_pos' => ($currentQueuePos ?? 0) + 1,
    //                         'htmlBody' => $modifiedHtml,
    //                     ];
    //                     $responseCode = 200;
    //                 } else {
    //                     $cookie1 = Cookie::forget('qProcessOp');
    //                     $cookie2 = Cookie::forget('qSessionId');
    //                     $cookie3 = Cookie::forget('checkByepassStatus');
                    
    //                     $finalResponse = [
    //                         'status' => 0,
    //                         'message' => 'status 0',
    //                         'qProcessOp' => false,
    //                     ];
    //                     $responseCode = 200;
    //                 }
    //             }
    //         }
    //     } else {
    //         $cookie1 = Cookie::forget('qProcessOp');
    //         $cookie2 = Cookie::forget('qSessionId');
    //         $cookie3 = Cookie::forget('checkByepassStatus');
    //         $finalResponse = [
    //             'status' => 0,
    //             'message' => 'session id not found queue',
    //             'qProcessOp' => false,
    //         ];
    //         $responseCode = 404;
    //     }
    //     DB::listen(function ($query) {
    //         Log::channel('querylogSQL')->info('SQL: ' . $query->sql);
    //         Log::channel('querylogBindings')->info('Bindings: ' . implode(', ', $query->bindings));
    //         Log::channel('querylogTime')->info('Time: ' . $query->time . 'ms');
    //         // Log::info('SQL====|||: ' . $query->sql.' | '.implode(', ', $query->bindings).' | '.$query->time . 'ms');
    //     });
        
    //     self::addResponseLog($browserSessionId, $finalResponse, $responseCode);
    //     if (isset($cookie1) && isset($cookie2)) {
    //         return response()->json($finalResponse, $responseCode)
    //             ->withCookie($cookie1)
    //             ->withCookie($cookie2);
    //     } else {
    //         return response()->json($finalResponse, $responseCode);
    //     }
    // }


    public function addResponseLog($session_id, $response, $response_code)
    {
        $log_message = "Response for session_id: " . json_encode($session_id) . " code: " . $response_code . " is - " . json_encode($response);
        Log::channel('queue-operations')->info($log_message);
    }

    public function verifyBypassCode(Request $request)
    {
        Log::channel('queue-operations')->info(json_encode($request));

        $requestData    =   $request->all();
        $pass_code    =   $requestData['search2'];
        $room_id    =   $requestData['manual_room_id'];
        $browser_session_id    =   $requestData['manual_browser_session_id'];
        $customer_id    =   $requestData['manual_customer_id'];

        $query =    "SELECT bpc.id 
                    FROM bypass_pass_codes as bpc 
                    inner join queuetb_queue_room as qr ON bpc.bypass_tamp_id = qr.bypass_template_id
                    WHERE ( qr.id = $room_id ) AND ( bpc.pass_code = '" . $pass_code . "') ";

        $pass_code_data = DB::select($query);
        Log::channel('queue-operations')->info(json_encode($pass_code_data));

        if ($pass_code_data) {
            self::updateRawtables($browser_session_id, 1);
        }

        $query = "SELECT id,browser_url from queuetb_raw_queue_operations  WHERE browser_session_id = '" . $browser_session_id . "' and customer_id =" . $customer_id;
        $queue_room_data = DB::selectOne($query);
        $redirectUrl = $queue_room_data->browser_url;
        return redirect($redirectUrl);
    }


    public function checkBypassCodeStatus(Request $request)
    {
        $requestData    =   $request->all();
        $browser_session_id    =   $requestData['session_id'];
        $queue_room_id          =   base64_decode($requestData['scriptAttributes']['intercept']);
        // $queue_room_id          =   $requestData['scriptAttributes']['intercept'];
        $customer_id = $requestData['scriptAttributes']['cid'] ?? null;


        $query = "SELECT id,status FROM queuetb_raw_queue_operations WHERE  browser_session_id = " . $browser_session_id;
        $browser_session_data = DB::selectOne($query);
        // $browser_session_data = DB::select($query);



        if ($browser_session_data) {
            $main_status = $browser_session_data->status;
            if ($main_status == 1) {
                $final_response = [
                    'status' => 1,
                    'message' => 'status 1',
                ];
                $response_code = 200;
            } else {
                $query = "SELECT qr.id, qr.target_url, bp_tamp.bypass_url,qr.queue_room_icon,qr_design.default_language 
                FROM queuetb_queue_room as qr
                LEFT JOIN bypass_template as bp_tamp ON qr.bypass_template_id = bp_tamp.id                
                INNER JOIN  queuetb_design_template as qr_design ON qr.queue_room_design_tempid = qr_design.id
                WHERE (qr.parent_user_id = " . $customer_id . ") 
                AND (qr.id  = " . $queue_room_id . ")";
                $queue_room_data = DB::selectOne($query);
                if (isset($queue_room_data->default_language) or !is_null($queue_room_data->default_language)) {
                    $this->default_language = $queue_room_data->default_language;
                }

                $bypass_url = $queue_room_data->bypass_url;
                $target_url = $queue_room_data->target_url;
                $queue_room_image_url = env('APP_URL') . "public/images/" . $queue_room_data->queue_room_icon;
                $query = "SELECT htm_data FROM in_line_tamplates where type = 'priority_access_page' and room_id = $queue_room_id and
                language = '$this->default_language'";
                $lang_data = DB::selectOne($query);
                $modifiedHtml='';
                 if($lang_data){
                        $modifiedHtml = html_entity_decode($lang_data->htm_data);    
                 }
                
                $dom = new \DOMDocument();
                libxml_use_internal_errors(true);
                // pawan
                if(!empty($modifiedHtml)){
                    $dom->loadHTML($modifiedHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
                }
                

                $images = $dom->getElementsByTagName('img');
                foreach ($images as $img) {
                    $img->setAttribute('src', $queue_room_image_url);
                }

                $forms = $dom->getElementsByTagName('form');
                foreach ($forms as $form) {
                    $form->setAttribute('action', env('APP_URL') . "api/verify-bypass-code");
                    $form->setAttribute('method', "post");

                    $manual_room_id = $dom->createElement('input');
                    $manual_room_id->setAttribute('type', 'hidden');
                    $manual_room_id->setAttribute('name', 'manual_room_id');
                    $manual_room_id->setAttribute('value', $queue_room_id);

                    $manual_browser_session_id = $dom->createElement('input');
                    $manual_browser_session_id->setAttribute('type', 'hidden');
                    $manual_browser_session_id->setAttribute('name', 'manual_browser_session_id');
                    $manual_browser_session_id->setAttribute('value', $browser_session_id);

                    $manual_customer_id = $dom->createElement('input');
                    $manual_customer_id->setAttribute('type', 'hidden');
                    $manual_customer_id->setAttribute('name', 'manual_customer_id');
                    $manual_customer_id->setAttribute('value', $customer_id);

                    $manual_target_url = $dom->createElement('input');
                    $manual_target_url->setAttribute('type', 'hidden');
                    $manual_target_url->setAttribute('name', 'manual_target_url');
                    $manual_target_url->setAttribute('value', $target_url);

                    $manual_bypass_url = $dom->createElement('input');
                    $manual_bypass_url->setAttribute('type', 'hidden');
                    $manual_bypass_url->setAttribute('name', 'manual_bypass_url');
                    $manual_bypass_url->setAttribute('value', $bypass_url);

                    $form->appendChild($manual_room_id);
                    $form->appendChild($manual_browser_session_id);
                    $form->appendChild($manual_customer_id);
                    $form->appendChild($manual_target_url);
                    $form->appendChild($manual_bypass_url);
                }

                $modifiedHtml = $dom->saveHTML();

                $final_response = [
                    'status' => 2,
                    'message' => 'status 2',
                    'qProcessOp' => true,
                    'checkByepassStatus' => true,
                    'htmlBody' => $modifiedHtml,
                ];
                $response_code = 200;
            }
        } else {

            $final_response = [
                'status' => 2,
                'message' => 'status 2',
            ];
            $response_code = 200;
        }

        if (isset($cookie1) && isset($cookie2)) {

            return response()->json($final_response, $response_code)
                ->withCookie($cookie1)
                ->withCookie($cookie2)
                ->withCookie($cookie3);
        } else {

            return response()->json($final_response, $response_code);
        }
    }


    // public function notificationRequest(Request $request)
    // {
    //     $requestData = $request->all();
    //     $email = $requestData['search2'];
    //     $room_id = $requestData['room_id'];
    //     $customer_id = $requestData['manual_customer_id'];

    //     $browser_session_id = $requestData['browser_session_id'];

    //     $query = "UPDATE queuetb_raw_queue_operations SET pre_q_notification_mail = '" . $email . "' WHERE browser_session_id = '" . $browser_session_id . "'";
    //     $queue_room_data = DB::select($query);


    //     $query = "SELECT id,browser_url from queuetb_raw_queue_operations  WHERE browser_session_id = '" . $browser_session_id . "' and customer_id =" . $customer_id;
    //     $queue_room_data = DB::selectOne($query);
    //     // $queue_room_data = DB::select($query);
    //     $redirectUrl = $queue_room_data->browser_url;
    //     return redirect($redirectUrl);
    // }

    public function notificationRequest(Request $request)
    {
        $requestData = $request->all();
        $accessCode = $requestData['search2'];
        $room_id = $requestData['room_id'];
        $customer_id = $requestData['manual_customer_id'];

        $browser_session_id = $requestData['browser_session_id'];

        $getByPassId = QueueRoom::select('bypass_template_id', 'target_url')->where('id', $room_id)->first();
        
        $getInputURL = DB::table('queuetb_raw_queue_operations')
                    ->select('browser_url')
                    // ->where('browser_session_id', $browser_session_id)
                    ->where('room_id', $room_id)
                    ->where('device_id', $browser_session_id)
                    ->first();

        // return $getByPassId;die;
        if (!empty($getByPassId))
        {
            $checkByPassId = DB::table('bypass_pass_codes')
                ->where('bypass_tamp_id', $getByPassId->bypass_template_id)
                // ->where('pass_code', 'LIKE', "%{$accessCode}%")  // Using LIKE to match patterns
                ->where('pass_code', $accessCode)
                ->count();

            if ($checkByPassId > 0) {
                $getInputURL->update(['status' => 6]);
                return redirect($getByPassId->target_url);
            } else {
                return redirect($getInputURL->browser_url);
            }
        }
        
        return redirect($getInputURL->browser_url);
    }

    public function waitingRoomNotificationRequest(Request $request)
    {
        $requestData = $request->all();
        $email = $requestData['search2'];
        $room_id = $requestData['room_id'];
        $customer_id = $requestData['manual_customer_id'];

        $browser_session_id = $requestData['manual_browser_session_id'];

        $query = "UPDATE queuetb_raw_queue_operations SET q_notification_mail = '" . $email . "' WHERE browser_session_id = '" . $browser_session_id . "'";
        $queue_room_data = DB::select($query);


        $query = "SELECT id,browser_url from queuetb_raw_queue_operations  WHERE browser_session_id = '" . $browser_session_id . "' and customer_id =" . $customer_id;
        $queue_room_data = DB::selectOne($query);
        // $queue_room_data = DB::select($query);
        $redirectUrl = $queue_room_data->browser_url;
        return redirect($redirectUrl);
    }
}
