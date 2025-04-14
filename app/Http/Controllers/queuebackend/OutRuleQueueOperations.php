<?php

namespace App\Http\Controllers\queuebackend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OutRuleQueueOperations extends Controller
{
    public function endQueue()
    {
         Log::channel('cron-log')->info("endQueue ran");
        $this_epoch_time = time();

        $query = "SELECT id,start_time_epoch FROM queuetb_queue_room WHERE is_started = 1 AND is_ended = 0 ORDER BY id DESC";
        $queue_room_data = DB::select($query);

        // Begin a transaction
        DB::beginTransaction();

        try {
            foreach ($queue_room_data as $room_data) {
                $this_end_time_epoch = $room_data->end_time_epoch;
                $this_id = $room_data->id;

                if ($this_end_time_epoch > $this_epoch_time) {
                    // Your update query to set 'is_ended' to 1
                    // For example:
                    DB::table('queuetb_queue_room')
                        ->where('id', $this_id)
                        ->update(['is_ended' => 1]);

                    // Log success message
                     Log::channel('cron-log')->info("OutRuleQueueOperations: Queue room with ID " . $this_id . " has ended.");
                }
            }

            // Commit the transaction if all update queries are successful
            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs during the update process
            DB::rollBack();

            // Log error message
             Log::channel('cron-log')->error("OutRuleQueueOperations: Update query failed: " . $e->getMessage());
        }
    }
}
