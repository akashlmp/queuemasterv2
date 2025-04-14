<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessDirectAccess extends Controller
{
    public function ProcessDirectAccess()
    {
        Log::channel('cron-log')->info("ProcessDirectAccess ran");


        $this_epoch_time = time();
        $twoMinutesAgo = strtotime('-2 minutes', $this_epoch_time);

        $query = "SELECT id,room_id FROM `queuetb_raw_queue_operations` WHERE (status = 10) AND (traffic_deduction = 0 ) AND (last_updated_epoch < " . $twoMinutesAgo . ") ORDER BY id DESC;";


        $queuetb_raw_queue_operations_data = DB::select($query);

        DB::beginTransaction();

        try {
            foreach ($queuetb_raw_queue_operations_data as $data) {
                // Update status of queue_serial_number_management
                $qu = "select currunt_traffic_count from queuetb_queue_room where id = " . $data->room_id;
                $roomData = DB::select($qu);
                if(!empty($roomData)) {
                    $currunt_traffic_count = $roomData[0]->currunt_traffic_count - 1;
                    if($currunt_traffic_count  < 0) {
                        $currunt_traffic_count = 0;
                    }
                    DB::table('queuetb_queue_room')
                        ->where('id', $data->room_id)
                        ->update(['currunt_traffic_count' => $currunt_traffic_count]);
                        //->decrement('currunt_traffic_count');
                }
                Log::channel('cron-log')->info("ProcessDirectAccess :- Update query room by 1  successful for serial number with ID: " . $data->id);
            }

            DB::commit();
            $this->updateQueueStatus($queue_serial_number_data);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('cron-log')->error("ProcessDirectAccess :- Update query failed: " . $e->getMessage());
        }
    }

    private function updateQueueStatus($queuetb_raw_queue_operations_data)
    {
        DB::beginTransaction();

        try {
            foreach ($queuetb_raw_queue_operations_data as $data) 
            {
                // Update status of queue_serial_number_management


                DB::table('queuetb_raw_queue_operations')
                    ->where('id', $data->id)
                    ->update(['traffic_deduction' => 1]);

                Log::channel('cron-log')->info("ProcessDirectAccess :- Update queue_serial_number_management traffic_deduction to 9  successful for serial number with ID: " . $data->id);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::channel('cron-log')->error("ProcessDirectAccess :- Update queue_serial_number_management traffic_deduction to 1 failed: " . $e->getMessage());
        }
    }
}
