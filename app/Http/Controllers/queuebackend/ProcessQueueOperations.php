<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use DateTime;
use DateTimeZone;
class ProcessQueueOperations extends Controller
{
    private function getUTCTime($timeZone, $time) {
        // Create a DateTime object with the specified time and time zone
        var_dump($timeZone);
        var_dump($time);
        $date = new DateTime($time, new DateTimeZone($timeZone));
        var_dump($date);
       
        // Set the time zone to UTC
        $date->setTimezone(new DateTimeZone('UTC'));
        var_dump('sss');
        var_dump($date->format('H:i:s'));
        // Return the date and time in UTC
        return $date->format('H:i:s');
    }

    // public function processQueue()
    // {
    //     Log::channel('cron-log')->info("processQueue ran");

        
    //     $query = "SELECT qsnm.id, qroom.max_traffic_visitor,qsnm.queue_serial_number,qsnm.room_id FROM queue_serial_number_management as qsnm INNER JOIN queuetb_queue_room as qroom ON qsnm.room_id = qroom.id WHERE (qsnm.cron_status != 1) AND (qroom.is_started = 1) AND (qroom.is_ended != 0)   ORDER BY qsnm.id ASC";
    //     $queue_serial_number_data = DB::select($query);
    //     if(!empty($_GET['tyt'])) {
    //         // echo "SELECT qsnm.id, qroom.max_traffic_visitor,qsnm.queue_serial_number FROM queue_serial_number_management as qsnm INNER JOIN queuetb_queue_room as qroom ON qsnm.room_id = qroom.id WHERE (qsnm.cron_status != 1) AND (qroom.is_started = 1) AND (qroom.is_ended != 0)   ORDER BY id ASC";
    //         // echo '<pre>';
    //         // print_r((array)$queue_serial_number_data);
    //         $loop_visiter[] = 0;
    //         foreach ($queue_serial_number_data as $serial_number) {
    //             if(isset($loop_visiter[$serial_number->id]) && $serial_number->max_traffic_visitor < $loop_visiter[$serial_number->id]) {
    //                 continue;
    //             } else {
    //                 if (empty($loop_visiter[$serial_number->id])) {
    //                     $loop_visiter[$serial_number->id] = 1;
    //                 }
    //                 $loop_visiter[$serial_number->id]++; 
    //             }
                
    //              // Update status of queue_serial_number_management
    //             $sql = 'select *,STR_TO_DATE(browser_time, "%l:%i:%s %p") as entry_browser_time from queuetb_raw_queue_operations where status = 5 and queue_serial_number_id = ' . $serial_number->id . '';
               
    //             $data = DB::select($sql);
    //             if(!empty($data) && !empty($data[0]) && !empty($_GET['tyt'])) {
    //                 $timezone_time = $this->getUTCTime($data[0]->browser_time_zone, $data[0]->entry_browser_time);
    //                 $time1 = new DateTime($timezone_time);
    //                 $time2 = new DateTime(date('H:i:s'));
    //                 $interval = $time1->diff($time2);
    //                 $seconds = (int) $interval->format('%s');
    //                 if($seconds > 50) {

    //                 }
    //             } 
    //         }
    //     }


    //     DB::beginTransaction();
    //     try {
    //         $loop_visiter[] = 0;
    //         foreach ($queue_serial_number_data as $serial_number) {
    //             echo $serial_number->room_id .'=>';
    //             if(isset($loop_visiter[$serial_number->room_id]) && $serial_number->max_traffic_visitor <= $loop_visiter[$serial_number->room_id]) {
    //                 continue;
    //             } else {
    //                 if (empty($loop_visiter[$serial_number->room_id])) {
    //                     $loop_visiter[$serial_number->room_id] = 1;
    //                 } else {
    //                     $loop_visiter[$serial_number->room_id]++; 
    //                 }
    //             }
    //             echo $loop_visiter[$serial_number->room_id];
    //             DB::table('queuetb_raw_queue_operations as qro')
    //                 ->where('qro.queue_serial_number_id', $serial_number->id)
    //                 ->where('qro.status', 5)
    //                 ->update(['qro.status' => 6]); //'qro.updated_at'=> date('Y-m-d H:i:s')
    //             Log::channel('cron-log')->info("ProcessQueueOperations: Updated status for serial number with ID: " . $serial_number->id);

    //             DB::table('queue_serial_number_management as qsnm')
    //             ->where('qsnm.id', $serial_number->id)
    //             ->update(['qsnm.cron_status' => 1]);
                
    //             Log::channel('cron-log')->info("ProcessQueueOperations: Updated status for serial number with ID: " . $serial_number->id);
    //         }
    //         DB::commit();
    //         // Call the second function
    //         //$this->updateQueueStatus($queue_serial_number_data);
    //     } catch (\Exception $er) {
    //         DB::rollBack();
    //         Log::channel('cron-log')->error("ProcessQueueOperations: Failed to update queue operation for serial number with ID: " . $serial_number->id . ". Error: " . $er->getMessage());
    //     }
    //     die("DIE");
    // }

    public function processQueue()
    {
        $getOprationList = DB::table('queuetb_raw_queue_operations')->where('status', 5)->get();
        if (sizeof($getOprationList))
        {
            foreach ($getOprationList as $list)
            {
                $updateStauts = DB::table('queuetb_raw_queue_operations')->where('id', $list->id)->update(['status' => 6]);

                if ($updateStauts)
                {
                    
                    QueueRoom::where([['id', $list->room_id], ['currunt_traffic_count', '>', 0]])->decrement('currunt_traffic_count', 1);

                    // $getQueueRoom = QueueRoom::select('id', 'currunt_traffic_count', 'max_traffic_visitor')->where('id', $queueRoomId)->first();
                    // if ($getQueueRoom->currunt_traffic_count == $getQueueRoom->max_traffic_visitor)
                    // {
                    //     # code...
                    // }
                    // QueueRoom::where([['id', $list->room_id], ['currunt_traffic_count', '=', 'max_traffic_visitor']])->update(['currunt_traffic_count' => 0]);
                }
            }

            return true;
        }

        return false;
    }

    private function updateQueueStatus($queue_serial_number_data)
    {
        DB::beginTransaction();
        try {
            foreach ($queue_serial_number_data as $serial_number) {
                // Update status of queue_serial_number_management
                DB::table('queue_serial_number_management as qsnm')
                    ->where('qsnm.id', $serial_number->id)
                    ->update(['qsnm.cron_status' => 1]);
                Log::channel('cron-log')->info("ProcessQueueOperations: Updated status for serial number with ID: " . $serial_number->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('cron-log')->error("ProcessQueueOperations: Failed to process queue operations. Error: " . $e->getMessage());
        }
    }
}
